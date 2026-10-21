---
title: Checkliste für den Shop-Start
audience: businesses, project-manager, website-creator
category: business-solutions
order: 220
summary: Alles, was Sie entscheiden, konfigurieren und testen, bevor ein Exponential-Shop seine erste echte Bestellung annimmt - Katalog, Preise und USt, Versand, Kasse, Zahlung, Mails und Belege, Mitarbeiter und Berechtigungen, Sicherheit, Leistung und der Starttag.
keywords: Shop-Start, Go-live, Checkliste für den Start, Start des Web-Shops, Testbestellung, Zahlungstest, einen Shop eröffnen, vor dem Start, store launch, go-live, launch checklist, webshop launch, test order, payment test, opening a store, pre-launch
type: help
---
# Checkliste für den Shop-Start

Diese Seite richtet sich an den Geschäftsinhaber und den Projektleiter, die einen Shop eröffnen. Arbeiten Sie sie
einige Wochen vor dem Start mit der Person durch, die den Shop gebaut hat. Jede Zeile nennt die Seite mit den
Einzelheiten. Die allgemeine Checkliste für Websites ist [Die Checkliste für den Start](../../../../eng-us/help/creators/website-creators/launch-checklist.md);
diese Seite ergänzt, was ein Shop braucht.

## Inhalt

