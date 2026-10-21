---
title: exphelp.ini [Landing_customer]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Landing_customer] in exphelp.ini: StartPage, Related"
keywords: Einstellung, exphelp.ini, Landing_customer, INI, setting, StartPage, Related
type: generated
generated: exp:help:examples
---
# exphelp.ini [Landing_customer]

Der Block `[Landing_customer]` von `settings/exphelp.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartPage

Standard: `help/customers/customers-start-here.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_customer]
StartPage=help/customers/customers-start-here.md
*/ ?>
```

## Related

Standard: eine Liste mit 5: `editor`, `admin`, `businesses`, `hosting`, `partner`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_customer]
# ein leeres "Related[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Related[]
Related[]=editor
Related[]=admin
Related[]=businesses
Related[]=hosting
Related[]=partner
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
