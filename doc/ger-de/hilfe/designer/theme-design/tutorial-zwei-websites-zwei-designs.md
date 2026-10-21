---
title: "Tutorial: zwei Websites, zwei Designs"
audience: designer, admin
category: theme-design
order: 840
summary: Schritt für Schritt zwei Marken-Websites aus einer Installation betreiben, die ihre Templates teilen: ein gemeinsames Design, ein kleines Marken-Design pro Website, die Design-Ketten der Siteaccesses, Marken-Token und Logos und die Prüfungen, die die Marken getrennt halten.
keywords: Tutorial, zwei Websites, Multisite, Marken-Design, gemeinsame Templates, Design-Kette eines Siteaccess, Marken-Token, zwei Marken, Untermarke, Design pro Website, tutorial, two sites, multisite, brand design, shared templates, siteaccess design chain, brand tokens, two brands, sub brand, design per site
type: help
---
# Tutorial: zwei Websites, zwei Designs

Dieses Tutorial ist für Designer, die zwei Marken gestalten, die sich eine Installation und dieselbe Seitenstruktur teilen, zum Beispiel
„North“ und „South“. Sie bauen Muster A aus [Designs in einer Multisite-Installation](designs-in-einer-multisite-installation.md): ein gemeinsames Design und eine
kleine Markenschicht pro Website. Planen Sie zwei Stunden ein.

## Inhalt

1. [Schritt 1: das gemeinsame Design](#schritt-1-das-gemeinsame-design)
2. [Schritt 2: zwei Marken-Designs](#schritt-2-zwei-marken-designs)
3. [Schritt 3: die Design-Ketten](#schritt-3-die-design-ketten)
4. [Schritt 4: das Marken-Stylesheet laden](#schritt-4-das-marken-stylesheet-laden)
5. [Schritt 5: beide prüfen](#schritt-5-beide-prüfen)

## Schritt 1: das gemeinsame Design

Ihre Theme-Erweiterung hat das Design `corporate` mit allen Templates, dem Basis-Stylesheet und den Skripten, auf Token aufgebaut
([Design-Token](design-tokens.md)).

## Schritt 2: zwei Marken-Designs

In derselben Erweiterung:

```
extension/corporate_theme/design/brand_north/stylesheets/brand.css
extension/corporate_theme/design/brand_north/images/logo.svg
extension/corporate_theme/design/brand_south/stylesheets/brand.css
extension/corporate_theme/design/brand_south/images/logo.svg
```

`brand_north/stylesheets/brand.css`:

```css
:root {
  --site-accent: #0b5cad;
  --site-font: "Source Sans 3", system-ui, sans-serif;
}
:root[data-theme="dark"] { --site-accent: #7fb3f0; }
```

`brand_south/stylesheets/brand.css` hat eigene Werte für dieselben Token und keine weiteren Regeln.

## Schritt 3: die Design-Ketten

```ini
# settings/siteaccess/north/site.ini.append.php
[DesignSettings]
SiteDesign=brand_north
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=corporate
AdditionalSiteDesignList[]=standard
```

und dasselbe für `south` mit `brand_south`. Die Erweiterung wird einmal mit `DesignExtensions[]` registriert; alle drei Ordner unter ihrem
`design/` werden zu Design-Basen.

## Schritt 4: das Marken-Stylesheet laden

Im gemeinsamen Pagelayout von `corporate`:

```tpl
{ezcss_load( array( 'tokens.css', 'corporate.css', 'brand.css' ) )}
...
<a class="site-logo" href={'/'|ezurl}><img src={'logo.svg'|ezimage} alt="{$site.title|wash}" width="160" height="40"></a>
```

`brand.css` und `logo.svg` werden zuerst im Marken-Design gefunden, weil es in jeder Kette an erster Stelle steht. Hat eine Marke keine
`brand.css`, findet die Kaskade keine, und der Packer überspringt sie mit einer Warnung; geben Sie `corporate` eine Standard-`brand.css`, damit
eine dritte Website mit sinnvollen Werten beginnt.

## Schritt 5: beide prüfen

- [ ] Jede Website zeigt ihre eigenen Farben und ihr Logo, hell und dunkel.
- [ ] Eine Template-Änderung in `corporate` erscheint auf beiden.
- [ ] Caches: Beide Siteaccesses stehen jeweils in der `RelatedSiteAccessList[]` des anderen, sodass Veröffentlichen beide leert.
- [ ] Markenspezifische Templates (falls vorhanden) liegen nur im Marken-Design, nie in `corporate` mit einem `if` auf den Namen des
      Siteaccess.

## Verwandte Seiten

- [Designs in einer Multisite-Installation](designs-in-einer-multisite-installation.md)
- [Die Design-Kaskade](die-design-kaskade.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
