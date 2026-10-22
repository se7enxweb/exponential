---
title: Twig-Templates und die Design-Engine
audience: dxp-developer
category: dxp-development
order: 19
summary: Wie die DXP mit Twig ausgibt - Designs als geordnete Listen von Themes, der Namensraum @ibexadesign mit Rückfall, Theme-Verzeichnisse unter templates/themes, Override-Pfade, die Konfigurationsschlüssel von ibexa/design-engine und wie ein Siteaccess sein Design wählt.
keywords: Twig, Design-Engine, ibexa/design-engine, @ibexadesign, design_list, templates_theme_paths, templates_override_paths, Themes, Rückfall, Design, Siteaccess, templates/themes, design engine, themes, fallback
type: help
---
# Twig-Templates und die Design-Engine

Diese Seite richtet sich an Entwickler und Theme-Bauer, die eine DXP-Website ausgeben. Die DXP gibt mit Twig aus, und
das Paket `ibexa/design-engine` ergänzt Designs und Themes mit einer Rückfallkette, ganz ähnlich wie die Design-Kette
von Exponential 6. Die Konfigurationsschlüssel unten wurden in einem installierten Projekt aus der Klasse
`Configuration` des Pakets gelesen.

## Inhalt

1. [Designs und Themes](#designs-und-themes)
2. [Der Namensraum @ibexadesign](#der-namensraum-ibexadesign)
3. [Wo Theme-Templates liegen](#wo-theme-templates-liegen)
4. [Konfigurationsschlüssel](#konfigurationsschlüssel)
5. [Einem Siteaccess sein Design geben](#einem-siteaccess-sein-design-geben)
6. [Herausfinden, welches Template verwendet wurde](#herausfinden-welches-template-verwendet-wurde)

## Designs und Themes

Die eigene Beschreibung des Pakets: "A design is a labeled collection of themes. Theme order defines the fallback
order." Ein Design `my_design` mit den Themes `[my_theme, standard]` sucht ein Template zuerst in `my_theme` und fällt
auf `standard` zurück. Das Design `standard` ist immer definiert; `ibexa/standard-design` liefert seine Templates
([Das Standarddesign und Ihr Theme](das-standarddesign-und-ihr-theme.md)).

## Der Namensraum @ibexadesign

Verweisen Sie auf Theme-Templates über den Twig-Namensraum `@ibexadesign`, nie über den Pfad eines Themes:

```twig
{% extends '@ibexadesign/pagelayout.html.twig' %}
{% include '@ibexadesign/parts/header.html.twig' %}
```

Die Engine löst den Namen über das Design des aktuellen Siteaccess und seine Theme-Reihenfolge auf. Das README des
Skeletons erwähnt einen Namensraum `@ezdesign`; auf Platform v5 registriert die installierte Engine `@ibexadesign`.

## Wo Theme-Templates liegen

Üblicherweise ist jedes Theme ein Verzeichnis unter `templates/themes/`:

```text
templates/themes/
  my_theme/
    pagelayout.html.twig
    full/article.html.twig
    line/article.html.twig
```

Auch Bundles können Themes mitliefern, in ihrem `Resources/views/themes/<theme>/`.

## Konfigurationsschlüssel

Unter `ibexa_design_engine` in `config/packages/`:

| Schlüssel | Bedeutung |
|---|---|
| `design_list` | Designs und ihre Themes in Rückfallreihenfolge, zum Beispiel `my_design: [my_theme, standard]` |
| `templates_theme_paths` | zusätzliche Template-Verzeichnisse je Theme ("Each path MUST exist") |
| `templates_override_paths` | Verzeichnisse, die vor jedem Theme-Verzeichnis geprüft werden |
| `disable_assets_pre_resolution` | wenn true, werden Asset-Pfade nicht zur Kompilierzeit vorab aufgelöst (Standard ist die Debug-Einstellung) |

```yaml
ibexa_design_engine:
    design_list:
        my_design: [my_theme, standard]
```

## Einem Siteaccess sein Design geben

```yaml
ibexa:
    system:
        site_group:
            design: my_design
```

Die Administrations-Siteaccesses verwenden ihr eigenes Design; lassen Sie es, wie es installiert ist.

## Herausfinden, welches Template verwendet wurde

- In `APP_ENV=dev` listet der Web Profiler von Symfony die gerenderten Templates, und die Design-Engine ergänzt ihn um
  einen Data Collector.
- `php bin/console debug:twig` listet Twig-Funktionen, -Filter und -Namensräume.
- `php bin/console exponential:debug:config-resolver design --siteaccess=site` zeigt das Design, das ein Siteaccess
  verwendet.

## Verwandte Seiten

- [Das Standarddesign und Ihr Theme](das-standarddesign-und-ihr-theme.md)
- [Twig-Funktionen für den Alltag](twig-funktionen-fuer-den-alltag.md)
- [Die Design-Kette in Exponential 6](../erweiterungsentwicklung/teil-2-builder/die-design-kette.md), zum Vergleich
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
