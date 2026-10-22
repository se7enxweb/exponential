---
title: FAQ zu E-Commerce
audience: ecommerce, customer, businesses
category: e-commerce
order: 90
summary: Kurze Antworten, jeweils mit Quelle, auf die Fragen, die Händler, Shop-Manager, Entwickler und Agenturen zum Verkauf mit Exponential stellen - was der Shop enthält und was ihm fehlt, Zahlungen, USt., Versand, Bestellungen, Daten, mehrere Websites, Leistung und Erweiterung.
keywords: FAQ, Fragen zu E-Commerce, Shop, Zahlungsanbieter, PayPal, Authorize.Net, USt., Umsatzsteuer, Versand, Gutscheine, Lager, Bestellungen, Export, Belege, Multisite, Headless, erweitern, Migration, e-commerce questions, payment providers, VAT, shipping, coupons, stock, orders, receipts, extending
type: help
---
# FAQ zu E-Commerce

Kurze Antworten auf die Fragen, die am häufigsten zum Verkauf mit Exponential gestellt werden. Jede Antwort verweist
auf die Seite mit den Einzelheiten. Für Fragen zum täglichen Betrieb eines Shops siehe auch die
[Shop-FAQ für Inhaber](../kunden/handel/shop-faq.md) und die [Shop-FAQ](../unternehmen/shops/shop-faq.md) für
Entscheider.

## Inhalt

