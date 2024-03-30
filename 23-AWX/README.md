# Creating a AWX Server
Creating a AWX Server! Here is the [youtube video](https://www.youtube.com/watch?v=OLIktAb9-FY&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=47&pp=gAQBiAQB) to follow with this guide!

> Note: The Operating System I am using is Oracle Linux 8, commands may vary depending on the Operating System of choice!


First we will need to download Kubernetes because the new version of AWX is where it is ran from 
```sh
curl -sfL https://get.k3s.io | sh -
```

We will then install the dependencies needed to download and build AWX 
```sh
yum -y install git make jq
```

Clone the AWX Repo
```sh
git clone https://github.com/ansible/awx-operator.git
```

We will then setup our kubenetes namespace
```sh
export NAMESPACE=awx
kubectl create ns ${NAMESPACE}
kubectl config set-context --current --namespace=awx
```

From there we will build AWX
```sh
cd awx-operator/
RELEASE_TAG=`curl -s https://api.github.com/repos/ansible/awx-operator/releases/latest | grep tag_name | cut -d '"' -f 4`
git checkout $RELEASE_TAG
make deploy
```

You can then view the operator running using the following command
```sh
kubectl get pods -n awx
```

Create your awx.yaml
```sh
vi awx.yaml
```

Input the following
```
---
apiVersion: awx.ansible.com/v1beta1
kind: AWX
metadata:
  name: awx-demo
spec:
  service_type: nodeport
```

Kick of the run for AWX
```sh
kubectl apply -f awx.yaml
```

You can watch the containers spin up by running this, this will take a few seconds to populate, if this does not populate please refer to troubleshooting section
```sh
watch kubectl get pods -n awx```
```

To get the port it is running on, run the following: 
```sh
kubectl get service -n awx
```

To get the admin password 
```sh
kubectl get secret awx-demo-admin-password -o jsonpath="{.data.password}" | base64 --decode
```

# Troubleshooting
There is an interesting error with the metrics API in newer versions of Operating Systems not trusting the cert. To work around this do the following
```sh
yum -y install wget
wget https://github.com/kubernetes-sigs/metrics-server/releases/download/v0.5.0/components.yaml
kubectl delete -f components.yaml  
# Update the components.yaml to include - --kubelet-insecure-tls after line - --metric-resolution=15s
kubectl apply -f components.yaml
```