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

        //todo separate exception handling
        $transactionId = $this->readCurrentTransactionId($request, $response);
        try {
            $transactionStateObject = $this->createTransactionStateObject($transactionId, $transactionDataCache);
            match ($request->server['request_uri']) {
                '/api/layout' => $this->publish($response, $this->config->outbounds->processReadLayout()),
                '/api/get' => $this->handleGet($response, $transactionStateObject),
                '/api/post' => $this->handlePost($request, $response, $transactionDataCache, $transactionStateObject)
            };
        } catch (FluxEcoException $e) {
            if ($e->usableInClientSideContext === true) {
                $clientSideContextExceptions[] = $e->getMessage();
            }
        } catch (Exception $e) {
            $serverSideContextExceptions[] = $e;
        }

        if (count($clientSideContextExceptions) > 0) {
            $response->header('Content-Type', 'application/json');
            $response->status(FluxEcoHttpStatusCode::BAD_REQUEST->value);
            $response->end(FluxEcoTransactionResponse::new(
                $transactionId,
                "",
                false,
                $clientSideContextExceptions
            )->toJson());
        }

        if (count($serverSideContextExceptions) > 0) {
            print_r($serverSideContextExceptions);

            $response->header('Content-Type', 'application/json'); //todo
            $response->status(FluxEcoHttpStatusCode::BAD_REQUEST->value);
            $response->end(FluxEcoTransactionResponse::new(
                $transactionId,
                "",
                false,
                ["Server Error"]
            ));
        }


    }

    private function createTransactionStateObject(string $transactionId, \Swoole\Table $transactionDataCache): FluxEcoTransactionStateObject
    {
        $currentTransactionData = $transactionDataCache->get($transactionId);
        if ($currentTransactionData !== false) {
            return FluxEcoTransactionStateObject::fromCachedState(
                $currentTransactionData['transactionId'],
                json_decode($currentTransactionData['data']),
                $currentTransactionData['lastHandledPage'],
                $currentTransactionData['expiration'],
            );
        }

        return FluxEcoTransactionStateObject::createNew(
            $transactionId,
            null,
            null,
            null
        );
    }

    private function handleGet($response, FluxEcoTransactionStateObject $transactionStateObject)
    {
        $this->publish($response, $this->config->outbounds->processReadCurrentPage($transactionStateObject));
    }

    private function handlePost($request, $response, \Swoole\Table $transactionDataCache, FluxEcoTransactionStateObject $transactionStateObject)
    {
        try {
            $requestContent = json_decode($request->rawContent());
            $data = $requestContent->data;
            $page = $requestContent->page;

            $storedData = $this->config->outbounds->processStoreRequestContent($page, $transactionStateObject, $data);

            $changedTransactionStateObject = FluxEcoTransactionStateObject::createNew(
                $transactionStateObject->transactionId,
                $storedData,
                $page
            );

            $transactionDataCache->set($changedTransactionStateObject->transactionId, [
                'transactionId' => $changedTransactionStateObject->transactionId,
                'data' => json_encode($changedTransactionStateObject->data),
                'lastHandledPage' => $changedTransactionStateObject->lastHandledPage,
                'expiration' => $changedTransactionStateObject->expiration
            ]);
            $this->publish($response, FluxEcoTransactionResponse::new($changedTransactionStateObject->transactionId, $changedTransactionStateObject->lastHandledPage, true)->toJson());
        } catch (Exception $e) {
            $this->publish($response, FluxEcoTransactionResponse::new($transactionStateObject->transactionId, null, false, [$e->getMessage()])->toJson(), FluxEcoHttpStatusCode::BAD_REQUEST->value);
        } finally {
            // clean up any resources allocated during the execution of the coroutine
            // nothing to do at this development tate
        }
    }

    private function readCurrentTransactionId(\Swoole\Http\Request $request, \Swoole\Http\Response $response): string
    {
        $transactionIdCookieName = $this->config->settings->transactionIdCookieName;
        $requestContent = json_decode($request->rawContent());
        if (is_object($requestContent) && $requestContent->page === "create") { //todo
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

    private function publish(\Swoole\Http\Response $response, string $payload, int $statusCode = 200)
    {
        $response->header('Content-Type', 'application/json');
        $response->header('Cache-Control', 'no-cache');
        $response->status($statusCode);
        $response->end($payload);
    }
}