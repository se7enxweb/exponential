---
title: Muster in extension/sevenx_themes_simple
audience: developer
category: standards
order: 471
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/sevenx_themes_simple mit den Entwurfsmustern.
keywords: extension/sevenx_themes_simple, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/sevenx_themes_simple

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/sevenx_themes_simple` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (1)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `sevenx_themes_simpleInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/sevenx_themes_simple/ezinfo.php:11` | – |

## INI-Plugin-Punkte (16)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[eZJSCore] JavaScriptOptimizer[]` | `ezjscJavascriptOptimizer` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:12` |
| `[eZJSCore] CssOptimizer[]` | `ezjscCssOptimizer` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:15` |
| `[ezjscServer_ezjsc] Class` | `ezjscServerFunctionsJs` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:88` |
| `[ezjscServer_ezjscnode] Class` | `ezjscServerFunctionsNode` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:94` |
| `[ezjscServer_ezpublishingqueue] Class` | `ezjscServerFunctionsPublishingQueue` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:112` |
| `[ezjscServer_ezajaxuploader] Class` | `ezjscServerFunctionsAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:115` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]` | `ezpRelationListAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:119` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelation]` | `ezpRelationAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:120` |
| `[eZJSCore] JavaScriptOptimizer[]` | `ezjscJavascriptOptimizer` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:12` |
| `[eZJSCore] CssOptimizer[]` | `ezjscCssOptimizer` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:15` |
| `[ezjscServer_ezjsc] Class` | `ezjscServerFunctionsJs` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:88` |
| `[ezjscServer_ezjscnode] Class` | `ezjscServerFunctionsNode` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:94` |
| `[ezjscServer_ezpublishingqueue] Class` | `ezjscServerFunctionsPublishingQueue` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:112` |
| `[ezjscServer_ezajaxuploader] Class` | `ezjscServerFunctionsAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:115` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]` | `ezpRelationListAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:119` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelation]` | `ezpRelationAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:120` |
