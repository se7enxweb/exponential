---
title: Video hochladen
audience: podcaster
category: podcasting-streaming
order: 12
summary: Wie Sie eine Videoepisode auf die Website bringen - wann Sie die Datei hochladen und wann Sie sie bei einem Videodienst hosten, die Klasse Video und ihr Player Html5 Video, MP4 und WebM, Größen und die Prüfungen nach dem Veröffentlichen.
keywords: Video hochladen, MP4, WebM, Videodatei, Klasse Video, Html5 Video, Videopodcast, Videoepisode, Poster, Breite, Höhe, Videohosting, YouTube, Vimeo, upload video, video file, Video class, video podcast, video episode, width, height, video hosting
type: help
---
# Video hochladen

Diese Seite ist für Produzenten von Videopodcasts, Videoepisoden und Clips. Exponential kann eine Videodatei speichern
und abspielen, und es kann ein Video von einem Videodienst einbetten. Was richtig ist, hängt von der Dateigröße, dem
Publikum und davon ab, was der Player können muss.

## Inhalt

1. [Hochladen oder einbetten](#hochladen-oder-einbetten)
2. [Die Datei vorbereiten](#die-datei-vorbereiten)
3. [Ein Video hochladen](#ein-video-hochladen)
4. [Playeroptionen](#playeroptionen)
5. [Das Ergebnis prüfen](#das-ergebnis-prüfen)

## Hochladen oder einbetten

| Bedarf | Auf die Website hochladen | Von einem Videodienst einbetten |
|---|---|---|
| Ein Videopodcast-Feed mit der Datei als Enclosure | ja: Der Feed braucht eine Datei auf der Website | nein: Es gibt keine Datei zum Anhängen |
| Untertitel, die der Zuschauer einschalten kann | nicht im Auslieferungszustand (kein `<track>` im Template) | ja, wenn der Dienst sie unterstützt |
| Angepasste Qualität für langsame Verbindungen | nein: eine Datei, wie hochgeladen | ja, meistens |
| Sehr große Dateien, viele Zuschauer | möglich, aber jeder Abruf läuft über Ihren Server | der Dienst trägt die Last |
| Kein Drittanbieter auf der Seite | ja | nein |

Zum Einbetten siehe [Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md).

## Die Datei vorbereiten

Exponential spielt die Datei so ab, wie Sie sie hochladen. Exportieren Sie sie in der Größe, die Zuschauer
herunterladen sollen.

| Entscheidung | Empfehlung | Warum |
|---|---|---|
| Format | MP4 (`video/mp4`) oder WebM | beide stehen in `settings/mime.ini` und laufen in aktuellen Browsern |
| Öffnet im Browser | MP4-, WebM- und Ogg-Video | `settings/file.ini [PassThroughSettings]` sendet sie `inline` |
| Größe | so klein, wie die Qualität es erlaubt | siehe [Upload-Grenzen und große Dateien](upload-grenzen-und-grosse-dateien.md) |
| Dateiname | kurz, kleingeschrieben, ohne Leerzeichen | er ist Teil der Adresse |

## Ein Video hochladen

**Im Bearbeitungsformular.** Legen Sie im Ordner der Sendung ein Objekt **Video** an (oder eines Ihrer Klasse für
Videoepisoden). Wählen Sie in **File** das Video unter **Neue Datei hochladen**, füllen Sie Name und Bildunterschrift
aus und drücken Sie **Zur Veröffentlichung schicken**. Das Dateifeld der Klasse *Video* verwendet den Playertyp
**Html5 Video** (`class-video.xml` im mitgelieferten Klassenpaket).

**Auf der Upload-Seite.** `settings/upload.ini [CreateSettings]` ordnet `video/mp4` der Klasse `video` zu, mit
`[video_ClassSettings] FileAttribute=file`. Ein hochgeladenes MP4 wird also zu einem Objekt *Video* mit der Datei in
seinem Playerfeld. Ältere Formate sind den alten Plugin-Klassen zugeordnet (`video/quicktime` zu *Quicktime*,
`video/x-msvideo` zu *Windows media*); diese Klassen schreiben Plugin-Markup, das aktuelle Browser nicht abspielen,
wandeln Sie solche Dateien deshalb vorher in MP4 um. Siehe [Bilder, Medien und Uploads](../medien-und-uploads.md#dateien-hochladen).

**Auf Installationen mit Media Site** nimmt die Klasse *NG Video* entweder eine hochgeladene **Video file** oder den
Bezeichner eines Videos bei YouTube, Vimeo oder Dailymotion auf, mit einem Bild **Poster**
([Eine Klasse für Episoden wählen](eine-klasse-fuer-episoden-waehlen.md)).

## Playeroptionen

| Option | Wirkung | Empfehlung |
|---|---|---|
| **Breite**, **Höhe** | die Größe des Players in Pixeln; 0 lässt sie weg | setzen Sie sie, oder überlassen Sie die Größe dem Design |
| **Controller** | `controls` | immer an |
| **Auto-Wiedergabe** | startet beim Laden der Seite | aus, aus Gründen der Barrierefreiheit und Höflichkeit |
| **Schleife** | beginnt am Ende von vorn | aus bei Episoden |

Der Player erhält `preload="none"`: Nichts wird heruntergeladen, bevor jemand auf Abspielen drückt, und bis dahin wird
auch kein Standbild gezeigt (`design/standard/templates/content/datatype/view/ezmedia.tpl`). Die Klasse *Video* hat
kein Feld für ein Vorschaubild; ein Design-Override kann eines hinzufügen.

## Das Ergebnis prüfen

- [ ] Öffnen Sie die Seite abgemeldet und auf einem Telefon: Das Video läuft, und Springen funktioniert.
- [ ] Das Feld zeigt **MIME-Typ** `video/mp4` (oder `video/webm`) und die erwartete **Größe**.
- [ ] Wenn im Video gesprochen wird, stehen Untertitel oder ein Transkript auf der Seite ([Transkripte und Untertitel](transkripte-und-untertitel.md)).
- [ ] Bei einem Videopodcast-Feed trägt das Enclosure denselben Typ und dieselbe Länge ([Ihren Feed prüfen](ihren-feed-pruefen.md)).

## Verwandte Seiten

- [Audio hochladen](audio-hochladen.md)
- [Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)
- [Speicher und Bandbreite](speicher-und-bandbreite.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
