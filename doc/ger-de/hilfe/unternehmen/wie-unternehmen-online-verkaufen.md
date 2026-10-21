---
title: Wie Unternehmen mit Exponential online verkaufen
audience: businesses, sales-marketing, project-manager
category: business-solutions
order: 30
summary: Die drei Kanäle, über die eine Organisation mit Exponential Geld verdient - Website-Anwendungen, mobile Apps und Netzwerke von Websites und Partnern -, was die Software für jeden bereitstellt und wie sie auf einem gemeinsamen Inhaltsspeicher zusammenarbeiten.
keywords: online verkaufen, Kanäle, E-Commerce, Website-Anwendung, mobile App, headless, REST API, Feeds, RSS, ATOM, OPML, Syndication, Partnernetzwerk, Marktplatz, Newsletter, Multisite, Zahlungen, PayPal, Omnichannel, sell online, channels, e-commerce, website application, mobile app, feeds, syndication, partner network, marketplace, payments, omnichannel
type: help
---
# Wie Unternehmen mit Exponential online verkaufen

Diese Seite ist für Inhaber und Marketingverantwortliche, die wissen möchten, wie Exponential Inhalte in Umsatz
verwandelt. Sie beschreibt drei Kanäle, Website-Anwendungen, mobile Apps und Netzwerke, sagt mit Quelle, was die
Software für jeden bereitstellt, und zeigt, wie ein einziger Inhaltsspeicher alle drei bedient.

## Inhalt

