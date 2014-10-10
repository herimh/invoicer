invoicer
========

Pretend create a symfony bundle to read invoices from an email account and extract the information in xml files. 

Requirements:
Symfony 2.*

Extra php extensions:
sudo apt-get install php5-dev

RarArchive instalation:
sudo pecl -v install rar
or
Manually instalation: http://php.net/manual/en/rar.installation.php

Uploads folder:
Create a folder %ProjectRoot%/web/uploads and set write permisions:
sudo chmod 777 -R web/uploads