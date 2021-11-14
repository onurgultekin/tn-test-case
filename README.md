# How it works?

After cloning the project, run:

 
###  `composer install`

After, run migrations as below:

###  `php artisan migrate`

After, you should seed database for apps table:

###  `php artisan db:seed`  

This will add 250 apps, 10 devices and 15K subscription to the related tables.

You should run 

###  `php artisan serve`

to start development server.

After, you should in an another terminal window below command to serve project with the port 8001 in the same time. This is because I wrote Google and Apple mock services in this project and it calls localhost:8001 to get data from Google and Apple mock services.

###  `php artisan serve --port=8001`

You can find database sql file in the project also.

###  sql_dump.sql

I created a Postman Collection also, you can find it in the project.

### postman_collection.json

All API endpoints can be found in it.

DB Diagram can be found in the file called

### db-diagram.png