FROM alpine:3.14

LABEL io.stack.enable true
LABEL io.stack.type operator

LABEL org.opencontainers.image.source https://github.com/sitepilot/dockerfiles

RUN apk add --no-cache \
    restic \
    rsync \
    openssh-client \
    mysql-client
