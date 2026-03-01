BC CIE - INSTALL
================


Introduction
============

## What is the BC CIE extension?

BC CIE is a true eZ Publish extension that provides cronjob parts, class methods and module views to provide easy export of collected informations from contentobjects into to csv or sylk (Excel) export files.

For more information about this extension please read the [README.md](../README.md) file.


## Copyright

BC CIE is copyright 1999 - 2015 Brookins Consulting and 2006 - 2007 Mathias VITALIS

See: [COPYRIGHT.md](../COPYRIGHT.md) for more information on the terms of the copyright and license


## License

BC CIE is licensed under the GNU General Public License.

The complete license agreement is included in the [LICENSE](LICENSE) file.

BC CIE is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

BC CIE is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

The GNU GPL gives you the right to use, modify and redistribute
BC CIE under certain conditions. The GNU GPL license
is distributed with the software, see the file [LICENSE](LICENSE).

It is also available at [http://www.gnu.org/licenses/gpl.txt](http://www.gnu.org/licenses/gpl.txt)

You should have received a copy of the GNU General Public License
along with BC CIE in [LICENSE](LICENSE). If not, see [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).

Using BC CIE under the terms of the GNU GPL is free (as in freedom).

For more information or questions please contact: license@brookinsconsulting.com


## Requirements

The following requirements exists for using BC CIE extension:

* eZ Publish version:

Make sure you use eZ Publish version 4.x (required) or higher. eZ Publish 4.6.x+ (Community Build, 2012.02+) is recommended.

* PHP version:

Make sure you have PHP 5.x or higher.


Getting eZ Publish
==================

You can download a version of eZ Publish from share.ez.no, you will find the various versions at:

[http://share.ez.no/download](http://share.ez.no/download)

NOTE: You will only require eZ Publish 4.x or higher (if you have a more recent version)

Information on the installation of eZ Publish can be found at:
[http://doc.ez.no/eZ-Publish/Technical-manual/4.6/Installation](http://doc.ez.no/eZ-Publish/Technical-manual/4.6/Installation)
and
[http://doc.ez.no](http://doc.ez.no)
[http://share.ez.no](http://share.ez.no)


Installing BC CIE extension
===========================

Copy the extension files into the extension directory


===========================

Copy the package into the `extension` directory in the root of your eZ Publish installation.


Unpack the extension package files into the extension directory
===========================

Unpack the files in the distribution. The command necessary is depends on the file you downloaded.

[tar.gz]

    tar -zxvf bccie-1_1_0.tar.gz

[zip]

    unzip bccie-1_1_0.tar.zip


(Optional) Composer installation of the latest GitHub brookinsconsulting bccie extension sources into the extension directory
===========================

You can optionaly fetch the latest extension source code from GitHub brookinsconsulting bccie repository via composer into the extension directory

    cd /path/to/ezpublish;
    composer require brookinsconsulting/bccie dev-master;
    sudo chmod -R 777 ../bccie;


(Optional) Git clone the latest GitHub brookinsconsulting bccie extension sources into the extension directory
===========================

You can optionaly fetch the latest extension source code from GitHub brookinsconsulting bccie repository into the extension directory

    cd /path/to/ezpublish;
    cd extension;

    mkdir bccie;
    cd bccie;

    git clone git@github.com:brookinsconsulting/bccie.git . ;

    sudo chmod -R 777 ../bccie;


(Optional) Git download the latest GitHub brookinsconsulting bccie extension sources into the extension directory
===========================

You can optionaly fetch the latest extension source code from GitHub brookinsconsulting bccie repository into the extension directory. Download or clone and place the downloaded extension into your extension folder.

    cd /path/to/ezpublish;
    cd extension;

    mkdir bccie;
    cd bccie;

    wget https://github.com/brookinsconsulting/bccie/tarball/master;

or

    wget https://github.com/brookinsconsulting/bccie/zipball/master

    tar -zxf brookinsconsulting-bccie-d1d1411.tar.gz;

or

    unzip brookinsconsulting-bccie-d1d1411.tar.gz;

    sudo chmod -R 777 ../bccie;


We must now enable the extension in eZ Publish.
===========================

To do this edit site.ini.append(.php) in the folder
/path/to/ezpublish/settings/override/. If this file does not exist;
create it. Locate (or add) the block

[ExtensionSettings] and add the line:

    ActiveExtensions[]=bccie

If you run several sites using only one distribution
and only some of the sites should use the extension,
make the changes in the override file of that siteaccess.

E.g /path/to/ezpublish/settings/siteaccess/ezwebin_site_user/site.ini.append(.php)
But instead of using ActiveExtensions you must add these lines instead:

    [ExtensionSettings]
    ActiveAccessExtensions[]=bccie

The extension can also be activated through eZ Publish admin interface.
An additional new menu tab "BC CIE Export" will appear in your Admin-interface.


Regenerate eZ Publish class autoloads
===========================

You must regenerate autoloads for extension classes to be available via eZ Publish autoloads. This may mean running the following different commands.

    cd /path/to/ezpublish;

    php bin/php/ezpgenerateautoloads.php -v -e;


There is no need to configure BC CIE extension after activation
===========================

There are optional settings in settings/bccie.ini mostly workflow event specific. Create a settings override to customize these values.


Configure optional settings
============================

There are optional settings in settings/bccie.ini which you can use to customize your use of the extension.


Usage
=====

Click the new "BC CIE Export" menu tab and follow the instructions on the page.


Troubleshooting
===============

* Read the FAQ

Some problems are more common than others. The most common ones are listed in the the [doc/FAQ.md](doc/FAQ.md).

* Support

If you have find any problems not handled by this document or the FAQ you can contact Brookins Consulting through the support system: [http://brookinsconsulting.com/contact](http://brookinsconsulting.com/contact)
