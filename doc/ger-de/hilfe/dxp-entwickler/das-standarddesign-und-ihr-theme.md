---
title: Das Standarddesign und Ihr Theme
audience: dxp-developer
category: dxp-development
order: 20
summary: Was ibexa/standard-design wirklich liefert (das Design standard und die Standard-View-Templates), woher die Templates des Themes standard kommen und wie Sie Ihr eigenes Theme darauf bauen - Pagelayout, Full und Line Views, Theme-Assets und die Reihenfolge der Themes.
keywords: Standarddesign, ibexa/standard-design, Theme, Pagelayout, viewbase_layout, Full View, Line View, Embed, Standard-Templates, Theme-Assets, assets/themes, Override, standard design, theme, default templates, theme assets
type: help
---
# Das Standarddesign und Ihr Theme

Diese Seite richtet sich an Entwickler und Theme-Bauer, die einer DXP-Website ihr eigenes Aussehen geben. Das Skeleton
hat "kein Demo-Design" ([DXP-Skeleton](../../funktionen/6.0/plattform-dxp-skeleton.md#was-es-ist)), also beginnt jede
Website mit dem Standarddesign und ergänzt ein Theme.

## Inhalt

1. [Was ibexa/standard-design liefert](#was-ibexastandard-design-liefert)
2. [Woher die Templates des Themes standard kommen](#woher-die-templates-des-themes-standard-kommen)
3. [Ihr Theme bauen](#ihr-theme-bauen)
4. [Theme-Assets](#theme-assets)
5. [Eine Checkliste für Themes](#eine-checkliste-für-themes)

## Was ibexa/standard-design liefert

In einem installierten Projekt enthält das Paket nur Konfiguration:

- es definiert das Design `standard` mit dem einzigen Theme `standard`;
- es lässt die Standard-Templates auf `@ibexadesign` zeigen, sodass jedes Theme sie ersetzen kann:

| Standard | Template |
|---|---|
| Pagelayout | `@ibexadesign/pagelayout.html.twig` |
| Basislayout der Inhaltsansichten | `@ibexadesign/viewbase_layout.html.twig` |
| Ansichten full, line, embed, embed image und linked text | `@ibexadesign/default/content/<view>.html.twig` |
| Ausgabe von Feldern | `@ibexadesign/content_fields.html.twig` |
| Einstellungen von Felddefinitionen | `@ibexadesign/fielddefinition_settings.html.twig` |

## Woher die Templates des Themes standard kommen

Bundles liefern Templates für das Theme `standard` in ihren Verzeichnissen `Resources/views/themes/standard/` mit
(darunter die Pakete für Rich Text, Matrix, Suche, Messenger, Benachrichtigungen und Twig-Komponenten). Die
Design-Engine sammelt die `themes/`-Verzeichnisse jedes Bundles und `templates/themes/` Ihres Projekts zu einer
Zuordnung je Theme.

## Ihr Theme bauen

1. Legen Sie das Theme-Verzeichnis an: `templates/themes/my_theme/`.
2. Definieren Sie ein Design, das Ihr Theme vor `standard` setzt:

   ```yaml
   ibexa_design_engine:
       design_list:
           my_design: [my_theme, standard]
   ```

3. Geben Sie den Website-Siteaccesses dieses Design (`ibexa.system.site_group.design: my_design`).
4. Schreiben Sie `templates/themes/my_theme/pagelayout.html.twig` mit Ihrem HTML-Gerüst und einem Block für den
   Inhalt.
5. Fügen Sie View-Templates für Ihre Inhaltstypen hinzu (zum Beispiel `full/article.html.twig`) und ordnen Sie sie in
   der Konfiguration der Inhaltsansichten Ihrer Siteaccess-Gruppe dem Inhaltstyp zu (die Einstellungen `content_view`
   des Core).
6. Leeren Sie den Cache und öffnen Sie eine Seite.

Alles, was Ihr Theme nicht definiert, fällt auf `standard` zurück.

## Theme-Assets

Die Design-Engine löst auch Assets je Theme auf: Sie sucht auf Anwendungsebene in `public/assets/themes/<theme>/` und
in den öffentlichen `themes/`-Verzeichnissen der Bundles. Die meisten Projekte bauen ihre Stylesheets und Skripte
stattdessen mit Webpack Encore und binden sie mit den Twig-Funktionen von Encore ein
([Assets bauen](assets-mit-yarn-und-encore-bauen.md)).

## Eine Checkliste für Themes

- Templates verweisen auf `@ibexadesign/...`, nie auf einen Theme-Pfad.
- Das Pagelayout hat Landmarks, einen Sprunglink, ein Sprachattribut und einen sichtbaren Fokusstil
  ([Grundlagen der Barrierefreiheit für Themes](../designer/theme-design/barrierefreiheit-grundlagen-fuer-themes.md)).
- Bilder verwenden Variationen, keine Originale ([Bildvariationen](bildvariationen.md)).
- Keine Inline-Skripte oder -Styles, damit eine Content Security Policy streng bleiben kann
  ([CSP-sicheres Design](../designer/theme-design/csp-sicheres-design.md)).
- Helles und dunkles Farbschema sind getestet, wenn die Website beide anbietet.

## Verwandte Seiten

- [Twig-Templates und die Design-Engine](twig-templates-und-die-design-engine.md)
- [Twig-Funktionen für den Alltag](twig-funktionen-fuer-den-alltag.md)
- [Designer: hier beginnen](../designer/designer-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
