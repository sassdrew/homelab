# Creating a Gitlab Runner
Creating your Gitlab Runner to run CI/CD Pipelines through Gitlab! Here is the [youtube video](https://www.youtube.com/watch?v=L_K5DuYsHPc&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=6&pp=gAQBiAQB) to follow with this guide!

> Note: The Operating System I am using is Centos 8 Stream, commands may vary depending on the Operating System of choice!

We will be creating a Gitlab runner using docker, so first we will need to install docker. First add the docker repository 
```sh
yum config-manager --add-repo=https://download.docker.com/linux/centos/docker-ce.repo
```

Install Docker 
```sh
yum -y install docker-ce
```

Start and Enable Docker
```sh
systemctl enable docker
systemctl start docker 
```

Start the gitlab-runner container 
```sh
docker run -d --name gitlab-runner --restart always \
  -v /srv/gitlab-runner/config:/etc/gitlab-runner \
  -v /var/run/docker.sock:/var/run/docker.sock \
  gitlab/gitlab-runner:latest
```

Register the runner
```sh
docker run --rm -it -v /srv/gitlab-runner/config:/etc/gitlab-runner gitlab/gitlab-runner register
```

Then follow the prompt, example configuration that I used:
```
Gitlab Instance URL: http://gitlab.asgard.local 
Registration Token: < Can be found in the web console -> Admin -> CI/CD -> Runners -> 3 dots -> copy token>
Description: <Up to you if you need to describe the runner>
Tags: <if you want to tag the runner to only run specific jobs>
```

Testing the runner you can create a new project copy the following .gitlab-ci.yml file in this directory to use in your project to run