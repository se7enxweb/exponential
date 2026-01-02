## Exponential 6.0 INSTALL


Requirements
------------

### Apache version:

   The latest version of the 1.3 branch.
   or
   Apache 2.x run in "prefork" mode.

### PHP version:

   The latest version of the 5.2 branch is strongly recommended.

   Note that you will have to increase the default "memory_limit" setting
   which is located in the "php.ini" configuration file to 64 MB or larger. (Don't
   forget to restart Apache after editing "php.ini".)

   The date.timezone directive must be set in php.ini or in
   .htaccess. For a list of supported timezones please see
   http://php.net/manual/en/timezones.php

### Composer version:

   The latest version of the 2.x branch is recommended.

### Database server:
   MySQL 4.1 or later (UTF-8 is required)
   or
   PostgreSQL 8.x
   or
   Oracle 11g

### Zeta Components:

Exponential 6.0 edition requires the latest stable release of Zeta Components.
   To install this, you also need to use composer.


GitHub Installation Guide
------------------

- Clone the repository

`git clone git@github.com:se7enxweb/exponential.git;`

- Install Exponential required PHP libraries like Zeta Components and Exponential extensions as specified in this project's composer.json.

`cd exponential; composer install;`

For the rest of the installation steps you will find the installation guide at https://doc.exponential.earth/Exponential/Technical-manual/6.x/Installation.html


Composer Installation Guide
------------------

- Install Exponential and Required PHP libraries like Zeta Components and Exponential extensions as specified in this project's composer.json.

`cd www_root_directory; composer create-project se7enxweb/exponential:v6.0.0 exponential;`

For the rest of the installation steps you will find the installation guide at https://doc.exponential.earth/Exponential/Technical-manual/6.x/Installation.html


File based Composer Installation Guide
------------------

- Download the package from [se7enxweb/exponential](https://packagist.org/packages/se7enxweb/exponential)

`mkdir exponential;`

- Install Exponential required PHP libraries like Zeta Components and Exponential extensions as specified in this project's composer.json.

`cd exponential; composer require se7enxweb/exponential:v6.0.0;`

For the rest of the installation steps you will find the installation guide at https://doc.exponential.earth/Exponential/Technical-manual/6.x/Installation.html
