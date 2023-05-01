<?php

use FluxEcoType\FluxEcoStateValues;

final readonly class Api
{
    public Adapters $adapters;

    /**
     * @throws Exception
     */
    private function __construct()
    {
        $this->adapters = Adapters::new();
    }

    public static function new(): Api
    {
        return new self();
    }

    /**
     * @throws Exception
     */
    public function handleHttpRequest(Swoole\Http\Request $request, Swoole\Http\Response $response, Swoole\Table $transactionDataCache): void
    {
        match ($request->server["request_method"]) {
            "GET" => $this->adapters->httpTransactionGateway->handleHttpGetRequest($request, $response, $this->adapters->objectFromJsonFile(), $this->adapters->readCookie($request), $this->adapters->storeCookie($response), $this->adapters->readTransactionStateValuesFromCache($transactionDataCache), $this->adapters->storeTransactionStateValuesInCache($transactionDataCache), $this->adapters->readTransactionStateValuesFromManager()),
            "POST" => $this->adapters->httpTransactionGateway->handleHttpPostRequest($request, $response, $this->adapters->readCookie($request), $this->adapters->storeCookie($response), $this->adapters->readTransactionStateValuesFromCache($transactionDataCache), $this->adapters->storeTransactionStateValuesInCache($transactionDataCache), $this->adapters->readTransactionStateValuesFromManager(), $this->adapters->processDataByTransactionStateManager())
        };
    }
}