---
title: Währungen, Datumsangaben und Formate
audience: businesses, sales-marketing, website-creator
category: business-solutions
order: 40
summary: Wie Exponential Datumsangaben, Zahlen und Beträge so darstellt, wie jeder Markt es erwartet, und was der eingebaute Shop mit Währungen, Wechselkursen, Rundung, Mehrwertsteuer je Land und Länderlisten macht – mit den Fragen, die für jeden Markt zu klären sind.
keywords: Währung, Währungen, Datumsangaben, Datumsformat, Zahlen, Locale, Wechselkurse, Rundung, Mehrwertsteuer, USt, Land, internationaler Shop, Unternehmen, currency, currencies, dates, date format, numbers, locale, ezmultiprice, exchange rates, PreferredCurrency, rounding, VAT, country, international store, business
type: help
---
# Währungen, Datumsangaben und Formate

Diese Seite richtet sich an Inhaber und Führungskräfte von Shops und Organisationen, die Kunden in mehreren Ländern
ansprechen. Ein deutscher Kunde erwartet `24.12.2026`, ein amerikanischer `12/24/2026`; ein japanischer Preis hat
keine Nachkommastellen. Diese Seite erklärt, um welche Formate sich Exponential über die Locale jeder Website kümmert,
was der eingebaute Shop mit mehreren Währungen macht und welche Fragen für Ihren Steuerberater und Ihren
Zahlungsanbieter bleiben.

## Inhalt

