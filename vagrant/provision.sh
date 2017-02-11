if [ $# != 1 ]; then
    echo 'Usage: provision.sh <IP>'
    exit 1
fi

# mysql passwords
wppass="123456"
rootpass="123456"


#if grep swapfile /etc/fstab > /dev/null; then
#  true
#else
#  dd if=/dev/zero of=/swapfile bs=1M count=2048
#  mkswap /swapfile
#  chmod 600 /swapfile
#  sudo swapon /swapfile
#  echo "/swapfile  none  swap  defaults  0  0" >> /etc/fstab
#fi

apt-get update

debconf-set-selections <<< "mysql-server mysql-server/root_password password $rootpass"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $rootpass"

apt-get -y install apache2
apt-get -y install apache2-utils
apt-get -y install curl
apt-get -y install geoip-database # unused?
apt-get -y install gettext # unused?
apt-get -y install git
apt-get -y install libcurl4-gnutls-dev # unused?
apt-get -y install libexpat1-dev # unused?
apt-get -y install libssl-dev # unused?
apt-get -y install libz-dev # unused?
apt-get -y install mysql-server-5.6
apt-get -y install nodejs
apt-get -y install npm
apt-get -y install phantomjs # unused?
apt-get -y install php-apc
apt-get -y install php5
apt-get -y install php5-curl
apt-get -y install php5-gd # unused?
apt-get -y install php5-geoip
apt-get -y install php5-imap # unused?
apt-get -y install php5-mcrypt
apt-get -y install php5-mysql
apt-get -y install php5-sqlite # unused?
apt-get -y install redis-server
apt-get -y install sendmail-bin # unused?
apt-get -y install xvfb # unused?

cd /vagrant_data
curl -sS https://getcomposer.org/installer | php

a2enmod cgi # unused?
a2enmod rewrite
a2enmod headers # unused?
service apache2 restart
