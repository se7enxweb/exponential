---
title: Einen Shop testen
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 71
summary: Ein vollständiger Testplan für einen Exponential-Shop - Testumgebung, Testdaten und Test-Mails, Katalog, Warenkorb, Kasse, Zahlung, USt, Versand, Bestellungen, Berechtigungen, Belege und Aufräumen - mit dem, was sich automatisieren lässt, und dem, was nach jedem Update zu wiederholen ist.
keywords: Shop testen, Testplan, Testbestellung, Testmodus, Sandbox, Testdaten, Mail-Transport per Datei, Regressionstest, Abnahme, USt-Test, Zahlungstest, Berechtigungstest, Aufräumen, testing a store, test plan, test order, test mode, sandbox, test data, file mail transport, regression test, acceptance, VAT test, payment test, permissions test, cleanup
type: help
---
# Einen Shop testen

Diese Seite richtet sich an Entwickler, Agenturen und Händler, die sicher sein müssen, dass ein Shop funktioniert, bevor
Kunden herausfinden, dass er es nicht tut. Sie gibt einen Testplan in der Reihenfolge, in der ein Kunde den Shop
erlebt, plus den Aufbau, der Tests sicher hält. Die kurze Startliste für Inhaber ist die
[Checkliste für den Shop-Start](../../unternehmen/shops/checkliste-fuer-den-shop-start.md); diese Seite ist der vollständige
Plan dahinter.

## Inhalt

