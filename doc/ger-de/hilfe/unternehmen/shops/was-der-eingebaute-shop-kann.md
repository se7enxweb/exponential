---
title: Was der eingebaute Shop kann
audience: businesses, sales-marketing, project-manager
category: business-solutions
order: 20
summary: Ein Rundgang für Geschäftsinhaber durch den Shop, der mit Exponential ausgeliefert wird - seine Teile, der Weg des Kunden, die Werkzeuge der Shop-Mitarbeiter und was er ohne Erweiterung nicht kann.
keywords: eingebauter Shop, E-Commerce-Funktionen, Funktionen des Web-Shops, Shop-Modul, Warenkorb, Kasse, Bestellungen, USt, Währungen, Rabatte, Wunschliste, Grenzen, built-in shop, e-commerce features, webshop features, shop module, basket, checkout, orders, VAT, currencies, discounts, wishlist, limits
type: help
---
# Was der eingebaute Shop kann

Diese Seite richtet sich an Geschäftsinhaber und Entscheider, die wissen möchten, was sie bekommen, wenn sie mit
Exponential verkaufen. Sie beschreibt in einfacher Sprache jeden Teil des Shops, der im Kernel mitgeliefert wird, und
sagt ehrlich, was nicht enthalten ist. Jeder Abschnitt verweist auf die Seite mit den Einzelheiten.

## Inhalt

