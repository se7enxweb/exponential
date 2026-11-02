---
title: exphelp.ini [Landing_integrator]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Landing_integrator] in exphelp.ini: StartPage, InfoPages, Related"
keywords: Einstellung, exphelp.ini, Landing_integrator, INI, setting, StartPage, InfoPages, Related
type: generated
generated: exp:help:examples
---
# exphelp.ini [Landing_integrator]

Der Block `[Landing_integrator]` von `settings/exphelp.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartPage

Standard: `help/integrators/integrators-start-here.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_integrator]
StartPage=help/integrators/integrators-start-here.md
*/ ?>
```

## InfoPages

Standard: eine Liste mit 8: `help/integrators/migrations-in-client-projects.md`, `help/migrations/README.md`, `help/migrations/recipes/migrations-in-an-extension-or-package.md`, `help/migrations/recipes/deploying-with-migrations.md`, `help/migrations/recipes/coming-from-kaliop-ezmigrationbundle.md`, `help/migrations/faq.md`, `help/integrators/offer-static-hosting-to-your-clients.md`, `guides/host-static-sites-on-github-pages.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_integrator]
# ein leeres "InfoPages[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
InfoPages[]
InfoPages[]=help/integrators/migrations-in-client-projects.md
InfoPages[]=help/migrations/README.md
InfoPages[]=help/migrations/recipes/migrations-in-an-extension-or-package.md
InfoPages[]=help/migrations/recipes/deploying-with-migrations.md
InfoPages[]=help/migrations/recipes/coming-from-kaliop-ezmigrationbundle.md
InfoPages[]=help/migrations/faq.md
InfoPages[]=help/integrators/offer-static-hosting-to-your-clients.md
InfoPages[]=guides/host-static-sites-on-github-pages.md
*/ ?>
```

## Related

Standard: eine Liste mit 2: `partner`, `developer`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_integrator]
# ein leeres "Related[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Related[]
Related[]=partner
Related[]=developer
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
