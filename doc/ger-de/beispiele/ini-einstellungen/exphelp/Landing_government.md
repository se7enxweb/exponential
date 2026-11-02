---
title: exphelp.ini [Landing_government]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Landing_government] in exphelp.ini: StartPage, InfoPages, Related"
keywords: Einstellung, exphelp.ini, Landing_government, INI, setting, StartPage, InfoPages, Related
type: generated
generated: exp:help:examples
---
# exphelp.ini [Landing_government]

Der Block `[Landing_government]` von `settings/exphelp.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartPage

Standard: `help/government/government-start-here.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_government]
StartPage=help/government/government-start-here.md
*/ ?>
```

## InfoPages

Standard: eine Liste mit 2: `help/government/accessible-static-sites-for-public-information.md`, `help/static-publishing/keep-private-content-private.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_government]
# ein leeres "InfoPages[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
InfoPages[]
InfoPages[]=help/government/accessible-static-sites-for-public-information.md
InfoPages[]=help/static-publishing/keep-private-content-private.md
*/ ?>
```

## Related

Standard: eine Liste mit 5: `businesses`, `hosting`, `partner`, `admin`, `non-profit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_government]
# ein leeres "Related[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Related[]
Related[]=businesses
Related[]=hosting
Related[]=partner
Related[]=admin
Related[]=non-profit
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
