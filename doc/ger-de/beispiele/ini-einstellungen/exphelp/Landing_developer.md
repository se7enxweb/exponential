---
title: exphelp.ini [Landing_developer]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Landing_developer] in exphelp.ini: StartPage, InfoPages, Blocks, Related"
keywords: Einstellung, exphelp.ini, Landing_developer, INI, setting, StartPage, InfoPages, Blocks, Related
type: generated
generated: exp:help:examples
---
# exphelp.ini [Landing_developer]

Der Block `[Landing_developer]` von `settings/exphelp.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartPage

Standard: `help/develop-with-git.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_developer]
StartPage=help/develop-with-git.md
*/ ?>
```

## InfoPages

Standard: eine Liste mit 8: `help/migrations/README.md`, `guides/migrations-tutorial.md`, `help/migrations/recipes/add-a-database-table.md`, `help/migrations/recipes/testing-migrations.md`, `help/migrations/recipes/deploying-with-migrations.md`, `help/migrations/recipes/team-workflow-with-git.md`, `guides/static-publishing-ci-and-github-actions.md`, `guides/host-static-sites-on-github-pages.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_developer]
# ein leeres "InfoPages[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
InfoPages[]
InfoPages[]=help/migrations/README.md
InfoPages[]=guides/migrations-tutorial.md
InfoPages[]=help/migrations/recipes/add-a-database-table.md
InfoPages[]=help/migrations/recipes/testing-migrations.md
InfoPages[]=help/migrations/recipes/deploying-with-migrations.md
InfoPages[]=help/migrations/recipes/team-workflow-with-git.md
InfoPages[]=guides/static-publishing-ci-and-github-actions.md
InfoPages[]=guides/host-static-sites-on-github-pages.md
*/ ?>
```

## Blocks

Standard: eine Liste mit 8: `hero`, `start`, `boxes`, `categories`, `commits`, `key`, `latest`, `related`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_developer]
# ein leeres "Blocks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Blocks[]
Blocks[]=hero
Blocks[]=start
Blocks[]=boxes
Blocks[]=categories
Blocks[]=commits
Blocks[]=key
Blocks[]=latest
Blocks[]=related
*/ ?>
```

## Related

Standard: eine Liste mit 12: `app-creator`, `designer`, `admin`, `supporter`, `ecosystem`, `php-developer`, `ai-agents`, `software-engineer`, `web-framework-developer`, `cms-developer`, `dxp-developer`, `integrator`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[Landing_developer]
# ein leeres "Related[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Related[]
Related[]=app-creator
Related[]=designer
Related[]=admin
Related[]=supporter
Related[]=ecosystem
Related[]=php-developer
Related[]=ai-agents
Related[]=software-engineer
Related[]=web-framework-developer
Related[]=cms-developer
Related[]=dxp-developer
Related[]=integrator
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