1. [Formate folgen der Locale der Website](#formate-folgen-der-locale-der-website)
2. [Beispiele aus den mitgelieferten Locales](#beispiele-aus-den-mitgelieferten-locales)
3. [Preise in mehreren Währungen](#preise-in-mehreren-währungen)
4. [Mehrwertsteuer und das Land des Kunden](#mehrwertsteuer-und-das-land-des-kunden)
5. [Länderlisten](#länderlisten)
6. [Fragen, die für jeden Markt zu klären sind](#fragen-die-für-jeden-markt-zu-klären-sind)

## Formate folgen der Locale der Website

Jede Locale-Datei nennt unter anderem die Datums- und Zeitformate, die Tages- und Monatsnamen, den ersten Tag der Woche
sowie die Zahlen- und Währungsformate ihres Landes
([Sprachen, Abschnitt 1](../../../../eng-us/languages.md#1-locales-translations-content-languages-and-siteaccesses)). Eine
Website-Adresse formatiert mit der Locale, auf die sie eingestellt ist (`site.ini [RegionalSettings] Locale`), sodass
ein deutscher Siteaccess deutsche und ein US-Siteaccess amerikanische Datumsangaben zeigt, ganz ohne Änderung am
Template.

Die Gruppen einer Locale-Datei im Überblick
([Sprachen, Abschnitt 7](../../../../eng-us/languages.md#7-adding-a-locale)):

| Gruppe | Was sie festlegt |
|---|---|
| `[Currency]` | Symbol, Name und ISO-4217-Code der Landeswährung; Dezimal- und Tausendertrennzeichen; wie viele Nachkommastellen (keine bei Währungen ohne Untereinheit wie JPY, KRW, VND, IRR); wo Symbol und Vorzeichen stehen |
| `[Numbers]` | Zahlenformat |
| `[DateTime]` | Lange und kurze Formate für Datum und Uhrzeit; ob die Woche am Montag beginnt |
| Monats- und Tagesnamen | In der Form, die innerhalb eines Datums verwendet wird |
| `[CountryNames]` | Ländernamen in der Sprache |

Templates formatieren Werte über die Locale-Operatoren wie `l10n` und `datetime`
([Locale-Operatoren](../../../../eng-us/help/template-operators/locale-operators.md)).

## Beispiele aus den mitgelieferten Locales

Werte aus den Dateien in `share/locale/`:

| Locale | Kurzes Datumsmuster | Währungscode und Symbol | Nachkommastellen | Woche beginnt am Montag |
|---|---|---|---|---|
| `eng-US` | `%m/%d/%Y` (Monat zuerst) | USD, `$` | 2 | nein |
| `ger-DE` | `%d.%m.%Y` | EUR, `€` | 2 | ja |
| `jpn-JP` | `%Y.%m.%d` | `円` | 0 | nein |

> **Hinweis**
> Die Währung in einer Locale ist die Währung des **Landes**. Sie legt fest, wie Beträge geschrieben werden, nicht,
> welche Währung Ihr Shop berechnet. Die Währungen des Shops werden getrennt eingerichtet, siehe unten.

## Preise in mehreren Währungen

Aus [Produkte, Preise und Mehrwertsteuer](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md#currencies-and-rounding):

| Was | Wie |
|---|---|
| Ein Preis je Produkt | Der Datentyp `ezprice` |
| Ein Preis je Währung | Der Datentyp `ezmultiprice` |
| Die Währung des Shops | `shop.ini [CurrencySettings] PreferredCurrency` (ausgeliefert: `USD`) |
| Wechselkurse für `ezmultiprice` | `[ExchangeRatesSettings]`: Basiswährung `EUR` und der Aktualisierungs-Handler `eZECB` wie ausgeliefert |
| Rundung | `[MathSettings]`: `round`, Genauigkeit `2` wie ausgeliefert |
| Währungen verwalten | `shop/currencylist` |
| Besucher wählen lassen | `shop/setpreferredcurrency` |

Was das für ein Unternehmen bedeutet:

- **Feste Preise je Markt** (zum Beispiel ein „psychologischer“ Preis von 19,90 in jeder Währung): Verwenden Sie einen
  Preis je Währung, den Ihr Team einträgt.
- **Umgerechnete Preise**: Lassen Sie Wechselkurse die Preise aus einer Basiswährung berechnen, und nehmen Sie in Kauf,
  dass sich das Ergebnis mit dem Kurs ändert.
- **Rundung** wird einmal für den Shop eingerichtet; prüfen Sie vor dem Start ihre Wirkung auf Ihre Preispunkte.

Was auch immer der Shop anzeigt: Welche Währung tatsächlich berechnet wird, legen Sie gemeinsam mit Ihrem
Zahlungsanbieter fest; siehe [Zahlungsanbieter für Unternehmen](../shops/zahlungsanbieter-fuer-unternehmen.md) und
[Preise und Währungen](../shops/preise-und-waehrungen.md).

## Mehrwertsteuer und das Land des Kunden

Die Mehrwertsteuerregeln des Shops legen fest, welcher USt-Typ je Kundenland und Produktkategorie gilt; benannte
USt-Typen (Normalsatz, ermäßigter Satz, Nullsatz) werden unter **Shop > USt-Typen** verwaltet
([Mehrwertsteuer](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md#vat)). Dieselbe Seite sagt es deutlich:
„Das Mehrwertsteuerrecht ist von Land zu Land verschieden. Der Shop berechnet, was Sie einrichten; ob das für Ihr
Unternehmen richtig ist, ist eine Frage für Ihren Steuerberater.“ Der [Bereich zu Shops](../shops/ust-regeln-und-ust-typen.md)
erklärt es für Unternehmen.

## Länderlisten

Jede Länderauswahl zeigt zwei Gruppen: eine Rangliste der meistgenutzten Länder, dann alle Länder von A bis Z nach ihrem
übersetzten Namen ([Die gruppierte Länderliste](../../../../eng-us/features/6.0/country-groups.md)). Sie wird von Länderfeldern,
dem Kontoformular des Shops und den Mehrwertsteuerregeln verwendet. Prüfen Sie bei internationalen Shops die
Rangliste, damit Ihre wichtigsten Märkte oben stehen; die [Seite zu Ländergruppen für Unternehmen](../shops/laendergruppen-und-regionen.md)
erklärt, wie.

## Fragen, die für jeden Markt zu klären sind

- [ ] Welche Locale verwendet der Siteaccess des Marktes (Sprache und Land)?
- [ ] Sehen Datumsangaben, Zahlen und Preise auf einer Produktseite, im Warenkorb und in der Bestellbestätigung per
      Mail richtig aus?
- [ ] Welche Währung wird berechnet, und unterstützt der Zahlungsanbieter sie?
- [ ] Feste Preise je Währung oder umgerechnete Preise? Wer aktualisiert sie?
- [ ] Welche Mehrwertsteuerregeln gelten, bestätigt von einem Steuerberater?
- [ ] Sind Versandkosten und Lieferländer für diesen Markt festgelegt ([Versand und Bearbeitung](../shops/versand-und-bearbeitung.md))?
- [ ] Sind AGB, Rückgaberegeln und rechtliche Hinweise für diesen Markt übersetzt und geprüft?

## Verwandte Seiten

- [Sprachen und Locales](sprachen-und-locales.md)
- [Checkliste Mehrsprachigkeit](checkliste-mehrsprachigkeit.md)
- [Online-Shops und kleine Geschäfte](../shops/README.md)
- [Produkte, Preise und Mehrwertsteuer](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md)
- [Sprachen in Exponential](../../../../eng-us/languages.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
