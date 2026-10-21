---
title: Ein Theme von Hand
audience: designer, developer
category: extension-development
order: 40
summary: Das kleinste vollständige Theme Datei für Datei bauen - Metadaten, die Registrierung des Designs, ein Siteaccess, ein Pagelayout, ein Stylesheet und ein Override - und jeden Schritt prüfen, damit Sie wissen, was jede Datei eines Themes tut.
keywords: Theme, von Hand, Pagelayout, Design registrieren, Siteaccess, theme, by hand, pagelayout.tpl, design.ini, DesignExtensions, FrontendCSSFileList, siteaccess, override.ini, ezcss_load, ezscript_load, module_result
type: help
---
# Ein Theme von Hand

Teil 6, Kapitel 4. Der Assistent des vorigen Kapitels ist schnell; ein Theme einmal von Hand zu schreiben lehrt Sie,
wozu jede Datei da ist. Dieses Kapitel baut `mytheme`, das kleinste Theme, das eine ganze Website verändert, in sechs
Dateien. Jedes Beispiel wurde geprüft: Das Pagelayout wurde durch die Template-Engine dargestellt, und die INI-Abschnitte
und Einstellungen gibt es in `design.ini` und `site.ini` des Kernels.

## Die Dateien

```text
extension/mytheme/
  ezinfo.php
  extension.xml
  settings/design.ini.append.php
  settings/siteaccess/mysite/site.ini.append.php
  design/mytheme/templates/pagelayout.tpl
  design/mytheme/stylesheets/mytheme.css
```

Ersetzen Sie `mysite` durch den Namen Ihres öffentlichen Siteaccess.

## 1. Wer Sie sind: ezinfo.php und extension.xml

Schreiben Sie sie so, wie [ezinfo.php](../teil-3-erweiterungsentwickler/ezinfo-php.md) und
[extension.xml](../teil-3-erweiterungsentwickler/extension-xml.md) es beschreiben: ein lesbarer Name, Version `1.0.0`, Ihr
Copyright, die Lizenz, eine Website.

## 2. Das Design registrieren: settings/design.ini.append.php

```ini
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mytheme

[StylesheetSettings]
FrontendCSSFileList[]=mytheme.css

*/ ?>
```

`DesignExtensions[]` fügt `extension/mytheme/design/` zu den Ordnern hinzu, in denen nach Designs gesucht wird.
`FrontendCSSFileList[]` nennt das Stylesheet, das das Pagelayout laden soll.

## 3. Das Design verwenden: die site.ini des Siteaccess

`settings/siteaccess/mysite/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=base

*/ ?>
```

`SiteDesign` wird zuerst durchsucht, dann die Designs aus `AdditionalSiteDesignList[]`, zuletzt das `StandardDesign`
des Kernels (`standard`). Die leere Zeile `AdditionalSiteDesignList[]` leert, was der Siteaccess vorher hatte, sodass
die Liste genau das enthält, was Sie geschrieben haben. Mehr dazu in [Themes und Siteaccesses](themes-und-siteaccesses.md).

## 4. Der Rahmen: design/mytheme/templates/pagelayout.tpl

```text
<!DOCTYPE html>
<html lang="{ezini( 'RegionalSettings', 'ContentObjectLocale' )|extract_left( 2 )|wash}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{ezini( 'SiteSettings', 'SiteName' )|wash}</title>
{ezcss_load( ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) )}
</head>
<body class="mytheme">
<header class="mytheme-header">
  <a class="mytheme-logo" href={'/'|ezurl}>{ezini( 'SiteSettings', 'SiteName' )|wash}</a>
</header>
<main id="content" class="mytheme-content">
{$module_result.content}
</main>
<footer class="mytheme-footer">
  <p>{currentdate()|datetime( 'custom', '%Y' )} {ezini( 'SiteSettings', 'SiteName' )|wash}</p>
</footer>
{ezscript_load( array( 'ezjsc::jquery', ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
</body>
</html>
```

Was jeder Teil tut:

| Zeile | Zweck |
|---|---|
| `ezini( 'RegionalSettings', 'ContentObjectLocale' )` | das Gebietsschema der Website (zum Beispiel `eng-US`); `extract_left( 2 )` ergibt die Sprache für `lang` |
| `ezcss_load( ... )` | die Funktion von ezjscore: bündelt die aufgeführten Stylesheets zu einer Datei und gibt ihren `<link>` aus |
| `{'/'\|ezurl}` | die Adresse der Startseite der Website, bereits in Anführungszeichen |
| `$module_result.content` | die Ausgabe des Moduls, das die Anfrage beantwortet hat: der Hauptinhalt der Seite |
| `ezscript_load( ... )` | bündelt die Skripte und gibt sie aus, hier jQuery aus ezjscore und Ihre Frontend-Liste |

Jeder Wert, der in HTML ausgegeben wird, läuft durch `wash`.

> **Hinweis**
> Außerhalb einer echten Anfrage dargestellt, gibt dieses Pagelayout alles außer dem Inhalt aus, und die
> Template-Engine meldet, dass `$module_result` unbekannt ist. Bei einer Seitenanfrage setzt der Kernel die Variable.

## 5. Das Aussehen: design/mytheme/stylesheets/mytheme.css

```css
:root {
    --mytheme-ink: #1f2328;
    --mytheme-surface: #ffffff;
    --mytheme-accent: #0b6bcb;
    --mytheme-space: 1rem;
}
body.mytheme { margin: 0; color: var(--mytheme-ink); background: var(--mytheme-surface); font-family: system-ui, sans-serif; }
.mytheme-header, .mytheme-footer { padding: var(--mytheme-space); }
.mytheme-content { max-width: 60rem; margin: 0 auto; padding: var(--mytheme-space); }
.mytheme-logo { color: var(--mytheme-accent); font-weight: 700; text-decoration: none; }
```

Zuerst die Variablen: Das Kapitel [Dunkler Modus und Design-Tokens in Themes](dunkler-modus-und-tokens-in-themes.md) baut
auf ihnen auf.

## 6. Einschalten und ansehen

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mytheme global
php bin/php/ezcache.php --clear-all
```

Öffnen Sie die Website: Ihr Kopfbereich, der Inhalt wie zuvor, Ihre Fußzeile, Ihre Farben. Jede Inhaltsseite verwendet
innerhalb Ihres Rahmens weiterhin die Templates von `base` und `standard`. Ersetzen Sie sie von hier aus eines nach dem
anderen ([Ein Theme mit dem RAD-Assistenten](ein-theme-mit-dem-rad-assistenten.md), Abschnitt „Vom Gerüst zum Theme“).

## Prüfen, welches Template verwendet wurde

Schalten Sie für Ihren Entwicklungs-Siteaccess das Template-Debugging ein (`site.ini [TemplateSettings]
ShowUsedTemplates=enabled` bei eingeschalteter Debug-Ausgabe). Die Debug-Ausgabe listet dann jedes Template auf, das
die Seite verwendet hat, und das Design, aus dem es stammt ([Templates debuggen](../teil-2-builder/templates-debuggen.md)).

## Verwandte Seiten

- Zurück: [Ein Theme mit dem RAD-Assistenten](ein-theme-mit-dem-rad-assistenten.md)
- Weiter: [Themes und Siteaccesses](themes-und-siteaccesses.md)
- [Die Design-Kette](../teil-2-builder/die-design-kette.md)
- [Siteaccess-Einstellungen](../teil-2-builder/siteaccess-einstellungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
