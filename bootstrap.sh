#!/bin/bash

apt-get update
apt-get upgrade -y

apt-get install -y git
apt-get install -y curl
apt-get install -y apache2
apt-get install -y php5
apt-get install -y php5-curl
apt-get install -y mysql-server
apt-get install -y phpmyadmin
apt-get install -y php5-mysqlnd

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
