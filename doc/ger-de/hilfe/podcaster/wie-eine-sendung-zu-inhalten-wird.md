---
title: Wie eine Sendung zu Inhalten wird
audience: podcaster
category: podcasting-streaming
order: 4
summary: Wie ein Podcast, eine Radiosendung oder eine Videoreihe in Exponential zu Inhalten wird - die Sendung als Ordner, Staffeln als Unterordner, Episoden als Inhalte mit einem Medienfeld - und wie diese Struktur entscheidet, was der Podcast-Feed auflistet, in welcher Reihenfolge und wie viel.
keywords: Inhaltsstruktur, Inhaltsbaum, Sendung, Staffel, Reihe, Episode, Ordner, Platzierung, Feed-Quelle, Unterknoten, Anzahl der Objekte, Reihenfolge, neueste zuerst, content structure, content tree, show, season, series, episode, folder, location, feed source, subnodes, number of objects, order, newest first
type: help
---
# Wie eine Sendung zu Inhalten wird

Diese Seite ist für Podcaster und Produzenten, die gerade eine Sendung einrichten und die Struktur von Anfang an
richtig anlegen möchten. In Exponential ist alles **Inhalt**: Objekte einer **Klasse** (Ordner, Artikel, Video ...),
die an **Platzierungen** in einem Baum liegen. Eine Sendung ist keine Ausnahme. Die Struktur, die Sie wählen,
entscheidet, was die Website zeigt und was der Podcast-Feed auflistet.

## Inhalt

