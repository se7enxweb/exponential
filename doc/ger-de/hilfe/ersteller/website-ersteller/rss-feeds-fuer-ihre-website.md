---
title: RSS-Feeds für Ihre Website
audience: website-creator, admin
category: website-creation
order: 410
summary: Veröffentlichen Sie RSS-2.0-, Atom-, OPML- oder Podcast-Feeds Ihrer Inhalte mit RSS-Exporten, verlinken Sie sie im Pagelayout, damit Browser und Feedreader sie finden, und importieren Sie Feeds anderer Websites mit dem Cronjob rssimport.
keywords: RSS, Atom, Feed, Podcast, OPML, RSS-Export, RSS-Import, Cronjob, automatische Feed-Erkennung, Website-Ersteller, RSS export, RSS import, rss/feed, rssimport, feed autodiscovery, website creator
type: help
---
# RSS-Feeds für Ihre Website

Diese Seite richtet sich an Website-Ersteller, die Feeds mit Nachrichten, Blogbeiträgen oder Podcasts anbieten oder die
Feeds anderer Websites anzeigen. Feeds werden unter **Setup > RSS** verwaltet
([RSS-Feeds](../../../anleitungen/rss-feeds.md)).

![Setup, RSS: die RSS-Exporte und -Importe der Website](../../../../eng-us/help/images/creators/rss-list.webp)

## Inhalt

1. [Exporte und Importe](#exporte-und-importe)
2. [Einen Feed veröffentlichen](#einen-feed-veröffentlichen)
3. [Browser und Feedreader ihn finden lassen](#browser-und-feedreader-ihn-finden-lassen)
4. [Podcasts](#podcasts)
5. [Einen Feed importieren](#einen-feed-importieren)
6. [Checkliste](#checkliste)

## Exporte und Importe

| | Export | Import |
|---|---|---|
| Was | ein Feed, den diese Website veröffentlicht | der Feed einer anderen Website, der in diese Website eingelesen wird |
| Adresse | `/rss/feed/<address>` | keine; er legt Objekte an |
| Formate | RSS 1.0, RSS 2.0, Atom, OPML, Apple Podcasts | was die andere Website anbietet |
| Braucht einen Cronjob | nein | ja: `rssimport.php` |

## Einen Feed veröffentlichen

1. **Setup > RSS > Neuer Export.**
2. Geben Sie ihm einen Namen und eine Adresse (zum Beispiel `news`): Der Feed liegt unter `/rss/feed/news`.
3. Fügen Sie **Quellen** hinzu: eine Platzierung (den Ordner News), die aufzulistende Klasse (`article`) und ob
   Unterelemente mitzählen; ordnen Sie Titel und Beschreibung den Feldern der Klasse zu (Titel, Einleitung).
4. Wählen Sie das Format (RSS 2.0 oder Atom für die meisten Websites), die Anzahl der Elemente und den Siteaccess, auf den
   seine Links zeigen.
5. Aktivieren Sie ihn, klicken Sie auf **Feed öffnen** und prüfen Sie das Ergebnis in einem Feedreader.

Die Karte des Exports zeigt, wann die zwischengespeicherte Kopie zuletzt geschrieben wurde, und markiert Exporte, die
Aufmerksamkeit brauchen (keine Adresse, eine fehlende Quelle). Bilder in Feeds verwenden die Bildvariante `rss`
(88 x 31 in `settings/image.ini`).

## Browser und Feedreader ihn finden lassen

Fügen Sie dem `<head>` des Pagelayouts einen Link zur automatischen Erkennung hinzu:

```text
<link rel="alternate" type="application/rss+xml" title="{'News'|i18n( 'design/mysite' )}"
      href={'rss/feed/news'|ezurl( , 'full' )}>
```

und für Besucher einen sichtbaren Link auf der Seite News.

## Podcasts

Das Format **Apple Podcasts** schreibt RSS 2.0 mit den iTunes-Tags. Quellen brauchen ein Feld mit einer Audiodatei; die
Funktionsseite [RSS, Podcast und Feedliste](../../../../eng-us/features/6.0/rss-podcast-and-feed-list.md) listet auf, was jedes
Format braucht.

## Einen Feed importieren

1. **Setup > RSS > Neuer Import**: die Adresse des Feeds, ein Zielordner und eine anzulegende Klasse (mit
   Feldzuordnung).
2. Aktivieren Sie ihn, und stellen Sie sicher, dass der Cronjob `rssimport` läuft ([Cronjobs](../../../anleitungen/cronjobs.md));
   ohne ihn wird nichts eingelesen.
3. Importierte Elemente sind gewöhnliche Objekte; wenn Sie den Import entfernen, bleiben sie erhalten.

Importieren Sie nur Feeds, die Sie weiterveröffentlichen dürfen.

## Checkliste

- [ ] Jeder Export hat eine Quelle, eine Adresse und ein Format und ist aktiv.
- [ ] Die Links im Feed zeigen auf den öffentlichen Siteaccess.
- [ ] Das Pagelayout verlinkt den Feed zur automatischen Erkennung.
- [ ] `robots.txt` sperrt `/rss/feed/` nicht, wenn Feeds gefunden werden sollen.
- [ ] Importe laufen per Cron in einen Ordner, den die Redaktion prüft.

## Verwandte Seiten

- [RSS-Feeds](../../../anleitungen/rss-feeds.md)
- [SEO-Grundlagen](seo-grundlagen.md)
- [RSS aufräumen](../../../bc/6.0/cleanuprss.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
