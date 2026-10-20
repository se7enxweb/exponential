---
title: exphelp.ini [CommitFeedSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CommitFeedSettings] in exphelp.ini: Enabled, FeedURL, CommitBaseURL, HistoryURL, CacheTTL, Timeout, RetryAfter, MaxCommits ..."
keywords: Einstellung, exphelp.ini, CommitFeedSettings, INI, setting, Enabled, FeedURL, CommitBaseURL, HistoryURL, CacheTTL, Timeout, RetryAfter, MaxCommits, PageSize
type: generated
generated: exp:help:examples
---
# exphelp.ini [CommitFeedSettings]

Der Block `[CommitFeedSettings]` von `settings/exphelp.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Die neuesten Commits des Projekts auf der Startseite der Entwickler (Block commits, expHelpCommitFeed). Der Server  
> liest den Feed (nie der Browser), höchstens einmal pro CacheTTL Sekunden, und blättert durch die Commits, die er gesehen hat.  
> enabled|disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CommitFeedSettings]
Enabled=enabled
*/ ?>
```

## FeedURL

> Der Atom-Feed der Commits (nur https)  

Standard: `https://github.com/se7enxweb/exponential/commits/main.atom`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CommitFeedSettings]
FeedURL=https://github.com/se7enxweb/exponential/commits/main.atom
*/ ?>
```

## CommitBaseURL

> Die Seite eines Commits: diese Adresse, gefolgt von seinem 40-stelligen Hash; und die ganze Historie  

Standard: `https://github.com/se7enxweb/exponential/commit/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CommitFeedSettings]
CommitBaseURL=https://github.com/se7enxweb/exponential/commit/
*/ ?>
```

## HistoryURL

Standard: `https://github.com/se7enxweb/exponential/commits/main`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CommitFeedSettings]
HistoryURL=https://github.com/se7enxweb/exponential/commits/main
*/ ?>
```

## CacheTTL

> Sekunden zwischen zwei Abrufen des Feeds; Sekunden, die ein Abruf dauern darf; Sekunden, bis ein fehlgeschlagener Abruf erneut versucht wird  

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CommitFeedSettings]
CacheTTL=600
*/ ?>
```

## Timeout

Standard: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CommitFeedSettings]
Timeout=4
*/ ?>
```

## RetryAfter

Standard: `120`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CommitFeedSettings]
RetryAfter=120
*/ ?>
```

## MaxCommits

> Wie viele Commits der Cache behält (der Feed enthält die neuesten 20; jeder Abruf fügt die neuen hinzu), und pro Seite  

Standard: `500`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CommitFeedSettings]
MaxCommits=500
*/ ?>
```

## PageSize

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CommitFeedSettings]
PageSize=10
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
