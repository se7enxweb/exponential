---
title: Fetch-Funktionen von shop
audience: developer, admin
category: examples
order: 10
summary: Die Fetch-Funktionen des Moduls shop mit ihren Parametern.
keywords: Beispiele, Referenz, Fetch-Funktionen von shop, examples, reference
type: generated
generated: exp:help:examples
---
# Fetch-Funktionen des Moduls shop

Definiert in `kernel/shop/function_definition.php`. Ein mit * markierter Parameter ist erforderlich.

| Funktion | Parameter |
|---|---|
| [basket](basket.md) |  |
| [best_sell_list](best_sell_list.md) | `top_parent_node_id`*, `limit`, `offset`, `start_time`, `end_time`, `duration`, `ascending`, `extended` |
| [currency](currency.md) | `code`* |
| [currency_list](currency_list.md) | `status` |
| [current_wish_list](current_wish_list.md) |  |
| [order](order.md) | `order_id`* |
| [order_status_history](order_status_history.md) | `order_id`* |
| [order_status_history_count](order_status_history_count.md) | `order_id`* |
| [preferred_currency_code](preferred_currency_code.md) |  |
| [product_category](product_category.md) | `category_id`* |
| [product_category_list](product_category_list.md) |  |
| [related_purchase](related_purchase.md) | `contentobject_id`*, `limit`* |
| [user_country](user_country.md) |  |
| [wish_list](wish_list.md) | `production_id`*, `offset`, `limit` |
| [wish_list_count](wish_list_count.md) | `production_id`* |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/function_definition.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
