---
title: Glossar für Podcaster
audience: podcaster
category: podcasting-streaming
order: 47
summary: Die Begriffe aus Podcasting und Streaming und die Begriffe von Exponential dahinter - von Enclosure, guid und RSS-Export bis zu Mediendatentyp, Siteaccess, Objektzustand und Workflow - jeweils kurz erklärt, mit der Seite, die weiterführt.
keywords: Glossar, Begriffe, Definitionen, Enclosure, guid, RSS-Export, Feed, Kanal, Element, Mediendatentyp, Klasse, Siteaccess, Sektion, Objektzustand, Workflow, Cronjob, CBR, Bitrate, glossary, terms, definitions, RSS export, channel, item, media datatype, class, section, object state
type: help
---
# Glossar für Podcaster

Begriffe aus Podcasting und Streaming und die Begriffe von Exponential, denen Sie beim Veröffentlichen einer Sendung
begegnen. Das allgemeine Glossar der Dokumentation ist das [Glossar](../../glossar.md).

## Inhalt

1. [Podcasting und Streaming](#podcasting-und-streaming)
2. [Begriffe von Exponential](#begriffe-von-exponential)

## Podcasting und Streaming

| Begriff | Bedeutung | Mehr |
|---|---|---|
| **Cover (Artwork)** | das Titelbild der Sendung; im Feed eine Adresse in `itunes:image href` | [Cover und Episodenbilder](cover-und-episodenbilder.md) |
| **Bitrate** | wie viele Bits pro Sekunde das Audio verwendet; zusammen mit der Länge bestimmt sie die Dateigröße | [Speicher und Bandbreite](speicher-und-bandbreite.md) |
| **CBR, VBR** | konstante und variable Bitrate; Exponential berechnet die Dauer nur für CBR-MP3 | [Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md) |
| **Kanal (Channel)** | der Teil eines Feeds auf Ebene der Sendung: Titel, Beschreibung, Cover, Eigentümer, Kategorie | [Referenz der Kanalfelder](referenz-der-kanalfelder.md) |
| **Untertitel (Captions)** | Text auf dem Bildschirm mit dem, was in einem Video gesagt wird | [Transkripte und Untertitel](transkripte-und-untertitel.md) |
| **Verzeichnis** | ein Katalog von Podcasts, den Apps durchsuchen, etwa Apple Podcasts | [Bei Podcast-Verzeichnissen einreichen](bei-podcast-verzeichnissen-einreichen.md) |
| **Enclosure** | das Element eines Feed-Elements, das auf die Episodendatei zeigt, mit ihrer Länge und ihrem Typ | [Referenz der Episodenzuordnung](referenz-der-episodenzuordnung.md) |
| **Episodisch, fortlaufend** | wie Apps eine Sendung darstellen: neueste zuerst oder der Reihe nach ab der ersten Episode (`itunes:type`) | [Referenz der Kanalfelder](referenz-der-kanalfelder.md) |
| **Feed** | das RSS-Dokument, das Apps abonnieren | [Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md) |
| **guid** | die dauerhafte Kennung eines Elements; Apps erkennen daran, ob eine Episode neu ist | [Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md) |
| **Element (Item)** | eine Episode in einem Feed | [Referenz der Episodenzuordnung](referenz-der-episodenzuordnung.md) |
| **MIME-Typ** | der Name des Formats einer Datei, etwa `audio/mpeg` | [Audio hochladen](audio-hochladen.md) |
| **OPML** | ein Dokument, das Feeds auflistet und dazu dient, eine Sammlung von Abonnements zu teilen | [Feeds für Leser und Netzwerke](feeds-fuer-leser-und-netzwerke.md) |
| **Range-Anfrage** | eine Anfrage nach einem Teil einer Datei, zum Springen und Fortsetzen | [Das Medienfeld und das Dateifeld](medienfeld-und-dateifeld.md) |
| **Shownotes** | der Text, der zu einer Episode gehört | [Shownotes schreiben](shownotes-schreiben.md) |
| **Transkript** | der vollständige Text dessen, was in einer Episode gesagt wird | [Transkripte und Untertitel](transkripte-und-untertitel.md) |

## Begriffe von Exponential

| Begriff | Bedeutung | Mehr |
|---|---|---|
| **Klasse** | eine Art von Inhaltsobjekt mit ihren Feldern, etwa Ordner, Video oder Ihre Episode | [Eine Klasse für Episoden wählen](eine-klasse-fuer-episoden-waehlen.md) |
| **Attribut, Feld** | ein Feld einer Klasse, mit einem Datentyp | [Eine Episodenklasse aufbauen](eine-episodenklasse-aufbauen.md) |
| **Datentyp** | die Art eines Felds: Textzeile, formatierter Text, Medien, Datei, Bild, Beziehung ... | [Datentypen wählen](../ersteller/website-ersteller/datentypen-waehlen.md) |
| **Mediendatentyp (`ezmedia`)** | ein Feld, das Audio oder Video speichert und einen Player darstellt | [Das Medienfeld und das Dateifeld](medienfeld-und-dateifeld.md) |
| **Dateidatentyp (`ezbinaryfile`)** | ein Feld, das jede Datei speichert und Downloads zählt | [Das Medienfeld und das Dateifeld](medienfeld-und-dateifeld.md) |
| **Inhaltsbaum, Ort, Knoten** | wo Objekte liegen; ein Objekt kann mehrere Orte haben | [Wie eine Sendung zu Inhalten wird](wie-eine-sendung-zu-inhalten-wird.md) |
| **Remote-ID** | die dauerhafte Kennung eines Objekts; der Podcast-Feed verwendet sie als `guid` | [Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md) |
| **Version, Entwurf, ausstehend** | jede Bearbeitung ist eine neue Version; ein Entwurf ist unveröffentlicht; ausstehend wartet auf einen Workflow | [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md) |
| **RSS-Export** | ein Feed, den die Website veröffentlicht, unter `/rss/feed/<access URL>` | [RSS-Feeds](../../anleitungen/rss-feeds.md) |
| **Quelle** | Ort, Klasse und Zuordnung, die ein Export liest | [Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md) |
| **Zugriffs-URL** | der letzte Teil der Adresse eines Feeds | [Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md) |
| **Siteaccess** | ein Zugang zur Website, mit eigenem Design, eigenen Sprachen und eigener Adresse | [Mehrsprachige Sendungen](mehrsprachige-sendungen.md) |
| **Sektion** | eine Gruppierung von Inhalten, die für Rechte verwendet wird | [Episoden nur für Mitglieder](episoden-nur-fuer-mitglieder.md) |
| **Rolle, Richtlinie** | was eine Benutzergruppe tun darf | [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md) |
| **Objektzustand** | eine Markierung pro Zustandsgruppe, in Richtlinien verwendbar | [Produktionsphasen mit Objektzuständen](produktionsphasen-mit-objektzustaenden.md) |
| **Workflow, Trigger, Ereignis** | Aktionen rund um das Veröffentlichen, etwa Freigabe oder das Warten auf ein Datum | [Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md) |
| **Cronjob, Cronjob-Teil** | zeitgesteuerte Skripte; `workflow.php` im Teil `frequent` setzt wartende Workflows fort | [Cronjobs](../../anleitungen/cronjobs.md) |
| **Layout-Editor, Block** | der Seitenbaukasten von Exponential Layouts und seine Bausteine | [Der Layout-Editor](../layout-editor.md) |
| **Override** | eine Einstellungsdatei, die eine mitgelieferte Einstellung ändert | [Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md) |

## Verwandte Seiten

- [Glossar](../../glossar.md)
- [Glossar für Journalisten](../presse/glossar-fuer-journalisten.md)
- [FAQ für Podcaster](faq-fuer-podcaster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
