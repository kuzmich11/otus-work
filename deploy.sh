cp ./deploy/nginx.conf /etc/nginx/conf.d/demo.conf -f
sed -i -- "s|%SERVER%|$1|g" /etc/nginx/conf.d/demo.conf
service nginx restart
www-data composer install -q
service php8.1-fpm restart
www-data sed -i -- "s|%DATABASE_HOST%|$2|g" .env
www-data sed -i -- "s|%DATABASE_USER%|$3|g" .env
www-data sed -i -- "s|%DATABASE_PASSWORD%|$4|g" .env
www-data sed -i -- "s|%DATABASE_NAME%|$5|g" .env
www-data php bin/console doctrine:migrations:migrate --no-interaction
www-data sed -i -- "s|%RABBITMQ_HOST%|$6|g" .env
www-data sed -i -- "s|%RABBITMQ_USER%|$7|g" .env
www-data sed -i -- "s|%RABBITMQ_PASSWORD%|$8|g" .env