# Creating a Uptime Kuma Server
Creating a Uptime Kuma Server! Here is the [youtube video](https://www.youtube.com/watch?v=xPDLqlFMnwU&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=12&pp=gAQBiAQB) to follow with this guide!

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

Run the container
```sh
docker run -d --restart=always -p 3001:3001 -v ./uptime-kuma:/app/data --name uptime-kuma louislam/uptime-kuma
```

Navigate on the browser to http://<server>:3001