---
title: Preise und Währungen
audience: businesses, admin, website-creator
category: business-solutions
order: 40
summary: Wie ein Exponential-Shop Preise speichert, in einer oder mehreren Währungen verkauft, mit Wechselkursen umrechnet und Beträge rundet - und welche Entscheidungen ein Unternehmen dazu trifft.
keywords: Preis, Währung, mehrere Währungen, ezprice, ezmultiprice, Wechselkurse, EZB, Rundung, bevorzugte Währung, Währungsliste, price, currency, multi-currency, exchange rates, ECB, rounding, preferred currency, currency list
type: help
---
# Preise und Währungen

Diese Seite richtet sich an Geschäftsinhaber, die entscheiden, wie ihr Shop Preise anzeigt und in welchen Währungen.
Sie erklärt die zwei Preisfelder, wie Wechselkurse und Rundung funktionieren und was Sie entscheiden sollten, bevor das
erste Produkt erfasst wird. Die Einstellungen sind unter
[Produkte, Preise und USt](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md) beschrieben.

## Inhalt

1. [Zwei Arten von Preisen](#zwei-arten-von-preisen)
2. [Die Shop-Währung](#die-shop-währung)
3. [In mehreren Währungen verkaufen](#in-mehreren-währungen-verkaufen)
4. [Wechselkurse](#wechselkurse)
5. [Rundung](#rundung)
6. [Preise und USt](#preise-und-ust)
7. [Entscheidungen und Checkliste](#entscheidungen-und-checkliste)

## Zwei Arten von Preisen

| Feld | Was es enthält | Passt zu |
|---|---|---|
| `ezprice` | Einen Preis mit einem USt-Typ und der Wahl, ob der Preis die USt enthält oder nicht | Shops, die in einer Währung verkaufen |
| `ezmultiprice` | Einen Preis pro Währung | Shops, die Preise in mehreren Währungen zeigen |

Quelle: [Produkte, Preise und USt](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md#preis-datentypen). Das
Preisfeld wird pro Produktklasse gewählt. Ein späterer Wechsel bedeutet, jedes Produkt zu bearbeiten; entscheiden Sie
also vor der Massenerfassung.

## Die Shop-Währung

`settings/shop.ini` legt die Währung des Shops in `[CurrencySettings] PreferredCurrency` fest; der ausgelieferte Wert
ist `USD`. Wer Ihren Shop baut, setzt Ihre Währung in einer Override-Datei, nie in der ausgelieferten Datei (das sagt
der Kommentar am Anfang von `settings/shop.ini`).

Währungen werden in der Administration unter **Shop** verwaltet (`shop/currencylist`, das die Richtlinie `shop/setup`
braucht). Jede Währung hat einen Code, ein Symbol, ein Gebietsschema für die Formatierung und einen Status aktiv oder
inaktiv (`kernel/shop/classes/ezcurrencydata.php`).

## In mehreren Währungen verkaufen

Mit `ezmultiprice` kann ein Besucher seine Währung wählen (`shop/setpreferredcurrency`), und das Produkt zeigt den
Preis für diese Währung. Für jede Währung kann ein Produkt Folgendes tragen:

- einen **eigenen Preis**, den Sie von Hand setzen, für Märkte, in denen Sie einen festen, attraktiven Preis möchten;
  oder
- einen **automatischen Preis**, berechnet aus dem Grundpreis und dem Wechselkurs.

Die Währungsliste hat die Aktionen **Auto-Preis aktualisieren** und **Auto-Umrechnungsfaktoren aktualisieren**
(`kernel/private/classes/views/shop/currencylist.php`).

> **Tipp**
> Setzen Sie eigene Preise in den Währungen Ihrer Hauptmärkte und lassen Sie die anderen automatisch. Kunden in Ihren
> Hauptmärkten sehen dann runde Preise, und die übrigen sehen trotzdem ihre eigene Währung.

Die Wahl der Währung ist ein Teil des Verkaufs in andere Länder; Formate für Datumsangaben und Zahlen behandelt
[Währungen, Datumsangaben und Formate](../mehrsprachig/waehrungen-datumsangaben-und-formate.md).

## Wechselkurse

| Einstellung in `shop.ini` | Ausgelieferter Wert | Bedeutung |
|---|---|---|
| `[ExchangeRatesSettings] ExchangeRatesUpdateHandler` | `eZECB` | Die Kurse stammen von der Europäischen Zentralbank |
| `[ExchangeRatesSettings] BaseCurrency` | `EUR` | Die Währung, auf die sich die Kurse beziehen |
| `[ECBExchangeRatesSettings] RatesURI` | `stats/eurofxref/eurofxref-daily.xml` | Die Datei mit den täglichen Referenzkursen |

Jede Währung kann außerdem einen **eigenen Kurs** tragen, der den automatischen überschreibt, und einen
**Umrechnungsfaktor** (`custom_rate_value`, `rate_factor` in `kernel/shop/classes/ezcurrencydata.php`). Ein Unternehmen
kann mit dem Faktor einen Aufschlag für das Währungsrisiko einrechnen.

Eine andere Kursquelle lässt sich als Handler in einer Erweiterung einbinden; die Kommentare in `settings/shop.ini`
beschreiben die Klasse, die er implementieren muss (`eZExchangeRatesUpdateHandler`). Das ist Entwicklerarbeit.

> **Hinweis**
> Referenzkurse sind nicht die Kurse, die Ihr Zahlungsanbieter oder Ihre Bank verwendet. Klären Sie mit ihnen, wie eine
> Zahlung in einer anderen Währung abgerechnet wird, bevor Sie sich auf automatische Preise verlassen.

## Rundung

| Einstellung in `shop.ini` | Ausgeliefert | Bedeutung |
|---|---|---|
| `[MathSettings] RoundingType` | `round` | `none`, `round`, `ceil` oder `floor` |
| `[MathSettings] RoundingPrecision` | `2` | Stellen, die nach dem Dezimaltrennzeichen erhalten bleiben |
| `[MathSettings] RoundingTarget` | `false` | Auf eine Zielendung runden |

Der Kommentar in `settings/shop.ini` gibt ein Beispiel: 89,468543, mit Genauigkeit 2 gerundet, ergibt 89,47; mit dem
Ziel 5 wird daraus 89,45 und mit dem Ziel 99 wird daraus 89,99. Mit Rundungszielen erzeugt ein Shop Preisendungen wie
,99 für umgerechnete Preise.

`[MathSettings] MathHandler` wählt zwischen Gleitkommaarithmetik von PHP (`eZPHPMath`, ausgeliefert) und der
Erweiterung bcmath (`eZBCMath`).

## Preise und USt

Bei `ezprice` gibt jeder Preis an, ob er die USt enthält und welcher USt-Typ gilt. Ein Shop, der an Verbraucher
verkauft, erfasst Preise meist einschließlich USt; ein Shop, der an Unternehmen verkauft, erfasst sie oft ohne. Die
Bestätigungsseite zeigt die USt, und die Bestellliste zeigt Summen sowohl mit als auch ohne USt
([Sortierung der Bestellliste](../../../funktionen/6.0/sortierung-der-bestellliste.md)). Die steuerliche Seite steht unter
[USt-Regeln und USt-Typen](ust-regeln-und-ust-typen.md).

## Entscheidungen und Checkliste

- [ ] Eine Währung (`ezprice`) oder mehrere (`ezmultiprice`)?
- [ ] Die Shop-Währung, gesetzt in einem Override von `[CurrencySettings] PreferredCurrency`.
- [ ] Welche Währungen aktiv sind und welche eigene Preise bekommen.
- [ ] Kursquelle: die ausgelieferten EZB-Kurse, eigene Kurse oder ein eigener Handler.
- [ ] Rundungsart, Genauigkeit und Zielendungen.
- [ ] Preise mit oder ohne USt erfasst.
- [ ] Ihr Zahlungsanbieter akzeptiert jede Währung, die Sie anzeigen.

## Verwandte Seiten

- [Produkte, Preise und USt](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md)
- [USt-Regeln und USt-Typen](ust-regeln-und-ust-typen.md)
- [Zahlungsanbieter für Unternehmen](zahlungsanbieter-fuer-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
