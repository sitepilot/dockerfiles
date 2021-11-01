FROM ubuntu:20.04

# Build args
ARG PHP_VERSION=80
ARG PHP_DOT_VERSION=8.0

# Environment
ENV PHP_VERSION $PHP_VERSION
ENV PHP_DOT_VERSION $PHP_DOT_VERSION

ENV STACK_PATH /opt/runtime/config
ENV RUNTIME_USER_ID 1001
ENV RUNTIME_USER_NAME runtime

# Labels
LABEL io.stack.enable true
LABEL io.stack.type runtime
LABEL io.stack.shell.id ${RUNTIME_USER_ID}

# Install packages
COPY install-packages /usr/local/bin

RUN install-packages \
    wget \
    curl \
    gnupg \
    msmtp \
    ca-certificates \
    nano \
    less \
    supervisor \
    git \
    mysql-client

# Install Openlitespeed & LSPHP
RUN wget -O - http://rpms.litespeedtech.com/debian/enable_lst_debian_repo.sh | bash \
    && install-packages \
    lsphp$PHP_VERSION \
    lsphp$PHP_VERSION-apcu \
    lsphp$PHP_VERSION-common \
    lsphp$PHP_VERSION-curl \
    lsphp$PHP_VERSION-igbinary \
    lsphp$PHP_VERSION-imagick \
    lsphp$PHP_VERSION-imap \
    lsphp$PHP_VERSION-intl \
    lsphp$PHP_VERSION-ldap \
    lsphp$PHP_VERSION-memcached \
    lsphp$PHP_VERSION-msgpack \
    lsphp$PHP_VERSION-mysql \
    lsphp$PHP_VERSION-opcache \
    lsphp$PHP_VERSION-pear \
    lsphp$PHP_VERSION-pgsql \
    lsphp$PHP_VERSION-pspell \
    lsphp$PHP_VERSION-redis \
    lsphp$PHP_VERSION-sqlite3 \
    lsphp$PHP_VERSION-tidy \
    openlitespeed \
    openssh-server

RUN ln -s /usr/local/lsws/lsphp$PHP_VERSION/bin/php /usr/local/bin/php

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && mv composer.phar /usr/local/bin/composer \
    && php -r "unlink('composer-setup.php');" \
    && composer --version

# Install WPCLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp \
    && wp --allow-root --version

# Install NodeJS
RUN curl -fsSL https://deb.nodesource.com/setup_14.x | bash - \
    && install-packages nodejs

# Create runtime user
RUN usermod -l runtime -u ${RUNTIME_USER_ID} -d /home/runtime www-data \
    && groupmod -n runtime -g ${RUNTIME_USER_ID} www-data \
    && chsh -s /bin/bash runtime

# Copy runtime files
COPY runtime /opt/runtime

RUN ln -sf /opt/runtime/config/sshd_config /etc/ssh/sshd_config

# Filesystem
RUN mkdir -p /home/runtime /tmp/lshttpd /run/sshd

RUN chown -R ${RUNTIME_USER_NAME}:${RUNTIME_USER_NAME} \
    /home/runtime \
    /usr/local/lsws \
    /tmp/lshttpd

RUN chown -R lsadm:lsadm /usr/local/lsws/admin

# Container config
EXPOSE 80

WORKDIR /home/runtime/public

ENTRYPOINT ["/opt/runtime/bin/entrypoint"]

CMD ["/usr/bin/supervisord", "-c", "/opt/runtime/config/supervisord.conf"]
