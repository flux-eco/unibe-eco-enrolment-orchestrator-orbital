<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Api;

use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Dispatchers;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Repositories;

use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config\Config;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;

use Swoole\Http;
use stdClass;


class HttpApi
{

    private function __construct(
        private Config        $config,
        private Ports\Service $service
    )
    {

    }

    public static function new(): self
    {
        $config = Config::new();

        $repository =   Repositories\EnrolmentConfigurationReferenceObjectRepository::new(
            $config->configFilesDirectoryPath,
            $config->soapWsdlServer,
            $config->soapServerHost,
            $config->credentials,
            $config->degreeProgramSubjectFilter
        );

        return new self(
            $config,
            Ports\Service::new(
                Ports\Outbounds::new(
                    "/opt/unibe-eco-enrolment-orchestrator-orbital/configs/",
                    $repository,
                    Dispatchers\ConfiguratioinMessageDispatcher::new(
                        $repository
                    ),
                    Dispatchers\EnrolmentMessageDispatcher::new(),
                    Repositories\EnrolmentRepository::new(
                        $config->soapWsdlServer,
                        $config->soapServerHost,
                        $config->credentials
                    )
                )
            )
        );
    }

    /**
     * @throws \Exception
     */
    final public function handleHttpRequest(Http\Request $request, Http\Response $response): void
    {
        $cookies = $request->cookie;

        $currentPage = ValueObjects\PageName::START;
        if (is_array($cookies) && array_key_exists(ValueObjects\ValueObjectName::CURRENT_PAGE->toParameterName(), $cookies)) {
            $currentPage = ValueObjects\PageName::from($cookies[ValueObjects\ValueObjectName::CURRENT_PAGE->toParameterName()]);
        }

        $sessionId = ValueObjects\SessionId::new()->id;
        if (is_array($cookies) && array_key_exists(ValueObjects\ValueObjectName::SESSION_ID->toParameterName(), $cookies)) {
            $sessionId = $cookies[ValueObjects\ValueObjectName::SESSION_ID->toParameterName()];
        }

        $identificationNumber = "";
        if (is_array($cookies) && array_key_exists(ValueObjects\ValueObjectName::IDENTIFICATION_NUMBER->toParameterName(), $cookies)) {
            $identificationNumber = $cookies[ValueObjects\ValueObjectName::IDENTIFICATION_NUMBER->toParameterName()];
        }

        $enrolmentData = ValueObjects\EnrolmentData::new();
        if (is_array($cookies) && array_key_exists(ValueObjects\ValueObjectName::ENROLMENT_DATA->toParameterName(), $cookies)) {
            $enrolmentData = ValueObjects\EnrolmentData::fromJson($cookies[ValueObjects\ValueObjectName::ENROLMENT_DATA->toParameterName()]);
        }


        $requestData = json_decode($request->rawContent());

        echo PHP_EOL;
        echo $request->server['request_uri'];
        print_r($currentPage);
        match ($request->server['request_uri']) {
            '/api/layout' => $this->service->provideLayout(Ports\IncomingMessages\ProvideLayout::new($this->config->valueObjectsConfigDirectoryPath), $this->publish($response)),
            '/api/get' => $this->service->providePage(Ports\IncomingMessages\ProvidePage::new($this->config->pageObjectDirectoryPath, $currentPage, $identificationNumber, $enrolmentData), $this->publish($response)),
            '/api/post' => $this->service->storeData(Ports\IncomingMessages\StoreData::new(ValueObjects\PageName::from($requestData->page), $sessionId, $requestData->data, $enrolmentData), $this->publish($response)),
        };


        /*
        match ($request->server['request_uri']) {
            '/api/generateJsonDocuments' => $this->generateJsonDocuments($this->publish($response))
        };




        echo $request->server['request_uri']; exit;

        match ($request->server['request_uri']) {
            '/api/layout' => $this->publish($response)(file_get_contents(__DIR__ . '/../../../JsonDocuments/layout.json')),
            '/api/get' => $this->publish($response)(
                Domain\Page::new(
                    'start',
                    Domain\Data::new([
                        Adapters\Values\Value::SEMESTERS,
                        Adapters\Values\Value::MIN_PASSWORD_LENGTH
                    ]),
                    false
                )
            ),
            '/api/post' => $this->publish($response)(
                'ok'
            ),
        };
        print_r($requestData);*/
    }


    private function publish(Http\Response $response)
    {
        return function (OutgoingMessages\Message $message) use ($response) {

            if (count($message->headers->cookies) > 0) {
                foreach ($message->headers->cookies as $name => $value) {
                    $response->setCookie($name, $value, time() + 3600);
                }
            }

            match ($message->headers->name) {
                OutgoingMessages\MessageName::DATA_STORED->value,
                OutgoingMessages\MessageName::PUBLISH_PAGE_OBJECT->value,
                OutgoingMessages\MessageName::PROVIDE_CONFIGURATION_OBJECT->value => $this->response($response, $message->payload),
                default => []
            };
        };
    }

    private function response(Http\Response $response, object $payload)
    {
        $response->header('Content-Type', 'application/json');
        $response->header('Cache-Control', 'no-cache');

        $response->end(json_encode($payload));
    }

}