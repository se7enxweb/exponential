---
title: Richtlinien des Moduls shop
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls shop (5) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, shop, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls shop

Die Richtlinienfunktionen von `shop` und ihre Einschränkungen, aus `$FunctionList` in `kernel/shop/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `shop/*` erlaubt jede Funktion.

## administrate

Geprüft von den Ansichten: [archivelist](../modulansichten/shop/archivelist.md), [archiveorder](../modulansichten/shop/archiveorder.md), [customerlist](../modulansichten/shop/customerlist.md), [customerorderview](../modulansichten/shop/customerorderview.md), [dashboard](../modulansichten/shop/dashboard.md), [orderlist](../modulansichten/shop/orderlist.md), [productsoverview](../modulansichten/shop/productsoverview.md), [removeorder](../modulansichten/shop/removeorder.md), [statistics](../modulansichten/shop/statistics.md), [unarchiveorder](../modulansichten/shop/unarchiveorder.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'shop', 'administrate' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'shop', 'function', 'administrate' ) )}
    ...
{/if}
```

## buy

Geprüft von den Ansichten: [add](../modulansichten/shop/add.md), [basket](../modulansichten/shop/basket.md), [checkout](../modulansichten/shop/checkout.md), [confirmorder](../modulansichten/shop/confirmorder.md), [orderview](../modulansichten/shop/orderview.md), [preferredcurrency](../modulansichten/shop/preferredcurrency.md), [register](../modulansichten/shop/register.md), [setpreferredcurrency](../modulansichten/shop/setpreferredcurrency.md), [setusercountry](../modulansichten/shop/setusercountry.md), [updatebasket](../modulansichten/shop/updatebasket.md), [userregister](../modulansichten/shop/userregister.md), [wishlist](../modulansichten/shop/wishlist.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'shop', 'buy' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'shop', 'function', 'buy' ) )}
    ...
{/if}
```

## edit_status

Geprüft von den Ansichten: [status](../modulansichten/shop/status.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'shop', 'edit_status' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'shop', 'function', 'edit_status' ) )}
    ...
{/if}
```

## setstatus

Geprüft von den Ansichten: [setstatus](../modulansichten/shop/setstatus.md).

| Einschränkung | Werte stammen aus |
|---|---|
| FromStatus | `eZOrderStatus::fetchPolicyList()` |
| ToStatus | `eZOrderStatus::fetchPolicyList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'shop', 'setstatus', array( 'FromStatus' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'shop', 'function', 'setstatus' ) )}
    ...
{/if}
```

## setup

Geprüft von den Ansichten: [currencylist](../modulansichten/shop/currencylist.md), [discountgroup](../modulansichten/shop/discountgroup.md), [discountgroupedit](../modulansichten/shop/discountgroupedit.md), [discountgroupview](../modulansichten/shop/discountgroupview.md), [discountruleedit](../modulansichten/shop/discountruleedit.md), [editcurrency](../modulansichten/shop/editcurrency.md), [editvatrule](../modulansichten/shop/editvatrule.md), [productcategories](../modulansichten/shop/productcategories.md), [vatrules](../modulansichten/shop/vatrules.md), [vattype](../modulansichten/shop/vattype.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'shop', 'setup' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'shop', 'function', 'setup' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von shop](../modulansichten/shop/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
