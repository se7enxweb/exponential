---
title: Kommende Veranstaltungen auflisten
audience: government, website-creator, developer
category: public-sector
order: 50
summary: Template-Fetches, die kommende Veranstaltungen auf einer Exponential-Site auflisten - nach Beginn ab jetzt, für einen Kalender oder die ganze Site, für einen Ort oder ein Schlagwort - mit Cache-Ablauf, damit vergangene Veranstaltungen herausfallen, und ein RSS-Feed der Veranstaltungen.
keywords: kommende Veranstaltungen, Fetch, Inhaltsliste, Inhaltsbaum, Attributfilter, Cache-Block, Ablauf, RSS, Feed mit Veranstaltungen, upcoming events, fetch, content list, content tree, attribute_filter, from_time, currentdate, sort_by attribute, main_node_only, cache block, expiry, events feed
type: help
---
# Kommende Veranstaltungen auflisten

Diese Seite richtet sich an Site-Ersteller und Entwickler, die „nächste Veranstaltungen“ auf der Startseite, einer
Themenseite oder der Seite eines Ortes zeigen. Die mitgelieferte Klasse Event speichert ihren Beginn in `from_time`
(Datum und Zeit), nach dem Fetches filtern und sortieren können. Die Fetch-Funktionen sind die aus
`kernel/content/function_definition.php`; Grundlagen zu Templates stehen in
[Templates und Design](../../../../anleitungen/templates-und-design.md).

## Inhalt

1. [Die nächsten Veranstaltungen eines Kalenders](#die-nächsten-veranstaltungen-eines-kalenders)
2. [Über die ganze Site](#über-die-ganze-site)
3. [Für einen Ort oder ein Thema](#für-einen-ort-oder-ein-thema)
4. [Caching](#caching)
5. [Ein Feed mit Veranstaltungen](#ein-feed-mit-veranstaltungen)

## Die nächsten Veranstaltungen eines Kalenders

```text
{def $events = fetch( 'content', 'list', hash(
        'parent_node_id', $calendar_node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'event' ),
        'attribute_filter', array( array( 'event/from_time', '>=', currentdate() ) ),
        'sort_by', array( 'attribute', true(), 'event/from_time' ),
        'limit', 5 ) )}
<ul>
{foreach $events as $event}
  <li><a href={$event.url_alias|ezurl}>{$event.name|wash}</a>,
      <time>{$event.data_map.from_time.content.timestamp|l10n( 'shortdatetime' )}</time></li>
{/foreach}
</ul>
{undef $events}
```

`currentdate()` ist die aktuelle Zeit; Veranstaltungen, die vor jetzt begonnen haben, fallen heraus. Für mehrtägige
Veranstaltungen, die bis zu ihrem Ende gelistet bleiben sollen, filtern Sie stattdessen nach `event/to_time` (und
geben Sie jeder solchen Veranstaltung eine To Time).

## Über die ganze Site

Verwenden Sie `fetch( 'content', 'tree', ... )` ab dem Wurzelknoten und fügen Sie `'main_node_only', true()` hinzu,
damit eine Veranstaltung mit zwei Orten einmal erscheint. Ein Baum-Fetch über die ganze Site ist schwerer als eine
Liste: Halten Sie `limit` klein und speichern Sie das Ergebnis im Cache.

## Für einen Ort oder ein Thema

| Filter | Wie |
|---|---|
| Veranstaltungen an einem Ort | fügen Sie Event eine Beziehung zu einem Ort hinzu ([Eine Klasse für Veranstaltungen modellieren](eine-klasse-fuer-veranstaltungen-modellieren.md)); auf der Seite des Ortes listet `fetch( 'content', 'reverse_related_objects', ... )` die Veranstaltungen, die auf ihn zeigen; behalten Sie dann die mit künftigem Beginn |
| Veranstaltungen zu einem Thema | Schlagworte: eztags fügt einen Attributfilter für `content/list` und `content/tree` hinzu (siehe `extension/eztags/doc/`); oder die mitgelieferte Kategorie Keywords |
| Veranstaltungen mehrerer Kalender | ein Multicalendar-Objekt, das die Kalender verknüpft ([Was mitgeliefert wird](was-fuer-veranstaltungen-mitgeliefert-wird.md)) |

## Caching

Eine Liste in einer zwischengespeicherten Seite zeigt vergangene Veranstaltungen, bis der Cache erneuert wird. Wählen
Sie eine Möglichkeit:

| Weg | Wirkung |
|---|---|
| Ein Cache-Block mit einem Ablauf, zum Beispiel eine Stunde | die Liste ist höchstens eine Stunde alt |
| Ein Ablauf für den Teilbaum des Kalenders | das Veröffentlichen einer Veranstaltung erneuert die Liste |
| `cache_ttl` in der vollständigen Ansicht der Seite | die Seite wird nach so vielen Sekunden neu aufgebaut |

[View-Cache und Cache-Blöcke](../../../../../eng-us/help/creators/website-creators/view-cache-and-cache-blocks.md) erklärt die
Möglichkeiten; kombinieren Sie einen Ablauf (für die vergehende Zeit) mit einem Ablauf für den Teilbaum (für neue
Veranstaltungen).

## Ein Feed mit Veranstaltungen

Ein RSS-Export des Kalenderordners, Klasse Event, mit dem Titel aus `short_title` und der Beschreibung aus `text`
(**Setup > RSS**, [RSS-Feeds](../nachrichten-und-warnmeldungen/rss-feeds.md)). RSS ordnet Elemente nach Veröffentlichung, nicht nach
Datum der Veranstaltung; sagen Sie das dort, wo Sie ihn anbieten. Ein iCalendar-Feed wird nicht mitgeliefert.

## Verwandte Seiten

- [Veranstaltungen](README.md)
- [Kalenderansichten](kalenderansichten.md)
- [Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
