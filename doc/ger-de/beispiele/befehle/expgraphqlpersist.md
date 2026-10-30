---
title: exp:expgraphqlpersist
audience: developer, admin
category: examples
summary: Speichert GraphQL-Abfragen als gespeicherte Abfragen auf dem Server (aufgerufen nach Name oder SHA-256), listet sie auf oder entfernt sie; ein Probelauf ohne --apply.
keywords: Konsole, Befehl, exp:expgraphqlpersist, expgraphqlpersist, console, command, CLI, expgraphqlpersist.php, exp:graphql:persist
type: generated
generated: exp:help:examples
---
# exp:expgraphqlpersist

Speichert GraphQL-Abfragen als gespeicherte Abfragen auf dem Server (aufgerufen nach Name oder SHA-256), listet sie auf oder entfernt sie; ein Probelauf ohne --apply.

Skript `bin/php/expgraphqlpersist.php`, Code in `kernel/private/classes/commands/expgraphqlpersist.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expgraphqlpersist --help
./console exp:graphql:persist --help
php bin/php/expgraphqlpersist.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Store GraphQL queries on the server as persisted queries
Each .graphql file (a file, or every .graphql file of a directory) is checked against the schema of
the siteaccess and stored under its SHA-256 and its file name without .graphql (its name). A client
then runs it by name (GET /api/graphql?id=articles) or by hash (Apollo's persisted queries), and only
persisted queries are cached (graphql.ini ResponseCache). Nothing is written without --apply.

./console exp:graphql:persist queries/                  check every .graphql file, store nothing
./console exp:graphql:persist queries/ --apply          store them
./console exp:graphql:persist menu.graphql --name=main-menu --apply
./console exp:graphql:persist --list [--format=json]
./console exp:graphql:persist --remove=main-menu --apply

Exit code: 0 fine; 1 a query does not fit the schema (nothing is stored then); 3 the library is
missing; 2 wrong options.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--name` | erforderlich | der Name der Abfrage (nur bei einer Datei); Standard: der Dateiname ohne .graphql |
| `--list` |  | die gespeicherten Abfragen auflisten |
| `--remove` | erforderlich | die gespeicherte Abfrage mit diesem Namen oder SHA-256 entfernen (und ihre Namen) |
| `--apply` |  | schreiben; ohne diese Option wird nichts geändert |
| `--format` | erforderlich | text (der Standard) oder json (mit --list) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[path]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expgraphqlpersist.php`, `kernel/private/classes/commands/expgraphqlpersist.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
