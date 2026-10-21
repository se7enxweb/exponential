---
title: RSS-Feeds
audience: government, website-creator, admin
category: public-sector
order: 40
summary: Wie eine Site des öffentlichen Sektors Nachrichten und Bekanntmachungen mit den RSS-Exporten von Exponential unter Setup > RSS als RSS-Feeds veröffentlicht - Quellen, Formate, Elemente, die Feed-Adresse, Caching - und wann Sie die Feeds anderer Behörden mit RSS-Importen lesen.
keywords: RSS, Atom, Feed, RSS-Export, RSS-Import, Setup RSS, Quellen, Unterknoten, Podcast, feed, RSS export, RSS import, rss/feed, CacheTime, rss_cache, sources, subnodes, OPML, podcast, rssimport cronjob
type: help
---
# RSS-Feeds

Diese Seite richtet sich an Site-Ersteller und Administratoren, die Journalisten, Partner-Sites und Feedreadern einen
Feed mit Nachrichten, Pressemitteilungen oder Bekanntmachungen geben. In Exponential ist ein Feed ein **RSS-Export**,
eingerichtet unter **Setup > RSS** (`/rss/list`), wofür die Richtlinie `rss/edit` nötig ist. Alles Folgende ist in
[RSS-Feeds](../../../../anleitungen/rss-feeds.md) dokumentiert.

## Inhalt

1. [Feeds, die eine Behörde typischerweise anbietet](#feeds-die-eine-behörde-typischerweise-anbietet)
2. [Einen Export anlegen](#einen-export-anlegen)
3. [Prüfen und Caching](#prüfen-und-caching)
4. [Andere Feeds lesen: Importe](#andere-feeds-lesen-importe)
5. [Fallstricke](#fallstricke)

## Feeds, die eine Behörde typischerweise anbietet

| Feed | Quelle | Hinweis |
|---|---|---|
| Nachrichten | der Ordner News, mit Unterknoten, Klasse Article | der meistgenutzte |
| Pressemitteilungen | der Ordner der Pressemitteilungen | für Medien |
| Bekanntmachungen oder Warnmeldungen | der Ordner Notices | kurzlebige Elemente; Reader sehen sie, bis sie versteckt werden |
| Beteiligungsverfahren | der Ordner der Beteiligungsverfahren | neue Beteiligungsverfahren |
| Stellen | der Ordner der Stellen, Klasse Job position | |

## Einen Export anlegen

1. **Setup > RSS**, **Neuer Export**.
2. **Der Feed**: Name, Beschreibung, **Feed-Format** (RSS 2.0 wird am meisten gelesen; Atom 1.0, RSS 1.0, OPML und
   Apple Podcasts gibt es ebenfalls), **URL des erzeugten RSS-Feeds** (der Feed liegt dann unter
   `/rss/feed/<access URL>`), kreuzen Sie **Aktiv** an, Site-URL.
3. **Was der Feed listet**: die Zahl der Elemente; **Nur Hauptknoten**, damit Elemente mit zwei Orten einmal
   erscheinen; **Quelle hinzufügen**: mit **Durchsuchen** zum Ordner, kreuzen Sie **Unterknoten** an, um die
   Jahresordner darunter einzuschließen, wählen Sie die Klasse, drücken Sie **Setzen** und wählen Sie, welche Attribute
   Titel (`title`), Beschreibung (`intro`), Kategorie und Anhang werden.
4. **OK**. Die Karte in der Liste zeigt die Feed-Adresse und **Feed öffnen**.

Feeds können mehrere Quellen haben (eine Karte je Quelle), zum Beispiel Nachrichten und Pressemitteilungen in einem
gemeinsamen Feed.

## Prüfen und Caching

| Prüfung | Wo |
|---|---|
| Der Feed öffnet sich | **Feed öffnen** auf der Karte |
| Die Karte sagt **Braucht Aufmerksamkeit** | keine Adresse, keine Quelle oder eine Quelle, die es nicht mehr gibt |
| Eine Änderung erscheint nicht | der Feed wird `site.ini [RSSSettings] CacheTime` Sekunden zwischengespeichert; warten Sie oder leeren Sie den RSS-Cache: `php bin/php/ezcache.php --clear-id=rss_cache` |

Für dringende Bekanntmachungen hält ein kurzer `CacheTime` für den Siteaccess des Bekanntmachungs-Feeds, oder das
Leeren des RSS-Caches in Ihrem Ablauf, Reader aktuell. Feedreader fragen nach ihrem eigenen Plan ab; RSS ist nicht
sofort.

Verlinken Sie die Feeds von den Seiten, zu denen sie gehören, mit einem sichtbaren Link („Nachrichten als RSS-Feed“)
und einem `<link rel="alternate" type="application/rss+xml">` im Kopf der Seite, damit Reader sie finden
([RSS-Feeds für Ihre Site](../../../../../eng-us/help/creators/website-creators/rss-feeds-for-your-site.md)).

## Andere Feeds lesen: Importe

Ein **RSS-Import** liest den Feed einer anderen Site und legt für jedes neue Element ein Objekt unter einem Ziel an,
wenn der Cronjob `rssimport` läuft ([Cronjobs](../../../../anleitungen/cronjobs.md)). Nützlich, um die Nachrichten einer
Partnerbehörde oder die Warnungen einer Bundesbehörde auf Ihrer Site zu zeigen.

| Vor dem Import | Warum |
|---|---|
| Die Quelle ist amtlich und stabil | Sie veröffentlichen erneut, was sie sagt |
| Das Ziel ist ein Ordner nur für Importe | importierte Objekte sind Inhalte in Ihrem Baum |
| Die Klasse ordnet Titel, Text und Link zu | im Importformular **3. Was aus jedem Eintrag wird** |
| Jemand prüft, was hereinkommt | Fehler in der Quelle erscheinen auf Ihrer Site |

Das Entfernen eines Imports beendet das Lesen; die von ihm angelegten Objekte bleiben.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Ein Quellordner mit eingeschränkten oder versteckten Inhalten | öffnen Sie den Feed abgemeldet und prüfen Sie, dass er nur listet, was Besucher sehen dürfen; wählen Sie Quellen, deren Inhalte alle öffentlich sind |
| Ein Export mit dem falschen Siteaccess | Links im Feed zeigen auf den falschen Host; prüfen Sie **Links zeigen auf** auf der Karte |
| Einen Export entfernen, auf den sich Medien verlassen | Reader erhalten einen Fehler; deaktivieren Sie ihn zuerst, informieren Sie sie, entfernen Sie ihn dann |

## Verwandte Seiten

- [Nachrichten und Warnmeldungen](README.md)
- [RSS: Feeds für Apple Podcasts und die Feed-Liste](../../../../../eng-us/features/6.0/rss-podcast-and-feed-list.md)
- [Benachrichtigungen und Abonnements](benachrichtigungen-und-abonnements.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
