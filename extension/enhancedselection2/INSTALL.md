Enhanced Selection Datatype install instructions
------------------------------------------------

* Clone the repo

* Place the `enhancedselection2` folder in the `extension` folder.

* Import `sql/mysql/schema.sql` file to your database

* Open `settings/override/site.ini.append.php` and add the `enhancedselection2`
  extension to the active extensions.

* If upgrading from v1.x to v2.x, be sure to run `bin/php/migrate_to_database.php` script
  to migrate your datatype to the new version
