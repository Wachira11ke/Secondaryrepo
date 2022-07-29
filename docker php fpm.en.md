+++
foo/bar
title = "Set up PHP-FPM and Nginx Docker Containers"
date = 2022-04-12
draft = false
keywords = ["docker php fpm"]
description = "Correctly link php-fpm and Nginx Docker containers"
tags = ["Docker", "Nginx"]
author = "John Wachira"
postlink = 29905953
inarticle = true

+++

In this tutorial, we will discuss how you can set up your PHP, PHP-FPM and NGINX containers when developing locally on Docker. 

**Takeaways**

1. You can construct and run containers on your command line.
2. The Docker file and its uses.
3. How containers interact.

Make sure you have the Docker program and Git Bash installed on your device.

## Set up the PHP cli Container

We will create the directory `C:/delft/docker-php/app`, where we store our source code:

```dockerfile
mkdir -p "C:/delft/docker-php/app"
```

In our tutorial, we will use the official PHP image. We run the code below;

```dockerfile
docker run -d --name docker-php -v "C:/delft/docker-php/app":/var/www php:7.0-cli
```

This means;

1. `docker run ` will run a container
2. `-d `   it will run in the background (detached)
3. `--name docker-php` specify the docker-php
4. `-v "C:/delft/docker-php/app":/var/www` will sync the directory `C:/delft/docker-php/app` on the windows host with` /var/www` in the container


5. `php:7.0-cli `will use this image to build the container

Output:

```dockerfile
$ docker run -d --name docker-php -v "C:/delft/docker-php/app":/var/www php:7.0-cli
Unable to find image 'php:7.0-cli' locally
```

Because our machine does not have the image, Docker will attempt to fetch the image from the official registry.

We run the `docker ps -a` command to see if the container is running.

You will note that the container stops running immediately after initialization. We need to add the `-i` argument to the `docker run` command.

Before running the `docker run` command again, run the command below;

```dockerfile
docker rm docker-php
```

The command above movers our fist `docker-php` since we can not use it again. Now we can run the `docker run` command with the `-i` flag.

```dockerfile
docker run -di --name docker-php -v "C:/delft/docker-php/app":/var/www php:7.0-cli
7b3024a542a2d25fd36cef96f4ea689ec7ebb758818758300097a7be3ad2c2f6

```

Run the `docker ps -a` command to check if the container is running.

To log in, run the command below;

```dockerfile
winpty docker exec -it docker-php bash
```

## Set up a Web Stack With PHP-FPM and NGINX

Let us now discuss how you can set up the php-fpm and Nginx containers.

### Set up NGINX

We begin with getting a server which will act as the container to run the official Nginx image. We will create a `docer-compose.yml` to run our latest Nginx image. We will utilize ports 80 and 8080.

```nginx
web:
 image: nginx:latest
 ports:
 - "8080:80"
```

We the run the `docer-compose up` command.

You will get;

![nginx](C:\Users\pc\Pictures\Camera Roll\Docker\nginx.png)

Let us proceed to mount the `docker-compose.yml` file to a local repository. We will use the folder`delft`, where our `docker-compose.yml` file is located.

```nginx
web:
    image: nginx:latest
    ports:
        - "8080:80"
    volumes:
        - ./delft:/delft
```

At this point, Nginx does not know our folder exists. We will use the following `site.conf` file to resolve this.

```nginx
server {
    index index.html;
    server_name php-docker.local;
    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;
    root /delft;
}
```

We need to activate the setup. Let us modify the `docker-compose.yml` file some more.

```nginx
web:
    image: nginx:latest
    ports:
        - "8080:80"
    volumes:
        - ./delft:/delft
        - ./site.conf:/etc/nginx/conf.d/site.conf
```

We can now add an `index.html ` to our `deft` folder and run the code below;

```nginx
docker-compose up
```

Our Nginx should be up and running.

### Add PHP-FPM

The next step is to fetch the official PHP7-FPM which will link to our Nginx container. The updated `docker-compose.yml` file should look like this;

```nginx
web:
    image: nginx:latest
    ports:
        - "8080:80"
    volumes:
        - ./delft:/delft
        - ./site.conf:/etc/nginx/conf.d/site.conf
    links:
        - php
php:
    image: php:7-fpm
```

We will now configure our Nginx container to interpret PHP files using the PHP-FPM container. Our updated `site.conf` file will read;

```nginx
server {
    index index.php index.html;
    server_name php-docker.local;
    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;
    root /delft;

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
}
```

Let's test the program. First, we will rename our `index.html` file to `index.php` and change the contents to;

```php
<?php
echo phpinfo();
```

Before running the `docker-compose up` command we need to mount out `delft` folder to our PHP container. The final iteration to our `docker-compose.yml` file will read;

```dockerfile
web:
    image: nginx:latest
    ports:
        - "8080:80"
    volumes:
        - ./delft:/delft
        - ./site.conf:/etc/nginx/conf.d/site.conf
    links:
        - php
php:
    image: php:7-fpm
    volumes:
        - ./delft:/delft
```

Running the `docker-compose up` command yields the image below.

![phpinfo](C:\Users\pc\Pictures\Camera Roll\Docker\phpinfo.png)

That sums up our tutorial. 