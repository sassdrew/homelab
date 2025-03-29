# Setting up Harbor on my Kubernetes Cluster 
Setting up Harbor! Video to be released, stay tuned!

> Note: The Operating System I am using is Oracle Linux 8, commands may vary depending on the Operating System of choice!

Add Harbor Repo and Download Chart
```sh
helm repo add harbor https://helm.goharbor.io
helm pull harbor/harbor --untar
```

Change to Harbor directory
```sh
cd harbor
```

Edit the values.yaml 
```sh
vi values.yaml
# update externalURL to what your Harbor URL should be for example in my video https://harbor.dragon.local
```

Install the Harbor Helm Chart
```sh
kubectl create namespace harbor 
helm install harbor . -n harbor
```

You are done setting up Harbor. Now we will setup ingress routes for services that will utilize Harbor. 
An example ingress route for Harbor
```sh
vi uptime-harbor-ingress.yaml
```

```sh
apiVersion: traefik.io/v1alpha1
kind: IngressRoute
metadata:
  name: harbor-portal
  namespace: harbor
spec:
  entryPoints:
    - websecure
  routes:
    - match: Host(`harbor.dragon.local`) && PathPrefix(`/`)
      kind: Rule
      services:
        - name: harbor-portal
          port: 80
    - match: Host(`harbor.dragon.local`) && PathPrefix(`/c/`)
      kind: Rule
      services:
        - name: harbor-core
          port: 80
    - match: Host(`harbor.dragon.local`) && PathPrefix(`/api/`)
      kind: Rule
      services:
        - name: harbor-core
          port: 80
    - match: Host(`harbor.dragon.local`) && PathPrefix(`/service/`)
      kind: Rule
      services:
        - name: harbor-core
          port: 80
    - match: Host(`harbor.dragon.local`) && PathPrefix(`/v2/`)
      kind: Rule
      services:
        - name: harbor-core
          port: 80
    - match: Host(`harbor.dragon.local`) && PathPrefix(`/chartrepo/`)
      kind: Rule
      services:
        - name: harbor-core
          port: 80
  tls:
    secretName: dragon-local-cert
```

Create the tls secret ( NOTE that the certs created were from step ca but could be any cert and key generated from other softwares )
```sh
kubectl create secret tls dragon-local-cert --cert=/root/certs/wildcard.dragon.local.crt --key=/root/certs/wildcard.dragon.local.key -n harbor
```

Deploy ingress route
```sh
kubectl apply -f uptime-harbor-ingress.yaml -n harbor
```

Make sure your DNS points to your MetalLB IP that is associated with your Traefik service, then you should be able to navigate to https://harbor.dragon.local ( in this example )


Example Pull from docker hub 
```sh
docker pull harbor.dragon.local/dockerhub-proxy/sassdrew/linux-escape-room:14
```