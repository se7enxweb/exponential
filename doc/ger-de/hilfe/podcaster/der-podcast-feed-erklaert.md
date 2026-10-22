---
title: Der Podcast-Feed erklärt
audience: podcaster
category: podcasting-streaming
order: 6
summary: Was der Export für Apple Podcasts von Exponential schreibt, Element für Element - den Kanal aus den Podcast-Feldern des Exports, ein Element pro Episode mit Enclosure, guid, Datum und Dauer - und die Regeln, die entscheiden, welche Episoden erscheinen, mit dem Code, der es tut.
keywords: Podcast-Feed, Apple Podcasts, iTunes-Namespace, RSS 2.0, Kanal, Element, Enclosure, Anhang, guid, Remote-ID, pubDate, itunes:duration, Feed-Cache, CacheTime, generateITunes, podcast feed, iTunes namespace, channel, item, remote id, feed cache
type: help
---
# Der Podcast-Feed erklärt

Diese Seite ist für Produzenten, die den Feed verstehen möchten, mit dem ihre Sendung veröffentlicht wird, bevor sie
ihn bei einem Verzeichnis einreichen oder eine Frage von dort beantworten. Alles hier ist das, was
`eZRSSExport::generateITunes()` und `podcastItemList()` in `kernel/classes/ezrssexport.php` tun. Die Schritte zur
Einrichtung stehen unter [Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md).

## Inhalt

