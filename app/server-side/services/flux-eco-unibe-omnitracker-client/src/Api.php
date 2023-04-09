<?php

namespace FluxEco\UnibeOmnitrackerClient;

use FluxEco\UnibeOmnitrackerClient\Types;
use FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi\BaseDataItemAttributesDefinition;
use FluxEcoType\FluxEcoActionDefinition;
use FluxEcoType\FluxEcoResponseDefinition;
use stdClass;

final readonly class Api
{
    private function __construct(
        public Config $config
    )
    {

    }

    public static function new()
    {
        return new self(Config::new());
    }

    public function readBaseDataItemAttributesDefinition(): BaseDataItemAttributesDefinition
    {
        return $this->config->baseDataItemDefinition;
    }

    public function createEnrolment(string $transactionId, string $password): object
    {
        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->createBasisdaten;
        /** @var Types\UnibeOmnitrackerSoapApi\CreateBasisdatenParametersDefinition $createBasisDatenParametersDefinition */
        $createBasisDatenParametersDefinition = $actionDefinition->parametersDefinition;

        $parametersObject = new stdClass();
        $parametersObject->{$createBasisDatenParametersDefinition->pSessionId->name} = $transactionId;
        $parametersObject->{$createBasisDatenParametersDefinition->pUserPassword->name} = $password;
        $parametersObject = $this->hydrateWitDefaults($parametersObject, $createBasisDatenParametersDefinition->defaultParametersDefinition);

        return $this->processOmnitrackerApiRequest($actionDefinition->name, $this->createAbsoluteActionPath($actionDefinition), $parametersObject, $actionDefinition->responseDefinition);
    }

    public function updateEnrolment(string $transactionId, object $baseDataItem): object
    {
        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->updateBasisdaten;
        /** @var Types\UnibeOmnitrackerSoapApi\UpdateBasisdatenParametersDefinition $updateBasisDatenParametersDefinition */
        $updateBasisDatenParametersDefinition = $actionDefinition->parametersDefinition;

        $parametersObject = new stdClass();
        $parametersObject->{$updateBasisDatenParametersDefinition->pSessionId->name} = $transactionId;
        $parametersObject->{$updateBasisDatenParametersDefinition->pObjBasisdaten->name} = $baseDataItem;
        $parametersObject = $this->hydrateWitDefaults($parametersObject, $updateBasisDatenParametersDefinition->defaultParametersDefinition);

        return $this->processOmnitrackerApiRequest($actionDefinition->name, $this->createAbsoluteActionPath($actionDefinition), $parametersObject, $actionDefinition->responseDefinition);
    }

    private function createAbsoluteActionPath(FluxEcoActionDefinition $actionDefinition): string
    {
        return $this->config->createAbsoluteActionPath($this->config->settings->unibeOmnitrackerSoapApiBindingDefinition->toString(), $actionDefinition->path);
    }

    private function hydrateWitDefaults(
        object                                                    $parametersObject,
        Types\UnibeOmnitrackerSoapApi\DefaultParametersDefinition $defaultParametersDefinition
    ): object
    {
        $parametersObject->{$defaultParametersDefinition->pOTUser->name} = $this->config->settings->omnitrackerCredentials->userName;
        $parametersObject->{$defaultParametersDefinition->pOTPassword->name} = $this->config->settings->omnitrackerCredentials->password;
        $parametersObject->{$defaultParametersDefinition->pOTServer->name} = $this->config->settings->omnitrackerServerHost;
        $parametersObject->{$defaultParametersDefinition->pLanguagecode->name} = $this->config->settings->defaultLanguageCode;
        return $parametersObject;
    }

    /**
     * @return object[]
     */
    public function readSalutations(): array
    {
        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->getListAnrede;

        $parametersObject = new stdClass();
        $parametersObject = $this->hydrateWitDefaults($parametersObject, $this->config->settings->defaultActionParameterDefinitions);


        $results = $this->processOmnitrackerApiRequest(
            $actionDefinition->name,
            $this->createAbsoluteActionPath($actionDefinition),
            $parametersObject,
            $actionDefinition->responseDefinition
        );

        $dataList = [];
        foreach ($results as $result) {
            foreach ($result as $item) {
                $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\IdLabelObject::new(
                    $item->UniqueId,
                    Types\ResponseData\Label::newGermanLabel($item->Title),
                )));
            }
        }
        return $dataList;
    }

    /**
     * @return object[]
     */
    public function readMunicipalities(): array
    {
        $requestAction = $this->config->settings->unibeOmnitrackerSoapApiRequestActions->getListGemeinde();
        $results = $this->processOmnitrackerApiRequest($requestAction);

        $dataList = [];
        foreach ($results as $item) {
            $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\Locality::new(
                $item->UniqueId,
                Types\ResponseData\Label::newGermanLabel($item->Title),
                $item->Plz,
                $item->KantonUniqueId
            )));
        }
        return $dataList;
    }

    /**
     * @return object[]
     */
    public function readCertificateTypes(): array
    {
        $requestAction = $this->config->settings->unibeOmnitrackerSoapApiRequestActions->getListStudienberechtigungsausweistyp();
        $results = $this->processOmnitrackerApiRequest($requestAction);

        $dataList = [];
        foreach ($results as $item) {
            $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\CertificateType::new(
                $item->UniqueId,
                Types\ResponseData\Label::newGermanLabel($item->Title),
                $item->WohngemeindeErforderlich
            )));
        }
        return $dataList;
    }

    /**
     * @return object[]
     */
    public function readCertificates(): array
    {
        $requestAction = $this->config->settings->unibeOmnitrackerSoapApiRequestActions->getListStudienberechtigungsausweis();
        $results = $this->processOmnitrackerApiRequest($requestAction);

        $dataList = [];
        foreach ($results as $item) {
            $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\Certificate::new(
                $item->UniqueId,
                Types\ResponseData\Label::newGermanLabel($item->Title),
                $item->GueltigAb,
                $item->GueltigBis,
                $item->TypUniqueId
            )));
        }
        return $dataList;
    }

    /**
     * @return object[]
     */
    public function readCantons(): array
    {
        $requestAction = $this->config->settings->unibeOmnitrackerSoapApiRequestActions->getListKanton();
        $results = $this->processOmnitrackerApiRequest($requestAction);

        $dataList = [];
        foreach ($results as $item) {
            $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\Canton::new(
                $item->UniqueId,
                Types\ResponseData\Label::newGermanLabel($item->Title)
            )));
        }
        return $dataList;
    }

    /**
     * @return object[]
     */
    public function readSchools(): array
    {
        $requestAction = $this->config->settings->unibeOmnitrackerSoapApiRequestActions->getListKanton();
        $results = $this->processOmnitrackerApiRequest($requestAction);

        $dataList = [];
        foreach ($results as $item) {
            $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\School::new(
                $item->SchuleUniqueId,
                Types\ResponseData\Label::newGermanLabel($item->Title),
                $item->TypUniqueId,
                $item->StudienberechtigungsausweisUniqueId,
                $item->CantonUniqueId
            )));
        }
        return $dataList;
    }

    /**
     * @return object[]
     */
    public function readCountries(): array
    {
        $requestAction = $this->config->settings->unibeOmnitrackerSoapApiRequestActions->getListStaat();
        $results = $this->processOmnitrackerApiRequest($requestAction);

        $dataList = [];
        foreach ($results as $item) {
            $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\Country::new(
                $item->UniqueId,
                Types\ResponseData\Label::newGermanLabel($item->Title),
                $item->Code
            )));
        }
        return $dataList;
    }

    private function processOmnitrackerApiRequest(
        string                    $actionName,
        string                    $absoluteActionPath,
        object                    $actionParameters,
        FluxEcoResponseDefinition $actionResponseDefinition): object|array
    {
        try {
            $options = [
                'connection_timeout' => 10,
            ];


            $soapClient = new \SoapClient($absoluteActionPath, $options);
            $response = $soapClient->{$actionName}($actionParameters);

            //return json_decode($response->{$actionResponseDefinition->data->name});
            return $response->{$actionResponseDefinition->data->name};
        } catch (\Exception $e) {
            error_log('SOAP request failed with error: ' . $e->getMessage());
            echo 'Sorry, an error occurred while processing your request. Please try again later.';
        }
    }
}