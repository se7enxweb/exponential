---
title: "Lektion 6.2: RSS-Feeds aus Sicht der Redakteure"
audience: trainer, editor
category: training
order: 62
summary: Die Teilnehmenden erklären, wie veröffentlichte Inhalte zu Feed-Einträgen werden, finden und öffnen einen Feed der Website, prüfen ihren eigenen Artikel darin und lesen auf der Seite der RSS-Feeds die Karte eines Exports, wo ihre Rolle es erlaubt. 30 Minuten.
keywords: redakteure 201, rss, atom, feed, rss-export, feed-adresse, feed öffnen, quellen, podcast, rss-import, newsreader, editors 201, rss export, feed address, open feed, sources, rss import
type: help
---
# Lektion 6.2: RSS-Feeds aus Sicht der Redakteure

Mit den Feeds einer Website können Leser und andere Websites neuen Inhalten folgen. Redakteure richten Feeds
normalerweise nicht ein, aber was sie veröffentlichen, landet darin: der Titel, die Beschreibung, das Bild. Diese
Lektion zeigt Redakteuren, wie ihre Inhalte in einen Feed gelangen und wie sie das prüfen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Redakteure 201](../README.md) |
| Modul | [Modul 6: Adressen, Feeds und PDF](README.md) |
| Dauer | 30 Minuten (15 Präsentation, 15 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure von Nachrichten, Blogs und Podcasts |
| Voraussetzungen | [Lektion 6.1](url-aliase-eines-elements.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, was ein RSS-Export ist: ein Feed der neuesten Elemente unter seinen Quellen;
- nennen, welche Felder ihrer Inhalte zum Titel und zur Beschreibung eines Feed-Eintrags werden;
- die Adresse eines Feeds öffnen und ihren eigenen Artikel darin finden;
- die Karte eines Exports lesen (**Feed öffnen**, Quellen, Einträge, zuletzt geschrieben), wo ihre Rolle es erlaubt.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [RSS-Feeds](../../../../anleitungen/rss-feeds.md).

1. **Exporte** (4 Minuten). Ein Export ist ein Feed, den diese Website unter `/rss/feed/<address>` veröffentlicht. Er
   listet die neuesten Elemente unter seinen **Quellen** auf (Stellen im Baum, jeweils mit einer Klasse und optional
   ihren Unterelementen), als RSS 1.0, RSS 2.0, Atom, OPML oder als Apple-Podcasts-Feed. Ein **Import** ist der
   umgekehrte Weg: Er liest den Feed einer anderen Website und legt Elemente an (ein Cronjob übernimmt das Lesen).
2. **Was Redakteure steuern** (4 Minuten). Für jede Quelle legt der Export fest, welches Feld zum Titel, zur
   Beschreibung, zur Kategorie und zum Anhang des Eintrags wird (zum Beispiel **Title** und **Intro** des Artikels).
   Gute Titel und Einleitungen ergeben gute Feed-Einträge. Ein neuer Artikel erscheint im Feed, wenn er unter einer
   Quelle veröffentlicht wird, innerhalb der Cache-Zeit des Feeds.
3. **Die Seite** (5 Minuten). Administratoren (Rolle mit `rss/edit`) öffnen **Setup** > **RSS**: *RSS-Feeds* mit einer
   **Übersicht**, **Feed finden**, **Anzeigen** (**Alle**, **Aktiv**, **Inaktiv**, **Brauchen Aufmerksamkeit**), den
   **RSS Exporte** und **RSS Importe** mit **Feed öffnen**, **Bearbeiten**, **Neuer Export**, **Neuer Import** und
   **Ausgewählte entfernen**. Der Editor-Siteaccess bietet das RSS-Modul nicht an. Zeigen Sie die Seite am Beamer, wenn
   die Teilnehmenden sie nicht öffnen dürfen.

### Häufige Fragen

**Warum ist mein neuer Artikel noch nicht im Feed?** Der Feed wird für eine von der Website festgelegte Zeit
zwischengespeichert; oder der Artikel liegt nicht unter einer der Quellen des Feeds oder hat eine andere Klasse.

**Kann ich einen Feed für meinen Ordner anlegen?** Administratoren können das; das Menü des Elements hat **Neuen
RSS/ATOM Feed erstellen** für Rollen, die es dürfen.

**Entfernt das Entfernen eines Exports Inhalte?** Nein. Seine Adresse antwortet nicht mehr; die Inhalte bleiben.

### Stolperfallen

- **Rohes XML im Browser.** Manche Browser zeigen einen Feed als Code. Das ist in Ordnung; die Teilnehmenden suchen
  darin ihren Titel.
- **Fehlende Berechtigung.** Die meisten Redakteure können **Setup** > **RSS** nicht öffnen; planen Sie die Vorführung.
- **Leere Einleitungen.** Ein Feed-Eintrag ohne Beschreibung sieht in Newsreadern kaputt aus; erinnern Sie die
  Teilnehmenden daran, Einleitungen zu schreiben.

## Praktische Übung

Verwenden Sie die Schulungsinstallation. Der Trainer hat einen RSS-2.0-Export angelegt, dessen Quelle der Ordner
**Training** mit Unterelementen ist, Klasse *Article*, Titel aus **Title** und Beschreibung aus **Intro**.

1. Der Trainer öffnet **Setup** > **RSS** am Beamer und zeigt die Karte des Exports: seine Adresse, Quellen und **Feed
   öffnen**. Notieren Sie die Adresse des Feeds.
2. Öffnen Sie die Adresse des Feeds in Ihrem Browser.
3. Suchen Sie die Titel der Artikel im Feed. Ist einer Ihrer Artikel dabei?
4. Veröffentlichen Sie in Ihrem Kursordner (unter **Training**) einen neuen Artikel `Feed test` mit einer Einleitung
   aus einem Satz.
5. Öffnen Sie den Feed erneut (warten Sie gegebenenfalls die Cache-Zeit ab, die der Trainer nennt). Suchen Sie
   `Feed test` und seine Beschreibung.
6. Wenn Sie eine Newsreader-App haben, abonnieren Sie die Adresse des Feeds und sehen Sie sich Ihren Artikel dort an.
7. Verbergen Sie `Feed test` und prüfen Sie den Feed nach der Cache-Zeit erneut.

## Erwartetes Ergebnis

- Der Feed listet die neuesten Artikel unter **Training** mit Titeln und Einleitungen als Beschreibungen auf.
- `Feed test` ist nach dem Veröffentlichen erschienen (innerhalb der Cache-Zeit) und nach dem Verbergen aus dem Feed
  verschwunden (verborgene Inhalte werden Besuchern nicht gezeigt).
- Der Trainer prüft das, indem er den Feed am Beamer öffnet und den Artikel einer Person findet.

![RSS: die Feeds, die die Website exportiert, und die Importe](../../../../../eng-us/help/images/trainers/e201-rss-list.webp)

## Quiz

1. Was ist ein RSS-Export?
2. Welche zwei Felder eines Artikels werden typischerweise zum Titel und zur Beschreibung eines Feed-Eintrags?
3. Ihr neuer Artikel ist nicht im Feed. Nennen Sie zwei mögliche Gründe.
4. Richtig oder falsch: Das Entfernen eines Exports löscht die Artikel, die er aufgelistet hat.
5. Welche Seite listet die Feeds der Website auf, und wer darf sie standardmäßig öffnen?

<details>
<summary>Antworten</summary>

1. Ein Feed, den diese Website veröffentlicht und der die neuesten Elemente unter seinen Quellen auflistet
   ([RSS-Feeds](../../../../anleitungen/rss-feeds.md)).
2. **Title** und **Intro** (so, wie der Export sie zuordnet).
3. Zwei beliebige aus: die Cache-Zeit des Feeds; der Artikel liegt nicht unter einer Quelle; seine Klasse ist nicht die
   Klasse der Quelle; er ist verborgen oder nicht veröffentlicht.
4. **Falsch.**
5. **Setup** > **RSS**, für Rollen mit der Richtlinie `rss/edit` (Administratoren).

</details>

## Weiterführende Informationen

- [RSS-Feeds](../../../../anleitungen/rss-feeds.md): Exporte, Importe und die Seite.
- [RSS, Podcasts und die Feed-Liste](../../../../funktionen/6.0/rss-podcast-und-feed-liste.md): die Formate.

## Verwandte Seiten

- [Modul 6: Adressen, Feeds und PDF](README.md)
- [Redakteure 201: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
