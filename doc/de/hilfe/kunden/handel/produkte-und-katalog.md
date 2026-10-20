---
title: Produkte und Katalog
audience: customer, editor, businesses
category: customer-success
order: 20
summary: Den Produktkatalog eines Exponential-Shops nach dem Start in gutem Zustand halten - wer was pflegt, die regelmäßigen Katalogprüfungen, ausverkaufte und saisonale Produkte und was Sie Ihren Partner fragen, wenn sich die Produktklasse ändern muss.
keywords: Produktkatalog, Katalogpflege, Produkte, Produktklasse, ausverkauft, saisonale Produkte, Produktordner, Katalogprüfungen, Produktredaktion, product catalog, catalog upkeep, products, product class, sold out, seasonal products, product folders, catalog checks, product editors
type: help
---
# Produkte und Katalog

Diese Seite ist für Eigentümer und Shop-Leitungen, die für den Katalog verantwortlich sind, sobald der Shop läuft. Es
geht um die Pflege: wer sich um Produkte kümmert, welche Prüfungen regelmäßig anstehen und wie Sie mit Produkten
umgehen, die ausverkauft sind, zurückkommen oder sich ändern. Wie Produkte in Exponential abgebildet werden, ist für
Entscheider unter [Produktklassen und Katalog](../../../../en/us/help/businesses/stores/product-classes-and-catalog.md) erklärt.

## Ein Produkt ist eine Seite

In Exponential ist ein Produkt ein Inhalt einer Klasse mit einem Preisfeld (`ezprice` oder `ezmultiprice`)
([Einen Shop betreiben: Überblick](../../../../en/us/help/creators/website-creators/shop-overview.md)). Für das Team bedeutet das:

- die Produktredaktion verwendet denselben Editor, dieselben Entwürfe, Versionen und dieselbe Vorschau wie für jede
  andere Seite ([Inhalte anlegen und bearbeiten](../../../../en/us/help/editing-content.md), [Veröffentlichen und Versionen](../../../../en/us/help/publishing-and-versions.md));
- eine falsche Änderung lässt sich rückgängig machen, indem man zu einer früheren Version zurückkehrt;
- Produkte können an mehreren Stellen des Menüs zugleich stehen (ein Objekt, mehrere Orte), sodass Ordner wie „Neu“
  und „Sale“ keine Kopien brauchen.

## Wer was pflegt

| Aufgabe | Wer | Was benötigt wird |
|---|---|---|
| Produkttexte, Bilder, Optionen | Produktredaktion | Inhalte in den Produktordnern anlegen und bearbeiten ([Rollen und Richtlinien](../../../../en/us/guides/roles-and-policies.md)) |
| Preise an Produkten | Produktredaktion oder Eigentümer | Inhalte bearbeiten; der Preis ist ein Feld des Produkts |
| USt-Typen, Produktkategorien, Währungen | Eigentümer | `shop/setup` (`kernel/shop/module.php`) |
| Neue Felder in der Produktklasse | Partner oder Administrator | Klassenbearbeitung unter **Setup > Klassen** |
| Katalogprüfungen | Shop-Leitung | `shop/administrate` für das Dashboard |

Entscheiden Sie, ob Preisänderungen einen Freigabeschritt durchlaufen. Wenn ja, kann Ihr Administrator einen
Freigabe-Workflow für die Produktordner einrichten ([Workflows](../../../../en/us/guides/workflows.md)).

## Regelmäßige Katalogprüfungen

| Wie oft | Prüfung | Wo |
|---|---|---|
| Wöchentlich | Produkte ohne Preis | Das Shop-Dashboard zählt sie ([Shop-Dashboard und Bestellstatus](../../../../en/us/features/6.0/store-dashboard.md)) |
| Wöchentlich | Ausverkaufte Artikel verborgen oder gekennzeichnet | Die Produktordner |
| Wöchentlich | Preise und USt-Typen neuer Produkte | **Shop > Produktübersicht** (`shop/productsoverview`) listet Produkte mit ihren Preisen |
| Monatlich | Bestseller weiterhin leicht zu finden | Rangliste im Dashboard, Startseite, Menüs |
| Monatlich | Bilder und Dateien, die niemand mehr verwendet | [Die Inhaltsprüfungen](../../../../en/us/features/6.0/content-checks.md) |
| Vierteljährlich | Suchbegriffe ohne Treffer | [Website-Suche](../../../../en/us/help/creators/website-creators/site-search.md) |
| Jährlich | Kategorien, Tags und Menüs passen noch zum Sortiment | Inhaltsstruktur |

