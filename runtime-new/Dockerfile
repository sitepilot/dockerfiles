FROM ubuntu:20.04

ARG BUILD_PHP_VERSION="7.4"

# Environment
ENV RUNTIME_USER_ID=1001
ENV RUNTIME_USER_NAME=runtime
ENV RUNTIME_USER_HOME=/home/runtime
ENV RUNTIME_LOGS_PATH=${RUNTIME_USER_HOME}/logs
ENV RUNTIME_CACHE_PATH=${RUNTIME_USER_HOME}/cache
ENV RUNTIME_PUBLIC_PATH=${RUNTIME_USER_HOME}/public
ENV BUILD_PHP_VERSION=${BUILD_PHP_VERSION}

# Platform
ENV SITEPILOT_CACHE_PATH=${RUNTIME_CACHE_PATH}

# Install packages
COPY install-packages /usr/local/bin

RUN install-packages \
    git \
    less \
    wget \
    curl \
    nano \
    msmtp \
    rsync \
    restic \
    supervisor \
    mariadb-client \
    openssh-server \
    ca-certificates \
    software-properties-common

# Install Nginx
RUN add-apt-repository ppa:ondrej/nginx -y \
    && install-packages nginx

# Install PHP
RUN add-apt-repository ppa:ondrej/php -y \
    && install-packages \
    php${BUILD_PHP_VERSION}-fpm \
    php${BUILD_PHP_VERSION}-common \
    php${BUILD_PHP_VERSION}-mysql \
    php${BUILD_PHP_VERSION}-xml \
    php${BUILD_PHP_VERSION}-xmlrpc \
    php${BUILD_PHP_VERSION}-curl \
    php${BUILD_PHP_VERSION}-gd \
    php${BUILD_PHP_VERSION}-imagick \
    php${BUILD_PHP_VERSION}-cli \
    php${BUILD_PHP_VERSION}-dev \
    php${BUILD_PHP_VERSION}-imap \
    php${BUILD_PHP_VERSION}-mbstring \
    php${BUILD_PHP_VERSION}-opcache \
    php${BUILD_PHP_VERSION}-redis \
    php${BUILD_PHP_VERSION}-soap \
    php${BUILD_PHP_VERSION}-zip \
    && mkdir -p /run/php

# Install Container CLI
RUN curl -OL https://github.com/sitepilot/container-cli/releases/download/v1.0.2/container \
    && chmod +x container \
    && mv container /usr/local/bin/container \
    && container --version

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
RUN usermod -l runtime -u ${RUNTIME_USER_ID} -d ${RUNTIME_USER_HOME} www-data \
    && groupmod -n runtime -g ${RUNTIME_USER_ID} www-data \
    && chsh -s /bin/bash runtime

# Config
COPY filesystem /

RUN ln -sf /opt/container/config/msmtp.conf /etc/msmtprc \
    && ln -sf /opt/container/config/nginx.conf /etc/nginx/nginx.conf \
    && ln -sf /opt/container/config/pool.conf /etc/php/${BUILD_PHP_VERSION}/fpm/pool.d/www.conf  \
    && ln -sf /opt/container/config/php.ini /etc/php/${BUILD_PHP_VERSION}/fpm/conf.d/90-runtime.ini

RUN mkdir -p /run/nginx /run/php /run/sshd

WORKDIR ${RUNTIME_PUBLIC_PATH}

ENTRYPOINT ["/opt/container/bin/entrypoint"]

CMD ["/usr/bin/supervisord", "-c", "/opt/container/config/supervisord.conf"]
