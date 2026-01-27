#!/bin/sh
set -e

# Write cron entry using current env
# Runs as root in alpine cron (uses busybox crond https://wiki.alpinelinux.org/wiki/Cron#Busybox_crond)
echo "${CRON_SCHEDULE} /app/gc_sessions.sh" > /etc/crontabs/root

echo "$(date -u +%Y-%m-%dT%H:%M:%SZ) session-gc scheduled: ${CRON_SCHEDULE} path=${SESSION_PATH} max_lifetime_s=${SESSION_MAX_LIFETIME}" >&2

exec crond -f -L /dev/stdout
