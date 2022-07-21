#! /bin/bash

sudo mysql -p -e "uninstall plugin validate_password"
sudo mysql -p -e "CREATE USER 'phpapi'@'localhost' IDENTIFIED BY '123'"
sudo mysql -p -e "CREATE DATABASE phpapi"
sudo mysql -p -e "GRANT ALL PRIVILEGES ON phpapi.* TO 'phpapi'@'localhost'"

sudo mysql -u phpapi -p phpapi < Projetos/phpapi/phpapi.sql 

mysql -u phpapi -p phpapi < phpapi.sql
mysql -u phpapi -p phpapi < phpapi_with_data.sql
