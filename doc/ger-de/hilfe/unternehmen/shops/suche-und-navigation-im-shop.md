---
title: Suche und Navigation im Shop
audience: businesses, website-creator, editor
category: business-solutions
order: 180
summary: Kunden helfen, Produkte in einem Exponential-Shop zu finden - Kategorien und Menüs aus dem Inhaltsbaum, mehrere Platzierungen pro Produkt, Tags, die eingebaute Suche, Solr mit Facetten über ezfind und Bewertungen.
keywords: Produktsuche, Shop-Navigation, Kategorien, Menüs, Brotkrumen, Tags, eztags, ezfind, Solr, Facetten, Filter, Suche auf der Website, Sternebewertung, product search, store navigation, categories, menus, breadcrumbs, facets, filters, site search, star rating
type: help
---
# Suche und Navigation im Shop

Diese Seite richtet sich an Geschäftsinhaber und Redakteure, die möchten, dass Kunden schnell das richtige Produkt
finden. Sie erklärt die Navigationswerkzeuge eines Exponential-Shops, wann die eingebaute Suche genügt und wann sich
Solr lohnt, und wie der Katalog beim Wachsen leicht zu durchstöbern bleibt.

## Inhalt

1. [Zwei Wege, auf denen Kunden Produkte finden](#zwei-wege-auf-denen-kunden-produkte-finden)
2. [Kategorien und Menüs](#kategorien-und-menüs)
3. [Ein Produkt an mehreren Stellen](#ein-produkt-an-mehreren-stellen)
4. [Tags als zweiter Weg](#tags-als-zweiter-weg)
5. [Die eingebaute Suche](#die-eingebaute-suche)
6. [Solr-Suche mit Facetten](#solr-suche-mit-facetten)
7. [Bewertungen und technische Daten](#bewertungen-und-technische-daten)
8. [Checkliste](#checkliste)

## Zwei Wege, auf denen Kunden Produkte finden

Manche Kunden stöbern: Sie klicken sich durch Kategorien. Andere suchen: Sie tippen ein, was sie möchten. Ein guter
Shop bedient beide, und in Exponential kommen beide aus denselben Inhalten, sodass ein einmal angelegtes Produkt ohne
Zusatzarbeit in Menüs und Suchergebnissen erscheint.

## Kategorien und Menüs

Menüs kommen aus dem Inhaltsbaum: Jede Kategorie ist ein Ordner, und das Menü zeigt die Ordner unterhalb eines
Startpunkts ([Menüs und Navigation](../../../../eng-us/help/creators/website-creators/menus-and-navigation.md)). Für einen Shop gilt:

- beschränken Sie die oberste Ebene auf die Handvoll Kategorien, in denen Kunden denken;
- ordnen Sie Kategorien nach Priorität, nicht alphabetisch;
- zeigen Sie Brotkrumen, damit Kunden wissen, wo sie sind, und eine Ebene nach oben gehen können;
- blenden Sie leere oder saisonale Kategorien aus, statt sie zu löschen.

Welche Klassen in Menüs erscheinen, ist eine Einstellung (`menu.ini`), sodass Produktseiten das Menü nicht überfüllen
müssen. Der Baum selbst wird unter [Den Inhaltsbaum strukturieren](../../../../eng-us/help/creators/website-creators/structuring-the-content-tree.md)
geplant.

## Ein Produkt an mehreren Stellen

Ein Inhaltsobjekt kann mehr als eine Platzierung haben. Ein Produkt kann in seiner eigenen Kategorie und unter „Neu
eingetroffen“ oder „Geschenke unter 50“ stehen, ohne kopiert zu werden, sodass eine Preisänderung nur einmal erfolgt.
Entfernen Sie die zusätzliche Platzierung, wenn die Kampagne endet.

## Tags als zweiter Weg

Tags laufen quer zu den Kategorien: Marke, Material, Anlass, Farbe. Mit `eztags` werden Tags zentral verwaltet, lassen
sich übersetzen und als Tag-Seiten und Tag-Wolken anzeigen
([Verschlagworten mit eztags](../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md)). Vereinbaren Sie ein kleines
Tag-Vokabular, bevor die Redakteure beginnen, sonst wächst die Tag-Liste schneller als der Katalog.

## Die eingebaute Suche

Die eingebaute Suchmaschine hält ihren Index in der Datenbank und genügt für die meisten Websites
([Suche auf der Website](../../../../eng-us/help/creators/website-creators/site-search.md)). Sie durchsucht die durchsuchbaren Felder
der Produkte (auf alpha markiert die Produktklasse Name, Artikelnummer, Beschreibungen und technische Daten als
durchsuchbar; den Preis nicht) und führt Suchstatistiken, sodass Sie sehen, wonach Kunden gesucht und was sie nicht
gefunden haben.

> **Tipp**
> Prüfen Sie die Suchstatistiken jeden Monat. Ein Suchbegriff ohne Ergebnisse ist ein Produkt, das Sie nicht haben,
> oder ein Wort, das Ihre Produktseiten noch nicht verwenden.

## Solr-Suche mit Facetten

Große Kataloge und Kunden, die filtern möchten, verlangen nach Solr über die Erweiterung `ezfind`
([Suche mit ezfind und Solr](../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md)). Sie ergänzt:

| Mit Solr | Nutzen für einen Shop |
|---|---|
| Facetten (Filter nach Klasse, Tag, Datum) | „Nur diese Marke anzeigen“ |
| Abstimmung der Relevanz | Die besten Treffer zuerst |
| Dateiinhalte | Handbücher und Datenblätter werden über ihren Text gefunden |
| Tag-Wolken | Ein visueller Weg in den Katalog |

`ezfind` wird im `composer.json` des Hauptverzeichnisses vorgeschlagen (`se7enxweb/ezfind`) und braucht einen
Solr-Server, den Ihr Hoster betreibt ([Such-Backends](../../hosting/grundlagen/such-backends.md)). Templates für Facetten
sind Designarbeit.

## Bewertungen und technische Daten

- Die Erweiterung `ezstarrating` fügt ein Feld für Sternebewertungen hinzu und lässt Templates nach Bewertung sortieren
  und abrufen ([ezstarrating](../../../../eng-us/features/6.0/extensions/ezstarrating.md)); auf alpha hat die Produktklasse eines.
- Eine Tabelle mit technischen Daten (`ezmatrix` in der Produktklasse von alpha) lässt Kunden Produkte auf der Seite
  vergleichen.

## Checkliste

- [ ] Kategorien der obersten Ebene vereinbart und nach Priorität geordnet.
- [ ] Brotkrumen auf Produkt- und Kategorieseiten.
- [ ] Kampagnenseiten verwenden zusätzliche Platzierungen, keine Kopien.
- [ ] Ein kurzes Tag-Vokabular ist aufgeschrieben.
- [ ] Die Suche findet Produkte nach Name und Artikelnummer; mit typischen Wörtern von Kunden getestet.
- [ ] Suchstatistiken werden monatlich durchgesehen.
- [ ] Solr wird in Betracht gezogen, sobald der Katalog oder der Bedarf an Filtern wächst.

## Verwandte Seiten

- [Produktklassen und Katalog](produktklassen-und-katalog.md)
- [Ihren Shop bewerben](ihren-shop-bewerben.md)
- [Katalogsuche](../bibliotheken/katalogsuche.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
