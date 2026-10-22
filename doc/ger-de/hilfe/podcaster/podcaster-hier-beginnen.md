---
title: "Podcaster, Radio und Streaming: hier beginnen"
audience: podcaster
category: podcasting-streaming
order: 1
summary: Wo Podcaster, Radioproduzenten und Streamer beginnen. Was eine Exponential-Website für eine Sendung leistet - Medienhosting, HTML5-Player, ein Feed für Apple Podcasts, eingebettete Streams, Zeitplanung, Freigabe, Rechte und Zugriff -, was sie nicht leistet, und eine Übersicht aller Seiten dieses Bereichs.
keywords: Podcast, Podcaster, Radio, Streaming, Streamer, Videopodcast, Livestream, Feed, Apple Podcasts, RSS, Episoden, Shownotes, hier beginnen, Überblick, Übersicht, podcast, radio, streaming, video podcast, live stream, episodes, show notes, start here, overview, index
featured: true
type: help
---
# Podcaster, Radio und Streaming: hier beginnen

Dieser Bereich der Hilfe ist für Menschen, die Sendungen mit Exponential veröffentlichen: Podcaster, Radioproduzenten,
Video-Creator und Streamer sowie die Redakteure und Administratoren, die mit ihnen arbeiten. Er erklärt, wie eine
Sendung zu Inhalten wird, wie Episoden auf die Website und in Podcast-Apps gelangen, wie Streams und Videos anderer
Dienste eingebettet werden und wie Veröffentlichungstermine, Freigabe, Rechte und Zugriff gehandhabt werden.

Jede Aussage auf diesen Seiten wurde im Code, in den mitgelieferten Einstellungen oder in einer laufenden Installation
geprüft und nennt ihre Quelle. Wo Exponential etwas nicht kann, sagen die Seiten das und nennen die Alternative.

> **Hinweis**
> In diesem Bereich geht es darum, Ihre eigene Sendung mit Exponential zu veröffentlichen. Wenn Sie als Journalist
> oder Creator in einem Podcast, einer Radiosendung oder einem Stream **über** Exponential sprechen, beginnen Sie bei
> [Presse, Medien und Influencer: hier beginnen](../presse/presse-hier-beginnen.md).

## Inhalt

