FROM phusion/baseimage:focal-1.0.0-alpha1-amd64
ARG DEBIAN_FRONTEND=noninteractive

# Update & install dependencies and do cleanup
RUN apt-get update && \
    apt-get dist-upgrade -y && \
    apt-get install -y \
        libsecp256k1-0 \
        composer \
        apache2 \
        libapache2-mod-php \
        php-mysql \
        php-curl \
        php-cli \
        php-mbstring \
        php-json \
        php-zmq \
        php-bcmath \
        php-gmp \
        php-bz2 \
        php-gd \
        php-sqlite3 \
        php-zip \
        php-intl \
        php-xml \
        htop \
        curl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Enable rewrite support for apache2
RUN a2enmod rewrite && \
    a2enmod ssl && \
    a2enmod proxy && \
    a2enmod proxy_http && \
    a2enmod proxy_wstunnel && \
    a2enmod proxy_ajp && \
    a2enmod headers && \
    a2dissite 000-default && \
    a2disconf other-vhosts-access-log

# Configure virtual host
COPY ./docker/cryptogate-apache2.conf /etc/apache2/sites-available/cryptogate.conf
RUN a2ensite cryptogate

RUN mkdir /app && \
    chown -R www-data:www-data /app && \
    chown -R www-data:www-data /var/www

# set correct access rights
RUN chown -R www-data:www-data /app/

# Add our startup script
RUN mkdir /etc/service/cryptogate
COPY docker/cryptogate.sh /etc/service/cryptogate/run
RUN chmod +x /etc/service/cryptogate/run

# Add our cryptogate daemon script
RUN mkdir /etc/service/cryptogate-daemon
COPY docker/cryptogate-daemon.sh /etc/service/cryptogate-daemon/run
RUN chmod +x /etc/service/cryptogate-daemon/run

# Add our cryptogate queue script
RUN mkdir /etc/service/cryptogate-queue
COPY docker/cryptogate-queue.sh /etc/service/cryptogate-queue/run
RUN chmod +x /etc/service/cryptogate-queue/run

# cleanup apache2 pid files on boot script
COPY docker/20_startup_apache2.sh /etc/my_init.d/20_startup_apache2.sh
RUN chmod +x /etc/my_init.d/20_startup_apache2.sh

# create container boot time file for checks
COPY docker/19_startup_time.sh /etc/my_init.d/19_startup_time.sh
RUN chmod +x /etc/my_init.d/19_startup_time.sh

# healthcheck
COPY docker/healthcheck.sh /usr/local/bin/healthcheck.sh
RUN chmod +x /usr/local/bin/healthcheck.sh

# Copy our app into docker
COPY ./app /app/app
COPY ./bootstrap /app/bootstrap
COPY ./config /app/config
COPY ./database /app/database
COPY ./public /app/public
COPY ./resources /app/resources
COPY ./routes /app/routes
COPY ./tests /app/tests
COPY ./artisan /app/artisan
COPY ./composer.json /app/composer.json
COPY ./composer.lock /app/composer.lock

RUN mkdir -p /app/storage/app && \
    mkdir -p /app/storage/framework && \
    mkdir -p /app/storage/logs && \
    mkdir -p /app/storage/framework/sessions && \
    mkdir -p /app/storage/framework/views && \
    mkdir -p /app/storage/framework/cache/data

# set correct access rights for copied files
RUN chown -R www-data:www-data /app/


# install composer dependencies
USER www-data
RUN cd /app && \
    COMPOSER_HOME=/var/www composer global require hirak/prestissimo && \
    COMPOSER_HOME=/var/www composer install


USER root
# setup symlink for storage volume folder
RUN ln -s /data/storage /app/storage

EXPOSE 80

VOLUME ["/data"]
CMD ["/sbin/my_init"]
#HEALTHCHECK --start-period=10s --interval=10s --timeout=5s --retries=3 CMD /usr/local/bin/healthcheck.sh
HEALTHCHECK --start-period=1s --interval=20s --timeout=2s --retries=3 CMD /bin/true
