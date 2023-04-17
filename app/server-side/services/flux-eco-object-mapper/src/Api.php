<?php

namespace FluxEco\ObjectMapper;

use FluxEco\ObjectMapper\Types\MappingOptions;
use FluxEcoType\FluxEcoAttributeDefinition;
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
     * @param array $srcKeyToAttributeDefinition new key is either a FluxEcoAttributeDefinition or a callable with the src object, the new object and with setStateValueIfSourceKeyNotExists the state object as parameters and the hydrated new object as result
     * @param MappingOptions|null $mappingOptions
     * @return object
     *
     * todo split up in separate methods / handlers / middlewares
     */
    public function createMappedObject(
        object $srcObject, array $srcKeyToAttributeDefinition, ?MappingOptions $mappingOptions = null
    )
    {
        if ($mappingOptions === null) {
            $mappingOptions = MappingOptions::new();
        }
        if ($mappingOptions->printSrcAttributesWhileMapping === true) {
            echo "srcObject " . print_r($srcObject, true) . PHP_EOL;
        }
        $newObject = new \stdClass();
        if ($mappingOptions->prefillFromCurrentState !== null) {
            foreach (get_object_vars($mappingOptions->prefillFromCurrentState->stateObject) as $key => $value) {
                $newObject->{$key} = $value;
            }
        }
        echo "newObject" . PHP_EOL . PHP_EOL;
        print_r($newObject);
        echo PHP_EOL . PHP_EOL;
        echo "srcObject" . PHP_EOL . PHP_EOL;
        print_r($srcObject);
        echo PHP_EOL . PHP_EOL;

        foreach ($srcObject as $scrKey => $srcValue) {

            if(array_key_exists($scrKey, $srcKeyToAttributeDefinition) === false) {
                echo "no mapping definition found for " .$scrKey." ". PHP_EOL;
                continue;
            }

            $toAttributeDefinition = $srcKeyToAttributeDefinition[$scrKey];

            if (is_callable($toAttributeDefinition)) {
                if ($mappingOptions->prefillFromCurrentState !== null) {
                    $newObject = $toAttributeDefinition($srcObject, $newObject, $mappingOptions->prefillFromCurrentState->stateObject);
                    echo "mapped srcKeyToNewKey " . $scrKey . " by callable" . PHP_EOL;
                    continue;
                }
                $newObject = $toAttributeDefinition($srcObject, $newObject);
                echo "mapped srcKeyToNewKey " . $scrKey . " by callable" . PHP_EOL;
                continue;
            }


            /** @var FluxEcoAttributeDefinition $toAttributeDefinition */

            if ($mappingOptions->prefillFromCurrentState !== null) {
                if (property_exists($mappingOptions->prefillFromCurrentState->stateObject, $toAttributeDefinition->name) === false) {
                    echo "MISS CONFIGURATION in stateObjectAttributes " . $toAttributeDefinition->name . " - " . print_r($mappingOptions->prefillFromCurrentState->stateObject, true) . PHP_EOL;
                    continue;
                }
            }
            //todo option if there should be throw an exception with not existing source keys
            //throw new Exception("srcKey not found: " . $scrKey);

            $srcValue = $srcObject->{$scrKey};
            settype($srcValue, ($toAttributeDefinition->type));

            $newObject->{$toAttributeDefinition->name} = $srcValue;
            echo "mapped srcKeyToNewKey " . $scrKey . " - " . $toAttributeDefinition->name . PHP_EOL;
        }
        return $newObject;
    }

    /**
     * @param array $srcObjects
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