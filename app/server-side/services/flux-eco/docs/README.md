# Road Map
## bounded context
Define a strict context for Flux-Eco (definitions), especially for implemented classes, and ensure that it does not become too large.

## rename types
rename types - dirs to definitions
## auto generate definition objects from schema AND/OR make the objects obsolete
auto generate instance creation of definition object from json definition files
and/or make them obsolete by validate the json definition files by corresponding schema
and handle the processes in a generic way


## object creator
change
```
$parameters = new stdClass();
$parameters->{$parametersDefinition->someAttributeName->name} = 1;
$parameters = $this->hydrateObject($parameters, $this->config->settings->defaultActionParameterDefinitions);
```

to
```
    objectBuilder->build(
        [
            $parametersDefinition->someAttributeName->name => 1,
            ...$this->config->settings->defaultActionParameterDefinitions
        ]
    )
```

## FluxEcoAttributeDefinition
FluxEcoAttributeDefinition could be enhanced with an optional object where each property
is a named mapping-definition. Like this an attribute definition could have a set of standard mappings
for his standard contexts.

## OutputDataObjectDefinitions in Workflow
- split up the definition in worklfow steps
- add json schema for validation