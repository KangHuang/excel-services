## PHP platform for proprietary services powered by Excel

This project is developed based on Laravel 5.2 ( https://laravel.com/docs/5.2 ) and its tutorial application provided by bestmomo (https://github.com/bestmomo/laravel5-example ) including the frontend templates and functions for email confirmation, password reset, etc. The work of this project is elaborated in the dissertation report 
'developing cloud services for proprietary software'.

This application has been deployed in the website ( https://www.jjbioenergy.org/ ), where the usage (user manual) is explained in Appendix A of the dissertation report. This document explains the installation of the application.

### installation for Linux

1. Install LAMP stack `sudo apt-get install lamp-server^`

1. Install Composer (dependency manager for php) following https://getcomposer.org/

2. Install the framework Laravel following https://laravel.com/docs/5.2/installation

3. Assuming the project is located at `/ver/www/html/project`, execute the commands

`cd /ver/www/html/project`

`composer install`

4. Generate an application key for security

`php artisan key:generate`

5. create a new database and configure `.env` file at project root and cache it.

`php artisan config:cache`

6. configure permissions setting

`sudo chmod -R 777 storage`

`sudo chmod -R 775 public/excel`

`sudo chown -R www-data:root public/excel`

7. initialize database tables and seeds

`php artisan migrate --seed`

8. a. **To perform a simple testing with PHP built-in server**

`php artisan serve`

and perform testing using port 8000.

8. b. **To deploy the application within Apache web server**, configure the `000-default.conf` file under the 
`/etc/apache2/sites-available` directory, set

```
DocumentRoot /ver/www/html/project/public
```
```
<Directory /ver/www/html/project/public>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
</Directory>
```

enable `mod_rewrite` by

`a2enmod rewrite`

and restart apache server by

`sudp service apache2 restart`

Then performing testing at localhost.

**Note**: The testing version of localhost cannot provide `IPNListener` function developed, i.e., the site can make a payment with PayPal but cannot receive IPN from PayPal server, hence cannot automatically authorize users' accessibility for paid services.

The PayPal application id and secret are in `__construct` function of `PaymentContoller` class at `app/Controllers/PaymentController.php`



