---
title: Der Aufbau einer Design-Erweiterung
audience: designer, developer
category: extension-development
order: 20
summary: Jeder Ordner und jede Datei einer Theme-Erweiterung - Templates, Override-Templates, Stylesheets, Skripte, Bilder, die Einstellungen, die sie registrieren, und ihre Override-Regeln - gezeigt am öffentlichen Theme sevenx_themes_simple.
keywords: Design-Erweiterung, Aufbau, Templates, Override-Templates, Stylesheets, JavaScript, Bilder, Siteaccess-Einstellungen, design extension, anatomy, override templates, javascript, images, design.ini, override.ini, siteaccess settings, pagelayout.tpl
type: help
---
# Der Aufbau einer Design-Erweiterung

Teil 6, Kapitel 2. Dieses Kapitel öffnet ein echtes Theme, se7enxweb/sevenx_themes_simple (GPL 2 oder später, zum
Zeitpunkt des Schreibens Version 1.0.24), und erklärt jeden Ordner und jede Datei. Lesen Sie es mit dem Repository des
Themes daneben.

## Der Design-Ordner

```text
design/simple/
  templates/                 templates found by name through the design chain
    pagelayout.tpl           the frame of every page
    page_head.tpl            <head>: title, meta tags
    page_head_style.tpl      stylesheets
    page_head_script.tpl     scripts
    page_header.tpl          the site header
    page_footer.tpl          the site footer
    node/view/               the kernel's node view templates, replaced
    content/  menu/  parts/  user/  shop/  error/  ...
  override/templates/        templates used through override rules
    full/  line/  embed/  view/
  stylesheets/
  javascript/
  images/
```

### templates/

Ein Template hier ersetzt das Template mit demselben Pfad in Designs weiter unten in der Kette (zum Beispiel im Design
`standard` des Kernels). `pagelayout.tpl` ist das Template, in dem jede Seite gezeichnet wird; das Theme teilt es in
`page_head*.tpl`, `page_header.tpl` und `page_footer.tpl` auf, die es einbindet. Das Aufteilen hält jede Datei kurz und
erlaubt einer Website, nur den Kopfbereich zu überschreiben.

### override/templates/

Templates, die nicht über ihren Namen gefunden, sondern von einer Override-Regel ausgewählt werden: „für einen Knoten der
Klasse `video` in der Vollansicht verwende `full/video.tpl`“. Der Ordnername `override/templates` ist die Konvention, die
die eigenen Designs des Kernels verwenden; die Regel nennt den Unterordner. Siehe
[Template-Overrides](../teil-2-builder/grundlagen-der-template-overrides.md).

### stylesheets/, javascript/, images/

Dateien, die über die Design-Kette von den Template-Funktionen und -Operatoren `ezcss_load`, `ezscript_load` und
`ezimage` gefunden werden. Das Theme hält Bibliotheken Dritter in einem Unterordner `libs/` von `stylesheets/`, jede mit
ihrer eigenen Lizenz (das Kapitel [Ein Theme verteilen](ein-theme-verteilen.md) erklärt, warum).

## Die Einstellungen

### settings/design.ini.append.php

```ini
[ExtensionSettings]
DesignExtensions[]=sevenx_themes_simple

[StylesheetSettings]
CSSFileList[]=websitetoolbar.css
CSSFileList[]=libs/fontawesome/css/all.min.css
CSSFileList[]=magnific-popup.css
CSSFileList[]=main.css
CSSFileList[]=main.res.css

[JavaScriptSettings]
JavaScriptList[]=ezjsc::jquery
JavaScriptList[]=jquery.magnific-popup.js
JavaScriptList[]=main.js
```

(Gegenüber der echten Datei gekürzt.) `DesignExtensions[]` registriert den Ordner `design/` der Erweiterung. Die Listen
der Stylesheets und Skripte sind das, was `page_head_style.tpl` und `page_head_script.tpl` laden. Die `design.ini` des
Kernels kennzeichnet `CSSFileList[]` und `JavaScriptList[]` als veraltet zugunsten von `FrontendCSSFileList[]` und
`FrontendJavaScriptList[]`; die Head-Templates des Themes laden sowohl die alten als auch die neuen Listen, sodass
beides funktioniert. Ein neues Theme sollte die Frontend-Listen verwenden
([Theme-Assets, der Packer und CSP](theme-assets-der-packer-und-csp.md)).

### settings/site.ini.append.php

```ini
[RegionalSettings]
TranslationExtensions[]=sevenx_themes_simple
```

Damit wird der Ordner `translations/` des Themes Teil der Übersetzungen der Website, sodass seine Oberflächentexte
übersetzt werden können.

### settings/siteaccess/ (ein Ordner je Siteaccess)

Das Theme liefert Einstellungen für die Siteaccesses mit, für die es gedacht ist (in diesem Theme `site` und
`sevenx_site_user`), darunter `override.ini.append.php` mit seinen Override-Regeln:

```ini
[full_video]
Source=node/view/full.tpl
MatchFile=full/video.tpl
Subdir=templates
Match[class_identifier]=video
```

Einstellungen im `settings/siteaccess/<name>/` einer Erweiterung gelten nur für den Siteaccess dieses Namens. Für den
Siteaccess muss die Erweiterung außerdem aktiv sein (`ActiveAccessExtensions[]` oder das globale `ActiveExtensions[]`).

## packages/

Die Templates des Themes erwarten Inhaltsklassen wie `article`, `blog_post` oder `event`. Das Theme liefert sie als
Pakete (`.ezpkg`-Dateien) aus, damit eine neue Website sie importieren kann, statt vorauszusetzen, dass sie existieren.
Siehe [Pakete und der Paketersteller](../teil-7-veroeffentlichen/pakete-ezpkg.md).

## Worauf Sie in Ihrem eigenen Theme achten sollten

- **Knoten-IDs in Override-Regeln.** Eine Regel mit `Match[node]=67` funktioniert auf der Website, für die sie
  geschrieben wurde. In einem Theme für andere bevorzugen Sie Klassenbezeichner, Sektionen oder eine Einstellung.
- **Überbleibsel des Editors.** Sicherungsdateien (`*.save`, `*~`) und minimierte Kopien neben dem Quelltext sollten
  entweder alle beabsichtigt sein oder gar nicht vorkommen. Legen Sie eine `.gitignore` für Sicherungsdateien an.
- **Ein Design-Name je Aussehen.** Hat das Theme zwei Erscheinungsbilder, machen Sie daraus zwei Designs (`simple` und
  `simple_dark`), keine Bedingungen quer durch die Templates.

## Verwandte Seiten

- Zurück: [Themes sind Erweiterungen](themes-sind-erweiterungen.md)
- Weiter: [Ein Theme mit dem RAD-Assistenten](ein-theme-mit-dem-rad-assistenten.md)
- [Der Verzeichnisaufbau einer Erweiterung](../teil-3-erweiterungsentwickler/verzeichnisaufbau-einer-erweiterung.md)
- [Override-Bedingungen](../teil-2-builder/override-bedingungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
