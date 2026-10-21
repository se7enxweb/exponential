---
title: Spenden und Spendenaufrufe
audience: businesses, project-manager, website-creator, sales-marketing
category: business-solutions
order: 20
summary: Die ehrlichen Möglichkeiten, auf einer Exponential-Website Spenden anzunehmen - es gibt kein eigenes Spendenmodul; verlinken Sie auf eine externe Spendenplattform oder verkaufen Sie feste Spendenartikel über den eingebauten Shop mit dem Gateway ezpaypal - mit den Abwägungen, dem, was eine gute Spendenseite enthält, und dem, was Sie mit Ihrem Steuerberater klären sollten.
keywords: Spenden, Spendensammlung, Spendenschaltfläche, Spendenseite, Shop, externe Spendenplattform, regelmäßige Spenden, Quittungen, Hilfsorganisation, gemeinnützige Organisation, donations, fundraising, donate button, giving page, ezpaypal, PayPal, shop, external donation platform, recurring donations, receipts, charity, nonprofit
type: help
---
# Spenden und Spendenaufrufe

Diese Seite richtet sich an Vorstände, Fundraiser und Projektleiter gemeinnütziger Organisationen. Sie sagt klar, was
Exponential für die Annahme von Spenden bietet und was nicht, vergleicht die beiden praktischen Möglichkeiten und
beschreibt eine gute Spendenseite. Lesen Sie sie, bevor Sie Ihrem Vorstand Online-Spenden versprechen.

## Inhalt

