---
title: fetch( 'section', 'roles' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion roles des Moduls section: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, section, roles, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'section', 'roles' )

Definiert in `kernel/section/function_definition.php`; sie ruft `eZSectionFunctionCollection::fetchRoles()` auf (`kernel/section/ezsectionfunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `section_id` | integer | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'section', 'roles', hash( 'section_id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'section', 'roles', array( 'section_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/section/view.tpl` Zeile 22:

```tpl
{def $roles_array = fetch( 'section', 'roles', hash( 'section_id', $section.id ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von section](README.md)
- [Die Ansichten des Moduls section](../../modulansichten/section/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/section/function_definition.php`, `kernel/section/ezsectionfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
