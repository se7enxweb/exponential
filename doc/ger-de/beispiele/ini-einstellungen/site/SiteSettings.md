---
title: site.ini [SiteSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SiteSettings] in site.ini: SiteName, SiteURL, SiteDescription, MetaDataArray, IndexPage, ErrorHandler, DefaultPage, DefaultAccess ..."
keywords: Einstellung, site.ini, SiteSettings, INI, setting, SiteName, SiteURL, SiteDescription, MetaDataArray, IndexPage, ErrorHandler, DefaultPage, DefaultAccess, FrontSiteAccess, LoginPage, SSLPort, SSLProxyServerName, SiteList, 64bitCompatibilityMode, HTTPAUTHServerVariable, AdditionalLoginFormActionURL, AllowedRedirectHosts, DisallowedReturnViews
type: generated
generated: exp:help:examples
---
# site.ini [SiteSettings]

Der Block `[SiteSettings]` von `settings/site.ini`, mit 18 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SiteName

> Name of the site, will be used in default templates in titles.  

Standard: `Exponential`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
SiteName=Exponential
*/ ?>
```

## SiteURL

> URL of site, often used to link to site in emails etc.  
> must not start with http:// and must not end with /, eg:  
> SiteURL=example.com  
> SiteURL=example.com/french  
> SiteURL=example.com/index.php/english  

Standard: `example.com`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
SiteURL=example.com
*/ ?>
```

## SiteDescription

> Description (Short) of the site, will be used in templates in head html.  

Standard: `This is an Exponential website about online shopping!`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
SiteDescription=This is an Exponential website about online shopping!
*/ ?>
```

## MetaDataArray

> The parts of the url that is used for siteaccess mathing  
> if any, needed for reverse lookups. Will use siteaccess name  
> if undefined and current matching type is uri.  
> Note: can not be defined in ActiveAccessExtension settings  
> eg: SiteUriParts\[\]=english  
> SiteUriParts\[\]  
> List of metadata to set in pagelayout  

Standard: ein Hash mit 4: `author=Exponential`, `copyright=Exponential`, `description=Content Management System`, `keywords=cms, publish, e-commerce, content management, development framework`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
# ein leeres "MetaDataArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MetaDataArray[]
MetaDataArray[author]=Exponential
MetaDataArray[copyright]=Exponential
MetaDataArray[description]=Content Management System
MetaDataArray[keywords]=cms, publish, e-commerce, content management, development framework
*/ ?>
```

## IndexPage

> Which page to show when the root index (/) is accessed  

Standard: `/content/view/full/2/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
IndexPage=/content/view/full/2/
*/ ?>
```

## ErrorHandler

> What to do when a module does not exists, use either defaultpage or displayerror  
> If defaultpage is used, the DefaultPage will be shown when an error occured  

Standard: `displayerror`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
ErrorHandler=displayerror
*/ ?>
```

## DefaultPage

> The default page to show, e.g. after user login this will be used for default redirection  

Standard: `/content/view/full/2/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
DefaultPage=/content/view/full/2/
*/ ?>
```

## DefaultAccess

> Default access is needed when uri type matching is done, this is  
> because with empty urls it's not possible to fetch the access  

Standard: `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
DefaultAccess=admin
*/ ?>
```

## FrontSiteAccess

> The site an admin siteaccess belongs to: its "Open the site" and "View on  
> site" links (template operator siteaccess\_url() without a parameter) go to  
> this siteaccess. Set it in the admin siteaccess's site.ini.append.php when one  
> installation serves several sites, each with its own admin. Empty: DefaultAccess.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
FrontSiteAccess=
*/ ?>
```

## LoginPage

> How the login page should be handled, use embedded to show inside default pagelayout  
> or custom for loginpagelayout.tpl  

Standard: `custom`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
LoginPage=custom
*/ ?>
```

## SSLPort

> The SSL port, the default should be OK for most sites but can be  
> changed if different. If the port is detect all redirects will  
> be done with https protocol.  

Standard: `443`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
SSLPort=443
*/ ?>
```

## SSLProxyServerName

> If you are using a SSL Proxy you will need to enter here the name of the proxy server  

Standard: `localhost`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
SSLProxyServerName=localhost
*/ ?>
```

## SiteList

> A list of sites this server provides, this usually the  
> siteaccess for the user view when installing multiple packages.  
> e.g.  
> SiteList\[\]=news  
> SiteList\[\]=plain  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
# ein leeres "SiteList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SiteList[]
*/ ?>
```

## 64bitCompatibilityMode

> Enables/Disables 64bit compatibility mode.  
> Affects some php's functions like crc32.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
64bitCompatibilityMode=enabled
*/ ?>
```

## HTTPAUTHServerVariable

> Use this variable on FASTCGI/CGI setups to define the Server variable name of the HTTP authentication string.  
> Only Webdav and SOAP interfaces have a dependency on this setting.  

Standard: `Authorization`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
HTTPAUTHServerVariable=Authorization
*/ ?>
```

## AdditionalLoginFormActionURL

> Use this setting to specify additional URL for login and then use it in your login template  
> to perform login action to other site then the current one. For example from user site you  
> can login directly to the admin interfase. Note that standard Exponential templates do not  
> support this feature.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
AdditionalLoginFormActionURL=
*/ ?>
```

## AllowedRedirectHosts

> Array of allowed hosts for redirects with full URL from Exponential modules.  
> Note: Current host and configured hosts (HostMatchMapItems/HostUriMatchMapItems) are always considered trusted  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
# ein leeres "AllowedRedirectHosts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedRedirectHosts[]
*/ ?>
```

## DisallowedReturnViews

> AllowedRedirectHosts\[\]=www.sometrustedhost.com  
> Module views that "back to where you came from" never returns to: Cancel and Discard of user/edit,  
> user/password, user/setting, section/edit, state edits, role/edit, class/edit and class/groupedit, which go to  
> the page the form names (RedirectIfDiscarded) or the page viewed last. These views act on a POST, end the  
> session or start a download, so a GET there shows nothing or does something. The running view itself is  
> always skipped. See doc/eng-us/features/6.0/safe-redirects.md  

Standard: eine Liste mit 14: `content/action`, `content/removeobject`, `content/removenode`, `content/removeassignment`, `content/removeeditversion`, `content/download`, `user/logout`, `user/login`, `user/activate`, `user/success`, `user/forgotpassword`, `user/register` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
# ein leeres "DisallowedReturnViews[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DisallowedReturnViews[]
DisallowedReturnViews[]=content/action
DisallowedReturnViews[]=content/removeobject
DisallowedReturnViews[]=content/removenode
DisallowedReturnViews[]=content/removeassignment
DisallowedReturnViews[]=content/removeeditversion
DisallowedReturnViews[]=content/download
DisallowedReturnViews[]=user/logout
DisallowedReturnViews[]=user/login
DisallowedReturnViews[]=user/activate
DisallowedReturnViews[]=user/success
DisallowedReturnViews[]=user/forgotpassword
DisallowedReturnViews[]=user/register
DisallowedReturnViews[]=layout/set
DisallowedReturnViews[]=shop/add
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
