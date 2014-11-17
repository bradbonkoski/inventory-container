# Version 0.0.1
FROM bradbonkoski/php-apache
MAINTAINER Brad Bonkoski "brad.bonkoski@gmail.com"

COPY .htaccess /var/www/
COPY bootstrap.php /var/www/
COPY index.php /var/www/
COPY LICENSE /var/www/

COPY composer.json /var/www/
copy composer.lock /var/www/
RUN cd /var/www; composer -n install

COPY src /var/www/src/
copy config/app_docker.yml /var/www/config/app.yml

RUN unlink /etc/apache2/sites-enabled/000-default.conf
COPY config/roles.conf /etc/apache2/sites-available/roles.conf
RUN ln -s /etc/apache2/sites-available/roles.conf /etc/apache2/sites-enabled/roles.conf
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/.