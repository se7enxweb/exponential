---
title: package.ini [RepositorySettings]
audience: developer, admin
category: examples
summary: "The settings of [RepositorySettings] in package.ini: RepositoryDirectory, RemotePackagesIndexURL, RemotePackagesIndexURLBase, AdditionalRemotePackagesIndexURLs, RemotePackagesIndexSources, Vendor, DownloadAttempts, DownloadRetryDelays ..."
keywords: package.ini, RepositorySettings, INI, setting, RepositoryDirectory, RemotePackagesIndexURL, RemotePackagesIndexURLBase, AdditionalRemotePackagesIndexURLs, RemotePackagesIndexSources, Vendor, DownloadAttempts, DownloadRetryDelays, DownloadTimeout, DownloadConnectTimeout
type: generated
generated: exp:help:examples
---
# package.ini [RepositorySettings]

The block `[RepositorySettings]` of `settings/package.ini`, with 10 setting(s).

Never edit `settings/package.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/package.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in an extension (read when the extension is active).

## RepositoryDirectory

Default: `packages`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
RepositoryDirectory=packages
*/ ?>
```

## RemotePackagesIndexURL

> URL where Exponential setup wizard will fetch packages from  
> If you want to use the old packages which were available in  
> versions prior to 3.9 use the following URL instead:  
> http://packages.ez.no/exponential/3.9legacypackages  

Default: `https://exponential.packages.exponential.earth/exponential/6.0/6.0.15`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
RemotePackagesIndexURL=https://exponential.packages.exponential.earth/exponential/6.0/6.0.15
*/ ?>
```

## RemotePackagesIndexURLBase

> If RemotePackagesIndexURL is empty, the RemotePackagesIndexURLBase setting  
> will be used, appended with the Exponential version numbers, like  
> RemotePackagesIndexURLBase/x.y/x.y.z for a stable release (6.0/6.0.15) and  
> RemotePackagesIndexURLBase/x.y/x.y.zalpha1 for any other  
> (ExponentialSDK::packageServerPath())  

Default: `https://exponential.packages.exponential.earth/exponential`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
RemotePackagesIndexURLBase=https://exponential.packages.exponential.earth/exponential
*/ ?>
```

## AdditionalRemotePackagesIndexURLs

> More package servers for the setup wizard, the kickstarter and exp:install (all three  
> read the index the same way, eZStepSiteTypes). Each is a folder with an index.xml, an  
> http:// or https:// address. They are read after RemotePackagesIndexURL: a package an  
> earlier index lists is kept as it is, a further server only adds the packages the  
> earlier ones do not have, and a server that cannot be read is skipped.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
# an empty "AdditionalRemotePackagesIndexURLs[]" first replaces the shipped list; without it the items are added
AdditionalRemotePackagesIndexURLs[]
*/ ?>
```

## RemotePackagesIndexSources

> Lists of further package servers kept in other settings files, as  
> <file>.ini;<block>;<setting>, the setting an array of <name>=<URL>. The default reads  
> the servers added on ezupdate's Package servers page (Setup > Updates and packages),  
> which it keeps in settings/override/ezupdate.ini.append.php. Empty the list to read  
> only the servers named above.  

Default: a list of 1: `ezupdate.ini;PackageServerSettings;Servers`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
# an empty "RemotePackagesIndexSources[]" first replaces the shipped list; without it the items are added
RemotePackagesIndexSources[]
RemotePackagesIndexSources[]=ezupdate.ini;PackageServerSettings;Servers
*/ ?>
```

## Vendor

> Vendor name of the Exponential package directory  

Default: `7x`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
Vendor=7x
*/ ?>
```

## DownloadAttempts

> Downloading a package (and the index): how many times the setup tries before  
> it gives up, and how many seconds it waits before each further attempt (the  
> last value is used for the attempts after it). A package server that is being  
> republished answers with errors for a few minutes.  

Default: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
DownloadAttempts=3
*/ ?>
```

## DownloadRetryDelays

Default: a list of 3: `2`, `5`, `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
# an empty "DownloadRetryDelays[]" first replaces the shipped list; without it the items are added
DownloadRetryDelays[]
DownloadRetryDelays[]=2
DownloadRetryDelays[]=5
DownloadRetryDelays[]=10
*/ ?>
```

## DownloadTimeout

> Seconds a whole download may take, and seconds to wait for the connection  

Default: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
DownloadTimeout=300
*/ ?>
```

## DownloadConnectTimeout

Default: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
DownloadConnectTimeout=30
*/ ?>
```

## Related

- [All blocks of package.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/package.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
