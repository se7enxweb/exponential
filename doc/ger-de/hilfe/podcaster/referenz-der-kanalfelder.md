---
title: Referenz der Kanalfelder
audience: podcaster
category: podcasting-streaming
order: 18
summary: Jedes Feld des Abschnitts Apple Podcasts eines RSS-Exports - was Sie eintragen, zu welchem Element es wird, wie Exponential den Wert beim Speichern des Exports prüft, und die Liste der Kategorien, die es akzeptiert.
keywords: Kanalfelder, Apple Podcasts, itunes:author, itunes:owner, itunes:image, itunes:category, itunes:explicit, itunes:type, itunes:block, itunes:complete, itunes:new-feed-url, podcast_head, Kategorien, Referenz, channel fields, categories, reference
type: help
---
# Referenz der Kanalfelder

Diese Seite ist für Produzenten und Administratoren, die den Abschnitt **Apple Podcasts** eines Podcast-Exports
ausfüllen. Die Felder werden als JSON in der Spalte `ezrss_export.podcast_head` gespeichert und von
`eZRSSExport::podcastHead()` gelesen; ihre Werte prüft `setPodcastHead()` beim Speichern des Exports
(`kernel/classes/ezrssexport.php`).

## Inhalt

1. [Die Felder](#die-felder)
2. [Wie Werte geprüft werden](#wie-werte-geprüft-werden)
3. [Kategorien](#kategorien)
4. [Felder aus dem übrigen Formular](#felder-aus-dem-übrigen-formular)

## Die Felder

| Feld im Formular | Element im Feed | Für Apple erforderlich (laut Funktionsbeschreibung) | Eintrag |
|---|---|---|---|
| **Autor** | `itunes:author` | nein | wer die Sendung macht, wie Hörer es sehen sollen |
| **Name des Eigentümers** | `itunes:owner/itunes:name` | ja | die Person oder Organisation, die für den Feed verantwortlich ist |
| **E-Mail des Eigentümers** | `itunes:owner/itunes:email` | ja | eine Adresse, an die das Verzeichnis schreiben darf; sie ist im Feed öffentlich |
| **Adresse des Titelbilds** | `itunes:image href` | ja | die vollständige Adresse des Cover-Bilds ([Cover und Episodenbilder](cover-und-episodenbilder.md)) |
| **Kategorie** | `itunes:category text` | ja | aus einer Liste gewählt (unten) |
| **Unterkategorie** | verschachteltes `itunes:category` | nein | eine der Unterkategorien der gewählten Kategorie |
| **Art der Sendung** | `itunes:type` | nein | *Episodisch - neueste zuerst* (Standard) oder *Fortlaufend - älteste zuerst* |
| **Sprache** | `language` | nein | ein Code aus zwei Buchstaben wie `en` oder ein regionaler wie `en-us`; leer verwendet die Sprache des Siteaccess |
| **Copyright** | `copyright` | nein | zum Beispiel "Copyright 2026 Example Media" |
| **Untertitel** | `itunes:subtitle` | nein | eine Zeile |
| **Zusammenfassung** | `itunes:summary` | nein | eine längere Beschreibung |
| **Enthält explizite Inhalte** | `itunes:explicit` | nein | ankreuzen, wenn eine Episode es braucht; gilt für jede Episode |
| **Die Sendung ist abgeschlossen - keine weiteren Folgen** | `itunes:complete` | nein | nur wenn die Sendung beendet ist |
| **Die Sendung aus dem Apple-Podcasts-Verzeichnis heraushalten** | `itunes:block` | nein | nur, um die Sendung zurückzuziehen |
| **Umgezogen nach** | `itunes:new-feed-url` | nein | nur wenn der Feed an eine neue Adresse umzieht |

Die E-Mail-Adresse des Eigentümers erscheint im öffentlichen Feed. Verwenden Sie eine Funktionsadresse der Sendung
(zum Beispiel `podcast@example.com`), keine persönliche.

## Wie Werte geprüft werden

| Feld | Regel (`setPodcastHead()`) |
|---|---|
| Adresse des Titelbilds, Umgezogen nach | `safeURL()`: Leerraum entfernt, nur `http` und `https` (oder eine relative Adresse), höchstens 255 Zeichen |
| Enthält explizite Inhalte | gespeichert als `true` oder `false`; `true`, `yes` und `1` gelten als wahr; leer ist `false` |
| Die Sendung ist abgeschlossen, Aus dem Verzeichnis heraushalten | `true`, `false` oder leer; nur bei `true` geschrieben, als `Yes` |
| Art der Sendung | `serial` bleibt `serial`; alles andere wird zu `episodic` |
| Kategorie | nur behalten, wenn Apple sie aufführt, exakt so geschrieben; sonst verworfen |
| Unterkategorie | nur behalten, wenn sie zur gewählten Kategorie gehört |
| Alle anderen Felder | `safeText()`: Steuerzeichen entfernt, getrimmt, höchstens 1024 Zeichen |

Ein verworfener Wert erzeugt keine Fehlermeldung; das Feld ist einfach leer, wenn Sie den Export wieder öffnen. Prüfen
Sie das Formular nach dem Speichern.

**Art der Sendung** sagt Apps nur, wie sie die Sendung darstellen sollen. Der Feed selbst listet Elemente immer
neueste zuerst auf ([Wie eine Sendung zu Inhalten wird](wie-eine-sendung-zu-inhalten-wird.md)).

## Kategorien

Die akzeptierte Liste ist `eZRSSExport::podcastCategories()`, im Code beschrieben als "the list Apple published with
the 2019 revision", also die Liste, die Apple mit der Überarbeitung von 2019 veröffentlicht hat. Kategorien der
obersten Ebene:

Arts, Business, Comedy, Education, Fiction, Government, History, Health & Fitness, Kids & Family, Leisure, Music,
News, Religion & Spirituality, Science, Society & Culture, Sports, Technology, True Crime, TV & Film.

Beispiele für Unterkategorien: *News* hat Business News, Daily News, Entertainment News, News Commentary, Politics,
Sports News und Tech News; *Society & Culture* hat Documentary, Personal Journals, Philosophy, Places & Travel und
Relationships; *Technology*, *History*, *Government* und *True Crime* haben keine. Die vollständige Liste steht im
Code. Wenn Apple seine Liste überarbeitet, ist diese Funktion laut Code "the one place to change", also die einzige
Stelle, die geändert werden muss.

## Felder aus dem übrigen Formular

| Feld | Element |
|---|---|
| **Name** | `title`, `itunes:title` |
| **Beschreibung** | `description` |
| **URL des erzeugten RSS-Feeds** (Zugriffs-URL) | die eigene Adresse des Feeds, in `atom:link rel="self"` |
| **URL der Website** | die Basis von `link` und jeder Episodenadresse |

## Verwandte Seiten

- [Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md)
- [Referenz der Episodenzuordnung](referenz-der-episodenzuordnung.md)
- [Eine Sendung umziehen, pausieren oder beenden](eine-sendung-umziehen-pausieren-oder-beenden.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
