---
title: Template-Kompilierung und Cache
audience: designer, developer
category: templates
order: 770
summary: Wie Templates zu PHP werden und welche Caches sie halten - TemplateCompile, kompilierte Templates in var/<site>/cache/template/compiled, DevelopmentMode zum Prüfen von Dateizeiten, die Caches template, template-override und template-block und exp:eztc zum Vorkompilieren.
keywords: Template-Kompilierung, kompilierte Templates, TemplateCompile, DevelopmentMode, Template-Cache, Cache der Template-Overrides, eztc, Templates vorkompilieren, Template-Cache leeren, TemplateCache, template compilation, compiled templates, template cache, template-override cache, precompile templates, cache clear templates
type: help
---
# Template-Kompilierung und Cache

Diese Seite ist für Designer, die wissen wollen, warum sich eine Template-Änderung manchmal sofort zeigt und manchmal nicht.

## Inhalt

1. [Kompilierung](#kompilierung)
2. [Die Template-Caches](#die-template-caches)
3. [Einstellungen für die Entwicklung](#einstellungen-für-die-entwicklung)
4. [Vorkompilieren](#vorkompilieren)

## Kompilierung

Mit `site.ini [TemplateSettings] TemplateCompile=enabled` (Standard) wird jedes Template bei seiner ersten Verwendung in PHP
übersetzt und unter `var/<site>/cache/template/compiled/` gespeichert; spätere Anfragen führen das PHP aus.
`TemplateOptimization` (enabled) optimiert den erzeugten Code.

## Die Template-Caches

| Cache-ID | Enthält | Leeren nach |
|---|---|---|
| `template` | kompilierte Templates | Template-Änderungen, wenn Dateizeiten nicht geprüft werden |
| `template-override` | die Tabellen für Overrides und die Template-Suche | dem Hinzufügen von Templates oder Override-Regeln |
| `template-block` | die gespeicherte Ausgabe von Cache-Blöcken | Änderungen an Teilen innerhalb von Cache-Blöcken |
| `content` | den View-Cache (gerenderte Vollansichten) | Änderungen an View-Templates |
| `design_base` | die Liste der Design-Ordner (mit `DesignLocationCache=enabled`) | dem Aktivieren einer Design-Erweiterung |

`php bin/php/ezcache.php --clear-id=<id>`; `--list-ids` gibt alle aus. `[TemplateSettings] TemplateCache=enabled` steuert
Cache-Blöcke (disabled: nie verwendet).

## Einstellungen für die Entwicklung

`[TemplateSettings] DevelopmentMode=enabled` lässt die Template-Engine die Zeiten von kompilierten und Quelldateien
vergleichen, sodass bearbeitete Templates ohne Leeren des Caches neu kompiliert werden (der Kommentar in `settings/site.ini`
warnt, dass das Dateizugriffe hinzufügt und nicht für Live-Websites ist). Es stellt auch den Packer auf Stufe 0
([Der Packer](../../../../eng-us/help/designers/theme-design/the-packer.md)). Schalten Sie es nur auf einem Entwicklungs-Siteaccess ein und leeren Sie beim
Umschalten die kompilierten Templates.

## Vorkompilieren

`./console exp:eztc` kompiliert die Templates eines Siteaccess vorab in den Template-Cache (`--force` kompiliert alle neu),
sodass die ersten Besucher nach einem Deployment nicht für die Kompilierung bezahlen.

## Verwandte Seiten

- [Caching für Designer](../../../../eng-us/help/designers/theme-design/caching-for-designers.md)
- [Der Packer-Cache und Deployments](../../../../eng-us/help/designers/theme-design/packer-cache-and-deploy.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
