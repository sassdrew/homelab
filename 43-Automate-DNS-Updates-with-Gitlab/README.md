# GitLab → BIND DNS Automation

This repository documents how to automate DNS updates on a BIND server using **GitLab CI/CD** and **SSH-based deployments**.  
The goal is to manage DNS records via Gitlab and push changes automatically to a BIND DNS server when updates are committed.

Creating an SSH Key 
```sh
cd ~/.ssh
ssh-keygen -m PEM -t rsa
~/.ssh/gitlab
<enter>
<enter>
```

Add the Public Key to your DNS server
```sh
cd ~/.ssh
ssh-keygen -m PEM -t rsa
~/.ssh/gitlab
<enter>
<enter>
```

Add the Private Key to Gitlab as a variable
```
In the Project navigate to Settings > CI/CD > Variables > Expand > Add variable 

Key: gitlab_key
Value: This should be the output of your private key ( cat ~/.ssh/gitlab )
Type: File 
Flags: Protect variable & Expand variable reference 
```

Copy the .gitlab-ci.yml file and update the zone names and DNS server IP