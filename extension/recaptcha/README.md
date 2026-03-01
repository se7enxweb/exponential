## reCAPTCHA Solution for eZ Publish

reCAPTCHA is an extension that allows the integration of the reCAPTCHA
anti-spam CAPTCHA service in your eZ publish content objects.  reCAPTCHA
provides a reCAPTCHA datatype that can be used in editing content and
information collection. reCAPTCHA provides both text and audio CAPTCHAs.

## About reCAPTCHA
Over 60 million CAPTCHAs are solved every day by people around the world.
reCAPTCHA channels this human effort into helping to digitize books from the
Internet Archive. When you solve a reCAPTCHA, you help preserve literature by
deciphering a word that was not readable by computers. 

reCAPTCHA is a project of the School of Computer Science at Carnegie Mellon
University.

From: [https://developers.google.com/recaptcha/](https://developers.google.com/recaptcha/)

## Installation

Follow these steps to add the reCAPTCHA module to your eZ publish installation:

  1) Extract the archive into the /extension directory

  2) Edit site.ini.append in /settings/override. Add the following to the file:

       [ExtensionSettings]
       ActiveExtensions[]=recaptcha

     If you already have the [ExtensionSettings] block, just add the second line.

  3) Visit [https://www.google.com/recaptcha/admin](https://www.google.com/recaptcha/admin) to signup and get your own API keys

  4) Edit extension/settings/recaptcha.ini.append.php and add your API keys

  5) Clear Cache

## Usage

To use, simply add a reCAPTCHA attribute to your content class.  

## License

This file may be distributed and/or modified under the terms of the "GNU
General Public License" version 2 as published by the Free Software Foundation

This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING THE
WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.

The "GNU General Public License" (GPL) is available at
[http://www.gnu.org/copyleft/gpl.html](http://www.gnu.org/copyleft/gpl.html).

## About

reCAPTCHA provides a reCAPTCHA eZ publish datatype that can be used in user registartion, editing content and information collection.

[https://github.com/se7enxweb/recaptcha](https://github.com/se7enxweb/recaptcha "https://github.com/se7enxweb/recaptcha")

