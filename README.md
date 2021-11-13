<p align="center">
	<img src="https://blockmatcher.ledgerleap.com/logo.png" width="400">
</p>


## BlockMatcher Backend

For those interested in buying/selling CSPR OTC. This system allows the creation of OTC token deals, matching buyers and sellers together. The admin, or broker, can then clear them in batches. Buyers come register and place their offers for what they are willing to pay. Sellers come register and place their tokens up for sale at a price they believe is fair. The admin matches buyers and sellers and batches them together for each era that there are sales to be brokered. 

This is the backend repo of the portal. To see the frontend repo, visit https://github.com/ledgerleapllc/blockmatcher-frontend

### Install and Deploy

Relies on Laravel PHP, server software (Apache/Nginx), and Mysql if hosting locally

```bash
sudo apt -y install apache2
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod ssl
sudo apt -y install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y php7.4
sudo apt-get install -y php7.4-{bcmath,bz2,intl,gd,mbstring,mysql,zip,common,curl,xml}
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
```

Setup the repo according to our VHOST path. Note, the actual VHOST path in this case should be set to **/var/www/blockmatcher-backend/public**

```bash
cd /var/www/
git clone https://github.com/ledgerleapllc/blockmatcher-backend
cd blockmatcher-backend
```

Install packages and setup environment

```bash
composer install
composer update
cp .env.example .env
```

After adjusting .env with your variables, run Artisan to finish setup

```bash
php artisan key:generate
php artisan migrate
php artisan passport:install
php artisan config:clear
php artisan route:clear
php artisan cache:clear
(crontab -l 2>>/dev/null; echo "* * * * * cd /var/www/blockmatcher-backend && php artisan schedule:run >> /dev/null 2>&1") | crontab -
```

You may also have to authorize Laravel to write to the storage directory

```bash
sudo chown -R www-data:www-data storage/
```

Last, you need to setup roles and admins to start using the portal and see it work. Visit the URL of the backend with the path **/install**. This will install these things for you. You will find your admin credentials generated in the Laravel log file. You may want to disable this endpoint after the initial install to prevent this install endpoint from being used again if you are planning on deploying to a production environment in the future. This is easily done by switching ENV variable **INSTALL_PATH_ENABLED** to 0, or false. You may need to run the following command if Laravel caching is on.

```bash
php artisan config:clear
```

### Usage Guide

For full functionality we recommend adding API keys to support the feature of live CSPR prices.

**Start here -**

After deployment of the portal, log in with the admin code. The first thing to be seen are three tables, sale offers, purchase offers, and batching details. This is where an admin can visually match buyers and sellers, and in turn create a new batch request to process them together. You can register new accounts as a buyer and a seller to test placing OTC orders, which will in turn appear on the admin's tables.

**Other notes -**

These features were scoped and determined to be the essential features needed for BlockMatcher. Email any questions to team@ledgerleap.com.

### Testing

We use PHPUnit for unit testing of the portal's critical functionality. In order to run the test suite, you will need to build composer dependencies and run PHP Artisan's commands, ensuring a proper backend build. Run **composer run-script --dev test** to run the unit tests and see output on th CLI. Run this command at the root of the repo directory.

```bash
composer run-script --dev test
```