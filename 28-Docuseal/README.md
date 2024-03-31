# Creating a docuseal Server
Creating a docuseal Server! Here is the [youtube video](https://www.youtube.com/watch?v=Fd-11sKAU7w&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=64&t=178s&pp=gAQBiAQB) to follow with this guide!

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

Start up the containers
```sh
docker run --name docuseal -p 3000:3000 -v.:/data docuseal/docuseal
```