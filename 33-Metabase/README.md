# Creating a metabase Server
Creating a metabase Server! Here is the [youtube video](https://www.youtube.com/watch?v=McJuvHLC90s&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=73&pp=gAQBiAQB) to follow with this guide!

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
mkdir -p /root/metabase
cd /root/metabase 
step ca certificate metabase.dragon.local metabase.dragon.local.crt metabase.dragon.local.key
```

Copy the cert over to the metabase server. ( Run this command on metabase server )
```sh
mkdir -p /etc/pki/nginx/private
scp root@<CA Server>:/root/metabase/metabase.dragon.local.crt /etc/pki/nginx/metabase.dragon.local.crt
scp root@<CA Server>:/root/metabase/metabase.dragon.local.key /etc/pki/nginx/private/metabase.asgard.local.key
```

Edit the nginx.conf 
```sh
vi /etc/nginx/nginx.conf
```

Uncomment the TLS section and update the ssl_certificate and update location to hit http://localhost:3000
```
# Settings for a TLS enabled server.

    server {
        listen       443 ssl http2 default_server;
        listen       [::]:443 ssl http2 default_server;
        server_name  _;
        root         /usr/share/nginx/html;

        ssl_certificate "/etc/pki/nginx/metabase.dragon.local.crt";
        ssl_certificate_key "/etc/pki/nginx/private/metabase.dragon.local.key";
        ssl_session_cache shared:SSL:1m;
        ssl_session_timeout  10m;
        ssl_ciphers PROFILE=SYSTEM;
        ssl_prefer_server_ciphers on;

        # Load configuration files for the default server block.
        include /etc/nginx/default.d/*.conf;

        location / {
            proxy_pass http://localhost:3000;
            client_max_body_size 0;
            proxy_read_timeout 999999s;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded_proto $scheme;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection $http_connection;
            proxy_set_header Origin $scheme://$http_host;
        }

        error_page 404 /404.html;
            location = /40x.html {
        }

```

Restart nginx 
```sh
systemctl restart nginx
```

Start up the containers
```sh
docker run -d -p 3000:3000 metabase/metabase

```