1. [In einem Absatz](#in-einem-absatz)
2. [Die Teile des Shops](#die-teile-des-shops)
3. [Was Ihre Kunden sehen](#was-ihre-kunden-sehen)
4. [Was Ihre Mitarbeiter verwenden](#was-ihre-mitarbeiter-verwenden)
5. [Erweiterungen, die ihn ergänzen](#erweiterungen-die-ihn-ergänzen)
6. [Was nicht enthalten ist](#was-nicht-enthalten-ist)
7. [Warum er zu kleinen Unternehmen passt](#warum-er-zu-kleinen-unternehmen-passt)

## In einem Absatz

Der Shop ist Teil von Exponential selbst, kein Zusatz: Das README nennt „Advanced e-Commerce Stores (Built in)“
([README.md](https://github.com/se7enxweb/exponential/blob/main/README.md)), und das Shop-Modul mit all seinen Ansichten ist in `kernel/shop/module.php`
definiert. Produkte sind Inhalte wie jede andere Seite, mit einem Preisfeld. Kunden füllen einen Warenkorb, gehen zur
Kasse, bestätigen, bezahlen über einen Zahlungsanbieter und erhalten eine Bestätigungsmail. Die Mitarbeiter verwalten
Bestellungen, Kunden, USt, Währungen und Rabatte im Tab **Shop** der Administration, der mit einem Dashboard öffnet
([Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md)).

## Die Teile des Shops

| Teil | Was er für Sie tut | Lesen Sie |
|---|---|---|
| Produkte | Jede Inhaltsklasse mit einem Preisfeld wird verkäuflich | [Produktklassen und Katalog](produktklassen-und-katalog.md) |
| Preise | Ein Preis oder ein Preis pro Währung, mit Optionen, die den Preis ändern | [Preise und Währungen](preise-und-waehrungen.md) |
| USt | Benannte USt-Sätze und Regeln nach Land des Kunden und Produktkategorie | [USt-Regeln und USt-Typen](ust-regeln-und-ust-typen.md) |
| Länder | Eine gruppierte Länderliste mit den häufigsten Ländern zuerst | [Ländergruppen und Regionen](laendergruppen-und-regionen.md) |
| Versand | Ein Workflow für pauschale Versandkosten oder ein eigener Versand-Handler | [Versand und Bearbeitung](versand-und-bearbeitung.md) |
| Warenkorb und Kasse | In den Warenkorb legen, Mengen, Kundendaten, Bestätigung, Zahlung | [Warenkorb und Bestellablauf](warenkorb-und-bestellablauf.md) |
| Bestellungen | Bestellliste, Bestellansicht, siebzehn Status, Archiv, Kunden | [Bestellungen und Bestellstatus](bestellungen-und-bestellstatus.md) |
| Belege und Mails | Eine Bestätigungsmail und auf Wunsch ein dauerhafter Beleg-Link | [Bestellbelege und Bestätigungsmails](bestellbelege-und-bestaetigungsmails.md) |
| Dashboard | Kennzahlen, Diagramme, Aufgaben und Hinweise zur Einrichtung auf einer Seite | [Shop-Dashboard und Berichte](shop-uebersicht-und-berichte.md) |
| Zahlung | Zahlungsanbieter laufen als Workflow-Schritt an der Kasse | [Zahlungsanbieter für Unternehmen](zahlungsanbieter-fuer-unternehmen.md) |
| Rabatte | Prozentuale Rabatte für Gruppen von Kunden | [Rabatte und Wunschlisten](rabatte-und-wunschlisten.md) |
| Wunschliste | Angemeldete Kunden merken sich Produkte für später | [Rabatte und Wunschlisten](rabatte-und-wunschlisten.md) |

## Was Ihre Kunden sehen

1. Eine Produktseite mit einer Schaltfläche **Zum Warenkorb hinzufügen**.
2. Einen Warenkorb mit Artikeln, Mengen und Summen.
3. Ein Formular für Name, E-Mail-Adresse und Anschrift (oder eine Anmeldung).
4. Eine Bestätigungsseite mit Versand und USt.
5. Die Seite oder das Formular des Zahlungsanbieters.
6. Die aufgegebene Bestellung, eine Bestätigungsmail und, wenn Sie es einschalten, einen Beleg-Link, der nie abläuft.

Die mitgelieferten Designs enthalten diese Seiten: Die Themes ezwebin und simple bringen die Shop-Templates mit
([Einen Shop betreiben: Überblick](../../../../eng-us/help/creators/website-creators/shop-overview.md)), und das Design standard hat den
vollständigen Satz der `shop/`-Templates in `design/standard/templates/shop/`.

## Was Ihre Mitarbeiter verwenden

- **Shop-Dashboard**: Bestellungen heute, in 7 und 30 Tagen, Umsatz pro Währung, offene Bestellungen, abgebrochene
  Warenkörbe, meistverkaufte Produkte und Vorschläge unter „Nächste Schritte“, die aus Ihren Daten und Einstellungen
  abgeleitet sind.
- **Bestellliste**: nach jeder Spalte sortierbar ([Sortierung der Bestellliste](../../../../eng-us/features/6.0/order-list-sorting.md)).
- **Bestellansicht** mit Statusänderungen und Statusverlauf (`eZOrderStatusHistory`, `kernel/classes/ezorderstatushistory.php`).
- **Archiv** für abgeschlossene Bestellungen, **Kundenliste**, **Statistik** pro Jahr und Monat.
- **USt-Typen und -Regeln**, **Währungen**, **Produktkategorien**, **Rabattgruppen**.
- **Produktübersicht**: die Produkte und ihre Preise in einer Liste (`shop/productsoverview`).

Jede Aktion braucht eine Richtlinie, Sie entscheiden also, wer was sieht ([Shop-Planung für kleine Unternehmen](shop-planung-fuer-kleine-unternehmen.md#personen-und-rollen)).
Der Audit-Trail zeichnet Handelsereignisse auf: Bestellungen, Zahlungen (nie Kartendaten), USt, Währungen und Rabatte
([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).

## Erweiterungen, die ihn ergänzen

| Erweiterung | Ergänzt | Status im Projekt |
|---|---|---|
| `ezpaypal` | Zahlungen mit PayPal | vom `composer.json` des Hauptverzeichnisses verlangt (`se7enxweb/ezpaypal`) |
| `ezauthorize` | Kartenzahlungen über Authorize.Net | im `composer.json` vorgeschlagen |
| `ezstarrating` | Sternebewertungen, auch für Produkte | [ezstarrating](../../../../eng-us/features/6.0/extensions/ezstarrating.md) |
| `ezfind` | Solr-Suche mit Facetten | im `composer.json` vorgeschlagen |
| `cjw_newsletter` | Newsletter an Kunden | [cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md) |
| `xrowmetadata` | Seitenmetadaten und Sitemaps | [xrowmetadata](../../../../eng-us/features/6.0/extensions/xrowmetadata.md) |

## Was nicht enthalten ist

Ehrlichkeit spart Geld. Der technische Überblick nennt diese Punkte als Dinge, die als Erweiterungen oder
Integrationen zu planen sind: **Lagerverwaltung, Rechnungen in einem Buchhaltungssystem, Versandetiketten und
Marktplätze** ([Einen Shop betreiben: Überblick](../../../../eng-us/help/creators/website-creators/shop-overview.md)). Im Code des
Kernels haben wir außerdem festgestellt:

- keine Gutschein- oder Aktionscodes: Rabatte sind an Kundengruppen gebunden, nicht an Codes, die an der Kasse
  eingegeben werden;
- keinen Export von Bestellungen aus der Bestellliste in eine Tabellendatei;
- keine automatische Auslieferung einer gekauften Datei nach der Zahlung
  ([Digitale Produkte und Downloads](digitale-produkte-und-downloads.md));
- nur eine mitgelieferte Versandart: ein pauschaler Betrag pro Bestellung
  ([Versand und Bearbeitung](versand-und-bearbeitung.md)).

Jeder dieser Punkte lässt sich mit einer Erweiterung, einem Workflow-Ereignis oder einem eigenen Handler ergänzen;
das ist Arbeit für Anwendungsentwickler ([App-Entwickler: hier beginnen](../../../../eng-us/help/creators/app-creators/app-creators-start-here.md))
oder eine Aufgabe für einen Partner ([Einen Partner auswählen](../auswahl/einen-partner-auswaehlen.md)).

## Warum er zu kleinen Unternehmen passt

- Ein System für Website und Shop: Produkte sind Seiten, Redakteure verwenden also für beides dieselben Werkzeuge.
- Kein separates Shop-Produkt, das lizenziert werden muss: Exponential steht unter GPL v2 oder später
  (`composer.json`, `license` `GPL-2.0-or-later`); siehe [Wofür Sie nicht bezahlen](../kosten/wofuer-sie-nicht-bezahlen.md).
- Das Dashboard sagt einem kleinen Team, was Aufmerksamkeit braucht, ohne dass Berichte gebaut werden müssen.
- Langlebiger Code: Die Klassen des Shops tragen Copyright-Zeilen, die bis 1998 zurückreichen (zum Beispiel
  `kernel/classes/ezvattype.php`), und das Release 6.0.15 hat ihn noch erweitert
  ([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md)).

## Verwandte Seiten

- [Einen Shop betreiben: Überblick](../../../../eng-us/help/creators/website-creators/shop-overview.md)
- [Wie Unternehmen mit Exponential online verkaufen](../wie-unternehmen-online-verkaufen.md)
- [Shop-FAQ](shop-faq.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
