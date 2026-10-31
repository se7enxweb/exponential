---
title: exp:expgraphqlkeys
audience: developer, admin
category: examples
summary: "Die persönlichen API-Schlüssel der GraphQL-API: auflisten, einen für einen Benutzer erzeugen (das Token wird einmal gezeigt) oder einen widerrufen; ein Probelauf ohne --apply."
keywords: Konsole, Befehl, exp:expgraphqlkeys, expgraphqlkeys, console, command, CLI, expgraphqlkeys.php, exp:graphql:keys
type: generated
generated: exp:help:examples
---
# exp:expgraphqlkeys

Die persönlichen API-Schlüssel der GraphQL-API: auflisten, einen für einen Benutzer erzeugen (das Token wird einmal gezeigt) oder einen widerrufen; ein Probelauf ohne --apply.

Skript `bin/php/expgraphqlkeys.php`, Code in `kernel/private/classes/commands/expgraphqlkeys.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expgraphqlkeys --help
./console exp:graphql:keys --help
php bin/php/expgraphqlkeys.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The personal API keys of the GraphQL API
Lists the keys with a scope of the GraphQL API (graphql:read, graphql:write), makes one for a user
(the scopes are checked against the user's policies as on the user's own page; the token is shown
once and never stored), or revokes one. The keys are the kernel's personal API keys (rest.ini
[ApiKeySettings]): the owner manages them on the page API access, an administrator at
oauthadmin/keys. Making and revoking are recorded in the audit (access.apikey.create, .revoke).
Nothing is written without --apply.

./console exp:graphql:keys                                          the list
./console exp:graphql:keys --list --format=json
./console exp:graphql:keys --create --for=frontend --name='Shop front end' --apply
./console exp:graphql:keys --create --for=editor --scope=graphql:read,graphql:write --days=30 --apply
./console exp:graphql:keys --revoke=expk_4f2a9c1d7e3b --apply

Exit code: 0 done (or shown); 1 the key could not be made or revoked; 2 wrong options (an unknown
user or key, a scope the user may not have, a lifetime that is not offered).
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--list` |  | die Schlüssel auflisten (der Standard) |
| `--create` |  | einen Schlüssel erzeugen (mit --for) |
| `--for` | erforderlich | der Eigentümer des neuen Schlüssels (ein Login) |
| `--name` | erforderlich | der Name des neuen Schlüssels (Standard: GraphQL) |
| `--scope` | erforderlich | die Bereiche des neuen Schlüssels, durch Kommas getrennt: graphql:read (der Standard), graphql:write |
| `--days` | erforderlich | die Lebensdauer des neuen Schlüssels in Tagen (rest.ini [ApiKeySettings] ExpiryChoices[]; Standard 90) |
| `--revoke` | erforderlich | den Schlüssel mit diesem Präfix (expk_...) oder dieser ID widerrufen |
| `--format` | erforderlich | text (der Standard) oder json (mit --list) |
| `--apply` |  | schreiben; ohne diese Option wird nichts geändert |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expgraphqlkeys.php`, `kernel/private/classes/commands/expgraphqlkeys.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
