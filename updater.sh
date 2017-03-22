#!/bin/bash

#
# Variables (may be overriden by environment)
#

STACKGURU_ENV=${STACKGURU_ENV:-"DEVELOPMENT"}
WORKSPACE_DIR=${WORKSPACE_DIR:-"$(dirname "$0")"}
UPDATER_LOG=${UPDATER_LOG:-"$HOME/discord_autoupdate.log"}
LOCK_FILE=${LOCK_FILE:-"/tmp/discord_updater.lock"}
PHP_ERROR_LOG_FILE=${PHP_ERROR_LOG_FILE:-"/var/log/php_errors.log"}
SYSTEMD_SERVICE=${SYSTEMD_SERVICE:-"stackguru.service"}

GIT_REMOTE=${GIT_REMOTE:-"origin"}
GIT_BRANCH=${GIT_BRANCH:-""}

if [ "${STACKGURU_ENV,,}" = "development" ]; then
  DEV_MODE=1
else
  DEV_MODE=0
  GIT_BRANCH=${GIT_BRANCH:-"master"}
fi

#
# Constants
#

BINARY_DEPENDENCIES=(  "sudo"  "git"  "composer"  "systemctl"  )
FILE_DEPENDENCIES=(
  "$WORKSPACE_DIR"
  "$WORKSPACE_DIR/run-bot.sh"
)
SERVICE_DEPENDENCIES=( "$SYSTEMD_SERVICE" )
#SERVICE_DEPENDENCIES=( "mysql.service" "$SYSTEMD_SERVICE" )


#
# Helper functions
#

# Format output
function log_formatted {
  NOW=$(date --utc +"%Y-%m-%d %H:%I:%S")
  echo "${NOW} | $@"
}

# Log standard output
function log_echo {
  # Log to stderr if DEBUG is set, otherwise log to UPDATER_LOG
  message="$@"
  if [ -z $DEV_MODE ]; then
    log_formatted "$message" >> $UPDATER_LOG
  else
    log_formatted "$message" >&1
  fi
}

# Log error output
function log_error {
  # Log to stderr if DEBUG is set, otherwise log to UPDATER_LOG
  message="ERROR: $@"
  if [ -z $DEV_MODE ]; then
    log_formatted "$message" >> $UPDATER_LOG
  else
    log_formatted "$message" >&2
  fi
}

# Log error output and quit
function log_fatal {
  log_error "$@.  Aborting."
  exit 1
}

# Check for binary in path
function check_binary_dependency {
  hash "$1" 2>/dev/null || log_fatal "Dependency '$1' is not installed or not in PATH."
}

# Check for file existence
function check_file_dependency {
  [ -e "$1" ] || log_fatal "File or folder '$1' does not exist."
}

# Check for systemd service existence
function check_service_dependency {
  if ! systemctl list-unit-files | grep "$1" >/dev/null; then
    log_fatal "Service '$1' does not exist."
  fi
}


# Lock/Unlock workspace
function lock_workspace {
  [ -f $LOCK_FILE ] && log_fatal "Workspace is locked already. Remove '${LOCK_FILE}' if necessary."
  touch $LOCK_FILE

  # Stop bot
  log_echo "Stopping service '${SYSTEMD_SERVICE}'..."
  if ! sudo systemctl stop "${SYSTEMD_SERVICE}"; then
    log_error "Failed to stop service '${SYSTEMD_SERVICE}'."
    unlock_workspace
    exit 2
  fi
}

function unlock_workspace {
  # Unlock workspace
  rm $LOCK_FILE

  # Restart bot
  log_echo "Restarting service '${SYSTEMD_SERVICE}'..."
  if ! sudo systemctl restart "${SYSTEMD_SERVICE}"; then
    log_error "Failed to restart service '${SYSTEMD_SERVICE}'."
    exit 2
  fi
  log_echo "Service '${SYSTEMD_SERVICE}' restarted."
}


#
# Check dependencies
#

# Binary dependencies
for dependency in "${BINARY_DEPENDENCIES[@]}"; do
  check_binary_dependency "$dependency"
done

# File dependencies
for dependency in "${FILE_DEPENDENCIES[@]}"; do
  check_file_dependency "$dependency"
done

# Service dependencies
for dependency in "${SERVICE_DEPENDENCIES[@]}"; do
  check_service_dependency "$dependency"
done


#
# Update mechanism
#

# Lock workspace
lock_workspace

# Switch to workspace directory
[ ! -z "$WORKSPACE_DIR" ] && cd "$WORKSPACE_DIR"

# Update source code
log_echo "Updating source code..."
git reset --hard >/dev/null
git pull "$GIT_REMOTE" "$GIT_BRANCH" >/dev/null

# Update composer dependencies
log_echo "Updating dependencies..."
if [ "$DEV_MODE" = "1" ]; then
  composer install
else
  composer install --no-dev --optimize-autoloader
fi
log_echo "Updated dependencies"

# Log update
COMMIT=$(git log -1 --oneline)
VERSION=$(git describe 2>/dev/null)
if [ -z $VERSION ]; then
  VERSION="${COMMIT}"
else
  VERSION="${VERSION} (${COMMIT})"
fi
log_echo "Updated source code! HEAD is now at: ${VERSION}"

# Unlock workspace, restart bot
unlock_workspace

exit 0
