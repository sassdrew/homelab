# Creating a Wordpress Server
Creating a Wordpress Server! Here is the [youtube video](https://www.youtube.com/watch?v=97kZG2wWm20&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=18&pp=gAQBiAQB) to follow with this guide!

> Note: The Operating System I am using is Oracle Linux 8, commands may vary depending on the Operating System of choice!

Install Wordpress dependencies
```sh
yum install httpd mariadb-server -y
yum module enable php:7.4 -y
yum install php php-fpm php-cli php-json php-gd php-mbstring php-pdo php-xml php-mysqlnd php-pecl-zip curl -y
```

Start and Enable httpd mariahdb php-fpm
```sh
systemctl start httpd mariadb php-fpm
systemctl enable httpd mariadb php-fpm
```

Login to mysql database
```sh
mysql
```

Create database ( replace wordpressuserpassword with the password you want to use )
```sh
CREATE DATABASE wordpressdb; 
CREATE USER `wordpressuser`@`localhost` IDENTIFIED by 'wordpressuserpassword';
GRANT ALL ON wordpressdb.* TO `wordpressuser`@`localhost`;
FLUSH PRIVILEGES;
EXIT; 
```

Download and extract wordpress tarball
```sh
cd /var/www/html
curl https://wordpress.org/latest.tar.gz --output wordpress.tar.gz
tar xf wordpress.tar.gz 
```

copy the sample config
```sh
cd wordpress
mv wp-config-sample.php wp-config.php
```

edit the wp-config.php with the database configuration you did above for the user and password
```sh
define( 'DB_NAME', 'wordpressdb' );
define( 'DB_USER', 'wordpressuser' );
define( 'DB_PASSWORD', 'wordpressuserpassword' );
```

Make sure the permissions on the directory are set correctly 
```sh
chown -R apache:apache /var/www/html/wordpress
chmod -R 755 /var/www/html/wordpress
```

edit wordpress.conf
```sh
vi /etc/httpd/conf.d/wordpress
```

customize the virtual host to be similar to the follow ( with your updated dns )
```
<VirtualHost *:80>
ServerAdmin root@localhost
ServerName wordpress.dragon.local
DocumentRoot /var/www/html/wordpress
<Directory "/var/www/html/wordpress">
Options Indexes FollowSymLinks
AllowOverride all
Require all granted
</Directory>
ErrorLog /var/log/httpd/wordpress_error.log
CustomLog /var/log/httpd/wordpress_access.log common
</VirtualHost>
```

For ssl configuration install mod_ssl
```sh
yum -y install mod_ssl
```

Create the certificate on the step-ca server ( Run this command on step-ca server )
```sh
mkdir -p /root/wordpress
cd /root/wordpress 
step ca certificate wordpress.dragon.local wordpress.dragon.local.crt wordpress.dragon.local.key
```

Copy the cert over to the wordpress server. ( Run this command on wordpress server )
```sh

scp root@<CA Server>:/root/wordpress/wordpress.dragon.local.crt /root/wordpress.dragon.local.crt
scp root@<CA Server>:/root/wordpress/wordpress.dragon.local.key /root/wordpress.asgard.local.key
```

Update ssl.conf
```sh
vi /etc/httpd/conf.d/ssl.conf
```

Update the following
```
DocumentRoot "/var/www/html/wordpress
ServerName wordpress.dragon.local:443
SSLEngine On
SSLCertificateFile /root/wordpress.dragon.local.crt
SSLCertificateKeyFile /root/wordpress.dragon.local.key
```

Restart httpd 
```sh
systemctl restart httpd
```