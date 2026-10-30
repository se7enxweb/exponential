---
title: exp:expgraphqlquery
audience: developer, admin
category: examples
summary: Führt eine GraphQL-Abfrage auf der Konsole aus, als ein Benutzer der Installation (anonym, wenn --as keinen nennt), und gibt die Antwort aus.
keywords: Konsole, Befehl, exp:expgraphqlquery, expgraphqlquery, console, command, CLI, expgraphqlquery.php, exp:graphql:query
type: generated
generated: exp:help:examples
---
# exp:expgraphqlquery

Führt eine GraphQL-Abfrage auf der Konsole aus, als ein Benutzer der Installation (anonym, wenn --as keinen nennt), und gibt die Antwort aus.

Skript `bin/php/expgraphqlquery.php`, Code in `kernel/private/classes/commands/expgraphqlquery.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expgraphqlquery --help
./console exp:graphql:query --help
php bin/php/expgraphqlquery.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Run a GraphQL query and print the answer
The query is the argument, or a file of it (a path ending in .graphql or .gql, or - for standard
input). It runs in the siteaccess of --siteaccess with the rights of the user of --as (a login),
else as the anonymous user; the console may always read the schema (introspection). The API need
not be switched on.

./console exp:graphql:query '{ node(id: 2) { name children { totalCount } } }'
./console exp:graphql:query queries/menu.graphql --variables='{"depth": 2}' --as=admin
./console exp:graphql:query '{ siteaccess { name schemaHash } }' --format=table

Exit code: 0 an answer without errors; 1 an answer with errors; 3 the library is missing;
2 wrong options (no query, variables not JSON, an unknown user).
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--variables` | erforderlich | die Variablen, ein JSON-Objekt |
| `--operation` | erforderlich | die auszuführende Operation, wenn die Abfrage mehrere hat |
| `--as` | erforderlich | als dieser Benutzer ausführen (ein Login); Standard: anonym |
| `--format` | erforderlich | json (der Standard) oder table (eine Zeile je Wert: sein Pfad und der Wert) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[query]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expgraphqlquery.php`, `kernel/private/classes/commands/expgraphqlquery.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
