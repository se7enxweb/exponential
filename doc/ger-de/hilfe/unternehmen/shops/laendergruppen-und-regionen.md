---
title: Ländergruppen und Regionen
audience: businesses, admin, website-creator
category: business-solutions
order: 60
summary: Wie die Länderliste des Shops für Kunden funktioniert - die häufigsten Länder zuerst, automatisches Ausfüllen im Browser, Ranglisten pro Website - und was der Verkauf in mehrere Länder und Regionen mit sich bringt.
keywords: Länderliste, Ländergruppen, countrygroups.ini, Regionen, internationaler Verkauf, automatisches Ausfüllen, Bestellformular, Versandländer, Märkte, country list, country groups, regions, international selling, autofill, checkout form, shipping countries, markets
type: help
---
# Ländergruppen und Regionen

Diese Seite richtet sich an Geschäftsinhaber, die an Kunden in mehr als einem Land verkaufen. Sie erklärt die
Länderliste, die Ihre Kunden an der Kasse sehen, wie Sie Ihre eigenen Märkte nach oben stellen und was sich sonst
ändert, wenn Sie über Grenzen hinweg verkaufen. Die technische Beschreibung steht unter
[Die gruppierte Länderliste](../../../funktionen/6.0/laendergruppen.md).

## Inhalt

