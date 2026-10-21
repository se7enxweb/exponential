---
title: Richtlinien des Moduls package
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls package (9) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, package, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls package

Die Richtlinienfunktionen von `package` und ihre Einschränkungen, aus `$FunctionList` in `kernel/package/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `package/*` erlaubt jede Funktion.

## create

Geprüft von den Ansichten: [create](../modulansichten/package/create.md).

| Einschränkung | Werte stammen aus |
|---|---|
| CreatorType | `eZPackageCreationHandler::creatorLimitationList()` |
| Role | `eZPackage::maintainerRoleListForRoles()` |
| Type | `eZPackage::typeList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'create', array( 'CreatorType' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'create' ) )}
    ...
{/if}
```

## edit

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'package', 'edit' )` oder der Fetch `user/has_access_to`).

| Einschränkung | Werte stammen aus |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'edit', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'edit' ) )}
    ...
{/if}
```

## export

Geprüft von den Ansichten: [export](../modulansichten/package/export.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'export', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'export' ) )}
    ...
{/if}
```

## export_extension

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'package', 'export_extension' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'export_extension' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'export_extension' ) )}
    ...
{/if}
```

## import

Geprüft von den Ansichten: [upload](../modulansichten/package/upload.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'import', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'import' ) )}
    ...
{/if}
```

## install

Geprüft von den Ansichten: [install](../modulansichten/package/install.md), [uninstall](../modulansichten/package/uninstall.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'install', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'install' ) )}
    ...
{/if}
```

## list

Geprüft von den Ansichten: [list](../modulansichten/package/list.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'list', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'list' ) )}
    ...
{/if}
```

## read

Geprüft von den Ansichten: [compare](../modulansichten/package/compare.md), [view](../modulansichten/package/view.md), [viewfile](../modulansichten/package/viewfile.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'read', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'read' ) )}
    ...
{/if}
```

## remove

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'package', 'remove' )` oder der Fetch `user/has_access_to`).

| Einschränkung | Werte stammen aus |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'remove', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'remove' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von package](../modulansichten/package/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
