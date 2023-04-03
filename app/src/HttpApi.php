<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Api;


class HttpApi
{

    private function __construct(
        private Config                      $config,
        private \archive\Core\Ports\Service $service
    )
    {

    }

    public static function new(): self
    {
        $config = Config::new();

        $repository = \archive\Repositories\EnrolmentConfigurationReferenceObjectRepository::new(
            $config->configFilesDirectoryPath,
            $config->soapWsdlServer,
            $config->soapServerHost,
            $config->credentials,
            $config->degreeProgramSubjectFilter
        );

        return new self(
            $config,
            \archive\Core\Ports\Service::new(
                \archive\Core\Ports\Outbounds::new(
                    "/opt/unibe-eco-enrolment-orchestrator-orbital/configs/",
                    $repository,
                    \archive\Dispatchers\ConfiguratioinMessageDispatcher::new(
                        $repository
                    ),
                    \archive\Dispatchers\EnrolmentMessageDispatcher::new(),
                    \archive\Repositories\EnrolmentRepository::new(
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

        $currentPage = \archive\Core\Domain\ValueObjects\PageName::START;
        if (is_array($cookies) && array_key_exists(\archive\Core\Domain\ValueObjects\ValueObjectName::CURRENT_PAGE->toParameterName(), $cookies)) {
            $currentPage = \archive\Core\Domain\ValueObjects\PageName::from($cookies[\archive\Core\Domain\ValueObjects\ValueObjectName::CURRENT_PAGE->toParameterName()]);
        }

        $sessionId = \archive\Core\Domain\ValueObjects\SessionId::new()->id;
        if (is_array($cookies) && array_key_exists(\archive\Core\Domain\ValueObjects\ValueObjectName::SESSION_ID->toParameterName(), $cookies)) {
            $sessionId = $cookies[\archive\Core\Domain\ValueObjects\ValueObjectName::SESSION_ID->toParameterName()];
        }

        $identificationNumber = "";
        if (is_array($cookies) && array_key_exists(\archive\Core\Domain\ValueObjects\ValueObjectName::IDENTIFICATION_NUMBER->toParameterName(), $cookies)) {
            $identificationNumber = $cookies[\archive\Core\Domain\ValueObjects\ValueObjectName::IDENTIFICATION_NUMBER->toParameterName()];
        }

        $enrolmentData = \archive\Core\Domain\ValueObjects\EnrolmentData::new();
        if (is_array($cookies) && array_key_exists(\archive\Core\Domain\ValueObjects\ValueObjectName::ENROLMENT_DATA->toParameterName(), $cookies)) {
            $enrolmentData = \archive\Core\Domain\ValueObjects\EnrolmentData::fromJson($cookies[\archive\Core\Domain\ValueObjects\ValueObjectName::ENROLMENT_DATA->toParameterName()]);
        }


        $requestData = json_decode($request->rawContent());

        match ($request->server['request_uri']) {
            '/api/layout' => $this->service->provideLayout(\archive\Core\Ports\IncomingMessages\ProvideLayout::new($this->config->DataAdapterConfigDirectoryPath), $this->publish($response)),
            '/api/get' => $this->service->providePage(\archive\Core\Ports\IncomingMessages\ProvidePage::new($this->config->pageObjectDirectoryPath, $currentPage, $identificationNumber, $enrolmentData), $this->publish($response)),
            '/api/post' => $this->service->storeData(\archive\Core\Ports\IncomingMessages\StoreData::new(\archive\Core\Domain\ValueObjects\PageName::from($requestData->page), $sessionId, $requestData->data, $enrolmentData), $this->publish($response)),
        };
    }


    private function publish(Http\Response $response)
    {
        return function (\archive\Core\Domain\OutgoingMessages\Message $message) use ($response) {

            if (count($message->headers->cookies) > 0) {
                foreach ($message->headers->cookies as $name => $value) {
                    $response->setCookie($name, $value, time() + 3600);
                }
            }

            match ($message->headers->name) {
                \archive\Core\Domain\OutgoingMessages\MessageName::DATA_STORED->value,
                \archive\Core\Domain\OutgoingMessages\MessageName::PUBLISH_PAGE_OBJECT->value,
                \archive\Core\Domain\OutgoingMessages\MessageName::PROVIDE_CONFIGURATION_OBJECT->value => $this->response($response, $message->payload),
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