---
title: Zahlungen mit PayPal
audience: businesses, admin, website-creator
category: business-solutions
order: 140
summary: PayPal-Zahlungen in einem Exponential-Shop mit der Erweiterung ezpaypal annehmen - wie die Weiterleitung und die Zahlungsbenachrichtigung funktionieren, was die Erweiterung prüft, bevor sie eine Bestellung als bezahlt markiert, welche Einstellungen ein Geschäftsinhaber festlegt und wie Sie testen.
keywords: PayPal, ezpaypal, paypal.ini, Benachrichtigungsadresse, Zahlungsbenachrichtigung, Sandbox, Zahlung mit Weiterleitung, PaymentMadeOrderStatusID, Geschäftskonto, Zahlungsstatus, notify URL, payment notification, sandbox, redirect payment, business account, payment status
type: help
---
# Zahlungen mit PayPal

Diese Seite richtet sich an Geschäftsinhaber, deren Kunden mit PayPal bezahlen sollen. Sie erklärt aus Sicht des
Unternehmens, was die Erweiterung `ezpaypal` tut, was sie prüft, bevor eine Bestellung als bezahlt gilt, welche
Einstellungen Sie festlegen und wie Sie vor dem Start testen. Die Referenz für die Einrichtung ist
[ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md).

## Inhalt

