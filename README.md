

# CORE NE


## Instalation with Vagrant

Steps:
1. Clone the repository
1. Duplicate Vagrantfile.example and rename it as Vagrantfile
1. Specify your credentials in the fields "smb_username" and smb_password
1. ```cd ne-core-backend```
1. ```cd app```
1. Rename ```.env.example``` to ```.env``` 
1. Specify MySQL connection on .env file
    ```
    DB_CONNECTION=mysql
    DB_HOST=192.168.33.10
    DB_PORT=3306
    DB_DATABASE=ne-core
    DB_USERNAME=ne-core
    DB_PASSWORD=password
    ``` 
 
1. Run ```vagrant up``` ON ROOT DIRECTORY, NOT IN /app
    6. If fails (ERROR: unexpected EOF), try a vagrant reload because the image of mariadb is unstable

1. Go to Portainer http://192.168.33.10:9200/ and go to the php container
1. ```cd /var/www/app/```
1. Run ```composer install```
1. Run ```php artisan key:generate```
1. Run ```php artisan config:clear```
1. Run migrations ```php artisan migrate:refresh --seed```
1. Run ```php artisan passport:install```
1. Run ```php artisan optimize```

