---
title: logfile.ini [ViewCountSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ViewCountSettings] in logfile.ini: SiteAccessList, AllSiteAccesses, MatchSiteAccess, StateDir, LockTimeout, RotatedSuffixes, MaxLineLength, LineRegexp ..."
keywords: Einstellung, logfile.ini, ViewCountSettings, INI, setting, SiteAccessList, AllSiteAccesses, MatchSiteAccess, StateDir, LockTimeout, RotatedSuffixes, MaxLineLength, LineRegexp, ViewModes, Methods, Statuses, SkipExtensions, SkipPathPrefixes, BotUserAgents, SkipEmptyUserAgent
type: generated
generated: exp:help:examples
---
# logfile.ini [ViewCountSettings]

Der Block `[ViewCountSettings]` von `settings/logfile.ini`, mit 15 Einstellung(en).

Bearbeiten Sie `settings/logfile.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logfile.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logfile.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logfile.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SiteAccessList

> Welche Siteaccesses ein Lauf zählt. Jeder liest das Zugriffsprotokoll, das seine eigene logfile.ini  
> \[AccessLogFileSettings\] nennt. Leer: nur der Siteaccess, für den der Lauf ist  
> (runcronjobs.php -s <siteaccess>), wie es der Cronjob immer tat.  
> Beispiel: SiteAccessList\[\]=site  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# ein leeres "SiteAccessList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SiteAccessList[]
*/ ?>
```

## AllSiteAccesses

> enabled: jeden Siteaccess aus AvailableSiteAccessList\[\] mit einem Zugriffsprotokoll zählen,  
> außer den Administrationen. SiteAccessList\[\] wird dann nicht gelesen.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
AllSiteAccesses=disabled
*/ ?>
```

## MatchSiteAccess

> Wie die Zeilen eines Protokolls den Siteaccesses zugeordnet werden. auto: nach der Siteaccess-Erkennung  
> des Kernels (site.ini \[SiteAccessSettings\] MatchOrder), wenn mehr als ein gezählter Siteaccess  
> das Protokoll liest, sonst alle Zeilen an den einen; enabled: immer zugeordnet, ein Protokoll mehrerer  
> Sites zählt also nur die Zeilen des gezählten Siteaccess; disabled: nie zugeordnet (jeder Siteaccess  
> eines gemeinsamen Protokolls zählt jede Zeile).  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
MatchSiteAccess=auto
*/ ?>
```

## StateDir

> Wo der Zustand liegt: eine Datei je Protokoll, darin die Stelle jedes Siteaccess.  
> Leer: viewcount unterhalb des VarDir des Siteaccess des Laufs. Ein relativer Pfad liegt unter der  
> Installation.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
StateDir=
*/ ?>
```

## LockTimeout

> Wie lange ein Lauf in Sekunden auf einen anderen Lauf wartet, der dasselbe Protokoll liest.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
LockTimeout=60
*/ ?>
```

## RotatedSuffixes

> Die Namen der rotierten Kopie eines Protokolls, an seinen Namen angehängt. Nach einer Rotation wird  
> zuerst der Rest der Kopie gelesen, dann das neue Protokoll von vorn. Eine Kopie wird an ihrer Inode oder  
> ihrer ersten Zeile erkannt, so wird auch eine komprimierte Kopie (.1.gz) gefunden.  

Standard: eine Liste mit 3: `.1`, `.1.gz`, `.0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# ein leeres "RotatedSuffixes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RotatedSuffixes[]
RotatedSuffixes[]=.1
RotatedSuffixes[]=.1.gz
RotatedSuffixes[]=.0
*/ ?>
```

## MaxLineLength

> Die längste gelesene Zeile in Bytes. Längere Zeilen werden übersprungen.  

Standard: `65536`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
MaxLineLength=65536
*/ ?>
```

## LineRegexp

> Ein eigenes Protokollformat: ein regulärer Ausdruck mit den benannten Gruppen time, request, status und,  
> wahlweise, host, ip, agent, referer. Leer: die Formate common, combined und vhost\_combined  
> (Apache, nginx, Plesk, Velocity), an jeder Zeile erkannt.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
LineRegexp=
*/ ?>
```

## ViewModes

> Die Ansichten, die für /content/view/<Ansicht>/<Knoten> zählen. Leer: jede Ansicht.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# ein leeres "ViewModes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ViewModes[]
*/ ?>
```

## Methods

> Die gezählten Anfragemethoden. Leer: jede Methode.  

Standard: eine Liste mit 1: `GET`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# ein leeres "Methods[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Methods[]
Methods[]=GET
*/ ?>
```