1. [Warum PayPal](#warum-paypal)
2. [Wie eine PayPal-Zahlung abläuft](#wie-eine-paypal-zahlung-abläuft)
3. [Was die Erweiterung prüft](#was-die-erweiterung-prüft)
4. [Was Sie festlegen](#was-sie-festlegen)
5. [Bestellstatus nach der Zahlung](#bestellstatus-nach-der-zahlung)
6. [In der Sandbox testen](#in-der-sandbox-testen)
7. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)
8. [Version und Lizenz](#version-und-lizenz)

## Warum PayPal

Für ein kleines Unternehmen hat ein Anbieter mit Weiterleitung wie PayPal zwei praktische Vorteile:

- **Kartendaten laufen nie über Ihre Website.** Der Kunde bezahlt auf der Seite von PayPal, Ihr Server verarbeitet also
  keine Kartennummern ([Kartenzahlungen und PCI-Grundlagen](kartenzahlungen-und-pci-grundlagen.md)).
- **Sie wird mit Exponential ausgeliefert.** `se7enxweb/ezpaypal` wird vom `composer.json` des Hauptverzeichnisses
  verlangt (`~1.2.3`), wird also mit dem System installiert und muss nur aktiviert und konfiguriert werden.

## Wie eine PayPal-Zahlung abläuft

Nach [ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md) und dem Code der Erweiterung
(`extension/ezpaypal/classes/ezpaypalgateway.php`):

1. Der Kunde bestätigt die Bestellung und geht zur Kasse.
2. Das PayPal-Workflow-Ereignis an der Kasse schickt den Kunden zu PayPal, mit Ihrem Geschäftskonto, einer kurzen
   Bestellbeschreibung, der Bestellnummer, dem Betrag und der Währung.
3. Der Kunde bezahlt auf der Seite von PayPal.
4. PayPal sendet eine Benachrichtigung an die Benachrichtigungsadresse Ihrer Website, `/paypal/notify_url`.
5. Die Erweiterung prüft die Benachrichtigung (nächster Abschnitt) und genehmigt, wenn alles in Ordnung ist, die
   Zahlung und setzt den Status der Bestellung.

> **Warnung**
> Die Benachrichtigungsadresse muss aus dem Internet erreichbar sein und darf keine Anmeldung verlangen
> ([Zahlungsanbieter](../../ersteller/website-ersteller/payment-gateways.md#paypal-einrichten)). Eine Website hinter
> HTTP-Basic-Authentifizierung, einer IP-Freigabeliste oder im Wartungsmodus nimmt Zahlungen an, die nie als bezahlt
> markiert werden.

## Was die Erweiterung prüft

Bevor eine Bestellung als bezahlt markiert wird, prüfen die Benachrichtigungsansicht
(`extension/ezpaypal/modules/paypal/notify_url.php`) und der Callback-Prüfer des Kernels
(`kernel/shop/classes/ezpaymentcallbackchecker.php`), dass:

- PayPal bestätigt, dass die Benachrichtigung echt ist (der Prüfer schickt sie zur Validierung an PayPal zurück);
- der Zahlungsstatus **Completed** ist;
- der Betrag der Bestellsumme einschließlich USt entspricht;
- die Währung der Währung der Bestellung entspricht.

Schlägt eine Prüfung fehl, wird die Bestellung nicht als bezahlt markiert, und der Grund wird in das Log der
Erweiterung geschrieben. Das schützt Sie vor gefälschten Benachrichtigungen und vor Zahlungen mit falschem Betrag.

## Was Sie festlegen

Alle Einstellungen gehören in `settings/override/paypal.ini.append.php`, nie in die ausgelieferte Datei
([ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md#einstellungen)):

| Einstellung | Festzulegen |
|---|---|
| `[PaypalSettings] Business` | Ihr PayPal-Geschäftskonto; Pflicht |
| `[ServerSettings] ServerName` | Die Sandbox von PayPal zum Testen, der Live-Server (ausgeliefert `https://www.paypal.com`) für echte Zahlungen |
| `[PaypalSettings] MaxDescriptionLength` | Wie lang die Bestellbeschreibung bei PayPal sein darf (ausgeliefert 127) |
| `NoNote`, `NoteLabel`, `PageStyle`, `BackgroundColor`, `LogoURI` | Wie die Seite von PayPal für Ihren Kunden aussieht: Ihr Logo, ein Notizfeld |
| `[OrderSettings] PaymentMadeOrderStatusID` | Der Bestellstatus, der bei erfolgter Zahlung gesetzt wird (ausgeliefert 1000) |

## Bestellstatus nach der Zahlung

Der ausgelieferte Wert von `PaymentMadeOrderStatusID` ist `1000`, die erste Nummer für **eigene** Status; die siebzehn
mitgelieferten Status verwenden 1 bis 17 ([Bestellungen und Bestellstatus](bestellungen-und-bestellstatus.md#eigene-status)).
Entscheiden Sie sich vor dem Start für eine dieser Möglichkeiten:

- verweisen Sie auf den mitgelieferten Status **Paid**, 5, damit das Dashboard und der Statusweg mit den
  Standardstatus arbeiten; oder
- legen Sie einen eigenen Status mit der Nummer 1000 an (zum Beispiel „Mit PayPal bezahlt“) und nehmen Sie ihn in
  Ihren Ablauf auf.

Führen Sie dann eine Testzahlung durch und prüfen Sie, ob die Bestellung den gewählten Status zeigt.

## In der Sandbox testen

- [ ] `ServerName` verweist auf den Sandbox-Server von PayPal, mit einem Sandbox-Geschäftskonto in `Business`.
- [ ] Eine Testzahlung wird abgeschlossen, und die Bestellung erhält den Status „bezahlt“.
- [ ] Eine abgebrochene Zahlung lässt die Bestellung unbezahlt, und der Kunde kann zum Warenkorb zurückkehren.
- [ ] Die Benachrichtigungsadresse ist von außen über HTTPS erreichbar (keine Anmeldung, keine IP-Sperre).
- [ ] Eine Bestellung in einer zweiten Währung wird von PayPal angenommen und bei der Rückkehr abgeglichen.
- [ ] `ServerName` und `Business` werden am Starttag auf Live-Werte umgestellt, und eine kleine echte Zahlung wird
  durchgeführt und erstattet.

Die Kontoarten, Gebühren und Integrationsregeln von PayPal legt PayPal fest; klären Sie mit PayPal, ob Ihr Konto diese
Art von Zahlung mit Weiterleitung und Benachrichtigung unterstützt, bevor Sie darauf aufbauen.

## Wenn etwas schiefgeht

| Anzeichen | Was Sie zuerst prüfen |
|---|---|
| Kunde hat bezahlt, Bestellung nicht als bezahlt markiert | Ist die Benachrichtigungsadresse ohne Anmeldung erreichbar? Zeigt das Log eine fehlgeschlagene Prüfung? |
| „Amount does not match“ im Log | Rundung oder eine abweichende Währung ([Preise und Währungen](preise-und-waehrungen.md)) |
| Kunden landen nach dem Start in der Sandbox | `ServerName` verweist noch auf die Sandbox |
| Der Status wird gesetzt, erscheint aber nicht in Ihrem Ablauf | `PaymentMadeOrderStatusID` verweist auf einen Status, den Sie nicht verwenden |

Zu Fehlern auf der Website im Allgemeinen siehe [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md).

## Version und Lizenz

Die Exponential-Releases von ezpaypal (1.2.0 bis 1.2.3) stehen unter GPL v2 oder später; das eigene README der
Erweiterung beschreibt noch ihre ursprüngliche doppelte Lizenzierung, und das Zahlungsverhalten ist in diesen Releases
unverändert ([ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md#was-sich-in-den-releases-von-exponential-6-geändert-hat)).

## Verwandte Seiten

- [ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md)
- [Zahlungsanbieter für Unternehmen](zahlungsanbieter-fuer-unternehmen.md)
- [Kartenzahlungen und PCI-Grundlagen](kartenzahlungen-und-pci-grundlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
