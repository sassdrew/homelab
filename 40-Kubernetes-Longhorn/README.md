# Setting up Longhorn on my Kubernetes Cluster 
Setting up Longhorn! Video to be released, stay tuned!

> Note: The Operating System I am using is Oracle Linux 8, commands may vary depending on the Operating System of choice!

On each of the nodes install isci 
```sh
dnf install -y iscsi-initiator-utils
systemctl start iscsid
systemctl enable iscsid
```

Install Helm
```sh
curl -fsSL https://raw.githubusercontent.com/helm/helm/main/scripts/get-helm-3 | bash
helm version
```

Grab Helm Chart for Longhorn
```sh
helm repo add longhorn https://charts.longhorn.io
helm pull longhorn/longhorn --untar
```

Change into longhorn directory
```sh
cd longhorn
```

Update Helm Values
```sh
vi values.yaml
```

```sh
# Update below to the number of replicas
defaultReplicaCount
```

Install longhorn
```sh
helm install longhorn . -n longhorn
```

Allocate MetalLB IP to access it externally, we'll switch this to traefik later
```sh
kubectl patch svc longhorn-frontend -n longhorn --type='merge' -p '{"spec": {"type": "LoadBalancer", "loadBalancerIP": "172.16.1.195"}}'
```

You can now navigate to http://172.16.1.195 and configure disks. :) 