<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter;

use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\DataAdapter;

use SoapFault;

final class RawDataReader implements UniversityEntranceQualification\Configs\RawDataReader
{

    private function __construct(
        private Configs\Config $configs
    )
    {

    }


    public static function new(
        Configs\Config $configs
    ): RawDataReader
    {
        return new self($configs);
    }

    public function getCountrySwitzerlandUniqueId(): int
    {
        return 1234; //todo
    }

    /**
     * @return UniversityEntranceQualification\Data\CertificateType[]
     */
    public function readCertificateTypes(): array
    {
        echo "readCertificateTypes" . PHP_EOL;
        $actionSchema = $this->configs->actionSchemas->getListStudienberechtigungsausweistyp;
        $actionParameters = $this->configs->settings->actionParameters->getListStudienberechtigungsausweistypParamaters;

        $collection = json_decode($this->readCollection($actionSchema, $actionParameters)->{$actionSchema->responseObjectName});;
        $resultList = [];
        foreach ($collection as $item) {
            $resultList[$item->UniqueId] = UniversityEntranceQualification\Data\CertificateType::new(
                $item->UniqueId,
                DataAdapter\Label::newGermanLabel($item->Title),
                $item->WohngemeindeErforderlich
            );
        }
        return $resultList;
    }


    public function readCertificates(): array
    {

        $actionSchema = $this->configs->actionSchemas->getListStudienberechtigungsausweis;
        $actionParameters = $this->configs->settings->actionParameters->getListStudienberechtigungsausweisParamaters;
        $collection = json_decode($this->readCollection($actionSchema, $actionParameters)->{$actionSchema->responseObjectName});

        $resultList = [];
        foreach ($collection as $item) {
            $resultList[$item->UniqueId] = UniversityEntranceQualification\Data\Certificate::new(
                $item->UniqueId,
                DataAdapter\Label::newGermanLabel($item->Title),
                $item->GueltigAb,
                $item->GueltigBis,
                $item->TypUniqueId
            );
        }
        return $resultList;
    }

    /**
     * @return UniversityEntranceQualification\Data\Canton[]
     */
    public function readCantons(): array
    {
        echo "readCantons" . PHP_EOL;
        $actionSchema = $this->configs->actionSchemas->getListKanton;
        $actionParameters = $this->configs->settings->actionParameters->getListKantonParamaters;

        $collection = json_decode($this->readCollection($actionSchema, $actionParameters)->{$actionSchema->responseObjectName});
        $resultList = [];
        foreach ($collection as $item) {
            $resultList[$item->UniqueId] = UniversityEntranceQualification\Data\Canton::new(
                $item->UniqueId,
                DataAdapter\Label::newGermanLabel($item->Title)
            );
        }
        return $resultList;
    }

    /**
     * @return UniversityEntranceQualification\Data\Locality[]
     */
    public function readMunicipalities(): array
    {
        echo "readPlaces" . PHP_EOL;
        $actionSchema = $this->configs->actionSchemas->getListGemeinde;
        $actionParameters = $this->configs->settings->actionParameters->getListGemeindeParamaters;

        $collection = json_decode($this->readCollection($actionSchema, $actionParameters)->{$actionSchema->responseObjectName});
        $resultList = [];
        foreach ($collection as $item) {
            $resultList[$item->UniqueId] = UniversityEntranceQualification\Data\Locality::new(
                $item->UniqueId,
                DataAdapter\Label::newGermanLabel($item->Title),
                $item->Plz,
                $item->KantonUniqueId
            );
        }
        return $resultList;
    }

    /**
     * @return  UniversityEntranceQualification\Data\School[]
     */
    public function readSchools(): array
    {
        echo "readSchools" . PHP_EOL;
        $actionSchema = $this->configs->actionSchemas->getListSchuleMaturitaet;
        $actionParameters = $this->configs->settings->actionParameters->getListSchuleMaturitaetParamaters;

        $collection = json_decode($this->readCollection($actionSchema, $actionParameters)->{$actionSchema->responseObjectName});
        $resultList = [];

        foreach ($collection as $item) {
            $resultList[] = UniversityEntranceQualification\Data\School::new(
                $item->SchuleUniqueId,
                DataAdapter\Label::newGermanLabel($item->Title),
                $item->TypUniqueId,
                $item->StudienberechtigungsausweisUniqueId,
                $item->CantonUniqueId
            );
        }

        return $resultList;
    }

    /**
     * @return UniversityEntranceQualification\Data\Country[]
     */
    public function readCountries(): array
    {
        echo "readCountries" . PHP_EOL;
        $actionSchema = $this->configs->actionSchemas->getListStaat;
        $actionParameters = $this->configs->settings->actionParameters->getListStaatParamaters;

        $collection = json_decode($this->readCollection($actionSchema, $actionParameters)->{$actionSchema->responseObjectName});
        $resultList = [];
        foreach ($collection as $item) {
            $resultList[] = UniversityEntranceQualification\Data\Country::new(
                $item->UniqueId,
                DataAdapter\Label::newGermanLabel($item->Title),
                $item->Code
            );
        }
        return $resultList;
    }


    private function readCollection(Schemas\ActionSchema $actionSchema, object $actionParameters): object|array
    {
        try {
            $client = $this->newSoapClient($actionSchema->wsdlFilePath);

            return $client->{$actionSchema->actionName}( json_decode(json_encode($actionParameters), true));
        } catch (\Exception $e) {
            // Log the error message for debugging purposes
            error_log('SOAP request failed with error: ' . $e->getMessage());
            // Display a user-friendly error message
            echo 'Sorry, an error occurred while processing your request. Please try again later.';
            // Optionally, retry the SOAP request with different parameters here
        }
    }

    private function newSoapClient(
        string $wsdlFilePath
    ): \SoapClient
    {
        try {
            $timeout = 10; // Specify the connection timeout in seconds
            $options = [
                'connection_timeout' => $timeout,
                // Add any additional options here as needed
            ];
            return new \SoapClient($this->configs->settings->soapServerSettings->toString() . "/" . $wsdlFilePath, $options);
            // Use the $soapClient object to make SOAP requests here
        } catch (SOAPFault $e) {
            // Log the error message for debugging purposes
            error_log('SOAP request failed with error: ' . $e->faultstring);
            // Display a user-friendly error message
            echo 'Sorry, an error occurred while processing your request. Please try again later.';
            // Optionally, retry the SOAP request with different parameters here
        }
    }


}