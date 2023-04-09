<?php

namespace FluxEco\ObjectMapper;

use FluxEco\ObjectMapper\Types\MappingOptions;
use mysql_xdevapi\Exception;

class Api
{

    private function __construct()
    {

    }

    public static function new(): self
    {
        return new self();
    }

    /**
     * @param object $srcObject
     * @param array $srcKeyToNewKey
     * @return object
     */
    public function createMappedObject(
        object $srcObject, array $srcKeyToNewKey, ?MappingOptions $mappingOptions = null
    )
    {
        $newObject = new \stdClass();
        if ($mappingOptions->printSrcAttributesWhileMapping === true) {
            print_r($srcObject);
        }
        foreach ($srcKeyToNewKey as $scrKey => $newKey) {
            if (property_exists($srcObject, $scrKey) === false) {
                if ($mappingOptions->setStateValueIfSourceKeyNotExists !== null) {
                    $newObject->{$newKey} = $mappingOptions->setStateValueIfSourceKeyNotExists->stateObjectAttributes->{$newKey};
                    continue;
                }
                throw new Exception("srcKey not found: " . $scrKey);
            }
            $newObject->{$newKey} = $srcObject->{$scrKey};
        }
        return $newObject;
    }

    /**
     * @param array $srcObject
     * @param array $srcKeyToNewKey
     * @return array
     */
    public function createMappedObjectList(
        array $srcObjects, array $srcKeyToNewKey, ?MappingOptions $mappingOptions = null
    )
    {
        $mappedObjects = [];
        foreach ($srcObjects as $srcObject) {
            $mappedObjects[] = $this->createMappedObject($srcObject, $srcKeyToNewKey, $mappingOptions);
        }
        return $mappedObjects;
    }
}