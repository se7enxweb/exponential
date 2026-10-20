---
title: fetch( 'audit', 'events' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion events des Moduls audit: 16 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, audit, events, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'audit', 'events' )

Definiert in `kernel/audit/function_definition.php`; sie ruft `expAuditFunctionCollection::fetchEvents()` auf (`kernel/classes/audit/console/expauditfunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `channel` | string | nein | `false` |
| `name` | string | nein | `false` |
| `user` | integer | nein | `false` |
| `login` | string | nein | `false` |
| `object` | mixed | nein | `false` |
| `target` | mixed | nein | `false` |
| `result` | string | nein | `false` |
| `severity` | string | nein | `false` |
| `request` | string | nein | `false` |
| `job` | string | nein | `false` |
| `run` | string | nein | `false` |
| `from` | string | nein | `false` |
| `to` | string | nein | `false` |
| `q` | string | nein | `false` |
| `offset` | integer | nein | `0` |
| `limit` | integer | nein | `10` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'audit', 'events', hash( 'channel', 'channel' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'audit', 'events', hash( 'channel', 'channel',
                                       'name', 'name',
                                       'user', 1,
                                       'login', 'login',
                                       'object', $object,
                                       'target', $target,
                                       'result', 'result',
                                       'severity', 'severity',
                                       'request', 'request',
                                       'job', 'job',
                                       'run', 'run',
                                       'from', 'from',
                                       'to', 'to',
                                       'q', 'q',
                                       'offset', 0,
                                       'limit', 10 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'audit', 'events', array( 'channel' => 'channel' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von audit](README.md)
- [Die Ansichten des Moduls audit](../../modulansichten/audit/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/audit/function_definition.php`, `kernel/classes/audit/console/expauditfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
