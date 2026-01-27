FROM alpine:3.19

RUN apk add --no-cache bash coreutils findutils

ENV SESSION_PATH=/var/lib/php/sessions \
    SESSION_MAX_LIFETIME=3600 \
    CRON_SCHEDULE="*/5 * * * *"

COPY gc_sessions.sh /app/gc_sessions.sh
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /app/gc_sessions.sh /entrypoint.sh

CMD ["/entrypoint.sh"]
