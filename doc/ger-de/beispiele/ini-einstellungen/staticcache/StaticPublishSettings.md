---
title: staticcache.ini [StaticPublishSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [StaticPublishSettings] in staticcache.ini: Enabled, TargetList, WorkDir, MaxSiteBytes, MaxFileBytes, Push, CommitName, CommitEmail"
keywords: Einstellung, staticcache.ini, StaticPublishSettings, INI, setting, Enabled, TargetList, WorkDir, MaxSiteBytes, MaxFileBytes, Push, CommitName, CommitEmail
type: generated
generated: exp:help:examples
---
# staticcache.ini [StaticPublishSettings]

Der Block `[StaticPublishSettings]` von `settings/staticcache.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/staticcache.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/staticcache.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/staticcache.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/staticcache.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Statisches Veröffentlichen: ein Front-Siteaccess, als Ordner statischer Dateien gebaut,  
> den jeder statische Host ausliefert (GitHub Pages, jeder Webserver), mit umgeschriebenen  
> Links und kopierten Dateien. Jedes Ziel ist ein Block \[Target\_<id>\] weiter unten;  
> ./console exp:static:targets listet sie, und exp:static:build baut eines.  
>
> Statisches Veröffentlichen als Ganzes: enabled oder disabled. Solange es disabled ist,  
> wird nichts gebaut.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
Enabled=disabled
*/ ?>
```

## TargetList

> Die Ziele, nach Kennung: die Kennungen der Blöcke \[Target\_<id>\], die gebaut werden.  
> Eine Kennung besteht aus 1 bis 50 Kleinbuchstaben, Ziffern, \_ und -.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
# ein leeres "TargetList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TargetList[]
*/ ?>
```

## WorkDir

> Wo die Builds liegen, relativ zum var-Verzeichnis der Website  
> (var/<site>/static-publish), sofern nicht absolut. Jedes Ziel hat darunter seinen eigenen  
> Ordner, nie im eigenen Repository der Website.  

Standard: `static-publish`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
WorkDir=static-publish
*/ ?>
```

## MaxSiteBytes

> Die meisten Bytes, die ein Build haben darf, bevor der Build warnt: die Größe, die der  
> Host einer Website erlaubt (GitHub Pages: 1 GB). Der Build warnt oberhalb von 90 %.  

Standard: `1000000000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
MaxSiteBytes=1000000000
*/ ?>
```

## MaxFileBytes

> Die meisten Bytes, die eine Datei haben darf, bevor der Build warnt: die Größe, die der  
> Host einer Datei erlaubt (GitHub: 100 MB).  

Standard: `100000000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
MaxFileBytes=100000000
*/ ?>
```

## Push

> Ob exp:static:publish und exp:static:rollback zum Remote des Ziels pushen:  
> enabled, oder disabled, um nur im Ordner des Ziels zu committen und zu taggen  
> (exp:static:push pusht später). Ein Push wird nie erzwungen.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
Push=enabled
*/ ?>
```

## CommitName

> Der Committer einer Veröffentlichung, wenn das Repository des Ziels keinen nennt.  

Standard: `Exponential static publishing`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
CommitName=Exponential static publishing
*/ ?>
```

## CommitEmail

> Seine E-Mail-Adresse; leer: static-publish@<Hostname>.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
CommitEmail=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von staticcache.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/staticcache.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
