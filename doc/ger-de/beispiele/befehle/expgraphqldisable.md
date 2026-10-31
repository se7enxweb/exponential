---
title: exp:expgraphqldisable
audience: developer, admin
category: examples
summary: Schaltet die GraphQL-API oder nur ihre Mutationen für jeden Siteaccess oder für einige aus; ein Probelauf ohne --apply.
keywords: Konsole, Befehl, exp:expgraphqldisable, expgraphqldisable, console, command, CLI, expgraphqldisable.php, exp:graphql:disable
type: generated
generated: exp:help:examples
---
# exp:expgraphqldisable

Schaltet die GraphQL-API oder nur ihre Mutationen für jeden Siteaccess oder für einige aus; ein Probelauf ohne --apply.

Skript `bin/php/expgraphqldisable.php`, Code in `kernel/private/classes/commands/expgraphqldisable.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expgraphqldisable --help
./console exp:graphql:disable --help
php bin/php/expgraphqldisable.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Switch the GraphQL API off
Sets graphql.ini [GraphQLSettings] Enabled=disabled in settings/override (every siteaccess), or takes
the siteaccesses of --siteaccess out of SiteAccessList[] there (settings/override wins over the files
of a siteaccess; a list that would become empty switches the API off). With --mutations only the
mutations are switched off (MutationsEnabled=disabled; enabled is the default, together with the
API) and the queries keep answering: a read-only API. /api/graphql
then answers 404 (DISABLED); the persisted queries, the keys and the cache stay.
Shows the changes as a diff and writes nothing without --apply.

./console exp:graphql:disable                               what would change
./console exp:graphql:disable --apply
./console exp:graphql:disable --siteaccess=site --mutations --apply

Exit code: 0 done (or shown); 1 a file could not be written; 2 wrong options (an unknown siteaccess).
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--siteaccess` | erforderlich | nur diese Siteaccesses, durch Kommas getrennt (Standard: jeder Siteaccess) |
| `--mutations` |  | nur die Mutationen ausschalten (sie sind standardmäßig eingeschaltet): eine API, die nur liest |
| `--apply` |  | schreiben; ohne diese Option wird nichts geändert |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expgraphqldisable.php`, `kernel/private/classes/commands/expgraphqldisable.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
