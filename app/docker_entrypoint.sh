#!/bin/sh
set -e

function install {
  php /opt/unibe-eco-enrolment-orchestrator-orbital/app/cli/createEnrolmentConfiguration.php
}

install
flux-eco-http-synapse.start