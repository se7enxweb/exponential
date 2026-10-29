---
title: "Themes für Exponential Platform"
audience: theme-designer
category: theme-design
order: 35
summary: Eine kurze Orientierung für Theme-Designer, die für Exponential Platform DXP gestalten sollen, die Symfony-Linie - Twig statt TPL, Designs als geordnete Theme-Listen, der Namensraum @ibexadesign, Ordner templates/themes, was aus diesem Bereich übertragbar ist und wo die DXP-Seiten weitergehen.
keywords: Theme-Designer, Exponential Platform, DXP, Twig, Design-Engine, ibexa/design-engine, ibexadesign, Standarddesign, Themes, Symfony, Encore, theme designer, design engine, standard design, themes
type: help
---
# Themes für Exponential Platform

Diese Seite ist für Theme-Designer, die für **Exponential Platform DXP** gestalten sollen, die auf Symfony beruhende
Linie der Produktfamilie, statt für Exponential 6 (den PHP-Kernel, um den es in diesem Bereich geht). Die Designarbeit
ist weitgehend dieselbe; die Template-Sprache und die Dateiablage sind es nicht. Diese Seite nennt die Unterschiede in
Kürze und verweist auf die Seiten für DXP-Entwickler, die sie gründlich behandeln.

## Inhalt

1. [Für welches Produkt gestalten Sie](#für-welches-produkt-gestalten-sie)
2. [Dieselbe Idee, andere Worte](#dieselbe-idee-andere-worte)
3. [Wo Theme-Dateien liegen](#wo-theme-dateien-liegen)
4. [Stylesheets und Skripte](#stylesheets-und-skripte)
5. [Was aus diesem Bereich übertragbar ist](#was-aus-diesem-bereich-übertragbar-ist)
6. [Wo es weitergeht](#wo-es-weitergeht)

## Für welches Produkt gestalten Sie

Fragen Sie zuerst. Eine Website auf Exponential 6 hat `.tpl`-Templates, `design/`-Ordner in Erweiterungen und
`settings/*.ini`-Dateien. Eine Website auf der DXP hat `.html.twig`-Templates, einen Ordner `templates/themes/` und
YAML-Konfiguration unter `config/`. [DXP, Legacy, Nexus oder Exponential 6 wählen](../dxp-entwickler/dxp-legacy-nexus-oder-exponential-6-waehlen.md)
erklärt die Produktlinien.

## Dieselbe Idee, andere Worte

| Exponential 6 | Exponential Platform DXP |
|---|---|
| ein Design (Ordner mit Templates) | ein Theme (`templates/themes/<theme>/`) |
| die Design-Kaskade eines Siteaccess | ein Design: eine geordnete Liste von Themes, der erste Treffer gewinnt |
| `standard` am Ende der Kaskade | das Design und Theme `standard`, bereitgestellt von `ibexa/standard-design` |
| TPL-Templates, `{include uri='design:...'}` | Twig-Templates, `{% include '@ibexadesign/...' %}` |
| `pagelayout.tpl` | `pagelayout.html.twig` |
| `node/view/full.tpl` und Overrides nach Klasse | Templates für Voll- und Zeilenansicht, in der Konfiguration der Inhaltsansichten Inhaltstypen zugeordnet |
| Bild-Aliase in `image.ini` | Bildvarianten ([Bildvarianten](../dxp-entwickler/bildvariationen.md)) |

Die Design-Engine der DXP (`ibexa/design-engine`) löst jeden Namen `@ibexadesign/...` über das Design des aktuellen
Siteaccess und dessen Reihenfolge der Themes auf, ganz ähnlich wie die Kaskade. Templates verweisen immer auf
`@ibexadesign/...`, nie auf den Ordnerpfad eines Themes
([Twig-Templates und die Design-Engine](../dxp-entwickler/twig-templates-und-die-design-engine.md)).

## Wo Theme-Dateien liegen

Ein Projekt-Theme ist ein Ordner unter `templates/themes/`, und ein Design, das es vor `standard` aufführt, wird unter
`ibexa_design_engine.design_list` konfiguriert; eine Siteaccess-Gruppe verwendet dann dieses Design. Auch Bundles können
Themes mitliefern. Die Schritte samt Konfiguration stehen in
[Das Standarddesign und Ihr Theme](../dxp-entwickler/das-standarddesign-und-ihr-theme.md#ihr-theme-bauen).

## Stylesheets und Skripte

Die DXP hat keinen ezjscore-Packer. Die meisten Projekte bauen ihre Stylesheets und Skripte mit Webpack Encore und binden
sie mit den Twig-Funktionen von Encore ein; die Design-Engine kann Theme-Assets auch aus `public/assets/themes/<theme>/`
auflösen ([Assets mit Yarn und Encore bauen](../dxp-entwickler/assets-mit-yarn-und-encore-bauen.md),
[Das Standarddesign und Ihr Theme](../dxp-entwickler/das-standarddesign-und-ihr-theme.md#theme-assets)).
Die Designprinzipien bleiben dieselben: keine Inline-Skripte oder -Stile, damit eine Content Security Policy streng
bleiben kann.

## Was aus diesem Bereich übertragbar ist

Fast die gesamte Designarbeit in diesem Bereich ist produktneutral:

- der Ablauf: [Vom Mockup zum Theme](vom-mockup-zum-theme.md), [Mit Entwicklern arbeiten](mit-entwicklern-arbeiten.md),
  [Mit Kunden und Reviews arbeiten](mit-kunden-und-reviews-arbeiten.md);
- Grundlagen: [Typografische Skala](typografische-skala.md), [Abstände und Raster](abstaende-und-raster.md),
  [Farbpaletten und Farbabstufungen](farbpaletten-und-farbabstufungen.md), [Von Designtool-Variablen zu CSS Custom Properties](von-designtool-variablen-zu-css-custom-properties.md);
- Design der Inhalte: [Ein Design auf Inhaltsklassen abbilden](ein-design-auf-inhaltsklassen-abbilden.md) (auf der DXP
  Inhaltstypen und Felder), [Ansichtsmodi gestalten](ansichtsmodi-gestalten.md), [Für Redakteure gestalten](fuer-redakteure-gestalten.md);
- Qualität: die [Checkliste zur Design-Qualitätssicherung](checkliste-design-qualitaetssicherung.md), Barrierefreiheit und Dunkelmodus.

Was nicht übertragbar ist: die TPL-Syntax, `override.ini`, `design.ini`, der Packer und alles in der Hilfe für Designer
über die Einstellungsdateien von Exponential 6.

## Wo es weitergeht

- [DXP-Entwickler: hier beginnen](../dxp-entwickler/dxp-entwickler-hier-beginnen.md)
- [Twig-Templates und die Design-Engine](../dxp-entwickler/twig-templates-und-die-design-engine.md)
- [Das Standarddesign und Ihr Theme](../dxp-entwickler/das-standarddesign-und-ihr-theme.md)
- [Twig-Funktionen für jeden Tag](../dxp-entwickler/twig-funktionen-fuer-den-alltag.md)

## Verwandte Seiten

- [Was Exponential Platform DXP ist](../dxp-entwickler/was-exponential-platform-dxp-ist.md)
- [Wie sich Exponential-Themes unterscheiden](wie-sich-exponential-themes-unterscheiden.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
