FROM fluxms/flux-eco-biotope:v2022-04-16-1 AS unibe-eco-enrolment-orchestrator-orbital
USER root

# |-------------------------------------------------------------------------- \
# | author martin@fluxlabs.ch
# |-------------------------------------------------------------------------- \

# |-------------------------------------------------------------------------- \
# | General Environment Variables
# |-------------------------------------------------------------------------- \
ENV FLUX_ECO_HTTP_SYNAPSE_AUTOLOAD_FILE_PATH=/opt/unibe-eco-enrolment-orchestrator-orbital/app/autoload.php
ENV FLUX_ECO_HTTP_SYNAPSE_HTTP_API_FQCN=HttpApi

ENV FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_DATA_DIRECTORY_PATH=/opt/unibe-eco-enrolment-orchestrator-orbital/app/server-side/data
ENV FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_WSDL_SERVER_HOST_FILE=/opt/unibe-eco-enrolment-orchestrator-orbital/secrets/soap-wsdl-server-host
ENV FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_WSDL_SERVER_PROTOCOL=https
ENV FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_WSDL_SERVER_PORT=443

ENV FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_SERVER_HOST_FILE=/opt/unibe-eco-enrolment-orchestrator-orbital/secrets/soap-server-host

ENV FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_USER_FILE=/opt/unibe-eco-enrolment-orchestrator-orbital/secrets/soap-user
ENV FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_PASSWORD_FILE=/opt/unibe-eco-enrolment-orchestrator-orbital/secrets/soap-password

ENTRYPOINT ["sh", "./docker_entrypoint.sh"]

COPY ./app /opt/unibe-eco-enrolment-orchestrator-orbital/app

RUN (cd /var/www/html && wget -O - https://github.com/fluxfw/flux-studis-selfservice-frontend/releases/download/v2023-04-12-1/flux-studis-selfservice-frontend-v2023-04-12-1-build.tar.gz| tar -xz --strip-components=1)
COPY ./docker/assets/logo.svg /var/www/html/Adapter/Logo/logo.svg

#RUN chown -R flux-eco:flux-eco "/opt/unibe-eco-enrolment-orchestrator-orbital/configs/reference-objects"

WORKDIR /opt/unibe-eco-enrolment-orchestrator-orbital/app
ENTRYPOINT ["sh", "./docker_entrypoint.sh"]