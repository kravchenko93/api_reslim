FROM php:7.4-fpm

WORKDIR /var/www/html

# создаем пользователя прокинутого извне и настраиваем под ним запуск контейнера
ARG HOST_UID

RUN useradd -u ${HOST_UID} docker_user

# создадим директорию куда мы будем писать всякие pid, сессии и другие файлы, появляющиеся в работе php-fpm
RUN mkdir /docker \
    && mkdir /docker/sessions \
    && chown -R ${HOST_UID} /docker

# заряжаем нашу entrypoint
COPY docker-entrypoint.sh /docker

# выставляем права
RUN  chmod 0755 /docker/docker-entrypoint.sh

RUN mkdir /home/docker_user \
    && mkdir /home/docker_user/.composer \
    && chown -R ${HOST_UID} /home/docker_user/.composer

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

RUN apt update

RUN apt install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

RUN apt install -y git
RUN pecl install xdebug-2.8.1

RUN apt -y install cron
RUN apt -y install supervisor

# Copy cron file to the cron.d directory
COPY crontab /etc/cron.d/cron

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/cron

# Apply cron job
RUN crontab /etc/cron.d/cron

# Create the log file to be able to run tail
RUN mkdir -p /var/log/cron

RUN apt clean autoclean
RUN apt autoremove --yes
RUN rm -rf /var/lib/{apt,dpkg,cache,log}/

ENTRYPOINT ["/docker/docker-entrypoint.sh"]

CMD ["php-fpm"]

EXPOSE 9000

#USER ${HOST_UID}
