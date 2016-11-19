---
title: Get Comunic
---

# Get Comunic
## Get Comunic to test it

This page will explain you how to get Comunic working on your personnal server.

### Requirements

Comunic was written in PHP and MySQL. The current version of Comunic works PHP 7.0 and need to meet the following requirements :

* It is recommended to use a Unix operating system to install Comunic but if you want to contribute, it is **required** (Comunic webserver is running Debian)
* **PHP7.0**, **Apache 2**, **Mysql server** must be installed on the server
* The following PHP extensions must be installed and enabled : **php7.0-zip**, **php7.0-gd**, **php7.0-mysql**, **php7.0-pdo**
* The packages **php7.0-mysql** and **libapache2-mod-php7.0** helps the components to be connected between them.
* It is recommended to have at least **200 Mb** of free space to install Comunic

### Software installations

#### Windows

On Windows, you can install WAMP softwares to run Comunic, such as Wamp or uWamp. They presents by default all required extensions.


#### macOS

You can [download and install MAMP](http://www.mamp.info/en/index.html) as web development environment to host Comunic.


#### Debian and Ubuntu for developpment

Xampp is the recommended web development environment. [Download and install](http://www.apachefriends.org/) it from the official website.


#### Debian and Ubuntu for deployment

!!! **Warning:** This method has been tested only with Ubuntu 16.04 LTS on a 64bit computer.


##### System update

First, you will need to update your system. Refresh packages list using :

```sudo apt-get update```

Then upgrade it with the following command :

```sudo apt-get upgrade```


##### Requirements installation and configuration

Install all required software using :

```sudo apt-get install apache2 mysql myqsl-server php7.0 php7.0-mysql php7.0-zip php7.0-zip php7.0-gd libapache2-mod-php7.0```

You will need to enable rewrite module of Apache : ```sudo a2enmod rewrite```

Go into the Apache configuration and modify the following lines :

	<Directory /var/www/>
		Options Indexes FollowSymLinks
		AllowOverride None
		Require all granted
	</Directory>

into

	<Directory /var/www/>
		Options Indexes FollowSymLinks
		AllowOverride All
		Require all granted
	</Directory>

Then it is required to edit PHP configuration. Go ahead and edit the file 

!! **Warning** This tutorial is still under construction !