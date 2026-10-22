---
title: SEO-Grundlagen
audience: cms-developer
category: cms-development
order: 45
summary: Für CMS-Entwickler die Suchmaschinenarbeit, die in den Bau einer Exponential-Website gehört. Die Seite behandelt saubere Adressen aus dem Baum, kanonische Links für Inhalte an mehreren Stellen, Titel und Beschreibungen aus Inhalten, den Datentyp xrowmetadata, XML-Sitemaps, robots.txt, Weiterleitungen nach Verschiebungen und Migrationen, Sprachfassungen und Geschwindigkeit.
keywords: SEO, Suchmaschinenoptimierung, URL-Aliase, kanonisch, canonical, Hauptknoten, Titel, Meta-Beschreibung, MetaDataArray, xrowmetadata, Sitemap, bcgooglesitemaps, robots.txt, Weiterleitungen, URL-Platzhalter, hreflang, Sprachumschalter, strukturierte Daten, Seitengeschwindigkeit, search engine optimization, URL aliases, main node, title, meta description, redirects, URL wildcards, language switcher, structured data, page speed
type: help
---
# SEO-Grundlagen

Diese Seite richtet sich an Entwickler, die Websites bauen, die Suchmaschinen verstehen müssen. Das meiste davon sind
keine Tricks, sondern Struktur: stabile Adressen, eine kanonische Adresse je Inhalt, ein echter Titel und eine echte
Beschreibung je Seite, eine Sitemap und Weiterleitungen, wenn sich etwas bewegt. Exponential liefert die Teile; Ihre
Templates und Einstellungen setzen sie zusammen.

## Inhalt

