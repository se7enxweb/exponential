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

> URL, von der der Setup-Assistent von Exponential Pakete holt  
> Wenn Sie die alten Pakete verwenden wollen, die in  
> Versionen vor 3.9 verfügbar waren, verwenden Sie stattdessen diese URL:  
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

> Ist RemotePackagesIndexURL leer, wird die Einstellung RemotePackagesIndexURLBase  
> verwendet, ergänzt um die Versionsnummern von Exponential, etwa  
> RemotePackagesIndexURLBase/x.y/x.y.z für ein stabiles Release (6.0/6.0.15) und  
> RemotePackagesIndexURLBase/x.y/x.y.zalpha1 für jedes andere  
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

> Weitere Paketserver für den Setup-Assistenten, den Kickstarter und exp:install (alle drei  
> lesen den Index gleich, eZStepSiteTypes). Jeder ist ein Ordner mit einer index.xml, eine  
> http://- oder https://-Adresse. Sie werden nach RemotePackagesIndexURL gelesen: Ein Paket, das ein  
> früherer Index aufführt, bleibt, wie es ist, ein weiterer Server fügt nur die Pakete hinzu, die die  
> früheren nicht haben, und ein Server, der sich nicht lesen lässt, wird übersprungen.  

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

> Listen weiterer Paketserver, die in anderen Einstellungsdateien gehalten werden, als  
> <file>.ini;<block>;<setting>, die Einstellung ein Array aus <name>=<URL>. Der Standard liest  
> die Server, die auf der Seite Paketserver von ezupdate hinzugefügt wurden (Setup > Updates und Pakete),  
> die es in settings/override/ezupdate.ini.append.php hält. Leeren Sie die Liste, um  
> nur die oben genannten Server zu lesen.  

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

> Herstellername des Paketverzeichnisses von Exponential  

Standard: `7x`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
Vendor=7x
*/ ?>
```

## DownloadAttempts

> Herunterladen eines Pakets (und des Index): wie oft das Setup es versucht, bevor  
> es aufgibt, und wie viele Sekunden es vor jedem weiteren Versuch wartet (der  
> letzte Wert gilt für die Versuche danach). Ein Paketserver, der gerade  
> neu veröffentlicht wird, antwortet einige Minuten lang mit Fehlern.  

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

> Sekunden, die ein ganzer Download dauern darf, und Sekunden Wartezeit auf die Verbindung  

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
