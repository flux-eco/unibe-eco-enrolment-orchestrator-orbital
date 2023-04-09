<?php

namespace FluxEcoType;

use Closure;
use Exception;

final readonly class FluxEcoWorkflowDefinitionNextPageName
{

    private function __construct(
       private string|Closure $nextPageName
    )
    {

    }

    /**
     * @param string|Closure  $nextPageName
     * next page can be either a string indicating the next page or a Closure that takes a
     * FluxEcoTransactionStateObject parameter and returns the next page name as a string.
     */
    public static function new(
        string|Closure $nextPageName
    ): self
    {
        return new self($nextPageName);
    }

    /**
     * @throws Exception
     */
    public function getNextPageName(FluxEcoTransactionStateObject $transactionStateObject): string
    {
        if (is_string($this->nextPageName) === true) {
            return $this->nextPageName;
        }
        return call_user_func($this->nextPageName, $transactionStateObject);
    }
}