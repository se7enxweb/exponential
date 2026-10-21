---
title: exphelp.ini [Landing_sponsor]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Landing_sponsor] in exphelp.ini: StartPage, Blocks, Related"
keywords: Einstellung, exphelp.ini, Landing_sponsor, INI, setting, StartPage, Blocks, Related
type: generated
generated: exp:help:examples
---
# exphelp.ini [Landing_sponsor]

Der Block `[Landing_sponsor]` von `settings/exphelp.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartPage

Standard: `help/sponsors/sponsors-start-here.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_sponsor]
StartPage=help/sponsors/sponsors-start-here.md
*/ ?>
```

## Blocks

Standard: eine Liste mit 8: `hero`, `support`, `start`, `boxes`, `categories`, `key`, `latest`, `related`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_sponsor]
# ein leeres "Blocks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Blocks[]
Blocks[]=hero
Blocks[]=support
Blocks[]=start
Blocks[]=boxes
Blocks[]=categories
Blocks[]=key
Blocks[]=latest
Blocks[]=related
*/ ?>
```

## Related

Standard: eine Liste mit 3: `supporter`, `partner`, `advocate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_sponsor]
# ein leeres "Related[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Related[]
Related[]=supporter
Related[]=partner
Related[]=advocate
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
