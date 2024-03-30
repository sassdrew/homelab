# Creating Container in Gitlab using Kaniko 
We will be creating a Gitlab CI/CD Pipeline that will deploy out configuration files to my DNS server! Here is the [youtube video](https://www.youtube.com/watch?v=JfxxxjCoQuk&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=8&pp=gAQBiAQB) to follow with this guide!

> Note: The Operating System I am using is Centos 8 Stream, commands may vary depending on the Operating System of choice!

If you have been looking for a good way to deploy out files to your server like configuration files and keep them in source control here is where we will show you how you can do so! In this specific example we will be updating our DNS zone file and reload that configuration so that it updates our DNS service :) 

Since we will be using a custom docker container that we create, we'll need to make sure that our gitlab runner can pull our container on our gitlab site on HTTP. To do so edit the /etc/docker/daemon.json
```sh
vi /etc/docker/daemon.json 
```

Add the following in the file replacing the domain with your domain
```
{
  "insecure-registries" : ["gitlab.dragon.local:5000"]
}
```

Create a new Gitlab Repository called DNS since we will want to deploy out DNS changes. Copy the zone file from the DNS server ( /var/named/data/dragon.local.zone ) example can be found in this directory. 

Create an ssh key to allow gitlab to be able to ssh from the runner to the server
```sh
cd ~/.ssh
ssh-keygen -m PEM -t rsa
~/.ssh/gitlab
<enter>
<enter>
```

Copy the public key ( ~/.ssh/gitlab.pub ) to the DNS server's /root/.ssh/authorized_keys 
```sh
ssh root@dns
vi ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys #Here is where you would paste the public key
```

Create the Gitlab CI Variable 
```
In the Project navigate to Settings > CI/CD > Variables > Expand > Add variable 
```

Set the following values for the variable 
```
Key: gitlab_key
Value: This should be the output of your private key ( cat ~/.ssh/gitlab )
Type: File 
Flags: Protect variable & Expand variable reference 
```

Once that is done copy the .gitlab-ci.yml and update the IPs to point to your DNS server. The pipeline will copy out the zone file and then reload the dns server making the DNS values active. 