## Ausverkaufte und saisonale Produkte

Der Shop hat keine Lagerverwaltung ([Was der eingebaute Shop leistet](../../../../en/us/help/businesses/stores/what-the-built-in-shop-does.md)),
daher ist „ausverkauft“ etwas, das Ihr Team von Hand erledigt. Einigen Sie sich auf einen Weg und verwenden Sie ihn
jedes Mal:

| Situation | Ein gängiger Ansatz | Wie |
|---|---|---|
| Für ein paar Tage nicht vorrätig | Seite stehen lassen, einen Hinweis in den Text schreiben | Das Produkt bearbeiten ([Inhalte anlegen und bearbeiten](../../../../en/us/help/editing-content.md)) |
| Länger nicht vorrätig | Das Produkt verbergen, sodass es aus Listen und Suche verschwindet | [Ein Element verbergen](../../../../en/us/help/publishing-and-versions.md#hide-an-item) |
| Saisonales Sortiment | Am Ende der Saison verbergen, im nächsten Jahr wieder einblenden | Verbergen und einblenden; Preise und Texte bleiben erhalten |
| Endgültig eingestellt | Verbergen oder in einen Archivordner verschieben; nur entfernen, wenn Sie sicher sind | [Entfernen und wiederherstellen](../../../../en/us/help/publishing-and-versions.md#remove-and-restore) |

> **Hinweis**
> Bevor ein Produkt entfernt oder verborgen wird, listet die Administration die Seiten auf, die darauf verweisen
> (Artikel, die darauf verlinken, verwandte Produkte). Das ist die ausgelieferte Einstellung,
> `content.ini [RemoveSettings] ReverseRelationCheck=warn`
> ([Entfernen und Verbergen: die Prüfung umgekehrter Beziehungen](../../../../en/us/features/6.0/reverse-relation-check.md)).

Für ein Produkt, das verkauft wurde, ist Verbergen meist besser als Entfernen: Alte Bestellungen behalten ihre
Bestellpositionen so oder so, aber ein verborgenes Produkt behält seine Adresse und seine Historie, falls es
zurückkommt.

## Wenn sich die Produktklasse ändern muss

Neue Felder (ein zweites Bild, eine Größentabelle, eine Kategorie für USt-Regeln) sind Änderungen an der
Produktklasse. Das ist Arbeit für Ihren Administrator oder Partner, nicht für die Redaktion. Fragen Sie:

- [ ] Müssen bestehende Produkte bearbeitet werden, um das neue Feld auszufüllen?
- [ ] Zeigt das Produkt-Template auf der Website das neue Feld an?
- [ ] Wird die Änderung auf einer Kopie getestet, bevor sie den Live-Shop erreicht?
- [ ] Wirkt sich die Änderung auf USt-Regeln, Preise oder Optionen im Warenkorb aus?

Das Preisfeld von einer Währung (`ezprice`) auf mehrere (`ezmultiprice`) umzustellen bedeutet, jedes Produkt zu
bearbeiten; planen Sie es daher wie ein kleines Projekt ([Preise und Währungen](preise-und-waehrungen.md)).

## Viele Produkte auf einmal

Das Verschieben, Kopieren oder Entfernen einer großen Zahl von Produkten kann im Hintergrund als Inhaltsauftrag
laufen, der in Stapeln arbeitet und nach einer Unterbrechung fortgesetzt wird ([Inhaltsaufträge](../../../../en/us/features/6.0/content-jobs.md)).
Mehrere ausgewählte Unterelemente lassen sich in einem Schritt kopieren ([Ausgewählte Unterelemente kopieren](../../../../en/us/features/6.0/subitems-copy-selected.md)).

## Verwandte Seiten

- [Ein Produkt anlegen](ein-produkt-anlegen.md)
- [Produktklassen und Katalog (für Unternehmen)](../../../../en/us/help/businesses/stores/product-classes-and-catalog.md)
- [Suche und Navigation im Shop](../../../../en/us/help/businesses/stores/store-search-and-navigation.md)
- [Produkte, Preise und Mehrwertsteuer](../../../../en/us/help/creators/website-creators/products-prices-and-vat.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
