# Creating a Gitlab Server
Creating your Code Repository using Gitlab! Here is the [youtube video](https://www.youtube.com/watch?v=k1eb-OO1lDE&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=4&pp=gAQBiAQB) to follow with this guide!

> Note: The Operating System I am using is Centos 8 Stream, commands may vary depending on the Operating System of choice!
> I mainly follow [Gitlab's Install Guide](https://about.gitlab.com/install/#centos-7) for this, so if you're looking for other OS distros you can visit [this page](https://about.gitlab.com/install/) to find it


Install needed dependencies
```sh
sudo yum install -y curl policycoreutils-python openssh-server perl
```

Make sure sshd server is installed and running ( this should be setup as you should be ssh to the machine already ) 
```sh
systemctl enable sshd
systemctl start sshd
```

Disable firewalld ( you can also just add the http and https service if you don't want to disable it )
```sh
systemctl stop firewalld
systemctl disable firewalld
```

Add gitlab package repository 
```sh
curl https://packages.gitlab.com/install/repositories/gitlab/gitlab-ee/script.rpm.sh | sudo bash
```

Install Gitlab, make sure to set the EXTERNAL_URL to be the DNS of your gitlab server
```sh
sudo EXTERNAL_URL="https://gitlab.dragon.local" yum install -y gitlab-ee
```

You should be able to find the default password stored in /etc/gitlab/initial_root_password
```sh
cat /etc/gitlab/initial_root_password
```

Navigate to your browser: https://gitlab.dragon.local
```
Username: root
Password: <Output from /etc/gitlab/initial_root_password>
```