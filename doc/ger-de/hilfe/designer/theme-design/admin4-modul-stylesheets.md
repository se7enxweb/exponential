---
title: admin4-Modul-Stylesheets
audience: designer, developer
category: theme-design
order: 560
summary: Wohin CSS gehört, wenn die gemeinsamen Komponenten nicht reichen: die Modul-Stylesheets des Kernels (setup, content, people, structure, workflow, help), geladen über AdminComponentCSSFileList, und das eigene Admin-Stylesheet einer Erweiterung in BackendCSSFileList, mit Regeln für Namen und Token.
keywords: Modul-Stylesheets, Admin-CSS, AdminComponentCSSFileList, BackendCSSFileList, modules/setup.css, a4-Modulklassen, Admin-CSS einer Erweiterung, Admin-Stylesheet, wohin CSS gehört, module stylesheets, admin css, a4-module classes, extension admin css, admin stylesheet, where css goes
type: help
---
# admin4-Modul-Stylesheets

Diese Seite ist für Designer und Entwickler, die für eine Admin-Seite CSS brauchen, das die [admin4-Komponenten](admin4-komponenten.md)
nicht ausdrücken können. Sie beantwortet: in welche Datei es gehört, wie diese Datei geladen wird und wie Sie die Klassen benennen.

## Inhalt

1. [Seiten des Kernels: die Modul-Stylesheets](#seiten-des-kernels-die-modul-stylesheets)
2. [Seiten einer Erweiterung: Ihr eigenes Stylesheet](#seiten-einer-erweiterung-ihr-eigenes-stylesheet)
3. [Regeln für das CSS selbst](#regeln-für-das-css-selbst)
4. [Nach einer Änderung](#nach-einer-änderung)

## Seiten des Kernels: die Modul-Stylesheets

Die Admin-Seiten des Kernels teilen sich ein Bündel. `settings/design.ini [StylesheetSettings] AdminComponentCSSFileList[]`
listet:

```ini
AdminComponentCSSFileList[]=components.css
AdminComponentCSSFileList[]=modules/setup.css
AdminComponentCSSFileList[]=modules/content.css
AdminComponentCSSFileList[]=modules/people.css
AdminComponentCSSFileList[]=modules/structure.css
AdminComponentCSSFileList[]=modules/workflow.css
AdminComponentCSSFileList[]=modules/help.css
```

| Datei | Für die Module |
|---|---|
| `modules/setup.css` | setup |
| `modules/content.css` | content |
| `modules/people.css` | role, section, state, user, notification, collaboration |
| `modules/structure.css` | class, pdf, rss, package |
| `modules/workflow.css` | workflow, trigger, audit, shop, settings, infocollector, search, url, visual, contentfinder |
| `modules/help.css` | die Hilfeseiten |

Jede liegt in `design/admin4/stylesheets/modules/` und in `design/admin/stylesheets/modules/`. Das `page_head_style.tpl` von
admin4 lädt die Liste nach den eigenen Stylesheets des Designs und vor `BackendCSSFileList`.

## Seiten einer Erweiterung: Ihr eigenes Stylesheet

Eine Erweiterung ergänzt `AdminComponentCSSFileList` **nicht**. Sie hält ihr Stylesheet in ihrem eigenen Admin-Designordner und
fügt es zu `BackendCSSFileList[]` hinzu:

```ini
# extension/myext/settings/design.ini.append.php
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
DesignExtensions[]=myext

[StylesheetSettings]
BackendCSSFileList[]=myext-admin.css
*/ ?>
```

```
extension/myext/design/admin4/stylesheets/myext-admin.css
```

Es wird nach den Komponenten geladen, sodass die Erweiterung bei ihrem eigenen Markup das letzte Wort hat. Siehe
[Tutorial: ein Admin-Modul-Stylesheet hinzufügen](../../../../eng-us/help/designers/theme-design/tutorial-add-an-admin-module-stylesheet.md).

## Regeln für das CSS selbst

- Nur, was die Komponenten nicht können.
- Klassennamen `.a4-<module>-*` für die Moduldateien des Kernels; eine Erweiterung verwendet ein eigenes Präfix.
- Nur Token für Farben, Radius, Abstände, Schriften und z-index; keine fest eingetragenen Werte (der Test prüft die Moduldateien).
- Ein dunkler Wert für jedes Farbtoken, das Sie hinzufügen; normalerweise fügen Sie keines hinzu.
- Logische Eigenschaften (`margin-inline-start`), damit rechts nach links funktioniert.

## Nach einer Änderung

Deployen Sie mit dem Packer-Schritt (`./console exp:velocity deploy --packer`), der den Template-Block-Cache leert, in dem der
gepackte Link steht, und laden Sie die Seite hart neu. Prüfen Sie die Seite hell und dunkel, bei 1440 px und bei 960 px mit
Skalierung 2.

## Verwandte Seiten

- [admin4-Komponenten](admin4-komponenten.md)
- [Der Test der Admin-Gestaltungsregeln](der-test-der-admin-gestaltungsregeln.md)
- [Stylesheets laden](../../../../eng-us/help/designers/theme-design/loading-stylesheets.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