1. [Adressen](#adressen)
2. [Eine kanonische Adresse](#eine-kanonische-adresse)
3. [Titel und Beschreibungen](#titel-und-beschreibungen)
4. [Metadaten aus Inhalten: xrowmetadata](#metadaten-aus-inhalten-xrowmetadata)
5. [Sitemaps und robots.txt](#sitemaps-und-robotstxt)
6. [Weiterleitungen](#weiterleitungen)
7. [Sprachfassungen](#sprachfassungen)
8. [Geschwindigkeit und Markup](#geschwindigkeit-und-markup)
9. [Checkliste](#checkliste)

## Adressen

Jeder Knoten erhält eine lesbare Adresse aus seinem Pfad im Baum (`/news/opening-of-the-new-hall`), kleingeschrieben
und mit Bindestrichen, wie in `site.ini [URLTranslator]` konfiguriert. Ein Baum, der wie die Navigation der Website
entworfen ist, ergibt Adressen, die sich selbst erklären; vermeiden Sie tiefe Ordnerketten, die nur für Redakteure
existieren ([URL-Aliase und Routing](url-aliase-und-routing.md),
[Ein Inhaltsmodell entwerfen](ein-inhaltsmodell-entwerfen.md#schritt-5-den-baum-zeichnen)). Beschränken Sie View-Parameter
(`/(offset)/10`) auf das Blättern und bekannte Filter.

## Eine kanonische Adresse

Ein Objekt mit mehreren Platzierungen hat mehrere Adressen. Geben Sie im Pagelayout oder in der Full View einen
kanonischen Link auf den Hauptknoten aus:

```smarty
<link rel="canonical" href={$node.object.main_node.url_alias|ezurl( 'double', 'full' )}>
```

`$module_result.content_info.main_node_url_alias` enthält dasselbe im Pagelayout
([Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md#der-hauptknoten-und-weitere-platzierungen)).

## Titel und Beschreibungen

- Der `<title>` wird im Pagelayout gebildet, meist aus dem Pfad des Modulergebnisses (Name des Knotens und Name der
  Website) ([Modulergebnis und Inhaltsinformationen](../designer/vorlagen/module-result-und-content-info.md)).
- `site.ini [SiteSettings] MetaDataArray[]` enthält die Standardwerte für die ganze Website (`description`,
  `keywords`, `author`, `copyright`); ersetzen Sie die mitgelieferten Werte in Ihren Siteaccess-Einstellungen.
- Beschreibungen je Seite kommen aus den Inhalten: ein Einleitungsfeld oder ein Metadaten-Attribut, maskiert im
  Pagelayout ausgegeben.

## Metadaten aus Inhalten: xrowmetadata

Die Erweiterung `xrowmetadata` der Standarddistribution fügt einen Datentyp `xrowmetadata` hinzu (Titel,
Schlüsselwörter, Beschreibung), den Redakteure je Objekt ausfüllen, und erzeugt XML-Sitemaps. Fügen Sie das Attribut
den Klassen hinzu, die eigene Seiten haben, und geben Sie es im Pagelayout aus, mit Rückfall auf Name und Einleitung,
wenn es leer ist ([Inhalte abbilden: Metadaten für Suchmaschinen](../../installation/migration/inhaltsmodell-zuordnen.md#11-metadaten-für-suchmaschinen)).

## Sitemaps und robots.txt

- **Sitemaps**: `bcgooglesitemaps`, Teil der Standarddistribution, erzeugt XML-Sitemaps über seine Cronjob-Teile;
  `xrowmetadata` kann sie ebenfalls erzeugen. Verwenden Sie eines davon, nicht beide.
- **robots.txt**: Eine neue Installation liefert im Web-Root eine mit sinnvollen Regeln mit; fügen Sie Ihre
  `Disallow:`-Zeilen und die Adresse Ihrer Sitemap hinzu
  ([Eine brauchbare robots.txt ab Werk](../../funktionen/6.0/robots-txt.md)).

## Weiterleitungen

Umbenannte und verschobene Inhalte behalten ihre alten Adressen als Verlaufseinträge, die weiterhin zu ihnen führen.
Für Adressen einer alten Website fügen Sie vor dem Start eigene URL-Aliase mit Weiterleitung oder URL-Platzhalter
(`old-blog/*` zu `news/{1}`) hinzu und beobachten danach die 404-Liste
([Links, URL-Aliase und Platzhalter](../../anleitungen/urls-und-aliase.md),
[Checkliste für die Migration](../../installation/migration/checkliste.md)).

## Sprachfassungen

Jede Sprache hat eigene Adressen für denselben Knoten. Verlinken Sie die Fassungen einer Seite miteinander:
`[RegionalSettings] TranslationSA[]` nennt die Siteaccesses der Übersetzungen, und der Operator `language_switcher` mit
`switchlanguage/to` baut die Links; `hreflang`-Alternativen schreibt Ihr Pagelayout aus denselben Daten
([Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md#ein-siteaccess-je-sprache)).

## Geschwindigkeit und Markup

Schnelle Seiten und sauberes Markup helfen Besuchern wie Suchmaschinen: semantische Überschriften, Landmarks, Bilder
mit Alternativtext, Breite und Höhe ([Leistung](leistung.md), [Barrierefreiheit](barrierefreiheit.md)). Strukturierte
Daten (JSON-LD) schreiben Ihre Templates; halten Sie sie in einer Template-Datei Ihres Designs, damit sie den Inhalten
folgen.

## Checkliste

- [ ] Adressen lesen sich gut; keine Test- oder "Kopie von"-Seiten im Baum übrig.
- [ ] Ein kanonischer Link auf jeder Seite mit Inhalten an mehreren Stellen.
- [ ] Titel und Beschreibungen je Seite; die mitgelieferten Werte von `MetaDataArray` ersetzt.
- [ ] Eine nach Plan erzeugte Sitemap, die in `robots.txt` genannt ist.
- [ ] Weiterleitungen für jede alte Adresse, die Besucher oder Links hatte.
- [ ] Sprachfassungen miteinander verlinkt.

## Verwandte Seiten

- [Checkliste für den Projektstart](checkliste-fuer-den-projektstart.md)
- [URL-Operatoren](../designer/vorlagen/url-operatoren.md)
- [Das Pagelayout](../designer/theme-design/das-pagelayout.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
