FROM php:8.2-cli

# Dependências do sistema
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    unzip \
    curl \
    git \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copia o projeto
COPY . .

# Instala dependências PHP (sem dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Script de inicialização
COPY start.sh /app/start.sh
RUN chmod +x /app/start.sh

EXPOSE 8000

CMD ["/app/start.sh"]
