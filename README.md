## PHP platform for proprietary services powered by Excel

This project is developed based on [Laravel 5.2](https://laravel.com/docs/5.2). It also uses the [HTML5 Boilerplate](https://startbootstrap.com/) and [Bootstrap](http://getbootstrap.com/) for frontend templates and JQuery and CSS plugins, [CKEditor](https://ckeditor.com/) is used for some text areas. In addition, [PHPExcel](https://github.com/PHPOffice/PHPExcel) is exploited for providing Excel services  The work of this project is elaborated in the dissertation report 
'developing cloud services for proprietary software'.

This application has been deployed in the website (https://www.jjbioenergy.org/), where the usage (user manual) is explained in Appendix A of the dissertation report. This document explains the installation of the application.

### installation for Linux

1. Install LAMP stack `sudo apt-get install lamp-server^`

2. Install Redis following https://www.rosehosting.com/blog/how-to-install-configure-and-use-redis-on-ubuntu-16-04/

3. Install Composer (dependency manager for php) following https://getcomposer.org/

4. Install the framework Laravel following https://laravel.com/docs/5.2/installation

5. Assuming the project is located at `/ver/www/html/project`, execute the commands

`cd /ver/www/html/project`

`composer install`

6. Generate an application key for security

`php artisan key:generate`

7. create a new database and configure `.env` file at project root and cache it.

`php artisan config:cache`

7. configure permissions setting

`sudo chmod -R 777 storage`

`sudo chmod -R 775 public/excel`

`sudo chown -R www-data:root public/excel`

8. initialize database tables and seeds

`php artisan migrate --seed`

9. a. **To perform a simple testing with PHP built-in server**

`php artisan serve`

and perform testing using port 8000.

9. b. **To deploy the application within Apache web server**, configure the `000-default.conf` file under the 
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



