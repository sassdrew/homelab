# Creating a Kubernetes Cluster 
Creating a Kubernetes Cluster! Video to be released, stay tuned!

> Note: The Operating System I am using is Oracle Linux 8, commands may vary depending on the Operating System of choice!

> Note: You'll want to run these commands for each of your nodes ( in my case kubemaster, kubenode1, kubenode2 )
Disable swap 
```sh
swapoff -a
sed -i '/swap/d' /etc/fstab
```

Setup certain kubernetes pre-req modules
```sh
cat <<EOF | sudo tee /etc/modules-load.d/k8s.conf
overlay
br_netfilter
EOF

modprobe overlay
modprobe br_netfilter
```

k8s config  
```sh
cat <<EOF | sudo tee /etc/sysctl.d/k8s.conf
net.bridge.bridge-nf-call-iptables = 1
net.bridge.bridge-nf-call-ip6tables = 1
net.ipv4.ip_forward = 1
EOF

sysctl --system
```

Install and setup containerd.io
```sh
yum config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
dnf install -y containerd.io
mkdir -p /etc/containerd
containerd config default | sudo tee /etc/containerd/config.toml > /dev/null
sed -i 's/SystemdCgroup = false/SystemdCgroup = true/' /etc/containerd/config.toml
systemctl restart containerd
systemctl enable containerd
```

Setup Kubernetes Repo
```sh
cat <<EOF | sudo tee /etc/yum.repos.d/kubernetes.repo
[kubernetes]
name=Kubernetes
baseurl=https://pkgs.k8s.io/core:/stable:/v1.32/rpm/
enabled=1
gpgcheck=1
gpgkey=https://pkgs.k8s.io/core:/stable:/v1.32/rpm/repodata/repomd.xml.key
EOF
```

Install kubeadm kubelet kubectl 
```sh
dnf install -y kubeadm kubelet kubectl
systemctl enable --now kubelet
```

> Note you can now run the rest below on just kubemaster unless specified otherwise
Initialize the pod ( change 10.0.0.0/16 to your desired pod CIDR, this should be different than your home network CIDR )
```sh
kubeadm init --pod-network-cidr=10.0.0.0/16 
mkdir -p $HOME/.kube
cp -i /etc/kubernetes/admin.conf $HOME/.kube/config
chown $(id -u):$(id -g) $HOME/.kube/config
```

Create join token
```sh
kubeadm token create --print-join-command
```

> Copy paste the output from the token create command on your other nodes ( ex. kubenode1, kubenode 2 ) to join the nodes to the master


Setup Flannel for kubernetes networking
```sh
yum -y install wget 
wget https://github.com/flannel-io/flannel/releases/latest/download/kube-flannel.yml
```

Update kube-flannel.yml
```sh
vi kube-flannel.yml
```
```sh
# Update the Network Line which is this
"Network": "10.244.0.0/16",
# Update it to be the CIDR that is used for the initalizing the pod so in this example
"Network": "10.0.0.0/16",
```

Setup kube-flannel
```sh
kubectl apply -f kube-flannel.yml
```

Setup MetalLB for assigning an external IP within your network
```sh
wget https://raw.githubusercontent.com/metallb/metallb/v0.13.9/config/manifests/metallb-native.yaml
```

Create metallb-config.yaml
```sh
vi metallb-config.yaml
```

```sh
apiVersion: metallb.io/v1beta1
kind: IPAddressPool
metadata:
  name: default-pool
  namespace: metallb-system
spec:
  addresses:
  - 172.16.1.94-172.16.1.96 # Change based on your network
---
apiVersion: metallb.io/v1beta1
kind: L2Advertisement
metadata:
  name: l2advertisement
  namespace: metallb-system
```

Deploy MetalLB
```sh
kubectl apply -f metallb-native.yaml
kubectl apply -f metallb-config.yaml
```

Create File Storage Stuff 
> Note: This assumes that you're using NFS storage mounted on /data on all the nodes in the cluster 

Create default storage class for nfs 
```sh
vi storageclass.yaml
```

```sh
apiVersion: storage.k8s.io/v1
kind: StorageClass
metadata:
  name: nfs-storage
  annotations:
    storageclass.kubernetes.io/is-default-class: "true"
provisioner: kubernetes.io/no-provisioner
volumeBindingMode: WaitForFirstConsumer
```

Deploy the storageclass
```sh
kubectl apply -f storageclass.yaml
```

## Setting up Uptime-Kuma
Install Helm 
```sh
curl -fsSL https://raw.githubusercontent.com/helm/helm/main/scripts/get-helm-3 | bash
helm version
```

Add Uptime Kuma Helm Chart
```sh
helm repo add uptime-kuma https://helm.irsigler.cloud
helm repo update
helm pull uptime-kuma/uptime-kuma --untar
```

Navigate to uptime-kuma directory 
```sh
cd uptime-kuma
```

Create uptime namespace
```sh
kubectl create namespace uptime
```

Create Persistence Volume ( PV ) for uptime kuma to attach to 
```sh
vi uptimekumavolume.yaml
```

```sh
apiVersion: v1
kind: PersistentVolume
metadata:
  name: uptime-kuma-pv
spec:
  capacity:
    storage: 10Gi
  accessModes:
    - ReadWriteOnce
  persistentVolumeReclaimPolicy: Retain
  storageClassName: nfs-storage  # Must match your PVC
  hostPath:
    path: "/data/uptime-kuma"
```

Create service to allocate an IP for uptime kuma via Metallb 
```sh
vi uptimekumalb.yaml 
```

```sh
service:
  type: LoadBalancer
  loadBalancerIP: 172.16.1.94  # Choose an available IP from MetalLB
  port: 3001  # Expose Uptime Kuma on port 8080 externally
  targetPort: 3001
```

Deploy uptime with metallb service 
```sh
helm install uptime . -n uptime -f uptimekumalb.yaml 
```

You should be able to see that the IP is allocated and port that you can connect to for the service
```sh
kbuectl get svc -n uptime 
```

YOU ARE DONE :) 