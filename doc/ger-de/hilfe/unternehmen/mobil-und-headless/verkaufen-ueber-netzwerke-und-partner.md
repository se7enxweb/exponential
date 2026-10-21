---
title: Verkaufen über Netzwerke und Partner
audience: businesses, sales-marketing, partner
category: business-solutions
order: 80
summary: Wie ein Unternehmen mit Exponential Kunden über seine eigene Website hinaus erreicht - Partner- und Händlerwebsites, Apps, Marktplätze und Datenfeeds, Suchmaschinen und soziale Netzwerke - und welche Funktion zu jedem Kanal passt, mit den Vereinbarungen und Kontrollen, die einzurichten sind.
keywords: online verkaufen, Partner, Händler, Wiederverkäufer, Marktplätze, Netzwerke, Produktfeeds, REST API, Syndication, xrowextract, Sitemaps, Open Graph, Partnerportal, Unternehmen, sell online, partners, dealers, resellers, marketplaces, networks, product feeds, syndication, sitemaps, partner portal, business
type: help
---
# Verkaufen über Netzwerke und Partner

Diese Seite ist für Inhaber, Vertriebsverantwortliche und Partnermanager, die außerhalb ihrer eigenen Website verkaufen
oder gefunden werden möchten: über Händler und Wiederverkäufer, auf Partnerwebsites, in einer mobilen App, auf
Marktplätzen und Vergleichsportalen, die Datendateien annehmen, in Suchmaschinen und in sozialen Netzwerken. Sie ordnet
jedem Kanal zu, was Exponential bietet, und listet auf, was mit Partnern zu vereinbaren ist. Sie verspricht keine
bestimmte Anbindung an einen Marktplatz: Wo die Quellen keine beschreiben, sagt diese Seite das.

## Inhalt

