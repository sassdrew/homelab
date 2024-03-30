# Creating a DNS Server

To start of with any homelab, you will need a DNS service! Here is the [youtube video](https://www.youtube.com/watch?v=MJF2gCWse7k) to follow with this guide!

> Note: The Operating System I am using is Oracle Linux 8, commands may vary depending on the Operating System of choice

Install bind and bind-utils
```sh
yum install bind bind-utils -y
```

Enable and start named, this is the service that DNS we will be utilizing
```sh
systemctl start named
systemctl enable named
```

Edit /etc/named.conf ( we will use the vi editor but you may use whichever you are most comfortable with nano, joe, etc). Below are the updates you will need to make to the file or reference the named.conf in this repo for full configuration
```sh
vi /etc/named.conf
```

Comment out the following lines
```
//listen-on port 53 { 127.0.0.1; };
//listen-on-v6 port 53 { ::1; };
```

Add allow-query ( replace 172.168.1.0/24 with your network range )
```
allow-query {localhost; 172.168.1.0/24;};
```

add forward zones ( in my case we will be creating the dragon.local zone )
```
zone "dragon.local" IN {
type master;
file "/var/named/data/dragon.local.zone";
allow-update { none; };
};
```


Create the zone file and add the contents of dragon.local.zone into this with updating the IP to be your DNS server
```sh
vi /var/named/data/dragon.local.zone
```

Restart the named service
```sh
systemctl restart named
```

You can watch for errors by tailing the messages logs 
```sh
tail -f /var/log/messages
```

You can validate the configuration and zone files by using the following commands
```sh
named-checkconf /etc/named.conf
named-checkzone dragon.local /var/named/data/dragon.local.zone
```

You should now be able to utilize this as a DNS server. Remember each time you update DNS you will need to update the serial number! Have fun with your new DNS Server! 