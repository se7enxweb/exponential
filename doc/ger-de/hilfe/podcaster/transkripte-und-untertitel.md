---
title: Transkripte und Untertitel
audience: podcaster
category: podcasting-streaming
order: 33
summary: Wie Sie mit Exponential Transkripte von Audioepisoden und Untertitel für Videos veröffentlichen - das Transkript als Text auf der Episodenseite oder als Datei, die fehlende Untertitelspur im Medien-Template und drei Wege darum herum, WebVTT-Dateien und warum der Feed kein Transkript trägt.
keywords: Transkript, Untertitel, WebVTT, vtt, srt, track, Barrierefreiheit, gehörlos, schwerhörig, WCAG, Audiodeskription, durchsuchbar, ezmedia, Video, transcript, captions, subtitles, accessibility, deaf, hard of hearing, audio description, searchable
type: help
---
# Transkripte und Untertitel

Diese Seite ist für Produzenten, die ihre Episoden für Menschen zugänglich machen, die sie nicht hören können, und sie
über die Suche auffindbar machen möchten. Für Audio ist die barrierefreie Alternative ein **Transkript**, für Video mit
Sprache sind es **Untertitel**. Der Bereich für die öffentliche Verwaltung erklärt den Hintergrund der Regeln zur
Barrierefreiheit ([Untertitel und Medien](../verwaltung/barrierefreiheit/untertitel-und-medien.md)).

## Inhalt

1. [Transkripte für Audio](#transkripte-für-audio)
2. [Wohin das Transkript gehört](#wohin-das-transkript-gehört)
3. [Untertitel für Video](#untertitel-für-video)
4. [Untertiteldateien](#untertiteldateien)
5. [Der Feed und Transkripte](#der-feed-und-transkripte)
6. [Checkliste](#checkliste)

## Transkripte für Audio

Ein Transkript ist der vollständige Text dessen, was gesagt wird, mit den Namen der Sprecher und einer Beschreibung
wichtiger Geräusche. Erstellen Sie es aus Ihrem geschnittenen Audio, nicht aus der Rohaufnahme, und korrigieren Sie es
durch Anhören; automatische Transkripte geben Namen und Fachbegriffe falsch wieder.

Formatieren Sie es zum Lesen:

```text
Moderator: Willkommen zu Episode 42. Heute bei uns ist <Gast>.
Gast: Danke für die Einladung.
[Musik wird ausgeblendet]
Moderator: Fangen wir an mit ...
```

## Wohin das Transkript gehört

| Ort | Wie | Geeignet für |
|---|---|---|
| Auf der Episodenseite unter dem Player | ein Feld mit formatiertem Text **Transcript** in der Episodenklasse | die meisten Sendungen: eine Seite, durchsuchbar mit der Suche der Website |
| Eine eigene Seite, von der Episode verlinkt | ein Artikel, der mit der Episode verknüpft ist | sehr lange Transkripte |
| Eine herunterladbare Datei | ein Dateifeld (`ezbinaryfile`), zum Beispiel ein PDF oder eine Textdatei | eine Ergänzung, kein Ersatz für die Seite |

Ein als durchsuchbar markiertes Feld mit formatiertem Text macht die Episode über das auffindbar, was darin gesagt
wurde ([Suchen](../suchen.md)). Fügen Sie das Feld hinzu, wie unter
[Eine Episodenklasse aufbauen](eine-episodenklasse-aufbauen.md) beschrieben.

## Untertitel für Video

Das Ansichts-Template des Medienfelds schreibt ein `<video>`-Element ohne `<track>`-Element
(`design/standard/templates/content/datatype/view/ezmedia.tpl`); im Auslieferungszustand lässt sich daher über das
Medienfeld keine Untertiteldatei anhängen. Drei Wege darum herum:

1. **Offene Untertitel**: Brennen Sie die Untertitel vor dem Hochladen ins Video ein. Sie funktionieren überall und
   lassen sich nicht ausschalten.
2. **Eine Untertitelspur in Ihrem Design**: Ein Entwickler überschreibt das Medien-Template für Ihr Design und ergänzt
   `<track kind="captions" srclang="en" src="...">`, das auf eine WebVTT-Datei in einem zweiten Dateifeld Ihrer
   Videoklasse verweist ([Template-Overrides](../ersteller/website-ersteller/template-overrides.md)). Das ist Designarbeit,
   keine eingebaute Funktion.
3. **Ein Videodienst mit Untertiteln**, auf der Seite eingebettet ([Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)).

Bei einem Livestream kommen Untertitel vom Streamingdienst, wenn er sie anbietet.

## Untertiteldateien

WebVTT (`.vtt`) und SubRip (`.srt`) stehen nicht in der mitgelieferten `settings/mime.ini`. So hochgeladen, erhalten
sie den Typ `application/octet-stream`, und Browser akzeptieren eine solche Datei nicht als `<track>`. Wenn Ihr Design
eine Untertitelspur verwendet, ergänzt der Administrator zuerst den Typ:

```ini
# settings/override/mime.ini.append.php
[vtt]
Types[]=text/vtt
```

Siehe [Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md).

## Der Feed und Transkripte

Der Podcast-Feed schreibt kein Transkript-Element ([Was nicht eingebaut ist](was-nicht-eingebaut-ist.md)). Podcast-Apps
können Ihr Transkript daher nicht aus dem Feed abrufen. Verlinken Sie das Transkript in den Shownotes ("Transkript:
<Adresse>"), damit es in Apps, die die Beschreibung zeigen, nur einen Fingertipp entfernt ist.

## Checkliste

- [ ] Jede Audioepisode hat ein Transkript auf der Seite oder einen Klick entfernt.
- [ ] Jedes Video mit Sprache hat Untertitel.
- [ ] Die Sprecher sind im Transkript genannt.
- [ ] Wichtige Geräusche und visuelle Informationen sind beschrieben.
- [ ] Das Transkript ist in den Shownotes verlinkt.
- [ ] Der Player hat Bedienelemente und startet nicht von selbst ([Barrierefreie Player und Seiten](barrierefreie-player-und-seiten.md)).

## Verwandte Seiten

- [Untertitel und barrierefreies Video](../presse/untertitel-und-barrierefreie-videos.md)
- [Untertitel und Medien](../verwaltung/barrierefreiheit/untertitel-und-medien.md)
- [Barrierefreie Player und Seiten](barrierefreie-player-und-seiten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
