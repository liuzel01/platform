FROM webdevops/php-apache-dev:8.2

LABEL Author="liuzel01"
LABEL Version="2023.07"
LABEL Description="PHP 8.2 开发环境镜像"

ENV APP_ENV dev
ENV APP_SECRET 1
ENV INSTANCE_ID 1
ENV DATABASE_URL mysql://root:root@mysql::3306/58shuwei
ENV APP_URL http://localhost
ENV MAILER_URL smtp://localhost:1025
ENV DB_HOST mysql
ENV DB_PORT 3306
ENV DB_NAME 58shuwei
ENV DB_USER root
ENV DB_PASSWORD root
ENV NVM_DIR="/var/www/.nvm" 
ENV NODE_PATH $NVM_DIR/versions/node/v$NODE_VERSION/lib/node_modules
ENV PATH      $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH
ENV WORKDIR /var/www/html
ENV APACHE_DOCROOT /var/www/html/public

WORKDIR /var/www/html

RUN echo 'deb https://mirrors.ustc.edu.cn/debian/ buster main contrib non-free' > /etc/apt/sources.list \
    && echo 'deb-src https://mirrors.ustc.edu.cn/debian/ buster main contrib non-free' >> /etc/apt/sources.list \
    && echo 'deb https://mirrors.ustc.edu.cn/debian/ buster-updates main contrib non-free' >> /etc/apt/sources.list \
    && echo 'deb-src https://mirrors.ustc.edu.cn/debian/ buster-updates main contrib non-free' >> /etc/apt/sources.list \
    && echo 'deb https://mirrors.ustc.edu.cn/debian/ buster-backports main contrib non-free' >> /etc/apt/sources.list \
    && echo 'deb-src https://mirrors.ustc.edu.cn/debian/ buster-backports main contrib non-free' >> /etc/apt/sources.list \
    && echo 'deb https://mirrors.ustc.edu.cn/debian-security/ buster/updates main contrib non-free' >> /etc/apt/sources.list \
    && echo 'deb-src https://mirrors.ustc.edu.cn/debian-security/ buster/updates main contrib non-free' >> /etc/apt/sources.list \
    && echo 'deb https://packages.sury.org/php/ buster main' > /etc/apt/sources.list.d/sury-php.list \
    && wget -qO - https://packages.sury.org/php/apt.gpg | apt-key add - \
    && apt-get update -y \
    && export CFLAGS="$PHP_CFLAGS" CPPFLAGS="$PHP_CPPFLAGS" LDFLAGS="$PHP_LDFLAGS" \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* 

RUN mkdir "/var/www/.nvm" \
    && curl -o- https://gitee.com/mirrors/nvm/raw/v0.39.1/install.sh | bash \
    && [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" \
    && [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  \
    && nvm install 18.17.0 \
    && nvm use 18.17.0 && npm install -g yarn \
    && nvm alias default 18.17.0  \
    && echo ""

# prepare "global" composer directory for www-data
RUN mkdir -p /var/www/.composer \
    && export COMPOSER_HOME="/var/www/.composer" \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/* 

# install shuwei
RUN rm -rf ${WORKDIR}/* \
    && git clone --branch=trunk https://github.com/58shuwei/platform /var/www/html \
    && composer install

RUN mkdir -p /var/log/apache2 \
    && chown -R 33:33 /var/www/html
# && php ./bin/shuwei system:install --drop-database --create-database -f --basic-setup \
# && composer run init:js \
# && composer run build:js:admin \

EXPOSE  80