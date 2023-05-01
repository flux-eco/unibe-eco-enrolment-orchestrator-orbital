<?php

namespace FluxEco\HttpWorkflowRequestHandler;

use Exception;
use stdClass;

final readonly class Api
{

    private function __construct(
        private State $state
    )
    {

    }

    public static function new(
        State $state
    ): self
    {
        return new self(
            $state
        );
    }


    /**
     * @throws \Exception
     */
    public function handleHttpGetRequest(\Swoole\Http\Request  $request,
                                         \Swoole\Http\Response $response,
                                         callable              $objectFromJsonFile,
                                         callable              $readCookie,
                                         callable              $storeCookie,
                                         callable              $readTransactionStateValuesFromCache,
                                         callable              $storeTransactionStateValuesInCache,
                                         callable              $readTransactionStateValuesFromManager): void
    {
        $content = $this->state->contentByGetRequest($request->server["request_uri"], $objectFromJsonFile, $readCookie, $storeCookie, $readTransactionStateValuesFromCache, $storeTransactionStateValuesInCache, $readTransactionStateValuesFromManager);
        $this->publish($response, json_encode($content));
    }

    /**
     * @throws Exception
     */
    public function handleHttpPostRequest(\Swoole\Http\Request  $request,
                                          \Swoole\Http\Response $response,
                                          callable              $readCookie,
                                          callable              $storeCookie,
                                          callable              $readTransactionStateValuesFromCache,
                                          callable              $storeTransactionStateValuesInCache,
                                          callable              $readTransactionStateValuesFromManager,
                                          callable              $processDataByTransactionStateManager
    ): void
    {
        try {
            $this->state->processDataFromPostRequest(json_decode($request->rawContent()), $readCookie, $storeCookie, $readTransactionStateValuesFromCache, $storeTransactionStateValuesInCache, $readTransactionStateValuesFromManager, $processDataByTransactionStateManager);
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }

        $responseContent = new stdClass();
        $responseContent->ok = true;
        $responseContent->{"error-messages"} = null;
        $this->publish($response, json_encode($responseContent));
    }


    private function publish(\Swoole\Http\Response $response, string $content, int $statusCode = 200)
    {
        $response->header('Content-Type', 'application/json');
        $response->header('Cache-Control', 'no-cache');
        $response->status($statusCode);
        $response->end($content);
    }
}