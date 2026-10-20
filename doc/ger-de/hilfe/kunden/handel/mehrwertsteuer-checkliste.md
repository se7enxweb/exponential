---
title: Mehrwertsteuer-Checkliste
audience: customer, admin, businesses
category: customer-success
order: 60
summary: Eine Checkliste, um die Mehrwertsteuer-Konfiguration eines Exponential-Shops einmal im Quartal, vor einer Satzänderung und zum Jahresende zu überprüfen - USt-Typen, USt-Regeln, Produktkategorien, Testbestellungen und die Unterlagen, die Ihre Steuerberatung braucht. Vorlage, keine Steuerberatung.
keywords: Mehrwertsteuer-Checkliste, Mehrwertsteuer-Prüfung, vierteljährliche Prüfung, USt-Typen, USt-Regeln, Steuerberater, Testbestellung, Jahresende, keine Steuerberatung, VAT checklist, VAT review, quarterly VAT check, VAT types, VAT rules, tax adviser, test order, year end
type: help
---
# Mehrwertsteuer-Checkliste

Diese Seite ist eine Vorlage für eine Checkliste für den Eigentümer und die Buchhaltung. Kopieren Sie sie, füllen Sie
sie jedes Mal aus, und bewahren Sie die ausgefüllten Kopien bei Ihren Mehrwertsteuer-Unterlagen auf. Die Erklärungen
stehen unter [USt-Typen und USt-Regeln](ust-typen-und-ust-regeln.md).

> **Warnung**
> Vorlage, keine Steuerberatung. Die Checkliste hilft Ihnen zu prüfen, dass der Shop das berechnet, was Sie
> beschlossen haben. Was Sie berechnen müssen, bestätigt Ihr Steuerberater.

## Wann Sie sie verwenden

| Anlass | Zu erledigende Abschnitte |
|---|---|
| Jedes Quartal | 1 bis 4 |
| Bevor eine Satzänderung wirksam wird | 1, 2, 4 |
| Wenn Sie in ein neues Land oder eine neue Produktgruppe verkaufen | 2, 3, 4 |
| Zum Jahresende | Alle |
| Nach einem Update von Exponential oder einer Änderung durch Ihren Partner | 4 |

## Die Checkliste

```text
VAT CHECKLIST - [store name]      Date: __________   Done by: __________

1. VAT TYPES  (Store > VAT types, shop/vattype)
[ ] Every VAT type is named after its meaning and percentage
[ ] Every percentage matches what the adviser confirmed (list below)
[ ] No VAT type is unused and confusing; any 0 percent type is intended
[ ] The Store dashboard's "Next steps" shows no unexpected 0 percent VAT type

   Name                      | Percentage | Confirmed by adviser on
   --------------------------|------------|------------------------
                             |            |
                             |            |

2. VAT RULES  (Store > VAT rules, shop/vatrules)
[ ] A general rule exists for "any country, any category"
[ ] Each country-specific rule is still wanted and confirmed
[ ] Each category-specific rule names an existing product category
[ ] No two rules say different things for the same country and category

3. PRODUCTS AND CATEGORIES
[ ] Every price uses a fixed VAT type or the dynamic type, consistently per class
[ ] With rules: every product has its product category set
[ ] New products since the last check have the right VAT type or category
[ ] Shipping cost and its VAT treatment confirmed with the adviser

4. TEST ORDERS
[ ] Test order 1: country ______, category ______ -> VAT shown ______  expected ______
[ ] Test order 2: country ______, category ______ -> VAT shown ______  expected ______
[ ] Totals with and without VAT in the order list match the confirmation page
[ ] Test orders handled as agreed (canceled or removed), so they are not counted

5. RECORDS (year end)
[ ] Month figures from Store > Product statistics saved for each month
[ ] Changes to VAT types and rules this year listed (audit trail, commerce channel)
[ ] Questions for the adviser written down

Signed off: __________________   Next check due: __________
```

## Wo jeder Punkt geprüft wird

| Punkt | Wo | Quelle |
|---|---|---|
| USt-Typen | **Shop > USt-Typen** | `shop/vattype`, Richtlinie `shop/setup` (`kernel/shop/module.php`) |
| USt-Regeln und Kategorien | **Shop > USt-Regeln**, **Shop > Produktkategorien** | `shop/vatrules`, `shop/productcategories` |
| Warnung bei 0 Prozent | Shop-Dashboard, „Nächste Schritte“ | [Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md) |
| Name des dynamischen USt-Typs | `settings/shop.ini [VATSettings] DynamicVatTypeName` | ausgeliefert „Determined by VAT charging rules“ |
| Summen mit und ohne Mehrwertsteuer | **Shop > Bestellungen**, sortierbare Spalten | [Sortierung der Bestellliste](../../../../eng-us/features/6.0/order-list-sorting.md) |
| Monatszahlen | **Shop > Produkt-Statistiken** (`shop/statistics`) | [Shop-Berichte](shop-berichte.md) |
| Nachweis der Änderungen | Das Prüfprotokoll, Kanal `commerce` | [Das Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md) |

## Wenn etwas nicht stimmt

- Ein falscher Prozentsatz: Korrigieren Sie den USt-Typ sofort und teilen Sie Ihrem Steuerberater mit, welche
  Bestellungen mit dem falschen Satz aufgegeben wurden. Bestellungen behalten die Mehrwertsteuer, mit der sie
  aufgegeben wurden; eine Bestellung zu korrigieren ist eine Frage der Buchhaltung, keine Einstellung des Shops.
- Eine fehlende Regel: Fügen Sie sie hinzu und geben Sie eine Testbestellung auf.
- Ein Produkt ohne Kategorie: Setzen Sie sie und prüfen Sie den Preis des Produkts auf der Bestätigungsseite.

## Verwandte Seiten

- [USt-Typen und USt-Regeln](ust-typen-und-ust-regeln.md)
- [USt-Regeln und USt-Typen für Unternehmen](../../../../eng-us/help/businesses/stores/vat-rules-and-vat-types.md)
- [Monatliche Shop-Checkliste](monatliche-shop-checkliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
