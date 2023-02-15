#!/bin/bash
sh install-dependencies.sh
docker build ../ -f Dockerfile --target unibe-eco-enrolment-orchestrator-orbital -t fluxms/unibe-eco-enrolment-orchestrator-orbital:v2023-02-15-1
