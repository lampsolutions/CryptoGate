FROM phusion/baseimage:0.11
ARG DEBIAN_FRONTEND=noninteractive

# Update & install dependencies and do cleanup
RUN apt-get update && \
    apt-get dist-upgrade -y && \
    apt-get install -y \
        libsecp256k1-0 \
        python3-setuptools \
        build-essential \
        libssl-dev \
        python-dev \
        python3-pyqt5 \
        python3-pip \
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
        php-bz2 \
        php-gd \
        php-sqlite3 \
        php-zip \
        php-intl \
        php-xml \
        curl \
        jq \
        git && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Enable rewrite support for apache2
RUN a2enmod rewrite && \
    a2enmod ssl && \
    a2enmod proxy && \
    a2enmod proxy_http && \
    a2enmod proxy_wstunnel && \
    a2enmod proxy_ajp && \
    a2dissite 000-default

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

# Install scrypt for electrum-ltc wallet
RUN pip3 install scrypt

#################### ELECTRUM BTC ####################

ENV ELECTRUM_VERSION 3.3.4
ENV ELECTRUM_PATH /opt/electrum
ENV ELECTRUM_FILENAME electrum.tar.gz
ENV ELECTRUM_SHA256 2f230e85bcc5833315a44959c645d05df6694847a4ad8f7fe91c974f27472240

# Download and install electrum bitcoin release
RUN curl --silent -L "https://download.electrum.org/$ELECTRUM_VERSION/Electrum-$ELECTRUM_VERSION.tar.gz" -o /tmp/$ELECTRUM_FILENAME && \
    mkdir $ELECTRUM_PATH && \
    cd /tmp && \
    echo "$ELECTRUM_SHA256 *$ELECTRUM_FILENAME" | sha256sum -c - && \
    tar xzf /tmp/$ELECTRUM_FILENAME -C $ELECTRUM_PATH --strip 1 && \
    rm /tmp/$ELECTRUM_FILENAME

RUN cd $ELECTRUM_PATH && \
    python3 setup.py install && \
    python3 -m pip install .[fast]

# Setup electrum websocket support
COPY ./docker/simple-websocket-server-master.zip /tmp/simple-websocket-server.zip
RUN python3 -m pip install /tmp/simple-websocket-server.zip

# Copy default electrum.conf
COPY ./docker/electrum-btc.conf /opt/electrum-btc.conf.default

# Add our startup script
RUN mkdir /etc/service/electrum-btc
COPY docker/electrum-btc.sh /etc/service/electrum-btc/run
RUN chmod +x /etc/service/electrum-btc/run

#################### ELECTRUM DASH ####################

ENV ELECTRUM_DASH_VERSION 3.2.5.1
ENV ELECTRUM_DASH_PATH /opt/electrum-dash
ENV ELECTRUM_DASH_FILENAME electrum-dash.tar.gz
ENV ELECTRUM_DASH_SHA256 91c020beb7990e349b246b59accaf7fc942437152407b810a4ef8b1de0d30d48


# Download and install electrum bitcoin release
RUN curl --silent -L "https://github.com/akhavr/electrum-dash/releases/download/$ELECTRUM_DASH_VERSION/Dash-Electrum-$ELECTRUM_DASH_VERSION.tar.gz" -o /tmp/$ELECTRUM_DASH_FILENAME && \
    mkdir $ELECTRUM_DASH_PATH && \
    cd /tmp && \
    echo "$ELECTRUM_DASH_SHA256 *$ELECTRUM_DASH_FILENAME" | sha256sum -c - && \
    tar xzf /tmp/$ELECTRUM_DASH_FILENAME -C $ELECTRUM_DASH_PATH --strip 1 && \
    rm /tmp/$ELECTRUM_DASH_FILENAME

RUN cd $ELECTRUM_DASH_PATH && \
    python3 setup.py install && \
    python3 -m pip install .[fast]

# Setup electrum websocket support
COPY ./docker/simple-websocket-server-master.zip /tmp/simple-websocket-server.zip
RUN python3 -m pip install /tmp/simple-websocket-server.zip

# Copy default electrum-dash.conf
COPY ./docker/electrum-dash.conf /opt/electrum-dash.conf.default

# Add our startup script
RUN mkdir /etc/service/electrum-dash
COPY docker/electrum-dash.sh /etc/service/electrum-dash/run
RUN chmod +x /etc/service/electrum-dash/run

