---
title: exphelp.ini [Tracker_community]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Tracker_community] in exphelp.ini: Name, Description, URL, NewIssueURL"
keywords: Einstellung, exphelp.ini, Tracker_community, INI, setting, Name, Description, URL, NewIssueURL
type: generated
generated: exp:help:examples
---
# exphelp.ini [Tracker_community]

Der Block `[Tracker_community]` von `settings/exphelp.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Exponential project and community`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Tracker_community]
Name=Exponential project and community
*/ ?>
```

## Description

Standard: `The project, the website, the ecosystem and anything that is not an error in a piece of software`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Tracker_community]
Description=The project, the website, the ecosystem and anything that is not an error in a piece of software
*/ ?>
```

## URL

Standard: `https://github.com/se7enxweb/exponential-community/issues`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Tracker_community]
URL=https://github.com/se7enxweb/exponential-community/issues
*/ ?>
```

## NewIssueURL

Standard: `https://github.com/se7enxweb/exponential-community/issues/new`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Tracker_community]
NewIssueURL=https://github.com/se7enxweb/exponential-community/issues/new
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