1. [Warum die Länderliste wichtig ist](#warum-die-länderliste-wichtig-ist)
2. [Was Kunden sehen](#was-kunden-sehen)
3. [Ihre Märkte zuerst](#ihre-märkte-zuerst)
4. [Automatisches Ausfüllen und weniger Fehler](#automatisches-ausfüllen-und-weniger-fehler)
5. [In mehrere Länder verkaufen](#in-mehrere-länder-verkaufen)
6. [Einschränken, wohin Sie verkaufen](#einschränken-wohin-sie-verkaufen)
7. [Checkliste](#checkliste)

## Warum die Länderliste wichtig ist

Das Land, das ein Kunde wählt, kann den USt-Satz bestimmen ([USt-Regeln und USt-Typen](ust-regeln-und-ust-typen.md))
und die Anschrift auf der Bestellung. Eine lange Liste von A bis Z bremst Kunden aus, und eine falsche Auswahl erzeugt
eine Bestellung, die Sie von Hand korrigieren müssen. Eine kurze Rangliste oben macht den häufigen Fall zu einem Klick.

## Was Kunden sehen

Jede Länderauswahl zeigt zwei Gruppen ([Die gruppierte Länderliste](../../../funktionen/6.0/laendergruppen.md)):

1. **Häufig gewählte Länder**, eine Rangliste.
2. **Alle Länder**, jedes Land aus `settings/country.ini`, von A bis Z sortiert nach dem übersetzten Namen in der
   Sprache des Lesers.

Das Kontoformular des Shops (`shop/userregister`) verwendet diese Liste in den Designs standard, admin, ezwebin, ezdemo
und media, ebenso der Bildschirm der USt-Regeln und das Länderfeld von Inhalten und Formularen. Die Gruppen sind native
Gruppen des Browsers, sie funktionieren also mit der Tastatur, mit Screenreadern und mit der eigenen Auswahl jedes
Browsers.

Die ausgelieferte Rangliste umfasst 37 Länder in der Reihenfolge Nordamerika, Südamerika, Europa, Vereinigtes
Königreich, Asien, Neuseeland und Australien sowie Afrika, mit den Vereinigten Staaten, Kanada und Mexiko an der
Spitze (dieselbe Seite, „The shipped ranking“).

## Ihre Märkte zuerst

Die Rangliste ist eine Einstellung, `settings/countrygroups.ini`, die sich für die ganze Installation oder pro
Siteaccess überschreiben lässt. Ein Shop, der überwiegend in deutschsprachige Länder verkauft, kann Deutschland,
Österreich und die Schweiz zuerst aufführen; die Funktionsseite zeigt genau dieses Beispiel. Außerdem können Sie:

- weitere Gruppen vor „Alle Länder“ einfügen, jede mit ihrer eigenen Bezeichnung (`GroupList[]`, `GroupName_<group>`);
- festlegen, ob die Länder der Rangliste in der Liste von A bis Z wiederholt werden (`RepeatInAllCountries`);
- jeder Website einer Multisite-Installation über Siteaccess-Einstellungen ihre eigene Rangliste geben
  ([Multisite einrichten](../../ersteller/website-ersteller/multisite-einrichten.md)).

> **Tipp**
> Führen Sie die Länder auf, in die Sie tatsächlich liefern, in der Reihenfolge Ihres Bestellvolumens. Sehen Sie im
> Shop-Dashboard nach, aus welchen Ländern Bestellungen kamen („So funktioniert Ihr Shop“ listet sie auf), und
> aktualisieren Sie die Rangliste einmal im Jahr.

## Automatisches Ausfüllen und weniger Fehler

Browser füllen Anschriften aus dem Profil des Kunden aus. Die gruppierte Liste wurde gebaut, damit das funktioniert
([Die gruppierte Länderliste](../../../funktionen/6.0/laendergruppen.md#automatisches-ausfüllen)):

- ein Kunde mit einer Anschrift in den Vereinigten Staaten landet nicht mehr bei „United States Minor Outlying Islands“;
- die Auswahl des Shops beginnt mit einer leeren Option „Land wählen“, statt stillschweigend ein Land vorzuwählen;
- Ländernamen werden nicht mehr gekürzt, sodass das automatische Ausfüllen sie findet;
- das Kontoformular des Designs media benennt alle Adressfelder, sodass ein Browser die ganze Anschrift ausfüllen kann.

Weniger falsche Länder bedeuten weniger falsche USt-Beträge und weniger zurückgeschickte Pakete.

## In mehrere Länder verkaufen

Das Land ist eine Entscheidung unter mehreren. Ein Shop, der ins Ausland verkauft, entscheidet meist außerdem über:

| Thema | Lesen Sie |
|---|---|
| USt pro Land | [USt-Regeln und USt-Typen](ust-regeln-und-ust-typen.md) |
| Preise in anderen Währungen | [Preise und Währungen](preise-und-waehrungen.md) |
| Versandkosten pro Zone | [Versand und Bearbeitung](versand-und-bearbeitung.md) |
| Shop-Seiten in anderen Sprachen | [Mehrsprachigkeit planen](../mehrsprachig/mehrsprachigkeit-planen.md) |
| Getrennte Websites pro Land | [Mehrere Marken planen](../multisite/mehrere-marken-planen.md) |
| Sprachen mit Schrift von rechts nach links | [Märkte mit Schrift von rechts nach links](../mehrsprachig/maerkte-mit-rechts-nach-links-schrift.md) |

## Einschränken, wohin Sie verkaufen

Die Länderliste selbst führt jedes Land aus `country.ini` auf; die Rangliste ändert nur die Reihenfolge. Ein Shop, der
nur in einige Länder liefert, hat diese Möglichkeiten:

- es deutlich auf den Produktseiten, im Warenkorb und in den Geschäftsbedingungen sagen;
- das Land im Bestätigungsschritt mit einem Workflow-Ereignis an `before_shop_confirmorder` prüfen
  ([Warenkorb, Kasse und Bestellungen](../../ersteller/website-ersteller/warenkorb-kasse-und-bestellungen.md#workflow-trigger));
- einen eigenen Shop-Account-Handler verwenden, dessen Formular nur Ihre Länder anbietet
  (`shopaccount.ini [AccountSettings]`, [Kundenkonten und Gastbestellung](kundenkonten-und-gastbestellung.md)).

Die zweite und die dritte Möglichkeit sind Entwicklerarbeit. Der ausgelieferte Shop hat keinen Schalter „nur in diese
Länder liefern“.

## Checkliste

- [ ] Die Ranglistengruppe führt Ihre Märkte in der Reihenfolge des Volumens auf.
- [ ] Jede Website einer Multisite-Installation hat die Rangliste, die zu ihrem Markt passt.
- [ ] Ein Test mit dem automatischen Ausfüllen des Browsers wählt das richtige Land.
- [ ] Für jedes Land mit abweichendem Satz gibt es USt-Regeln.
- [ ] Kunden sehen, wohin Sie liefern, bevor sie die Kasse erreichen.

## Verwandte Seiten

- [Die gruppierte Länderliste](../../../funktionen/6.0/laendergruppen.md)
- [USt-Regeln und USt-Typen](ust-regeln-und-ust-typen.md)
- [Versand und Bearbeitung](versand-und-bearbeitung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
