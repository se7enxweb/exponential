---
title: exp:expgraphqlwebhooks
audience: developer, admin
category: examples
summary: "Die Webhooks der GraphQL-API: auflisten, einen hinzufügen oder ändern (mit einem neuen Signiergeheimnis), einen entfernen, einen Test senden, sofort zustellen, das Zustellprotokoll lesen; ein Probelauf ohne --apply."
keywords: Konsole, Befehl, exp:expgraphqlwebhooks, expgraphqlwebhooks, console, command, CLI, expgraphqlwebhooks.php, exp:graphql:webhooks
type: generated
generated: exp:help:examples
---
# exp:expgraphqlwebhooks

Die Webhooks der GraphQL-API: auflisten, einen hinzufügen oder ändern (mit einem neuen Signiergeheimnis), einen entfernen, einen Test senden, sofort zustellen, das Zustellprotokoll lesen; ein Probelauf ohne --apply.

Skript `bin/php/expgraphqlwebhooks.php`, Code in `kernel/private/classes/commands/expgraphqlwebhooks.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expgraphqlwebhooks --help
./console exp:graphql:webhooks --help
php bin/php/expgraphqlwebhooks.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The webhooks of the GraphQL API
Every event of the GraphQL event journal an end point asks for (publish, update, trash, delete, move,
hide, reveal, mutation) is POSTed to it as JSON, signed with HMAC-SHA-256 (X-Exponential-Signature,
X-Exponential-Timestamp), by the cronjob part graphqlwebhooks, with retries. The end points are
settings (graphql.ini [GraphQLWebhooks] EndpointList[] and a block [GraphQLWebhook_<name>] each,
written to settings/override); the signing secret is kept in the secret store (Setup > Secrets)
and shown once, when --add makes it. Nothing is written or sent without --apply.

./console exp:graphql:webhooks                                    the list
./console exp:graphql:webhooks --add=shop --url=https://shop.example.com/hooks/exponential \
      --events=publish,update,trash --classes=article --siteaccess=eng --apply
./console exp:graphql:webhooks --add=search --url=https://search.example.com/hook --payload=query \
      --query=article_for_index --as=indexer --apply
./console exp:graphql:webhooks --test=shop --apply                 send a test event now
./console exp:graphql:webhooks --deliver --apply                   what the cronjob part does, now
./console exp:graphql:webhooks --log --limit=50
./console exp:graphql:webhooks --remove=shop --apply

Exit code: 0 done (or shown); 1 a file could not be written, a secret not stored, or the test was
not answered with 2xx; 2 wrong options (an unknown webhook, a URL or an event that is not one).
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--list` |  | die Webhooks auflisten (der Standard) |
| `--add` | erforderlich | den Webhook dieses Namens hinzufügen oder ändern (Kleinbuchstaben, Ziffern und _) |
| `--remove` | erforderlich | den Webhook dieses Namens entfernen (und sein Signiergeheimnis) |
| `--test` | erforderlich | ein Testereignis (ping) an den Webhook dieses Namens senden |
| `--deliver` |  | das Journal lesen und die fälligen Zustellungen senden, wie es der Cronjob-Teil tut |
| `--log` |  | die letzten Zeilen des Zustellprotokolls |
| `--url` | erforderlich | der Endpunkt (--add): http:// oder https:// |
| `--events` | erforderlich | die Ereignisse, die er haben will (--add), durch Kommas getrennt: publish, update, trash, delete, move, hide, reveal, mutation; keine: jedes Ereignis |
| `--siteaccess` | erforderlich | nur Inhalte dieser Siteaccesses (ihr Wurzelknoten und ihre Sprachen), durch Kommas getrennt |
| `--classes` | erforderlich | nur diese Klassenbezeichner, durch Kommas getrennt |
| `--subtree` | erforderlich | nur Inhalte an oder unter diesen Knoten-IDs, durch Kommas getrennt |
| `--payload` | erforderlich | event (der Standard): das Ereignis und seine IDs; query: die Antwort einer gespeicherten Abfrage |
| `--query` | erforderlich | der Name oder SHA-256 der gespeicherten Abfrage (--payload=query) |
| `--as` | erforderlich | der Benutzer, als der die gespeicherte Abfrage läuft (ein Login; Standard: anonym) |
| `--secret-name` | erforderlich | der Name des Signiergeheimnisses im Geheimnisspeicher oder env:VARIABLE (Standard graphql.webhook.<name>) |
| `--limit` | erforderlich | Zeilen des Zustellprotokolls (--log; Standard 20) |
| `--format` | erforderlich | text (der Standard) oder json (die Liste, --log) |
| `--apply` |  | schreiben und senden; ohne die Option wird nichts geändert |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expgraphqlwebhooks.php`, `kernel/private/classes/commands/expgraphqlwebhooks.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
