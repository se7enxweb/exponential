---
title: fetch( 'audit', 'can_read' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion can_read des Moduls audit: 1 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, audit, can_read, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'audit', 'can_read' )

Definiert in `kernel/audit/function_definition.php`; sie ruft `expAuditFunctionCollection::fetchCanRead()` auf (`kernel/classes/audit/console/expauditfunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `channel` | string | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'audit', 'can_read', hash( 'channel', 'channel' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'audit', 'can_read', array( 'channel' => 'channel' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von audit](README.md)
- [Die Ansichten des Moduls audit](../../modulansichten/audit/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/audit/function_definition.php`, `kernel/classes/audit/console/expauditfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
