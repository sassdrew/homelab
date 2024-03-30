# Creating a mattermost Server
Creating a mattermost Server! Here is the [youtube video](https://www.youtube.com/watch?v=9BgVRShk00Y&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=35&pp=gAQBiAQB) to follow with this guide!

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

Create the certificate on the step-ca server ( Run this command on step-ca server )
```sh
mkdir -p /root/mattermost
cd /root/mattermost 
step ca certificate mattermost.dragon.local mattermost.dragon.local.crt mattermost.dragon.local.key
```

Copy the cert over to the mattermost server. ( Run this command on mattermost server )
```sh
scp root@<CA Server>:/root/mattermost/mattermost.dragon.local.crt /root/mattermost.dragon.local.crt
scp root@<CA Server>:/root/mattermost/mattermost.dragon.local.key /root/mattermost.asgard.local.key
```

Install git to download mattermost repo
```sh
yum -y install git
```

Clone the github repository and copy the env
```sh
git clone https://github.com/mattermost/docker
cd docker
cp env.example .env
```

In the docker directory create the following directories and make sure they're owned by the ID 2000
```sh
mkdir -p ./volumes/app/mattermost/{config,data,logs,plugins,client/plugins,bleve-indexes}
sudo chown -R 2000:2000 ./volumes/app/mattermost
```

Copy the certs to the location
```sh
 mv /root/mattermost.dragon.local.crt ./volumes/web/cert/cert.pem
 mv /root/mattermost.dragon.local.key ./volumes/web/cert/key-no-password.pem
```

Startup the service 
```sh
docker compose -f docker-compose.yml -f docker-compose.nginx.yml up -d
```