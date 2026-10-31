---
title: graphql.ini [GraphQLWebhooks]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GraphQLWebhooks] in graphql.ini: EndpointList, Timeout, MaxAttempts, RetryBackoff, MaxBackoff, BatchSize, LogMaxBytes"
keywords: Einstellung, graphql.ini, GraphQLWebhooks, INI, setting, EndpointList, Timeout, MaxAttempts, RetryBackoff, MaxBackoff, BatchSize, LogMaxBytes
type: generated
generated: exp:help:examples
---
# graphql.ini [GraphQLWebhooks]

Der Block `[GraphQLWebhooks]` von `settings/graphql.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/graphql.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/graphql.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/graphql.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/graphql.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## EndpointList

> Die Webhooks: Jedes Ereignis des Journals, das ein Endpunkt haben will, wird ihm als JSON per POST gesendet, signiert  
> mit HMAC-SHA-256 (X-Exponential-Signature: sha256=<Hex von HMAC(Geheimnis, X-Exponential-Timestamp + "." + Body)>),  
> vom Cronjob-Teil graphqlwebhooks (auch in der Gruppe frequent) oder von ./console exp:graphql:webhooks --deliver; nie  
> von der Anfrage, die den Inhalt geändert hat. Fügen Sie sie mit ./console exp:graphql:webhooks --add=<name>  
> --url=<https://...> --apply oder in Setup > GraphQL hinzu: Beides schreibt settings/override/graphql.ini.append.php  
> und legt das Signiergeheimnis im Geheimnisspeicher ab.  
>
> Die Endpunkte, jeder mit seinem Block \[GraphQLWebhook\_<name>\]:  
>
>   \[GraphQLWebhook\_shop\]  
>   URL=https://shop.example.com/hooks/exponential  
>   # die Ereignisse, die er haben will: publish, update, trash, delete, move, hide, reveal, mutation; keine: jedes  
>   Events\[\]=publish  
>   Events\[\]=update  
>   # nur Inhalte unter dem Wurzelknoten dieser Siteaccesses, in ihren Sprachen; keine: jeder Siteaccess  
>   SiteAccessList\[\]=eng  
>   # nur diese Klassen; keine: jede Klasse  
>   ClassList\[\]=article  
>   # nur Inhalte an oder unter diesen Knoten; keine: der ganze Baum  
>   SubtreeList\[\]=2  
>   # der NAME des Signiergeheimnisses im Geheimnisspeicher (Setup > Secrets) oder env:VARIABLE; der Wert steht nie  
>   # in einer Einstellungsdatei. Standard: graphql.webhook.<name>  
>   Secret=graphql.webhook.shop  
>   # event (der Standard): das Ereignis und seine IDs; query: die Antwort einer gespeicherten Abfrage (Query, ihr Name  
>   # oder SHA-256) mit den Variablen objectId, nodeId, version, language und event, ausgeführt als QueryUser (ein  
>   # Login; standardmäßig anonym)  
>   Payload=event  
>   Query=  
>   QueryUser=  
>   # disabled behält den Endpunkt, sendet ihm aber nichts  
>   Enabled=enabled  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLWebhooks]
# ein leeres "EndpointList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
EndpointList[]
*/ ?>
```

## Timeout

> Sekunden, die ein Endpunkt zum Antworten hat; eine 2xx-Antwort stellt zu, alles andere wird erneut versucht.  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLWebhooks]
Timeout=10
*/ ?>
```

## MaxAttempts

> Versuche je Zustellung; dann bleibt sie in var/<site>/graphql/webhooks/failed/, und das Zustellprotokoll sagt es.  

Standard: `8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLWebhooks]
MaxAttempts=8
*/ ?>
```

## RetryBackoff

> Sekunden vor dem zweiten Versuch, für jeden weiteren Versuch verdoppelt, höchstens MaxBackoff.  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLWebhooks]
RetryBackoff=30
*/ ?>
```

## MaxBackoff

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLWebhooks]
MaxBackoff=3600
*/ ?>
```

## BatchSize

> Höchstens so viele Zustellungen je Lauf.  

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLWebhooks]
BatchSize=200
*/ ?>
```

## LogMaxBytes

> Das Zustellprotokoll (var/<site>/graphql/webhooks/deliveries.jsonl) wird ab dieser Größe als deliveries.1.jsonl behalten.  

Standard: `1M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLWebhooks]
LogMaxBytes=1M
*/ ?>
```

## Siehe auch

- [Alle Blöcke von graphql.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/graphql.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