1. [Ein Speicher, drei Kanäle](#ein-speicher-drei-kanäle)
2. [Kanal 1: Website-Anwendungen](#kanal-1-website-anwendungen)
3. [Kanal 2: mobile Apps](#kanal-2-mobile-apps)
4. [Kanal 3: Netzwerke von Websites und Partnern](#kanal-3-netzwerke-von-websites-und-partnern)
5. [Was Besucher zu jedem Kanal bringt](#was-besucher-zu-jedem-kanal-bringt)
6. [Warum Verfügbarkeit Teil des Verkaufs ist](#warum-verfügbarkeit-teil-des-verkaufs-ist)
7. [Eine Planungstabelle](#eine-planungstabelle)

## Ein Speicher, drei Kanäle

Exponential trennt Inhalt und Darstellung („Separation of content and presentation layer“, `README.md`, „Main
Exponential features“). Ein Produkt, ein Artikel oder ein Mitgliederdatensatz wird einmal gespeichert, als Inhalt einer
Klasse, die Sie festlegen, und jeder Kanal stellt ihn auf seine Weise dar:

| Kanal | Stellt die Inhalte dar als | Gebaut mit |
|---|---|---|
| Website-Anwendungen | HTML-Seiten, ein Shop, Bereiche nach Anmeldung | Templates, Designs, Exponential Layouts |
| Mobile Apps | JSON für eine App | Die REST API |
| Netzwerke | Feeds und synchronisierte Kopien auf anderen Websites | RSS-, ATOM-, iTunes- und OPML-Feeds; die Erweiterung `syndication`; Multisite |

Redakteure arbeiten an einem Ort. Ein Preis oder eine Beschreibung, die einmal geändert wird, ist in jedem Kanal
geändert, und damit fällt eine ganze Klasse von Fehlern weg (ein Produkt, das auf der Website zu einem Preis verkauft
wird, den die App nicht mehr zeigt).

## Kanal 1: Website-Anwendungen

Eine Website-Anwendung ist mehr als eine Broschüre: Kunden handeln auf ihr. Exponential bringt die Teile mit, die die
meisten Unternehmen brauchen:

| Bedarf | Bereitgestellt durch | Lesen |
|---|---|---|
| Produkte verkaufen | „Advanced e-Commerce Stores (Built in)“: Produkte, Warenkorb, Bestellungen, USt, Währungen | [Online-Shops](shops/README.md) |
| Zahlungen annehmen | Zahlungsanbieter über das Workflow-System, zum Beispiel PayPal mit `ezpaypal` | [Zahlungsanbieter für Unternehmen](shops/zahlungsanbieter-fuer-unternehmen.md) |
| Interessenten und Anfragen sammeln | „Contact forms“ und der Informationssammler | [Formulare mit dem Informationssammler](../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md) |
| Zugang verkaufen | Registrierung und Bereiche nur für Mitglieder über Rollen und Sektionen | [Mitgliederwebsites](mitglieder/README.md) |
| Partner und Händler bedienen | Extranets auf derselben Installation | [Extranets und Partnerportale](extranets/README.md) |
| Landingpages schnell bauen | Exponential Layouts, ein visueller Seitenbaukasten mit Regeln, die Layouts Inhalten zuordnen | [Der Layout-Editor](../layout-editor.md) |

Quellen: `README.md`, „Main Exponential features“, „Exponential Layouts“.

## Kanal 2: mobile Apps

Exponential hat „A headless CRUD REST API“ (`README.md`). Lesezugriffe antworten unter `/api/ezp/v1/...` und
`/api/ezp/v2/...`, Schreibzugriffe unter v2, und „jeder Lese- und Schreibzugriff prüft die Richtlinien des Benutzers“;
Clients authentifizieren sich mit OAuth2 oder einem persönlichen API-Schlüssel
([REST API v1 und v2: eine Extension](../../anleitungen/rest-api-v1-und-v2.md)).

Für ein Unternehmen bedeutet das:

- eine App zeigt dieselben Produkte, Artikel und Veranstaltungen wie die Website, am selben Ort bearbeitet;
- die App sieht nur, was das Konto, das sie verwendet, sehen darf, weil dieselben Rollen und Richtlinien gelten;
- die App braucht kein eigenes Inhalts-Backend, also ein System weniger, das gehostet und abgesichert werden muss.

Eine mobilfreundliche Website ist oft der erste Schritt und braucht gar keine App. Die Möglichkeiten werden in
[Mobile Strategie](mobil-und-headless/mobile-strategie.md) verglichen; die technische Seite steht in
[Headless mit der REST API](mobil-und-headless/headless-mit-der-rest-api.md) und
[Headless Exponential](../../../eng-us/help/creators/app-creators/headless-exponential.md).

## Kanal 3: Netzwerke von Websites und Partnern

Viele Organisationen verdienen über die Websites anderer und über mehrere eigene Websites:

| Netzwerk | Bereitgestellt durch | Lesen |
|---|---|---|
| Feedreader, Podcast-Apps, Aggregatoren | „RSS feeds“, „ATOM feeds“, „iTunes RSS feeds“, „OPML feeds“ | [RSS-Feeds: Exporte veröffentlichen und Importe einlesen](../../anleitungen/rss-feeds.md), [Feeds und Syndication](mobil-und-headless/feeds-und-syndication.md) |
| Ihre eigenen Marken oder Länder | „Multi-site support“ aus einer Installation | [Marken mit Multisite](multisite/README.md) |
| Websites von Partnern und Händlern, die Ihre Inhalte tragen | Die Erweiterung `syndication` exportiert einen Teil des Inhaltsbaums als Feed, den eine andere Installation importiert, in eine Richtung oder in beide, auch für „große E-Commerce-Netzwerke“ | [syndication](../../../eng-us/features/6.0/extensions/syndication.md) |
| Andere Systeme und Dienste | Entfernte Dienste und die REST API | [Entfernte Dienste](mobil-und-headless/entfernte-dienste.md) |

Wie Sie diese planen, steht in [Verkaufen über Netzwerke und Partner](mobil-und-headless/verkaufen-ueber-netzwerke-und-partner.md).

## Was Besucher zu jedem Kanal bringt

Die Software liefert die Werkzeuge; die Strategie ist Ihre Sache. Die Werkzeuge, die es im Repository gibt:

- **Newsletter** mit `cjw_newsletter`: Listen, Abonnements und Ausgaben ([Newsletter](../../../eng-us/help/businesses/newsletters/README.md)).
- **Suchmaschinen**: lesbare URL-Aliase, Sitemaps, `robots.txt` und Metadaten
  ([SEO-Grundlagen](../../../eng-us/help/creators/website-creators/seo-basics.md), [Sitemaps](../../../eng-us/help/creators/website-creators/sitemaps.md)).
- **Viele Sprachen**: Inhalte in den Sprachen Ihrer Kunden ([Mehrsprachige Unternehmen](../../../eng-us/help/businesses/multilingual/README.md)).
- **Feeds**, die andere Websites und Apps aufgreifen (siehe oben).

Marketingseiten für Menschen, die Exponential selbst verkaufen, sind ein anderes Thema; sie stehen unter
[Vertrieb und Marketing: hier beginnen](../geschaeft/vertrieb-und-marketing-hier-beginnen.md).

## Warum Verfügbarkeit Teil des Verkaufs ist

Jeder der Kanäle oben endet bei derselben Installation. Ist sie nicht erreichbar oder langsam, fallen Website, App und
Partner-Websites gemeinsam aus. Deshalb ist der Bereich [Zuverlässigkeit](zuverlaessigkeit/README.md) für einen
Vertriebsleiter genauso wichtig wie für die Person, die den Server betreibt, und deshalb sollte ein Unternehmen seine
[Kosten eines Ausfalls](kosten/arbeitsblatt-kosten-eines-ausfalls.md) kennen, bevor es ein Hosting wählt.

## Eine Planungstabelle

Kopieren Sie sie und füllen Sie sie für Ihre Organisation aus.

```text
CHANNEL PLAN

Channel                 | Now? | Revenue or saving it brings | Content it needs   | Owner
------------------------|------|-----------------------------|--------------------|---------
Website store           |      |                             |                    |
Signed-in customer area |      |                             |                    |
Partner or dealer area  |      |                             |                    |
Mobile app (REST)       |      |                             |                    |
Feeds (RSS, ATOM)       |      |                             |                    |
Partner sites (syndic.) |      |                             |                    |
Newsletter              |      |                             |                    |
Other brands / countries|      |                             |                    |

Cost of one hour unavailable (all channels): <from the cost of downtime worksheet>
```

## Verwandte Seiten

- [Unternehmen: hier beginnen](unternehmen-hier-beginnen.md)
- [Online-Shops](shops/README.md)
- [Mobil und headless](mobil-und-headless/README.md)
- [Funktionshighlights, den Bedürfnissen der Kunden zugeordnet](../geschaeft/funktionshighlights-fuer-kunden.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
