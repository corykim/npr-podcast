#!/bin/sh
php /srv/www/podcasts/update.php $1 > /var/log/corykim.podcasts.log 2>&1
php /srv/www/podcasts/trim.php $1 > /var/log/corykim.podcasts.log 2>&1