---
title: fetch( 'user', 'has_access_to' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion has_access_to des Moduls user: 3 Parameter, 2 erforderlich."
keywords: Fetch-Funktion, fetch, user, has_access_to, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'user', 'has_access_to' )

Definiert in `kernel/user/function_definition.php`; sie ruft `eZUserFunctionCollection::hasAccessTo()` auf (`kernel/user/ezuserfunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `module` | string | ja |  |
| `function` | string | ja |  |
| `user_id` | integer | nein |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'user', 'has_access_to', hash( 'module', 'module',
                                             'function', 'function' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'user', 'has_access_to', hash( 'module', 'module',
                                             'function', 'function',
                                             'user_id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'user', 'has_access_to', array( 'module' => 'module', 'function' => 'function' ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/content/edit.tpl` Zeile 64:

```tpl
{if and( ezini( 'NotificationSettings', 'PublishWithoutNotification', 'notification.ini' )|eq( 'enabled' ), fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'publish_without_notification' ) ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von user](README.md)
- [Die Ansichten des Moduls user](../../modulansichten/user/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/function_definition.php`, `kernel/user/ezuserfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
