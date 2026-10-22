---
title: Livestreams und Premieren
audience: podcaster
category: podcasting-streaming
order: 25
summary: Was eine Exponential-Website für eine Livesendung, eine Radiosendung oder eine Videopremiere leisten kann und was nicht - sie bettet den Player eines Streamingdienstes ein, kündigt den Stream an, schaltet das Layout einer Seite für die Dauer des Ereignisses um und bewahrt die Aufzeichnung -, während der Stream selbst über den Streamingdienst läuft.
keywords: Livestream, Livesendung, Radio, Simulcast, Premiere, Streamingdienst, Einbettung, Layout-Regel, Zeitbedingung, Ankündigung, Aufzeichnung, Zeitplan, Veranstaltungsseite, live stream, live show, premiere, streaming service, embed, layout rule, time condition, announcement, replay, schedule, event page
type: help
---
# Livestreams und Premieren

Diese Seite ist für Radioproduzenten, Streamer und Podcaster, die live gehen oder ein Video als Premiere zeigen.
Exponential empfängt, kodiert und liefert keinen Livestream aus: Es gibt keine Einspeisung, kein HLS oder DASH und
keinen eigenen Liveplayer im Kernel oder in den mitgelieferten Erweiterungen ([Was nicht eingebaut ist](was-nicht-eingebaut-ist.md)).
Was die Website leistet, ist alles rund um den Stream: die Seite, auf die die Menschen kommen, die Ankündigung, den
Player Ihres Streamingdienstes und danach die Aufzeichnung.

## Inhalt

1. [Die Teile eines Live-Ereignisses](#die-teile-eines-live-ereignisses)
2. [Vorher: ankündigen](#vorher-ankündigen)
3. [Währenddessen: die Live-Seite](#währenddessen-die-live-seite)
4. [Eine Seite, die von selbst umschaltet](#eine-seite-die-von-selbst-umschaltet)
5. [Danach: die Aufzeichnung](#danach-die-aufzeichnung)
6. [Der Livestream eines Radiosenders](#der-livestream-eines-radiosenders)

## Die Teile eines Live-Ereignisses

| Teil | Wo er liegt | Seite |
|---|---|---|
| Der Stream | Ihr Streamingdienst (oder Ihr Sendeablaufsystem beim Radio) | außerhalb von Exponential |
| Die Live-Seite | ein Objekt auf Ihrer Website, mit eingebettetem Player des Dienstes | [Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md) |
| Die Ankündigung | ein Artikel oder eine Nachricht, veröffentlicht oder geplant | [Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md) |
| Das Umschalten zum Beginn | eine Layout-Regel mit einer Zeitbedingung | [Layout-Regeln](../ersteller/website-ersteller/layout-regeln.md) |
| Die Aufzeichnung | die Aufnahme, hochgeladen oder eingebettet, dazu Shownotes | [Video hochladen](video-hochladen.md) |

Für die Produktionsseite (Szenen, Ablaufplan, Chat, Privatsphäre vor der Kamera) lesen Sie den
[Leitfaden für Livestreams](../presse/leitfaden-fuer-livestreams.md) im Pressebereich.

## Vorher: ankündigen

1. Legen Sie die Live-Seite vorab an, zum Beispiel einen Artikel "Live: <Titel>, <Datum und Uhrzeit>" mit dem Thema der
   Sendung, dem Beginn in welcher Zeitzone und den Gästen.
2. Betten Sie den Player des Streamingdienstes mit dem Block **External video** ein (YouTube, Vimeo, Dailymotion oder
   die Player-Adresse eines anderen Dienstes). Viele Dienste vergeben für einen geplanten Stream eine feste
   Player-Adresse; verwenden Sie diese.
3. Veröffentlichen Sie die Seite jetzt, oder planen Sie sie mit dem Ereignis *Bis Datum warten*, wenn Ihre Website es
   hat.
4. Verlinken Sie die Seite von der Seite der Sendung, im Newsletter und auf Ihren Kanälen.

## Währenddessen: die Live-Seite

- Der Player lädt, nachdem der Besucher auf **Load video** klickt; die Seite selbst sendet bis dahin also nichts an den
  Dienst ([Datenschutz und Player von Drittanbietern](datenschutz-und-player-von-drittanbietern.md)).
- Halten Sie den Text der Seite kurz und stabil. Die Seite während des Streams zu bearbeiten ist möglich, aber jede
  veröffentlichte Änderung ist eine neue Version; bereiten Sie Korrekturen als Entwurf vor.
- Wenn der Stream ausfällt, veröffentlichen Sie einen kurzen Hinweis auf der Seite; die Änderung ist auf den meisten
  Websites sofort sichtbar.

## Eine Seite, die von selbst umschaltet

Mit Exponential Layouts kann eine Layout-Regel eine Bedingung `time` mit `from` und `to` tragen. Ordnen Sie der Seite
der Sendung für die Dauer des Ereignisses ein "Live"-Layout zu, mit höherer Priorität als die normale Regel der Seite;
davor und danach erscheint das normale Layout. Das Format ist JSON, zum Beispiel
`{"from":"2026-10-01","to":"2026-10-31"}`, und ein leerer Zeitraum passt immer
([Layout-Regeln](../ersteller/website-ersteller/layout-regeln.md#bedingungen)).

Prüfen Sie die Seite abgemeldet kurz nach dem Beginn und nach dem Ende. Caches können das vorherige Layout eine Weile
festhalten; schaltet sie nicht um, fragen Sie Ihren Administrator, welche Caches für diese Seite gelten.

## Danach: die Aufzeichnung

1. Laden Sie die Aufnahme vom Streamingdienst herunter.
2. Laden Sie sie als Videoepisode hoch, oder betten Sie die Aufzeichnung des Dienstes mit dem Block External video ein.
3. Machen Sie aus der Live-Seite die Seite der Aufzeichnung: Ändern Sie den Titel, ergänzen Sie Shownotes, Kapitel und
   das Transkript ([Vorlage für Shownotes](vorlage-fuer-shownotes.md), [Transkripte und Untertitel](transkripte-und-untertitel.md)).
4. Wenn die Sendung auch als Podcast läuft, veröffentlichen Sie das Audio als Episode, damit es in den Feed gelangt
   ([Eine Episode veröffentlichen](eine-episode-veroeffentlichen.md)).

Behalten Sie dieselbe Seite und Adresse: Links, die vor dem Ereignis geteilt wurden, funktionieren weiter.

## Der Livestream eines Radiosenders

Der durchgehende Stream eines Senders ist derselbe Fall: Der Stream kommt vom Streaming-Server des Senders, und die
Website bettet seinen Player ein oder verlinkt ihn. Die Website kann den Programmplan als Inhalte führen (auf
Installationen mit dem mitgelieferten Klassenpaket gibt es eine Klasse für einen Veranstaltungskalender,
`event_calendar` und `event`), dazu die Seiten der Sendungen, die Moderatoren und die Episoden auf Abruf mit ihren
Podcast-Feeds.

## Verwandte Seiten

- [Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)
- [Leitfaden für Livestreams](../presse/leitfaden-fuer-livestreams.md)
- [Landingpages mit Layouts](../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
