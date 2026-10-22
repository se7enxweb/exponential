---
title: Audio hochladen
audience: podcaster
category: podcasting-streaming
order: 11
summary: Wie Sie das Audio einer Episode auf die Website bringen - die Datei vorbereiten, sie im Bearbeitungsformular der Episode oder auf der Upload-Seite hochladen, die Playeroptionen wählen und den Dateityp und die Größe prüfen, die der Podcast-Feed ankündigen wird.
keywords: Audio hochladen, MP3, Audiodatei, Medienfeld, Html5 Audio, Upload-Seite, Mehrfach-Upload, Dateityp, audio/mpeg, m4a, Dateigröße, Bedienelemente, Auto-Wiedergabe, Episodenaudio, upload audio, audio file, media field, upload page, multiupload, file type, file size, controls, autoplay, episode audio
type: help
---
# Audio hochladen

Diese Seite ist für Produzenten und Redakteure, die das Audio einer Episode hinzufügen. Sie behandelt die Vorbereitung
der Datei, die beiden Wege des Hochladens und die Prüfungen, die den Feed gültig halten. Video behandelt
[Video hochladen](video-hochladen.md).

## Inhalt

1. [Die Datei vorbereiten](#die-datei-vorbereiten)
2. [Im Bearbeitungsformular hochladen](#im-bearbeitungsformular-hochladen)
3. [Auf der Upload-Seite hochladen](#auf-der-upload-seite-hochladen)
4. [Playeroptionen](#playeroptionen)
5. [Die Datei prüfen](#die-datei-prüfen)
6. [Eine Datei ersetzen](#eine-datei-ersetzen)

## Die Datei vorbereiten

Exponential speichert die Datei genau so, wie Sie sie hochladen; es wandelt Audio nicht um und komprimiert es nicht
([Was nicht eingebaut ist](was-nicht-eingebaut-ist.md)). Bereiten Sie die fertige Datei deshalb in Ihrer
Schnittsoftware vor:

| Entscheidung | Empfehlung | Warum |
|---|---|---|
| Format | MP3 | `settings/mime.ini` ordnet `.mp3` dem Typ `audio/mpeg` zu; jede Podcast-App spielt es ab |
| Bitrate | konstant (CBR) | `itunes:duration` im Feed wird nur für CBR-MP3 berechnet |
| Dateiname | kurz, kleingeschrieben, ohne Leerzeichen, z. B. `example-show-042.mp3` | der Name ist Teil der Download-Adresse |
| Größe | innerhalb der Upload-Grenze Ihrer Website | die Upload-Seite zeigt die Grenze an |

Formate wie M4A, AAC oder Opus stehen nicht in der mitgelieferten `mime.ini`; so hochgeladen, erhalten sie den Typ
`application/octet-stream`, den Podcast-Apps ablehnen. Bitten Sie Ihren Administrator, sie zuerst hinzuzufügen
([Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md)).

## Im Bearbeitungsformular hochladen

Das ist der übliche Weg für eine Episode:

1. Legen Sie die Episode im Ordner der Sendung (oder der Staffel) an, oder bearbeiten Sie eine vorhandene.
2. Wählen Sie im Audiofeld (einem Medienfeld) die Datei unter **Neue Datei hochladen**.
3. Füllen Sie die übrigen Felder aus und drücken Sie **Zur Veröffentlichung schicken**.

Die Datei wird mit dem Formular hochgeladen. Eine große Datei braucht so lange, wie Ihre Verbindung braucht; lassen
Sie die Seite offen, bis das Formular zurückkommt. Wird der Upload abgelehnt, sagt das Feld warum, zum Beispiel dass
die Datei größer ist, als `upload_max_filesize` von PHP erlaubt (`kernel/classes/datatypes/ezmedia/ezmediatype.php`).

## Auf der Upload-Seite hochladen

Die Upload-Seite nimmt viele Dateien auf einmal an ([Bilder, Medien und Uploads](../medien-und-uploads.md#dateien-hochladen)).
Jede Datei wird zu einem Objekt der Klasse, die `settings/upload.ini [CreateSettings] MimeClassMap[]` für ihren Typ
nennt. Im Auslieferungszustand gibt es keinen Eintrag für `audio/mpeg`, daher wird ein MP3 zu einem Objekt **File**
(`DefaultClass=file`): ein Download, kein Player. Verwenden Sie die Upload-Seite für Audio nur, wenn:

- Ihr Administrator `audio/mpeg` Ihrer Episodenklasse zugeordnet hat ([Eine Episodenklasse aufbauen](eine-episodenklasse-aufbauen.md)), oder
- Objekte *File* genau das sind, was Sie wollen, zum Beispiel als Download-Archiv.

Die Option **Keep as drafts** (als Entwürfe behalten) lässt die neuen Objekte unveröffentlicht, sodass Sie Shownotes
ergänzen können, bevor etwas erscheint.

## Playeroptionen

Bei einem Medienfeld mit dem Playertyp **Html5 Audio** bietet das Formular:

| Option | Wirkung auf der Seite | Empfehlung |
|---|---|---|
| **Controller** | `controls`: Abspielen, Pause, Springen, Lautstärke | immer an |
| **Auto-Wiedergabe** | beginnt beim Öffnen der Seite abzuspielen | aus, aus Gründen der Barrierefreiheit und Höflichkeit |
| **Schleife** | beginnt am Ende von vorn | aus bei Episoden |

Das Template schreibt `preload="none"`, sodass die Datei erst geholt wird, wenn jemand auf Abspielen drückt
(`design/standard/templates/content/datatype/view/ezmedia.tpl`).

## Die Datei prüfen

Öffnen Sie die Episode nach dem Veröffentlichen abgemeldet und:

- [ ] drücken Sie auf Abspielen: Das Audio startet, und Springen funktioniert (Downloads beantworten `Range`-Anfragen);
- [ ] sehen Sie sich das Feld in der Administration an: **MIME-Typ** zeigt `audio/mpeg`, **Größe** entspricht Ihrer Datei;
- [ ] öffnen Sie den Feed, sobald er aufgebaut ist: Das `enclosure` des Elements hat denselben Typ und dieselbe
  Länge ([Ihren Feed prüfen](ihren-feed-pruefen.md)).

## Eine Datei ersetzen

Um das Audio einer veröffentlichten Episode zu korrigieren, bearbeiten Sie die Episode, laden die neue Datei im
selben Feld hoch und veröffentlichen. Die Episode behält ihre Remote-ID, daher behandeln Podcast-Apps sie als dieselbe
Episode mit einer neuen Datei. Legen Sie für eine korrigierte Datei keine neue Episode an; Apps würden sie zweimal
auflisten ([Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md)).

Die frühere Version bleibt in der Geschichte des Objekts, sodass Sie zu ihr zurückkehren können; siehe
[Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md#zu-einer-früheren-version-zurückkehren).

## Verwandte Seiten

- [Das Medienfeld und das Dateifeld](medienfeld-und-dateifeld.md)
- [Upload-Grenzen und große Dateien](upload-grenzen-und-grosse-dateien.md)
- [Eine Episode veröffentlichen](eine-episode-veroeffentlichen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
