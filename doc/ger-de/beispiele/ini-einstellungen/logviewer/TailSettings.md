---
title: logviewer.ini [TailSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TailSettings] in logviewer.ini: InitialBytes, MaxBytesPerPoll, PollInterval, MaxPollInterval, MaxLinesKept, WrapLines, ChunkCacheTTL, ChunkCacheMaxBytes"
keywords: Einstellung, logviewer.ini, TailSettings, INI, setting, InitialBytes, MaxBytesPerPoll, PollInterval, MaxPollInterval, MaxLinesKept, WrapLines, ChunkCacheTTL, ChunkCacheMaxBytes
type: generated
generated: exp:help:examples
---
# logviewer.ini [TailSettings]

Der Block `[TailSettings]` von `settings/logviewer.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## InitialBytes

> How much of a file the page shows when it is opened: the last InitialBytes bytes, from the first whole line.  

Standard: `65536`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
InitialBytes=65536
*/ ?>
```

## MaxBytesPerPoll

> The most bytes one poll reads (a file that grew more than that is caught up over the next polls).  

Standard: `262144`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
MaxBytesPerPoll=262144
*/ ?>
```

## PollInterval

> The poll interval of the page in milliseconds, while lines arrive.  

Standard: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
PollInterval=1000
*/ ?>
```

## MaxPollInterval

> While nothing arrives the interval doubles after each empty poll, up to MaxPollInterval milliseconds.  

Standard: `15000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
MaxPollInterval=15000
*/ ?>
```

## MaxLinesKept

> The most lines the page keeps (older lines are dropped from the page, never from the file).  

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
MaxLinesKept=5000
*/ ?>
```

## WrapLines

> Whether long lines are wrapped for a viewer who has not chosen yet. The page's "Wrap lines" switch stores each  
> viewer's choice as the user preference admin\_logviewer\_wrap (1 or 0), kept for every log and every visit.  
> enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
WrapLines=enabled
*/ ?>
```

## ChunkCacheTTL

> The server keeps the last chunk read of each file for this many seconds, so several viewers of the same file,  
> and a poll whose file has not changed, are answered without opening it. 0 switches the cache off.  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
ChunkCacheTTL=10
*/ ?>
```

## ChunkCacheMaxBytes

> The most bytes of a chunk the cache keeps.  

Standard: `262144`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
ChunkCacheMaxBytes=262144
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
