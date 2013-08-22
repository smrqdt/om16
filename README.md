#tapeshop

A lightweight shop system. This is in early development and I would not recommend using it in production right know, since it is not tested and has some known security issues.

##Install
To install Tapeshop from the source you need [composer](http://www.getcomposer.org).

1. Download or checkout the sources of Tapeshop
2. Install composer dependencies and generate autoloader 
 ```composer.phar install```
2. Configure mod rewrite in your .htaccess file.

       RewriteEngine On
       RewriteBase /path/to/tapeshop/
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteRule ^ index.php [QSA,L]
If you get a ```Error 403``` you might need to add ```Options +FollowSymlinks```in the first line.

3. ~~Point your Browser to ```http://your.domain.tld/path/to/tapeshop/install.php```~~ For some reason this does not work all the time. If it works (which will result in a running application) you can stop here.
4. Change config.php to fit your database credentials and enter the email addresses for user notification and for support inquiries.

       <?php
       /**
        * Database Details
        */
       define( "DB_HOSTNAME", "localhost" );
       define( "DB_USERNAME", "root" );
       define( "DB_PASSWORD", "" );
       define( "DB_NAME", "tapeshop" );
       define( "DB_PROVIDER", "mysql" );
       
       /*
        * Email
        */
       define( "SHOPADRESS", "shop@example.com");
       define ("SUPPORTADRESS", "support@example.com");

4. Run the ```tapeshop.sql```against your databse to load the DB structure.
5. Point your browser to ```http://your.domain.tld/path/to/tapeshop/singup``` and fill out the form to create a user.
6. Use the database management tool of yout choice and find the users table. It should contain only the user you just created. Update the record and set the admin flag to ```true```or ```1```.
