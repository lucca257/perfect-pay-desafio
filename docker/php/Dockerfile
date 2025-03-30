FROM php:8.3-fpm

WORKDIR /var/www

# instalando dependencias como root
USER root

# instalando dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libxrender1 \
    fontconfig \
    libjpeg62-turbo-dev \
    xfonts-75dpi \
    xfonts-base \
    default-mysql-client \
    && docker-php-ext-install zip

# limpando cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# instalando extensões do PHP para MySQL em vez de SQLite
RUN docker-php-ext-install pdo_mysql mbstring dom xml xmlwriter exif pcntl bcmath gd

# obtendo Composer
COPY --from=composer:2.2.9 /usr/bin/composer /usr/bin/composer

# Creating storage directory and setting permissions
ARG UID=1000
ARG GID=1000

# Garantindo permissões corretas para pastas Laravel
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache && \
    chown -R ${UID}:${GID} /var/www/storage /var/www/bootstrap/cache

# Crie o grupo e usuário com o UID e GID do host
RUN groupadd -g ${GID} myusergroup && \
    useradd -u ${UID} -g ${GID} -m moavi

# Mude o usuário para o que foi criado
USER moavi

# exponsto porta 9000 e iniciando servidor php-fpm
EXPOSE 9000
CMD ["php-fpm"]