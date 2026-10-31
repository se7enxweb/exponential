---
title: exp:expgraphqlenable
audience: developer, admin
category: examples
summary: "Schaltet die GraphQL-API mit einem Befehl ein: die Siteaccesses, die Mutationen, CORS für ein Frontend, ein API-Schlüssel, das Schema, ein curl-Beispiel; ein Probelauf ohne --apply."
keywords: Konsole, Befehl, exp:expgraphqlenable, expgraphqlenable, console, command, CLI, expgraphqlenable.php, exp:graphql:enable
type: generated
generated: exp:help:examples
---
# exp:expgraphqlenable

Schaltet die GraphQL-API mit einem Befehl ein: die Siteaccesses, die Mutationen, CORS für ein Frontend, ein API-Schlüssel, das Schema, ein curl-Beispiel; ein Probelauf ohne --apply.

Skript `bin/php/expgraphqlenable.php`, Code in `kernel/private/classes/commands/expgraphqlenable.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expgraphqlenable --help
./console exp:graphql:enable --help
php bin/php/expgraphqlenable.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Switch the GraphQL API on
Sets graphql.ini [GraphQLSettings] Enabled=enabled in settings/override, adds the siteaccesses of
--siteaccess to SiteAccessList[] (without it the API answers in every siteaccess); the mutations
come on with it (MutationsEnabled=enabled, the default), and --mutations writes that too, where
exp:graphql:disable --mutations switched them off. Lets a front end's origin call it with
--origin (rest.ini [CorsSettings]), makes a personal API key for a user with --key-for (the token
is shown once), checks that the schema builds, and prints the end point, a curl example and the
playground. Shows the changes as a diff and writes nothing without --apply; with --apply each file
is backed up first and the INI caches are cleared.

./console exp:graphql:enable                                        what would change
./console exp:graphql:enable --siteaccess=site --apply
./console exp:graphql:enable --siteaccess=site --origin=https://shop.example.com --key-for=frontend --apply
./console exp:graphql:enable --siteaccess=site --mutations --key-for=editor --apply

Exit code: 0 done (or shown); 1 a file could not be written, or the key not made; 3 the library is
missing; 2 wrong options (an unknown siteaccess or user, an origin that is not one).
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--siteaccess` | erforderlich | die Siteaccesses, in denen sie antwortet, durch Kommas getrennt (Standard: jeder Siteaccess) |
| `--origin` | erforderlich | der Origin eines Frontends, das sie aus einem Browser aufrufen darf (CORS), durch Kommas getrennt |
| `--mutations` |  | die Mutationen wieder einschalten (in den Siteaccesses aus --siteaccess); sie sind standardmäßig eingeschaltet |
| `--key-for` | erforderlich | einen persönlichen API-Schlüssel für diesen Benutzer erzeugen (ein Login) |
| `--key-name` | erforderlich | der Name des Schlüssels (Standard: GraphQL front end) |
| `--scope` | erforderlich | die Bereiche des Schlüssels: graphql:read (der Standard), graphql:write (der Standard mit --mutations) |
| `--days` | erforderlich | die Lebensdauer des Schlüssels in Tagen (rest.ini [ApiKeySettings] ExpiryChoices[]; Standard 90) |
| `--apply` |  | schreiben; ohne diese Option wird nichts geändert |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expgraphqlenable.php`, `kernel/private/classes/commands/expgraphqlenable.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
