run: cp ./deploy/nginx.conf /etc/nginx/conf.d/demo.conf -f
run: sed -i -- "s|%SERVER%|$1|g" /etc/nginx/conf.d/demo.conf
run: service nginx restart
run: -u www-data composer install -q
run: service php8.1-fpm restart
run: -u www-data sed -i -- "s|%DATABASE_HOST%|$2|g" .env
run: -u www-data sed -i -- "s|%DATABASE_USER%|$3|g" .env
run: -u www-data sed -i -- "s|%DATABASE_PASSWORD%|$4|g" .env
run: -u www-data sed -i -- "s|%DATABASE_NAME%|$5|g" .env
run: -u www-data php bin/console doctrine:migrations:migrate --no-interaction
run: -u www-data sed -i -- "s|%RABBITMQ_HOST%|$6|g" .env
run: -u www-data sed -i -- "s|%RABBITMQ_USER%|$7|g" .env
run: -u www-data sed -i -- "s|%RABBITMQ_PASSWORD%|$8|g" .env