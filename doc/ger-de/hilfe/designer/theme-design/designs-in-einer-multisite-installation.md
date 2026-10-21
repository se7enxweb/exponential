---
title: Designs in einer Multisite-Installation
audience: designer, admin
category: theme-design
order: 180
summary: Muster für mehrere Websites in einer Installation: ein gemeinsames Design mit einem kleinen Marken-Design pro Website, ein Design pro Website, Sprach-Siteaccesses, die ein Design teilen, und Website-Erweiterungen, die ihre eigenen Einstellungen tragen.
keywords: Multisite, mehrere Websites, Marken-Design, gemeinsames Design, Design pro Website, Website-Erweiterung, Sprach-Siteaccess, ExtensionSettingsSiteAccess, Einstellungs-Override in einer Erweiterung, Design pro Marke, multisite, multi-site, several sites, brand design, shared design, per site design, site extension, language siteaccess, design per brand
type: help
---
# Designs in einer Multisite-Installation

Diese Seite ist für Designer, die mehrere Websites gestalten, die aus einer Installation laufen: Marken eines Unternehmens, eine
Website pro Land, eine Kampagnen-Website neben der Hauptwebsite. Sie beantwortet: wie Sie Gemeinsames teilen, Unterschiedliches
getrennt halten und das Kopieren von Templates vermeiden.

## Inhalt

1. [Drei Muster](#drei-muster)
2. [Muster A: gemeinsames Design plus eine Markenschicht](#muster-a-gemeinsames-design-plus-eine-markenschicht)
3. [Muster B: ein Design pro Website](#muster-b-ein-design-pro-website)
4. [Muster C: Sprach-Siteaccesses einer Website](#muster-c-sprach-siteaccesses-einer-website)
5. [Websites, die in einer eigenen Erweiterung leben](#websites-die-in-einer-eigenen-erweiterung-leben)
6. [Stolperfallen](#stolperfallen)

## Drei Muster

| Muster | Wann | Aufwand |
|---|---|---|
| A: gemeinsames Design + Markenschicht | die Websites teilen die Struktur und unterscheiden sich in Farbe, Logo, Schriften | am geringsten: ein Satz Templates |
| B: ein Design pro Website | die Websites sehen anders aus und verhalten sich anders | ein Satz Templates pro Website |
| C: Sprach-Siteaccesses einer Website | dieselbe Website in mehreren Sprachen | keiner: nur Einstellungen |

## Muster A: gemeinsames Design plus eine Markenschicht

Legen Sie jedes Template in ein gemeinsames Design, etwa `corporate`. Geben Sie jeder Website ein winziges eigenes Design, das nur
enthält, was sich unterscheidet: ein Stylesheet mit den Token der Marke, das Logobild, vielleicht das Template des Fußbereichs.

```
extension/corporate_theme/design/corporate/      templates, base CSS, scripts
extension/corporate_theme/design/brand_north/    stylesheets/brand.css, images/logo.svg
extension/corporate_theme/design/brand_south/    stylesheets/brand.css, images/logo.svg
```

```ini
# settings/siteaccess/north/site.ini.append.php
[DesignSettings]
SiteDesign=brand_north
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=corporate
AdditionalSiteDesignList[]=standard
```

Das gemeinsame Pagelayout lädt `brand.css` nach seinem Basis-Stylesheet; die Kaskade findet für jede Website die richtige
`brand.css`, weil jedes Marken-Design zuerst durchsucht wird. Mit Design-Token kann die Markendatei ein Dutzend Zeilen lang sein
([Design-Token](design-tokens.md)):

```css
/* brand_north/stylesheets/brand.css */
:root {
  --site-accent: #0b5cad;
  --site-font: "Source Sans 3", system-ui, sans-serif;
}
```

## Muster B: ein Design pro Website

Jede Website bekommt ein vollständiges Design, möglicherweise in einer eigenen Erweiterung. Gemeinsame Teile (ein Cookie-Banner, ein
Suchformular) können trotzdem in einem gemeinsamen Design leben, das in der `AdditionalSiteDesignList[]` jeder Website steht.

## Muster C: Sprach-Siteaccesses einer Website

`site_eng` und `site_ger` zeigen dieselbe Website in zwei Sprachen. Sie verwenden dasselbe `SiteDesign`; nur `[RegionalSettings]`
unterscheidet sich. Um das Kopieren der Siteaccess-Einstellungen des Themes zu vermeiden, nennt der zweite Siteaccess den ersten in
`site.ini [SiteAccessSettings] ExtensionSettingsSiteAccess` (der Siteaccess `starter_ger` der Demonstrationsinstallation tut das mit
`cjw_starter`). Siehe [Multisite- und mehrsprachige Projekte planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md).

## Websites, die in einer eigenen Erweiterung leben

Seit Januar 2026 kann eine Erweiterung, die eine ganze Website enthält, Einstellungen mitbringen, die für alle ihre Siteaccesses
gelten, in `extension/<site extension>/settings/override/`, und für eine Gruppe davon in `settings/override__<group>/`; diese werden
nur gelesen, während einer der Siteaccesses der Erweiterung läuft
([Einstellungen pro Website in Erweiterungen](../../../../eng-us/features/6.0/multi-site-ini-overrides.md)). Die Design-Zeilen einer Website
können dort liegen:

```ini
# extension/site_example/settings/override/site.ini.append.php
<?php /*
[DesignSettings]
SiteDesign=example
*/ ?>
```

## Stolperfallen

- **Ein Design-Ordner `standard` in einer Erweiterung** wird von jedem Siteaccess durchsucht, dessen Kette auf `standard` endet,
  einschließlich anderer Marken und der Administration. Halten Sie Marken-Templates in Designs mit Markennamen.
- **Caches gelten pro Siteaccess.** Eine Änderung am Template einer Marke braucht das Leeren ihrer Caches; führen Sie verwandte
  Siteaccesses in `RelatedSiteAccessList[]` auf, damit Inhaltsänderungen den View-Cache aller leeren.
- **Bilder und Links in gemeinsamen Templates.** Verwenden Sie `ezimage`, `ezdesign` und `ezurl`, nie Pfade mit einem Design- oder
  Siteaccess-Namen darin, damit dasselbe Template für jede Website funktioniert.
- **Overrides nach Knoten-id** unterscheiden sich pro Website-Baum. Ziehen Sie Klassenbezeichner, Sektionen oder Remote-ids vor
  ([Match-Schlüssel der Overrides](../templates/override-match-schluessel.md)).

## Verwandte Seiten

- [Designs und Siteaccesses](designs-und-siteaccesses.md)
- [Tutorial: zwei Websites, zwei Designs](../../../../eng-us/help/designers/theme-design/tutorial-two-sites-two-designs.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
