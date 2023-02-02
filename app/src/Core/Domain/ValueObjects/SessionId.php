<?php

namespace  UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

use Exception;

final readonly class SessionId
{
    private function __construct(
       public string $id
    )
    {

    }

    /**
     * @throws Exception
     */
    public static function new() : self
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6])&0x0f|0x40); // set version to 0100
        $data[8] = chr(ord($data[8])&0x3f|0x80); // set bits 6-7 to 10
        $id = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        return new self($id);
    }
}