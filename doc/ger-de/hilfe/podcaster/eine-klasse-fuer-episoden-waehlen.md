---
title: Eine Klasse für Episoden wählen
audience: podcaster
category: podcasting-streaming
order: 7
summary: Welche Inhaltsklasse Sie für Podcast- und Videoepisoden verwenden - die Klasse Video, die Klassen Audio und NG Video von Installationen mit Media Site, die Klasse File oder eine eigene Klasse - mit den Feldern, die jede hat, und was das für den Player und den Feed bedeutet.
keywords: Inhaltsklasse, Episodenklasse, Video, Audio, ng_audio, ng_video, File, Datei, ezmedia, html5_audio, html5_video, upload.ini, MimeClassMap, Klasse wählen, content class, episode class, choose class
type: help
---
# Eine Klasse für Episoden wählen

Diese Seite ist für Produzenten und Website-Ersteller, die entscheiden, welche Art von Objekt eine Episode ist. Welche
Klassen es gibt, hängt von den Seitenpaketen ab, mit denen Ihre Website installiert wurde; fragen Sie Ihren
Administrator oder öffnen Sie **Setup > Klassen**. Die Feldlisten unten wurden aus einer laufenden Installation mit den
mitgelieferten Paketen gelesen.

## Inhalt

1. [Die Kandidaten](#die-kandidaten)
2. [Video](#video)
3. [Audio (Installationen mit Media Site)](#audio-installationen-mit-media-site)
4. [NG Video (Installationen mit Media Site)](#ng-video-installationen-mit-media-site)
5. [File](#file)
6. [Eine eigene Klasse](#eine-eigene-klasse)
7. [Was die Upload-Seite wählt](#was-die-upload-seite-wählt)

## Die Kandidaten

| Klasse | Bezeichner | Herkunft | Medienfeld | Geeignet für |
|---|---|---|---|---|
| Video | `video` | das Klassenpaket (`class-video.xml`) | Medien, Html5 Video | Videoepisoden mit Player |
| Audio | `ng_audio` | das Paket mit den Demo-Inhalten der Media Site | Medien, Html5 Audio | Audioepisoden mit Shownotes und Bild |
| NG Video | `ng_video` | das Paket der Media Site | Dateifeld oder eine ID von YouTube, Vimeo oder Dailymotion | Videoseiten, die Uploads und gehostete Videos mischen |
| File | `file` | jede Installation | Dateifeld | Downloads, oder Audio, wenn nichts anderes vorhanden ist |
| Ihre eigene | beliebig | von Ihrem Administrator gebaut | nach Wahl | eine Sendung mit eigenen Anforderungen |

## Video

Felder: **Name**, **File** (Medien, Playertyp Html5 Video), **Caption** (formatierter Text), **Tags**
(Schlüsselwörter), **Star Rating** und **Meta Data** auf Installationen mit den Erweiterungen für Bewertungen und
Metadaten. Sie hat kein eigenes Feld für Shownotes; dafür dient die Bildunterschrift (**Caption**). Ordnen Sie
**File** als Enclosure des Feeds zu.

## Audio (Installationen mit Media Site)

Felder: **Title** (Pflichtfeld), **Teaser title**, **Teaser intro**, **Description** (formatierter Text), **File**
(Medien, Playertyp Html5 Audio), **Image** und **Meta Data**. Das kommt einer fertigen Episodenklasse am nächsten:
Ordnen Sie im Feed **Title**, **Description** und **File** zu. Die Design-Templates dafür gibt es auf Installationen
mit dem Media-Theme (`extension/sevenx_themes_media/design/media/templates/content/views/full/ng_audio.tpl` und die
Templates daneben).

## NG Video (Installationen mit Media Site)

Beschrieben als "HTML5, YouTube, Vimeo and Dailymotion video". Zu den Feldern gehören **Title**, **Video type**
(YouTube, Vimeo, Dailymotion oder eine hochgeladene Datei), **Video file** (Dateifeld), **Video identifier** für das
gehostete Video, **Poster**, **Teaser intro text**, **Description**, **Main topic** und **Tags** (eztags),
**Sponsored content disclosure** und **Autoplay**. Die Vollansicht spielt YouTube erst nach einem Klick ab
(`extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_video.tpl`).

Für den Feed können nur Objekte mit einer hochgeladenen **Video file** ein Enclosure tragen; ein Objekt, das auf
YouTube verweist, hat nichts anzuhängen und fehlt in einem Podcast-Feed.

## File

Felder: ein Name, eine Beschreibung und die **File** (Dateifeld). Sie zeigt einen Download-Link, keinen Player, es sei
denn, Ihr Design fügt einen hinzu. Sie zählt Downloads ([Downloads und Statistiken](downloads-und-statistiken.md)). Im
Auslieferungszustand werden hochgeladene MP3-Dateien zu Objekten der Klasse *File* (siehe unten), deshalb beginnen
viele Websites mit dieser Klasse, ohne sie gewählt zu haben.

## Eine eigene Klasse

Wenn keine davon passt, baut Ihr Administrator eine Episodenklasse mit genau den Feldern, die Ihre Sendung braucht:
Shownotes, Gäste, ein Veröffentlichungsdatum für die Zeitplanung, ein Transkript, eine Lizenz. Einen Vorschlag finden
Sie unter [Eine Episodenklasse aufbauen](eine-episodenklasse-aufbauen.md).

## Was die Upload-Seite wählt

Wenn Dateien über die Upload-Seite hochgeladen werden, entscheidet `settings/upload.ini [CreateSettings]
MimeClassMap[]` die Klasse nach dem MIME-Typ. Im Auslieferungszustand: Bilder zu `image`, `video/quicktime` zu
`quicktime`, `video/x-msvideo` zu `windows_media`, Real-Video zu `real_video`, Flash zu `flash`, **`video/mp4` zu
`video`** und alles andere zu `DefaultClass=file`. Für `audio/mpeg` gibt es keinen Eintrag, daher wird ein
hochgeladenes MP3 zu einem Objekt **File**. Um Objekte *Audio* zu erhalten, ergänzt der Administrator in einem
Override `MimeClassMap[audio/mpeg]=ng_audio` (oder Ihre eigene Klasse), zusammen mit den Upload-Einstellungen der
Klasse ([Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md)).

Wenn Sie ein Objekt mit **Neu erstellen** im Ordner anlegen und dabei die Klasse wählen, stellt sich die Frage gar
nicht erst.

## Verwandte Seiten

- [Das Medienfeld und das Dateifeld](medienfeld-und-dateifeld.md)
- [Inhaltsklassen für Websites](../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md)
- [Datentypen wählen](../ersteller/website-ersteller/datentypen-waehlen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