1. [Kanäle und die passenden Funktionen](#kanäle-und-die-passenden-funktionen)
2. [Partner- und Händlerwebsites](#partner--und-händlerwebsites)
3. [Datendateien für Marktplätze und Partner](#datendateien-für-marktplätze-und-partner)
4. [Suchmaschinen und soziale Netzwerke](#suchmaschinen-und-soziale-netzwerke)
5. [Ihre eigene App als Kanal](#ihre-eigene-app-als-kanal)
6. [Vereinbarungen und Kontrollen](#vereinbarungen-und-kontrollen)

## Kanäle und die passenden Funktionen

| Kanal | Funktion | Lesen |
|---|---|---|
| Ihre Website und Ihr Shop | Der eingebaute Shop | [Wie Unternehmen online verkaufen](../wie-unternehmen-online-verkaufen.md), [Shops](../shops/README.md) |
| Partner- oder Händlerwebsites auf Exponential | `syndication`, in eine oder beide Richtungen | [Feeds und Syndication](feeds-und-syndication.md#syndication-zwischen-installationen) |
| Partner- oder Händlerwebsites auf anderen Systemen | Feeds als RSS oder Atom; die REST API; geplante Dateiexporte | unten |
| Ein Portal, in dem sich Partner anmelden | Ein Extranet; die Remote-Dienste und Beispielportale | [Extranets](../extranets/README.md), [Entfernte Dienste](entfernte-dienste.md) |
| Marktplätze und Vergleichsportale, die Dateien annehmen | Geplante Exporte mit `xrowextract` | unten |
| Suchmaschinen | Sitemaps und Seitenmetadaten | unten |
| Soziale Netzwerke | Open-Graph-Tags | unten |
| Ihre eigene mobile App | Die REST API | [Headless mit der REST API](headless-mit-der-rest-api.md) |
| Feedreader und Podcast-Apps | RSS, Atom, Apple Podcasts | [Feeds und Syndication](feeds-und-syndication.md) |

## Partner- und Händlerwebsites

Drei Wege, je nachdem, was der Partner betreibt:

- **Der Partner betreibt ebenfalls Exponential**: `syndication` exportiert einen Teil Ihres Inhaltsbaums als Feed, den
  die Installation des Partners importiert, per Cron synchron gehalten. Seine Dokumentation nennt „portal networks“ und
  „large e-commerce networks“ (Portalnetzwerke und große E-Commerce-Netzwerke) unter den Verwendungen der Syndication in
  beide Richtungen, und Importe können sich gegenüber einer geschützten Quelle authentifizieren
  ([syndication](../../../../eng-us/features/6.0/extensions/syndication.md)).
- **Der Partner betreibt ein anderes System**: Geben Sie ihm einen Feed (RSS oder Atom) der Produkte oder Neuigkeiten,
  die er zeigen darf, oder einen Integrationsbenutzer mit einem Schlüssel nur zum Lesen für die REST API
  ([Apps und API-Schlüssel](apps-und-api-schluessel.md)).
- **Der Partner meldet sich bei Ihnen an**: ein Händler- oder Partnerbereich auf Ihrer eigenen Website, mit
  Registrierung, Freigabe und Berechtigungen
  ([Händler- und Lieferantenportale](../extranets/haendler-und-lieferantenportale.md)).

## Datendateien für Marktplätze und Partner

Viele Marktplätze, Vergleichsportale und Großhandelspartner nehmen Produktdaten als Dateien an. `xrowextract`, der Tab
**Export** der Administration, „turns content into files and reads files back into content“ (macht aus Inhalten
Dateien und liest Dateien zurück in Inhalte): CSV, JSON, XML und Inhaltspakete, mit Vorschauen, Filtern, gespeicherten
Voreinstellungen, Hintergrundjobs, geplanten Läufen und Zustellung an SFTP, S3 und andere Ziele
([xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md)). Das ältere `ezxmlexport` bleibt für Websites erhalten,
die es noch verwenden; für neue Arbeit verweist seine Seite auf `xrowextract`.

> **Hinweis**
> Die Quellen beschreiben keinen fertigen Konnektor für einen bestimmten Marktplatz. Jeder Marktplatz hat sein eigenes
> Dateiformat und seine eigenen Regeln; Ihre Entwickler bilden Ihre Produktfelder mit einer Export-Voreinstellung
> darauf ab oder bauen eine Integration auf der REST API oder den Remote-Diensten. Planen Sie diese Zuordnung pro
> Marktplatz im Budget ein.

## Suchmaschinen und soziale Netzwerke

| Bedarf | Funktion | Lesen |
|---|---|---|
| Suchmaschinen finden alle öffentlichen Seiten | XML-Sitemaps pro Siteaccess, mit einer mehrsprachigen Variante (`bcgooglesitemaps`), oder Sitemaps aus `xrowmetadata` | [Sitemaps](../../../../eng-us/help/creators/website-creators/sitemaps.md), [bcgooglesitemaps](../../../../eng-us/features/6.0/extensions/bcgooglesitemaps.md) |
| Titel, Beschreibungen und kanonische Links pro Seite | Das Metadatenattribut von `xrowmetadata` | [Seitenmetadaten und Teilen in sozialen Medien](../../../../eng-us/help/creators/website-creators/metadata-and-social-sharing.md) |
| Gute Vorschauen, wenn ein Link geteilt wird | Open-Graph-Tags; das Simple-Theme gibt `og:title`, `og:site_name` und ein `og:image` aus; `xrowmetadata` fügt ein Open-Graph-Bild pro Seite hinzu | dieselbe |
| Grundlagen, um gefunden zu werden | Adressen, Überschriften, Sprachversionen | [SEO-Grundlagen für Exponential-Websites](../../../../eng-us/help/creators/website-creators/seo-basics.md) |

## Ihre eigene App als Kanal

Eine App liest Ihre Produkte und Inhalte über die REST API; Warenkorb, Bestellablauf und Bestellhistorie lassen sich
über die Remote-Dienste anbieten, die Produkte, Warenkorb und Bestellablauf, Bestellungen, Zahlungen und Versand
umfassen ([Entfernte Dienste](entfernte-dienste.md#was-sich-darüber-erledigen-lässt)). Die Zahlungen selbst laufen über die
Zahlungsanbieter Ihres Shops ([Zahlungsanbieter für Unternehmen](../shops/zahlungsanbieter-fuer-unternehmen.md)). Lesen
Sie [Mobile Strategie](mobile-strategie.md), bevor Sie sich auf eine App festlegen.

## Vereinbarungen und Kontrollen

Bevor Inhalte oder Daten Ihre Installation zu einem Partner verlassen:

- [ ] **Was darf der Partner zeigen?** Nennen Sie die Ordner, Klassen und Felder; geben Sie Zugriff auf nichts anderes.
- [ ] **Welche Zugangsdaten?** Eine pro Partner, mit dem kleinsten Bereich, widerrufen, wenn der Vertrag endet
      ([Hausregeln für ein Unternehmen](apps-und-api-schluessel.md#hausregeln-für-ein-unternehmen)).
- [ ] **Preise und Bestand**: Wer ist verantwortlich, wenn ein Partner einen veralteten Preis zeigt? Vereinbaren Sie
      Aktualisierungsintervalle.
- [ ] **Rechte**: Bilder und Texte, die Sie verschicken, sind vielleicht nur für Ihre eigene Verwendung lizenziert;
      prüfen Sie das, bevor Sie sie weitergeben.
- [ ] **Personenbezogene Daten**: Bekommen Partner Kundendaten? Wenn ja, schließen Sie einen Vertrag zur
      Auftragsverarbeitung ([Sicherheit](../../../../eng-us/help/businesses/security/README.md)); diese Seite ist keine Rechtsberatung.
- [ ] **Monitoring**: Feeds, Exporte und Importe laufen per Cron; jemand prüft, dass sie gelaufen sind
      ([Zuverlässigkeit des App-Backends](zuverlaessigkeit-des-app-backends.md)).
- [ ] **Ein Ausstiegsplan**: wie der Zugriff und die Kopien eines Partners entfernt werden, wenn Sie sich trennen.

## Verwandte Seiten

- [Feeds und Syndication](feeds-und-syndication.md)
- [Entfernte Dienste](entfernte-dienste.md)
- [Wie Unternehmen online verkaufen](../wie-unternehmen-online-verkaufen.md)
- [Ihren Shop bewerben](../shops/ihren-shop-bewerben.md)
- [Partnerportal planen](../extranets/partnerportal-planen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
