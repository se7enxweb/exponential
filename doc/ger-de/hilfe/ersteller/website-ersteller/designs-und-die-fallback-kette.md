---
title: Designs und die Fallback-Kette
audience: website-creator, developer
category: website-creation
order: 110
summary: Wie Exponential ein Template, Stylesheet oder Bild findet - Site-Design, zusätzliche Designs, Erweiterungsdesigns und das Design standard - und wie Sie die Kette für jeden Siteaccess lesen und ändern.
keywords: Design, Fallback, Fallback-Kette, Siteaccess, Template-Suche, Reihenfolge der Erweiterungen, Website-Ersteller, design, fallback, SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions, siteaccess, template lookup, ezdesign, ezimage, extension order, website creator
type: help
---
# Designs und die Fallback-Kette

Diese Seite richtet sich an Website-Ersteller, die verstehen wollen, warum eine Seite ein bestimmtes Template verwendet
und nicht ein anderes. Jeder Siteaccess hat eine Kette von Designs; Exponential geht sie durch und nimmt die erste Datei,
die es findet. Sobald Sie die Kette lesen können, sind Overrides, Themes und Erweiterungen kein Rätsel mehr.

## Inhalt

1. [Die Kette](#die-kette)
2. [Wo Designs liegen](#wo-designs-liegen)
3. [Die Kette eines Siteaccess lesen](#die-kette-eines-siteaccess-lesen)
4. [Was die Kette verwendet](#was-die-kette-verwendet)
5. [Die Kette ändern](#die-kette-ändern)
6. [Häufige Fehler](#häufige-fehler)

## Die Kette

Für ein Template wie `node/view/full.tpl` verwendet Exponential den ersten Treffer in dieser Reihenfolge
([1. Wie ein Template gefunden wird](../../../anleitungen/templates-und-design.md#1-wie-ein-template-gefunden-wird)):

1. die **Override-Templates**, deren Regeln in `override.ini` auf die Seite passen, in
   `design/<design>/override/templates/`;
2. das einfache Verzeichnis `templates/` des **Site-Designs** (`[DesignSettings] SiteDesign`);
3. dieselben Verzeichnisse jedes **zusätzlichen Designs** (`AdditionalSiteDesignList[]`, in Reihenfolge);
4. das **Standard-Design** (`StandardDesign=standard` in `settings/site.ini`), der Fallback, den Sie nie bearbeiten.

Jeder Design-Name kann an mehreren Orten existieren: in `design/` der Installation und in jeder Erweiterung, die sich
als Design-Erweiterung erklärt. Erweiterungen werden in ihrer Ladereihenfolge durchsucht, daher gewinnt eine Erweiterung
weiter vorne in der Liste gegenüber einer späteren mit derselben Datei.

## Wo Designs liegen

| Ort | Erklärt durch |
|---|---|
| `design/<design>/` der Installation | nichts; wird immer durchsucht |
| `extension/<ext>/design/<design>/` | `DesignExtensions[]=<ext>` in `settings/design.ini.append.php` der Erweiterung |

Ein Design-Verzeichnis enthält `templates/`, `override/templates/`, `stylesheets/`, `images/` und `javascript/`.

## Die Kette eines Siteaccess lesen

```bash
php bin/php/console exp:ini where site/DesignSettings/SiteDesign <siteaccess>
php bin/php/console exp:ini get site/DesignSettings/AdditionalSiteDesignList <siteaccess>
php bin/php/console exp:ini get design/ExtensionSettings/DesignExtensions <siteaccess>
```

`where` gibt jede Datei aus, die den Wert setzt, und die gültige; `get` gibt den Wert aus
([Der Befehl exp:ini](../../../../eng-us/features/6.0/exp-ini-command.md)). Um zu sehen, welche Datei auf einer echten Seite
geantwortet hat, schalten Sie für den Siteaccess die Template-Pfad-Kommentare ein, laden Sie neu und suchen Sie im
Seitenquelltext nach `START` ([Template-Pfad-Kommentare](../../../../eng-us/features/6.0/template-path-comments.md)).

## Was die Kette verwendet

| In einem Template | Findet |
|---|---|
| `{include uri='design:parts/header.tpl'}` | ein Template, über die Kette |
| `{'logo.png'\|ezimage}` | ein Bild in `images/` der Kette |
| `{'stylesheets/site.css'\|ezdesign}` | jede Datei der Kette |
| `{ezcss_load(...)}`, `{ezscript_load(...)}` | Stylesheets und Skripte der Kette, von ezjscore gepackt ([Stylesheets und Skripte](stylesheets-und-skripte.md)) |
| `{node_view_gui content_node=$node view='line'}` | `node/view/line.tpl` oder den passenden Override |

## Die Kette ändern

Ein Siteaccess, sein eigenes Design zuerst, das Theme als Fallback:

```ini
# settings/siteaccess/<site>/site.ini.append.php
[DesignSettings]
SiteDesign=mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=simple
```

Die leere Zeile `AdditionalSiteDesignList[]` löscht, was frühere Dateien gesetzt haben, sodass die Liste genau Ihre ist.
Leeren Sie dann die Caches, die sich Template-Orte merken:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=template-override --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
```

Um zu ändern, welche Erweiterung bei gleichem Design-Namen gewinnt, ändern Sie die Reihenfolge der Erweiterungen
([Setup > Extensions: Ladereihenfolge](../../../../eng-us/features/6.0/extension-loading-order.md)).

## Häufige Fehler

| Symptom | Ursache | Lösung |
|---|---|---|
| Eine neue Template-Datei wird ignoriert | der Override-Cache enthält noch die alte Suche | `template-override` und `template` leeren |
| Ein Theme-Update hat Ihre Änderung gelöscht | Sie haben das Theme an Ort und Stelle bearbeitet | verschieben Sie die Änderung in Ihre eigene Design-Erweiterung |
| Die Administration zeigt Ihr öffentliches Design | Sie haben `SiteDesign` in `settings/override/` gesetzt, was für jeden Siteaccess gilt | setzen Sie es in `settings/siteaccess/<site>/` |
| Die Datei Ihrer Erweiterung verliert gegen die des Themes | Ihre Erweiterung wird nach dem Theme geladen | setzen Sie sie in `ActiveAccessExtensions[]` an den Anfang oder ändern Sie die Reihenfolge unter Setup > Extensions |
| Eine Seite ist nach dem Hinzufügen eines Designs leer | das Pagelayout des neuen Designs hat einen Fehler | `php bin/php/eztemplatecheck.php` auf die Datei ([5. Die Syntax prüfen](../../../anleitungen/templates-und-design.md#5-die-syntax-prüfen-bevor-sie-in-den-browser-schauen)) |

## Verwandte Seiten

- [Ein Design oder Theme wählen](ein-design-oder-theme-waehlen.md)
- [Template-Overrides](template-overrides.md)
- [Zusätzliche Erweiterungsverzeichnisse](../../../../eng-us/features/6.0/additional-extension-directories.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
