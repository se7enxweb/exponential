---
title: exphelp.ini [Tracker_velocity]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Tracker_velocity] in exphelp.ini: Name, Description, URL, NewIssueURL"
keywords: Einstellung, exphelp.ini, Tracker_velocity, INI, setting, Name, Description, URL, NewIssueURL
type: generated
generated: exp:help:examples
---
# exphelp.ini [Tracker_velocity]

Der Block `[Tracker_velocity]` von `settings/exphelp.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Exponential Velocity (the application server)`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Tracker_velocity]
Name=Exponential Velocity (the application server)
*/ ?>
```

## Description

Standard: `Errors of the Velocity server itself: starting, workers, its response cache, HTTPS, the exp:velocity command`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Tracker_velocity]
Description=Errors of the Velocity server itself: starting, workers, its response cache, HTTPS, the exp:velocity command
*/ ?>
```

## URL

Standard: `https://github.com/se7enxweb/exponential-velocity/issues`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Tracker_velocity]
URL=https://github.com/se7enxweb/exponential-velocity/issues
*/ ?>
```

## NewIssueURL

Standard: `https://github.com/se7enxweb/exponential-velocity/issues/new`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Tracker_velocity]
NewIssueURL=https://github.com/se7enxweb/exponential-velocity/issues/new
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