1. [Die Bausteine](#die-bausteine)
2. [Eine Struktur, die funktioniert](#eine-struktur-die-funktioniert)
3. [Was der Feed aus der Struktur übernimmt](#was-der-feed-aus-der-struktur-übernimmt)
4. [Mehrere Sendungen auf einer Website](#mehrere-sendungen-auf-einer-website)
5. [Was Sie früh entscheiden sollten](#was-sie-früh-entscheiden-sollten)

## Die Bausteine

| Begriff der Sendung | Begriff in Exponential | Hinweise |
|---|---|---|
| Die Sendung oder das Programm | Ein **Ordner** (oder eine Container-Klasse Ihrer Website) | Enthält die Beschreibung und das Cover, die auf der Website erscheinen |
| Eine Staffel oder Reihe | Ein **Unterordner** der Sendung | Optional; nützlich für lang laufende Sendungen |
| Eine Episode | Ein **Objekt** einer Episodenklasse mit einem Medien- oder Dateifeld | Ein Objekt pro Episode; seine Versionen bewahren die Geschichte |
| Shownotes | Ein Feld mit formatiertem Text in der Episode | Bei entsprechender Zuordnung auch die Beschreibung im Feed |
| Ein Gast | Ein Objekt einer Personenklasse, mit der Episode verknüpft | Siehe [Gäste und Mitwirkende](gaeste-und-mitwirkende.md) |
| Der Feed | Ein **RSS-Export**, der den Ordner der Sendung als Quelle nennt | Eingerichtet unter **Setup > RSS** |

Die Klassen beschreibt [Eine Klasse für Episoden wählen](eine-klasse-fuer-episoden-waehlen.md). Ein Objekt kann mehr als
eine Platzierung haben, zum Beispiel in der Sendung und in einem Ordner "Best of"; die Exportoption **Nur
Hauptknoten** verhindert, dass eine solche Episode zweimal im Feed erscheint.

## Eine Struktur, die funktioniert

```text
Podcasts                      (folder)
  The Example Show            (folder: description, artwork)
    Season 1                  (folder)
      Episode 1: Pilot        (episode item: audio, show notes)
      Episode 2: ...
    Season 2                  (folder)
      Episode 9: ...
  Guests                      (folder of person items)
```

Die Namen sind Beispiele. Legen Sie Gäste in einem eigenen Ordner ab, damit ein Gast mit mehreren Episoden verknüpft
werden kann. [Die Sendungsstruktur einrichten](die-sendungsstruktur-einrichten.md) baut dies Schritt für Schritt auf.

## Was der Feed aus der Struktur übernimmt

Ein Export listet die Objekte unterhalb jeder **Quelle** auf. Eine Quelle ist eine Platzierung, eine Klasse und das
Kontrollkästchen **Unterknoten** (`eZRSSExportItem::fetchNodeList()` in `kernel/classes/ezrssexportitem.php`):

| Einstellung der Quelle | Was aufgelistet wird |
|---|---|
| Unterknoten aus | Nur die direkten Kinder der Platzierung (Tiefe 1) der gewählten Klasse |
| Unterknoten an | Jedes Objekt der gewählten Klasse irgendwo unterhalb der Platzierung |

Eine Sendung mit Staffelordnern braucht also **Unterknoten** an, mit dem Ordner der Sendung als Quelle. Eine Sendung
ohne Staffeln funktioniert mit beiden Einstellungen.

Zwei weitere Regeln ergeben sich aus derselben Funktion:

- **Reihenfolge**: Die Objekte werden nach ihrem Datum der **Veröffentlichung** sortiert, neueste zuerst. Die **Art
  der Sendung** eines Podcast-Exports (*Episodisch - neueste zuerst* oder *Fortlaufend - älteste zuerst*) schreibt
  `itunes:type` für die Apps; sie ordnet den Feed selbst nicht um.
- **Wie viele**: Die **Anzahl der Objekte** des Exports begrenzt die Liste. Die Auswahl stammt aus
  `settings/site.ini [RSSSettings] NumberOfObjectsList[]` (5 bis 1000, Standard 20). Ältere Episoden jenseits der
  Grenze fallen aus dem Feed; auf der Website bleiben sie.

Wählen Sie eine Zahl, die groß genug für Ihr Archiv ist: Eine Podcast-App, die heute abonniert, sieht nur, was der
Feed auflistet.

## Mehrere Sendungen auf einer Website

Geben Sie jeder Sendung einen eigenen Ordner und einen eigenen Export. Jeder Export hat seine eigene Adresse
(`/rss/feed/<access URL>`), seine eigenen Kanalfelder und sein eigenes Cover. Ein Netzwerk von Sendungen kann dann
eine OPML-Liste aller Feeds veröffentlichen ([Feeds für Leser und Netzwerke](feeds-fuer-leser-und-netzwerke.md)).
Für eine Sendung pro Sprache siehe [Mehrsprachige Sendungen](mehrsprachige-sendungen.md).

## Was Sie früh entscheiden sollten

- **Die Episodenklasse.** Sie später zu ändern bedeutet, Inhalte zwischen Klassen zu verschieben.
- **Wo die Dateien liegen.** Neben der Episode oder in der Medienbibliothek; beides funktioniert
  ([Bilder, Medien und Uploads](../medien-und-uploads.md)).
- **Staffeln oder nicht.** Staffelordner später hinzuzufügen bedeutet, Episoden zu verschieben; die Einstellung
  **Unterknoten** des Feeds muss dazu passen.
- **Das Datum der Veröffentlichung.** Es bestimmt die Reihenfolge im Feed. Die Zeitplanung mit dem Ereignis *Bis
  Datum warten* kann es auf den Veröffentlichungstermin setzen ([Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md)).
- **Legen Sie eine Episode nie neu an, um sie zu korrigieren.** Die `guid` des Feeds ist die Remote-ID des Objekts;
  ein neues Objekt ist für jede App eine neue Episode ([Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md)).

## Verwandte Seiten

- [Die Sendungsstruktur einrichten](die-sendungsstruktur-einrichten.md)
- [Den Inhaltsbaum strukturieren](../ersteller/website-ersteller/den-inhaltsbaum-strukturieren.md)
- [RSS-Feeds](../../anleitungen/rss-feeds.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
