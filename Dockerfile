FROM php:7.1-cli

# RUN apt-get update && apt-get install -y \
#         libfreetype6-dev \
#         libjpeg62-turbo-dev \
#         libmcrypt-dev \
#         libpng12-dev \
RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql \
    && docker-php-ext-install -j$(nproc) mbstring

VOLUME /app

WORKDIR /app

CMD [ "./run-bot.sh" ]
