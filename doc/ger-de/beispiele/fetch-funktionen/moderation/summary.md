---
title: fetch( 'moderation', 'summary' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion summary des Moduls moderation: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, moderation, summary, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'moderation', 'summary' )

Definiert in `kernel/moderation/function_definition.php`; sie ruft `expModerationAdmin::fetchSummary()` auf (`kernel/classes/moderation/expmoderationadmin.php`).

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'moderation', 'summary' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'moderation', 'summary', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von moderation](README.md)
- [Die Ansichten des Moduls moderation](../../modulansichten/moderation/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/moderation/function_definition.php`, `kernel/classes/moderation/expmoderationadmin.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
