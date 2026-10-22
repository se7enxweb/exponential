---
title: Was Exponential für eine Sendung leistet
audience: podcaster
category: podcasting-streaming
order: 2
summary: Eine Übersicht, was eine Exponential-Website für einen Podcast, eine Radiosendung oder einen Videokanal leisten kann - Medienhosting, Player, ein Feed für Apple Podcasts, eingebettete Streams, Zeitplanung, Rechte und Zugriff -, wo jeder Teil liegt und welche Seite ihn erklärt.
keywords: Podcast, Radio, Streaming, Fähigkeiten, Überblick, Medienhosting, Audio, Video, Feed, Apple Podcasts, Einbettung, Zeitplanung, was es kann, podcast, capabilities, overview, media hosting, embed, scheduling, what it can do
type: help
---
# Was Exponential für eine Sendung leistet

Diese Seite ist für Podcaster, Radioproduzenten und Streamer, die vor dem Start wissen möchten, was ihre
Exponential-Website für eine Sendung leisten kann und was nicht. Jede Zeile nennt die Funktion, wo sie im Code oder in
den Einstellungen liegt, und die Seite dieses Bereichs, die sie erklärt. Wo etwas eine Erweiterung, eine Änderung am
Design oder einen anderen Dienst braucht, sagt die Zeile das.

## Inhalt

