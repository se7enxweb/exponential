---
title: Eine Datenkatalogseite
audience: government, website-creator, editor
category: public-sector
order: 90
summary: Wie Sie in Exponential die Seite bauen, die alle Datensätze einer öffentlichen Stelle auflistet - ein Katalogordner, eine Listenansicht mit Lizenz und Format, Filter nach Thema mit Tags, ein auf den Katalog beschränktes Suchfeld und der Link zum Feed.
keywords: Datenkatalog, Katalogseite, Liste der Datensätze, Ordner, Zeilenansicht, Template-Override, eztags, Filter, Thema, Suche, Open-Data-Portal, data catalog, catalog page, dataset list, folder, line view, template override, filter, theme, search, open data portal
type: help
---
# Eine Datenkatalogseite

Diese Seite richtet sich an Websiteersteller und Redaktionen, die die Einstiegsseite für die offenen Daten einer
öffentlichen Stelle bauen. In Exponential ist ein Katalog ein Ordner, dessen Unterobjekte Datensatzseiten sind; Ihr
Design entscheidet, wie die Liste aussieht. Die Template-Techniken stammen aus den Seiten für Websiteersteller, die in
jedem Schritt verlinkt sind.

## Inhalt

1. [Was die Katalogseite zeigen sollte](#was-die-katalogseite-zeigen-sollte)
2. [Der Katalogordner](#der-katalogordner)
3. [Die Liste der Datensätze](#die-liste-der-datensätze)
4. [Nach Thema filtern](#nach-thema-filtern)
5. [Den Katalog durchsuchen](#den-katalog-durchsuchen)
6. [Der Link zum Feed](#der-link-zum-feed)
7. [Checkliste](#checkliste)

## Was die Katalogseite zeigen sollte

- Eine kurze Einleitung: welche Daten die Stelle veröffentlicht, unter welchen Lizenzen und wie man mehr anfordert.
- Die Liste der Datensätze, die neuesten zuerst oder alphabetisch, jeweils mit Titel, einer einzeiligen Beschreibung,
  den Themen, den Formaten, der Lizenz und dem Datum der letzten Änderung.
- Möglichkeiten, die Liste einzugrenzen: Themen, ein Suchfeld.
- Ein Feed neuer Datensätze und das Kontaktpostfach.

## Der Katalogordner

1. Legen Sie in der **Inhaltsstruktur** einen **Ordner** namens „Offene Daten“ an der Stelle der Navigation an, an
   die er passt.
2. Schreiben Sie die Einleitung in die Beschreibung des Ordners.
3. Stellen Sie die Sortierung des Ordners auf die gewünschte Reihenfolge ein (zum Beispiel nach Änderung, die neuesten
   zuerst).
4. Veröffentlichen Sie Datensätze als Unterobjekte (siehe „Datensatzdateien veröffentlichen“).

## Die Liste der Datensätze

Die Vollansicht des Ordners listet seine Unterobjekte auf. Geben Sie der Datensatzklasse eine eigene **Zeilenansicht**
(line), damit jede Zeile zeigt, was Nachnutzer brauchen. Überschreiben Sie in Ihrem Design `node/view/line.tpl` für
die Klasse `dataset` ([Template-Overrides](../../ersteller/website-ersteller/template-overrides.md)):

```text
<article class="dataset-line">
  <h3><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h3>
  {attribute_view_gui attribute=$node.data_map.description}
  <dl>
    <dt>{'License'|i18n( 'design/mysite' )}</dt><dd>{attribute_view_gui attribute=$node.data_map.license}</dd>
    <dt>{'Last changed'|i18n( 'design/mysite' )}</dt><dd>{attribute_view_gui attribute=$node.data_map.modified}</dd>
  </dl>
</article>
```

Verwenden Sie `|wash` für jeden ausgegebenen Wert und übersetzen Sie die Bezeichnungen mit `|i18n`, damit der Katalog
in jeder Sprache der Website funktioniert
([Eine mehrsprachige Website bauen](../../ersteller/website-ersteller/mehrsprachige-websites.md)). Halten Sie die Liste
barrierefrei: eine Überschrift pro Datensatz, echte Links, keine Information allein über Farbe
([Barrierefreiheit für Websiteersteller](../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)).

## Nach Thema filtern

Mit der Erweiterung `eztags` gibt ein Tags-Feld an der Datensatzklasse jedem Tag eine eigene Seite, die alles damit
Verschlagwortete auflistet, und Synonyme lassen Menschen ein Thema unter einem anderen Wort finden
([Verschlagworten mit eztags](../../ersteller/website-ersteller/verschlagworten-mit-eztags.md)). Verlinken Sie die Themen-Tags
von der Katalogseite aus. Ohne `eztags` sind ein Auswahlfeld und ein Ordner pro Thema eine einfachere Alternative.

## Den Katalog durchsuchen

Ein Suchfeld, das `SearchText` an `content/search` sendet, durchsucht die ganze Website. Um nur den Katalog zu
durchsuchen, fügen Sie dem Formular ein verstecktes Feld `SubTreeArray` mit der Knoten-ID des Katalogordners hinzu: Die
Ansicht `content/search` liest es und beschränkt die Ergebnisse auf diesen Teilbaum
(`kernel/private/classes/views/content/search.php`) ([Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md)).
Mit Solr (`ezfind`) stehen Facetten nach Klasse, Tag oder Datum zur Verfügung
([Suche mit ezfind und Solr](../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md)).

Markieren Sie die Datensatzfelder, nach denen Menschen suchen, in der Klasse als **Durchsuchbar**: Titel,
Beschreibung und Schlüsselwörter.

## Der Link zum Feed

Legen Sie einen RSS-Export des Katalogordners an (siehe „Feeds für neue und geänderte Datensätze“) und verlinken Sie
ihn von der Katalogseite aus mit einem sichtbaren Link und einem Element
`<link rel="alternate" type="application/rss+xml">` im Kopf der Seite.

## Checkliste

- [ ] Die Einleitung sagt, was veröffentlicht wird, unter welchen Lizenzen und wie man mehr anfordert.
- [ ] Jede Zeile zeigt Titel, Beschreibung, Lizenz, Formate und letzte Änderung.
- [ ] Themen sind Tags oder Auswahlwerte aus einer kontrollierten Liste.
- [ ] Der Katalog funktioniert abgemeldet, auf einem Telefon und in jeder Sprache der Website.
- [ ] Der Link zum Feed und das Kontaktpostfach stehen auf der Seite.

## Verwandte Seiten

- [Template-Overrides](../../ersteller/website-ersteller/template-overrides.md)
- [Menüs und Navigation](../../ersteller/website-ersteller/menues-und-navigation.md)
- [Den Inhaltsbaum strukturieren](../../ersteller/website-ersteller/den-inhaltsbaum-strukturieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
