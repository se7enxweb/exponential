---
title: exp:expgraphqlstatus
audience: developer, admin
category: examples
summary: "Die GraphQL-API eines Siteaccess: ein- oder ausgeschaltet, Endpunkt, Bibliothek, Grenzen, Introspection, Benennung, Schema-Hash, Klassen und Datentypen ohne eigene Abbildung."
keywords: Konsole, Befehl, exp:expgraphqlstatus, expgraphqlstatus, console, command, CLI, expgraphqlstatus.php, exp:graphql:status
type: generated
generated: exp:help:examples
---
# exp:expgraphqlstatus

Die GraphQL-API eines Siteaccess: ein- oder ausgeschaltet, Endpunkt, Bibliothek, Grenzen, Introspection, Benennung, Schema-Hash, Klassen und Datentypen ohne eigene Abbildung.

Skript `bin/php/expgraphqlstatus.php`, Code in `kernel/private/classes/commands/expgraphqlstatus.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expgraphqlstatus --help
./console exp:graphql:status --help
php bin/php/expgraphqlstatus.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Show the GraphQL API of a siteaccess
Whether it is switched on, its end point, the library and its version, the limits, introspection,
the naming, the schema hash, the classes in the schema and the datatypes that have no mapping
of their own (they are strings).

./console exp:graphql:status --siteaccess=site
./console exp:graphql:status --format=json

Exit code: 0 the library is available; 3 it is missing; 2 wrong options.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | text (Standard) oder json |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expgraphqlstatus.php`, `kernel/private/classes/commands/expgraphqlstatus.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