1. [Eine sichere Testumgebung](#eine-sichere-testumgebung)
2. [Testdaten](#testdaten)
3. [Der Plan](#der-plan)
4. [Was sich automatisieren lässt](#was-sich-automatisieren-lässt)
5. [Nach jedem Update](#nach-jedem-update)
6. [Aufräumen](#aufräumen)

## Eine sichere Testumgebung

- Testen Sie, wann immer möglich, auf einer Kopie der Site, nicht im Live-Shop
  ([Mit Git entwickeln](../../mit-git-entwickeln.md), [Sicherungen und Wiederherstellungen](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md)).
- Versetzen Sie jeden Zahlungsanbieter in seinen Testmodus: `paypal.ini [ServerSettings]` auf die Sandbox gerichtet oder
  `ezauthorize.ini TestMode=true` ([Durchgang durch paymentgateways.ini](../settings/paymentgateways-ini-durchgegangen.md)).
- Stoppen Sie echte Mails: `site.ini [MailSettings] Transport=file` schreibt Mails in Dateien, statt sie zu senden.
  Prüfen Sie, welcher Wert gilt, denn eine Override-Datei kann einen anderen Transport setzen
  ([Durchgänge durch die Shop-Einstellungen](../settings/README.md#den-wirksamen-wert-prüfen)).
- Verwenden Sie Test-Kundenkonten mit Adressen auf reservierten Domains wie `example.invalid`, niemals Adressen oder
  Daten echter Kunden.
- Der Callback des Anbieters muss die Test-Site erreichen; eine Test-Site hinter einer Anmeldung oder einem VPN kann ihn
  nicht empfangen.

## Testdaten

| Element | Vorbereiten |
|---|---|
| Produkte | Eines je Klasse, eines mit Optionen, eines ohne Preis, eines unveröffentlicht |
| USt | Ein Produkt je Produktkategorie; Kunden für jedes Land mit einer Regel und eines ohne |
| Währungen | Eine Bestellung je Währung, in der Sie verkaufen |
| Kunden | Ein Gast, ein registrierter Kunde, ein Mitglied jeder Rabattgruppe, ein Mitarbeiter je Rolle |

## Der Plan

**Katalog**

- [ ] Jede Produktklasse zeigt einen Preis und das Formular „Zum Warenkorb hinzufügen“.
- [ ] **Web-Shop > Produkt Übersicht** listet alle Produkte jeder Klasse mit Preisen auf.
- [ ] Inhalte ohne Preisfeld lassen sich nicht in den Warenkorb legen (der Kernel bricht das Hinzufügen ab), und
  Produkte mit leerem Preis fängt die Dashboard-Kennzahl „Produkte ohne Preis“ ab.

**Warenkorb**

- [ ] Hinzufügen, Menge ändern, entfernen; null und ungültige Mengen ergeben einen Fehler.
- [ ] `RedirectAfterAddToBasket` verhält sich wie festgelegt.
- [ ] Ändern Sie den Preis eines Produkts, während es in einem Warenkorb liegt: An der Kasse wird der Artikel entfernt
  und als entfernt aufgeführt ([Die Kassenoperationen Schritt für Schritt](../kasse/die-kassenoperationen-schritt-fuer-schritt.md#schritt-3-die-schaltfläche-kasse)).

**Kundendaten und Bestätigung**

- [ ] Der Bestellabschluss als Gast und angemeldet erreicht die Bestätigungsseite.
- [ ] Versand und USt stimmen für jedes Testland und jede Produktkategorie.
- [ ] Rabatte gelten nur für die richtigen Gruppen ([Rabatte](../../kunden/handel/rabatte.md)).
- [ ] Abbrechen führt zu einem vollen Warenkorb zurück; ein Neuladen der Seite fügt den Versand nicht doppelt hinzu.

**Zahlung**

- [ ] Erfolgreiche Zahlung: Bestellung mit einer Nummer aufgegeben, Status wie konfiguriert, Mail im Test-Postfach.
- [ ] Abgebrochene Zahlung: keine aufgegebene Bestellung, der Warenkorb ist wieder da, ein neuer Versuch funktioniert.
- [ ] Abgelehnte Karte oder fehlgeschlagene Zahlung: eine klare Meldung.
- [ ] Auswahl des Gateways, wenn mehrere Gateways ausgewählt sind.

**Bestellungen und Mitarbeiter**

- [ ] Jede Mitarbeiterrolle sieht genau die Bildschirme und Statusänderungen, die sie sehen soll
  ([Statusübergänge und Berechtigungen](../bestellungen/statusuebergaenge-und-berechtigungen.md#die-rollen-testen)).
- [ ] Archivieren und Zurückholen; die Kennzahlen des Dashboards ändern sich wie erwartet.

**Belege und Mail**

- [ ] Inhalt und Links der Bestellmail; der Beleg öffnet sich auf einem anderen Gerät ohne Anmeldung, wenn Belege
  eingeschaltet sind.

**Barrierefreiheit und Geräte**

- [ ] Der vollständige Weg nur mit der Tastatur und auf einem Telefon ([Barrierefreiheit im Shop](barrierefreiheit-im-shop.md)).

## Was sich automatisieren lässt

- Berechnungen in Ihren eigenen Handlern: Unit-Tests ohne Datenbank ([PHPUnit-Tests ohne Datenbank](../../ersteller/anwendungsentwickler/phpunit-tests-ohne-datenbank.md)).
- Wiederholbare Abläufe: Die eigenen Tests des Repositorys für die Handelsdienste
  (`tests/tests/extension/expservices/commerce/`) zeigen, wie sich Warenkorb- und Bestelloperationen aus Code heraus
  durchspielen lassen ([Anwendungen testen](../../ersteller/anwendungsentwickler/anwendungen-testen.md)).
- Visuelle Prüfungen von Produkt-, Warenkorb- und Kassenseiten nach Designänderungen.

## Nach jedem Update

Wiederholen Sie nach jedem Update von Exponential oder einer Erweiterung und nach jeder Änderung an Shop-Einstellungen
oder Workflows den Abschnitt Zahlung und eine vollständige Bestellung je Zahlungsart
([Upgrades ohne Überraschungen](../../unternehmen/zuverlaessigkeit/aktualisierungen-ohne-ueberraschungen.md)).

## Aufräumen

Auf einer Testkopie entfernt das Aufräumskript des Shops alle Warenkörbe, Wunschlisten und Bestellungen. Im Live-Shop
entfernen Sie Testbestellungen einzeln und führen dieses Skript niemals aus
([Archivieren, Entfernen und Aufräumen](../bestellungen/archivieren-entfernen-und-aufraeumen.md)). Schalten Sie die Anbieter zurück in
den Live-Modus und die Mail zurück auf den echten Transport, und geben Sie eine kleine echte Bestellung auf.

## Verwandte Seiten

- [Einen Shop betreiben](README.md)
- [Warenkorb, Kasse und Bestellungen: Checkliste für eine Testbestellung](../../ersteller/website-ersteller/warenkorb-kasse-und-bestellungen.md#checkliste-für-eine-testbestellung)
- [Ablauf von Warenkorb und Kasse: den Weg testen](../../unternehmen/shops/warenkorb-und-bestellablauf.md#den-weg-testen)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
