---
title: RSS-Feeds für Ihr Blog
audience: individual, website-creator, customer
category: personal-sites
order: 1625
summary: Wie eine einzelne Person einem Blog mit den eingebauten RSS-Exporten unter Setup > RSS einen RSS- oder Atom-Feed gibt - Quellen, Feldzuordnung, Format, die Feed-Adresse, Autodiscovery, Podcasts, die optionale Erweiterung autorss und warum die Erweiterung syndication etwas anderes ist.
keywords: RSS, Atom, Feed, RSS-Export, Setup RSS, rss/feed, Autodiscovery, Feedreader, Podcast, autorss, syndication, Blog, abonnieren, feed reader, subscribe, RSS export
type: help
---
# RSS-Feeds für Ihr Blog

Diese Seite ist für Privatpersonen, deren Leser ihrem Blog in einem Feedreader folgen sollen. Ein Feed ist eine
Liste Ihrer neuesten Beiträge, die Feedreader für Ihre Leser prüfen, damit diese nicht an einen Besuch denken
müssen. Feeds sind in Exponential eingebaut; es muss nichts installiert werden.

## Inhalt

1. [Was mitgeliefert wird](#was-mitgeliefert-wird)
2. [Den Feed anlegen](#den-feed-anlegen)
3. [Leser den Feed finden lassen](#leser-den-feed-finden-lassen)
4. [Den Feed prüfen](#den-feed-prüfen)
5. [Podcasts](#podcasts)
6. [Optional: autorss](#optional-autorss)
7. [Kein Feed-Werkzeug: syndication](#kein-feed-werkzeug-syndication)
8. [Checkliste](#checkliste)

## Was mitgeliefert wird

Das Kernel-Modul `rss` verwaltet unter **Setup > RSS** **RSS-Exporte** (Feeds, die Ihre Website veröffentlicht) und
**RSS-Importe** (Feeds anderer Websites, die in Ihre eingelesen werden) ([RSS-Feeds](../../../anleitungen/rss-feeds.md)).

| | Export |
|---|---|
| Adresse | `/rss/feed/<name>` |
| Formate | RSS 1.0, RSS 2.0, Atom, OPML, Apple Podcasts |
| Braucht Cron | Nein |

Quelle: [RSS-Feeds für Ihre Website](../../ersteller/website-ersteller/rss-feeds-fuer-ihre-website.md) und
[RSS, Podcasts und Feedliste](../../../funktionen/6.0/rss-podcast-und-feed-liste.md).

## Den Feed anlegen

1. **Setup > RSS > Neuer Export.**
2. **Name und Adresse**: zum Beispiel `notes`; der Feed liegt dann unter `/rss/feed/notes`.
3. **Quelle**: Ihr *Blog* als Ort, *Blog post* als Klasse.
4. **Feldzuordnung**: Titel auf `title`, Beschreibung auf `body` (oder auf ein Zusammenfassungsfeld, wenn Ihre Klasse
   eines hat).
5. **Format**: RSS 2.0 oder Atom; beide werden von jedem gängigen Feedreader gelesen.
6. **Anzahl der Einträge**: zehn bis zwanzig sind üblich.
7. **Siteaccess**: die öffentliche Website, damit Links im Feed auf die öffentlichen Seiten zeigen.
8. **Aktivieren** und auf **Feed öffnen** klicken.

Bilder in Feeds verwenden die Bildvariante `rss` (88 x 31 in `settings/image.ini`).

## Leser den Feed finden lassen

- **Ein sichtbarer Link** auf der Blogseite: „Per RSS folgen", der auf `/rss/feed/notes` zeigt.
- **Autodiscovery**: eine Zeile `<link rel="alternate" type="application/rss+xml" ...>` im `<head>` des Pagelayouts,
  damit Browser und Feedreader den Feed über die Adresse Ihres Blogs finden. Das ist eine Template-Änderung; die
  Seite für Website-Ersteller enthält die genaue Zeile
  ([Browser und Leser den Feed finden lassen](../../ersteller/website-ersteller/rss-feeds-fuer-ihre-website.md#browser-und-feedreader-ihn-finden-lassen)).
- **`robots.txt`** darf `/rss/feed/` nicht sperren ([robots.txt](../../ersteller/website-ersteller/robots-txt.md)).

## Den Feed prüfen

- Öffnen Sie die Feed-Adresse in einem Browser: Sie sollten XML sehen, keine Fehlerseite.
- Fügen Sie den Feed einem Feedreader hinzu und veröffentlichen Sie einen Testbeitrag; er sollte nach der nächsten
  Prüfung des Readers erscheinen.
- Prüfen Sie, dass Links im Feed den öffentlichen Beitrag öffnen, nicht die Administration.
- Die Export-Karte unter **Setup > RSS** markiert Exporte, die Aufmerksamkeit brauchen (keine Adresse, eine fehlende
  Quelle).

## Podcasts

Wenn Sie Audio veröffentlichen, schreibt das Format **Apple Podcasts** RSS 2.0 mit den iTunes-Tags. Die Quellklasse
braucht ein Feld für eine Audiodatei ([RSS- und Podcast-Feeds](../../kunden/wachstum/rss-und-podcast-feeds.md)). Die
Audiodateien werden von Ihrem Server ausgeliefert; prüfen Sie also Speicherplatz und Datenvolumen Ihres Hostings.

## Optional: autorss

`autorss` ist eine eigene Erweiterung (Composer-Paket `se7enxweb/autorss`, Version 1.1.1 auf der
Demonstrationsinstallation), die ein Workflow-Ereignis hinzufügt, das beim Veröffentlichen von Inhalten automatisch
RSS-Exporte anlegt. Sie steht **nicht** in der `require`-Liste der `composer.json` im Wurzelverzeichnis; eine neue
Installation hat sie also nicht, und ihre eigene Beschreibung nennt sie ein Werkzeug „for advanced developers" (für
fortgeschrittene Entwickler). Für ein einzelnes Blog ist ein von Hand angelegter Export einfacher.

## Kein Feed-Werkzeug: syndication

Die installierte Erweiterung `syndication` klingt nach Feeds, ist aber keines: Sie synchronisiert Inhalte zwischen
getrennten Exponential-Installationen über SOAP ([syndication](../../../funktionen/6.0/erweiterungen/syndication.md)). Für
einen Blog-Feed brauchen Sie sie nicht.

## Checkliste

- [ ] Ein RSS-Export pro Blog, aktiv, mit Quelle, Zuordnung und Format.
- [ ] Links im Feed zeigen auf den öffentlichen Siteaccess.
- [ ] Ein sichtbarer Feed-Link auf der Blogseite.
- [ ] Autodiscovery-Zeile im Pagelayout, wenn Sie Templates bearbeiten können.
- [ ] In einem Feedreader getestet.

## Verwandte Seiten

- [Blogs](README.md)
- [RSS-Feeds für Ihre Website](../../ersteller/website-ersteller/rss-feeds-fuer-ihre-website.md)
- [RSS- und Podcast-Feeds](../../kunden/wachstum/rss-und-podcast-feeds.md)
- [RSS-Feeds (Anleitung)](../../../anleitungen/rss-feeds.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
