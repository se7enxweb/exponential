---
title: package.ini [RepositorySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RepositorySettings] in package.ini: RepositoryDirectory, RemotePackagesIndexURL, RemotePackagesIndexURLBase, AdditionalRemotePackagesIndexURLs, RemotePackagesIndexSources, Vendor, DownloadAttempts, DownloadRetryDelays ..."
keywords: Einstellung, package.ini, RepositorySettings, INI, setting, RepositoryDirectory, RemotePackagesIndexURL, RemotePackagesIndexURLBase, AdditionalRemotePackagesIndexURLs, RemotePackagesIndexSources, Vendor, DownloadAttempts, DownloadRetryDelays, DownloadTimeout, DownloadConnectTimeout
type: generated
generated: exp:help:examples
---
# package.ini [RepositorySettings]

Der Block `[RepositorySettings]` von `settings/package.ini`, mit 10 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RepositoryDirectory

Standard: `packages`.

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

Standard: `https://exponential.packages.exponential.earth/exponential/6.0/6.0.15`.

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

Standard: `https://exponential.packages.exponential.earth/exponential`.

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

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
# ein leeres "AdditionalRemotePackagesIndexURLs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdditionalRemotePackagesIndexURLs[]
*/ ?>
```

## RemotePackagesIndexSources

> Lists of further package servers kept in other settings files, as  
> <file>.ini;<block>;<setting>, the setting an array of <name>=<URL>. The default reads  
> the servers added on ezupdate's Package servers page (Setup > Updates and packages),  
> which it keeps in settings/override/ezupdate.ini.append.php. Empty the list to read  
> only the servers named above.  

Standard: eine Liste mit 1: `ezupdate.ini;PackageServerSettings;Servers`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
# ein leeres "RemotePackagesIndexSources[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RemotePackagesIndexSources[]
RemotePackagesIndexSources[]=ezupdate.ini;PackageServerSettings;Servers
*/ ?>
```

## Vendor

> Vendor name of the Exponential package directory  

Standard: `7x`.

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

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
DownloadAttempts=3
*/ ?>
```

## DownloadRetryDelays

Standard: eine Liste mit 3: `2`, `5`, `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
# ein leeres "DownloadRetryDelays[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DownloadRetryDelays[]
DownloadRetryDelays[]=2
DownloadRetryDelays[]=5
DownloadRetryDelays[]=10
*/ ?>
```

## DownloadTimeout

> Seconds a whole download may take, and seconds to wait for the connection  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
DownloadTimeout=300
*/ ?>
```

## DownloadConnectTimeout

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
DownloadConnectTimeout=30
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
