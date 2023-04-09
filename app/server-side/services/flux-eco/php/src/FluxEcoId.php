<?php

namespace FluxEcoType;

final readonly class FluxEcoId
{
    private function __construct(
        public string $id,
        public string $type
    )
    {

    }

    public static function newUuid4(): self
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set variant
        return new self(
            vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)),
            "uuid"
        );
    }

    public static function newHashedUuid4(): self
    {
        $uuid = self::newUuid4();
        $hashedUuid = password_hash($uuid->id, PASSWORD_DEFAULT);
        return new self(
            $hashedUuid,
            "hashedUuid4"
        );
    }
}