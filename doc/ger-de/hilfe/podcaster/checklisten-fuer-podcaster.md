---
title: Checklisten für Podcaster
audience: podcaster
category: podcasting-streaming
order: 46
summary: Gebrauchsfertige Checklisten für eine Sendung mit Exponential - eine Sendung starten, jede Episode veröffentlichen, den Feed prüfen, ein Live-Ereignis, eine Episode zurückziehen und eine monatliche Kontrolle - jeder Punkt mit der Seite verlinkt, die ihn erklärt.
keywords: Checkliste, Checkliste zum Start, Checkliste für Episoden, Checkliste für den Feed, Checkliste für Live-Ereignisse, monatliche Prüfung, Podcast-Start, Qualität, Veröffentlichung, Überprüfung, checklist, launch checklist, episode checklist, feed checklist, live event checklist, monthly check, podcast launch, quality, release, review
type: help
---
# Checklisten für Podcaster

Diese Seite sammelt die Checklisten dieses Bereichs an einem Ort, zum Ausdrucken oder Kopieren in die Werkzeuge Ihres
Teams. Jeder Punkt verlinkt auf die Seite, die ihn erklärt.

## Inhalt

1. [Eine Sendung starten](#eine-sendung-starten)
2. [Jede Episode](#jede-episode)
3. [Der Feed nach Änderungen](#der-feed-nach-änderungen)
4. [Ein Live-Ereignis](#ein-live-ereignis)
5. [Eine Episode zurückziehen](#eine-episode-zurückziehen)
6. [Einmal im Monat](#einmal-im-monat)

## Eine Sendung starten

- [ ] Die Episodenklasse existiert und hat die Felder, die die Sendung braucht ([Eine Episodenklasse aufbauen](eine-episodenklasse-aufbauen.md)).
- [ ] Das Medienfeld verwendet Html5 Audio oder Html5 Video ([Das Medienfeld und das Dateifeld](medienfeld-und-dateifeld.md)).
- [ ] Der Ordner der Sendung, die Staffelordner und der Ordner für Gäste existieren ([Die Sendungsstruktur einrichten](die-sendungsstruktur-einrichten.md)).
- [ ] Die Upload-Grenzen passen zur größten Episode ([Upload-Grenzen und große Dateien](upload-grenzen-und-grosse-dateien.md)).
- [ ] Das Hosting deckt Speicherplatz, Datenverkehr und Sicherungen für ein Jahr ab ([Speicher und Bandbreite](speicher-und-bandbreite.md)).
- [ ] Der Podcast-Export ist aufgebaut, aktiv und hat alle Kanalfelder ([Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md)).
- [ ] Die Adresse des Titelbilds öffnet das Cover-Bild ([Cover und Episodenbilder](cover-und-episodenbilder.md)).
- [ ] Die E-Mail des Eigentümers ist eine Funktionsadresse der Sendung ([Referenz der Kanalfelder](referenz-der-kanalfelder.md)).
- [ ] Zeitplanung und Freigabe sind bei Bedarf eingerichtet ([Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md), [Freigaben vor der Veröffentlichung](freigaben-vor-der-veroeffentlichung.md)).
- [ ] Testepisoden sind entfernt und der Feed ist geprüft ([Ihren Feed prüfen](ihren-feed-pruefen.md)).
- [ ] Die Datenschutzerklärung nennt eingebettete Player und Logs ([Datenschutz und Player von Drittanbietern](datenschutz-und-player-von-drittanbietern.md)).
- [ ] Der Feed ist bei den gewählten Verzeichnissen eingereicht ([Bei Podcast-Verzeichnissen einreichen](bei-podcast-verzeichnissen-einreichen.md)).

## Jede Episode

- [ ] Fertiges MP3, konstante Bitrate, lesbarer Dateiname ([Audio hochladen](audio-hochladen.md)).
- [ ] Im richtigen Ordner der Sendung oder Staffel angelegt ([Eine Episode veröffentlichen](eine-episode-veroeffentlichen.md)).
- [ ] Titel mit Nummer, kurze Beschreibung, Shownotes ([Vorlage für Shownotes](vorlage-fuer-shownotes.md)).
- [ ] Gäste verknüpft und wie vereinbart genannt ([Gäste und Mitwirkende](gaeste-und-mitwirkende.md)).
- [ ] Sponsoren offengelegt ([Shownotes schreiben](shownotes-schreiben.md)).
- [ ] Musik und Clips geklärt und genannt ([Rechte und Lizenzen dessen, was Sie veröffentlichen](rechte-und-lizenzen.md)).
- [ ] Transkript auf der Seite oder verlinkt ([Transkripte und Untertitel](transkripte-und-untertitel.md)).
- [ ] Controller an, Auto-Wiedergabe und Schleife aus ([Barrierefreie Player und Seiten](barrierefreie-player-und-seiten.md)).
- [ ] Veröffentlichungsdatum gesetzt oder sofort veröffentlicht ([Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md)).
- [ ] Nach der Veröffentlichung: Die Seite spielt abgemeldet ab; der Feed führt die Episode mit dem richtigen Enclosure.

## Der Feed nach Änderungen

Durchführen nach Änderungen am Export, an der Klasse, am Hostnamen, an HTTPS oder an einem Proxy:

- [ ] Der Feed antwortet mit `200` und `application/rss+xml`.
- [ ] Links und Enclosures verwenden den öffentlichen Host und `https://`.
- [ ] Jedes Element hat ein Enclosure mit einem Audio- oder Videotyp und der echten Länge.
- [ ] `Range`-Anfragen an eine Datei antworten mit `206`.
- [ ] Bestehende Episoden haben ihre `guid` behalten.

Einzelheiten: [Ihren Feed prüfen](ihren-feed-pruefen.md).

## Ein Live-Ereignis

- [ ] Der Stream ist beim Streamingdienst eingerichtet, mit einer festen Player-Adresse ([Livestreams und Premieren](livestreams-und-premieren.md)).
- [ ] Die Live-Seite existiert, mit Uhrzeit und Zeitzone und dem Block External video ([Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)).
- [ ] Die Seite ist angekündigt oder geplant.
- [ ] Eine Layout-Regel schaltet die Seite der Sendung für das Ereignis um, wenn gewünscht.
- [ ] Danach: Aufzeichnung hochgeladen oder eingebettet, Notizen und Transkript ergänzt, Audio als Episode veröffentlicht.

Die Produktionsseite: [Leitfaden für Livestreams](../presse/leitfaden-fuer-livestreams.md).

## Eine Episode zurückziehen

- [ ] Verstecken (umkehrbar) oder in den Papierkorb entfernen, nicht löschen ([Episoden verbergen und zurückziehen](episoden-verbergen-und-zurueckziehen.md)).
- [ ] Keine andere Seite, Einbettung oder Newsletter verlinkt sie.
- [ ] Der Feed führt sie nach der Cache-Zeit nicht mehr.
- [ ] Grund und Datum sind festgehalten.

## Einmal im Monat

- [ ] Die RSS-Liste zeigt keine Markierung **Braucht Aufmerksamkeit** ([RSS-Feeds](../../anleitungen/rss-feeds.md)).
- [ ] Die **Anzahl der Objekte** deckt noch das Archiv ab.
- [ ] Der belegte Speicherplatz liegt im Plan ([Speicher und Bandbreite](speicher-und-bandbreite.md)).
- [ ] Download-Trends notiert, mit ihrer Definition ([Downloads und Statistiken](downloads-und-statistiken.md)).
- [ ] Defekte Links in Shownotes behoben ([Link- und Inhaltsprüfungen](../ersteller/website-ersteller/link-und-inhaltspruefungen.md)).
- [ ] Tags aufgeräumt ([Tags und Themen](tags-und-themen.md)).
- [ ] Veraltete Entwürfe entfernt ([Ein Veröffentlichungskalender](ein-veroeffentlichungskalender.md)).

## Verwandte Seiten

- [Checklisten für die Presse](../presse/checklisten-fuer-die-presse.md)
- [FAQ für Podcaster](faq-fuer-podcaster.md)
- [Lesepfad für Podcaster](lesepfad-fuer-podcaster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
