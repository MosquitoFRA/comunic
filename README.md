# Comunic

## What is Comunic ?

Comunic is an open-source social network. Its goal is to respect people's privacy, and to be an alternative to big social networks such as Facebook or Twitter. This project has been created by Pierre HUBERT with the help of some of his friends and of frameworks, you will find more details about this at http://communiquons.org/about.php

Today, the structure of Comunic has to be improved. This is the current goal for the evolution of the website. Don't hesitate to help us !


## Installation

Comunic requires some softwares to works; to install them you can type the following lines (in Ubuntu 16.04 and similar operating systems)

```bash
sudo apt-get install php7.0 php7.0-mysql mysql myqsl-server php7.0-zip php7.0-gd apache2 libapache2-mod-php7
```

To make Comunic operationnal, you will need to create a database and import the sql file contained in the admin2260 directory.

Then you will need to use the file ```conf.php``` of the same directory to specify the right website's URL, and configure all the available options. https is supported by the service.


## Official documentation

The official documentation of Comunic can be found at http://communiquons.org/developper