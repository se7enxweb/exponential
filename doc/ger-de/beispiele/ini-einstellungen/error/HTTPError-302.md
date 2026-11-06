---
title: error.ini [HTTPError-302]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HTTPError-302] in error.ini: HTTPName, HeaderList"
keywords: Einstellung, error.ini, HTTPError-302, INI, setting, HTTPName, HeaderList
type: generated
generated: exp:help:examples
---
# error.ini [HTTPError-302]

Der Block `[HTTPError-302]` von `settings/error.ini`, mit 2 Einstellung(en).

> Die anderen Status, mit denen eine verschobene Seite antworten kann (seit 6.0.15): ein vorübergehender  
> URL-Platzhalter (302) und die Weiterleitungen von exp\_url\_redirect, die den Status der URL-Zuordnung behalten,  
> aus der sie importiert wurden. Ohne die Gruppe eines Status antwortet eine solche Weiterleitung mit 301.  

Bearbeiten Sie `settings/error.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/error.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/error.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/error.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HTTPName

Standard: `Found`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[HTTPError-302]
HTTPName=Found
*/ ?>
```

## HeaderList

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[HTTPError-302]
# ein leeres "HeaderList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HeaderList[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von error.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/error.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
