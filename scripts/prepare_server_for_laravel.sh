#!/bin/bash

# Update system packages
sudo apt-get update
sudo apt install make
# Install PHP 8.3 and necessary extensions
sudo apt-get install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update
sudo apt-get install -y php8.3 php8.3-cli php8.3-common php8.3-sqlite3 php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip php8.3-intl php8.3-gd


# Install Nginx
sudo apt-get install -y nginx

# Install MySQL
sudo apt-get install -y mysql-server

sudo apt install -y composer

# Navigate to web root
cd /var/www || exit

# get Laravel project
git clone -b main git@github.com:taylorsuccessor/laravel_11_skeleton_2024_06_12.git laravel

cd laravel || exit

composer install

# Set permissions
sudo chown -R www-data:www-data /var/www/laravel
sudo chmod -R 775 /var/www/laravel/storage
sudo chmod -R 775 /var/www/laravel/storage/logs/

# [Important]
# make sure fpm listen to /run/php/php8.3-fpm.sock by config
# /etc/php/8.3/fpm/pool.d/www.conf
# listen = /run/php/php8.3-fpm.sock
sudo service php8.3-fpm start

# Configure Nginx
sudo cat > /etc/nginx/sites-available/default <<EOF
server {
    listen 80;
    #server_name localhost;
    root /var/www/laravel/public;
    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

#sudo ln -s /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/
sudo systemctl restart nginx

# Configure MySQL database
sudo mysql -e "CREATE DATABASE IF NOT EXISTS laravel_db;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'laravel_user'@'localhost' IDENTIFIED BY 'your_password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON laravel_db.* TO 'laravel_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

cd /var/www/laravel
# Configure Laravel environment
cp .env.example .env
php artisan key:generate
sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=mysql/g" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=laravel_db/g" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=laravel_user/g" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=your_password/g" .env

# Run migrations and seed (optional)
php artisan migrate --seed
make seed
# Set permissions again
sudo chown -R www-data:www-data /var/www/laravel
sudo chmod -R 775 /var/www/laravel/storage

# Restart Nginx
sudo systemctl restart nginx

echo "Setup complete. Navigate to http://localhost to see your Laravel application."
