#!/bin/sh
set -e

function install {
  php cli/createEnrolmentConfiguration.php
}

install
flux-eco-http-synapse.start