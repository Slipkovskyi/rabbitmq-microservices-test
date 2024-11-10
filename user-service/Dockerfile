FROM php:8.2-cli
WORKDIR /app
COPY . .

# Установка необходимых зависимостей для PHP и Composer
RUN apt-get update && \
    apt-get install -y librabbitmq-dev git unzip && \
    docker-php-ext-install bcmath sockets

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Команда для установки зависимостей Composer при запуске контейнера
CMD ["sh", "-c", "composer install && php index.php"]