1. [Entscheidungen auf Papier](#entscheidungen-auf-papier)
2. [Katalog](#katalog)
3. [Preise, Steuern und Versand](#preise-steuern-und-versand)
4. [Kasse und Kunden](#kasse-und-kunden)
5. [Zahlung](#zahlung)
6. [Mails und Belege](#mails-und-belege)
7. [Mitarbeiter und Berechtigungen](#mitarbeiter-und-berechtigungen)
8. [Sicherheit und Daten](#sicherheit-und-daten)
9. [Leistung und Betrieb](#leistung-und-betrieb)
10. [Testbestellungen](#testbestellungen)
11. [Der Starttag](#der-starttag)
12. [Die erste Woche](#die-erste-woche)

## Entscheidungen auf Papier

- [ ] Das Planungsarbeitsblatt ist ausgefüllt ([Shop-Planung für kleine Unternehmen](shop-planung-fuer-kleine-unternehmen.md#ein-planungsarbeitsblatt)).
- [ ] Verkaufsbedingungen, Texte zu Versand, Rückgabe und Datenschutz sind geschrieben und von Ihrer rechtlichen
  Beratung freigegeben.
- [ ] Steuersätze sind von Ihrem Steuerberater bestätigt.
- [ ] Fehlende Funktionen (Lagerbestand, Rechnungen, Etiketten, Gutscheine) haben eine Antwort: Erweiterung,
  Integration oder manueller Ablauf ([Was der eingebaute Shop kann](was-der-eingebaute-shop-kann.md#was-nicht-enthalten-ist)).

## Katalog

- [ ] Jedes Produkt hat Namen, Artikelnummer, Preis, Bild und Beschreibung ([Produktklassen und Katalog](produktklassen-und-katalog.md)).
- [ ] Das Dashboard zeigt keine Produkte ohne Preis ([Shop-Dashboard und Berichte](shop-uebersicht-und-berichte.md)).
- [ ] Kategorien, Menüs und Tags funktionieren; die Suche findet Produkte nach Name und Nummer ([Suche und Navigation im Shop](suche-und-navigation-im-shop.md)).

## Preise, Steuern und Versand

- [ ] Die Shop-Währung ist in einem Override gesetzt; andere Währungen sind nur aktiv, wenn gewünscht ([Preise und Währungen](preise-und-waehrungen.md)).
- [ ] Die Rundung ergibt die Preisendungen, die Sie erwarten.
- [ ] USt-Typen sind benannt und korrekt; USt-Regeln für jedes Land mit abweichendem Satz ([USt-Regeln und USt-Typen](ust-regeln-und-ust-typen.md)).
- [ ] Das Dashboard zeigt keinen unerwarteten USt-Typ mit 0 Prozent.
- [ ] Die Versandkosten erscheinen auf der Bestätigungsseite ([Versand und Bearbeitung](versand-und-bearbeitung.md)).
- [ ] Die Länderliste führt Ihre Märkte zuerst auf ([Ländergruppen und Regionen](laendergruppen-und-regionen.md)).

## Kasse und Kunden

- [ ] Der Shop-Account-Handler entspricht Ihrer Wahl zwischen Gastbestellung und Bestellung mit Konto ([Kundenkonten und Gastbestellung](kundenkonten-und-gastbestellung.md)).
- [ ] Das Bestellformular fragt jedes Adressfeld ab, das Ihr Versanddienstleister braucht.
- [ ] `RedirectAfterAddToBasket` und der Adressname des Warenkorbs sind so, wie Sie es möchten ([Warenkorb und Bestellablauf](warenkorb-und-bestellablauf.md)).
- [ ] Warenkorb-, Kassen- und Kontoseiten werden von keinem CDN oder Proxy gecacht ([Shop-Leistung zu Spitzenzeiten](shop-leistung-zu-spitzenzeiten.md#seiten-die-sich-nicht-cachen-lassen)).
- [ ] Warenkorb-, Kassen- und Kontoseiten werden aus Suchmaschinen herausgehalten ([robots.txt](../../../../eng-us/help/creators/website-creators/robots-txt.md)).

## Zahlung

- [ ] Die Zahlungsanbindung ist aktiv und mit dem Auslöser `shop_checkout` verbunden; das Dashboard warnt nicht vor
  einem fehlenden Zahlungsschritt ([Zahlungsanbieter für Unternehmen](zahlungsanbieter-fuer-unternehmen.md)).
- [ ] Die Anbindung setzt den Bestellstatus, den Sie für bezahlte Bestellungen verwenden ([Zahlungen mit PayPal](zahlungen-mit-paypal.md#bestellstatus-nach-der-zahlung)).
- [ ] Die Adresse für Zahlungsbenachrichtigungen ist aus dem Internet ohne Anmeldung erreichbar.
- [ ] Bei einem Kartenformular auf der Website: Umgang mit Kartendaten geprüft ([Kartenzahlungen und PCI-Grundlagen](kartenzahlungen-und-pci-grundlagen.md)).

## Mails und Belege

- [ ] `SendOrderEmail` eingeschaltet; `AdminEmail` und `EmailSender` auf echte Adressen gesetzt ([Bestellbelege und Bestätigungsmails](bestellbelege-und-bestaetigungsmails.md)).
- [ ] Das Template der Bestellmail enthält Ihre Kontaktdaten und Geschäftsbedingungen.
- [ ] Dauerhafte Belege eingeschaltet, wenn Gäste kaufen (`OrderLinkView=orderreceipt`).
- [ ] Testmails kommen im Posteingang an, nicht im Spam ([Mailzustellung](../../hosting/betrieb/mailzustellung.md)).

## Mitarbeiter und Berechtigungen

- [ ] Jeder Mitarbeiter hat ein persönliches Konto mit zweistufiger Anmeldung.
- [ ] Die Rollen folgen den Shop-Funktionen: `buy`, `administrate`, `setup`, `setstatus`
  ([Bestellungen und Bestellstatus](bestellungen-und-bestellstatus.md#wer-einen-status-ändern-darf)).
- [ ] Der Statusweg ist aufgeschrieben, und alle haben ihn gesehen.
- [ ] Für jeden Tag, an dem der Shop geöffnet ist, auch am Wochenende, ist jemand für die Bestellungen benannt.

## Sicherheit und Daten

- [ ] Die [Sicherheitscheckliste für Shops](sicherheitscheckliste-fuer-shops.md) ist abgearbeitet.
- [ ] Datenschutzhinweis veröffentlicht; das Opt-in für Werbung ist vom Kauf getrennt ([Opt-in und Einwilligung](../newsletter/opt-in-und-einwilligung.md)).
- [ ] Backups laufen, und eine Wiederherstellung wurde getestet ([Backups, die sich wiederherstellen lassen](../zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md)).

## Leistung und Betrieb

- [ ] Cronjobs laufen, einschließlich der Warenkorb-Bereinigung ([Cronjobs](../../../anleitungen/cronjobs.md)).
- [ ] Ein Benchmark-Ausgangswert ist gespeichert ([Shop-Leistung zu Spitzenzeiten](shop-leistung-zu-spitzenzeiten.md#vor-der-spitze-messen)).
- [ ] Die Überwachung prüft die Startseite, eine Produktseite und den Warenkorb ([Zustandsprüfungen und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)).
- [ ] Der Wartungsmodus ist bekannt und getestet, für spätere geplante Arbeiten ([Wartungsmodus](../../../../eng-us/features/6.0/maintenance-mode.md)).

## Testbestellungen

Geben Sie diese im Testmodus des Zahlungsanbieters auf, am Desktop und auf einem Smartphone
([Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md#test-order-checklist)):

- [ ] Gastbestellung, ein Produkt, Standardland.
- [ ] Angemeldeter Kunde, mehrere Produkte mit Optionen, ein zweites Land.
- [ ] Bestellung in einer zweiten Währung, wenn Sie eine anbieten.
- [ ] Kunde in einer Rabattgruppe; der Rabatt erscheint.
- [ ] Abgebrochene Zahlung; der Kunde kann es erneut versuchen.
- [ ] Abbruch auf der Bestätigungsseite; der Warenkorb bleibt erhalten.
- [ ] Jede Bestellung erscheint unter Shop mit dem richtigen Status; die Mails kommen an; der Beleg-Link funktioniert
  und lässt sich drucken.
- [ ] Mitarbeiter führen eine Bestellung durch den ganzen Statusweg.

## Der Starttag

1. Stellen Sie die Zahlungsanbindung von Test auf Live um (Serveradresse, Konto, Testmodus aus).
2. Entfernen Sie Testbestellungen aus der aktiven Liste (archivieren Sie sie), damit das Dashboard sauber beginnt.
3. Geben Sie eine kleine echte Bestellung mit einer echten Zahlung auf, erstatten Sie sie dann im Konto beim Anbieter
   und setzen Sie die Bestellung auf Refunded.
4. Beobachten Sie in den ersten Stunden das Dashboard, den Posteingang für Bestellmails und die Logs.
5. Kündigen Sie den Shop an ([Ihren Shop bewerben](ihren-shop-bewerben.md)).

## Die erste Woche

- [ ] Jede Bestellung innerhalb der versprochenen Zeit bearbeitet.
- [ ] Die „Nächste Schritte“ des Dashboards täglich durchgesehen.
- [ ] Kundenfragen für die FAQ gesammelt ([Shop-FAQ](shop-faq.md)).
- [ ] Die tägliche Routine vereinbart ([Einen Shop im Alltag betreiben](einen-shop-im-alltag-betreiben.md)).

Eine allgemeine Vorlage für den Go-live steht unter [Checkliste: Go-live](../vorlagen/checkliste-go-live.md).

## Verwandte Seiten

- [Die Checkliste für den Start](../../../../eng-us/help/creators/website-creators/launch-checklist.md)
- [Live gehen: Hosts und HTTPS](../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md)
- [Checkliste für den Start einer Kunden-Website](../../hosting/checklisten/checkliste-fuer-den-start-einer-kunden-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
