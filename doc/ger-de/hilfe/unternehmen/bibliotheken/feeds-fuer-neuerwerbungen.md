---
title: Feeds für Neuerwerbungen
audience: businesses, editor, admin
category: business-solutions
order: 70
summary: Wie eine Bibliothek oder ein Archiv Lesern und Partnerwebsites sagt, was neu ist - Feeds als RSS 2.0 und Atom mit neuen Einträgen, ein Feed pro Thema oder Sammlung, Browser die Feeds finden lassen, Podcasts aufgezeichneter Vorträge, OPML-Listen, ein wiederkehrender Newsletter der Neuerwerbungen und Listen der Neuerwerbungen auf der Website selbst.
keywords: Neuerwerbungen, RSS, Atom, Feed, OPML, Podcast, rss/feed, Syndication, neue Bücher, wiederkehrender Newsletter, Artikelpool, Bibliothek, new acquisitions, feed, podcast, syndication, new books, recurring newsletter, article pool, library
type: help
---
# Feeds für Neuerwerbungen

Diese Seite ist für Bibliothekare und Mitarbeiter der Kommunikation, die möchten, dass Leser, Lehrkräfte und
Partnerwebsites von neuen Einträgen erfahren, ohne jeden Tag den Katalog zu besuchen. Sie erklärt die Feed-Exporte von
Exponential in einfachen Worten, die Einrichtungen, die zu einer Bibliothek passen, und zwei weitere Kanäle, die
dieselben Inhalte verwenden: eine Liste auf der Website und einen Newsletter.

## Inhalt

