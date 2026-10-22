---
title: Feeds für Leser und Netzwerke
audience: podcaster
category: podcasting-streaming
order: 23
summary: Die Feeds, die eine Sendung oder ein Podcast-Netzwerk neben dem Podcast-Feed veröffentlichen kann - RSS 2.0 oder Atom mit den Episodenseiten für Newsreader, eine OPML-Liste aller Sendungen eines Netzwerks - und der Import des Feeds eines Partners auf die Website, mit dem, was jedes Format trägt und was nicht.
keywords: RSS 2.0, Atom, RSS 1.0, OPML, Feed-Liste, Netzwerk, Newsreader, Syndikation, Import, rssimport, Feed eines Partners, Feed der Shownotes, OPMLMaxOutlines, feed list, network, news reader, syndication, import, partner feed, show notes feed
type: help
---
# Feeds für Leser und Netzwerke

Diese Seite ist für Produzenten und Redakteure von Netzwerken, die mehr als einen Podcast-Feed möchten: einen Feed der
neuen Episodenseiten für Newsreader, eine Liste aller Sendungen für ein Netzwerk oder die Episoden eines Partners auf
der Website. All das sind RSS-Exporte oder -Importe unter **Setup > RSS** ([RSS-Feeds](../../anleitungen/rss-feeds.md)).

## Inhalt

1. [Welches Format wofür](#welches-format-wofür)
2. [Ein Feed der Episodenseiten](#ein-feed-der-episodenseiten)
3. [Eine Liste aller Sendungen (OPML)](#eine-liste-aller-sendungen-opml)
4. [Die Episoden eines Partners zeigen](#die-episoden-eines-partners-zeigen)
5. [Mehrere Feeds in Ordnung halten](#mehrere-feeds-in-ordnung-halten)

## Welches Format wofür

| Format im Formular | Gespeicherter Wert | Trägt | Verwenden Sie es für |
|---|---|---|---|
| Apple Podcasts (RSS 2.0 + iTunes) | `ITUNES` | Episoden mit Audio-Enclosures, iTunes-Kanalfelder | Podcast-Apps und Verzeichnisse |
| RSS 2.0 | `2.0` | Elemente mit Titel, Link, Beschreibung, Kategorie; ein Bild des Kanals | Newsreader, andere Websites |
| Atom 1.0 | `ATOM` | dasselbe, als Atom | Reader, die Atom bevorzugen |
| RSS 1.0 (RDF) | `1.0` | dasselbe, als RDF | ältere Systeme |
| OPML 2.0 (list of feeds) | `OPML` | Einträge, die andere Feeds, Knoten oder Adressen nennen | die Liste der Sendungen eines Netzwerks |

Die Bezeichnungen stammen aus `eZRSSExport::formatLabels()` (`kernel/classes/ezrssexport.php`).

> **Hinweis**
> Exporte in RSS 1.0, RSS 2.0 und Atom schreiben **kein Enclosure**: Ihr Writer (`generateFeed()`) hat keines. Eine
> Podcast-App, die sie abonniert, erhält nur Text. Verwenden Sie für Audio das Format Apple Podcasts.

## Ein Feed der Episodenseiten

Manche Hörer verfolgen eine Sendung in einem Newsreader, und Partner-Websites zeigen die neuesten Episoden. Für sie:

1. **Neuer Export**, Format **RSS 2.0** (oder Atom), ein Name wie "Example Show: neue Episoden".
2. Quelle: der Ordner der Sendung, **Unterknoten** wie im Podcast-Feed, die Episodenklasse.
3. Ordnen Sie **Titel** dem Titel und **Beschreibung** der kurzen Beschreibung zu.
4. Machen Sie ihn **Aktiv** und geben Sie ihm eine eigene Zugriffs-URL.

Leser erhalten dann einen Link zu jeder neuen Episodenseite, auf der der Player und die vollständigen Notizen stehen.
Der RSS-2.0-Export kann ein **Bild** tragen, ausgewählt aus den Bildern der Website.

## Eine Liste aller Sendungen (OPML)

Ein Netzwerk mit mehreren Sendungen kann ein OPML-Dokument veröffentlichen, das alle ihre Feeds auflistet; Leser
importieren es und abonnieren alles auf einmal.

1. **Neuer Export**, **Feed-Format** `OPML 2.0 (list of feeds)`. Die Seite zeigt die OPML-Bedienelemente anstelle der
   Inhaltsquellen ([OPML-Exporte](../../bc/6.0/opml.md#einen-export-in-ein-opml-dokument-verwandeln)).
2. Fügen Sie für den Podcast-Export jeder Sendung einen Eintrag hinzu. Die Adresse jedes Eintrags wird beim Schreiben
   des Dokuments ermittelt, "so renaming a feed cannot leave a dead entry behind", sodass das Umbenennen eines Feeds
   keinen toten Eintrag hinterlassen kann.
3. Füllen Sie die Kopffelder aus (Name und E-Mail des Eigentümers), machen Sie den Export aktiv und öffnen Sie ihn.

Das Dokument wird als `text/x-opml` ausgeliefert (`kernel/private/classes/views/rss/feed.php`). Es enthält höchstens
`settings/site.ini [RSSSettings] OPMLMaxOutlines` Einträge, im Auslieferungszustand 5000 und nie mehr als 50000.

## Die Episoden eines Partners zeigen

Ein **RSS-Import** liest den Feed einer anderen Website und legt unterhalb eines Ziels für jeden Eintrag, den er noch
nicht gesehen hat, ein Objekt an. Der Cronjob `rssimport.php` führt aktive Importe aus; ohne ihn wird nichts gelesen.
Importe lesen nur Adressen mit `http` und `https`. Importierte Objekte behalten, was die Zuordnung ausfüllt; ein Import
lädt die Audiodateien des Partners nicht herunter ([RSS-Feeds](../../anleitungen/rss-feeds.md#4-eine-import-karte-und-der-cronjob)).
Importierte Objekte werden vom Import selbst nie entfernt; dafür gibt es ein Aufräumskript
([Aufräumen nach RSS-Importen](../../bc/6.0/cleanuprss.md)).

Fragen Sie den Partner vorher. Seine Episoden, Notizen und Cover sind seine Inhalte
([Rechte und Lizenzen](rechte-und-lizenzen.md)).

## Mehrere Feeds in Ordnung halten

- Benennen Sie Exporte so, dass sich die Liste gut sortiert: "Example Show: Podcast", "Example Show: neue Episoden".
- Verwenden Sie nach jeder Änderung am Inhaltsbaum den Filter **Brauchen Aufmerksamkeit** der RSS-Liste.
- Notieren Sie, welcher Partner oder welches Verzeichnis welche Adresse verwendet; einen Export zu entfernen lässt
  seine Adresse verstummen.

## Verwandte Seiten

- [Einen Podcast-Feed aufbauen](einen-podcast-feed-aufbauen.md)
- [RSS-Feeds für Ihre Website](../ersteller/website-ersteller/rss-feeds-fuer-ihre-website.md)
- [RSS- und Podcast-Feeds](../kunden/wachstum/rss-und-podcast-feeds.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
