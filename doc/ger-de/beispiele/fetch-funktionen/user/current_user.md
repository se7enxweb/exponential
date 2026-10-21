---
title: fetch( 'user', 'current_user' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion current_user des Moduls user: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, user, current_user, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'user', 'current_user' )

Definiert in `kernel/user/function_definition.php`; sie ruft `eZUserFunctionCollection::fetchCurrentUser()` auf (`kernel/user/ezuserfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'user', 'current_user' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'user', 'current_user', array() );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/contentstructuremenu/content_structure_menu_dynamic.tpl` Zeile 114:

```tpl
*}{default current_user=fetch('user','current_user')} data-tree-request="{json_encode( hash(
```

## Siehe auch

- [Alle Fetch-Funktionen von user](README.md)
- [Die Ansichten des Moduls user](../../modulansichten/user/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/function_definition.php`, `kernel/user/ezuserfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
