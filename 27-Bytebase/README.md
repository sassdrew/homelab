# Creating a bytebase Server
Creating a bytebase Server! Here is the [youtube video](https://www.youtube.com/watch?v=KTIjzi3ftp4&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=63&pp=gAQBiAQB) to follow with this guide!

> Note: The Operating System I am using is Oracle Linux 8, commands may vary depending on the Operating System of choice!


First we will need to add the docker yum repository
```sh
yum config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
```

Install Docker 
```sh
yum -y install docker-ce
```

Start and Enable on startup Docker 
```sh
systemctl enable docker 
systemctl start docker 
```

We will then install nginx to be able to host our HTTPS/TLS certs that are created on StepCA 
```sh
yum -y install nginx
```

Create the certificate on the step-ca server ( Run this command on step-ca server )
```sh
mkdir -p /root/bytebase
cd /root/bytebase 
step ca certificate bytebase.dragon.local bytebase.dragon.local.crt bytebase.dragon.local.key
```

Copy the cert over to the bytebase server. ( Run this command on bytebase server )
```sh
mkdir -p /etc/pki/nginx/private
scp root@<CA Server>:/root/bytebase/bytebase.dragon.local.crt /etc/pki/nginx/bytebase.dragon.local.crt
scp root@<CA Server>:/root/bytebase/bytebase.dragon.local.key /etc/pki/nginx/private/bytebase.asgard.local.key
```

Edit the nginx.conf 
```sh
vi /etc/nginx/nginx.conf
```

Uncomment the TLS section and update the ssl_certificate and update location to hit http://localhost:8080
```
# Settings for a TLS enabled server.

    server {
        listen       443 ssl http2 default_server;
        listen       [::]:443 ssl http2 default_server;
        server_name  _;
        root         /usr/share/nginx/html;

        ssl_certificate "/etc/pki/nginx/bytebase.dragon.local.crt";
        ssl_certificate_key "/etc/pki/nginx/private/bytebase.dragon.local.key";
        ssl_session_cache shared:SSL:1m;
        ssl_session_timeout  10m;
        ssl_ciphers PROFILE=SYSTEM;
        ssl_prefer_server_ciphers on;

        # Load configuration files for the default server block.
        include /etc/nginx/default.d/*.conf;

        location / {
	          proxy_pass http://localhost:8080;
        }

        error_page 404 /404.html;
            location = /40x.html {
        }

```

Restart nginx 
```sh
systemctl restart nginx
```


Create the following directories
```sh
mkdir -p ~/.bytebase/data
```

Start up the containers
```sh
#!/bin/bash

docker run --init \
  --name bytebase \
  --restart always \
  --publish 80:8080 \
  --health-cmd "curl --fail http://localhost:80/healthz || exit 1" \
  --health-interval 5m \
  --health-timeout 60s \
  --volume ~/.bytebase/data:/var/opt/bytebase \
  bytebase/bytebase:2.7.0 \
  --data /var/opt/bytebase \
  --external-url http://bytebase.dragon.local \
  --port 8080
```