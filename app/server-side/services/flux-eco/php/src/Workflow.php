<?php

namespace FluxEcoType;

use Exception;

final readonly class Workflow
{

    /**
     * @param FluxEcoWorkflowDefinitionNextPageAsPropertyName[] $nextPageNames
     */
    private function __construct(
        public string $startPageName,
        private array $nextPageNames,
    )
    {

    }


    /**
     * @param FluxEcoWorkflowDefinitionNextPageAsPropertyName[] $nextPageNames
     * An associative array with key as previous handled page name and value as the next page name.
     * Value can be either a string indicating the next page or a closure that takes a
     * FluxEcoTransactionStateObject parameter and returns the next page name as a string.
     */
    public static function new(
        string $startPageName,
        array $nextPageNames
    ): self
    {
        return new self(...get_defined_vars());
    }

    /**
     * @throws Exception
     */
    public function getNextPage(?string $lastHandledPage, FluxEcoTransactionStateObject $transactionStateObject): string
    {
        if($lastHandledPage === null) {
            return $this->startPageName;
        }

        if (array_key_exists($lastHandledPage, $this->nextPageNames) === false) {
            throw new Exception("no further page found - last handled page: " . $lastHandledPage. " searched in: ".print_r($this->nextPageNames, true));
        }
        return $this->nextPageNames[$lastHandledPage]->getNextPageAsPropertyName($transactionStateObject);
    }
}