1. [Was es gibt](#was-es-gibt)
2. [Möglichkeit A: auf eine externe Spendenplattform verlinken](#möglichkeit-a-auf-eine-externe-spendenplattform-verlinken)
3. [Möglichkeit B: Spendenartikel im eingebauten Shop](#möglichkeit-b-spendenartikel-im-eingebauten-shop)
4. [Die Möglichkeiten im Vergleich](#die-möglichkeiten-im-vergleich)
5. [Was eine gute Spendenseite enthält](#was-eine-gute-spendenseite-enthält)
6. [Quittungen, Steuern und Buchhaltung](#quittungen-steuern-und-buchhaltung)
7. [Sicherheit und Vertrauen](#sicherheit-und-vertrauen)
8. [Checkliste Spenden](#checkliste-spenden)

## Was es gibt

- **Es gibt kein eigenes Spendenmodul** im Repository: keinen Spenden-Datentyp, kein Feld „Betrag nach Wahl“, keine
  Funktion für Gift Aid oder Steuerquittungen. Eine Suche in Code und Dokumentation nach Spendenfunktionen findet nur die
  Seiten über Spenden an das Exponential-Projekt selbst.
- **Der eingebaute Shop** verkauft Produkte mit einem Preis, den Redakteure festlegen (`ezprice` oder `ezmultiprice`),
  über Warenkorb, Kasse und einen Zahlungsschritt ([Einen Shop betreiben: Überblick](../../../../eng-us/help/creators/website-creators/shop-overview.md)).
- **`ezpaypal`** ist ein Zahlungs-Gateway für den Shop, das von `composer.json` verlangt wird: Der Kunde wird zum Bezahlen
  zu PayPal geschickt, und PayPal ruft die Website zurück ([ezpaypal](../../../../eng-us/features/6.0/extensions/ezpaypal.md)).
- **Regelmäßige Zahlungen** sind weder für den Shop noch für `ezpaypal` dokumentiert.

## Möglichkeit A: auf eine externe Spendenplattform verlinken

Die meisten kleinen gemeinnützigen Organisationen machen es so: eine deutliche Schaltfläche **Spenden** auf der Website,
die zu einer Spendenseite führt, die ein Zahlungsanbieter oder eine Fundraising-Plattform betreibt, die Sie bereits
nutzen.

So bauen Sie das in Exponential:

1. Legen Sie eine Seite „Spenden“ an, die erklärt, warum man geben sollte und was eine Spende bewirkt.
2. Setzen Sie den Link zur Plattform darauf und eine Schaltfläche **Spenden** in den Kopfbereich oder auf die
   Startseite. In Exponential Layouts erledigt das der Standardblock `button` („Button / Link“) ohne Entwickler
   ([Referenz zu Layouts](../../../../eng-us/bc/6.0/LAYOUTS.md)); ein Linkfeld (`ezurl`) an einer Seitenklasse funktioniert ebenso
   ([Datentypen wählen](../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)).
3. Für eine Kampagne setzen Sie die Schaltfläche auf die Kampagnenseite ([Kampagnen und Landingpages](kampagnen-und-landingpages.md)).
4. Sagen Sie Spendern vor dem Klick, dass sie Ihre Website verlassen, und nennen Sie die Plattform in Ihrem
   Datenschutzhinweis.

Was Sie gewinnen: Die Plattform übernimmt Kartendaten, regelmäßige Spenden und Quittungen, soweit sie diese anbietet;
Ihre Website verarbeitet keine Zahlungsdaten. Was Sie aufgeben: Die Spende selbst wird nicht in Exponential erfasst.

## Möglichkeit B: Spendenartikel im eingebauten Shop

Wenn Sie bereits Artikel (T-Shirts, Eintrittskarten, Publikationen) über den Shop verkaufen, können Sie feste
Spendenartikel hinzufügen:

1. Ein Produkt der mitgelieferten Klasse `product`, zum Beispiel „Spende 10“, „Spende 25“, „Spende 50“, jeweils mit
   seinem Preis ([Produkte, Preise und USt](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md)).
   Optionen wie `ezoption` oder `ezrangeoption` „lassen Besucher Optionen wählen ..., die den Preis ändern können“; sie
   bieten Auswahlmöglichkeiten, die Sie festlegen, keinen freien Betrag.
2. `ezpaypal` ist eingerichtet: `Business` in `settings/override/paypal.ini.append.php`, gegen die Sandbox von PayPal
   getestet, das PayPal-Ereignis in den Workflow der Kasse aufgenommen
   ([Zahlungs-Gateways](../../../../eng-us/help/creators/website-creators/payment-gateways.md#setting-up-paypal)).
3. Die USt-Typen für diese Artikel sind so gesetzt, wie Ihr Steuerberater es empfiehlt
   ([USt](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md#vat)).
4. Bestellquittungen sind eingeschaltet, damit Spender ihre Quittung jederzeit abrufen können
   ([Bestellquittungen](../../../../eng-us/features/6.0/order-receipts.md)).
5. Das Shop-Dashboard ist geprüft: Es sagt, wenn ein Zahlungsschritt fehlt
   ([Shop-Dashboard](../../../../eng-us/features/6.0/store-dashboard.md)).

Was Sie gewinnen: Spenden erscheinen als Bestellungen in der Administration, mit ihrem Zahlungsstatus
(`PaymentMadeOrderStatusID`, standardmäßig `1000`). Was Sie aufgeben: Spender gehen durch einen Warenkorb und eine Kasse,
was sich wie ein Kauf liest; es gibt keinen freien Betrag und keine regelmäßigen Spenden.

Zum Shop im Detail siehe den Bereich [Shops](../shops/README.md) und
[Zahlungen mit PayPal](../shops/zahlungen-mit-paypal.md).

## Die Möglichkeiten im Vergleich

| | A: externe Plattform | B: Shop-Artikel mit `ezpaypal` |
|---|---|---|
| Aufwand für die Einrichtung | Gering: eine Seite und eine Schaltfläche | Mittel: Produkte, Gateway, Workflow, Tests |
| Freier Betrag | Soweit die Plattform ihn anbietet | Nein, nur feste Artikel oder festgelegte Optionen |
| Regelmäßige Spenden | Soweit die Plattform sie anbietet | Nicht dokumentiert |
| Zahlungsdaten auf Ihrer Website | Keine | Keine: Die Zahlung geschieht bei PayPal; die Bestelldaten liegen auf Ihrer Website |
| Spenden in Exponential sichtbar | Nein | Ja, als Bestellungen |
| Quittungen | Soweit die Plattform sie anbietet | Bestellquittungen (eine Kaufquittung, keine Zuwendungsbestätigung) |
| Gebühren | Die der Plattform | Die von PayPal |

Keine der beiden Möglichkeiten bringt eine Lizenzgebühr mit sich; die Gebühren sind die des Zahlungsanbieters.
Vergleichen Sie deren veröffentlichte Bedingungen selbst.

## Was eine gute Spendenseite enthält

- [ ] Ein Satz darüber, was eine Spende bewirkt, mit einem echten Beispiel aus Ihrer Arbeit.
- [ ] Die Spendenschaltfläche ohne Scrollen sichtbar und am Ende wiederholt.
- [ ] Wer Sie sind: rechtlicher Name, die Registrierungsangaben, die Ihr Recht verlangt, ein Kontakt.
- [ ] Wohin das Geld geht, mit einem Link zu Ihrem neuesten Jahresbericht (ein Datei-Download).
- [ ] Andere Wege zu geben (Überweisung, Sachspenden, Ehrenamt) für Menschen, die diese bevorzugen.
- [ ] Ein Datenschutzhinweis, der den Zahlungsanbieter abdeckt.
- [ ] Eine Dankesseite für Spender, die vom Anbieter zurückkehren.
- [ ] Barrierefrei: Beschriftungen, Kontrast, Bedienung per Tastatur ([Barrierefreiheit für gemeinnützige Organisationen](barrierefreiheit-fuer-gemeinnuetzige.md)).

## Quittungen, Steuern und Buchhaltung

Die Bestellquittungen von Exponential sind Quittungen für eine Bestellung. Ob Sie Spendenquittungen oder
Zuwendungsbestätigungen ausstellen müssen und in welcher Form, hängt von Ihrem Land ab; Exponential hat dafür keine
Funktion. Fragen Sie Ihren Steuerberater und verwenden Sie die Aufzeichnungen des Anbieters als Quelle für Ihre
Buchhaltung.

## Sicherheit und Vertrauen

- Liefern Sie die gesamte Website über HTTPS aus ([Online gehen: Hosts und HTTPS](../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md)).
- Beim Shop „muss die Benachrichtigungs-URL von PayPal aus dem Internet erreichbar sein und darf keine Anmeldung
  verlangen“ ([Zahlungs-Gateways](../../../../eng-us/help/creators/website-creators/payment-gateways.md#setting-up-paypal)).
- Halten Sie Administratorkonten wenige und schützen Sie sie mit zweistufiger Anmeldung
  ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- Zum Umgang mit Karten allgemein lesen Sie [Kartenzahlungen und PCI-Grundlagen](../shops/kartenzahlungen-und-pci-grundlagen.md).

## Checkliste Spenden

- [ ] Möglichkeit gewählt (A, B oder beide) und mit Begründung aufgeschrieben.
- [ ] Spendenseite vollständig (Liste oben) und auf einem Telefon getestet.
- [ ] Möglichkeit A: Link getestet; Spender wissen, dass sie die Website verlassen; der Anbieter steht im
      Datenschutzhinweis.
- [ ] Möglichkeit B: Sandbox-Zahlung abgeschlossen und die Bestellung wurde bezahlt; eine abgebrochene Zahlung lässt die
      Bestellung unbezahlt.
- [ ] Quittungen und steuerliche Behandlung mit Ihrem Steuerberater abgestimmt.
- [ ] Eine namentlich benannte Person prüft Spenden oder Bestellungen wöchentlich.

## Verwandte Seiten

- [Gemeinnützige Organisationen](README.md)
- [Zahlungs-Gateways](../../../../eng-us/help/creators/website-creators/payment-gateways.md)
- [Zahlungs-Gateways für Unternehmen](../shops/zahlungsanbieter-fuer-unternehmen.md)
- [Wie Unternehmen mit Exponential online verkaufen](../wie-unternehmen-online-verkaufen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
