sudo cp ./deploy/nginx.conf /etc/nginx/conf.d/demo.conf -f
sudo sed -i -- "s|%SERVER%|$1|g" /etc/nginx/conf.d/demo.conf
sudo service nginx restart
sudo -u www-data composer install -q
sudo service php8.1-fpm restart
sudo -u www-data sed -i -- "s|%DATABASE_HOST%|$2|g" .env
sudo -u www-data sed -i -- "s|%DATABASE_USER%|$3|g" .env
sudo -u www-data sed -i -- "s|%DATABASE_PASSWORD%|$4|g" .env
sudo -u www-data sed -i -- "s|%DATABASE_NAME%|$5|g" .env
sudo -u www-data php bin/console doctrine:migrations:migrate --no-interaction
sudo -u www-data sed -i -- "s|%RABBITMQ_HOST%|$6|g" .env
sudo -u www-data sed -i -- "s|%RABBITMQ_USER%|$7|g" .env
sudo -u www-data sed -i -- "s|%RABBITMQ_PASSWORD%|$8|g" .env