---
title: "Mit RAD: eine Design-Erweiterung"
audience: developer, designer, app-creator
category: extension-development
order: 60
summary: Der Assistent für Design-Extensions schreibt ein Theme-Gerüst; danach richten Sie einen Siteaccess auf das neue Design und passen zwei erzeugte Einstellungen an, anhand der echten Ausgabe des Assistenten.
keywords: RAD, Assistent für Design-Extensions, Theme, Design, Pagelayout, Override, Stylesheet, JavaScript, design extension wizard, setup/designextension, theme, design, pagelayout, page_head, override, stylesheet, javascript, SiteDesign, AdditionalSiteDesignList, FrontendCSSFileList
type: help
---
# Mit RAD: eine Design-Erweiterung

Tutorial 6. Der **Assistent für Design-Extensions** (`/setup/designextension`) schreibt das Gerüst eines Themes: ein in
Teile aufgeteiltes Pagelayout, ein Stylesheet und ein Skript, die in `design.ini` registriert sind, einen Bilderordner
und ein Beispiel-Override. Designer machen in
[Teil 6](../../teil-6-theme-entwickler/README.md) weiter. Die Ausgabe unten
wurde vom Generator des Assistenten (`expDesignExtensionWizard::files()`) erzeugt; jede PHP-Datei hat `php -l`
bestanden.

## 1. Das Formular

| Feld | Wert |
|---|---|
| Name der Extension | `mysite_design` |
| Titel | `My site design` |
| Greift zurück auf | `base` |
| Was hineinkommt | Seitenlayout, Seitenteile, Stylesheets, JavaScript, Bildverzeichnis, Überschreib-Template, ezinfo.php (die Voreinstellungen); optional: Druck-Stylesheet, Siteaccess-Einstellungen, Template-Operatoren |

## 2. Die Dateien

```text
.gitignore
LICENSE
README.md
composer.json
design/mysite_design/images/.gitkeep
design/mysite_design/javascript/site.js
design/mysite_design/override/templates/full/article.tpl
design/mysite_design/stylesheets/site.css
design/mysite_design/templates/page_footer.tpl
design/mysite_design/templates/page_head.tpl
design/mysite_design/templates/page_header.tpl
design/mysite_design/templates/pagelayout.tpl
extension.xml
ezinfo.php
settings/design.ini.append.php
settings/override.ini.append.php
```

Das Design ist nach der Erweiterung benannt, `mysite_design`. Das Pagelayout ist kurz und gut lesbar:

```text
<!DOCTYPE html>
<html lang="{ezini( 'RegionalSettings', 'ContentObjectLocale' )|extract_left( 2 )}">
<head>
{include uri='design:page_head.tpl'}
</head>
<body class="mysite_design {$module_result.node_id|wash}">
{include uri='design:page_header.tpl'}
<main id="content" class="mysite_design-content">
{$module_result.content}
</main>
{include uri='design:page_footer.tpl'}
{ezscript_load( array( 'ezjsc::jquery',
                       ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' ),
                       ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
</body>
</html>
```

`page_head.tpl` bildet den Titel aus dem Pfad und lädt die Stylesheets mit `ezcss_load`. Das Beispiel-Override in
`settings/override.ini.append.php` gibt Artikeln eine eigene Full View:

```ini
[full_article_mysite_design]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
```

## 3. Einen Siteaccess auf das Design richten

`DesignExtensions[]=mysite_design` (in der erzeugten `design.ini.append.php`) macht die Design-Ordner der Erweiterung
durchsuchbar, aber ein Design wird nur von einem Siteaccess verwendet, der es nennt. Die erzeugte README sagt, das
Design werde gefunden, „ohne dass sonst etwas eingestellt werden muss“; tatsächlich müssen Sie es trotzdem setzen, zum
Beispiel:

```bash
./console exp:ini set site.ini/DesignSettings/SiteDesign mysite_design siteaccess:mysite
./console exp:ini clear 'site.ini/DesignSettings/AdditionalSiteDesignList[]' siteaccess:mysite
./console exp:ini add 'site.ini/DesignSettings/AdditionalSiteDesignList[]' base siteaccess:mysite
```

oder kreuzen Sie im Assistenten **Siteaccess-Einstellungen** an und tragen Sie den Siteaccess ein, sodass die
Erweiterung `settings/siteaccess/<name>/site.ini.append.php` selbst mitbringt. Prüfen Sie das Ergebnis mit dem Skript
zur Design-Kette aus [Die Design-Kette](../../teil-2-builder/die-design-kette.md).

## 4. Die Frontend-Listen bevorzugen

Die erzeugte `design.ini.append.php` registriert die Dateien in `CSSFileList[]` und `JavaScriptList[]`, die `design.ini`
zugunsten von `FrontendCSSFileList[]` und `FrontendJavaScriptList[]` als veraltet kennzeichnet. Ändern Sie die beiden
Zeilen:

```ini
[StylesheetSettings]
FrontendCSSFileList[]=site.css

[JavaScriptSettings]
FrontendJavaScriptList[]=site.js
```

Das erzeugte Pagelayout lädt bereits beide Listen.

## 5. Einschalten

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_design global
php bin/php/ezcache.php --clear-all
```

Öffnen Sie die Website: Ihr Rahmen, Ihr Stylesheet, Artikel mit Ihrer Full View. Die erzeugte `site.js` zeigt das Muster,
das zur Content Security Policy passt: eine Skriptdatei, die die Seite verbessert, aber nicht nötig ist, damit sie
funktioniert.

## Verwandte Seiten

- Zurück: [Mit RAD: ein Workflow-Ereignis](rad-workflow-ereignis.md)
- Weiter: [Mit RAD: eine Erweiterung nur mit Einstellungen](rad-einstellungs-erweiterung.md)
- [Ein Theme mit dem RAD-Assistenten](../../teil-6-theme-entwickler/ein-theme-mit-dem-rad-assistenten.md)
- [Ein Design innerhalb einer Erweiterung](../design-in-einer-erweiterung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
