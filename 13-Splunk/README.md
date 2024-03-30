# Creating a Splunk Server
Creating a Splunk Server! Here is the [youtube video](https://www.youtube.com/watch?v=kOnsce0eudg&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=29&pp=gAQBiAQB) to follow with this guide!

> Note: The Operating System I am using is Oracle Linux 8, commands may vary depending on the Operating System of choice!

Splunk is a very useful server to centralize logs for viewing and dashboards. 

To get started with install a Splunk server we will need to install wget to download the packages.
```sh
yum -y install wget 
```

Then we will need to grab the splunk RPM from their site, this can be found by logging into their site and getting the download page for splunk server. 
```sh
wget -O splunk-9.1.0.2-b6436b649711.x86_64.rpm "https://download.splunk.com/products/splunk/releases/9.1.0.2/linux/splunk-9.1.0.2-b6436b649711.x86_64.rpm"
```

Then install the package
```sh
rpm -i splunk-9.1.0.2-b6436b649711.x86_64.rpm
```

Start splunk as the splunk user 
```sh
su - splunk
splunk start --accept-license
```

Create the certificate on the step-ca server ( Run this command on step-ca server )
```sh
mkdir -p /root/splunk
cd /root/splunk 
step ca certificate splunk.dragon.local splunk.dragon.local.crt splunk.dragon.local.key
```

Copy the cert over to the splunk server. ( Run this command on splunk server )
```sh
scp root@<CA Server>:/root/splunk/splunk.dragon.local.crt /opt/splunk/etc/auth/sloccerts/splunk.dragon.local.crt
scp root@<CA Server>:/root/splunk/splunk.dragon.local.key /opt/splunk/etc/auth/sloccerts/splunk.dragon.local.key
```

Make sure the permissions are correct, should be owned by splunk 
```sh
chown -R splunk.splunk /opt/splunk/etc/auth/sloccerts/ 
```

Then edit the web.conf to use the certs from our Step-ca Server
```sh
vi /opt/splunk/etc/system/local/web.conf
```

Update the settings with the following
```
[settings]
enableSplunkWebSSL = true
privKeyPath = /opt/splunk/etc/auth/sloccerts/splunk.dragon.local.key
serverCert = /opt/splunk/etc/auth/sloccerts/splunk.dragon.local.crt
```

Restart splunk with the new changes as the splunk user 
```sh
su - splunk ( this is not needed if already as the splunk user from previously )
splunk restart
```

Enable splunk on boot
```sh
splunk enable boot-start 
```

You will then need to setup receiving in the web GUI. 
```
In the browser go to splunk navigate to Settings > Forwarding and receiving > Configure receiving 
Listen on this port: 9997
Save
```

# Configure Splunk Universal forwarder Linux 
Once you have your splunk server working, you will need to configure some universal forwarders on the clients that you want data to be ingesting into the server. 

Install wget to be able to grab the splunk forwarder package
```sh
yum -y install wget
```

Download the universal forward from splunk's web page
```sh
wget -O splunkforwarder-9.1.0.1-77f73c9edb85.x86_64.rpm "https://download.splunk.com/products/universalforwarder/releases/9.1.0.1/linux/splunkforwarder-9.1.0.1-77f73c9edb85.x86_64.rpm"
```

Install the RPM package 
```sh
rpm -i splunkforwarder-9.1.0.1-77f73c9edb85.x86_64.rpm
```

Start splunk as the splunkfwd user
```sh
su - splunk
splunk start --accept-license
```

Add where splunk should forward data 
```sh
splunk add forward-server splunk.dragon.local:9997
splunk set deploy-poll splunk.dragon.local:8089
```


# Troubleshooting
In the case you run into any issues with splunk, you can watch the following splunk log for errors
```sh
tail -f /opt/splunkforwarder/var/log/splunk/splunkd.log
```