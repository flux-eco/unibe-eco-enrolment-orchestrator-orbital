<?php

namespace FluxEco\UnibeEnrolment;

final readonly class Config
{
    private function __construct(
        public Types\Settings  $settings,
        public Types\Outbounds $outbounds
    )
    {

    }

    public static function new(
        Types\Outbounds $outbounds
    ): self
    {
        return new self(
            Types\Settings::new(true),
            $outbounds
        );
    }

}