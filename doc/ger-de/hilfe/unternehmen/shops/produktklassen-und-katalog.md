---
title: Produktklassen und Katalog
audience: businesses, editor, website-creator
category: business-solutions
order: 30
summary: Wie Produkte in einem Exponential-Shop modelliert werden - die Produktklasse, Preis- und Optionsfelder, Ordner und Kategorien - und was ein Unternehmen über seinen Katalog entscheiden sollte.
keywords: Produktklasse, Katalog, Produktkatalog, Produktfelder, Optionen, Varianten, Artikelnummer, SKU, Produktkategorie, Ordner, Bilder, product class, catalog, product catalog, product fields, options, variants, product number, product category, folders, images
type: help
---
# Produktklassen und Katalog

Diese Seite richtet sich an Geschäftsinhaber und Redakteure, die den Shop mit Produkten füllen. Sie erklärt, was ein
Produkt in Exponential ist, welche Felder ein Produkt haben kann, wie Optionen und Varianten funktionieren und wie Sie
den Katalog so ordnen, dass Kunden und Mitarbeiter die Dinge finden. Die Einrichtung selbst ist unter
[Produkte, Preise und USt](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md) beschrieben.

## Inhalt

1. [Ein Produkt ist Inhalt](#ein-produkt-ist-inhalt)
2. [Die Produktklasse im Auslieferungszustand](#die-produktklasse-im-auslieferungszustand)
3. [Felder, die sich lohnen](#felder-die-sich-lohnen)
4. [Optionen und Varianten](#optionen-und-varianten)
5. [Den Katalog ordnen](#den-katalog-ordnen)
6. [Wer Produkte pflegt](#wer-produkte-pflegt)
7. [Checkliste für den Katalog](#checkliste-für-den-katalog)

## Ein Produkt ist Inhalt

In Exponential ist jede Seite ein Inhaltsobjekt einer **Klasse**, einer Vorlage, die festlegt, welche Felder es hat.
Ein Produkt ist einfach Inhalt einer Klasse mit einem Preisfeld (`ezprice` oder `ezmultiprice`)
([Einen Shop betreiben: Überblick](../../ersteller/website-ersteller/shop-ueberblick.md)). Das hat für ein Unternehmen
praktische Vorteile:

- Redakteure schreiben Produktseiten mit demselben Editor, denselben Versionen und derselben Vorschau wie jede andere
  Seite ([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)).
- Produkte lassen sich wie andere Inhalte übersetzen ([Mehrsprachige Websites](../../ersteller/website-ersteller/mehrsprachige-websites.md)).
- Produkte erscheinen ohne Zusatzarbeit in Menüs, in der Suche und in Feeds.
- Produkte lassen sich mit Artikeln, Anleitungen und anderen Produkten verknüpfen
  ([Beziehungen und Wiederverwendung](../../ersteller/website-ersteller/relationen-und-wiederverwendung.md)).

## Die Produktklasse im Auslieferungszustand

Auf der Installation alpha (https://alpha.se7enx.com) hat die Klasse **Product** diese Felder in dieser Reihenfolge:

| Feld | Datentyp | Verwendung |
|---|---|---|
| Name | `ezstring` | Der Produktname |
| Product number | `ezstring` | Ihre Artikelnummer oder SKU |
| Short description | `ezxmltext` | Anreißer für Listen |
| Description | `ezxmltext` | Der vollständige Text |
| Price | `ezprice` | Der Preis mit seinem USt-Typ |
| Image | `ezobjectrelationlist` | Bilder, als verknüpfte Bildobjekte |
| Caption (Image) | `ezxmltext` | Bildunterschrift |
| Additional options | `ezmultioption` | Auswahlmöglichkeiten, die den Preis ändern können |
| Tags | `ezkeyword` | Schlagwörter |
| Star Rating | `ezsrrating` | Kundenbewertungen ([ezstarrating](../../../funktionen/6.0/erweiterungen/ezstarrating.md)) |
| Meta Data | `xrowmetadata` | Metadaten für Suchmaschinen ([xrowmetadata](../../../funktionen/6.0/erweiterungen/xrowmetadata.md)) |
| Specifications | `ezmatrix` | Eine Tabelle mit technischen Daten |

Die Klasse ist ein Container, sodass ein Produkt Unterseiten wie Handbücher oder Zubehör enthalten kann. Die Klasse in
Ihrer Installation kann abweichen: Sie hängt vom installierten Seitenpaket ab
([Seitenpakete und mitgelieferte Themes](../../ersteller/website-ersteller/seitenpakete-und-mitgelieferte-themes.md)).

## Felder, die sich lohnen

Klassen lassen sich jederzeit unter **Setup > Klassen** ändern ([Inhaltsklassen für Websites](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md)).
Felder, die Unternehmen oft hinzufügen:

| Bedarf | Datentyp | Hinweis |
|---|---|---|
| Produktkategorie für USt-Regeln | `ezproductcategory` | Nur nötig, wenn die USt je nach Produktart abweicht ([USt-Regeln und USt-Typen](ust-regeln-und-ust-typen.md)) |
| Preise in mehreren Währungen | `ezmultiprice` statt `ezprice` | ([Preise und Währungen](preise-und-waehrungen.md)) |
| Handbuch zum Herunterladen | `ezbinaryfile` | ([Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md)) |
| Marke, Material, Farbe als Filter | `eztags` oder eine Auswahl | ([Verschlagworten mit eztags](../../ersteller/website-ersteller/verschlagworten-mit-eztags.md)) |
| Gewicht, für Versandregeln | `ezfloat` oder `ezinteger` | Nur mit einem eigenen Versand-Handler sinnvoll ([Versand und Bearbeitung](versand-und-bearbeitung.md)) |

Datentypen werden unter [Datentypen für Ihre Felder auswählen](../../ersteller/website-ersteller/datentypen-waehlen.md)
verglichen.

## Optionen und Varianten

Der Kernel liefert vier Options-Datentypen mit: `ezoption`, `ezmultioption`, `ezmultioption2` und `ezrangeoption`
(`kernel/classes/datatypes/`). Eine Option lässt den Kunden zum Beispiel eine Größe oder Farbe wählen und kann den
Preis ändern; die Wahl wird mit dem Artikel im Warenkorb gespeichert
([Produkte, Preise und USt](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md#optionen-und-varianten)).

Wann sollte eine Variante stattdessen ein eigenes Produkt sein?

| Verwenden Sie eine Option, wenn | Verwenden Sie ein eigenes Produkt, wenn |
|---|---|
| Die Varianten Bilder und Text gemeinsam haben | Jede Variante eigene Bilder oder eine eigene Beschreibung hat |
| Preisunterschiede einfache Aufschläge sind | Jede Variante für Ihr Lager eine eigene Artikelnummer braucht |
| Sie den Bestand nicht pro Variante führen | Nach den Varianten getrennt gesucht wird |

> **Hinweis**
> Der Shop hat keine Lagerverwaltung ([Was der eingebaute Shop kann](was-der-eingebaute-shop-kann.md#was-nicht-enthalten-ist)).
> Wenn der Bestand pro Variante wichtig ist, planen Sie eine Erweiterung oder eine Anbindung an Ihr
> Warenwirtschaftssystem.

## Den Katalog ordnen

- **Ordner** bilden die Hauptkategorien und das Menü: ein Ordner pro Kategorie, die Produkte darin
  ([Den Inhaltsbaum strukturieren](../../ersteller/website-ersteller/den-inhaltsbaum-strukturieren.md)).
- **Ein Produkt, mehrere Kategorien**: Ein Inhaltsobjekt kann mehrere Platzierungen haben, sodass ein Produkt unter
  „Neu“ und in seiner eigenen Kategorie stehen kann, ohne kopiert zu werden.
- **Tags** bieten einen zweiten Weg durch den Katalog (Marke, Material, Anlass).
- **Sektionen** trennen einen Großhandelskatalog vom öffentlichen, mit Berechtigungen pro Sektion
  ([Sektionen und Objektzustände](../../ersteller/website-ersteller/sektionen-und-objektzustaende.md)).
- **Landingpages** für Kampagnen und Saisons ([Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md)).

## Wer Produkte pflegt

| Aufgabe | Wer | Berechtigung |
|---|---|---|
| Produktseiten schreiben und aktualisieren | Redakteure | Inhalte anlegen und bearbeiten in den Produktordnern |
| Preise ändern | Redakteure oder der Shop-Inhaber | Inhalte bearbeiten; Preise sind Felder des Produkts |
| USt-Typen, Produktkategorien, Währungen anlegen | Shop-Inhaber | `shop/setup` |
| Prüfen, ob jedes Produkt einen Preis hat | Shop-Inhaber | das Dashboard zählt Produkte ohne Preis |

Das Shop-Dashboard meldet die Zahl der Produkte und der Produkte ohne Preis und listet die Produktklassen mit ihrem
Preisattribut auf ([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)).

## Checkliste für den Katalog

- [ ] Die Produktklasse hat jedes Feld, das Ihre Produktseiten brauchen.
- [ ] Jedes Produkt hat eine Artikelnummer, die zu Ihrer Buchhaltung passt.
- [ ] Preise sind gesetzt, und der USt-Typ jedes Preises stimmt.
- [ ] Optionen sind im Warenkorb mit ihren Preisänderungen getestet.
- [ ] Kategorien, Menüs und Tags sind abgestimmt, bevor die Massenerfassung beginnt.
- [ ] Bildgrößen sind einmal festgelegt ([Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md)).

## Verwandte Seiten

- [Produkte, Preise und USt](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md)
- [Suche und Navigation im Shop](suche-und-navigation-im-shop.md)
- [Ihr Inhaltsmodell entwerfen](../../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
