<?php

namespace FluxEco\HttpWorkflowRequestHandler;

final readonly class Config
{
    public string $name;
    public Types\Settings  $settings;
    private function __construct(
        public Types\Outbounds $outbounds
    )
    {
        $this->name = "flux-eco-http-workflow-request-handler";
        $this->settings = Types\Settings::new(sprintf('%s/%s', $this->name, "transaction-id"));
    }

    public static function new(Types\Outbounds $outbounds)
    {
        return new self(
            $outbounds
        );
    }

}