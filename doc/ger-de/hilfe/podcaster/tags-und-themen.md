---
title: Tags und Themen
audience: podcaster
category: podcasting-streaming
order: 37
summary: Wie Sie Episoden auf einer Exponential-Website nach Themen ordnen - der Datentyp Schlüsselwörter des Kernels, eztags auf Installationen mit dieser Erweiterung, Themenseiten und Listen und wie ein Schlüsselwortfeld die Kategorie jedes Elements im Podcast-Feed füllen kann.
keywords: Tags, Themen, Schlüsselwörter, Schlagwörter, ezkeyword, eztags, Kategorien, Taxonomie, Themenseiten, Hauptthema, Feed-Kategorie, nach Themen stöbern, Suche, tags, topics, keywords, categories, taxonomy, topic pages, main topic, feed category, browse by topic, search
type: help
---
# Tags und Themen

Diese Seite ist für Produzenten mit einem wachsenden Archiv, die möchten, dass Hörer Episoden nach Thema, Art des
Gastes oder Leitmotiv finden. Exponential bietet zwei Werkzeuge: den Datentyp **Schlüsselwörter** im Kernel und
**eztags**, eine Erweiterung mit einem gepflegten Tag-Baum, auf Installationen, die sie haben.

## Inhalt

1. [Schlüsselwörter oder Tags](#schlüsselwörter-oder-tags)
2. [Schlüsselwörter](#schlüsselwörter)
3. [eztags](#eztags)
4. [Themen im Feed](#themen-im-feed)
5. [Gutes Verschlagworten für eine Sendung](#gutes-verschlagworten-für-eine-sendung)

## Schlüsselwörter oder Tags

| | Schlüsselwörter (`ezkeyword`) | eztags |
|---|---|---|
| Wo | Kernel, `kernel/classes/datatypes/ezkeyword/` | die Erweiterung eztags, wo installiert |
| Eingabe | freier Text, durch Kommas getrennt | aus einem Tag-Baum gewählt oder ihm hinzugefügt |
| Kontrolle der Schreibweise | keine: "E-Mail" und "Email" sind zwei Schlüsselwörter | ein Tag, überall verwendet |
| Übersetzungen des Tags | nein | ja, Tags haben Übersetzungen |
| Verwendet von | der Klasse *Video* (**Tags**) | der Klasse *NG Video* (**Main topic**, **Tags**) auf Installationen mit Media Site |

Bei mehr als einer Handvoll Themen hält eztags den Katalog einheitlich; siehe
[Inhalte mit eztags verschlagworten](../ersteller/website-ersteller/verschlagworten-mit-eztags.md).

## Schlüsselwörter

Fügen Sie der Episodenklasse ein Feld **Schlüsselwörter** hinzu ([Eine Episodenklasse aufbauen](eine-episodenklasse-aufbauen.md))
und tippen Sie Themen durch Kommas getrennt ein. Führen Sie eine gemeinsame Liste der Schreibweisen, die Ihr Team
verwendet. Ein Design kann Episoden mit demselben Schlüsselwort auflisten; die Suche der Website findet Episoden über
ihre Schlüsselwörter, wenn das Feld durchsuchbar ist.

## eztags

Auf Installationen mit eztags:

1. Ein Administrator legt einen Tag-Baum für die Sendung an, zum Beispiel *Themen*, *Formate* (Interview, Diskussion,
   Solo), *Fachgebiete der Gäste* ([Einrichten](../ersteller/website-ersteller/verschlagworten-mit-eztags.md#einrichten)).
2. Die Episodenklasse erhält ein eztags-Feld, das auf diesen Zweig beschränkt ist.
3. Redakteure wählen beim Bearbeiten Tags aus; neue Tags können per Berechtigung erlaubt werden oder nicht
   ([Rechte](../ersteller/website-ersteller/verschlagworten-mit-eztags.md#rechte)).
4. Das Design zeigt Tag-Seiten, die jede Episode mit einem Tag auflisten
   ([Tags auf einer Website anzeigen](../ersteller/website-ersteller/verschlagworten-mit-eztags.md#tags-auf-einer-website-anzeigen)).

Mit Exponential Layouts können Listen auf Basis von Tags einen Block einer Seite füllen, sodass die Seite einer Sendung
"Mehr zu diesem Thema" tragen kann (`extension/explayouts_tags_query` auf Installationen, die sie haben).

## Themen im Feed

Ein Element des Podcast-Feeds kann ein `category`-Element tragen. Es stammt aus dem Feld, das für die Quelle als
**Kategorie** zugeordnet ist ([Referenz der Episodenzuordnung](referenz-der-episodenzuordnung.md)). Ein Feld
**Schlüsselwörter** funktioniert direkt: Der Feed schreibt die Schlüsselwörter als eine Zeichenkette
(`podcastText()` in `kernel/classes/ezrssexport.php` behandelt `eZKeyword`). Ein eztags-Feld gehört nicht zu den
Typen, die `podcastText()` behandelt, und schreibt dort deshalb nichts; ordnen Sie stattdessen ein Schlüsselwort- oder
Textfeld zu.

Diese Kategorie des Elements ist von der **Kategorie** der Sendung im Abschnitt Apple Podcasts getrennt, die eine der
Kategorien von Apple sein muss ([Referenz der Kanalfelder](referenz-der-kanalfelder.md)).

## Gutes Verschlagworten für eine Sendung

- Einigen Sie sich zuerst auf eine kleine Menge von Themen; fügen Sie neue mit Absicht hinzu, nicht beim Tippen.
- Verschlagworten Sie neben dem Gegenstand auch das Format (Interview, Diskussion, live), wenn Hörer danach fragen.
- Verschlagworten Sie die Namen von Gästen nicht als Themen, wenn sie Personenseiten haben; verknüpfen Sie stattdessen
  die Person ([Gäste und Mitwirkende](gaeste-und-mitwirkende.md)).
- Überprüfen Sie die Tags einmal pro Staffel: Doppelte zusammenführen, Tags mit nur einer Episode entfernen.

## Verwandte Seiten

- [Inhalte mit eztags verschlagworten](../ersteller/website-ersteller/verschlagworten-mit-eztags.md)
- [Suchen](../suchen.md)
- [Wie eine Sendung zu Inhalten wird](wie-eine-sendung-zu-inhalten-wird.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
