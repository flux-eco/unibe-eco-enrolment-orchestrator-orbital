<?php

namespace FluxEcoType;

use Closure;
use Exception;

final readonly class FluxEcoWorkflowDefinitionNextPageAsPropertyName
{

    private function __construct(
       private string|Closure $nextPagePropertyName
    )
    {

    }

    /**
     * @param string|Closure  $nextPagePropertyName
     * next page can be either a string indicating the next page or a Closure that takes a
     * FluxEcoTransactionStateObject parameter and returns the next page name as a string.
     */
    public static function new(
        string|Closure $nextPagePropertyName
    ): self
    {
        return new self($nextPagePropertyName);
    }

    /**
     * @throws Exception
     */
    public function getNextPageAsPropertyName(FluxEcoTransactionStateObject $transactionStateObject): string
    {
        if (is_string($this->nextPagePropertyName) === true) {
            return $this->nextPagePropertyName;
        }
        return call_user_func($this->nextPagePropertyName, $transactionStateObject);
    }
}