---
title: fetch( 'apikey', 'counts' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion counts des Moduls apikey: 1 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, apikey, counts, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'apikey', 'counts' )

Definiert in `kernel/apikey/function_definition.php`; sie ruft `expApiKeyFunctionCollection::fetchCounts()` auf (`kernel/classes/apikey/expapikeyfunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `user_id` | integer | nein | `0` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'apikey', 'counts', hash( 'user_id', 0 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'apikey', 'counts', array( 'user_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/user/setting.tpl` Zeile 43:

```tpl
{def $setting_key_counts = fetch( 'apikey', 'counts', hash( 'user_id', $userID ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von apikey](README.md)
- [Die Ansichten des Moduls apikey](../../modulansichten/apikey/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/apikey/function_definition.php`, `kernel/classes/apikey/expapikeyfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
