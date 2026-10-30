---
title: exp:expgraphqlschema
audience: developer, admin
category: examples
summary: "Gibt das GraphQL-Schema eines Siteaccess aus oder schreibt es in eine Datei: die Schemasprache (sdl), das Ergebnis der Introspection (json) oder die Klassen und ihre Felder (summary)."
keywords: Konsole, Befehl, exp:expgraphqlschema, expgraphqlschema, console, command, CLI, expgraphqlschema.php, exp:graphql:schema
type: generated
generated: exp:help:examples
---
# exp:expgraphqlschema

Gibt das GraphQL-Schema eines Siteaccess aus oder schreibt es in eine Datei: die Schemasprache (sdl), das Ergebnis der Introspection (json) oder die Klassen und ihre Felder (summary).

Skript `bin/php/expgraphqlschema.php`, Code in `kernel/private/classes/commands/expgraphqlschema.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expgraphqlschema --help
./console exp:graphql:schema --help
php bin/php/expgraphqlschema.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Print the GraphQL schema of a siteaccess
sdl (the default): the schema language, for code generators and editors;
json: the introspection result ({ "data": { "__schema": ... } }), as a client reads it;
summary: the classes, their types and fields, the datatype of each field and whether it has a mapping.
The schema is printed whether or not the API is switched on (a schema for development).

./console exp:graphql:schema --siteaccess=site
./console exp:graphql:schema --format=json --output=schema.json

Exit code: 0 printed; 1 the file could not be written; 3 the library is missing; 2 wrong options.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | sdl (der Standard), json oder summary |
| `--output` | erforderlich | in diese Datei schreiben statt auf den Bildschirm |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expgraphqlschema.php`, `kernel/private/classes/commands/expgraphqlschema.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
