---
title: Ihre Website markengerecht gestalten
audience: website-creator, admin
category: website-creation
order: 160
summary: Bringen Sie Ihre Marke auf eine Exponential-Website - Website-Name und Beschreibung in site.ini, Logo und Fußbereich über das Seitendesign-Objekt, Favicons und das Web-Manifest in Ihrem Design und die Meta-Tags jeder Seite.
keywords: Markengestaltung, Branding, Website-Name, Logo, Favicon, Web-Manifest, Seitendesign, Meta-Tags, Website-Ersteller, branding, site name, SiteName, SiteDescription, MetaDataArray, logo, favicon, web manifest, page design, template_look, powered by, meta tags, Open Graph, website creator
type: help
---
# Ihre Website markengerecht gestalten

Diese Seite richtet sich an Website-Ersteller, die die Vorgaben durch ihre eigene Marke ersetzen: den Website-Namen,
das Logo, das Favicon, den Fußbereich und die Tags im Seitenkopf.

## Inhalt

1. [Website-Name und Beschreibung](#website-name-und-beschreibung)
2. [Das Logo und das Seitendesign-Objekt](#das-logo-und-das-seitendesign-objekt)
3. [Favicons und das Web-Manifest](#favicons-und-das-web-manifest)
4. [Meta-Tags](#meta-tags)
5. [Fußbereich und „Powered by“](#fußbereich-und-powered-by)
6. [Checkliste](#checkliste)

## Website-Name und Beschreibung

In `site.ini [SiteSettings]` jedes öffentlichen Siteaccess:

```ini
# settings/siteaccess/<site>/site.ini.append.php
[SiteSettings]
SiteName=Example Inc.
SiteURL=www.example.com
SiteDescription=Example Inc. builds bridges.
MetaDataArray[author]=Example Inc.
```

| Schlüssel | Verwendet für |
|---|---|
| `SiteName` | Titel (`$site.title`), Mail-Betreffzeilen, `og:site_name` im Theme simple |
| `SiteURL` | Links in Mails, Feeds und Sitemaps; ohne Schema, ohne abschließenden Schrägstrich |
| `SiteDescription` | das Meta-Tag description in `page_head.tpl` des Themes simple |
| `MetaDataArray[]` | Meta-Tags, die das Pagelayout ausgibt |

Leeren Sie danach den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`. Mehrere Sprachen? Setzen
Sie den Namen pro Sprach-Siteaccess ([Eine mehrsprachige Website bauen](mehrsprachige-websites.md)).

## Das Logo und das Seitendesign-Objekt

Die auf ezwebin basierenden Designs, einschließlich des mitgelieferten Themes simple, lesen das Logo aus einem
**Seitendesign**-Objekt (Klassen-Bezeichner `template_look`, im Pagelayout gefunden mit
`fetch( 'content', 'class', hash( 'class_id', 'template_look' ) )`). Die Redaktion ändert das Logo, indem sie dieses
Objekt in der Administration bearbeitet; sein Bildfeld enthält das Logo mit seinem Alternativtext. Ohne Bild greift das
Theme auf `images/logo.png` des Designs zurück (`{'logo.png'|ezimage}`).

Für Ihr eigenes Design haben Sie die Wahl: eine Logodatei in `images/` Ihrer Design-Erweiterung (am einfachsten) oder
ein Inhaltsobjekt, damit die Redaktion es ändern kann.

## Favicons und das Web-Manifest

Das Theme simple verlinkt seine Favicons in `link.tpl`, aus `images/favicon/` des Designs:

```text
<link rel="icon" type="image/png" sizes="32x32" href="{"favicon/favicon-32x32.png"|ezimage(no)}">
<link rel="apple-touch-icon" sizes="180x180" href="{"favicon/apple-icon-180x180.png"|ezimage(no)}">
<link rel="manifest" href="{"images/favicon/site.webmanifest.json"|ezdesign(no)}">
```

Um sie zu ersetzen, legen Sie Dateien mit denselben Namen in `images/favicon/` Ihrer eigenen Design-Erweiterung ab (die
vor dem Theme aufgeführt ist): Die Design-Kette findet Ihre zuerst
([Designs und die Fallback-Kette](designs-und-die-fallback-kette.md)). Ein `favicon.ico` im Wurzelverzeichnis der
Website wird aus dem Wurzelverzeichnis der Installation ausgeliefert.

## Meta-Tags

| Tag | Woher es im Theme simple kommt |
|---|---|
| `<title>` | `$site.title`, gebildet aus der Seite und `SiteName` |
| `description` | `SiteDescription` |
| `og:title`, `twitter:title` | der Seitentitel |
| `og:site_name` | `SiteName` |
| `og:image`, `twitter:image` | `images/favicon/favicon-banner.png` des Designs |

Beschreibungen und Schlüsselwörter pro Seite brauchen ein Metadatenfeld am Inhalt; siehe
[Metadaten und Teilen in sozialen Medien](metadaten-und-teilen-in-sozialen-medien.md).

## Fußbereich und „Powered by“

Das Theme simple gibt in `page_footer.tpl` einen Hinweis „Powered by Exponential“ aus, es sei denn, das Attribut
**Hide powered by** (`hide_powered_by`) des Seitendesign-Objekts ist gesetzt; bearbeiten Sie dieses Objekt, eine
Template-Änderung ist nicht nötig ([sevenx_themes_simple](../../../funktionen/6.0/erweiterungen/sevenx_themes_simple.md)).
Den Hinweis stehen zu lassen ist eine freundliche Art, das Projekt zu unterstützen.

## Checkliste

- [ ] `SiteName`, `SiteURL` und `SiteDescription` für jeden öffentlichen Siteaccess und jede Sprache gesetzt.
- [ ] Das Logo hat einen aussagekräftigen Alternativtext.
- [ ] Favicons und Manifest in Ihrer Design-Erweiterung ersetzt, nicht im Theme.
- [ ] Das Bild für das Teilen in sozialen Medien zeigt Ihre Marke.
- [ ] Mails der Website tragen den Website-Namen
  ([Benachrichtigungen und Mail](../anwendungsentwickler/benachrichtigungen-und-mail.md)).

## Verwandte Seiten

- [Ein Design oder Theme wählen](ein-design-oder-theme-waehlen.md)
- [Marken- und Namensregeln](../../geschaeft/marken-und-namensregeln.md)
- [SEO-Grundlagen](seo-grundlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
