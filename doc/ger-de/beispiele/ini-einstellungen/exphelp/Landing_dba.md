---
title: exphelp.ini [Landing_dba]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Landing_dba] in exphelp.ini: StartPage, InfoPages, Related"
keywords: Einstellung, exphelp.ini, Landing_dba, INI, setting, StartPage, InfoPages, Related
type: generated
generated: exp:help:examples
---
# exphelp.ini [Landing_dba]

Der Block `[Landing_dba]` von `settings/exphelp.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartPage

Standard: `help/database-administrators/database-administrators-start-here.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_dba]
StartPage=help/database-administrators/database-administrators-start-here.md
*/ ?>
```

## InfoPages

Standard: eine Liste mit 6: `help/hosting/operations/migrations-on-hosted-sites.md`, `help/migrations/README.md`, `help/migrations/recipes/engine-notes.md`, `help/migrations/recipes/coming-from-sql-update-files.md`, `help/migrations/recipes/captions-and-rollback-in-practice.md`, `help/migrations/when-a-migration-fails.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_dba]
# ein leeres "InfoPages[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
InfoPages[]
InfoPages[]=help/hosting/operations/migrations-on-hosted-sites.md
InfoPages[]=help/migrations/README.md
InfoPages[]=help/migrations/recipes/engine-notes.md
InfoPages[]=help/migrations/recipes/coming-from-sql-update-files.md
InfoPages[]=help/migrations/recipes/captions-and-rollback-in-practice.md
InfoPages[]=help/migrations/when-a-migration-fails.md
*/ ?>
```

## Related

Standard: eine Liste mit 2: `admin`, `hosting`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_dba]
# ein leeres "Related[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Related[]
Related[]=admin
Related[]=hosting
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
