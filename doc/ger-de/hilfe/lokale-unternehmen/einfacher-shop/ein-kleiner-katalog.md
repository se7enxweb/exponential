---
title: Ein kleiner Katalog
audience: local-business, businesses, customer, editor
category: local-business
order: 42
summary: Einen kleinen Produktkatalog für ein lokales Unternehmen mit der mitgelieferten Produktklasse einrichten und pflegen - Produkte, Gutscheine und Geschenkkarten, Optionen wie Größen, Fotos und Beschreibungen, ausverkaufte und saisonale Artikel und eine Produkt-Checkliste.
keywords: Katalog, Produkte, Produktklasse, Gutscheine, Geschenkkarten, Optionen, Preis, Fotos, ausverkauft, saisonale Produkte, ezprice, ezoption, lokales Unternehmen, catalog, products, product class, vouchers, gift cards, options, price, sold out, seasonal products, local business
type: help
---
# Ein kleiner Katalog

Diese Seite ist für Inhaber oder Redakteure eines Shops eines lokalen Unternehmens mit einer Handvoll bis einigen
hundert Produkten. In Exponential ist ein Produkt ein gewöhnlicher Inhalt: ein Objekt einer Klasse mit einem
Preisfeld, abgelegt in einem Ordner. Die Seitenpakete von `ezwebin` liefern eine Klasse `product` mit
(`extension/ezwebin/packages/product-1.0-1.ezpkg`), und Ersteller passen sie an
([Produktklassen und Katalog](../../unternehmen/shops/produktklassen-und-katalog.md)).

## Inhalt

1. [Woraus ein Produkt besteht](#woraus-ein-produkt-besteht)
2. [Gutscheine und Geschenkkarten](#gutscheine-und-geschenkkarten)
3. [Optionen: Größen, Geschmacksrichtungen, Farben](#optionen-größen-geschmacksrichtungen-farben)
4. [Ordner und Menüs](#ordner-und-menüs)
5. [Ausverkaufte und saisonale Produkte](#ausverkaufte-und-saisonale-produkte)
6. [Ein Produkt hinzufügen, Schritt für Schritt](#ein-produkt-hinzufügen-schritt-für-schritt)

## Woraus ein Produkt besteht

| Teil | Datentyp | Tipp für ein lokales Unternehmen |
|---|---|---|
| Name | Textzeile | wonach Menschen suchen würden: „Roggensauerteigbrot, 1 kg“ |
| Kurzbeschreibung | Formatierter Text | ein oder zwei Sätze, die wichtigsten Fakten |
| Beschreibung | Formatierter Text | Zutaten, Materialien, Pflege, Allergene, wo relevant |
| Preis | Preis (`ezprice`) oder Mehrfachpreis (`ezmultiprice`) | der Preis, mit dem gewählten USt-Typ ([Preise und Währungen](../../unternehmen/shops/preise-und-waehrungen.md)) |
| Bild | Bild | Ihr eigenes Foto vor einem schlichten Hintergrund ([Fotos, die verkaufen](../marketing/fotos-die-verkaufen.md)) |
| Produktnummer | Textzeile | optional, für Ihre eigenen Unterlagen |

Die Felder Ihrer Produktklasse können abweichen; Ihr Ersteller legt sie mit Ihnen fest.

## Gutscheine und Geschenkkarten

Ein Gutschein ist ebenfalls ein Produkt: Name „Geschenkgutschein 25“, Preis, ein Foto der gedruckten Karte. Was der
Shop **nicht** kann, ist Gutscheincodes zu erzeugen und zu prüfen: Ein System für Gutschein- oder Rabattcodes ist
nicht eingebaut ([Rabatte und Wunschlisten](../../unternehmen/shops/rabatte-und-wunschlisten.md)). Ein üblicher Weg für
kleine Unternehmen:

1. Der Kunde kauft das Gutscheinprodukt.
2. Mitarbeitende sehen die Bestellung, schreiben eine nummerierte Gutscheinkarte von Hand oder nach einer Vorlage und
   verschicken oder übergeben sie.
3. Die Nummern werden in einer einfachen Liste geführt; ein eingelöster Gutschein wird an der Kasse abgehakt.

Für mehrere Werte verwenden Sie ein Produkt mit einer Option oder ein Produkt pro Wert.

## Optionen: Größen, Geschmacksrichtungen, Farben

Produkte können Optionen haben, die der Kunde wählt, bevor er in den Warenkorb legt (Options-Datentypen wie
`ezoption`, `ezmultioption2` und `ezrangeoption` stehen in `kernel/classes/datatypes/`). Eine Option kann den Preis
ändern. Halten Sie die Optionen gering; eine lange Optionsliste auf einem Smartphone ermüdet. Einzelheiten:
[Produktklassen und Katalog](../../unternehmen/shops/produktklassen-und-katalog.md).

## Ordner und Menüs

Legen Sie Produkte in wenige Ordner („Brot“, „Kuchen“, „Geschenke“). Ordner werden zum Menü und zu den Adressen des
Shops (`/Shop/Gifts`). Ein Produkt kann über einen zweiten Platz in zwei Ordnern erscheinen, ohne kopiert zu werden
([Suche und Navigation im Shop](../../unternehmen/shops/suche-und-navigation-im-shop.md)).

## Ausverkaufte und saisonale Produkte

Der eingebaute Shop hat **keine Lagerzählung** (sie steht unter den Dingen, die er nicht kann:
[Was der eingebaute Shop kann](../../unternehmen/shops/was-der-eingebaute-shop-kann.md)). Für einen kleinen Katalog:

| Situation | Tun |
|---|---|
| Vorübergehend ausverkauft | das Produkt verstecken, oder „Ausverkauft“ an den Namen anhängen und die Warenkorb-Schaltfläche entfernen (Template, fragen Sie den Ersteller) |
| Saisonal (Weihnachtsplätzchen) | nach der Saison verstecken, im nächsten Jahr wieder einblenden; oder das Verstecken zu einem Datum planen ([Zeitgesteuertes Veröffentlichen](../../kunden/alltag/zeitgesteuertes-veroeffentlichen.md)) |
| Aus dem Sortiment genommen | in den Papierkorb verschieben, nachdem offene Bestellungen erledigt sind |

Verstecken bewahrt das Produkt und seine Adresse; Wiedereinblenden bringt es so zurück, wie es war.

## Ein Produkt hinzufügen, Schritt für Schritt

1. Vorbereiten: Name, kurzer Text, Beschreibung, Preis, USt-Typ, ein oder zwei Fotos.
2. Öffnen Sie in der Administration oder im Editor den richtigen Ordner, und legen Sie ein **Produkt** an.
3. Füllen Sie die Felder aus, laden Sie das Foto hoch, schreiben Sie einen Alternativtext.
4. Veröffentlichen Sie, öffnen Sie die Produktseite, legen Sie es in den Warenkorb, und prüfen Sie Preis und
   Mehrwertsteuer auf der Bestätigungsseite.
5. Entfernen Sie den Test-Warenkorb.

Die vollständige Routine steht in [Ein Produkt hinzufügen](../../kunden/handel/ein-produkt-anlegen.md).

## Verwandte Seiten

- [Brauchen Sie einen Shop?](brauchen-sie-einen-shop.md)
- [Produkte und Katalog](../../kunden/handel/produkte-und-katalog.md)
- [Einen Shop im Alltag betreiben](../../unternehmen/shops/einen-shop-im-alltag-betreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