1. [Drei Kanäle aus denselben Einträgen](#drei-kanäle-aus-denselben-einträgen)
2. [Was ein Feed-Export ist](#was-ein-feed-export-ist)
3. [Feeds, die eine Bibliothek typischerweise anbietet](#feeds-die-eine-bibliothek-typischerweise-anbietet)
4. [Einen Feed für Neuerwerbungen einrichten](#einen-feed-für-neuerwerbungen-einrichten)
5. [Lesern die Feeds zugänglich machen](#lesern-die-feeds-zugänglich-machen)
6. [Aufgezeichnete Vorträge als Podcast](#aufgezeichnete-vorträge-als-podcast)
7. [Ein Newsletter der Neuerwerbungen](#ein-newsletter-der-neuerwerbungen)
8. [Neuerwerbungen auf der Website](#neuerwerbungen-auf-der-website)
9. [Checkliste für Feeds](#checkliste-für-feeds)

## Drei Kanäle aus denselben Einträgen

| Kanal | Erreicht | Funktion |
|---|---|---|
| Ein Feed | Feedreader, Partnerwebsites, andere Systeme | RSS-Exporte (`/rss/feed/<address>`) |
| Ein Newsletter | die Postfächer der Abonnenten | wiederkehrende Versendungen von `cjw_newsletter` mit einem Artikelpool |
| Eine Liste auf der Website | Besucher | eine Ordnerliste, eine Tag-Seite oder ein Layout-Block |

Alle drei lesen dieselben veröffentlichten Einträge; Katalogisierende haben also keine zusätzliche Arbeit: Einen
Eintrag zu veröffentlichen genügt.

## Was ein Feed-Export ist

„An **export** is a feed this site publishes at `/rss/feed/<address>`“: Ein Export ist ein Feed, den diese Website
unter `/rss/feed/<address>` veröffentlicht. Er listet die neuesten Objekte unterhalb seiner **Quellen** (Orte, jeweils
mit einer Klasse und optional ihren Unterelementen), als RSS 1.0, RSS 2.0, Atom, OPML oder als Feed für Apple Podcasts
([RSS-Feeds, In short](../../../anleitungen/rss-feeds.md#kurz-gesagt)). Exporte brauchen keinen Cronjob; Importe der Feeds
anderer Websites schon (`rssimport.php`).

Feed-Adressen werden aus dem eigenen Siteaccess des Feeds aufgelöst; Feeds bewerben also die öffentliche Website und
nicht den Host der Administration
([Feed-Adressen verwenden die öffentliche Website](../../../../eng-us/features/6.0/rss-podcast-and-feed-list.md#feed-addresses-use-the-public-site)).

## Feeds, die eine Bibliothek typischerweise anbietet

| Feed | Quellort | Klasse | Unterelemente |
|---|---|---|---|
| Alle neuen Einträge | Die Wurzel des Katalogs | Jede Eintragsklasse (eine Quelle pro Klasse) | Ja |
| Neu in einer Sammlung | Die Sammlung | Eintragsklassen | Ja |
| Neuigkeiten und Veranstaltungen | Der Nachrichtenordner | Article, Event | Nein |
| Eine Liste aller Feeds | Ein OPML-Export, der die anderen Feeds aufführt | | |

Eine Quelle nennt einen Ort und eine Klasse; fügen Sie eine Quelle pro Eintragsklasse hinzu, wenn ein Feed Bücher und
Aufnahmen enthalten soll. Die Karte eines Exports zeigt bis zu fünf Quellen, „then 'and N more'“ (danach „und N
weitere“, [RSS-Feeds, An export card](../../../anleitungen/rss-feeds.md#3-eine-export-karte)).

Feeds nach Thema: Eine Feed-Quelle ist ein Ort im Inhaltsbaum, kein Tag. Wenn Leser einen Feed pro Thema möchten,
braucht das Thema einen Ort (einen Ordner), oder ein Entwickler muss einen Feed auf dem Tag bauen; planen Sie das mit
Ihrem Website-Ersteller.

## Einen Feed für Neuerwerbungen einrichten

1. **Setup > RSS > Neuer Export.**
2. Geben Sie ihm einen Namen und eine Adresse, zum Beispiel `new-acquisitions`; der Feed liegt unter
   `/rss/feed/new-acquisitions`.
3. Fügen Sie die Quellen hinzu: die Wurzel des Katalogs, die Eintragsklasse, mit Unterelementen; ordnen Sie Titel und
   Beschreibung den Feldern des Eintrags zu (Titel, Zusammenfassung).
4. Wählen Sie das Format (RSS 2.0 oder Atom für die meisten Websites), die Zahl der Einträge und den Siteaccess, auf den
   seine Links zeigen.
5. Aktivieren Sie ihn, drücken Sie **Feed öffnen** und prüfen Sie das Ergebnis in einem Feedreader.

Quelle: [RSS-Feeds für Ihre Website, Publishing a feed](../../../../eng-us/help/creators/website-creators/rss-feeds-for-your-site.md#publishing-a-feed).
Bilder in Feeds verwenden den Bildalias `rss` (88 x 31 in `settings/image.ini`); wenn Cover wichtig sind, fragen Sie
Ihren Designer nach der Größe des Alias.

> **Tipp**
> Ein Feed listet die zuletzt veröffentlichten Objekte (`kernel/classes/ezrssexportitem.php` sortiert seine Quellen nach
> `published`, die neuesten zuerst); ein Eintrag, der später bearbeitet wird, ist also nicht allein deshalb wieder
> „neu“, weil er sich geändert hat. Wenn Sie eine alte Sammlung importieren, veröffentlichen Sie sie, bevor der Feed
> angekündigt wird, sonst füllt sich der erste Feed mit alten Einträgen.

## Lesern die Feeds zugänglich machen

- Fügen Sie dem `<head>` des Pagelayouts einen Autodiscovery-Link hinzu, damit Browser und Feedreader den Feed finden,
  und einen sichtbaren Link auf der Seite, um die es im Feed geht
  ([RSS-Feeds für Ihre Website](../../../../eng-us/help/creators/website-creators/rss-feeds-for-your-site.md#letting-browsers-and-readers-find-it)).
- Stellen Sie sicher, dass `robots.txt` `/rss/feed/` nicht sperrt, wenn Feeds gefunden werden sollen (dieselbe Seite,
  Checkliste).
- Nennen Sie Partnerinstitutionen die Adresse; sie können den Feed mit ihren eigenen Systemen importieren, oder mit
  einem RSS-Import, wenn sie ebenfalls Exponential betreiben.

## Aufgezeichnete Vorträge als Podcast

Ein RSS-Export kann als **Apple Podcasts (RSS 2.0 + iTunes)** geschrieben werden, „so a show published here can be
submitted to Apple without a hand written feed“ (damit eine hier veröffentlichte Sendung ohne handgeschriebenen Feed bei
Apple eingereicht werden kann). Die Quelle braucht ein Audiodateifeld, das als Enclosure zugeordnet ist
([RSS: Feeds für Apple Podcasts](../../../../eng-us/features/6.0/rss-podcast-and-feed-list.md#apple-podcasts-as-a-feed-format)).
Autorengespräche, Lesungen oder Vorträge, die die Bibliothek aufzeichnet, lassen sich so veröffentlichen.

## Ein Newsletter der Neuerwerbungen

Die Erweiterung `cjw_newsletter` (Release 4.2.0) kann nach einem Zeitplan einen Newsletter aus einem **Artikelpool**
verschicken:

- eine **wiederkehrende Versendung** im Modus **copy** kopiert eine Vorlagenausgabe und füllt sie mit **auto-fill** aus
  dem Pool mit Inhalten, die seit der letzten Versendung des Zeitplans veröffentlicht wurden;
- **skip if empty** (der Standard) verschickt nichts, wenn nichts Neues veröffentlicht wurde;
- die Voreinstellungen des Pools sind die Inhaltswurzel, die Klasse `cjw_newsletter_article`, 10 Einträge und 31 Tage
  (`[ArticlePoolSettings]`); setzen Sie den Pool der Liste auf Ihren Katalog und Ihre Eintragsklassen.

Quelle: [editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md). Es werden nur Inhalte
übernommen, die der anonyme Benutzer lesen darf; eingeschränkte Einträge erreichen den Newsletter also nie.
Einwilligung, Versand und alles Weitere stehen im Bereich [Newsletter](../newsletter/README.md).

## Neuerwerbungen auf der Website

- Die vollständige Ansicht eines Ordners kann seine neuesten Unterelemente auflisten; eine Tag-Seite listet Einträge zu
  einem Thema auf ([Tags und Schlagwörter](tags-und-schlagwoerter.md#themenseiten-und-listen-nach-thema)).
- Ein Layout-Block mit einer dynamischen Sammlung hält sich selbst aktuell, zum Beispiel „die neuesten fünf Artikel in
  dieser Sektion“ ([Landingpages mit Layouts](../../../../eng-us/help/creators/website-creators/landing-pages-with-layouts.md),
  [Referenz zu Layouts](../../../../eng-us/bc/6.0/LAYOUTS.md)).
- Ein gespeichertes PDF der Neuerwerbungen des Monats kann zum Herunterladen angeboten werden
  ([PDF- und Druckexporte](pdf-und-druckexporte.md)).

## Checkliste für Feeds

- [ ] Jeder Export hat eine Quelle, eine Adresse und ein Format und ist aktiv.
- [ ] Jede Eintragsklasse, die erscheinen soll, hat eine eigene Quelle.
- [ ] Titel und Beschreibung Feldern des Eintrags zugeordnet, die sich auch ohne Zusammenhang gut lesen.
- [ ] Links im Feed zeigen auf den öffentlichen Siteaccess; **Feed öffnen** in einem Feedreader geprüft.
- [ ] Das Pagelayout verlinkt die Feeds für Autodiscovery; `robots.txt` sperrt `/rss/feed/` nicht.
- [ ] Alte Sammlungen veröffentlicht, bevor ein Feed angekündigt wird.
- [ ] Ein wiederkehrender Newsletter geplant, wenn Leser Mails bevorzugen.

## Verwandte Seiten

- [Bibliotheken und Kataloge](README.md)
- [RSS-Feeds (Anleitung)](../../../anleitungen/rss-feeds.md)
- [RSS-Feeds für Ihre Website](../../../../eng-us/help/creators/website-creators/rss-feeds-for-your-site.md)
- [Feeds und Syndication](../mobil-und-headless/feeds-und-syndication.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
