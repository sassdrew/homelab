# Setting up Traefik on my Kubernetes Cluster 
Setting up Traefik! Video to be released, stay tuned!

> Note: The Operating System I am using is Oracle Linux 8, commands may vary depending on the Operating System of choice!

Add Traefik Repo and Download Chart
```sh
helm repo add traefik https://traefik.github.io/charts 
helm pull traefik/traefik --untar
```

Change to traefik directory
```sh
cd traefik
```

Edit the values.yaml 
```sh
vi values.yaml
# update loadBalancerIP to IP from metallb
```

Install the Traefik Helm Chart
```sh
helm install traefik . -n traefik
```

You are done setting up traefik. Now we will setup ingress routes for services that will utilize traefik. 
An example ingress route for uptime kuma 
```sh
vi uptime-traefik-ingress.yaml
```

```sh
apiVersion: traefik.io/v1alpha1
kind: IngressRoute
metadata:
  name: uptime
  namespace: uptime
spec:
  entryPoints:
    - websecure
  routes:
    - match: Host(`uptime.dragon.local`) 
      kind: Rule
      services:
        - name: uptime-uptime-kuma
          port: 3001
  tls:
    secretName: dragon-local-cert
```

Create the tls secret ( NOTE that the certs created were from step ca but could be any cert and key generated from other softwares )
```sh
kubectl create secret tls dragon-local-cert --cert=/root/certs/wildcard.dragon.local.crt --key=/root/certs/wildcard.dragon.local.key -n uptime
```

Deploy ingress route
```sh
kubectl apply -f uptime-traefik-ingress.yaml -n uptime
```

Make sure your DNS points to your MetalLB IP that is associated with your Traefik service, then you should be able to navigate to https://uptime.dragon.local ( in this example )
