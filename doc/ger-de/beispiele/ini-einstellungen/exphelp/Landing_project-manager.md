---
title: exphelp.ini [Landing_project-manager]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Landing_project-manager] in exphelp.ini: StartPage, ReadingPath, Related"
keywords: Einstellung, exphelp.ini, Landing_project-manager, INI, setting, StartPage, ReadingPath, Related
type: generated
generated: exp:help:examples
---
# exphelp.ini [Landing_project-manager]

Der Block `[Landing_project-manager]` von `settings/exphelp.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartPage

Standard: `help/leadership/project-managers-start-here.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_project-manager]
StartPage=help/leadership/project-managers-start-here.md
*/ ?>
```

## ReadingPath

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_project-manager]
# ein leeres "ReadingPath[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ReadingPath[]
*/ ?>
```

## Related

Standard: eine Liste mit 3: `sales-marketing`, `website-creator`, `partner`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_project-manager]
# ein leeres "Related[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Related[]
Related[]=sales-marketing
Related[]=website-creator
Related[]=partner
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
