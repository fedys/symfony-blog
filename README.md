# symfony-blog

This is a simple blog application built with [Symfony framework](https://symfony.com/).

## Installation
Run the following commands, assuming you have [docker](https://www.docker.com/) and [docker-compose](https://docs.docker.com/compose/)
installed on you machine:
```shell script
git clone git@github.com:fedys/symfony-blog.git
cd symfony-blog/docker
make init
```
When the installation successfully finishes, the application will be available on http://localhost/.  
(If there is a service that already listens on port `80` on your machine, feel free to specify a different port
via `docker-compose.override.yml` file.) 


## Administration
The administration is available on http://localhost/admin. Use the following credentials:  
Username: _admin_  
Password: _blog.1_

## API
The API documentation is available on http://localhost/api.

## Tests
Run the following commands to execute all tests, assuming you are in the project root:
```shell script
cd docker
make tests
```