## Statuses

> Die gezählten Antwortstatus. 304 (nicht geändert) ist ein Aufruf einer Seite, die der Browser schon hatte.  
> Leer: jeder Status.  

Standard: eine Liste mit 2: `200`, `304`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# ein leeres "Statuses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Statuses[]
Statuses[]=200
Statuses[]=304
*/ ?>
```

## SkipExtensions

> Pfade mit diesen Endungen sind Dateien, keine Seitenaufrufe.  

Standard: eine Liste mit 33: `css`, `js`, `mjs`, `map`, `png`, `jpg`, `jpeg`, `gif`, `webp`, `avif`, `svg`, `ico` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# ein leeres "SkipExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SkipExtensions[]
SkipExtensions[]=css
SkipExtensions[]=js
SkipExtensions[]=mjs
SkipExtensions[]=map
SkipExtensions[]=png
SkipExtensions[]=jpg
SkipExtensions[]=jpeg
SkipExtensions[]=gif
SkipExtensions[]=webp
SkipExtensions[]=avif
SkipExtensions[]=svg
SkipExtensions[]=ico
SkipExtensions[]=bmp
SkipExtensions[]=tif
SkipExtensions[]=tiff
SkipExtensions[]=woff
SkipExtensions[]=woff2
SkipExtensions[]=ttf
SkipExtensions[]=otf
SkipExtensions[]=eot
# ... 13 weitere in der ausgelieferten Datei
*/ ?>
```

## SkipPathPrefixes

> Pfade, die so beginnen, sind keine Seitenaufrufe: Dateien, Dienste und Modulansichten. Verglichen  
> wird mit dem Pfad, wie er angefragt wurde, und mit dem Pfad ohne den Teil des Siteaccess.  

Standard: eine Liste mit 18: `/var/`, `/design/`, `/extension/`, `/share/`, `/lib/`, `/.well-known/`, `/ezjscore/`, `/api/`, `/user/`, `/layout/`, `/content/search`, `/content/download/` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# ein leeres "SkipPathPrefixes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SkipPathPrefixes[]
SkipPathPrefixes[]=/var/
SkipPathPrefixes[]=/design/
SkipPathPrefixes[]=/extension/
SkipPathPrefixes[]=/share/
SkipPathPrefixes[]=/lib/
SkipPathPrefixes[]=/.well-known/
SkipPathPrefixes[]=/ezjscore/
SkipPathPrefixes[]=/api/
SkipPathPrefixes[]=/user/
SkipPathPrefixes[]=/layout/
SkipPathPrefixes[]=/content/search
SkipPathPrefixes[]=/content/download/
SkipPathPrefixes[]=/content/edit/
SkipPathPrefixes[]=/content/action
SkipPathPrefixes[]=/notification/
SkipPathPrefixes[]=/shop/
SkipPathPrefixes[]=/rss/
SkipPathPrefixes[]=/Q/
*/ ?>
```

## BotUserAgents

> Anfragen, deren User-Agent einen dieser Einträge enthält (in beliebiger Schreibweise), sind Robots, keine Aufrufe.  

Standard: eine Liste mit 24: `bot`, `crawl`, `spider`, `slurp`, `scrap`, `warmer`, `preview`, `monitor`, `uptime`, `pingdom`, `curl/`, `wget/` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# ein leeres "BotUserAgents[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
BotUserAgents[]
BotUserAgents[]=bot
BotUserAgents[]=crawl
BotUserAgents[]=spider
BotUserAgents[]=slurp
BotUserAgents[]=scrap
BotUserAgents[]=warmer
BotUserAgents[]=preview
BotUserAgents[]=monitor
BotUserAgents[]=uptime
BotUserAgents[]=pingdom
BotUserAgents[]=curl/
BotUserAgents[]=wget/
BotUserAgents[]=python
BotUserAgents[]=java/
BotUserAgents[]=go-http-client
BotUserAgents[]=okhttp
BotUserAgents[]=libwww
BotUserAgents[]=httpclient
BotUserAgents[]=headless
BotUserAgents[]=lighthouse
# ... 4 weitere in der ausgelieferten Datei
*/ ?>
```

## SkipEmptyUserAgent

> enabled: eine Anfrage ohne User-Agent ist ein Robot.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
SkipEmptyUserAgent=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logfile.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logfile.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
