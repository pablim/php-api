#! /bin/bash

sudo mysqldump -p --no-data phpapi > $PWD"/phpapi.sql"
sudo mysqldump -p phpapi > $PWD"/phpapi_with_data.sql"

