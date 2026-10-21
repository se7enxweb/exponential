---
title: fetch( 'audit', 'event' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion event des Moduls audit: 1 Parameter, 1 erforderlich."
keywords: Ereignis, Fetch-Funktion, fetch, audit, event, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'audit', 'event' )

Definiert in `kernel/audit/function_definition.php`; sie ruft `expAuditFunctionCollection::fetchEvent()` auf (`kernel/classes/audit/console/expauditfunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `id` | string | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'audit', 'event', hash( 'id', 'id' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'audit', 'event', array( 'id' => 'id' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von audit](README.md)
- [Die Ansichten des Moduls audit](../../modulansichten/audit/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/audit/function_definition.php`, `kernel/classes/audit/console/expauditfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
