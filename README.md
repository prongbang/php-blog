# Slim Framework 3 Skeleton Application

Use this skeleton application to quickly setup and start working on a new Slim Framework 3 application. This application uses the latest Slim 3 with the PHP-View template renderer. It also uses the Monolog logger.

This skeleton application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

## Install the Application

Run this command from the directory in which you want to install your new Slim Framework application.

    php composer.phar create-project slim/slim-skeleton [my-app-name]

Replace `[my-app-name]` with the desired directory name for your new application. You'll want to:

* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writeable.

To run the application in development, you can also run this command. 

	php composer.phar start

Run this command to run the test suite

	php composer.phar test

That's it! Now go build something cool.

## ORM 
> composer require illuminate/database "~5.1"
 * https://laravel.com/api/4.2/Illuminate/Database/Eloquent/Model.html
 * https://stackoverflow.com/questions/19325312/how-to-create-multiple-where-clause-query-using-laravel-eloquent
 * https://laracasts.com/discuss/channels/eloquent/how-do-you-parameterize-whereraw-in-the-query-builder
 * https://laracasts.com/discuss/channels/eloquent/combining-multiple-hasmany-relationships-into-one-model-relation-query-builder
 * http://fideloper.com/laravel-raw-queries
 * https://code.tutsplus.com/tutorials/using-illuminate-database-with-eloquent-in-your-php-app-without-laravel--cms-27247
 * https://siipo.la/blog/how-to-use-eloquent-orm-migrations-outside-laravel
 * http://www.bradcypert.com/building-a-restful-api-in-php-using-slim-eloquent/
 * https://www.slimframework.com/2013/03/21/slim-and-laravel-eloquent-orm.html
 * https://www.slimframework.com/docs/cookbook/database-eloquent.html

## CSRF
> composer require slim/csrf

## TWIG
> composer require slim/twig-view
* https://twig.symfony.com/doc/2.x/filters/raw.html
