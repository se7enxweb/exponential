---
title: Content-Commerce mit Layouts
audience: ecommerce, editor, businesses
category: e-commerce
order: 74
summary: Mit Inhalten verkaufen in Exponential - Landing- und Kampagnenseiten, die mit dem Layouts-Editor gebaut werden und Produkte auflisten, zeitlich begrenzte Regeln für Aktionen, redaktionelle Inhalte neben Produkten und die Grenze zwischen Layouts und den eigenen Templates des Shops.
keywords: Content-Commerce, Landingpage, Kampagnenseite, Produktlisten-Block, Collection, dynamische Collection, Layout-Regel, Zeitbedingung, Aktion, Sale, redaktionell, Merchandising, content commerce, Layouts, landing page, campaign page, product list block, collection, dynamic collection, layout rule, time condition, sale, editorial, explayouts, merchandising
type: help
---
# Content-Commerce mit Layouts

Diese Seite richtet sich an Händler, Marketingverantwortliche und Redakteure, die Seiten möchten, die verkaufen: eine
Kampagnenseite für eine Aktion, einen Geschenkratgeber, eine Kategorieseite mit redaktioneller Einleitung. In
Exponential sind Produkte Inhalte, sodass der Layouts-Editor sie wie jeden anderen Inhalt auf Seiten platzieren kann.
Diese Seite zeigt, wie, und wo die eigenen Templates des Shops übernehmen.

## Inhalt

1. [Warum es funktioniert](#warum-es-funktioniert)
2. [Eine Landingpage für eine Aktion](#eine-landingpage-für-eine-aktion)
3. [Muster](#muster)
4. [Wo Layouts aufhören](#wo-layouts-aufhören)
5. [Checkliste](#checkliste)

## Warum es funktioniert

Der Layouts-Editor ordnet **Blöcke** in **Zonen** an; Listenblöcke zeigen eine **Collection** von Inhalten, entweder von
Hand ausgewählt (manuell) oder über eine Abfrage gefunden (dynamisch); **Regeln** entscheiden, welche Seite welches
Layout bekommt, und können zeitlich begrenzt werden
([Landingpages mit dem Layouts-Editor](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md#die-fünf-grundbegriffe)).
Produkte sind Inhaltsobjekte in Ordnern, sodass eine Collection „Unterelemente des Ordners Sale“ oder sechs von Hand
ausgewählte Produkte sein kann. Ein Klick auf ein Produkt führt zu seiner vollständigen Ansicht mit der Schaltfläche
**Zum Warenkorb hinzufügen**.

## Eine Landingpage für eine Aktion

1. Legen Sie ein Landingpage-Objekt an, zum Beispiel `/Spring-sale`.
2. Legen Sie im Editor ein Layout mit einer Hero-Zone und einer Hauptzone an.
3. Oben: ein Hero-Block mit der Botschaft und einer Schaltfläche.
4. Hauptzone: ein Listenblock mit einem Raster-Ansichtstyp und einer dynamischen Collection, „Unterelemente des Ordners
   Sale“, Limit 12.
5. Fügen Sie einen Rich-Text-Block mit den Bedingungen der Aktion hinzu: Daten, Versand, Rückgaben.
6. Unter **Mappings** eine Regel für die Landingpage mit einer Zeitbedingung (von, bis), damit das Layout nur während der
   Aktion gilt ([Layout-Regeln](../../ersteller/website-ersteller/layout-regeln.md)).
7. Vorschau, veröffentlichen, und prüfen Sie die Seite abgemeldet.

Der Aktionspreis selbst wird nicht durch das Layout gesetzt: Ändern Sie die Preise der Produkte oder verwenden Sie
Rabattgruppen für Kundengruppen ([Rabatte](../../kunden/handel/rabatte.md)). Gutscheincodes gibt es im Kernel nicht.

## Muster

| Muster | Blöcke und Collections |
|---|---|
| Kategorieseite mit Einleitung | Rich Text, dann ein Listenblock mit „Unterelemente dieses Kategorieordners“ |
| Geschenkratgeber | Mehrere betitelte Listenblöcke mit manuellen Collections |
| Neuheiten | Dynamische Collection, sortiert nach Veröffentlichungsdatum |
| Bestseller | Ein Template-Block mit `fetch( 'shop', 'best_sell_list', ... )` aus den Bestelldaten des Shops (`kernel/shop/function_definition.php`) |
| „Auch gekauft“ auf Produktseiten | Ein Template-Block mit `fetch( 'shop', 'related_purchase', ... )` |
| Gemeinsamer Werbe-Header | Ein gemeinsames Layout, das in viele Seiten verlinkt ist ([Gemeinsamer Header und Footer](../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md)) |

Bestseller und „auch gekauft“ brauchen einen von einem Entwickler geschriebenen Template-Block (einen `tpl_block`), da sie
aus Bestelldaten statt aus dem Inhaltsbaum stammen.

## Wo Layouts aufhören

| Layouts | Shop-Templates |
|---|---|
| Landing-, Kampagnen-, Kategorie- und Startseiten | Vollständige Produktansicht mit dem Formular „Zum Warenkorb hinzufügen“ |
| Redaktionelle Inhalte rund um Produkte | Warenkorb, Kundendaten, Bestätigung, Kasse |
| Zeitlich begrenzte Anordnungen | Bestellansicht, Beleg, Bestellmail |

Warenkorb und Kasse sind Shop-Views mit eigenen Templates; halten Sie sie aus Layouts heraus
([Landingpages mit dem Layouts-Editor](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md#wann-layouts-und-wann-templates)).

## Checkliste

- [ ] Jedes gezeigte Produkt ist veröffentlicht und hat einen Preis.
- [ ] Die Bedingungen der Aktion stehen auf der Seite, nicht nur im Bild.
- [ ] Zeitbedingungen beginnen und enden, wenn sich die Preise ändern.
- [ ] Nach einer Preisänderung zeigen die Listen der Landingpage abgemeldet die neuen Preise; falls nicht, prüfen Sie
  die Caches ([Caching für Verfügbarkeit](../../unternehmen/zuverlaessigkeit/caching-fuer-verfuegbarkeit.md)).
- [ ] Die Seite besteht die Prüfungen zur Barrierefreiheit ([Barrierefreiheit im Shop](barrierefreiheit-im-shop.md)).

## Verwandte Seiten

- [Der Layouts-Editor](../../layout-editor.md)
- [Ihren Shop bewerben](../../unternehmen/shops/ihren-shop-bewerben.md)
- [Layout-Typen, Zonen und Blöcke](../../ersteller/website-ersteller/layouttypen-zonen-und-bloecke.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
