# Creating a tubearchivist Server
Creating a tubearchivist Server! Here is the [youtube video](https://www.youtube.com/watch?v=vS9uABQ61cc&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=42&pp=gAQBiAQB) to follow with this guide!

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

Install docker compose, you can view their latest releases [here](https://github.com/docker/compose/releases/) for more updated versions of docker compose binaries 
```sh
curl -L https://github.com/docker/compose/releases/download/v2.26.1/docker-compose-linux-x86_64 -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose
```

We will then install nginx to be able to host our HTTPS/TLS certs that are created on StepCA 
```sh
yum -y install nginx
```

Create the certificate on the step-ca server ( Run this command on step-ca server )
```sh
mkdir -p /root/tubearchivist
cd /root/tubearchivist 
step ca certificate tubearchivist.dragon.local tubearchivist.dragon.local.crt tubearchivist.dragon.local.key
```

Copy the cert over to the tubearchivist server. ( Run this command on tubearchivist server )
```sh
mkdir -p /etc/pki/nginx/private
scp root@<CA Server>:/root/tubearchivist/tubearchivist.dragon.local.crt /etc/pki/nginx/tubearchivist.dragon.local.crt
scp root@<CA Server>:/root/tubearchivist/tubearchivist.dragon.local.key /etc/pki/nginx/private/tubearchivist.asgard.local.key
```

Edit the nginx.conf 
```sh
vi /etc/nginx/nginx.conf
```

Uncomment the TLS section and update the ssl_certificate and update location to hit http://localhost:8000
```
# Settings for a TLS enabled server.

    server {
        listen       443 ssl http2 default_server;
        listen       [::]:443 ssl http2 default_server;
        server_name  _;
        root         /usr/share/nginx/html;

        ssl_certificate "/etc/pki/nginx/tubearchivist.dragon.local.crt";
        ssl_certificate_key "/etc/pki/nginx/private/tubearchivist.dragon.local.key";
        ssl_session_cache shared:SSL:1m;
        ssl_session_timeout  10m;
        ssl_ciphers PROFILE=SYSTEM;
        ssl_prefer_server_ciphers on;

        # Load configuration files for the default server block.
        include /etc/nginx/default.d/*.conf;

        location / {
	          proxy_pass http://localhost:8000;
        }

        error_page 404 /404.html;
            location = /40x.html {
        }

```

Restart nginx 
```sh
systemctl restart nginx
```

Create docker-compose.yml in /root/. Copy the contents from the docker-compose file in this repo's directory. 
```
vi docker-compose.yml 
```

Create the following directories 
```sh
mkdir -p media cache redis es
```


Start up the containers
```sh
docker-compose up -d 
```

In some cases you may need to reset the permissions on the es directory 
```sh
chown 1000:0 -R /root/es
```

If you have to reset permissions because it didn't startup correctly restart docker-compose
```sh
docker-compose restart 
```