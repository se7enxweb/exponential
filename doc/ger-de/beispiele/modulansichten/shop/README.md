---
title: Das Modul shop
audience: developer, admin
category: examples
order: 10
summary: "Die Ansichten des Moduls shop: Adressen, Parameter und Richtlinienfunktionen."
keywords: Beispiele, Referenz, Das Modul shop, examples, reference
type: generated
generated: exp:help:examples
---
# Das Modul shop

Name `eZShop`, definiert in `kernel/shop/module.php`. 36 Ansichten.

| Ansicht | Adresse | Richtlinienfunktionen |
|---|---|---|
| [add](add.md) | `/shop/add/<ObjectID>/<Quantity>` | buy |
| [archivelist](archivelist.md) | `/shop/archivelist` | administrate |
| [archiveorder](archiveorder.md) | `/shop/archiveorder` | administrate |
| [basket](basket.md) | `/shop/basket` | buy |
| [checkout](checkout.md) | `/shop/checkout` | buy |
| [confirmorder](confirmorder.md) | `/shop/confirmorder` | buy |
| [currencylist](currencylist.md) | `/shop/currencylist` | setup |
| [customerlist](customerlist.md) | `/shop/customerlist` | administrate |
| [customerorderview](customerorderview.md) | `/shop/customerorderview/<CustomerID>/<Email>` | administrate |
| [dashboard](dashboard.md) | `/shop/dashboard` | administrate |
| [discountgroup](discountgroup.md) | `/shop/discountgroup` | setup |
| [discountgroupedit](discountgroupedit.md) | `/shop/discountgroupedit/<DiscountGroupID>` | setup |
| [discountgroupview](discountgroupview.md) | `/shop/discountgroupview/<DiscountGroupID>` | setup |
| [discountruleedit](discountruleedit.md) | `/shop/discountruleedit/<DiscountGroupID>/<DiscountRuleID>` | setup |
| [editcurrency](editcurrency.md) | `/shop/editcurrency` | setup |
| [editvatrule](editvatrule.md) | `/shop/editvatrule/<ruleID>` | setup |
| [orderlist](orderlist.md) | `/shop/orderlist` | administrate |
| [orderreceipt](orderreceipt.md) | `/shop/orderreceipt/<Token>` | (Modul) |
| [orderreciept](orderreciept.md) | `/shop/orderreciept/<Token>` | (Modul) |
| [orderview](orderview.md) | `/shop/orderview/<OrderID>` | buy |
| [preferredcurrency](preferredcurrency.md) | `/shop/preferredcurrency` | buy |
| [productcategories](productcategories.md) | `/shop/productcategories` | setup |
| [productsoverview](productsoverview.md) | `/shop/productsoverview` | administrate |
| [register](register.md) | `/shop/register` | buy |
| [removeorder](removeorder.md) | `/shop/removeorder` | administrate |
| [setpreferredcurrency](setpreferredcurrency.md) | `/shop/setpreferredcurrency` | buy |
| [setstatus](setstatus.md) | `/shop/setstatus` | setstatus |
| [setusercountry](setusercountry.md) | `/shop/setusercountry` | buy |
| [statistics](statistics.md) | `/shop/statistics/<Year>/<Month>` | administrate |
| [status](status.md) | `/shop/status` | edit_status |
| [unarchiveorder](unarchiveorder.md) | `/shop/unarchiveorder` | administrate |
| [updatebasket](updatebasket.md) | `/shop/updatebasket` | buy |
| [userregister](userregister.md) | `/shop/userregister` | buy |
| [vatrules](vatrules.md) | `/shop/vatrules` | setup |
| [vattype](vattype.md) | `/shop/vattype` | setup |
| [wishlist](wishlist.md) | `/shop/wishlist` | buy |

- [Die Richtlinien dieses Moduls](../../richtlinien/shop.md)

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/module.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
