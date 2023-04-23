ARG PHP=8.2
FROM docker.io/php:${PHP}-cli

ARG MSS_PASS=swish
ENV COMPOSER_ALLOW_SUPERUSER=1

# If this fails, you probably need to download the test certificates
# from swish.
WORKDIR /mss
COPY mss_*.zip .

RUN apt update \
    && apt install -y unzip git \
    && unzip mss_*.zip \
    && /bin/bash -c ' \
        root=$(find -name Swish_TLS_RootCA.pem | head -n1); \
        cert=$(find -name Swish_Merchant_TestCertificate_*.pem | head -n1); \
        key=$(find -name Swish_Merchant_TestCertificate_*.key | head -n1); \
        cp "${root}" root.pem; cat "${key}" "${cert}" > client.pem; \
    ' \
    && rm -rf mss* /var/lib/apt/lists/*

COPY --from=docker.io/composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /src

COPY composer.json composer.lock .
RUN composer install

COPY . .

CMD cp -a /mss/* tests/_data \
    && composer install \
    && ./vendor/bin/phpunit
