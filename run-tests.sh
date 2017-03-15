#!/usr/bin/env bash

phpunit --colors=auto --verbose --debug --bootstrap test-environment.php tests
#phpunit --debug --stop-on-failure --bootstrap test-environment.php tests # Stop tests when an error or failure occurred