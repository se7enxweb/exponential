---
title: fetch( 'package', 'content_export_access' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion content_export_access des Moduls package: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, package, content_export_access, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'package', 'content_export_access' )

Definiert in `kernel/package/function_definition.php`; sie ruft `eZPackageFunctionCollection::contentExportAccess()` auf (`kernel/package/ezpackagefunctioncollection.php`).

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'package', 'content_export_access' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'package', 'content_export_access', array() );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/popupmenu/popup_export_items.tpl` Zeile 13:

```tpl
{if and( fetch( 'package', 'content_export_access' ), fetch( 'user', 'can_open', hash( 'uri', 'package/create' ) ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von package](README.md)
- [Die Ansichten des Moduls package](../../modulansichten/package/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/function_definition.php`, `kernel/package/ezpackagefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
