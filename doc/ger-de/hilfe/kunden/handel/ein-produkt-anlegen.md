---
title: Ein Produkt anlegen
audience: customer, editor
category: customer-success
order: 30
summary: Die Checkliste, die ein Shop-Team für jedes neue Produkt in einem Exponential-Shop verwendet - was vorzubereiten ist, welche Felder auszufüllen sind, Preis und Mehrwertsteuer, Optionen, ein Test im Warenkorb und wer das Produkt freigibt, bevor es online geht.
keywords: Produkt anlegen, neues Produkt, Produkt-Checkliste, Produktseite, Preis, USt-Typ, Optionen, Artikelnummer, Produktbilder, Produkt veröffentlichen, add product, new product, product checklist, SKU, VAT type, publish product
type: help
---
# Ein Produkt anlegen

Diese Seite ist für die Produktredaktion und die Shop-Leitung, die neue Produkte freigibt. Sie ist die Checkliste des
Betreibers für jedes neue Produkt: was bereitliegen muss, was auszufüllen ist und was zu testen ist, bevor Kunden es
kaufen können. Die Klicks zum Anlegen und Veröffentlichen von Inhalten stehen unter [Inhalte anlegen und bearbeiten](../../inhalte-bearbeiten.md)
und [Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md); diese Seite wiederholt sie nicht.

## Bevor Sie beginnen

- [ ] Der Produktordner, in den es gehört, existiert (ein Ordner pro Kategorie ist die übliche Struktur,
  [Produktklassen und Katalog](../../unternehmen/shops/produktklassen-und-katalog.md)).
- [ ] Sie haben die Berechtigung, in diesem Ordner Inhalte der Produktklasse anzulegen.
- [ ] Texte, Bilder und der Preis sind abgestimmt. Preise und Mehrwertsteuer entscheidet der Eigentümer, nicht die Redaktion.
- [ ] Die Artikelnummer stimmt mit der in Ihrer Buchhaltung oder Lagerliste überein.

## Die Felder

Die Felder hängen davon ab, wie Ihre Produktklasse aufgebaut ist. In der Demo-Installation unter
<https://alpha.se7enx.com> hat die Klasse Product einen Namen, eine Artikelnummer, eine Kurzbeschreibung, eine
Beschreibung, einen Preis, Bilder, zusätzliche Optionen, Tags, eine Sternebewertung, Metadaten und eine
Tabelle mit technischen Daten ([Produktklassen und Katalog](../../unternehmen/shops/produktklassen-und-katalog.md)). Ihre eigene Klasse kann abweichen.

| Feld (typisch) | Ausfüllen | Prüfen |
|---|---|---|
| Name | Der Name, nach dem Kunden suchen | Kurz, keine internen Kürzel |
| Artikelnummer | Ihre Artikelnummer oder SKU | Gleich wie in Ihrer Buchhaltung |
| Kurzbeschreibung | Ein oder zwei Sätze für Listen | Liest sich gut auf dem Smartphone |
| Beschreibung | Der vollständige Text | Größen, Materialien, Lieferumfang |
| Preis | Der Betrag, der USt-Typ und ob er die Mehrwertsteuer enthält | Siehe nächster Abschnitt |
| Bilder | Verknüpfte Bildobjekte | Alternativtext ausgefüllt ([Medien und Uploads](../../medien-und-uploads.md)) |
| Optionen | Größen, Farben, Extras, mit Preisunterschieden | Im Warenkorb getestet |
| Tags oder Schlagwörter | Wörter für Suche und Filter | Die Wörter, die Ihre Kunden verwenden |
| Metadaten | Titel und Beschreibung für Suchmaschinen | Keine Kopie des Namens |

## Preis und Mehrwertsteuer

Bei einem Preis in einer einzigen Währung (`ezprice`) nennt jeder Preis einen USt-Typ und gibt an, ob der Betrag die
Mehrwertsteuer enthält ([Preise und Währungen](../../unternehmen/shops/preise-und-waehrungen.md)).

- [ ] Der USt-Typ ist der richtige für dieses Produkt, oder „Determined by VAT charging rules“, wenn Ihr Shop
  USt-Regeln verwendet (der Name kommt aus `settings/shop.ini [VATSettings] DynamicVatTypeName`).
- [ ] Mit USt-Regeln: Das Feld für die Produktkategorie ist gesetzt, sonst können die Regeln nicht erkennen, welcher
  Satz gilt ([USt-Typen und USt-Regeln](ust-typen-und-ust-regeln.md)).
- [ ] „Preis enthält Mehrwertsteuer“ ist genauso gesetzt wie bei Ihren anderen Produkten.
- [ ] Bei Preisen in mehreren Währungen (`ezmultiprice`): eigene Preise für Ihre Hauptmärkte, automatische Preise für
  den Rest ([Preise und Währungen](preise-und-waehrungen.md)).

## Optionen

Der Kernel hat vier Optionsfelder: `ezoption`, `ezmultioption`, `ezmultioption2` und `ezrangeoption`
(`kernel/classes/datatypes/`). Eine Option kann den Preis ändern, und die Auswahl wird mit dem Warenkorbartikel gespeichert.

- [ ] Jede Option, die ein Kunde wählen kann, können Sie auch tatsächlich liefern.
- [ ] Preisaufschläge sind mit derselben Mehrwertsteuer-Behandlung erfasst wie der Grundpreis.
- [ ] Keiner der Optionsnamen muss am Telefon erklärt werden.

## Testen, bevor es online geht

Sehen Sie sich zuerst die Vorschau des Entwurfs an ([Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md)). Nach dem Veröffentlichen:

- [ ] Das Produkt erscheint in seinem Ordner, im Menü und in der Suche.
- [ ] **Zum Warenkorb hinzufügen** funktioniert, mit jeder Option.
- [ ] Der Warenkorb zeigt den richtigen Preis; die Bestätigungsseite zeigt die richtige Mehrwertsteuer und die richtigen Versandkosten.
- [ ] Das Shop-Dashboard zählt es nicht zu den „Produkten ohne Preis“
  ([Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md)).

Sie müssen nicht für jedes Produkt eine echte Bestellung aufgeben. Eine Testbestellung lohnt sich bei einer neuen Art
von Produkt, einem neuen USt-Typ oder einem neuen Optionsfeld ([Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md)).

## Wer freigibt

| Produkt | Vorgeschlagene Freigabe |
|---|---|
| Ein neuer Artikel in einem bestehenden Sortiment | Die Redaktion veröffentlicht; die Shop-Leitung prüft den Preis am selben Tag |
| Ein neues Sortiment oder eine neue Kategorie | Der Eigentümer genehmigt Preis und Mehrwertsteuer vor dem Veröffentlichen |
| Ein Produkt mit einem neuen Steuersatz oder einem neuen Optionsfeld | Eigentümer, dazu eine Testbestellung |

Wenn Ihre Website einen Freigabe-Workflow für die Produktordner hat, geht der Entwurf automatisch an die freigebende
Person, sobald die Redaktion veröffentlicht ([Workflows](../../../anleitungen/workflows.md)).

## Verwandte Seiten

- [Produkte und Katalog](produkte-und-katalog.md)
- [Inhalte anlegen und bearbeiten](../../inhalte-bearbeiten.md)
- [Produktklassen und Katalog (für Unternehmen)](../../unternehmen/shops/produktklassen-und-katalog.md)
- [Produkte, Preise und Mehrwertsteuer](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
