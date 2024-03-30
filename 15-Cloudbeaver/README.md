# Creating a cloudbeaver Server
Creating a cloudbeaver Server! Here is the [youtube video](https://www.youtube.com/watch?v=B8c6lm7aZ2M&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=34&pp=gAQBiAQB) to follow with this guide!

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
mkdir -p /root/cloudbeaver
cd /root/cloudbeaver 
step ca certificate cloudbeaver.dragon.local cloudbeaver.dragon.local.crt cloudbeaver.dragon.local.key
```

Copy the cert over to the cloudbeaver server. ( Run this command on cloudbeaver server )
```sh
mkdir -p /etc/pki/nginx/private
scp root@<CA Server>:/root/cloudbeaver/cloudbeaver.dragon.local.crt /etc/pki/nginx/cloudbeaver.dragon.local.crt
scp root@<CA Server>:/root/cloudbeaver/cloudbeaver.dragon.local.key /etc/pki/nginx/private/cloudbeaver.asgard.local.key
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

        ssl_certificate "/etc/pki/nginx/cloudbeaver.dragon.local.crt";
        ssl_certificate_key "/etc/pki/nginx/private/cloudbeaver.dragon.local.key";
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

Create the following directory
```sh
mkdir -p /var/cloudbeaver/workspace
```

Run the cloudbeaver container
```sh
docker run --name cloudbeaver -d -ti -p 8080:8978 -v /var/cloudbeaver/workspace:/opt/cloudbeaver/workspace dbeaver/cloudbeaver:latest
```