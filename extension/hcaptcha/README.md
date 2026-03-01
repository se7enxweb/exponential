## hCaptcha Solution for eZ Publish

hCaptcha is an extension that allows the integration of
the hCaptcha anti-spam CAPTCHA service in your eZ publish content objects.

hCaptcha provides a hCaptcha datatype that can be used in editing
content and information collection.

## About hCaptcha

Over 60 million CAPTCHAs are solved every day by people around the world.

hCaptcha offers a comparable service as the Google reCAPTCHA solution.

From: [https://docs.hcaptcha.com/](https://docs.hcaptcha.com/)

Migration: [https://docs.hcaptcha.com/switch](https://docs.hcaptcha.com/switch)

## Installation

Follow these steps to add the hCaptcha module to your eZ publish installation:

  1) Extract the archive into the extension/ directory

  2) Edit site.ini.append.php in settings/override/. Add the following to the file:

       [ExtensionSettings]
       ActiveExtensions[]=hcaptcha

     If you already have the [ExtensionSettings] block, just add the second line.

  3) Visit [https://dashboard.hcaptcha.com/signup](https://dashboard.hcaptcha.com/signup) and [https://dashboard.hcaptcha.com/sites](https://dashboard.hcaptcha.com/sites) to signup and get your own website API keys

  4) Edit extension/settings/hcaptcha.ini.append.php and add your API keys

  5) Clear Cache

## Usage

To use, simply add a hCaptcha attribute to your content class.  

## License

This file may be distributed and/or modified under the terms of the "GNU
General Public License" version 2 as published by the Free Software Foundation

This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING THE
WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.

The "GNU General Public License" (GPL) is available at
[http://www.gnu.org/copyleft/gpl.html](http://www.gnu.org/copyleft/gpl.html).

## About

hCaptcha provides a hCaptcha eZ publish datatype that can be used in user registartion, editing content and information collection.

[https://github.com/se7enxweb/hcaptcha](https://github.com/se7enxweb/hcaptcha "https://github.com/se7enxweb/hcaptcha")

This solution is based on an original creation by Bruce Morrison.

[https://github.com/se7enxweb/recaptcha](https://github.com/se7enxweb/recaptcha "https://github.com/se7enxweb/recaptcha")

