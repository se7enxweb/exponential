---
title: exphelp.ini [FeaturedSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FeaturedSettings] in exphelp.ini: PageList"
keywords: Einstellung, exphelp.ini, FeaturedSettings, INI, setting, PageList
type: generated
generated: exp:help:examples
---
# exphelp.ini [FeaturedSettings]

Der Block `[FeaturedSettings]` von `settings/exphelp.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PageList

> Page ids (paths below doc/eng-us/) shown first on the dashboard  

Standard: eine Liste mit 17: `help/getting-started.md`, `help/admin-tour.md`, `help/editing-content.md`, `help/who-is-7x.md`, `help/who-is-the-exponential-foundation.md`, `help/publishing-and-versions.md`, `help/accounts-and-sign-in.md`, `help/when-something-goes-wrong.md`, `help/develop-with-git.md`, `help/leadership/project-managers-start-here.md`, `help/business/sales-and-marketing-start-here.md`, `help/leadership/leadership-reading-path.md` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[FeaturedSettings]
# ein leeres "PageList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PageList[]
PageList[]=help/getting-started.md
PageList[]=help/admin-tour.md
PageList[]=help/editing-content.md
PageList[]=help/who-is-7x.md
PageList[]=help/who-is-the-exponential-foundation.md
PageList[]=help/publishing-and-versions.md
PageList[]=help/accounts-and-sign-in.md
PageList[]=help/when-something-goes-wrong.md
PageList[]=help/develop-with-git.md
PageList[]=help/leadership/project-managers-start-here.md
PageList[]=help/business/sales-and-marketing-start-here.md
PageList[]=help/leadership/leadership-reading-path.md
PageList[]=help/database-support.md
PageList[]=help/os-support.md
PageList[]=examples/README.md
PageList[]=standards/README.md
PageList[]=install/migration/README.md
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