#################### ELECTRUM LTC ####################

ENV ELECTRUM_LTC_VERSION 3.3.4.1
ENV ELECTRUM_LTC_PATH /opt/electrum-ltc
ENV ELECTRUM_LTC_FILENAME electrum-ltc.tar.gz
ENV ELECTRUM_LTC_SHA256 62227ed18eb683975871318fd682f20687d0e42d74f350d510cff9745fb64a6a

# Download and install electrum bitcoin release
RUN curl --silent -L "https://electrum-ltc.org/download/Electrum-LTC-$ELECTRUM_LTC_VERSION.tar.gz" -o /tmp/$ELECTRUM_LTC_FILENAME && \
    mkdir $ELECTRUM_LTC_PATH && \
    cd /tmp && \
    echo "$ELECTRUM_LTC_SHA256 *$ELECTRUM_LTC_FILENAME" | sha256sum -c - && \
    tar xzf /tmp/$ELECTRUM_LTC_FILENAME -C $ELECTRUM_LTC_PATH --strip 1 && \
    rm /tmp/$ELECTRUM_LTC_FILENAME

RUN cd $ELECTRUM_LTC_PATH && \
    python3 setup.py install && \
    python3 -m pip install .[fast]

# Setup electrum websocket support
COPY ./docker/simple-websocket-server-master.zip /tmp/simple-websocket-server.zip
RUN python3 -m pip install /tmp/simple-websocket-server.zip

# Copy default electrum-ltc.conf
COPY ./docker/electrum-ltc.conf /opt/electrum-ltc.conf.default

# Add our startup script
RUN mkdir /etc/service/electrum-ltc
COPY docker/electrum-ltc.sh /etc/service/electrum-ltc/run
RUN chmod +x /etc/service/electrum-ltc/run

#################### ELECTRUM BCH ####################

ENV ELECTRUM_BCH_VERSION 4.0.1
ENV ELECTRUM_BCH_PATH /opt/electrum-bch
ENV ELECTRUM_BCH_FILENAME electrum-bch.tar.gz
ENV ELECTRUM_BCH_SHA256 88f89303fd947983482378e5cdd5a23b6edb334f2a746274ca65f921dc00d199

# Download and install electrum bitcoin release
RUN curl --silent -L "https://electroncash.org/downloads/$ELECTRUM_BCH_VERSION/win-linux/Electron-Cash-$ELECTRUM_BCH_VERSION.tar.gz" -o /tmp/$ELECTRUM_BCH_FILENAME && \
    mkdir $ELECTRUM_BCH_PATH && \
    cd /tmp && \
    echo "$ELECTRUM_BCH_SHA256 *$ELECTRUM_BCH_FILENAME" | sha256sum -c - && \
    tar xzf /tmp/$ELECTRUM_BCH_FILENAME -C $ELECTRUM_BCH_PATH --strip 1 && \
    rm /tmp/$ELECTRUM_BCH_FILENAME

RUN cd $ELECTRUM_BCH_PATH && \
    python3 setup.py install && \
    python3 -m pip install .[fast]

# Setup electrum websocket support
COPY ./docker/simple-websocket-server-master.zip /tmp/simple-websocket-server.zip
RUN python3 -m pip install /tmp/simple-websocket-server.zip

# Copy default electrum-bch.conf
COPY ./docker/electrum-bch.conf /opt/electrum-bch.conf.default

# Add our startup script
RUN mkdir /etc/service/electrum-bch
COPY docker/electrum-bch.sh /etc/service/electrum-bch/run
RUN chmod +x /etc/service/electrum-bch/run

# Add our payments watcher daemon script
RUN mkdir /etc/service/payments_watcher
COPY docker/payments_watcher.sh /etc/service/payments_watcher/run
RUN chmod +x /etc/service/payments_watcher/run

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


# set correct access rights for copied files
RUN chown -R www-data:www-data /app/

# setup symlink for storage volume folder
RUN ln -s /data/storage /app/storage

# install composer dependencies
USER www-data
RUN cd /app && \
    COMPOSER_HOME=/var/www composer global require hirak/prestissimo && \
    COMPOSER_HOME=/var/www composer install

USER root

EXPOSE 80

VOLUME ["/data"]
CMD ["/sbin/my_init"]

