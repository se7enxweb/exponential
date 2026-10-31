---
title: graphql.ini [GraphQLEvents]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GraphQLEvents] in graphql.ini: JournalMaxBytes, JournalKeep"
keywords: Einstellung, graphql.ini, GraphQLEvents, INI, setting, JournalMaxBytes, JournalKeep
type: generated
generated: exp:help:examples
---
# graphql.ini [GraphQLEvents]

Der Block `[GraphQLEvents]` von `settings/graphql.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/graphql.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/graphql.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/graphql.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/graphql.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## JournalMaxBytes

> Das Ereignisjournal: was mit Inhalten geschieht (publish, update, trash, delete, move, hide, reveal und die  
> GraphQL-Mutationen), eine JSON-Zeile je Ereignis in var/<site>/graphql/events/, geschrieben am Ende der Anfrage, die  
> sie geändert hat, und gelesen von den Webhooks und den Subscriptions. Nur geschrieben, solange die API eingeschaltet  
> ist und jemand zuhört (Subscriptions=enabled oder ein Webhook in \[GraphQLWebhooks\] EndpointList\[\]). Eine Datei wächst  
> bis JournalMaxBytes (Bytes oder mit k, M, G), dann beginnt die nächste; die neuesten JournalKeep Dateien bleiben. Eine  
> Subscription oder ein Webhook, die weiter zurückliegen als die behaltenen Dateien, machen mit der ältesten weiter.  

Standard: `5M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLEvents]
JournalMaxBytes=5M
*/ ?>
```

## JournalKeep

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLEvents]
JournalKeep=3
*/ ?>
```

## Siehe auch

- [Alle Blöcke von graphql.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/graphql.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
