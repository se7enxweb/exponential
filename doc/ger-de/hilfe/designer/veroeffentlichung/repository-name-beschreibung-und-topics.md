---
title: "Repository-Name, Beschreibung und Topics"
audience: designer, sales-marketing
category: theme-distribution
order: 40
summary: Einen Repository-Namen, eine einzeilige Beschreibung und GitHub-Topics wählen, mit denen Menschen ein Theme finden, mit den Entscheidungen, die die veröffentlichten Exponential-Themes getroffen haben.
keywords: Repository-Name, Beschreibung, Topics, GitHub-Topics, Suche, Auffindbarkeit, Schlüsselwörter, responsive-design, exponential, repository name, description, GitHub topics, search, discoverability, keywords
type: help
---
# Repository-Name, Beschreibung und Topics

Diese Seite ist für Designer, die möchten, dass ihr Theme über die Suche auf GitHub, auf Packagist und im Web gefunden
wird.

## Name

Verwenden Sie den Namen der Erweiterung als Namen des Repositorys (`cjw_themes_jumper`, `sevenx_themes_simple`). Dann
sagen Repository, Ordner und Einstellungen alle dasselbe ([Ihre Theme-Erweiterung benennen](../../../../eng-us/help/designers/extensions/naming-your-theme-extension.md)).

## Beschreibung

Ein Satz: was es ist, wofür, worauf. Echte Beispiele:

| Theme | Beschreibung (GitHub) |
|---|---|
| `sevenx_themes_simple` | „7x Exponential : Default Design Theme : Simple“ |
| `sevenx_themes_media` | „Converted Netgen media-site theme for Exponential Legacy / Exponential 6 - hand-maintained eZ4 templates, media design, template operators and siteaccess settings ...“ (sie fährt mit den Demo-Websites fort, die es bedient) |
| `cjw_themes_jumper` | „Converted Exponential Platform Nexus v2.5+ media-site based design theme for Exponential Legacy / Exponential 6 - hand-maintained eZ4 templates, ... for the CJW JAC Example Startersite demo site.“ |

Die beiden langen zeigen einen Zielkonflikt: vollständig, aber in Suchergebnissen nach etwa einer Zeile abgeschnitten.
Ein gutes Muster: „<Name>: a <kind of> theme for Exponential 6, with <the two features people look for>.“ Verwenden Sie
denselben Satz als `description` in `composer.json`, damit Packagist ihn ebenfalls zeigt.

## Topics

GitHub-Topics sind kleingeschriebene Wörter mit Bindestrichen. Die veröffentlichten Themes verwenden zum Beispiel:

| Theme | Topics |
|---|---|
| `cjw_themes_jumper` | `exponential`, `exponential-layouts`, `responsive`, `responsive-design`, `responsive-layout`, `responsive-theme`, `responsive-ui` |
| `sevenx_themes_media` | `exponential-layouts-design`, `responsive`, `responsive-design`, `responsive-layout` |
| `sevenx_themes_simple` | `responsive-design` |

Wählen Sie Topics, die Menschen eintippen würden: das Produkt (`exponential`), die Art (`theme`, `design`), die
Eigenschaften (`responsive-design`, `accessibility`, `dark-mode`), den Zweck (`blog`, `ecommerce`).

## Schritt für Schritt

1. Schreiben Sie den Satz und fünf bis zehn Topics.
2. Wenden Sie sie an:

   ```bash
   gh repo edit yourvendor/mytheme --description "..." --add-topic exponential --add-topic theme
   ```

3. Setzen Sie denselben Satz in `description` und `keywords` von `composer.json`.
4. Suchen Sie einen Tag später auf GitHub nach `topic:exponential` und prüfen Sie, dass Ihr Theme erscheint.

## Verwandte Seiten

- [Ein README, das erklärt und verkauft](ein-readme-das-erklaert-und-verkauft.md)
- [Die Produktseite eines Themes schreiben](../../../../eng-us/help/designers/sales/product-page.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