1. [Was Exponential für eine Sendung leistet](#was-exponential-für-eine-sendung-leistet)
2. [Ihre erste Stunde](#ihre-erste-stunde)
3. [Fünf Regeln für jede Sendung](#fünf-regeln-für-jede-sendung)
4. [Übersicht der Podcaster-Seiten](#übersicht-der-podcaster-seiten)
5. [Seiten für andere Zielgruppen, die Sie verwenden werden](#seiten-für-andere-zielgruppen-die-sie-verwenden-werden)
6. [Diese Seiten wiederfinden](#diese-seiten-wiederfinden)

## Was Exponential für eine Sendung leistet

- **Speichert und spielt** Audio und Video mit dem Datentyp Medien und dem eigenen Player des Browsers ab (Html5
  Audio, Html5 Video).
- **Veröffentlicht einen Podcast-Feed**: einen RSS-Export im Format *Apple Podcasts (RSS 2.0 + iTunes)* mit
  Enclosures, stabilen `guid`-Werten und den Kanalfeldern, die Apple verlangt (`kernel/classes/ezrssexport.php`).
- **Bettet Player ein** von YouTube, Vimeo, Dailymotion und anderen Diensten, mit dem Block External video des
  Layout-Editors, der sie erst nach einem Klick lädt.
- **Plant und gibt frei** mit den Workflow-Ereignissen *Bis Datum warten* und *Freigeben*.
- **Schützt und ordnet** mit Sektionen, Rollen, Objektzuständen, Tags und Übersetzungen.

Es **transkodiert keine** Dateien, streamt nicht live, schreibt keine Staffel- oder Episodennummern in den Feed und
zählt keine Wiedergaben in Podcast-Apps ([Was nicht eingebaut ist](was-nicht-eingebaut-ist.md)).

## Ihre erste Stunde

1. **Den Umfang kennen** (10 Minuten): [Was Exponential für eine Sendung leistet](was-exponential-fuer-eine-sendung-tut.md)
   und [Was nicht eingebaut ist](was-nicht-eingebaut-ist.md).
2. **Die Struktur verstehen** (10 Minuten): [Wie eine Sendung zu Inhalten wird](wie-eine-sendung-zu-inhalten-wird.md).
3. **Den Feed verstehen** (15 Minuten): [Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md).
4. **Eine Episode von Anfang bis Ende begleiten** (15 Minuten): [Eine Episode veröffentlichen](eine-episode-veroeffentlichen.md).
5. **Wissen, wen Sie was fragen** (10 Minuten): [Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md).

Eine ausführlichere Reihenfolge für jede Rolle finden Sie im [Lesepfad für Podcaster](lesepfad-fuer-podcaster.md).

## Fünf Regeln für jede Sendung

1. **Bearbeiten, nie neu anlegen.** Die `guid` einer Episode ist die Remote-ID ihres Objekts; ein neu angelegtes
   Element ist für jede App eine neue Episode ([Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md)).
2. **Die Feed-Adresse behalten.** Wählen Sie die Zugriffs-URL einmal; für einen Umzug verwenden Sie **Umgezogen nach**
   ([Eine Sendung umziehen, pausieren oder beenden](eine-sendung-umziehen-pausieren-oder-beenden.md)).
3. **MP3 mit konstanter Bitrate veröffentlichen**, sofern Ihr Administrator keine anderen Typen hinzugefügt hat
   ([Audio hochladen](audio-hochladen.md)).
4. **Abgemeldet prüfen.** Öffnen Sie nach jeder Veröffentlichung die Episode und den Feed als Besucher
   ([Ihren Feed prüfen](ihren-feed-pruefen.md)).
5. **Menschen und Rechte respektieren.** Klären Sie Musikrechte, sprechen Sie sich mit Gästen ab, halten Sie
   personenbezogene Daten von öffentlichen Seiten fern
   ([Rechte und Lizenzen dessen, was Sie veröffentlichen](rechte-und-lizenzen.md), [Gäste und Mitwirkende](gaeste-und-mitwirkende.md)).

## Übersicht der Podcaster-Seiten

### Orientierung

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Was Exponential für eine Sendung leistet](was-exponential-fuer-eine-sendung-tut.md) | Jede Fähigkeit mit ihrer Quelle und Seite sehen |
| [Was nicht eingebaut ist](was-nicht-eingebaut-ist.md) | Die Grenzen kennen, bevor Sie etwas versprechen |
| [Wie eine Sendung zu Inhalten wird](wie-eine-sendung-zu-inhalten-wird.md) | Sendungen, Staffeln und Episoden so strukturieren, dass der Feed funktioniert |
| [Das Medienfeld und das Dateifeld](medienfeld-und-dateifeld.md) | Wählen, wie Episoden Audio und Video speichern |
| [Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md) | Jedes Element des Feeds verstehen und wissen, wann es sich ändert |
| [Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md) | Gezielt nach den richtigen Einstellungen fragen |

### Eine Sendung einrichten

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Eine Klasse für Episoden wählen](eine-klasse-fuer-episoden-waehlen.md) | Video, Audio, NG Video, Datei oder Ihre eigene Klasse wählen |
| [Eine Episodenklasse aufbauen](eine-episodenklasse-aufbauen.md) | Eine Klasse mit Shownotes, Gästen, Veröffentlichungsdatum und Transkript bauen |
| [Die Sendungsstruktur einrichten](die-sendungsstruktur-einrichten.md) | Die Ordner für Sendung, Staffeln und Gäste anlegen |
| [Cover und Episodenbilder](cover-und-episodenbilder.md) | Cover-Bilder in den Feed und Bilder auf die Website bringen |

### Episoden

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Audio hochladen](audio-hochladen.md), [Video hochladen](video-hochladen.md) | Episodendateien vorbereiten und hochladen |
| [Eine Episode veröffentlichen](eine-episode-veroeffentlichen.md) | Eine Episode vom Entwurf bis in die Podcast-Apps bringen |
| [Shownotes schreiben](shownotes-schreiben.md), [Vorlage für Shownotes](vorlage-fuer-shownotes.md) | Notizen schreiben, die auf der Website und in Apps funktionieren |
| [Gäste und Mitwirkende](gaeste-und-mitwirkende.md) | Menschen nennen und mit ihren Daten sorgsam umgehen |
| [Transkripte und Untertitel](transkripte-und-untertitel.md), [Barrierefreie Player und Seiten](barrierefreie-player-und-seiten.md) | Jede Episode barrierefrei machen |
| [Tags und Themen](tags-und-themen.md) | Das Archiv nach Themen ordnen |
| [Mehrsprachige Sendungen](mehrsprachige-sendungen.md) | Eine Sendung in mehreren Sprachen veröffentlichen |

### Feeds

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md) | Den Export für Apple Podcasts Schritt für Schritt anlegen |
| [Referenz der Kanalfelder](referenz-der-kanalfelder.md), [Referenz der Episodenzuordnung](referenz-der-episodenzuordnung.md) | Jedes Feld richtig ausfüllen und zuordnen |
| [Ihren Feed prüfen](ihren-feed-pruefen.md) | Den Feed und die Dateien selbst testen |
| [Bei Podcast-Verzeichnissen einreichen](bei-podcast-verzeichnissen-einreichen.md) | Den Feed einreichen und danach gesund halten |
| [Eine Sendung umziehen, pausieren oder beenden](eine-sendung-umziehen-pausieren-oder-beenden.md) | Eine Sendung umziehen, abschließen oder zurückziehen, ohne Abonnenten zu verlieren |
| [Feeds für Leser und Netzwerke](feeds-fuer-leser-und-netzwerke.md) | RSS, Atom und OPML für Leser und Netzwerke veröffentlichen |

### Streams und Einbettungen

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md) | Player von Video- und Streamingdiensten anzeigen |
| [Livestreams und Premieren](livestreams-und-premieren.md) | Die Website rund um eine Livesendung oder Premiere betreiben |
| [Medien im formatierten Text](medien-im-formatierten-text.md) | Eine Episode oder einen Clip in einen Artikel setzen |
| [Datenschutz und Player von Drittanbietern](datenschutz-und-player-von-drittanbietern.md) | Player einbetten, ohne Hörer unnötig preiszugeben |

### Veröffentlichung, Rechte und Zugriff

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md), [Ein Veröffentlichungskalender](ein-veroeffentlichungskalender.md) | Pünktlich veröffentlichen und die Staffel planen |
| [Freigaben vor der Veröffentlichung](freigaben-vor-der-veroeffentlichung.md), [Produktionsphasen mit Objektzuständen](produktionsphasen-mit-objektzustaenden.md) | Episoden prüfen und Produktionsphasen verfolgen |
| [Episoden verbergen und zurückziehen](episoden-verbergen-und-zurueckziehen.md) | Episoden vorübergehend oder endgültig herausnehmen |
| [Rechte und Lizenzen dessen, was Sie veröffentlichen](rechte-und-lizenzen.md) | Lizenzen festhalten und Rechteprobleme lösen |
| [Episoden nur für Mitglieder](episoden-nur-fuer-mitglieder.md) | Bonusepisoden Mitgliedern vorbehalten |

