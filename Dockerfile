FROM php:8.3-apache

# Install required packages: cron, PostgreSQL client, and PHP PostgreSQL extension
RUN apt-get update && apt-get install -y \
    cron \
    postgresql-client \
    vim \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean

# Set vim as the default editor
ENV EDITOR=vim

# Add cron jobs
COPY ./cron/mycron /etc/cron.d/mycron
RUN chmod 0644 /etc/cron.d/mycron

# Apply the cron job
RUN crontab /etc/cron.d/mycron

# Ensure Apache and cron start
CMD cron && apachectl -D FOREGROUND
