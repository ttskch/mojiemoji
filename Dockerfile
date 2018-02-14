FROM ttskch/nginx-php-fpm-heroku

RUN \
    apk update \
    \
    # install gd and dependencies
    && apk add freetype-dev libjpeg-turbo-dev libpng-dev \
    && apk add php7-gd \
    # ensure FreeType Support and JPEG Support
    && php -i | grep "JPEG Support => enabled" \
    && php -i | grep "FreeType Support => enabled" \
    \
    # instal utils
    && apk add curl \
    && apk add nodejs-npm \
    \
    # remove caches to decrease image size
    && rm -rf /var/cache/apk/* \
    \
    # set to prod to APP_ENV and re-do composer install
    && sed -i -E "s/APP_ENV=dev/APP_ENV=prod/" .env \
    && composer install --no-interaction \
    && chmod -R a+w $DOCROOT

COPY docker/nginx.conf $NGINX_CONFD_DIR/audio2video.me.conf

USER nonroot
