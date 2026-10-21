---
title: fetch( 'user', 'can_open' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion can_open des Moduls user: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, user, can_open, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'user', 'can_open' )

Definiert in `kernel/user/function_definition.php`; sie ruft `eZUserFunctionCollection::canOpen()` auf (`kernel/user/ezuserfunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `uri` | string | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'user', 'can_open', hash( 'uri', 'uri' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'user', 'can_open', array( 'uri' => 'uri' ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/class/templates.tpl` Zeile 26:

```tpl
<td>{if fetch( 'user', 'can_open', hash( 'uri', 'visual/templateedit' ) )}<a class="a4-btn a4-btn-small" href={concat( '/visual/templateedit/', $override.target )|ezurl} title="{'Edit the override template for the <%override_name> override.'|i18n( 'design/admin/class/view',, hash( '%override_name', $override.block ) )|wash}">{'Edit'|i18n( 'design/admin/class/view' )}</a>{/if}</td>
```

## Siehe auch

- [Alle Fetch-Funktionen von user](README.md)
- [Die Ansichten des Moduls user](../../modulansichten/user/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/function_definition.php`, `kernel/user/ezuserfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
