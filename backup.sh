#!/bin/sh
DIR=`date +%Y%m%d%H`
DEST=/var/www/clients/client0/web2/web/backup/$DIR
mkdir $DEST
mongodump -h 127.0.0.1:27017 -d db_doicard -u root -p 1234fF@#@@ -o $DEST
