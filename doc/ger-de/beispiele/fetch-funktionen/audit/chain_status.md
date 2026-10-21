---
title: fetch( 'audit', 'chain_status' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion chain_status des Moduls audit: 2 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, audit, chain_status, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'audit', 'chain_status' )

Definiert in `kernel/audit/function_definition.php`; sie ruft `expAuditFunctionCollection::fetchChainStatus()` auf (`kernel/classes/audit/console/expauditfunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `channel` | string | nein | `false` |
| `verify` | boolean | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'audit', 'chain_status', hash( 'channel', 'channel' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'audit', 'chain_status', hash( 'channel', 'channel',
                                             'verify', true() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'audit', 'chain_status', array( 'channel' => 'channel' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von audit](README.md)
- [Die Ansichten des Moduls audit](../../modulansichten/audit/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/audit/function_definition.php`, `kernel/classes/audit/console/expauditfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
