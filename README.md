# Web-Item-Market project

Welcome to SWeb-Item-Market !

Web element gallery.

Application developped by DYS-Tools ( Sacha & Yohann Durand)

## Technology 

This architecture proposes a reutilisable code and easy to maintain. It also provides good practice like MVC layout and object oriented

- Symfony ( 5.0.8 )
- CircleCI
- Docker ( configure your environment)
- Ansible ( deploy with ansible folder)


## Use this project 
How to use this project on your environment ? 

-  clone this project on your environment 
-  configure your variable environment
-  run `composer install`
-  run `yarn install`
-  run `php bin/console d:d:c`
-  run `php bin/console make:migration`
-  run `php bin/console d:m:m`
-  You can run this project with docker containers (docker-compose included in this repository )


##### For Docker run :
run this project with docker containers (docker-compose included in this repository )
```
docker-compose up -d
```
## Deployment

##### For Ansible, create your ansible/hosts.ini and ansible/templates/.env and run:
```
ansible-playbook ansible/playbook.yml -i ansible/hosts.ini --ask-vault-pass
```

##### This website is available in "..." 

## Testing 
For generate a coverage-html
```
php bin/phpunit --coverage-html public/data 
```
Testing Symfony Website
```
php bin/phpunit
```

## Other information 
Visit our website for more informations

Standard :
1. PSR2 ( https://www.php-fig.org/psr/psr-2/ )