1. [Das Format](#das-format)
2. [Der Kanal](#der-kanal)
3. [Die Episoden](#die-episoden)
4. [Welche Episoden erscheinen](#welche-episoden-erscheinen)
5. [Adressen im Feed](#adressen-im-feed)
6. [Wann Änderungen sichtbar werden](#wann-änderungen-sichtbar-werden)

## Das Format

Das Exportformat **Apple Podcasts (RSS 2.0 + iTunes)** (gespeicherter Wert `ITUNES`, aufgeführt in
`settings/site.ini [RSSSettings] AvailableVersionList[]`) ist RSS 2.0 mit drei Namespaces: `itunes`
(`http://www.itunes.com/dtds/podcast-1.0.dtd`), `content` und `atom`. Jede Podcast-App, die RSS liest, kann ihn
abonnieren; die Elemente von Apple sind einfach zusätzliche Angaben für Apps, die sie verstehen.

## Der Kanal

Der Kanal wird aus den eigenen Feldern des Exports und seinem Abschnitt **Apple Podcasts** geschrieben:

| Element | Quelle |
|---|---|
| `title`, `itunes:title` | der **Name** des Exports |
| `link` | die Adresse der Website (siehe [Adressen im Feed](#adressen-im-feed)) |
| `description` | die **Beschreibung** des Exports |
| `language` | **Sprache**, oder der HTTP-Code der Locale der Website, wenn leer |
| `itunes:image href=...` | **Adresse des Titelbilds** |
| `itunes:category` (mit einer verschachtelten Unterkategorie) | **Kategorie**, **Unterkategorie** |
| `itunes:explicit` | **Enthält explizite Inhalte**, `true` oder `false` |
| `itunes:author`, `itunes:subtitle`, `itunes:summary`, `copyright` | **Autor**, **Untertitel**, **Zusammenfassung**, **Copyright** |
| `itunes:type` | **Art der Sendung**: `episodic` oder `serial` |
| `itunes:owner` mit `itunes:name`, `itunes:email` | **Name des Eigentümers**, **E-Mail des Eigentümers** |
| `itunes:block`, `itunes:complete`, `itunes:new-feed-url` | nur wenn gesetzt ([Eine Sendung umziehen, pausieren oder beenden](eine-sendung-umziehen-pausieren-oder-beenden.md)) |
| `atom:link rel="self"` | die eigene Adresse des Feeds |
| `lastBuildDate`, `generator` | der Zeitpunkt der Erzeugung, die Produktedition |

Ein Element mit leerem Wert wird weggelassen (`eZRSSExport::element()`). Jedes Feld beschreibt die
[Referenz der Kanalfelder](referenz-der-kanalfelder.md).

## Die Episoden

Jede Episode wird zu einem `item`:

| Element | Quelle |
|---|---|
| `title`, `itunes:title` | das als **Titel** zugeordnete Feld, oder der Name des Objekts, wenn es leer ist |
| `link` | die Seite der Episode auf der Website |
| `description` | das als **Beschreibung** zugeordnete Feld, als Text |
| `pubDate` | das Datum der **Veröffentlichung** des Objekts |
| `enclosure url length type` | das als **Anhang (Medien)** zugeordnete Feld: seine Download-Adresse, seine Größe in Byte und sein MIME-Typ, gelesen aus der gespeicherten Datei |
| `guid isPermaLink="false"` | die **Remote-ID** des Objekts |
| `itunes:duration` | aus der Datei berechnet für ein MP3 mit konstanter Bitrate, sonst weggelassen |
| `itunes:explicit` | der Wert des Kanals |
| `itunes:author` | der Name des Benutzers, der das Objekt angelegt hat |
| `category` | das als **Kategorie** zugeordnete Feld |

Die `guid` ist die wichtigste Angabe. Sie ändert sich nie, solange das Objekt existiert, auch nicht, wenn die Episode
umbenannt, verschoben oder erneut veröffentlicht wird; deshalb zeigen Apps eine alte Episode nicht als neu an. Eine
Episode zu korrigieren heißt, sie zu bearbeiten, nie, sie zu löschen und neu anzulegen. Einzelheiten zu jeder
Zuordnung: [Referenz der Episodenzuordnung](referenz-der-episodenzuordnung.md).

## Welche Episoden erscheinen

1. Die Quellen des Exports werden gelesen: Platzierung, Klasse und Einstellung **Unterknoten** jeder Quelle
   ([Wie eine Sendung zu Inhalten wird](wie-eine-sendung-zu-inhalten-wird.md)).
2. Die Objekte werden nach dem Datum der Veröffentlichung sortiert, neueste zuerst, und bei der **Anzahl der Objekte**
   abgeschnitten.
3. Ein verborgenes Objekt wird übersprungen, es sei denn, die Website ist so eingestellt, dass sie verborgene Objekte
   zeigt.
4. Ein Objekt, dessen Klasse für diese Quelle keine Zuordnung hat, wird übersprungen.
5. Ein Objekt, dessen Enclosure-Feld leer ist oder kein Medien- oder Dateifeld ist, wird übersprungen: "An episode
   with nothing to play is not an episode" (Code-Kommentar; sinngemäß: Eine Episode, die nichts abzuspielen hat, ist
   keine Episode).

Entwürfe und Objekte, die auf einen geplanten Termin warten, sind nicht veröffentlicht und stehen daher auch nicht im
Feed.

## Adressen im Feed

Links und Enclosure-Adressen werden aus der **URL der Website** des Exports gebildet. Ist sie leer, wird die
öffentliche Adresse des Siteaccess des Exports verwendet (`publicSiteURL()`), nicht die Adresse der Administration,
die Sie gerade benutzen. Mit eingeschalteter URL-Übersetzung ist `link` die sprechende URL der Episode, sonst
`/content/view/full/<node id>`. Das Enclosure ist immer `/content/download/<object id>/<attribute id>/<file name>`.

## Wann Änderungen sichtbar werden

Der Feed wird `settings/site.ini [RSSSettings] CacheTime` Sekunden lang gecacht, im Auslieferungszustand 1200
(20 Minuten); 0 erzeugt ihn bei jeder Anfrage neu. Eine neue Episode erscheint daher spätestens 20 Minuten nach dem
Veröffentlichen im Feed, und Podcast-Apps holen sie dann nach ihrem eigenen Zeitplan ab. Ein Administrator kann den
Feed-Cache sofort leeren (`php bin/php/ezcache.php --clear-id=rss_cache --allow-root-user`, aus
[RSS-Feeds](../../anleitungen/rss-feeds.md#7-fehlerbehebung)).

Wenn das Erzeugen des Feeds fehlschlägt, antwortet die Adresse trotzdem mit einem gültigen, leeren Feed mit dem Titel
der Sendung (`emptyPodcast()`), und der Fehler landet im Debug-Log.

## Verwandte Seiten

- [Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md)
- [Ihren Feed prüfen](ihren-feed-pruefen.md)
- [RSS: Feeds für Apple Podcasts, eine seitenweise Feed-Liste und sicherere Exporte](../../funktionen/6.0/rss-podcast-und-feed-liste.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
