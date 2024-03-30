# Creating Container in Gitlab using Kaniko 
Creating your containers in Gitlab using Kaniko! Here is the [youtube video](https://www.youtube.com/watch?v=JxrsoHrMRzQ&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=7&pp=gAQBiAQB) to follow with this guide!

> Note: The Operating System I am using is Centos 8 Stream, commands may vary depending on the Operating System of choice!

You will need to make sure that you have container registry is enabled in Gitlab for storing containers. To do so edit edit the /etc/gitlab/gitlab.rb 
```sh
vi /etc/gitlab/gitlab.rb 
```

Edit the following fields and replace gitlab.dragon.local with your domain
```
registry_external_url 'http://gitlab.dragon.local:5000' 
registry['enable'] = true
registry['registry_http_addr'] = "0.0.0.0:5000"
registry_nginx['enable'] = false
```

Once the changes have been done, reconfigure gitlab 
```sh
gitlab-ctl reconfigure
```

Create a new Project in Gitlab and copy the two files ( Dockerfile and .gitlab-ci.yml ) from this directory to your project. Update docker file to be whatever container you would like to create!