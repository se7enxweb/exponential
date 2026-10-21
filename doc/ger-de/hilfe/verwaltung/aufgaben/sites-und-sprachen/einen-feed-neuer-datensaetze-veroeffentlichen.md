---
title: Einen Feed neuer Datensätze veröffentlichen
audience: government, admin
category: public-sector
order: 240
summary: Aufgabe für Administratoren - einen RSS-2.0- oder Atom-Export des Datenkatalogs unter Setup > RSS anlegen, die Felder der Datensätze zuordnen und den Feed von der Katalogseite aus verlinken.
keywords: RSS, Atom, Feed, neue Datensätze, Setup RSS, Export, Katalog, offene Daten, feed, new datasets, rss/feed, export, catalog, open data
type: help
---
# Einen Feed neuer Datensätze veröffentlichen

Diese Aufgabe richtet sich an Administratoren mit der Richtlinie `rss/edit`. Hintergrund:
[Feeds für neue und geänderte Datensätze](../../offene-daten/feeds-fuer-neue-und-geaenderte-datensaetze.md) und
[RSS-Feeds](../../../../anleitungen/rss-feeds.md).

## Schritte

1. **Setup > RSS** (`/rss/list`), **Neuer Export**.
2. **Der Feed**: Name „Neue Datensätze“, eine Beschreibung, Format **RSS 2.0** oder **Atom 1.0**, Zugriffs-URL
   `datasets`, kreuzen Sie **Aktiv** an.
3. **Was der Feed listet**: die Zahl der Elemente; in der Karte der Quelle mit **Durchsuchen** zum Katalogordner,
   kreuzen Sie **Unterknoten** an, wenn die Datensätze in Themenordnern liegen, wählen Sie die Klasse `dataset`,
   **Setzen**, ordnen Sie Titel und Beschreibung zu.
4. **OK**.
5. Verlinken Sie den Feed auf der Katalogseite: ein sichtbarer Link und `<link rel="alternate" type="application/rss+xml"
   href="/rss/feed/datasets">` im Kopf der Seite.

## Prüfen

Auf der Karte des Exports zeigt **Feed öffnen** das XML mit den neuesten Datensätzen; ein Feedreader zeigt abgemeldet
dasselbe.

## Probleme

| Problem | Lösung |
|---|---|
| Die Karte sagt **Braucht Aufmerksamkeit** | keine Adresse, keine Quelle oder ein fehlender Ort der Quelle |
| Ein neuer Datensatz fehlt | Feeds werden `[RSSSettings] CacheTime` Sekunden lang zwischengespeichert; `php bin/php/ezcache.php --clear-id=rss_cache` |

## Verwandte Seiten

- [RSS-Feeds für Ihre Site](../../../../../eng-us/help/creators/website-creators/rss-feeds-for-your-site.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
