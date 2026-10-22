---
title: Mehrsprachige Sendungen
audience: podcaster
category: podcasting-streaming
order: 38
summary: Wie Sie eine Sendung auf einer Exponential-Website in mehreren Sprachen betreiben - übersetzte Episoden oder eigene Sendungen pro Sprache, übersetzbare Medienfelder, ein Podcast-Feed pro Sprache mit seinem Feld Sprache und seinem Siteaccess, und die Prüfungen, ob jeder Feed die richtige Sprache trägt.
keywords: mehrsprachig, Sprachen, Übersetzung, übersetzte Episoden, Synchronisation, Sprachversion, Siteaccess, Feed-Sprache, Feld Sprache, übersetzbares Attribut, Sprachumschalter, multilingual, languages, translation, translated episodes, dubbing, language version, feed language, Language field, translatable attribute, language switcher
type: help
---
# Mehrsprachige Sendungen

Diese Seite ist für Produzenten, die eine Sendung in mehr als einer Sprache veröffentlichen, oder mehrere Sendungen für
verschiedene Sprachgruppen auf einer Website. Exponential speichert Übersetzungen jedes Objekts und liefert jede
Sprache über ihren eigenen Siteaccess aus ([Inhaltssprachen](../../anleitungen/inhaltssprachen.md)). Wie sich das auf eine
Sendung abbildet, hängt davon ab, ob die Episoden in jeder Sprache wirklich dieselben sind.

## Inhalt

1. [Zwei Modelle](#zwei-modelle)
2. [Übersetzte Episoden](#übersetzte-episoden)
3. [Eigene Sendungen pro Sprache](#eigene-sendungen-pro-sprache)
4. [Ein Feed pro Sprache](#ein-feed-pro-sprache)
5. [Prüfungen](#prüfungen)

## Zwei Modelle

| Modell | Wann | In Exponential |
|---|---|---|
| **Übersetzte Episoden** | dieselbe Episode gibt es in mehreren Sprachen (synchronisiert oder nach einem Skript neu aufgenommen) | ein Objekt pro Episode, mit einer Übersetzung pro Sprache |
| **Eigene Sendungen** | jede Sprache hat eigene Inhalte und einen eigenen Rhythmus | ein Ordner pro Sprache, jeder mit eigenen Episoden |

Mischen ist möglich, aber entscheiden Sie pro Sendung; ein Feed listet Objekte auf, und jedes Modell ergibt einen
sauberen Feed.

## Übersetzte Episoden

1. Die Website hat die Sprachen und für jede einen Siteaccess
   ([Eine mehrsprachige Website aufbauen](../ersteller/website-ersteller/mehrsprachige-websites.md)).
2. In der Episodenklasse ist das Medienfeld **übersetzbar**, sodass jede Übersetzung ihre eigene Audiodatei trägt
   ([Eine Episodenklasse aufbauen](eine-episodenklasse-aufbauen.md#entscheidungen-die-später-zählen)). Titel, Notizen und
   Transkript sind ebenfalls übersetzbar.
3. Veröffentlichen Sie die Episode in ihrer Hauptsprache und übersetzen Sie sie dann: Wählen Sie die Sprache neben
   **Bearbeiten**, laden Sie das Audio dieser Sprache hoch und übersetzen Sie die Notizen
   ([Sprachen](../sprachen.md#ein-objekt-übersetzen)).

Die Übersetzungen eines Objekts teilen seine Remote-ID, deshalb hat die Episode im Feed jeder Sprache dieselbe `guid`.
Apps behandeln Feeds als getrennte Sendungen, daher schadet das nicht, aber denken Sie daran, falls Sie Feeds später
zusammenführen.

Wenn das Audio in jeder Sprache gleich ist (Musik, eine sprachneutrale Aufnahme), machen Sie das Medienfeld nicht
übersetzbar und übersetzen Sie nur den Text.

## Eigene Sendungen pro Sprache

Legen Sie pro Sprache einen Ordner für die Sendung an, jeder mit eigenen Episoden in dieser Sprache, und behandeln Sie
jeden als eigene Sendung: eigener Feed, eigenes Cover, eigene Beschreibung und eigener Eintrag in den Verzeichnissen.
Das ist einfacher, wenn sich die Inhalte unterscheiden, und jeder Feed enthält nur eine Sprache.

## Ein Feed pro Sprache

Legen Sie pro Sprache einen Podcast-Export an:

| Feld | Einstellung |
|---|---|
| **Name**, **Beschreibung**, **Zusammenfassung** | in dieser Sprache |
| **Sprache** (Abschnitt Apple Podcasts) | der Code dieser Sprache, zum Beispiel `de` oder `fr-ca`; leer verwendet die Sprache des Siteaccess |
| **URL der Website** | die Adresse der Website dieser Sprache, damit Links und Enclosures auf sie zeigen (**Links zeigen auf** auf der RSS-Karte zeigt, welchen Siteaccess eine leere URL der Website verwendet) |
| **URL des erzeugten RSS-Feeds** (Zugriffs-URL) | pro Sprache verschieden, zum Beispiel `example-show-de` |
| Quelle | der Ordner der Sendung (übersetztes Modell) oder der Ordner der Sendung in dieser Sprache (getrenntes Modell) |

Welche Übersetzung der Feed zeigt, hängt vom Siteaccess ab, der ihn ausliefert: Jeder Siteaccess hat seine eigene
Sprachliste (`SiteLanguageList`), und der Feed-Cache wird pro Siteaccess und Feed geführt
(`kernel/private/classes/views/rss/feed.php`). Öffnen Sie jeden Feed über den Host seiner Sprache.

Noch nicht übersetzte Episoden können in einer anderen Sprache gezeigt werden, wo die Fallback-Sprachen des Siteaccess
das zulassen ([Fallbacks und nicht übersetzte Inhalte](../ersteller/website-ersteller/mehrsprachige-websites.md#fallbacks-und-nicht-übersetzte-inhalte)).
Veröffentlichen Sie bei einem Feed die Übersetzung, bevor Sie sich darauf verlassen.

## Prüfungen

- [ ] Das Element `language` jedes Feeds nennt die richtige Sprache.
- [ ] Titel und Beschreibung jedes Elements sind in dieser Sprache.
- [ ] Jedes Enclosure ist die Audiodatei dieser Sprache.
- [ ] Die Links in jedem Feed zeigen auf die Website dieser Sprache.
- [ ] Die Episodenseite bietet einen Sprachumschalter zu den anderen Versionen
      ([Sprachumschalter und Sprach-URLs](../ersteller/website-ersteller/sprachumschalter-und-sprach-urls.md)).

## Verwandte Seiten

- [Inhaltssprachen](../../anleitungen/inhaltssprachen.md)
- [Routine für Übersetzungen](../kunden/alltag/uebersetzungsroutine.md)
- [Eine mehrsprachige Website aufbauen](../ersteller/website-ersteller/mehrsprachige-websites.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
