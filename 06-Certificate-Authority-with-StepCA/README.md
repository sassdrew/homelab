# Creating a Certificate Authority ( CA ) Server with StepCA 
Creating your Code Repository using Gitlab! Here is the [youtube video](https://www.youtube.com/watch?v=aAirdY2fdmM&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=9&pp=gAQBiAQB) to follow with this guide!

> Note: The Operating System I am using is Oracle Linux 8, commands may vary depending on the Operating System of choice!

Having a Certificate Authority Server is very nice to handle HTTPS connections in your homelab. Following this guide you will be able to install a StepCA server to host your certificates for everything you deploy :) 

First we will be using the docker version of StepCA for easier deployment. Add the docker yum repository
```sh
yum config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
```

Install Docker 
```sh
yum -y install docker-ce
```

Create step user
```sh
useradd step
```

Run the StepCA Container in a screen session
```sh
screen -S step
docker run -d -it -v /home/step:/home/step \
    -p 9000:9000 \
    -e "DOCKER_STEPCA_INIT_NAME=Smallstep" \
    -e "DOCKER_STEPCA_INIT_DNS_NAMES=localhost,$(hostname -f)" \
    -e "DOCKER_STEPCA_INIT_REMOTE_MANAGEMENT=true" \
    smallstep/step-ca
```
Make sure to save the password that is outputted on the screen. To exit screen hit Control-A Control-D 


Install wget to be able to install the step cli 
```sh 
yum -y install wget 
```

Install the [step client](https://smallstep.com/docs/step-cli/installation/) as this will allow us to run step command to create certificates
```sh
wget https://dl.smallstep.com/gh-release/cli/docs-ca-install/v0.23.4/step-cli_0.23.4_amd64.rpm
sudo rpm -i step-cli_0.23.4_amd64.rpm
```

Trust and import the root CA 
```sh
CA_FINGERPRINT=$(docker run -v /home/step:/home/step smallstep/step-ca step certificate fingerprint certs/root_ca.crt)
step ca bootstrap --ca-url https://localhost:9000 --fingerprint $CA_FINGERPRINT --install
```

You can now test to see if it is trusting the CA by running the following command 
```sh
curl https://localhost:9000/health
```

And if it is working correctly it should output 
```
{"status":"ok"}
```

To update the certs that are created to last longer than 1 day ( by default it's one day ), we will update the following file
```sh
vi /home/step/config/ca.json
```

In the authority section add the claims values as shown below to not expire until 10 years 
```sh
    "authority": {
		"enableAdmin": true,
                "claims":
		{
                	"minTLSCertDuration": "5m",
                	"maxTLSCertDuration": "87600h",
                	"defaultTLSCertDuration": "87600h"
		}
	},
```

To create a cert follow the following ( on the StepCA Server )
```sh
step ca <certificate domain> <cert file> <keyfile> 
# For example 
step ca certificate gitlab.dragon.local gitlab.dragon.local.crt gitlab.dragon.local.key
```
The password is the password that was outputted when initially kicking off the docker container 