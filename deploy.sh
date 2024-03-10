cp ./deploy/nginx.conf /etc/nginx/conf.d/demo.conf -f
sed -i -- "s/SERVER/$SERVER/g" /etc/nginx/conf.d/demo.conf
service nginx restart
composer install -q
service php8.1-fpm restart
sed -i -- "s/DATABASE_HOST/$DATABASE_HOST/g" .env
sed -i -- "s/DATABASE_USER/$DATABASE_USER/g" .env
sed -i -- "s/DATABASE_PASSWORD/$DATABASE_PASSWORD/g" .env
sed -i -- "s/DATABASE_NAME/$DATABASE_NAME/g" .env
php bin/console doctrine:migrations:migrate --no-interaction
sed -i -- "s/RABBITMQ_HOST/$RABBITMQ_HOST/g" .env
sed -i -- "s/RABBITMQ_USER/$RABBITMQ_USER/g" .env
sed -i -- "s/RABBITMQ_PASSWORD/$RABBITMQ_PASSWORD/g" .env