composer install
cp deploy/nginx.conf /etc/nginx/conf.d/demo.conf -f
sed -i -- "s|%SERVER_NAME%|$1|g" /etc/nginx/conf.d/demo.conf
service nginx restart
service php8.1-fpm restart
sed -i -- "s|%DATABASE_HOST%|$2|g" /var/www/demo/.env
sed -i -- "s|%DATABASE_USER%|$3|g" /var/www/demo/.env
sed -i -- "s|%DATABASE_PASSWORD%|$4|g" /var/www/demo/.env
sed -i -- "s|%DATABASE_NAME%|$5|g" /var/www/demo/.env
php bin/console doctrine:migrations:migrate --no-interaction
sed -i -- "s|%RABBITMQ_HOST%|$6|g" /var/www/demo/.env
sed -i -- "s|%RABBITMQ_USER%|$7|g" /var/www/demo/.env
sed -i -- "s|%RABBITMQ_PASSWORD%|$8|g" /var/www/demo/.env