# Creating a Kasm Server
Creating a Kasm Server! Here is the [youtube video](https://www.youtube.com/watch?v=rSQF9cN6KN0&list=PLhkW8M2MBf-H33LeTrVMc0LwN3EuOqGQV&index=28&t=544s&pp=gAQBiAQB) to follow with this guide!

> Note: The Operating System I am using is Oracle Linux 8, commands may vary depending on the Operating System of choice!

You can always check out the [latest install guide](https://kasmweb.com/docs/latest/index.html) for newer versions

To install download the gzip tarball
```sh
cd /tmp
curl -O https://kasm-static-content.s3.amazonaws.com/kasm_release_1.15.0.06fdc8.tar.gz
```

Extract the gzip tarball
```sh
tar -xf kasm_release_1.15.0.06fdc8.tar.gz
```

Run the install.sh script to install 
```sh
sudo bash kasm_release/install.sh
```

Once completed you can navigate to your browser to https://<Server> and login