---
title: Eine Episode veröffentlichen
audience: podcaster
category: podcasting-streaming
order: 13
summary: Der ganze Weg einer Episode vom Entwurf bis in die Podcast-Apps - im richtigen Ordner anlegen, Titel, Audio und Shownotes ausfüllen, Vorschau ansehen, sofort oder zu einem Termin veröffentlichen und die Seite und den Feed prüfen - mit dem, was hinter jedem Schritt geschieht.
keywords: Episode veröffentlichen, neue Episode, Veröffentlichung, Entwurf, Vorschau, zur Veröffentlichung schicken, Feed, Podcast-Apps, Checkliste für Episoden, Shownotes, Audio, publish episode, new episode, release, draft, preview, send for publishing, podcast apps, episode checklist, show notes
type: help
---
# Eine Episode veröffentlichen

Diese Seite ist für Produzenten und Redakteure, die eine Episode veröffentlichen, vom leeren Entwurf bis zu dem
Moment, in dem Podcast-Apps sie abrufen können. Sie setzt voraus, dass die Struktur der Sendung und der Feed
existieren ([Die Sendungsstruktur einrichten](die-sendungsstruktur-einrichten.md),
[Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Den Entwurf anlegen](#den-entwurf-anlegen)
3. [Die Episode ausfüllen](#die-episode-ausfüllen)
4. [Vorschau](#vorschau)
5. [Jetzt oder später veröffentlichen](#jetzt-oder-später-veröffentlichen)
6. [Nach dem Veröffentlichen](#nach-dem-veröffentlichen)

## Bevor Sie beginnen

Halten Sie bereit: die fertige Audiodatei (MP3, konstante Bitrate), den Titel, eine Beschreibung in ein oder zwei
Sätzen, die Shownotes ([Vorlage für Shownotes](vorlage-fuer-shownotes.md)), Namen und Links der Gäste und ein Bild, wenn
Ihre Klasse eines hat.

## Den Entwurf anlegen

1. Öffnen Sie in der **Inhaltsstruktur** den Ordner der Sendung oder, wenn die Sendung Staffeln hat, den Ordner der
   Staffel.
2. Drücken Sie unter **Unterelemente** auf **Neu erstellen** und wählen Sie die Episodenklasse.

Die Episode muss **unterhalb des Ordners angelegt werden, den der Feed liest**. Eine anderswo angelegte Episode ist
auf der Website, aber nicht im Feed ([Wie eine Sendung zu Inhalten wird](wie-eine-sendung-zu-inhalten-wird.md)).

## Die Episode ausfüllen

| Feld (Namen je nach Klasse verschieden) | Was Sie eintragen |
|---|---|
| Titel | Den Titel der Episode, wie Apps ihn zeigen sollen; mit Nummer, wenn Sie Nummern verwenden ("42: ...") |
| Beschreibung oder Anrisstext | Ein oder zwei Sätze; oft das, was der Feed als Beschreibung der Episode zeigt |
| Audio | Die Datei, unter **Neue Datei hochladen** ([Audio hochladen](audio-hochladen.md)) |
| Shownotes | Links, Kapitel, Mitwirkende ([Shownotes schreiben](shownotes-schreiben.md)) |
| Gäste | Die Personen-Objekte verknüpfen ([Gäste und Mitwirkende](gaeste-und-mitwirkende.md)) |
| Veröffentlichungsdatum | Nur wenn Ihre Website Veröffentlichungen plant ([Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md)) |

**Entwurf speichern und beenden** bewahrt den Entwurf für später auf; er steht unter **Meine Entwürfe**
([Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md#meine-entwürfe)).

## Vorschau

Drücken Sie **Vorschau** neben dem Bearbeitungsformular, um den Entwurf so zu sehen, wie er aussehen wird
([Das Bearbeitungsformular](../inhalte-bearbeiten.md#das-bearbeitungsformular)). Prüfen Sie, ob der Player erscheint, die
Links der Shownotes funktionieren und das Bild einen Alternativtext hat. Der Feed ist nicht Teil der Vorschau: Ein
Entwurf steht nie im Feed.

## Jetzt oder später veröffentlichen

Drücken Sie **Zur Veröffentlichung schicken**.

- Auf den meisten Websites wird die Episode sofort veröffentlicht.
- Wenn Ihre Website eine Freigabe verwendet, wird die Version **ausstehend**, bis der Freigebende sie annimmt
  ([Freigaben vor der Veröffentlichung](freigaben-vor-der-veroeffentlichung.md)).
- Wenn Ihre Website das Ereignis *Bis Datum warten* verwendet und das Veröffentlichungsdatum in der Zukunft liegt,
  wartet die Version bis zum ersten Lauf des Workflow-Cronjobs nach diesem Datum
  ([Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md)).

Ausstehende Versionen stehen in **Meine Warteliste**.

## Nach dem Veröffentlichen

Was als Nächstes geschieht, und wann:

| Wann | Was |
|---|---|
| Sofort | Die Episodenseite ist auf der Website; Listen und Menüs, die den Ordner zeigen, enthalten sie |
| Innerhalb der Cache-Zeit des Feeds (im Auslieferungszustand 1200 Sekunden) | Der Feed führt das neue Element oben auf (`settings/site.ini [RSSSettings] CacheTime`) |
| Nach dem eigenen Zeitplan jeder App | Podcast-Apps und Verzeichnisse rufen den Feed ab und zeigen die Episode |

Prüfen Sie abgemeldet:

- [ ] Die Episodenseite öffnet sich, und das Audio spielt;
- [ ] der Feed führt die Episode mit einem `enclosure` auf ([Ihren Feed prüfen](ihren-feed-pruefen.md));
- [ ] das Datum der Veröffentlichung des Objekts ist das erwartete: Es bestimmt die Reihenfolge im Feed.

Wenn Sie einen Fehler finden, **bearbeiten Sie die Episode**; entfernen Sie sie nicht, um sie neu anzulegen. Ein neues
Objekt erhält eine neue `guid`, und Apps würden die Episode zweimal zeigen
([Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md)).

## Verwandte Seiten

- [Checklisten für Podcaster](checklisten-fuer-podcaster.md)
- [Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md)
- [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
