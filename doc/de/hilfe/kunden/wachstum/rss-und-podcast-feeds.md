---
title: RSS- und Podcast-Feeds
audience: customer, editor, admin
category: customer-success
order: 14
summary: Was ein Website-Betreiber mit den Feeds einer Exponential-Website macht - Exporte als RSS, Atom, OPML und für Apple Podcasts, Feeds anderer Websites importieren, was pro Feed zu entscheiden ist, ein Podcast aus Sicht des Betreibers und die Prüfungen, die Feeds funktionsfähig halten.
keywords: RSS, Atom, OPML, Podcast, Apple Podcasts, Feed-Export, Feed-Import, Syndikation, rssimport, Nachrichten-Feed, Website-Betreiber, podcast, feed export, feed import, syndication, news feed, site owner
type: help
---
# RSS- und Podcast-Feeds

Diese Seite ist für Betreiber, die möchten, dass Neuigkeiten, Veranstaltungen oder Folgen Menschen über Feed-Reader,
Partnerwebsites und Podcast-Verzeichnisse erreichen. Exponential veröffentlicht Feeds aus Inhalten und kann die Feeds
anderer Websites in Inhalte einlesen ([RSS-Feeds](../../../../en/us/guides/rss-feeds.md)). Der Administrator richtet sie ein;
Sie entscheiden, wofür jeder Feed da ist, und halten ihn am Leben.

## Inhalt

1. [Was die Website veröffentlichen und lesen kann](#was-die-website-veröffentlichen-und-lesen-kann)
2. [Pro Feed entscheiden](#pro-feed-entscheiden)
3. [Ein Podcast](#ein-podcast)
4. [Andere Feeds importieren](#andere-feeds-importieren)
5. [Feeds funktionsfähig halten](#feeds-funktionsfähig-halten)

## Was die Website veröffentlichen und lesen kann

Aus [RSS-Feeds](../../../../en/us/guides/rss-feeds.md):

- Ein **Export** ist ein Feed, den diese Website unter `/rss/feed/<address>` veröffentlicht. Er listet die neuesten
  Objekte unterhalb seiner **Quellen** (Orte, jeweils mit einer Klasse und optional ihren Unterelementen), als RSS 1.0,
  RSS 2.0, Atom, OPML oder als Feed für Apple Podcasts.
- Ein **Import** liest den Feed einer anderen Website und legt unterhalb seines Ziels für jeden Eintrag, den er noch
  nicht gesehen hat, ein Element an. Der Cronjob `rssimport` führt die aktiven Importe aus; ohne ihn wird nichts gelesen.
- Die Seite in der Administration (**Setup > RSS**) zeigt die Exporte und Importe mit einer Übersicht und einem Filter
  für jene, die Aufmerksamkeit brauchen.

## Pro Feed entscheiden

| Frage | Beispielantwort |
|---|---|
| Wofür ist der Feed da? | „Neuigkeiten für Partnerwebsites und Leser“ |
| Welcher Teil des Baums ist seine Quelle, und welche Klasse? | der Ordner News, Klasse Article |
| Welches Format? | RSS 2.0 für Leser; Apple Podcasts für eine Sendung |
| Wer ist zuständig? | die Nachrichtenredaktion |
| Wer nutzt ihn? | listen Sie die Partnerwebsites auf, die ihn einbinden, damit Sie sie über Änderungen informieren können |

> **Warnung**
> Wird ein Export entfernt, antwortet seine Adresse seinen Lesern nicht mehr ([RSS-Feeds](../../../../en/us/guides/rss-feeds.md)).
> Bevor Sie einen Feed entfernen oder umbenennen, informieren Sie die Websites und Personen, die ihn nutzen.

## Ein Podcast

Ein Export kann als **Apple Podcasts (RSS 2.0 + iTunes)** geschrieben werden, sodass eine auf der Website
veröffentlichte Sendung ohne handgeschriebenen Feed bei Apple eingereicht werden kann
([RSS, Podcast und die Feed-Liste](../../../../en/us/features/6.0/rss-podcast-and-feed-list.md)). Aus Sicht des Betreibers:

| Sie liefern | Warum |
|---|---|
| Eine Klasse für Folgen mit einem Feld für die Audiodatei | das Enclosure des Feeds muss ein Medien- oder Dateifeld nennen, das das Audio enthält |
| Name und E-Mail des Eigentümers, Coverbild, Kategorie | Pflichtfelder des Kanals |
| Autor, Sprache, Copyright, Beschreibung | optionale Felder des Kanals |
| Eine Entscheidung zu expliziten Inhalten | standardmäßig aus |
| Eine Person, die Folgen veröffentlicht | jede Folge ist ein Inhaltselement im Quellordner |

Wenn die Sendung endet, kann der Export sie als beendet kennzeichnen oder aus dem Verzeichnis heraushalten (dieselbe
Quelle). Vereinbaren Sie mit Ihrem Administrator, welche Funktionsadresse statt einer persönlichen in die E-Mail des
Eigentümers kommt.

## Andere Feeds importieren

Importe sind nützlich für die Neuigkeiten eines Partners auf Ihrer Website oder für einen Feed von einer anderen Ihrer
eigenen Websites. Bevor Sie einen anfordern:

- Haben Sie das Recht, die Inhalte erneut zu veröffentlichen?
- Wohin sollen die Elemente, und wer prüft sie?
- Ein entfernter Import behält die Elemente, die er angelegt hat ([RSS-Feeds](../../../../en/us/guides/rss-feeds.md)); die
  Feed-Liste kann aufräumen, was ein Import angelegt hat ([RSS, Podcast und die Feed-Liste](../../../../en/us/features/6.0/rss-podcast-and-feed-list.md)).

Für das Teilen in beide Richtungen zwischen Ihren eigenen Installationen siehe
[Feeds und Syndikation](../../../../en/us/help/businesses/mobile-and-headless/feeds-and-syndication.md).

## Feeds funktionsfähig halten

Jedes Quartal:

- [ ] Öffnen Sie jede Feed-Adresse, von der Sie wissen, dass sie genutzt wird: Sie antwortet und listet aktuelle Elemente.
- [ ] Die Feed-Adressen verwenden die öffentliche Website, nicht die Administration
      ([RSS, Podcast und die Feed-Liste](../../../../en/us/features/6.0/rss-podcast-and-feed-list.md)).
- [ ] **Setup > RSS**, Filter **Need attention**: Beheben oder stilllegen, was aufgelistet ist.
- [ ] Importe: Das neueste importierte Element ist aktuell; wenn nicht, läuft der Cronjob `rssimport` vielleicht nicht
      ([Cronjobs](../../../../en/us/guides/cronjobs.md)).
- [ ] Podcast: Das Verzeichnis zeigt die neueste Folge.

## Verwandte Seiten

- [RSS-Feeds](../../../../en/us/guides/rss-feeds.md)
- [RSS-Feeds für Ihre Website](../../../../en/us/help/creators/website-creators/rss-feeds-for-your-site.md)
- [Feeds für Neuerwerbungen](../../../../en/us/help/businesses/libraries/feeds-for-new-acquisitions.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
