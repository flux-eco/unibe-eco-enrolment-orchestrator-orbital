#!/bin/bash
sh install-dependencies.sh
docker build ../ -f Dockerfile --target unibe-eco-enrolment-orchestrator-orbital -t fluxms/unibe-eco-enrolment-orchestrator-orbital:v2022-04-16-1