---
title: Shop-Dashboard und Berichte
audience: businesses, admin, project-manager
category: business-solutions
order: 120
summary: Was das Shop-Dashboard einem Geschäftsinhaber sagt - Verkäufe, Kunden, offene Bestellungen, abgebrochene Warenkörbe, Bestseller und Hinweise zur Einrichtung - sowie die Bestellstatistik und wie Sie sie in einer wöchentlichen Durchsicht nutzen.
keywords: Shop-Dashboard, Shop-Übersicht, Berichte, Umsatz, Verkaufszahlen, offene Bestellungen, abgebrochene Warenkörbe, Bestseller, Statistik, nächste Schritte, Kennzahlen, store dashboard, shop dashboard, reports, revenue, sales figures, open orders, abandoned baskets, best sellers, statistics, next steps, KPIs
type: help
---
# Shop-Dashboard und Berichte

Diese Seite richtet sich an Inhaber und Verantwortliche, die wissen möchten, wie es um ihren Shop steht, ohne Berichte
zu bauen. Sie erklärt, was das Shop-Dashboard zeigt, wie Sie es lesen und wie Sie es in einer kurzen wöchentlichen
Durchsicht nutzen. Die Referenz ist [Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md).

## Inhalt

1. [Das Dashboard öffnen](#das-dashboard-öffnen)
2. [Auf einen Blick](#auf-einen-blick)
3. [Diagramme und Ranglisten](#diagramme-und-ranglisten)
4. [Zu erledigen](#zu-erledigen)
5. [So funktioniert Ihr Shop](#so-funktioniert-ihr-shop)
6. [Nächste Schritte](#nächste-schritte)
7. [Bestellstatistik](#bestellstatistik)
8. [Eine wöchentliche Durchsicht](#eine-wöchentliche-durchsicht)
9. [Was das Dashboard nicht tut](#was-das-dashboard-nicht-tut)

## Das Dashboard öffnen

Melden Sie sich in der Administration an und klicken Sie auf **Shop** (der Tab hieß vor dem 30. September 2026
Webshop). Das Dashboard ist der erste Eintrag des Shop-Menüs; es braucht die Richtlinie `shop/administrate`, wie die
Bestellliste und die Statistik. Titel und Texte sind übersetzt, auch ins Deutsche.

## Auf einen Blick

| Kennzahl | Was sie Ihnen sagt |
|---|---|
| Bestellungen heute, in 7 und in 30 Tagen, mit dem Trend gegenüber den 30 Tagen davor | Ob das Geschäft wächst |
| Umsatz und durchschnittliche Bestellung, pro Währung | Was die Bestellungen wert sind |
| Kunden: neu, wiederkehrend, insgesamt | Ob Kunden wiederkommen |
| Offene Bestellungen und wie viele zu lange warten | Ob das Team hinterherkommt |
| Laufende und abgebrochene Warenkörbe | Wo Kunden aussteigen |
| Produkte und Produkte ohne Preis | Lücken im Katalog |

All das stammt von der Funktionsseite. Die Summen verwenden die Arithmetik der Bestellliste, sodass Dashboard und
Liste übereinstimmen. Fehlgeschlagene, stornierte und erstattete Bestellungen zählen als Bestellungen, aber nicht als
Umsatz ([Bestellungen und Bestellstatus](bestellungen-und-bestellstatus.md#was-abgeschlossen-und-kein-umsatz-bedeuten)).

## Diagramme und Ranglisten

- **Umsatz pro Tag**, um die Wirkung einer Kampagne oder eines Feiertags zu sehen.
- **Aufteilung nach Bestellstatus**, um zu sehen, wo sich Bestellungen stauen.
- **Meistverkaufte Produkte**, um zu entscheiden, was Sie bewerben und was Sie nachbestellen.

## Zu erledigen

- Bestellungen, die auf den Shop warten, die am längsten wartenden zuerst.
- Die neuesten Bestellungen, Warenkörbe und unbestätigten Bestellvorgänge.
- Die Produktklassen mit ihrem Preisattribut.

Eine Bestellung ist offen, bis ihr Status abgeschlossen ist, sodass stornierte und erstattete Bestellungen diese Liste
verlassen. Bestellungen, die auf den Kunden warten (zum Beispiel Awaiting payment oder Ready for pickup), werden nach
einer kürzeren Frist hervorgehoben.

## So funktioniert Ihr Shop

Dieser Abschnitt beschreibt den Bestellvorgang Schritt für Schritt so, wie **diese Installation** konfiguriert ist: in
den Warenkorb legen, der Warenkorb, der Shop-Account-Handler, die Bestätigungsseite mit Versand und USt, die Zahlung
(die Workflows an den Shop-Auslösern und die verfügbaren Zahlungsanbieter), die Bestellmails und der Beleg sowie die
Bearbeitung durch die Status. Jeder Schritt nennt die Einstellung oder Seite der Administration, die ihn ändert. Es
folgen Bestellstatus, USt-Typen, USt-Regeln, die Länder, aus denen Bestellungen kamen, Währungen und Rabattgruppen.

> **Tipp**
> Drucken Sie diesen Abschnitt aus und bewahren Sie ihn bei Ihrer Shop-Dokumentation auf. Wenn eine neue Person
> dazukommt oder ein Partner die Einrichtung übernimmt, zeigt er, wie Ihr Shop heute tatsächlich funktioniert.

## Nächste Schritte

Vorschläge, die aus Ihren Daten und Einstellungen abgeleitet sind, die dringendsten zuerst. Die Funktionsseite nennt
unter anderem:

- ausstehende Bestellungen, die länger als zwei Tage warten;
- keinen Zahlungsschritt an der Kasse;
- einen USt-Typ mit 0 Prozent, der verwendet wird;
- eine Warenkorb-Bereinigung, die Warenkörbe löschen würde, die in PHP-Sitzungen gehalten werden.

Behandeln Sie diese Liste als die eigene Checkliste des Shops: Eine leere Liste ist ein gutes Zeichen.

## Bestellstatistik

**Shop > Statistik** (`shop/statistics`) zeigt die Verkäufe pro Produkt für ein Jahr und einen Monat Ihrer Wahl
(`kernel/private/classes/views/shop/orderstatistics.php`). Verwenden Sie sie für Monatsabschlusszahlen und zum
Vergleich von Saisons.

Das Dashboard berechnet seine Zahlen mit einer festen Menge von etwa zwanzig Aggregatabfragen, unabhängig von der Größe
des Shops, geschrieben so, dass sie auf Oracle, MySQL, PostgreSQL und SQLite gleich laufen; es bleibt also schnell,
während der Bestellverlauf wächst
([Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md#what-is-on-the-page)).

## Eine wöchentliche Durchsicht

Eine vorgeschlagene Routine von einer Viertelstunde für den Inhaber:

1. Bestellungen und Umsatz für 7 und 30 Tage: steigend, gleichbleibend oder fallend?
2. Offene Bestellungen und zu lange wartende Bestellungen: Kommt das Team hinterher?
3. Abgebrochene Warenkörbe: Hat eine Änderung bei Versand oder Preisen sie ansteigen lassen?
4. Bestseller: Stehen sie auf der Startseite und im Newsletter?
5. Nächste Schritte: Gibt es etwas Neues?
6. Bestellungen mit Payment failed: Hat der Anbieter Probleme?

Eine Vorlage für die monatliche Fassung steht unter [Monatliche Betriebscheckliste](../vorlagen/monatliche-betriebscheckliste.md).

## Was das Dashboard nicht tut

- Es exportiert keine Zahlen in eine Tabellenkalkulation; die Shop-Ansichten des Kernels haben keinen CSV-Export der
  Bestellungen.
- Es erfasst keine Besucher; zum Besucheraufkommen siehe [Besucherstatistiken und Webanalyse](../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md).
- Es ersetzt nicht Ihre Buchhaltung: Der Umsatz auf dem Dashboard ist Bestellwert, nicht verbuchte Einnahmen.

Wenn Sie Exporte oder Anbindungen an die Buchhaltung brauchen, planen Sie sie als Erweiterung oder Integration
([Was der eingebaute Shop kann](../../../../eng-us/help/businesses/stores/what-the-built-in-shop-does.md#what-is-not-included)).

## Verwandte Seiten

- [Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md)
- [Sortierung der Bestellliste](../../../../eng-us/features/6.0/order-list-sorting.md)
- [Einen Shop im Alltag betreiben](einen-shop-im-alltag-betreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
