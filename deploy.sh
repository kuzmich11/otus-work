su -c cp deploy/nginx.conf /etc/nginx/conf.d/demo.conf -f
su -c sed -i -- "s|%SERVER_NAME%|$1|g" /etc/nginx/conf.d/demo.conf
su -c service nginx restart
su -c -u www-data composer install -q
su -c service php8.1-fpm restart
su -c -u www-data sed -i -- "s|%DATABASE_HOST%|$2|g" .env
su -c -u www-data sed -i -- "s|%DATABASE_USER%|$3|g" .env
su -c -u www-data sed -i -- "s|%DATABASE_PASSWORD%|$4|g" .env
su -c -u www-data sed -i -- "s|%DATABASE_NAME%|$5|g" .env
su -c -u www-data php bin/console doctrine:migrations:migrate --no-interaction
su -c -u www-data sed -i -- "s|%RABBITMQ_HOST%|$6|g" .env
su -c -u www-data sed -i -- "s|%RABBITMQ_USER%|$7|g" .env
su -c -u www-data sed -i -- "s|%RABBITMQ_PASSWORD%|$8|g" .env