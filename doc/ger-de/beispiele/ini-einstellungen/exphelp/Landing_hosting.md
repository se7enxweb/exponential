---
title: exphelp.ini [Landing_hosting]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Landing_hosting] in exphelp.ini: StartPage, InfoPages, Related"
keywords: Einstellung, exphelp.ini, Landing_hosting, INI, setting, StartPage, InfoPages, Related
type: generated
generated: exp:help:examples
---
# exphelp.ini [Landing_hosting]

Der Block `[Landing_hosting]` von `settings/exphelp.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartPage

Standard: `help/hosting/hosting-companies-start-here.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_hosting]
StartPage=help/hosting/hosting-companies-start-here.md
*/ ?>
```

## InfoPages

Standard: eine Liste mit 11: `help/hosting/operations/migrations-on-hosted-sites.md`, `help/migrations/README.md`, `help/migrations/migrations-versus-migrating-a-site.md`, `help/migrations/recipes/deploying-with-migrations.md`, `help/migrations/add-a-remote-step-by-step.md`, `help/migrations/recipes/engine-notes.md`, `help/migrations/recipes/captions-and-rollback-in-practice.md`, `help/hosting/static-publishing-and-your-hosting.md`, `guides/host-static-sites-on-github-pages.md`, `help/static-publishing/github-pages-limits-and-terms.md`, `help/static-publishing/keep-private-content-private.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_hosting]
# ein leeres "InfoPages[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
InfoPages[]
InfoPages[]=help/hosting/operations/migrations-on-hosted-sites.md
InfoPages[]=help/migrations/README.md
InfoPages[]=help/migrations/migrations-versus-migrating-a-site.md
InfoPages[]=help/migrations/recipes/deploying-with-migrations.md
InfoPages[]=help/migrations/add-a-remote-step-by-step.md
InfoPages[]=help/migrations/recipes/engine-notes.md
InfoPages[]=help/migrations/recipes/captions-and-rollback-in-practice.md
InfoPages[]=help/hosting/static-publishing-and-your-hosting.md
InfoPages[]=guides/host-static-sites-on-github-pages.md
InfoPages[]=help/static-publishing/github-pages-limits-and-terms.md
InfoPages[]=help/static-publishing/keep-private-content-private.md
*/ ?>
```

## Related

Standard: eine Liste mit 6: `admin`, `partner`, `developer`, `reseller`, `bundler`, `dba`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_hosting]
# ein leeres "Related[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Related[]
Related[]=admin
Related[]=partner
Related[]=developer
Related[]=reseller
Related[]=bundler
Related[]=dba
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
