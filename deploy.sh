sudo cp ./deploy/nginx.conf /etc/nginx/conf.d/demo.conf -f
sudo sed -i -- "s|%SERVER%|$SERVER|g" /etc/nginx/conf.d/demo.conf
sudo service nginx restart
sudo -u www-data composer install -q
sudo service php8.1-fpm restart
sudo -u www-data sed -i -- "s|%DATABASE_HOST%|$DATABASE_HOST|g" .env
sudo -u www-data sed -i -- "s|%DATABASE_USER%|$DATABASE_USER|g" .env
sudo -u www-data sed -i -- "s|%DATABASE_PASSWORD%|$DATABASE_PASSWORD|g" .env
sudo -u www-data sed -i -- "s|%DATABASE_NAME%|$DATABASE_NAME|g" .env
sudo -u www-data php bin/console doctrine:migrations:migrate --no-interaction
sudo -u www-data sed -i -- "s|%RABBITMQ_HOST%|$RABBITMQ_HOST|g" .env
sudo -u www-data sed -i -- "s|%RABBITMQ_USER%|$RABBITMQ_USER|g" .env
sudo -u www-data sed -i -- "s|%RABBITMQ_PASSWORD%|$RABBITMQ_PASSWORD|g" .env