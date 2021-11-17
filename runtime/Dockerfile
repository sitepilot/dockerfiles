FROM ubuntu:20.04

# Build args
ARG PHP_VERSION "80"
ARG PHP_DOT_VERSION "8.0"

# Environment
ENV PHP_VERSION $PHP_VERSION
ENV PHP_DOT_VERSION $PHP_DOT_VERSION

ENV RUNTIME_USER_ID 1001
ENV RUNTIME_USER_NAME runtime
ENV RUNTIME_USER_HOME /home/runtime

# Install packages
COPY install-packages /usr/local/bin

RUN install-packages \
    git \
    wget \
    curl \
    nano \
    msmtp \
    restic \
    supervisor \
    mariadb-client \
    openssh-server \
    ca-certificates

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
    openlitespeed

RUN ln -s /usr/local/lsws/lsphp$PHP_VERSION/bin/php /usr/local/bin/php
RUN ln -s /opt/container/config/php.ini /usr/local/lsws/lsphp$PHP_VERSION/etc/php/$PHP_DOT_VERSION/mods-available/99-container.ini

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

# Install Container CLI
RUN curl -OL https://github.com/sitepilot/container-cli/releases/download/v1.0.2/container \
    && chmod +x container \
    && mv container /usr/local/bin/container \
    && container --version

# Create runtime user
RUN usermod -l runtime -u ${RUNTIME_USER_ID} -d ${RUNTIME_USER_HOME} www-data \
    && groupmod -n runtime -g ${RUNTIME_USER_ID} www-data \
    && chsh -s /bin/bash runtime

# Copy runtime files
COPY container /opt/container

# Filesystem
RUN mkdir -p /tmp/lshttpd /run/sshd

RUN chown -R ${RUNTIME_USER_NAME}:${RUNTIME_USER_NAME} \
    /tmp/lshttpd \
    /usr/local/lsws

RUN chown -R lsadm:lsadm /usr/local/lsws/admin

# Container config
EXPOSE 80

WORKDIR ${RUNTIME_USER_HOME}/public

ENTRYPOINT ["/opt/container/bin/entrypoint"]

CMD ["/usr/bin/supervisord", "-c", "/opt/container/config/supervisord.conf"]
