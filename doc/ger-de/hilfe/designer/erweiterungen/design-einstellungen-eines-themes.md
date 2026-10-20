---
title: "Die Design-Einstellungen, die ein Theme mitliefert"
audience: designer, developer
category: theme-extensions
order: 130
summary: Welche Design-Einstellungen in die Theme-Erweiterung gehören und welche der Installation - SiteDesign, AdditionalSiteDesignList[], StandardDesign und die Listen für Stylesheets und Skripte - mit einer empfohlenen Aufteilung.
keywords: SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignSettings, FrontendCSSFileList, FrontendJavaScriptList, StylesheetSettings, design.ini, site.ini, Design-Einstellungen, Aufteilung, design settings
type: help
---
# Die Design-Einstellungen, die ein Theme mitliefert

Diese Seite ist für Designer, die entscheiden, welche Einstellungen sie in ihr Theme setzen und welche sie der Person
überlassen, die es installiert. Wie Designs aufeinander zurückgreifen, erklärt [Die Design-Kaskade](../../../../eng-us/help/designers/theme-design/the-design-cascade.md);
hier geht es nur darum, **wo jede Einstellung liegt**.

## Die beteiligten Einstellungen

| Einstellung | Datei und Block | Standard im Kernel | Bedeutung |
|---|---|---|---|
| `SiteDesign` | `site.ini [DesignSettings]` | `admin` (der globale Standard; Siteaccesses setzen ihren eigenen) | Das Design, das ein Siteaccess zuerst verwendet |
| `AdditionalSiteDesignList[]` | `site.ini [DesignSettings]` | leer | Weitere Designs, die vor dem Standard-Design durchsucht werden |
| `StandardDesign` | `site.ini [DesignSettings]` | `standard` | Der letzte Rückgriff |
| `DesignExtensions[]` | `design.ini [ExtensionSettings]` | leer | Erweiterungen, die nach Designs durchsucht werden |
| `FrontendCSSFileList[]`, `FrontendJavaScriptList[]` | `design.ini [StylesheetSettings]`, `[JavaScriptSettings]` | leer | Stylesheets und Skripte, die das öffentliche Pagelayout lädt ([Stylesheets laden](../../../../eng-us/help/designers/theme-design/loading-stylesheets.md)) |

## Die empfohlene Aufteilung

| In der Theme-Erweiterung | In der Installation (Siteaccess oder Override) |
|---|---|
| `DesignExtensions[]=<extension>` | `SiteDesign=<design>` für den Siteaccess, der das Theme verwenden soll |
| Die eigenen Listen des Themes für Stylesheets und Skripte | `AdditionalSiteDesignList[]`, wenn die Installation mehrere Themes kombiniert |
| Standardwerte der eigenen Einstellungsdatei des Themes | Website-Name, URLs, Mail- und Datenbankeinstellungen |

Der Grund: Ein Theme kann auf einer Installation mit mehreren Siteaccesses installiert werden. Setzte das Theme
`SiteDesign` global, änderte es auch die Administration und jede andere Website.

## Wenn das Theme einen Siteaccess mitliefert

Manche Themes kommen mit einem eigenen Siteaccess (`cjw_themes_jumper` hat Einstellungen für `cjw_starter`). Dann darf
`SiteDesign` in `extension/<theme>/settings/siteaccess/<siteaccess>/site.ini.append.php` liegen, weil es nur gilt,
solange dieser Siteaccess läuft ([Siteaccess-Einstellungen in einem Theme](siteaccess-einstellungen-in-einem-theme.md)).

## Schritt für Schritt

1. Setzen Sie `DesignExtensions[]` und die Stylesheet-Listen in das Theme.
2. Dokumentieren Sie die Zeile `SiteDesign` im README, statt sie zu setzen.
3. Setzen Sie sie auf einer Testinstallation für einen Siteaccess:

   ```bash
   ./console exp:ini set site.ini/DesignSettings/SiteDesign mytheme siteaccess:site --dry-run --allow-root-user
   ```

   Lesen Sie die ausgegebene Datei und führen Sie den Befehl dann ohne `--dry-run` erneut aus.
4. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
5. Prüfen Sie, dass die Administration weiterhin ihr eigenes Design verwendet.

## Verwandte Seiten

- [Designs und Siteaccesses](../../../../eng-us/help/designers/theme-design/designs-and-siteaccesses.md)
- [Das Design registrieren](das-design-registrieren.md)
- [Einstellungsdateien in einer Erweiterung](einstellungsdateien-in-einer-erweiterung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
