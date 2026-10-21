---
title: Variablen des Pagelayouts
audience: designer
category: theme-design
order: 70
summary: Jede Variable, die der Kernel dem Pagelayout gibt ($module_result, $site, $current_user, $access_type, $ui_context und mehr), die Schlüssel von $module_result.content_info auf Inhaltsseiten und Beispiele für ihre Verwendung.
keywords: Variablen des Pagelayouts, module_result, content_info, site, current_user, access_type, ui_context, navigation_part, uri_string, requested_uri_string, path, title_path, view_parameters, persistent_variable, pagelayout variables
type: help
---
# Variablen des Pagelayouts

Diese Seite ist für Designer, die ein Pagelayout schreiben oder lesen. Sie listet die Variablen, die der Kernel setzt, bevor er
`pagelayout.tpl` rendert, so wie sie in `kernel/private/classes/ezpkernelweb.php` gesetzt werden, und die Schlüssel, die eine
Inhaltsansicht in `kernel/classes/eznodeviewfunctions.php` hinzufügt.

## Inhalt

1. [Die Variablen](#die-variablen)
2. [$module_result](#module_result)
3. [$module_result.content_info auf Inhaltsseiten](#module_resultcontent_info-auf-inhaltsseiten)
4. [$site](#site)
5. [Beispiele](#beispiele)
6. [Variablen, die Sie nicht erwarten sollten](#variablen-die-sie-nicht-erwarten-sollten)

## Die Variablen

| Variable | Was sie enthält |
|---|---|
| `$module_result` | was die Modul-View erzeugt hat (ein Hash, unten) |
| `$site` | Titel, Design, HTTP-Header-Werte, URI, Metadaten, Version und Textrichtung der Website |
| `$current_user` | der angemeldete Benutzer (ein `eZUser`), für Besucher der anonyme Benutzer; `false` auf den wenigen Seiten, die ohne Benutzerobjekt laufen |
| `$anonymous_user_id` | `site.ini [UserSettings] AnonymousUserID` |
| `$access_type` | der zugeordnete Siteaccess (`name`, der Zuordnungstyp ...) |
| `$navigation_part` | der Navigationsteil der View (die Administration verwendet ihn für den aktiven Reiter) |
| `$uri_string` | die URI der Anfrage, wie der Kernel sie aufgelöst hat |
| `$requested_uri_string` | die URI wie angefragt (bevor ein URL-Alias übersetzt wurde) |
| `$ui_context` | der Kontext der View, wie ihr Modul ihn angibt, zum Beispiel `view`, `edit`, `browse`, `administration`, `authentication` (die Einträge `ui_context` von `kernel/*/module.php`) |
| `$ui_component` | der Modulname der View |
| `$warning_list` | Warnungen des Kernels, die zu zeigen sind, oder `false` |

## $module_result

| Schlüssel | Inhalt |
|---|---|
| `content` | das HTML der View: geben Sie es einmal mit `{$module_result.content}` aus |
| `path` | der Pfad des Brotkrumenpfads: eine Liste von Hashes mit `text`, `url`, `url_alias`, `node_id`; `false`, wenn die View keinen setzt |
| `title_path` | wie `path`, für den `<title>` |
| `uri` | die URI der Anfrage |
| `content_info` | bei Inhaltsansichten: Fakten über den gezeigten Knoten (unten) |
| `view_parameters` | die View-Parameter der URL, z. B. ergibt `(offset)/20` den `offset` 20 |
| `node_id`, `section_id` | bei Inhaltsansichten |
| `navigation_part` | Bezeichner des Navigationsteils |
| `ui_context`, `ui_component` | wie oben |
| `is_mobile_device`, `mobile_device_alias` | aus der Erkennung mobiler Geräte des Kernels |
| `errorType`, `errorNumber` | auf Fehlerseiten (das Titel-Template des Designs media liest sie) |

## $module_result.content_info auf Inhaltsseiten

Eine Inhaltsansicht (`content/view`) füllt `content_info` mit diesen Schlüsseln (aus `eZNodeviewfunctions`):

| Schlüssel | Beispielhafte Verwendung |
|---|---|
| `node_id`, `object_id`, `parent_node_id` | den Knoten holen; den aktuellen Menüpunkt markieren |
| `class_id`, `class_identifier` | eine klassenspezifische Body-Klasse oder ein Kopfbereich |
| `viewmode` | `full` oder ein besonderer Modus wie `payload` des Designs media |
| `node_depth` | wie tief die Seite im Baum liegt |
| `url_alias`, `main_node_url_alias` | kanonische Links |
| `section_id`, `section_identifier`, `navigation_part_identifier` | Gestaltung nach Sektion |
| `remote_id`, `node_remote_id`, `parent_node_remote_id`, `parent_object_remote_id` | stabile Bezeichner, die sich zwischen Installationen nicht ändern |
| `parent_class_id`, `parent_class_identifier` | Gestaltung nach der Klasse des Elternknotens |
| `class_group`, `state`, `state_identifier` | Klassengruppen und Objektzustände |
| `current_language`, `language_mask` | die gezeigte Sprache |
| `offset` | der Versatz beim Blättern |
| `persistent_variable` | was View-Templates für das Pagelayout gesetzt haben ([Die persistent variable](../templates/die-persistent-variable.md)) |

`content_info` ist auf Seiten anderer Module (Suche, Benutzer, Fehler) kein Array. Prüfen Sie immer, bevor Sie es lesen:

```tpl
{if and( is_array( $module_result.content_info ), is_set( $module_result.content_info.class_identifier ) )}
    ...
{/if}
```

## $site

| Schlüssel | Inhalt |
|---|---|
| `title` | `site.ini [SiteSettings] SiteName` |
| `design` | `site.ini [DesignSettings] SiteDesign` |
| `http_equiv.Content-Type` | `text/html; charset=...` |
| `http_equiv.Content-language` | die Sprache der Seite, für `<html lang="...">` |
| `text_direction` | `rtl` für eine Oberflächensprache von rechts nach links, sonst `ltr` |
| `uri` | das ursprüngliche URI-Objekt |
| `meta` | `site.ini [SiteSettings] MetaDataArray`, mit einer aus dem Pfad gebildeten `description`, wenn keine gesetzt ist |
| `version` | die Exponential-Version |
| `page_title` | der Titel des Moduls |

## Beispiele

Eine Body-Klasse pro Inhaltsklasse, sodass ein Stylesheet Artikelseiten anders gestalten kann als Ordner:

```tpl
{def $body_class = 'page'}
{if and( is_array( $module_result.content_info ), is_set( $module_result.content_info.class_identifier ) )}
    {set $body_class = concat( 'page page-', $module_result.content_info.class_identifier )}
{/if}
<body class="{$body_class|wash}">
```

Der Name eines angemeldeten Benutzers im Kopfbereich:

```tpl
{if $current_user.is_logged_in}
    <span class="user-name">{$current_user.contentobject.name|wash}</span>
    <a href={'/user/logout'|ezurl}>{'Sign out'|i18n( 'design/mytheme' )}</a>
{else}
    <a href={'/user/login'|ezurl}>{'Sign in'|i18n( 'design/mytheme' )}</a>
{/if}
```

Das Sprachattribut und die Richtung:

```tpl
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

## Variablen, die Sie nicht erwarten sollten

- **`$node`**: im Pagelayout nicht gesetzt (der Kernel entfernt ihn); holen Sie den Knoten aus `content_info.node_id`
  (seit 3.9, siehe `doc/eng-us/features/3.9/no_more_node_in_pagelayout.txt`).
- **Variablen von View-Templates**: Ein `{def}` in `node/view/full.tpl` erreicht das Pagelayout nicht. Verwenden Sie die persistent
  variable, um Werte zu übergeben.
- **Irgendetwas Benutzerspezifisches in einem Cache-Block**, ohne dass der Benutzer in den Schlüsseln steht: Die zwischengespeicherte Kopie
  würde allen gezeigt.

## Verwandte Seiten

- [Das Pagelayout](das-pagelayout.md)
- [Modulergebnis und Inhaltsinformation](../templates/module-result-und-content-info.md)
- [Brotkrumenpfade](../templates/brotkrumen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
