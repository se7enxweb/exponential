---
title: Was nicht eingebaut ist
audience: podcaster
category: podcasting-streaming
order: 3
summary: Die ehrliche Liste dessen, was eine Exponential-Website für einen Podcast oder einen Stream nicht leistet - kein Transkodieren, keine Live-Einspeisung, keine Staffel- und Episodennummern im Feed, kein Transkript-Tag, keine Statistik pro Hörer, einige Audioformate werden nicht erkannt - und was Sie jeweils stattdessen tun.
keywords: Grenzen, Lücken, nicht unterstützt, Transkodieren, Livestreaming, HLS, itunes:episode, itunes:season, Transkript, Kapitel, m4a, aac, opus, Untertitel, Statistik, was fehlt, limitations, gaps, not supported, transcoding, live streaming, transcript, chapters, captions, statistics, what is missing
type: help
---
# Was nicht eingebaut ist

Diese Seite ist für Podcaster und Produzenten, die eine Sendung mit Exponential planen und die Grenzen kennen möchten,
bevor sie etwas versprechen. Jeder Punkt unten wurde im Code der Versionslinie 6.0 geprüft; jeder sagt, was Sie
stattdessen tun. Wenn eine spätere Version eine Lücke schließt, ändert sich diese Seite.

## Inhalt

1. [Medienverarbeitung](#medienverarbeitung)
2. [Der Podcast-Feed](#der-podcast-feed)
3. [Formate](#formate)
4. [Player, Untertitel und Einbettungen](#player-untertitel-und-einbettungen)
5. [Reichweitenzahlen](#reichweitenzahlen)

## Medienverarbeitung

| Nicht vorhanden | Warum es wichtig ist | Stattdessen |
|---|---|---|
| Kein Transkodieren oder Kodieren | Die Datei, die Sie hochladen, ist die Datei, die Hörer erhalten, mit ihrer Größe und Bitrate | Exportieren Sie das fertige MP3 oder MP4 aus Ihrer Schnittsoftware mit der gewünschten Bitrate und laden Sie es dann hoch |
| Keine Live-Einspeisung, kein HLS oder DASH | Exponential kann keinen Livestream von Ihrem Encoder empfangen oder ausliefern | Streamen Sie über einen Streamingdienst und betten Sie dessen Player ein ([Livestreams und Premieren](livestreams-und-premieren.md)) |
| Keine Werkzeuge für Wellenform, Kapitel oder Clips | Nichts schneidet oder markiert Audio im Browser | Erledigen Sie das in Ihrem Audioeditor; schreiben Sie Kapitelzeiten als Text in die Shownotes |

Kein Code in `kernel/`, `lib/` oder den mitgelieferten Erweiterungen ruft einen Encoder wie ffmpeg auf.

## Der Podcast-Feed

Der Export für Apple Podcasts (`eZRSSExport::generateITunes()` in `kernel/classes/ezrssexport.php`) schreibt die
Kanal- und Elementangaben, die unter [Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md) aufgeführt sind. Er
schreibt **nicht**:

| Nicht geschrieben | Wirkung | Stattdessen |
|---|---|---|
| `itunes:episode`, `itunes:season`, `itunes:episodeType` | Apps zeigen keine Episoden- oder Staffelnummern aus dem Feed | Setzen Sie die Nummer in den Episodentitel ("Episode 42: ...") und verwenden Sie auf der Website Ordner für Staffeln |
| Ein `itunes:explicit` pro Episode | Jede Episode trägt den Wert des Kanals | Setzen Sie **Enthält explizite Inhalte** für die ganze Sendung, wenn eine Episode es braucht |
| Ein `itunes:image` pro Episode | Apps zeigen für jede Episode das Cover der Sendung | Behalten Sie Episodenbilder auf den Seiten der Website ([Cover und Episodenbilder](cover-und-episodenbilder.md)) |
| Tags für Transkripte oder Kapitel | Apps können kein Transkript aus dem Feed abrufen | Veröffentlichen Sie das Transkript auf der Episodenseite ([Transkripte und Untertitel](transkripte-und-untertitel.md)) |
| Einen gewählten Episodenautor | `itunes:author` eines Elements ist der **Benutzer, der das Element angelegt hat** | Nennen Sie Gäste und Moderatoren in den Shownotes ([Gäste und Mitwirkende](gaeste-und-mitwirkende.md)) |

Zwei weitere Regeln ergeben sich aus dem Code: Eine Episode, deren Enclosure-Feld leer ist, fehlt im Feed, und die
Dauer (`itunes:duration`) wird nur für MPEG-1-Layer-III-Audio mit konstanter Bitrate berechnet, also für ein CBR-MP3
(`audioDuration()`). Bei einem VBR-MP3 oder einem anderen Format fehlt die Dauer einfach, was die Verzeichnisse
zulassen.

## Formate

`settings/mime.ini` führt `mp3` und `mp2` als `audio/mpeg`, `oga` als `audio/ogg`, `webm` als `video/webm` und
`audio/webm`, `mp4` als `video/mp4` sowie `wav`. Es hat **keine Einträge für `m4a`, `aac`, `opus`, `vtt` oder
`srt`**. Eine Datei mit einer nicht aufgeführten Endung erhält den Standardtyp `application/octet-stream`
(`eZMimeType::defaultMimeType()` in `lib/ezutils/classes/ezmimetype.php`), und dieser Typ erscheint dann im Enclosure
des Feeds, den Podcast-Apps nicht abspielen.

Stattdessen: Veröffentlichen Sie MP3, oder bitten Sie Ihren Administrator, die fehlende Endung in einem Override von
`mime.ini` zu ergänzen ([Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md)).

## Player, Untertitel und Einbettungen

| Nicht vorhanden | Stattdessen |
|---|---|
| Eine Untertitelspur: Das Medien-Template schreibt kein `<track>`-Element | Untertitel einbrennen, das Template in Ihrem Design überschreiben oder einen Videodienst mit Untertiteln nutzen ([Transkripte und Untertitel](transkripte-und-untertitel.md)) |
| Ein Vorschaubild für den HTML5-Videoplayer des Medienfelds | Die Klasse *NG Video* auf Installationen mit Media Site hat ein Feld *Poster*; sonst ein Design-Override |
| Beliebiger `<iframe>`-Code im formatierten Text | Verwenden Sie den Block *External video* oder *HTML snippet* des Layout-Editors ([Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)) |
| Ein zeitgemäßes Video-Custom-Tag im mitgelieferten Design ezwebin | Sein Tag `video` schreibt Flash-Markup; verwenden Sie es nicht ([Medien im formatierten Text](medien-im-formatierten-text.md)) |

## Reichweitenzahlen

| Nicht vorhanden | Stattdessen |
|---|---|
| Wiedergabezahlen pro Hörer oder pro Episode | Verwenden Sie die Statistiken, die Ihnen Ihre Podcast-Verzeichnisse liefern |
| Ein Download-Zähler beim Medienfeld | Das **Datei**-Feld (`ezbinaryfile`) zählt Downloads; das Medienfeld nicht ([Downloads und Statistiken](downloads-und-statistiken.md)) |
| Private Feed-Adressen pro Abonnent | Nicht verfügbar; siehe [Episoden nur für Mitglieder](episoden-nur-fuer-mitglieder.md) |

## Verwandte Seiten

- [Was Exponential für eine Sendung leistet](was-exponential-fuer-eine-sendung-tut.md)
- [Fehlersuche bei Feeds und Medien](fehlersuche-bei-feeds-und-medien.md)
- [FAQ für Podcaster](faq-fuer-podcaster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
