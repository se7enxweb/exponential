---
title: Metadaten und Teilen in sozialen Netzwerken
audience: government, website-creator, editor
category: public-sector
order: 90
summary: Wie Sie Nachrichten und Bekanntmachungen in Exponential mit dem Datentyp xrowmetadata gute Titel, Beschreibungen und Bilder zum Teilen geben, sie im Kopf der Seite ausgeben und das Teilen ohne Tracker von Drittanbietern auf einer Site des öffentlichen Sektors anbieten.
keywords: Metadaten, Seitentitel, Meta-Beschreibung, Open Graph, Bild zum Teilen, Teilen in sozialen Netzwerken, Schaltflächen zum Teilen, Datenschutz, Sitemaps, kanonisch, metadata, xrowmetadata, page title, meta description, sharing image, social sharing, share buttons, privacy, sitemaps, canonical
type: help
---
# Metadaten und Teilen in sozialen Netzwerken

Diese Seite richtet sich an Site-Ersteller und Redakteure, die möchten, dass Nachrichten und Bekanntmachungen in
Suchergebnissen und beim Teilen von Links gut aussehen. Die Erweiterung `xrowmetadata`, die die Standardinstallation
aktiviert, fügt ein Attribut Metadaten mit Seitentitel, Schlüsselwörtern, Beschreibung, kanonischem Link und (seit
1.3.6) einem Open-Graph-Bild hinzu. Quellen: [xrowmetadata](../../../../../eng-us/features/6.0/extensions/xrowmetadata.md) und
[Metadaten und Teilen in sozialen Netzwerken](../../../../../eng-us/help/creators/website-creators/metadata-and-social-sharing.md).

## Inhalt

1. [Das Attribut Metadaten](#das-attribut-metadaten)
2. [Im Kopf der Seite](#im-kopf-der-seite)
3. [Teilen ohne Tracker](#teilen-ohne-tracker)
4. [Checkliste für Redakteure](#checkliste-für-redakteure)

## Das Attribut Metadaten

| Feld | Verwendung |
|---|---|
| Titel | der Titel, den Suchergebnisse und Browser-Tabs zeigen; bei Bedarf kürzer als die Überschrift |
| Beschreibung | ein oder zwei Sätze; der Auszug in Ergebnissen und Linkvorschauen |
| Schlüsselwörter | von Suchmaschinen heute kaum verwendet; optional |
| Canonical | wenn derselbe Inhalt unter mehreren Adressen erreichbar ist |
| Open-Graph-Bild | das Bild, das Linkvorschauen zeigen |

Viele mitgelieferte Klassen haben bereits ein Attribut Meta Data (`metadata`): unter anderem Event, Folder, Frontpage,
Feedback form und die Klassen `ng_*`. Bei einer neuen Installation hat die Klasse Article der Basisdaten keines; fügen
Sie unter **Setup > Klassen** eines mit dem Datentyp Metadaten hinzu
([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md)).

## Im Kopf der Seite

Ihr Pagelayout gibt die Metadaten des aktuellen Knotens aus, zum Beispiel:

```text
{def $meta = metadata( $module_result.node_id )}
{if $meta}
  {if $meta.title}<title>{$meta.title|wash}</title>{/if}
  {if $meta.description}<meta name="description" content="{$meta.description|wash}">{/if}
{/if}
```

Fügen Sie Open-Graph-Tags (`og:title`, `og:description`, `og:image`, `og:url`) auf dieselbe Weise hinzu; das
vollständige Beispiel steht in [xrowmetadata](../../../../../eng-us/features/6.0/extensions/xrowmetadata.md). Sind die Metadaten
leer, greifen Sie auf den Namen des Objekts und seine Einleitung zurück.

## Teilen ohne Tracker

| Möglichkeit | Datenschutz |
|---|---|
| Einfache Links zum Teilen (ein Link auf die Teilen-Adresse der Plattform mit der URL der Seite) | nichts wird geladen, bis der Besucher klickt |
| Eine Schaltfläche „Link kopieren“ | nichts verlässt die Site |
| Eingebettete Widgets sozialer Plattformen zum Teilen | sie laden Skripte von Drittanbietern und können Besucher auf jeder Seite verfolgen |

Die Administration von Exponential lädt nichts von Drittanbietern, und das Produkt liefert kein Cookie-Banner mit
([Standards zum Datenschutz](../../../../../eng-us/standards/privacy.md)); öffentliche Seiten frei von Widgets Dritter zu halten,
vermeidet Fragen der Einwilligung.

## Checkliste für Redakteure

- [ ] Der Titel sagt in den ersten Worten, worum es geht.
- [ ] Die Beschreibung ist ein vollständiger Satz, keine Kopie des Titels.
- [ ] Das Bild zum Teilen hat in seinem Bildobjekt einen aussagekräftigen Alternativtext und zeigt nicht versehentlich
  personenbezogene Daten.
- [ ] Bekanntmachungen, die bald versteckt werden, brauchen kein Bild zum Teilen.

## Verwandte Seiten

- [Nachrichten und Warnmeldungen](README.md)
- [SEO-Grundlagen](../../../../../eng-us/help/creators/website-creators/seo-basics.md)
- [Sitemaps](../../../../../eng-us/help/creators/website-creators/sitemaps.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
