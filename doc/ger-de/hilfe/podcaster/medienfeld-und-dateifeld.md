---
title: Das Medienfeld und das Dateifeld
audience: podcaster
category: podcasting-streaming
order: 5
summary: Die beiden Feldtypen, die in Exponential Audio und Video aufnehmen - der Datentyp Medien (ezmedia) mit seinen Playertypen und Optionen und der Datentyp Datei (ezbinaryfile) mit seinem Download-Zähler - wie jeder gespeichert, ausgeliefert und vom Podcast-Feed verwendet wird.
keywords: ezmedia, ezbinaryfile, Medienfeld, Dateifeld, Playertyp, Mediaplayertyp, Html5 Audio, Html5 Video, Auto-Wiedergabe, Schleife, Controller, content/download, Range, Content-Disposition, Download-Zähler, Speicherung, media field, file field, player type, autoplay, loop, download count, storage
type: help
---
# Das Medienfeld und das Dateifeld

Diese Seite ist für Produzenten und Administratoren, die festlegen, wie Episoden ihr Audio oder Video speichern.
Exponential hat dafür zwei Datentypen. Beide können das **Enclosure** (den Anhang) eines Podcast-Feeds bilden:
`podcastItemList()` in `kernel/classes/ezrssexport.php` akzeptiert einen Wert vom Typ `eZMedia` oder `eZBinaryFile`.

## Inhalt

1. [Nebeneinander](#nebeneinander)
2. [Das Medienfeld](#das-medienfeld)
3. [Das Dateifeld](#das-dateifeld)
4. [Wie Dateien gespeichert werden](#wie-dateien-gespeichert-werden)
5. [Wie Dateien ausgeliefert werden](#wie-dateien-ausgeliefert-werden)
6. [Welches Sie verwenden](#welches-sie-verwenden)

## Nebeneinander

| | Medien (`ezmedia`) | Datei (`ezbinaryfile`) |
|---|---|---|
| Zeigt einen Player auf der Seite | ja, gemäß seinem Playertyp | nein, einen Download-Link |
| Optionen pro Objekt | Breite, Höhe, Auto-Wiedergabe, Schleife, Controller | keine |
| Höchstgröße pro Klassenattribut | ja, in MB | ja, in MB |
| Zählt Downloads | nein | ja (`download_count`) |
| Als Enclosure im Feed verwendbar | ja | ja |
| Verwendet von | Klasse *Video* (Html5 Video), Klasse *Audio* auf Installationen mit Media Site (Html5 Audio) | Klasse *File*, *NG Video* (Videodatei) |

## Das Medienfeld

Das Klassenattribut hat einen **Mediaplayertyp** und eine **Maximale Dateigröße**
(`design/standard/templates/class/datatype/edit/ezmedia.tpl`). Die Playertypen sind Flash, QuickTime, RealPlayer,
Silverlight, Windows Media Player, **Html5 Video** und **Html5 Audio**. Nur die beiden HTML5-Typen funktionieren in
aktuellen Browsern; die anderen schreiben Plugin-Markup für Player, die es nicht mehr gibt, und die Content Security
Policy der Website blockiert `<object>` und `<embed>`, wenn sie durchgesetzt wird
(`settings/site.ini [ContentSecurityPolicy] Directives[object-src]='none'`).

Wenn ein Redakteur ein Objekt ausfüllt, bietet das Formular die Datei und, für die HTML5-Typen, **Breite**, **Höhe**,
**Auto-Wiedergabe**, **Schleife** und **Controller** (`design/standard/templates/content/datatype/edit/ezmedia.tpl`).
Das Ansichts-Template `design/standard/templates/content/datatype/view/ezmedia.tpl` macht daraus Attribute:

| Option | HTML5-Attribut |
|---|---|
| Controller | `controls` |
| Auto-Wiedergabe | `autoplay` |
| Schleife | `loop` |
| Breite, Höhe | `width`, `height` (nur Video) |

Das Template schreibt immer `preload="none"`, damit eine Seite mit vielen Playern nicht jede Datei zu laden beginnt.
Es schreibt kein `<track>` und kein `poster`.

## Das Dateifeld

Das Dateifeld speichert jede beliebige Datei und zeigt einen Download-Link mit ihrem Namen und ihrer Größe. Jeder
Download über `content/download` erhöht seinen `download_count` um eins
(`kernel/classes/datatypes/ezbinaryfile/ezbinaryfiletype.php`). Von sich aus spielt es nichts ab; ein Design-Template
kann die Datei trotzdem in ein `<audio>`- oder `<video>`-Element setzen, wie es das Template von *NG Video* auf
Installationen mit Media Site mit einem `<video>`-Element für hochgeladene Videos tut
(`extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_video.tpl`).

## Wie Dateien gespeichert werden

Hochgeladene Medien werden unterhalb des Speicherverzeichnisses der Website in `original/<mime group>` abgelegt, zum
Beispiel `original/audio` oder `original/video` (`kernel/classes/datatypes/ezmedia/ezmediatype.php`). Die Gruppe
ergibt sich aus dem MIME-Typ der Datei, der wiederum über `settings/mime.ini` aus ihrer Endung folgt. Zur Planung
des Speicherplatzes siehe [Speicher und Bandbreite](speicher-und-bandbreite.md).

## Wie Dateien ausgeliefert werden

Player und Podcast-Apps holen die Datei von
`/content/download/<object id>/<attribute id>/<file name>`. Diese Ansicht prüft, ob der Besucher das Objekt lesen
darf; dann sendet der Handler `eZFilePassthroughHandler` die Datei über PHP
(`kernel/classes/binaryhandlers/ezfilepassthrough/ezfilepassthroughhandler.php`):

- Er beantwortet `Range`-Anfragen mit `206 Partial Content`, sodass Player springen und Apps abgebrochene Downloads
  fortsetzen können;
- er sendet `Accept-Ranges: bytes`, die `Content-Length` und ein `Expires` zehn Minuten in der Zukunft
  (`eZFile::downloadHeaders()` in `lib/ezfile/classes/ezfile.php`);
- er sendet `Content-Disposition: attachment`, es sei denn, `settings/file.ini [PassThroughSettings]
  ContentDisposition[]` gibt für den Typ `inline` an. Die mitgelieferte Liste hat `inline` für `video/mp4`,
  `video/webm`, `video/ogg` und Flash, nicht für Audio. Ein `<audio>`-Element spielt die Datei trotzdem ab; wer den
  Link direkt öffnet, erhält einen Download.

## Welches Sie verwenden

- Eine Episodenseite mit Player: **Medienfeld**, Playertyp **Html5 Audio** oder **Html5 Video**.
- Ein Download-Zähler ist wichtiger als ein Player auf der Seite: **Dateifeld**, dazu ein Player in Ihrem
  Design-Template.
- Beides: Manche Websites halten das Audio in einem Medienfeld und bieten zusätzlich einen Download in hoher Qualität
  in einem Dateifeld an.

## Verwandte Seiten

- [Eine Klasse für Episoden wählen](eine-klasse-fuer-episoden-waehlen.md)
- [Audio hochladen](audio-hochladen.md)
- [Dateien und Downloads](../ersteller/website-ersteller/dateien-und-downloads.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