1. [Was der Shop ist](#was-der-shop-ist)
2. [Was der Shop nicht kann](#was-der-shop-nicht-kann)
3. [Zahlungen](#zahlungen)
4. [Steuern und Versand](#steuern-und-versand)
5. [Bestellungen und Daten](#bestellungen-und-daten)
6. [Websites, Apps und Wachstum](#websites-apps-und-wachstum)
7. [Bauen und erweitern](#bauen-und-erweitern)

## Was der Shop ist

**Ist der Shop eine Erweiterung, die ich installieren muss?**
Nein. Er ist Teil des Kernels: das Modul `shop` mit seinen Views in `kernel/shop/module.php`, seinen Klassen in
`kernel/classes/` und seinen Einstellungen in `settings/shop.ini`. Sie schalten die Teile ein, die Sie brauchen
([Was der eingebaute Shop kann](../unternehmen/shops/was-der-eingebaute-shop-kann.md)).

**Was macht Inhalte zu einem Produkt?**
Ein Preisfeld: Jede Klasse mit einem Attribut `ezprice` oder `ezmultiprice` ist eine Produktklasse
([Katalogmodellierung](katalog/README.md)).

**Wo arbeiten die Mitarbeitenden?**
Im Reiter **Web-Shop** der Administration, der das Shop-Dashboard öffnet
([Pfad für Shop-Manager](rollen/pfad-fuer-shop-manager.md)).

## Was der Shop nicht kann

**Was ist nicht enthalten?**
Lagerverwaltung, Gutschein- oder Rabattcodes, Rechnungen in einem Buchhaltungssystem, Versandetiketten, Marktplätze,
automatische Auslieferung von Dateien nach der Zahlung und Versand über einen Pauschalbetrag hinaus ohne Handler. Jedes
davon lässt sich als Erweiterung ergänzen ([Entscheidungs-Checklisten](betrieb/entscheidungs-checklisten.md#über-den-kernel-hinaus)).

**Kann ich Bestellungen aus der Bestellliste in eine Tabelle exportieren?**
Nein. Bestelldaten stehen über die Bestelldienste von `expservices` als JSON zur Verfügung, aus denen ein Skript eine
Tabelle schreiben kann ([Shop-Kennzahlen und Exporte](betrieb/shop-kennzahlen-und-exporte.md)).

## Zahlungen

**Welche Zahlungsanbieter werden unterstützt?**
Das Repository enthält `ezpaypal` (PayPal, Weiterleitung) und `ezauthorize` (Authorize.Net, Kartenformular auf Ihrer
Website). Andere Anbieter brauchen ein dafür geschriebenes Gateway
([Entwicklung von Zahlungs-Gateways](erweitern/entwicklung-von-zahlungs-gateways.md)).

**Wie ist die Zahlung mit der Kasse verbunden?**
Als Workflow-Ereignis „Payment Gateway“ am Vorher-Trigger von `shop_checkout`
([Das Ereignis Zahlungs-Gateway](kasse/das-ereignis-zahlungs-gateway.md)).

**Können Kunden zwischen mehreren Anbietern wählen?**
Ja. Wählen Sie im Ereignis mehrere Gateways aus; der Kunde wählt dann eines auf `workflow/selectgateway.tpl`.

**Ein Kunde hat bezahlt, aber es gibt keine Bestellung. Warum?**
Der Rückruf des Anbieters hat die Website nicht erreicht, daher wurde die temporäre Bestellung nie aktiviert
([Fehlersuche an der Kasse](betrieb/fehlersuche-an-der-kasse.md#kunde-hat-bezahlt-aber-keine-bestellung)).

## Steuern und Versand

**Berechnet der Shop die USt. nach Land?**
Ja, mit USt-Regeln nach Land und Produktkategorie, sobald `shop.ini [VATSettings] Handler=ezdefault` gesetzt ist
([Produktkategorien und USt-Regeln](katalog/produktkategorien-und-ust-regeln.md)).

**Warum erhalten manche Kunden 0 Prozent USt.?**
Keine USt-Regel passte zu ihrem Land und zur Kategorie des Produkts. Fügen Sie eine Auffangregel hinzu.

**Wie wird der Versand berechnet?**
Als Pauschalbetrag pro Bestellung mit dem Ereignis „Simple shipping“ oder beliebig anders mit einem Versand-Handler
([Das Ereignis einfacher Versand](kasse/das-ereignis-einfacher-versand.md), [Eigener Versand-Handler](erweitern/eigener-versand-handler.md)).

## Bestellungen und Daten

**Lassen sich Mitarbeitende im Versand auf ihre eigenen Schritte beschränken?**
Ja, mit Richtlinien für `shop/setstatus`, begrenzt durch `FromStatus` und `ToStatus`
([Statusübergänge und Berechtigungen](bestellungen/statusuebergaenge-und-berechtigungen.md)).

**Was ist der Unterschied zwischen Archivieren und Entfernen einer Bestellung?**
Archivieren blendet eine abgeschlossene Bestellung aus der Liste aus und behält sie; Entfernen löscht sie endgültig
([Archivieren, Entfernen und Aufräumen](bestellungen/archivieren-entfernen-und-aufraeumen.md)).

**Können Gäste ihre Bestellung später wiederfinden?**
Ja, mit dauerhaften Belegen: `shop.ini [OrderViewSettings] OrderLinkView=orderreceipt`
([Bestellmails und Belege für Betreiber](bestellungen/bestellmails-und-belege-fuer-betreiber.md)).

**Werden Kartendaten gespeichert?**
Der Kernel speichert keine Kartendaten, und der Prüfpfad zeichnet Zahlungen ohne sie auf
([Prüfpfad für den Handel](betrieb/audit-trail-fuer-den-handel.md)). Mit einem Kartenformular auf Ihrer Website darf auch
Ihr Gateway-Code sie nicht speichern.

## Websites, Apps und Wachstum

**Kann ich mehrere Shops in einer Installation betreiben?**
Ja, mit Siteaccesses; sie teilen sich Bestellungen, USt., Währungen und Rabatte in einer Datenbank
([Mehrere Shops und Multisite](betrieb/mehrere-shops-und-multisite.md)).

**Kann eine App den Shop nutzen?**
Ja, über die Handelsdienste von `expservices` (Produkte, Warenkorb, Bestellungen und mehr) oder die REST-API für
Inhalte ([Headless Commerce mit expservices](betrieb/headless-commerce-mit-expservices.md)).

**Kann das Marketing Aktionsseiten ohne Entwickler bauen?**
Ja, mit dem Layout-Editor, einschließlich zeitlich begrenzter Regeln
([Content Commerce mit Layouts](betrieb/content-commerce-mit-layouts.md)).

## Bauen und erweitern

**Wie importiere ich einen Katalog?**
Mit `bin/php/ezcsvimport.php` für einfache Kataloge oder mit Code für alles, was vorhandene Produkte aktualisieren muss
([Produktimporte und Migrationen](betrieb/produktimporte-und-migrationen.md)).

**Wohin gehören meine Handler und Gateways?**
In eine eigene Erweiterung, ausgewählt über Einstellungen; nie in `kernel/` ([Den Shop erweitern](erweitern/README.md)).

**Gibt es einen Generator für Handler?**
Ja: den RAD-Assistenten für Handler (`setup/handlerextension/<kind>`) und den Assistenten für Workflow-Ereignisse
(`setup/workflowevent`) ([Pfad für Entwickler](rollen/pfad-fuer-entwickler.md#generatoren-die-zeit-sparen)).

## Verwandte Seiten

- [E-Commerce: hier beginnen](e-commerce-hier-beginnen.md)
- [Glossar zu E-Commerce](glossar-zu-e-commerce.md)
- [FAQ für Unternehmen](../unternehmen/faq-fuer-unternehmen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
