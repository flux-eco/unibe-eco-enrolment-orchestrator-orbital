<?php

use FluxEco\UnibeEnrolment;
use FluxEco\HttpWorkflowRequestHandler;
use FluxEcoType\FluxEcoContentType;
use FluxEcoType\FluxEcoExceptionDefinitions\FluxEcoException;
use FluxEcoType\FluxEcoHttpStatusCode;
use FluxEcoType\FluxEcoResponseDefinition;

final readonly class Api
{
    public Adapters $adapters;
    public UnibeEnrolment\Api $unibeEnrolmentApi;
    public HttpWorkflowRequestHandler\Api $httpWorkflowRequestHandlerApi;

    private function __construct()
    {
        $this->adapters = Adapters::new();
        $this->unibeEnrolmentApi = UnibeEnrolment\Api::new(
            UnibeEnrolment\Types\Outbounds::new(
                $this->adapters->newUnibeEnrolmentOutboundsActionsProcessor()
            )
        );
        $this->httpWorkflowRequestHandlerApi = HttpWorkflowRequestHandler\Api::new(HttpWorkflowRequestHandler\Types\Outbounds::new(
            $this->adapters->newHttpWorkflowRequestHandlerOutboundsActionsProcessor($this->unibeEnrolmentApi)
        ));
    }

    public static function new()
    {
        return new self();
    }


    public function handleHttpRequest(Swoole\Http\Request $request, Swoole\Http\Response $response, Swoole\Table $transactionDataCache): void
    {
        $this->httpWorkflowRequestHandlerApi->handleHttpRequest($request, $response, $transactionDataCache);
    }
}