---
title: Patterns in extension/sevenx_themes_simple
audience: developer
category: standards
order: 471
summary: Every class, view, fetch function, operator and plugin point of extension/sevenx_themes_simple with its design patterns.
keywords: extension/sevenx_themes_simple, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/sevenx_themes_simple

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/sevenx_themes_simple` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (1)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `sevenx_themes_simpleInfo` | class | [Value object / struct](value-object.md) | `extension/sevenx_themes_simple/ezinfo.php:11` | – |

## INI plugin points (16)

| Setting | Class | Set in |
|---|---|---|
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