### Hosting

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Speicher und Bandbreite](speicher-und-bandbreite.md) | Speicherplatz, Datenverkehr und Sicherungen planen |
| [Upload-Grenzen und große Dateien](upload-grenzen-und-grosse-dateien.md) | Upload-Grenzen verstehen und anheben |
| [CDN und externes Medienhosting](cdn-und-externes-medienhosting.md) | Wissen, was ein CDN und anderes Hosting leisten können |
| [Downloads und Statistiken](downloads-und-statistiken.md) | Reichweitenzahlen ehrlich lesen |

### Nachschlagen

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Fehlersuche bei Feeds und Medien](fehlersuche-bei-feeds-und-medien.md) | Die häufigsten Probleme beheben |
| [FAQ für Podcaster](faq-fuer-podcaster.md) | Schnelle Antworten bekommen |
| [Checklisten für Podcaster](checklisten-fuer-podcaster.md) | Einen Start, eine Episode, einen Feed, ein Live-Ereignis prüfen |
| [Glossar für Podcaster](glossar-fuer-podcaster.md) | Begriffe aus Podcasting und Exponential nachschlagen |
| [Lesepfad für Podcaster](lesepfad-fuer-podcaster.md) | Den Bereich in der Reihenfolge Ihrer Rolle lesen |