1. [Die kurze Antwort](#die-kurze-antwort)
2. [Medien und Player](#medien-und-player)
3. [Feeds](#feeds)
4. [Streams und Einbettungen](#streams-und-einbettungen)
5. [Veröffentlichung, Menschen und Rechte](#veröffentlichung-menschen-und-rechte)
6. [Hosting und Reichweite](#hosting-und-reichweite)

## Die kurze Antwort

Exponential ist ein Content-Management-System, kein Podcast-Hoster und kein Streaming-Server. Es speichert Ihre Audio-
und Videodateien als Inhalte, spielt sie mit dem eigenen Player des Browsers ab, erzeugt aus Ihren Episoden einen
**Feed für Apple Podcasts**, bettet Player von Video- und Streamingdiensten ein und gibt Ihnen rund um all das die
Werkzeuge eines CMS: Entwürfe, Versionen, zeitgesteuertes Veröffentlichen, Freigabe, Sektionen, Rollen, Übersetzungen
und Tags. Es kodiert, transkodiert und streamt nicht live, und es zählt keine einzelnen Hörer. Die Liste der Lücken
steht unter [Was nicht eingebaut ist](was-nicht-eingebaut-ist.md).

## Medien und Player

| Sie möchten | Was es gibt | Wo | Seite |
|---|---|---|---|
| Eine Audio- oder Videodatei speichern | Den Datentyp **Medien** (`ezmedia`) und den Datentyp **Datei** (`ezbinaryfile`) | `kernel/classes/datatypes/ezmedia/`, `kernel/classes/datatypes/ezbinaryfile/` | [Das Medienfeld und das Dateifeld](medienfeld-und-dateifeld.md) |
| Audio auf der Seite abspielen | Den Playertyp **Html5 Audio**: ein `<audio>`-Element mit Bedienelementen und den Optionen Autoplay und Schleife | `design/standard/templates/content/datatype/view/ezmedia.tpl` | [Audio hochladen](audio-hochladen.md) |
| Video auf der Seite abspielen | Den Playertyp **Html5 Video**: ein `<video>`-Element | dasselbe Template | [Video hochladen](video-hochladen.md) |
| Viele Dateien auf einmal hochladen | Die Upload-Seite der Medienbibliothek | `extension/ezmultiupload` auf Installationen, die sie haben | [Bilder, Medien und Uploads](../medien-und-uploads.md) |
| Hörer in einer langen Datei springen lassen | Downloads beantworten HTTP-`Range`-Anfragen | `kernel/classes/binaryhandlers/ezfilepassthrough/ezfilepassthroughhandler.php` | [Speicher und Bandbreite](speicher-und-bandbreite.md) |

## Feeds

| Sie möchten | Was es gibt | Wo | Seite |
|---|---|---|---|
| Einen Podcast-Feed für Apple Podcasts und andere Apps | Das RSS-Exportformat **Apple Podcasts (RSS 2.0 + iTunes)** | `kernel/classes/ezrssexport.php` (`generateITunes()`) | [Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md) |
| Einen Feed der Shownotes für Newsreader | Exporte in RSS 1.0, RSS 2.0 und Atom (ohne Enclosures) | `settings/site.ini [RSSSettings] AvailableVersionList[]` | [Feeds für Leser und Netzwerke](feeds-fuer-leser-und-netzwerke.md) |
| Eine Liste aller Sendungen eines Netzwerks | Den Export in OPML 2.0 | `kernel/classes/ezrssexportopmlitem.php` | [Feeds für Leser und Netzwerke](feeds-fuer-leser-und-netzwerke.md) |
| Den Feed eines Partners auf die Website holen | RSS-Importe, ausgeführt vom Cronjob `rssimport.php` | `kernel/private/classes/cronjobs/rssimport.php` | [RSS-Feeds](../../anleitungen/rss-feeds.md) |

## Streams und Einbettungen

| Sie möchten | Was es gibt | Wo | Seite |
|---|---|---|---|
| Ein Video von YouTube, Vimeo oder Dailymotion zeigen | Den Block **External video** des Layout-Editors; die Klasse *NG Video* auf Installationen mit Media Site | `extension/explayouts` (Block `video`) | [Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md) |
| Einen Livestream zeigen | Den Player des Streamingdienstes einbetten; Exponential empfängt oder sendet den Stream nicht | derselbe Block oder ein Block *HTML snippet* | [Livestreams und Premieren](livestreams-und-premieren.md) |
| Eine Episode in einen Artikel setzen | **Objekt einbetten** im Editor für formatierten Text | `extension/ezoe` | [Medien im formatierten Text](medien-im-formatierten-text.md) |

## Veröffentlichung, Menschen und Rechte

| Sie möchten | Was es gibt | Wo | Seite |
|---|---|---|---|
| Zu einer festen Zeit veröffentlichen | Das Workflow-Ereignis *Bis Datum warten* und den Workflow-Cronjob | `kernel/classes/workflowtypes/event/ezwaituntildate/` | [Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md) |
| Eine Episode von einem Redakteur freigeben lassen | Das Workflow-Ereignis *Freigeben* | `kernel/classes/workflowtypes/event/ezapprove/` | [Freigaben vor der Veröffentlichung](freigaben-vor-der-veroeffentlichung.md) |
| Aufgenommen, geschnitten, freigegeben verfolgen | Objektzustände | [Objektzustände](../../anleitungen/objektzustaende.md) | [Produktionsphasen mit Objektzuständen](produktionsphasen-mit-objektzustaenden.md) |
| Gäste nennen | Beziehungsfelder zu Personen-Inhalten, den Datentyp *Autoren* | `kernel/classes/datatypes/ezauthor/` | [Gäste und Mitwirkende](gaeste-und-mitwirkende.md) |
| Einige Episoden Mitgliedern vorbehalten | Sektionen und Rollen | [Sektionen](../../anleitungen/sektionen.md), [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md) | [Episoden nur für Mitglieder](episoden-nur-fuer-mitglieder.md) |
| Die Lizenz einer Aufnahme festhalten | Kein eigenes Feld; fügen Sie Ihrer Klasse eines hinzu | Ihre Klassendefinition | [Rechte und Lizenzen](rechte-und-lizenzen.md) |

## Hosting und Reichweite

| Sie möchten | Was es gibt | Seite |
|---|---|---|
| Wissen, wie viel Speicherplatz und Datenverkehr Sie brauchen | Dateien liegen unterhalb des Speicherverzeichnisses der Website; jeder Download läuft durch PHP | [Speicher und Bandbreite](speicher-und-bandbreite.md) |
| Große Dateien hochladen | PHP-Grenzen und eine Höchstgröße pro Feld | [Upload-Grenzen und große Dateien](upload-grenzen-und-grosse-dateien.md) |
| Ein CDN vorschalten | Statische Dateien und Bilder lassen sich gut cachen; Downloads werden auf Rechte geprüft | [CDN und externes Medienhosting](cdn-und-externes-medienhosting.md) |
| Downloads zählen | Einen Zähler, nur bei Dateifeldern | [Downloads und Statistiken](downloads-und-statistiken.md) |
| In mehreren Sprachen veröffentlichen | Übersetzungen jedes Inhalts, eine Sprache pro Feed | [Mehrsprachige Sendungen](mehrsprachige-sendungen.md) |

## Verwandte Seiten

- [Podcaster, Radio und Streaming: hier beginnen](podcaster-hier-beginnen.md)
- [Was nicht eingebaut ist](was-nicht-eingebaut-ist.md)
- [RSS: Feeds für Apple Podcasts, eine seitenweise Feed-Liste und sicherere Exporte](../../funktionen/6.0/rss-podcast-und-feed-liste.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
