
apache2.conf
phpapi.conf -> /etc/apache2/sites-avaiable

# criar link simbolico de /etc/apache2/sites-avaiable/phpapi.conf em /etc/apache2/sites-enabled
cd /etc/apache2/sites-enabled
sudo ln -s ../sites-available/phpapi.conf phpapi.conf

/etc/hosts
localhost   phpapi.com

sudo a2enmod rewrite
sudo apt-get install php libapache2-mod-php