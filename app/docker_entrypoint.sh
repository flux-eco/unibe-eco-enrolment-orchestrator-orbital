#!/bin/sh
set -e

function configure {
  php server-side/configure.php
}

#configure
flux-eco-http-synapse.start