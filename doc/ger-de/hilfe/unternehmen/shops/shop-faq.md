---
title: Shop-FAQ
audience: businesses, sales-marketing, project-manager
category: business-solutions
order: 240
summary: Kurze Antworten auf die Fragen, die Geschäftsinhaber zum Verkauf mit dem eingebauten Shop von Exponential am häufigsten stellen - Funktionen, Zahlungen, Steuern, Lagerbestand, Gutscheine, Downloads, Kosten, Sicherheit und Leistung - jede mit einem Link zur ausführlichen Seite.
keywords: Shop-FAQ, Fragen zum Web-Shop, E-Commerce-FAQ, Shop-Funktionen, Zahlungen, USt, Lagerbestand, Gutscheine, digitale Downloads, Kosten, Lizenz, store FAQ, webshop questions, e-commerce FAQ, shop features, payments, VAT, stock, coupons, digital downloads, cost, license
type: help
---
# Shop-FAQ

Diese Seite richtet sich an Geschäftsinhaber und Entscheider, die schnelle Antworten zum Online-Verkauf mit Exponential
möchten. Jede Antwort ist kurz und verweist auf die Seite mit den Einzelheiten und den Quellen.

## Inhalt

1. [Der Shop im Allgemeinen](#der-shop-im-allgemeinen)
2. [Produkte und Preise](#produkte-und-preise)
3. [Kasse und Kunden](#kasse-und-kunden)
4. [Zahlungen](#zahlungen)
5. [Bestellungen und tägliche Arbeit](#bestellungen-und-tägliche-arbeit)
6. [Fehlende Funktionen](#fehlende-funktionen)
7. [Kosten, Sicherheit und Zuverlässigkeit](#kosten-sicherheit-und-zuverlässigkeit)

## Der Shop im Allgemeinen

**Ist der Shop ein Zusatz, den ich kaufen muss?**
Nein. Der Shop ist Teil des Exponential-Kernels (`kernel/shop/`), und das README nennt „Advanced e-Commerce Stores
(Built in)“ unter den wichtigsten Funktionen. Exponential steht unter der Lizenz GPL v2 oder später.
Siehe [Was der eingebaute Shop kann](../../../../eng-us/help/businesses/stores/what-the-built-in-shop-does.md).

**Können meine Website und mein Shop dieselbe Website sein?**
Ja. Produkte sind Inhaltsseiten mit einem Preisfeld, bearbeitet mit denselben Werkzeugen wie der Rest der Website.
Siehe [Produktklassen und Katalog](produktklassen-und-katalog.md).

**Kann ich mehrere Shops betreiben, zum Beispiel einen pro Land oder Marke?**
Exponential betreibt mehrere Websites aus einer Installation, jede mit eigenen Einstellungen, etwa ihrer Länder-Rangliste.
Siehe [Mehrere Marken planen](../../../../eng-us/help/businesses/multisite/multi-brand-planning.md) und [Ländergruppen und Regionen](laendergruppen-und-regionen.md).

## Produkte und Preise

**Kann ich in mehreren Währungen verkaufen?**
Ja, mit dem Preisfeld für mehrere Währungen, eigenen oder automatischen Preisen pro Währung und Wechselkursen.
Siehe [Preise und Währungen](preise-und-waehrungen.md).

**Können Produkte Größen und Farben haben?**
Ja, mit Optionsfeldern, die den Preis ändern können. Siehe [Produktklassen und Katalog](produktklassen-und-katalog.md#optionen-und-varianten).

**Behandelt der Shop die USt für verschiedene Länder?**
Er berechnet, was Sie konfigurieren: USt-Typen und USt-Regeln nach Land des Kunden und Produktkategorie. Welche Sätze
richtig sind, ist eine Frage für Ihren Steuerberater. Siehe [USt-Regeln und USt-Typen](../../../../eng-us/help/businesses/stores/vat-rules-and-vat-types.md).

## Kasse und Kunden

**Müssen Kunden ein Konto anlegen?**
Nein. Mit der ausgelieferten Einstellung kann jeder mit einem Formular bestellen; Sie können stattdessen Konten
verlangen. Siehe [Kundenkonten und Gastbestellung](kundenkonten-und-gastbestellung.md).

**Finden Gäste ihre Bestellung später wieder?**
Ja, wenn Sie dauerhafte Belege einschalten: einen signierten Link, der jahrelang auf jedem Gerät funktioniert.
Siehe [Bestellbelege und Bestätigungsmails](bestellbelege-und-bestaetigungsmails.md).

**Was kostet der Versand?**
Von Haus aus ein pauschaler Betrag pro Bestellung, den Sie festlegen. Regeln nach Gewicht, Zone oder Bestellwert
brauchen einen Versand-Handler. Siehe [Versand und Bearbeitung](versand-und-bearbeitung.md).

## Zahlungen

**Welche Zahlungsanbieter funktionieren?**
PayPal über `ezpaypal`, das mit Exponential installiert wird, und Authorize.Net über `ezauthorize`, das vorgeschlagen
wird. Andere Anbieter brauchen eine Erweiterung für die Anbindung. Siehe [Zahlungsanbieter für Unternehmen](zahlungsanbieter-fuer-unternehmen.md).

**Laufen Kartennummern über meine Website?**
Nicht bei einem Anbieter mit Weiterleitung wie PayPal. Bei einem Kartenformular auf der Website wie ezauthorize schon,
und der Umgang des Codes mit Kartendaten muss vor dem Start geprüft werden. Siehe [Kartenzahlungen und PCI-Grundlagen](kartenzahlungen-und-pci-grundlagen.md).

**Kann ich Bestellungen ohne Online-Zahlung annehmen, auf Rechnung oder bei Abholung?**
Ja: Ohne Zahlungsschritt werden Bestellungen unbezahlt aufgegeben, und Sie kümmern sich selbst um die Zahlung. Das
Dashboard erinnert Sie daran, dass kein Zahlungsschritt konfiguriert ist. Siehe [Zahlungsanbieter für Unternehmen](zahlungsanbieter-fuer-unternehmen.md#wo-die-zahlung-hineinpasst).

## Bestellungen und tägliche Arbeit

**Wie sehe ich, was zu tun ist?**
Das Shop-Dashboard listet die Bestellungen auf, die auf den Shop warten, die am längsten wartenden zuerst, und schlägt
nächste Schritte vor. Siehe [Shop-Dashboard und Berichte](shop-uebersicht-und-berichte.md).

**Welche Bestellstatus gibt es?**
Siebzehn, von Pending und Paid über Packed und Shipped bis Completed, Cancelled und Refunded, dazu Ihre eigenen.
Siehe [Bestellungen und Bestellstatus](bestellungen-und-bestellstatus.md).

**Kann ich einschränken, was eine Packhilfe tun darf?**
Ja. Die Berechtigung, Status zu ändern, lässt sich auf bestimmte Ausgangs- und Zielstatus beschränken.
Siehe [Bestellungen und Bestellstatus](bestellungen-und-bestellstatus.md#wer-einen-status-ändern-darf).

## Fehlende Funktionen

**Führt der Shop einen Lagerbestand?**
Nein. Die Lagerverwaltung gehört zu den Dingen, die als Erweiterung oder Integration ergänzt werden.
Siehe [Was der eingebaute Shop kann](../../../../eng-us/help/businesses/stores/what-the-built-in-shop-does.md#what-is-not-included).

**Können Kunden einen Gutscheincode eingeben?**
Nein. Rabatte folgen Kundengruppen. Gutscheincodes brauchen eine Erweiterung.
Siehe [Rabatte und Wunschlisten](rabatte-und-wunschlisten.md#was-nicht-enthalten-ist).

**Kann ich Downloads verkaufen, die nach der Zahlung automatisch ausgeliefert werden?**
Nicht von Haus aus. Die Teile gibt es (geschützte Dateien, Benutzergruppen, Kassen-Workflows); sie zu verbinden ist
Handarbeit oder eine kleine Erweiterung. Siehe [Digitale Produkte und Downloads](digitale-produkte-und-downloads.md).

**Kann ich Bestellungen in meine Buchhaltungssoftware exportieren?**
Nicht aus der ausgelieferten Bestellliste. Die Anbindung an die Buchhaltung ist Arbeit für eine Erweiterung oder einen
Partner. Siehe [Shop-Dashboard und Berichte](shop-uebersicht-und-berichte.md#was-das-dashboard-nicht-tut).

**Wer kann ergänzen, was fehlt?**
Ein Entwickler oder ein Partner. Siehe [Einen Partner auswählen](../auswahl/einen-partner-auswaehlen.md) und
[Zusammenarbeit mit 7x und dem Exponential-Projekt](../auswahl/zusammenarbeit-mit-7x-und-dem-projekt.md).

## Kosten, Sicherheit und Zuverlässigkeit

**Was kostet der Betrieb eines Shops?**
Vor allem Hosting, Einrichtung und laufende Pflege, da es keine Lizenzgebühr gibt. Rechnen Sie es mit Ihren eigenen
Zahlen durch. Siehe [Gesamtbetriebskosten](../kosten/gesamtbetriebskosten.md) und [Durchgerechnetes Beispiel: ein kleiner Shop](../kosten/durchgerechnetes-beispiel-kleiner-shop.md).

**Wie halte ich den Shop sicher?**
Persönliche Konten mit zweistufiger Anmeldung, eingeschränkte Shop-Berechtigungen, HTTPS, Updates und getestete
Backups. Siehe [Sicherheitscheckliste für Shops](../../../../eng-us/help/businesses/stores/store-security-checklist.md).

**Bleibt der Shop während eines Ausverkaufs schnell?**
Öffentliche Seiten lassen sich cachen; Warenkorb und Kasse nicht, planen Sie also Kapazität für sie ein und messen Sie
vor der Spitze. Siehe [Shop-Leistung zu Spitzenzeiten](shop-leistung-zu-spitzenzeiten.md).

**Wo melde ich ein Sicherheitsproblem in Exponential?**
Vertraulich, an die Adresse in [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md), nie in einem öffentlichen Issue.

## Verwandte Seiten

- [Online-Shops und kleine Geschäfte](README.md)
- [FAQ für Unternehmen](../faq-fuer-unternehmen.md)
- [FAQ für Website-Ersteller](../../../../eng-us/help/creators/website-creators/website-creator-faq.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
