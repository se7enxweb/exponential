---
title: Preise und Währungen
audience: customer, admin, businesses
category: customer-success
order: 40
summary: Preise, Währungen und Wechselkurse in einem laufenden Exponential-Shop ändern - wer es darf, wie Sie eine Preisänderung planen, automatische Preise und Kurse aktuell halten und die Prüfungen nach jeder Änderung.
keywords: Preisänderung, Preise, Währungen, Wechselkurse, automatische Preise, eigene Preise, Rundung, bevorzugte Währung, Währungsliste, mehrere Währungen, price change, prices, currencies, exchange rates, automatic prices, custom prices, rounding, preferred currency, multi-currency
type: help
---
# Preise und Währungen

Diese Seite ist für Eigentümer und Shop-Leitungen, die Preise ändern oder in mehreren Währungen verkaufen. Sie behandelt
den laufenden Shop: eine Preisänderung planen, Wechselkurse und automatische Preise aktuell halten und das Ergebnis
prüfen. Wie Preise und Währungen funktionieren und was vor dem Start zu entscheiden ist, steht unter
[Preise und Währungen für Unternehmen](../../../../en/us/help/businesses/stores/prices-and-currencies.md).

## Wo Preise gespeichert sind

Ein Preis ist ein Feld des Produkts, eine Preisänderung ist also eine Inhaltsbearbeitung dieses Produkts. Die
Währungseinstellungen sind davon getrennt: Die Währungsliste (**Shop**, `shop/currencylist`) erfordert die Richtlinie
`shop/setup` (`kernel/shop/module.php`).

| Was sich ändert | Wer es üblicherweise tut | Wo |
|---|---|---|
| Der Preis eines Produkts | Produktredaktion oder Eigentümer | Das Bearbeitungsformular des Produkts ([Inhalte anlegen und bearbeiten](../../../../en/us/help/editing-content.md)) |
| Viele Preise auf einmal | Partner oder Administrator, oder die Redaktion einzeln | In den Shop-Ansichten gibt es keinen Editor für Massenänderungen von Preisen |
| Welche Währungen aktiv sind | Eigentümer | `shop/currencylist`, `shop/editcurrency` |
| Wechselkurse und automatische Preise | Eigentümer oder Administrator | Die Aktualisierungs-Schaltflächen der Währungsliste |
| Die Shop-Währung, Rundung | Partner, in einem Override von `settings/shop.ini` | `[CurrencySettings]`, `[MathSettings]` |

## Eine Preisänderung planen

1. **Listen** Sie die Produkte auf, die sich ändern, mit altem und neuem Preis. **Shop > Produktübersicht**
   (`shop/productsoverview`) zeigt die Produkte einer Klasse mit ihren Preisen und hilft beim Erstellen der Liste.
2. **Legen Sie den Zeitpunkt fest.** Preise ändern sich in dem Moment, in dem das bearbeitete Produkt veröffentlicht
   wird. Für eine Änderung um Mitternacht verwenden Sie die zeitgesteuerte Veröffentlichung, wenn Ihre Website sie
   hat ([Workflows](../../../../en/us/guides/workflows.md)).
3. **Bearbeiten** Sie die Produkte. Mit Entwürfen können mehrere Personen die Änderungen vorbereiten, ohne dass Kunden
   sie sehen ([Veröffentlichen und Versionen](../../../../en/us/help/publishing-and-versions.md)).
4. **Prüfen** Sie das Ergebnis (siehe unten).
5. **Informieren** Sie das Team und, falls nötig, Ihre Kunden.

> **Hinweis**
> Eine Preisänderung ändert keine bereits aufgegebenen Bestellungen: Jede Bestellung behält ihre eigenen Positionen
> und Summen.

## Mehrere Währungen

Mit `ezmultiprice` trägt jedes Produkt einen Preis pro Währung, entweder einen **eigenen Preis**, der von Hand gesetzt
wird, oder einen **automatischen Preis**, der aus dem Grundpreis und dem Wechselkurs berechnet wird
([Preise und Währungen für Unternehmen](../../../../en/us/help/businesses/stores/prices-and-currencies.md)).

| Einstellung in `settings/shop.ini` | Ausgelieferter Wert | Was er für Sie bedeutet |
|---|---|---|
| `[CurrencySettings] PreferredCurrency` | `USD` | Die Währung des Shops; Ihr Entwickler setzt Ihre in einem Override |
| `[ExchangeRatesSettings] ExchangeRatesUpdateHandler` | `eZECB` | Die Kurse kommen von der Europäischen Zentralbank |
| `[ExchangeRatesSettings] BaseCurrency` | `EUR` | Die Währung, auf die sich die Kurse beziehen |
| `[MathSettings] RoundingType` | `round` | Wie umgerechnete Beträge gerundet werden |
| `[MathSettings] RoundingPrecision` | `2` | Nachkommastellen |

Die Währungsliste hat die Aktionen **Auto-Umrechnungsfaktoren aktualisieren** und **Auto-Preis aktualisieren**
(`kernel/private/classes/views/shop/currencylist.php`).

### Eine Routine für Shops mit mehreren Währungen

| Wie oft | Aufgabe |
|---|---|
| Wöchentlich, oder vor einer Kampagne | Auto-Umrechnungsfaktoren aktualisieren, dann Auto-Preise aktualisieren |
| Monatlich | Einige umgerechnete Preise mit dem vergleichen, was Ihr Zahlungsanbieter abrechnet |
| Wenn ein Markt wichtiger wird | Dieser Währung eigene Preise geben |
| Jährlich | Prüfen, dass jede aktive Währung von Ihrem Zahlungsanbieter akzeptiert wird |

> **Tipp**
> Referenzkurse sind nicht die Kurse Ihrer Bank. Eine Währung kann einen eigenen Kurs und einen Kursfaktor tragen
> (`kernel/shop/classes/ezcurrencydata.php`), den manche Shops nutzen, um einen Aufschlag für das Währungsrisiko
> einzurechnen. Entscheiden Sie das mit der Person, die Ihre Buchhaltung macht.

## Nach jeder Änderung

- [ ] Öffnen Sie zwei oder drei geänderte Produkte auf der öffentlichen Website: Der neue Preis wird angezeigt.
- [ ] Legen Sie eines in den Warenkorb: Warenkorb und Bestätigungsseite zeigen den neuen Preis und die richtige Mehrwertsteuer.
- [ ] In einer anderen Währung, wenn Sie in mehreren verkaufen: Der Preis ist der, den Sie erwarten.
- [ ] Das Shop-Dashboard meldet keine Produkte ohne Preis
  ([Shop-Dashboard und Bestellstatus](../../../../en/us/features/6.0/store-dashboard.md)).
- [ ] Kampagnenseiten und Newsletter, die Preise nennen, sind aktualisiert.

Wenn die öffentliche Website noch den alten Preis zeigt, kommt die Seite möglicherweise aus einem Cache. Fragen Sie
Ihren Administrator, bevor während geschäftiger Zeiten Caches geleert werden ([Caches](../../../../en/us/guides/caches.md)).

## Verwandte Seiten

- [Preise und Währungen für Unternehmen](../../../../en/us/help/businesses/stores/prices-and-currencies.md)
- [USt-Typen und USt-Regeln](ust-typen-und-ust-regeln.md)
- [Rabatte](rabatte.md)
- [Produkte, Preise und Mehrwertsteuer](../../../../en/us/help/creators/website-creators/products-prices-and-vat.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
