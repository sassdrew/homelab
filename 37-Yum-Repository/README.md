# Creating a Yum Repository Server
Creating a Yum Repository Server! Here is the [youtube video](https://www.youtube.com/watch?v=DF-2h-RCKBA&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=90&pp=gAQBiAQB) to follow with this guide!

> Note: The Operating System I am using is Oracle Linux 8, commands may vary depending on the Operating System of choice!

First we will want to install epel-release to be able to install screen 
```sh
yum -y install epel-release
```

Now we can install screen 
```sh
yum -y install screen
```

We'll create the directory in which the packages will live 
```sh
mkdir -p /repos/OL8
```

Create the sync.sh script
```sh
vi sync.sh
```

Input the following into the script
```sh
#!/bin/bash 

reposync -n --download-metadata --repoid=ol8_baseos_latest -p /repos/OL8/
reposync -n --download-metadata --repoid=ol8_appstream -p /repos/OL8/
reposync -n --download-metadata --repoid=ol8_developer_EPEL -p /repos/OL8/
reposync -n --download-metadata --repoid=ol8_developer_EPEL_modular -p /repos/OL8/
```

Start a screen session to start the initial sync 
```sh
screen -S sync
```

Kick off the initial sync 
>Note: This will take a few hours to fully complete
```sh
/root/sync.sh
```

Install httpd to host the web package for other servers to grab the packages
```sh
yum -y install httpd
```

Edit the httpd.conf
```sh
vi /etc/httpd/conf/httpd.conf
```

Update the following lines 
```sh
#DocumentRoot "/var/www/html"
DocumentRoot "/repos"

#<Directory "/var/www/html">
<Directory "/repos">
```

Restart httpd to apply the changes
```sh
systemctl restart httpd
```

Once the script is done, you can now use the server as a yum server. You can continuously sync new packages by putting the script in cron