---
title: Ihre Blöcke gestalten
audience: designer, developer
category: build-two-ways
order: 560
summary: Wohin im Theme Fit & Healthy das CSS für Ihre eigenen Blöcke, Ansichtstypen und Template-Blöcke gehört (seine Stylesheet-Liste steht in pagelayout/head/style.tpl, nicht in design.ini) und wie Sie Ihre Datei hinzufügen, damit sie mit denen des Themes gepackt wird. Vorher und nachher getestet.
keywords: CSS, Stylesheet, Gestaltung, ezcss_load, style.tpl, CSSFileList, Packer, ezjscore, mysite.css, Design-Stylesheets, Block-Styles, stylesheet, styling, packer, design stylesheets, block styles
type: help
---
# Ihre Blöcke gestalten

Neues Markup braucht CSS. Im Theme Fit & Healthy liegt der Ort dafür nicht dort, wo Sie zuerst suchen würden.

## Inhalt

1. [Wo das Theme seine Stylesheets listet](#wo-das-theme-seine-stylesheets-listet)
2. [Ihr Stylesheet hinzufügen](#ihr-stylesheet-hinzufügen)
3. [Prüfen](#prüfen)
4. [Regeln für das CSS](#regeln-für-das-css)

## Wo das Theme seine Stylesheets listet

Das Media-Design lädt sein CSS in `pagelayout/head/style.tpl` mit einem einzigen Aufruf:

```smarty
{ezcss_load( array( 'inter.css',
                    'explayouts_standard/grid.css',
                    'index.css',
                    ...
                    'exp-mobile-layout.css' ) )}
```

`ezcss_load` (ezjscore) packt die Dateien in eine einzige Datei unter `var/<site>/cache/public/stylesheets/` und verlinkt sie.
Die Pfade werden über den Design-Fallback aufgelöst, `mysite.css` wird also in
`extension/mysite/design/media/stylesheets/mysite.css` gefunden.

Auf der Testkopie hat `CSSFileList[]=mysite.css` in `[StylesheetSettings]` der `design.ini` (wie es die eigene
`design.ini.append.php` des Themes für seine Dateien macht) die Datei **nicht** in das gepackte CSS der Seite gebracht:
Das Seitenlayout dieses Themes liest diese Liste nicht.

## Ihr Stylesheet hinzufügen

1. Schreiben Sie `extension/mysite/design/media/stylesheets/mysite.css`, zum Beispiel:

   ```css
   .latest-fitness ul { list-style: none; padding: 0; }
   .latest-fitness li { padding: 0.5rem 0; border-bottom: 1px solid #e5e5e5; }
   .list-compact { list-style: none; padding: 0; }
   .notice { padding: 1rem 1.25rem; border-left: 4px solid #2563eb; background: #eff6ff; }
   .notice-warning { border-left-color: #b45309; background: #fffbeb; }
   ```

2. Kopieren Sie `pagelayout/head/style.tpl` aus dem Theme nach `extension/mysite/design/media/templates/pagelayout/head/style.tpl`
   und fügen Sie Ihre Datei am Ende der Liste hinzu:

   ```smarty
                       'exp-mobile-layout.css',
                       'mysite.css' ) )}
   ```

3. Leeren Sie **Template & Inhalt** (`./console exp:cache clear --tag=template,content`): Das leert auch die gepackten Dateien
   (`ezjscore-packer`).

## Prüfen

Der Name der gepackten Datei ändert sich, und sie enthält Ihre Regeln:

```text
$ grep -o "latest-fitness[^}]*}" var/site/cache/public/stylesheets/<hash>_all.css
latest-fitness ul { list-style:none;padding:0}
latest-fitness li { padding:0.5rem 0;border-bottom:1px solid #e5e5e5}
```

Vorher und nachher, der Template-Block *Latest in Fitness*:

![Die Liste Latest in Fitness mit Aufzählungszeichen, vor dem Stylesheet](../../../eng-us/help/build-two-ways/images/tb-03-latest-fitness-on-site.webp)

![Dieselbe Liste ohne Aufzählungszeichen und mit einer Linie zwischen den Einträgen](../../../eng-us/help/build-two-ways/images/st-01-latest-fitness-styled.webp)

Ein Browser, der noch die alten Styles zeigt, hat die alte Datei im Cache: Laden Sie ohne Cache neu.

## Regeln für das CSS

- Bei gleicher Spezifität gewinnt das Letzte in der Liste: Ihre Datei nach der des Themes erlaubt es, das Theme ohne
  `!important` anzupassen.
- Verwenden Sie die Klassen, die der Wrapper des Blocks Ihnen gibt: `ngl-block`, `ngl-<block type>`, `ngl-vt-<view type>` und die CSS-Klasse,
  die ein Redakteur im Reiter **Design** setzt.
- Halten Sie das Markup frei von `style=""`; die Styles gehören ins Stylesheet, damit sie gepackt und gecacht werden.

## Verwandte Seiten

- [Einen Blocktyp hinzufügen](../../../eng-us/help/build-two-ways/add-a-block-type.md)
- [Einen Ansichtstyp hinzufügen](ansichtstyp-hinzufuegen.md)
- [Ihre eigene Design-Erweiterung](../../../eng-us/help/build-two-ways/your-own-design-extension.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
