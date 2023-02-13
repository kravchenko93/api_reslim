#!/bin/sh

cron f

/usr/bin/supervisord
#-c /etc/supervisor/conf.d/supervisord.conf

exec "$@"
