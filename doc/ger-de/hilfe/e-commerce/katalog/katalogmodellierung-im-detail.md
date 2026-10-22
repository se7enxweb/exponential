---
title: Katalogmodellierung im Detail
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 21
summary: Produktklassen für einen Exponential-Shop entwerfen - eine Klasse oder mehrere, die Felder eines Produkts, Artikelnummern, Relationen, Ordner und mehrere Orte, Übersetzungen und Modelle, die sich gut importieren und auswerten lassen.
keywords: Katalogmodellierung, Produktklasse, Entwurf von Inhaltsklassen, SKU, Artikelnummer, ezstring, ezisbn, Relationen, Bilder, Ordner, Orte, Übersetzungen, mehrsprachiger Katalog, importfreundlich, catalog modeling, product class, content class design, item number, relations, images, locations, translations, multilingual catalog, import friendly
type: help
---
# Katalogmodellierung im Detail

Diese Seite ist für Entwickler und Händler, die die Inhaltsklassen eines Shop-Katalogs entwerfen. Sie geht die
Entscheidungen in der Reihenfolge durch, in der sie meist getroffen werden, und erklärt jeweils die Abwägungen im
Inhaltsmodell von Exponential.

## Inhalt

1. [Eine Produktklasse oder mehrere](#eine-produktklasse-oder-mehrere)
2. [Die Felder eines Produkts](#die-felder-eines-produkts)
3. [Artikelnummern und Kennungen](#artikelnummern-und-kennungen)
4. [Bilder und verwandte Produkte](#bilder-und-verwandte-produkte)
5. [Ordner und mehrere Orte](#ordner-und-mehrere-orte)
6. [Übersetzungen](#übersetzungen)
7. [Modelle, die sich gut importieren und auswerten lassen](#modelle-die-sich-gut-importieren-und-auswerten-lassen)
8. [Ein durchgerechnetes Beispiel](#ein-durchgerechnetes-beispiel)

## Eine Produktklasse oder mehrere

Eine Klasse ist eine Menge von Feldern. Produkte, die unterschiedliche Felder brauchen, gehören in verschiedene
Klassen; Produkte, die sich nur in den Werten unterscheiden, gehören in eine.

| Situation | Modell |
|---|---|
| Alle Produkte werden gleich beschrieben | Eine Klasse, zum Beispiel `product` |
| Bücher und Kleidung: unterschiedliche Felder | Zwei Klassen, jede mit einem Preisfeld |
| Physische Produkte und Dienstleistungen | Zwei Klassen; Dienstleistungen brauchen weder Gewicht noch Maße |
| Produkte und Geschenkkarten | Getrennte Klassen; die Auslieferung einer Geschenkkarte ist individuelle Arbeit |

Jede Klasse mit einem Preisfeld ist eine Produktklasse. **Web-Shop > Produkt Übersicht** zeigt jeweils eine Klasse,
und das Dashboard listet die Produktklassen mit ihrem Preisattribut auf; einige klar benannte Klassen lassen sich daher
leichter betreiben als viele.

## Die Felder eines Produkts

| Feld | Datentyp | Hinweis |
|---|---|---|
| Name | `ezstring` | Wird im Warenkorb und in der Bestellung angezeigt; halten Sie ihn kurz und für die Mitarbeitenden eindeutig genug |
| Artikelnummer | `ezstring` (oder `ezisbn` für Bücher) | Siehe unten |
| Kurzbeschreibung | `ezxmltext` oder `eztext` | Wird in Listen und Blöcken von Landingpages verwendet |
| Beschreibung | `ezxmltext` | Die vollständige Ansicht |
| Preis | `ezprice` oder `ezmultiprice` | Genau einer pro Klasse ([Preisfelder im Vergleich](preisfelder-im-vergleich.md)) |
| Steuerklasse | `ezproductcategory` | Nur wenn die USt. von der Produktkategorie abhängt ([Produktkategorien und USt-Regeln](produktkategorien-und-ust-regeln.md)) |
| Optionen | `ezoption`, `ezmultioption`, `ezmultioption2`, `ezrangeoption` | Nur für Auswahlen, die den Preis ändern ([Optionen und Varianten](optionen-und-varianten.md)) |
| Bilder | `ezimage` oder Relationen zu Bildobjekten | Siehe unten |
| Gewicht, Maße | `ezfloat`, `ezinteger` | Nur wenn ein Versand-Handler sie lesen wird |

Wie Sie Datentypen allgemein wählen, steht unter
[Datentypen für Ihre Felder wählen](../../ersteller/website-ersteller/datentypen-waehlen.md).

## Artikelnummern und Kennungen

Der Kernel hat kein eigenes Feld für die Artikelnummer (SKU). Fügen Sie eines als `ezstring` hinzu und machen Sie es zu
einem Pflichtfeld. Danach suchen die Mitarbeitenden, darauf gleichen Importe ab, und das erwartet ein Buchhaltungs- oder
Lagersystem. Die Bestellung speichert Namen und Preis des Produkts in ihrer Produktsammlung; eine Artikelnummer im
Namen oder ein eigenes, in den Bestell-Templates sichtbares Feld hilft den Mitarbeitenden daher, Produkte in alten
Bestellungen zu erkennen.

## Bilder und verwandte Produkte

- Ein einzelnes Feld `ezimage` ist für einen kleinen Katalog am einfachsten.
- Eine Objektrelationsliste zu Bildobjekten lässt mehrere Produkte Bilder aus der Medienbibliothek teilen
  ([Relationen und Wiederverwendung](../../ersteller/website-ersteller/relationen-und-wiederverwendung.md)).
- Eine Objektrelationsliste zu anderen Produkten ergibt von Hand gewählte „verwandte Produkte“. Der Shop hat außerdem
  eine Fetch-Funktion, `fetch( 'shop', 'related_purchase', hash( 'contentobject_id', ..., 'limit', ... ) )`, die
  Produkte auflistet, die aus echten Bestellungen zusammen mit einem Produkt gekauft wurden
  (`kernel/shop/function_definition.php`).

## Ordner und mehrere Orte

Produkte liegen wie alle Inhalte in Ordnern. Ein Produkt kann mehrere Orte haben, sodass es ohne Kopie in „Schuhe“ und
in „Sale“ erscheinen kann. Behalten Sie einen Hauptort für seine Adresse und seine Brotkrumennavigation. Sektionen
können Produkte für Berechtigungen und für Rabattregeln gruppieren, die auf Sektionen zielen können
([Rabatte](../../kunden/handel/rabatte.md)).

## Übersetzungen

Ein in mehrere Sprachen übersetztes Produkt bleibt ein Produkt: Das Preisfeld und die Optionen sind Felder der
Übersetzung, prüfen Sie also die Preise in jeder Sprache, die Sie veröffentlichen. Mehrsprachige Websites beschreibt
[Mehrsprachige Websites](../../ersteller/website-ersteller/mehrsprachige-websites.md).

## Modelle, die sich gut importieren und auswerten lassen

- Halten Sie die Reihenfolge der Felder stabil: Der CSV-Import liest die Spalten in der Reihenfolge der Felder der
  Klasse ([Produktimporte und Migrationen](../betrieb/produktimporte-und-migrationen.md)).
- Verwenden Sie einfache Datentypen für Daten, die von außen kommen (Zeichenketten, Zahlen, Preise), und Rich Text nur
  für Beschreibungen.
- Geben Sie jeder Klasse und jedem Feld einen klaren Bezeichner; Templates, Importe und entfernte Dienste verwenden
  Bezeichner.
- Schreiben Sie keine Preise in Optionstexte: Optionen haben eigene Preisfelder.

## Ein durchgerechnetes Beispiel

Ein kleiner Shop verkauft gedruckte Bücher und Geschenksets, in einer Währung, mit einem ermäßigten USt-Satz für
Bücher:

| Klasse | Felder |
|---|---|
| `book` | title (`ezstring`), ISBN (`ezisbn`), author (`ezstring`), description (`ezxmltext`), price (`ezprice`), tax class (`ezproductcategory`), cover (`ezimage`) |
| `gift_set` | name (`ezstring`), item number (`ezstring`), description (`ezxmltext`), price (`ezprice`), tax class (`ezproductcategory`), image (`ezimage`), wrapping (`ezoption`) |

USt.: zwei USt-Typen (regulär und ermäßigt), zwei Produktkategorien („Bücher“, „Sonstiges“), USt-Regeln pro Land und
der eingeschaltete USt-Handler ([Produktkategorien und USt-Regeln](produktkategorien-und-ust-regeln.md)).

## Verwandte Seiten

- [Katalogmodellierung](README.md)
- [Inhaltsklassen für Websites](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md)
- [Ein Produkt hinzufügen](../../kunden/handel/ein-produkt-anlegen.md)
- [Suche und Navigation im Shop](../../unternehmen/shops/suche-und-navigation-im-shop.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
