---
title: site.ini [SiteSettings]
audience: developer, admin
category: examples
summary: "The settings of [SiteSettings] in site.ini: SiteName, SiteURL, SiteDescription, MetaDataArray, IndexPage, ErrorHandler, DefaultPage, DefaultAccess ..."
keywords: site.ini, SiteSettings, INI, setting, SiteName, SiteURL, SiteDescription, MetaDataArray, IndexPage, ErrorHandler, DefaultPage, DefaultAccess, FrontSiteAccess, LoginPage, SSLPort, SSLProxyServerName, SiteList, 64bitCompatibilityMode, HTTPAUTHServerVariable, AdditionalLoginFormActionURL, AllowedRedirectHosts, DisallowedReturnViews
type: generated
generated: exp:help:examples
---
# site.ini [SiteSettings]

The block `[SiteSettings]` of `settings/site.ini`, with 18 setting(s).

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## SiteName

> Name of the site, will be used in default templates in titles.  

Default: `Exponential`.

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

Default: `example.com`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
SiteURL=example.com
*/ ?>
```

## SiteDescription

> Description (Short) of the site, will be used in templates in head html.  

Default: `This is an Exponential website about online shopping!`.

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

Default: a hash of 4: `author=Exponential`, `copyright=Exponential`, `description=Content Management System`, `keywords=cms, publish, e-commerce, content management, development framework`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
# an empty "MetaDataArray[]" first replaces the shipped list; without it the items are added
MetaDataArray[]
MetaDataArray[author]=Exponential
MetaDataArray[copyright]=Exponential
MetaDataArray[description]=Content Management System
MetaDataArray[keywords]=cms, publish, e-commerce, content management, development framework
*/ ?>
```

## IndexPage

> Which page to show when the root index (/) is accessed  

Default: `/content/view/full/2/`.

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

Default: `displayerror`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
ErrorHandler=displayerror
*/ ?>
```

## DefaultPage

> The default page to show, e.g. after user login this will be used for default redirection  

Default: `/content/view/full/2/`.

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

Default: `admin`.

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

Default: empty.

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

Default: `custom`.

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

Default: `443`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
SSLPort=443
*/ ?>
```

## SSLProxyServerName

> If you are using a SSL Proxy you will need to enter here the name of the proxy server  

Default: `localhost`.

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

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
# an empty "SiteList[]" first replaces the shipped list; without it the items are added
SiteList[]
*/ ?>
```

## 64bitCompatibilityMode

> Enables/Disables 64bit compatibility mode.  
> Affects some php's functions like crc32.  

Default: `enabled`.

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

Default: `Authorization`.

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

Default: empty.

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

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
# an empty "AllowedRedirectHosts[]" first replaces the shipped list; without it the items are added
AllowedRedirectHosts[]
*/ ?>
```

## DisallowedReturnViews

> AllowedRedirectHosts\[\]=www.sometrustedhost.com  
> Module views that "back to where you came from" never returns to: Cancel and Discard of user/edit,  
> user/password, user/setting, section/edit, state edits, role/edit, class/edit and class/groupedit, which go to  
> the page the form names (RedirectIfDiscarded) or the page viewed last. These views act on a POST, end the  
> session or start a download, so a GET there shows nothing or does something. The running view itself is  
> always skipped. See doc/features/6.0/safe-redirects.md  

Default: a list of 14: `content/action`, `content/removeobject`, `content/removenode`, `content/removeassignment`, `content/removeeditversion`, `content/download`, `user/logout`, `user/login`, `user/activate`, `user/success`, `user/forgotpassword`, `user/register` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
# an empty "DisallowedReturnViews[]" first replaces the shipped list; without it the items are added
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

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