## Seiten für andere Zielgruppen, die Sie verwenden werden

| Seite | Warum Podcaster sie lesen |
|---|---|
| [Bilder, Medien und Uploads](../medien-und-uploads.md) | Die Medienbibliothek und die Upload-Seite, für Redakteure |
| [RSS-Feeds](../../anleitungen/rss-feeds.md) | Die vollständige Anleitung zur RSS-Seite, zu Exporten und Importen |
| [RSS: Feeds für Apple Podcasts, eine seitenweise Feed-Liste und sicherere Exporte](../../funktionen/6.0/rss-podcast-und-feed-liste.md) | Die Funktionsbeschreibung des Podcast-Formats |
| [RSS- und Podcast-Feeds](../kunden/wachstum/rss-und-podcast-feeds.md) | Feeds und Podcasts aus Sicht des Website-Betreibers |
| [Zeitgesteuertes Veröffentlichen](../kunden/alltag/zeitgesteuertes-veroeffentlichen.md) | Alles, was eine Website zu einem Termin tun kann |
| [Der Layout-Editor](../layout-editor.md) | Der Seitenbaukasten, der eingebettete Player aufnimmt |
| [Untertitel und Medien](../verwaltung/barrierefreiheit/untertitel-und-medien.md) | Untertitel und Transkripte im Detail |
| [Gäste in Podcast und Radio](../presse/gaeste-in-podcast-und-radio.md), [Gliederungen für Podcast-Episoden](../presse/gliederungen-fuer-podcast-folgen.md) | Gäste und Episoden vorbereiten, aus dem Pressebereich |
| [Leitfaden für Livestreams](../presse/leitfaden-fuer-livestreams.md), [Untertitel und barrierefreies Video](../presse/untertitel-und-barrierefreie-videos.md) | Einen Livestream und barrierefreies Video produzieren |
| [CDN](../hosting/leistung/cdn.md), [Speicheraufbau](../hosting/betrieb/aufbau-des-speichers.md) | Womit sich Ihr Hoster befasst |
| [Freie Dokumentation](../freie-dokumentation.md) | Was Sie mit diesen Seiten tun dürfen |

## Diese Seiten wiederfinden

- Filtern Sie in der Hilfe nach der Zielgruppe **Podcaster, Radio und Streaming**, oder öffnen Sie **Alle Seiten** und
  den Ordner `podcaster`.
- Jede Seite hat **Auf GitHub bearbeiten**. Wenn hier etwas falsch oder veraltet ist, sagen Sie es uns dort oder im
  [Issue-Tracker der Community](https://github.com/se7enxweb/exponential-community/issues).

## Verwandte Seiten

- [Was Exponential für eine Sendung leistet](was-exponential-fuer-eine-sendung-tut.md)
- [Lesepfad für Podcaster](lesepfad-fuer-podcaster.md)
- [Presse, Medien und Influencer: hier beginnen](../presse/presse-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
