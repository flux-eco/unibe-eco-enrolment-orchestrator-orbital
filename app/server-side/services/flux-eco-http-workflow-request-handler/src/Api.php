<?php

namespace FluxEco\HttpWorkflowRequestHandler;

use Exception;
use FluxEco\UnibeOmnitrackerClient\Types\Exceptions\FluxEcoUnibeOmnitrackerClientFluxEcoInvalidInputException;
use FluxEcoType\FluxEcoContentType;
use FluxEcoType\FluxEcoExceptionDefinitions\FluxEcoException;
use FluxEcoType\FluxEcoHttpStatusCode;
use FluxEcoType\FluxEcoTransactionResponse;
use FluxEcoType\FluxEcoTransactionStateObject;

final readonly class Api
{
    private function __construct(
        private Config $config
    )
    {

    }

    public static function new(
        Types\Outbounds $outbounds
    ): self
    {
        return new self(
            Config::new($outbounds)
        );
    }

    public function handleHttpRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response, \Swoole\Table $transactionDataCache): void
    {
        $clientSideContextExceptions = [];
        $serverSideContextExceptions = [];

        try {
            $transactionId = $this->readCurrentTransactionId($request, $response);

            $transactionStateObject = $this->createTransactionStateObject($transactionId, $transactionDataCache);

            if ($this->config->outbounds->isLastPage($transactionStateObject)) {
                $this->removeTransactionDataFromCache($transactionId, $transactionDataCache);
                $this->resetTransactionIdCookie($response);
            }

            match ($request->server['request_uri']) {
                '/api/layout' => $this->publish($response, $this->config->outbounds->processReadLayout()),
                '/api/get' => $this->handleGet($response, $transactionStateObject),
                '/api/back' => $this->handleBack($response, $transactionDataCache, $transactionStateObject),
                '/api/post' => $this->handlePost($request, $response, $transactionDataCache, $transactionStateObject),
                '/api/logout' => $this->handleLogout($response),
            };
        } catch (FluxEcoException $e) {
            if ($e->usableInClientSideContext === true) {
                $clientSideContextExceptions[] = $e->getMessage();
            }
        }

        if (count($clientSideContextExceptions) > 0) {
            $response->header('Content-Type', 'application/json');
            $response->status(FluxEcoHttpStatusCode::OK->value);
            $response->end(FluxEcoTransactionResponse::new(
                $transactionId,
                "",
                false,
                $clientSideContextExceptions
            )->toJson());
        }

        if (count($serverSideContextExceptions) > 0) {
            $response->header('Content-Type', 'application/json'); //todo
            $response->status(FluxEcoHttpStatusCode::BAD_REQUEST->value);
            $response->end(FluxEcoTransactionResponse::new(
                $transactionId,
                "",
                false,
                ["Server Error"]
            )->toJson());
        }
    }

    private function removeTransactionDataFromCache(string $transactionId, \Swoole\Table $transactionDataCache): void
    {
        $transactionDataCache->del($transactionId);
    }

    private function resetTransactionIdCookie(\Swoole\Http\Response $response): void
    {
        $response->setCookie($this->config->settings->transactionIdCookieName, "", 0);
    }

    private function createTransactionStateObject(string $transactionId, \Swoole\Table $transactionDataCache): FluxEcoTransactionStateObject
    {
        $currentTransactionData = $transactionDataCache->get($transactionId);
        if ($currentTransactionData !== false) {
            return FluxEcoTransactionStateObject::fromCachedState(
                $currentTransactionData['transactionId'],
                json_decode($currentTransactionData['data']),
                json_decode($currentTransactionData['handledPageNamesCurrentWorkflow'], true),
                $currentTransactionData['currentPageName'],
                $currentTransactionData['expiration'],
            );
        }

        return FluxEcoTransactionStateObject::createNew(
            $transactionId,
            null,
            [],
            $this->config->outbounds->processReadStartPageName(), //todo get rid of processReadStartPageName
            null
        );
    }

    private function handleBack($response, \Swoole\Table $transactionDataCache, FluxEcoTransactionStateObject $transactionStateObject): void
    {
        $previousPageName = $this->config->outbounds->processReadPreviousPageName($transactionStateObject);

        $handledPageNamesCurrentWorkflow = $transactionStateObject->handledPageNamesCurrentWorkflow;
        array_pop($handledPageNamesCurrentWorkflow);

        $transactionDataCache->set($transactionStateObject->transactionId, [
            'transactionId' => $transactionStateObject->transactionId,
            'data' => json_encode($transactionStateObject->data),
            'handledPageNamesCurrentWorkflow' => json_encode($handledPageNamesCurrentWorkflow),
            'currentPageName' => $previousPageName,
            'expiration' => $transactionStateObject->expiration
        ]);

        $this->publish($response, FluxEcoTransactionResponse::new($transactionStateObject->transactionId, $transactionStateObject->currentPageName, true)->toJson());
    }

    private function handleGet($response, FluxEcoTransactionStateObject $transactionStateObject): void
    {
        $this->publish($response, $this->config->outbounds->processReadCurrentPage($transactionStateObject));
    }


    private function handlePost($request, $response, \Swoole\Table $transactionDataCache, FluxEcoTransactionStateObject $transactionStateObject): void
    {
        $requestContent = json_decode($request->rawContent());
        $page = $requestContent->page;
        match (true) {
            $this->config->outbounds->isResumePage($page) => $this->handlePostResume($request, $response, $transactionDataCache, $transactionStateObject),
            ($page === $transactionStateObject->currentPageName) => $this->handlePostStoreData($request, $response, $transactionDataCache, $transactionStateObject),
        };
    }

    private function handlePostResume($request, $response, \Swoole\Table $transactionDataCache, FluxEcoTransactionStateObject $transactionStateObject): void
    {
        $requestContent = json_decode($request->rawContent());
        $processData = $requestContent->data;

        $workflowStateData = $this->config->outbounds->processReadResumeEnrolmentData($transactionStateObject->transactionId, $processData);

        $lastHandledPage = $this->config->outbounds->processReadLastHandledPageNameFromWorkflowState($workflowStateData);

        $handledPageNamesCurrentWorkflow = []; //$transactionStateObject->handledPageNamesCurrentWorkflow; //todo
        $handledPageNamesCurrentWorkflow[] = $lastHandledPage;

        $resumedTransactionStateObject = FluxEcoTransactionStateObject::createNew(
            $transactionStateObject->transactionId,
            $workflowStateData,
            $handledPageNamesCurrentWorkflow,
            $this->config->outbounds->processReadNextPageName($lastHandledPage, $transactionStateObject)
        );

        $this->postHandled($response, $transactionDataCache, $resumedTransactionStateObject, $workflowStateData);
    }

    private function handlePostStoreData($request, $response, \Swoole\Table $transactionDataCache, FluxEcoTransactionStateObject $transactionStateObject)
    {
        $requestContent = json_decode($request->rawContent());
        $processData = $requestContent->data;

        $workflowStateData = $this->config->outbounds->processStoreRequestContent($transactionStateObject, $processData);
        $this->postHandled($response, $transactionDataCache, $transactionStateObject, $workflowStateData);
    }


    private function postHandled($response, \Swoole\Table $transactionDataCache, FluxEcoTransactionStateObject $transactionStateObject, object $workflowStateData)
    {
        $handledPageNamesCurrentWorkflow = $transactionStateObject->handledPageNamesCurrentWorkflow;
        $handledPageNamesCurrentWorkflow[] = $transactionStateObject->currentPageName;

        $changedTransactionStateObject = FluxEcoTransactionStateObject::createNew(
            $transactionStateObject->transactionId,
            $workflowStateData,
            $handledPageNamesCurrentWorkflow,
            $this->config->outbounds->processReadNextPageName($transactionStateObject->currentPageName, $transactionStateObject)
        );
        $transactionDataCache->set($changedTransactionStateObject->transactionId, [
            'transactionId' => $changedTransactionStateObject->transactionId,
            'data' => json_encode($changedTransactionStateObject->data),
            'handledPageNamesCurrentWorkflow' => json_encode($changedTransactionStateObject->handledPageNamesCurrentWorkflow),
            'currentPageName' => $changedTransactionStateObject->currentPageName,
            'expiration' => $changedTransactionStateObject->expiration
        ]);
        $this->publish($response, FluxEcoTransactionResponse::new($changedTransactionStateObject->transactionId, $changedTransactionStateObject->currentPageName, true)->toJson());
    }

    private function handleLogout(\Swoole\Http\Response $response)
    {
        $this->resetTransactionIdCookie($response);
        $this->publish($response, FluxEcoTransactionResponse::new("", "", true)->toJson());
    }

    private function readCurrentTransactionId(\Swoole\Http\Request $request, \Swoole\Http\Response $response): string
    {
        $transactionIdCookieName = $this->config->settings->transactionIdCookieName;
        $requestContent = json_decode($request->rawContent());

        if (is_object($requestContent) && $this->config->outbounds->isStartPage($requestContent->page)) {
            return $this->createTransactionId($transactionIdCookieName, $response);
        }

        $cookies = $request->cookie;
        //todo resume
        if (is_array($cookies) && array_key_exists($transactionIdCookieName, $cookies)) {
            return $cookies[$transactionIdCookieName];
        }
        return $this->createTransactionId($transactionIdCookieName, $response);
    }

    private function createTransactionId(string $transactionIdCookieName, \Swoole\Http\Response $response): string
    {
        $transactionId = $this->config->outbounds->processCreateTransactionId();
        $response->setCookie($transactionIdCookieName, $transactionId, time() + 3600);
        return $transactionId;
    }

    private
    function publish(\Swoole\Http\Response $response, string $payload, int $statusCode = 200)
    {
        $response->header('Content-Type', 'application/json');
        $response->header('Cache-Control', 'no-cache');
        $response->status($statusCode);
        $response->end($payload);
    }
}