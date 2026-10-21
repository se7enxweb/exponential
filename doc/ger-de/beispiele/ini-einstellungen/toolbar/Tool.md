---
title: toolbar.ini [Tool]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Tool] in toolbar.ini: AvailableToolArray"
keywords: Einstellung, toolbar.ini, Tool, INI, setting, AvailableToolArray
type: generated
generated: exp:help:examples
---
# toolbar.ini [Tool]

Der Block `[Tool]` von `settings/toolbar.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/toolbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/toolbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/toolbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/toolbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AvailableToolArray

Standard: eine Liste mit 13: `basket`, `preferred_currency`, `user_country`, `calendar`, `link`, `login`, `searchbox`, `node_list`, `users`, `notification`, `best_seller`, `create_object` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Tool]
# ein leeres "AvailableToolArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableToolArray[]
AvailableToolArray[]=basket
AvailableToolArray[]=preferred_currency
AvailableToolArray[]=user_country
AvailableToolArray[]=calendar
AvailableToolArray[]=link
AvailableToolArray[]=login
AvailableToolArray[]=searchbox
AvailableToolArray[]=node_list
AvailableToolArray[]=users
AvailableToolArray[]=notification
AvailableToolArray[]=best_seller
AvailableToolArray[]=create_object
AvailableToolArray[]=drafts
*/ ?>
```

## Siehe auch

- [Alle Blöcke von toolbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/toolbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
