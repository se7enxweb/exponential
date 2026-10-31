---
title: exp:expgraphqlstarter
audience: developer, admin
category: examples
summary: Erzeugt ein Starter-Frontend (Next.js, Nuxt, Astro, reines HTML oder Expo), das die Site über die GraphQL-API liest, mit Abfragen aus dem aktuellen Schema; ein Probelauf ohne --apply.
keywords: Konsole, Befehl, exp:expgraphqlstarter, expgraphqlstarter, console, command, CLI, expgraphqlstarter.php, exp:graphql:starter
type: generated
generated: exp:help:examples
---
# exp:expgraphqlstarter

Erzeugt ein Starter-Frontend (Next.js, Nuxt, Astro, reines HTML oder Expo), das die Site über die GraphQL-API liest, mit Abfragen aus dem aktuellen Schema; ein Probelauf ohne --apply.

Skript `bin/php/expgraphqlstarter.php`, Code in `kernel/private/classes/commands/expgraphqlstarter.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expgraphqlstarter --help
./console exp:graphql:starter --help
php bin/php/expgraphqlstarter.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Generate a starter front end for the GraphQL API
Writes a project into a new directory: the kit's files (shipped in share/graphql/starters/) with the
end point filled in, and queries generated from the schema of the siteaccess: the menu, a page by its
URL alias, and one query per content class with every field of the class, as a module of the kit and
as .graphql files for exp:graphql:persist. Every generated query is checked against the schema and its
limits first. Nothing is installed or deployed: run npm in the directory. Lists the files and writes
nothing without --apply.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--kit` | erforderlich | das Kit: html, next, nuxt, astro oder expo |
| `--to` | erforderlich | das Verzeichnis, in das das Projekt geschrieben wird (neu oder leer) |
| `--endpoint` | erforderlich | die Adresse des Endpunkts (Standard: site.ini [SiteSettings] SiteURL und /api/graphql) |
| `--list` |  | die Kits auflisten |
| `--apply` |  | schreiben; ohne diese Option wird nichts geändert |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expgraphqlstarter.php`, `kernel/private/classes/commands/expgraphqlstarter.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
