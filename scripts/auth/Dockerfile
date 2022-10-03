FROM php:8.1-cli-alpine

WORKDIR /app

# Instalando depdendências
COPY ["composer.json", "composer.lock", "./"]
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-interaction

# Copiando resto da aplicação
COPY . ./

EXPOSE 80
CMD ["composer", "start"]
