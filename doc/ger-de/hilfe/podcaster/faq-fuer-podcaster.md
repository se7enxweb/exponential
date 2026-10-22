---
title: FAQ für Podcaster
audience: podcaster
category: podcasting-streaming
order: 45
summary: Kurze Antworten auf die Fragen, die Podcaster, Radioproduzenten und Streamer am häufigsten zu Exponential stellen - Feeds, Formate, Player, Livestreams, Zeitplanung, Statistiken, Mitglieder, Kosten und Grenzen - jeweils mit der Seite, die es erklärt.
keywords: FAQ, Fragen, Antworten, häufige Fragen, Podcast, Radio, Streaming, Feed, Apple Podcasts, Spotify, MP3, live, Zeitplan, Statistiken, Mitglieder, Videopodcast, questions, answers, schedule, statistics, members, video podcast
type: help
---
# FAQ für Podcaster

Kurze Antworten, jeweils mit der Seite, die die Einzelheiten enthält. Wenn Ihre Frage nicht dabei ist, beginnen Sie bei
[Podcaster, Radio und Streaming: hier beginnen](podcaster-hier-beginnen.md).

## Inhalt

1. [Feeds](#feeds)
2. [Dateien und Player](#dateien-und-player)
3. [Streams und Video](#streams-und-video)
4. [Veröffentlichen](#veröffentlichen)
5. [Publikum und Geld](#publikum-und-geld)

## Feeds

**Kann Exponential einen Podcast-Feed veröffentlichen?**
Ja. Ein RSS-Export im Format **Apple Podcasts (RSS 2.0 + iTunes)** schreibt RSS 2.0 mit den iTunes-Elementen und einem
Enclosure pro Episode ([Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md)).

**Funktioniert er mit anderen Apps als Apple Podcasts?**
Der Feed ist gewöhnliches RSS 2.0 mit Enclosures, das Podcast-Apps allgemein lesen. Jedes Verzeichnis hat sein eigenes
Einreichungsverfahren außerhalb von Exponential ([Bei Podcast-Verzeichnissen einreichen](bei-podcast-verzeichnissen-einreichen.md)).

**Kann mein normaler RSS-2.0-Feed das Audio tragen?**
Nein. Die Exporte in RSS 1.0, RSS 2.0 und Atom schreiben keine Enclosures; verwenden Sie das Format Apple Podcasts
([Feeds für Leser und Netzwerke](feeds-fuer-leser-und-netzwerke.md)).

**Stehen Staffel- und Episodennummern im Feed?**
Nein; `itunes:season`, `itunes:episode` und `itunes:episodeType` werden nicht geschrieben. Setzen Sie die Nummer in den
Titel ([Was nicht eingebaut ist](was-nicht-eingebaut-ist.md)).

**Warum steht meine neue Episode noch nicht im Feed?**
Der Feed wird im Auslieferungszustand 1200 Sekunden gecacht, und Apps rufen nach ihrem eigenen Zeitplan ab
([Ihren Feed prüfen](ihren-feed-pruefen.md#wenn-eine-änderung-nicht-erscheint)).

**Wie viele Episoden enthält der Feed?**
So viele, wie die **Anzahl der Objekte** des Exports angibt, mit den mitgelieferten Auswahlwerten bis zu 1000,
neueste zuerst ([Wie eine Sendung zu Inhalten wird](wie-eine-sendung-zu-inhalten-wird.md)).

## Dateien und Player

**Welches Audioformat sollte ich verwenden?**
MP3, konstante Bitrate. Es wird `audio/mpeg` zugeordnet, und der Feed kann seine Dauer berechnen
([Audio hochladen](audio-hochladen.md)).

**Kann ich M4A oder Opus hochladen?**
Erst nachdem der Administrator die Endung in `mime.ini` ergänzt hat; sonst erhält die Datei den Typ
`application/octet-stream` ([Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md)).

**Wandelt oder komprimiert Exponential meine Dateien?**
Nein. Die Datei, die Sie hochladen, ist die Datei, die Hörer erhalten.

**Welchen Player verwendet die Website?**
Das eigene `<audio>`- oder `<video>`-Element des Browsers, aus den Playertypen Html5 Audio und Html5 Video des
Medienfelds ([Das Medienfeld und das Dateifeld](medienfeld-und-dateifeld.md)).

**Wie groß darf eine Episode sein?**
So groß, wie die kleinste der Grenzen von PHP, des Felds und der Upload-Seite es erlaubt
([Upload-Grenzen und große Dateien](upload-grenzen-und-grosse-dateien.md)).

## Streams und Video

**Kann ich aus Exponential live streamen?**
Nein. Streamen Sie über einen Streamingdienst und betten Sie dessen Player ein; die Website trägt die Seite, die
Ankündigung und die Aufzeichnung ([Livestreams und Premieren](livestreams-und-premieren.md)).

**Kann ich YouTube oder Vimeo einbetten?**
Ja, mit dem Block External video des Layout-Editors, der den Player erst nach einem Klick lädt
([Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)).

**Kann ich einen Videopodcast veröffentlichen?**
Ja, wenn die Videodatei auf die Website hochgeladen und als Enclosure zugeordnet ist. Eingebettete Videos haben keine
Datei zum Anhängen.

**Kann ich einem hochgeladenen Video Untertitel hinzufügen?**
Im Auslieferungszustand nicht über das Medienfeld; brennen Sie sie ein, fügen Sie in Ihrem Design eine Untertitelspur
hinzu oder verwenden Sie einen Videodienst ([Transkripte und Untertitel](transkripte-und-untertitel.md)).

## Veröffentlichen

**Kann eine Episode zu einer festen Zeit erscheinen?**
Ja, mit dem Workflow-Ereignis *Bis Datum warten*, sobald ein Administrator es eingerichtet hat; sie erscheint beim
ersten Cron-Lauf nach dem Datum ([Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md)).

**Kann jemand Episoden vorher freigeben?**
Ja, mit dem Workflow-Ereignis *Freigeben* ([Freigaben vor der Veröffentlichung](freigaben-vor-der-veroeffentlichung.md)).

**Ich habe einen Fehler in einer veröffentlichten Episode gemacht. Löschen und neu anlegen?**
Nein: Bearbeiten Sie sie. Ein neu angelegtes Objekt erhält eine neue `guid`, und Apps führen es als neue Episode
([Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md)).

**Kann ich in mehreren Sprachen veröffentlichen?**
Ja, als Übersetzungen jeder Episode oder als getrennte Sendungen, mit einem Feed pro Sprache
([Mehrsprachige Sendungen](mehrsprachige-sendungen.md)).

## Publikum und Geld

**Wie viele Menschen hören zu?**
Die Website kann keine Wiedergaben in Apps zählen. Sie zählt Downloads von Dateifeldern, Seitenaufrufe und Anfragen in
den Server-Logs; die Verzeichnisse haben ihre eigenen Zahlen ([Downloads und Statistiken](downloads-und-statistiken.md)).

**Kann ich Premium-Episoden verkaufen?**
Episoden nur für Mitglieder funktionieren auf der Website über Sektionen und Rollen. Einen privaten Feed pro Abonnent
gibt es nicht ([Episoden nur für Mitglieder](episoden-nur-fuer-mitglieder.md)).

**Was kostet das Hosting einer Sendung?**
Vor allem Speicherplatz und Datenübertragung, die beide mit jeder Episode und jedem Abonnenten wachsen
([Speicher und Bandbreite](speicher-und-bandbreite.md)).

**Darf ich Exponential für eine kommerzielle Sendung kostenlos verwenden?**
Die Software ist frei und quelloffen unter der GNU GPL, Version 2 oder später; Ihre Episoden bleiben Ihre
([Rechte und Lizenzen dessen, was Sie veröffentlichen](rechte-und-lizenzen.md)).

## Verwandte Seiten

- [Glossar für Podcaster](glossar-fuer-podcaster.md)
- [Fehlersuche bei Feeds und Medien](fehlersuche-bei-feeds-und-medien.md)
- [FAQ für Creator](../presse/faq-fuer-creator.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
