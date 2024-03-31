# Creating a memos Server
Creating a memos Server! Here is the [youtube video](https://www.youtube.com/watch?v=McJuvHLC90s&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=73&pp=gAQBiAQB) to follow with this guide!

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
mkdir -p /root/memos
cd /root/memos 
step ca certificate memos.dragon.local memos.dragon.local.crt memos.dragon.local.key
```

Copy the cert over to the memos server. ( Run this command on memos server )
```sh
mkdir -p /etc/pki/nginx/private
scp root@<CA Server>:/root/memos/memos.dragon.local.crt /etc/pki/nginx/memos.dragon.local.crt
scp root@<CA Server>:/root/memos/memos.dragon.local.key /etc/pki/nginx/private/memos.asgard.local.key
```

Edit the nginx.conf 
```sh
vi /etc/nginx/nginx.conf
```

Uncomment the TLS section and update the ssl_certificate and update location to hit http://localhost:5230
```
# Settings for a TLS enabled server.

    server {
        listen       443 ssl http2 default_server;
        listen       [::]:443 ssl http2 default_server;
        server_name  _;
        root         /usr/share/nginx/html;

        ssl_certificate "/etc/pki/nginx/memos.dragon.local.crt";
        ssl_certificate_key "/etc/pki/nginx/private/memos.dragon.local.key";
        ssl_session_cache shared:SSL:1m;
        ssl_session_timeout  10m;
        ssl_ciphers PROFILE=SYSTEM;
        ssl_prefer_server_ciphers on;

        # Load configuration files for the default server block.
        include /etc/nginx/default.d/*.conf;

        location / {
            proxy_pass http://localhost:5230;
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

Create the following directories
```sh
mkdir -p ~/.memos/
```

Start up the containers
```sh
docker run -d --name memos -p 5230:5230 -v ~/.memos/:/var/opt/memos neosmemo/memos:latest
```