---
title: Referenz des Modulergebnisses
audience: developer, designer, app-creator
category: development
order: 310
summary: Jeder im Kernel gefundene Schlüssel von $Result / $module_result - content, path, title_path, content_info und seine 25 Schlüssel, view_parameters, pagelayout, navigation_part, left_menu, errorType und der Rest - mit dem Code, der jeden setzt, und den Werten, die content/view auf alpha zurückgab.
keywords: $module_result, $Result, content_info, path, title_path, view_parameters, Pagelayout, navigation_part, errorType, errorNumber, cache_ttl, template_list, ui_context, Referenz, Modulergebnis, pagelayout, module result, reference
type: help
---
# Referenz des Modulergebnisses

Eine Modulansicht gibt ein Array zurück, `$Result`; der Kernel fügt einige Schlüssel hinzu und übergibt es dem Pagelayout als `$module_result`
(siehe [Templates von Modulansichten](templates-von-modulansichten.md)). Diese Seite führt jeden Schlüssel auf, den die Ansichten des Kernels,
`eZModule::run()` und `ezpKernelWeb` setzen, gefunden durch Durchsuchen des Kernels nach `$Result['...']` und `$moduleResult['...']`. Die Werte
in den Beispielen sind das, was `content/view/full/96` auf alpha zurückgab.

## Inhalt

1. [Schlüssel, die jede Ansicht setzen kann](#schlüssel-die-jede-ansicht-setzen-kann)
2. [Schlüssel, die der Kernel hinzufügt](#schlüssel-die-der-kernel-hinzufügt)
3. [Schlüssel von Inhaltsansichten](#schlüssel-von-inhaltsansichten)
4. [content_info](#content_info)
5. [Schlüssel von Fehlerergebnissen](#schlüssel-von-fehlerergebnissen)
6. [Es im Pagelayout lesen](#es-im-pagelayout-lesen)
7. [Verwandte Seiten](#verwandte-seiten)

## Schlüssel, die jede Ansicht setzen kann

| Schlüssel | Bedeutung | Gesetzt von |
|---|---|---|
| `content` | das HTML der Ansicht, vom Pagelayout mit `{$module_result.content}` platziert | jeder Ansicht |
| `path` | die Brotkrumennavigation: eine Liste von `array( 'text' => ..., 'url' => ... )`, die letzte mit `url` false; Inhaltsansichten fügen `url_alias` und `node_id` hinzu | jeder Ansicht; `false`, wenn eine Ansicht keinen setzt (`ezpKernelWeb`) |
| `title_path` | dasselbe für den Seitentitel, wenn er sich von `path` unterscheiden soll | Inhaltsansichten, einigen anderen |
| `pagelayout` | ein anderes Pagelayout-Template, oder `false` für keines | z. B. den Anmeldeansichten für Benutzer (`loginpagelayout.tpl`), `layout/set`, `content/treemenu` (`false`) |
| `navigation_part` | Bezeichner des Navigationsteils der Administration (Reiter im oberen Menü) | Ansichten; sonst `default_navigation_part` aus `module.php` |
| `left_menu` | ein Template für das linke Menü des Admin-Designs | den Ansichten der Setup-Logs (`design:setup/logs_menu.tpl`) |
| `ui_context` | `edit`, `view`, `browse`, `administration`, `ajax`, `authentication` ...; wird auch aus `module.php` genommen | Ansichten oder `module.php` |
| `requested_uri_string` | die als angefragt zu meldende URI, wenn die Ansicht unter einer anderen lief | Ansichten, die erneut ausgeführt werden |
| `rerun_uri` | lässt den Kernel stattdessen eine andere URI ausführen (mit `eZModule::STATUS_RERUN`) | Ansichten |
| `external_css` | ob die Debug-Ausgabe ein externes Stylesheet nutzt | Ansichten |
| `title` | ein Titel für eine Fehlerseite (abgelehnte Formular-Token) | der Fehleransicht |

## Schlüssel, die der Kernel hinzufügt

| Schlüssel | Wert | Wo |
|---|---|---|
| `is_default_navigation_part` | `true`, wenn `navigation_part` aus `module.php` kam, sonst `false` | `eZModule::run()`, `lib/ezutils/classes/ezmodule.php` |
| `ui_context` | der UI-Kontext des Moduls, wenn die Ansicht keinen gesetzt hat | `ezpKernelWeb::run()` |
| `ui_component` | die UI-Komponente des Moduls (standardmäßig der Modulname) | `ezpKernelWeb::run()` |
| `is_mobile_device`, `mobile_device_alias` | die Erkennung mobiler Geräte für die Anfrage | `ezpKernelWeb::run()` |
| `uri` | die URI der Anfrage (`eZSys::requestURI()`) | `ezpKernelWeb::run()`, nur wenn ein Pagelayout gerendert wird |

## Schlüssel von Inhaltsansichten

`eZNodeviewfunctions::generateNodeViewData()` (`kernel/classes/eznodeviewfunctions.php`) baut diese für `content/view`:

| Schlüssel | Wert auf alpha (Knoten 96) |
|---|---|
| `view_parameters` | `offset`, `year`, `month`, `day`, `namefilter`, plus jeder Benutzerparameter (siehe [Ansichtsparameter](ansichtsparameter.md)) |
| `section_id` | `1` |
| `node_id` | `96` |
| `content_info` | siehe unten |
| `template_list` | die zum Rendern der Ansicht genutzten Templates (11 auf alpha), mit dem View-Cache gespeichert, damit ein geändertes Template ihn verfallen lässt |
| `cache_ttl` | `-1`, oder der Wert einer Template-Variablen `$cache_ttl`; `0` fügt `no_cache` hinzu |
| `no_cache` | `true`, wenn ein Template `cache_ttl` auf 0 gesetzt hat |

## content_info

`content_info` lässt das Pagelayout wissen, was die Seite zeigt, ohne Fetch. Alle 25 Schlüssel, wie alpha sie für Knoten 96 zurückgab:

| Schlüssel | Wert auf alpha | Bedeutung |
|---|---|---|
| `object_id` | `94` | ID des Inhaltsobjekts |
| `node_id` | `96` | Knoten-ID |
| `parent_node_id` | `92` | ID des Elternknotens |
| `class_id` | `44` | Klassen-ID |
| `class_identifier` | `'ng_article'` | Klassenbezeichner |
| `remote_id` | `'media-o-958'` | Remote-ID des Objekts |
| `node_remote_id` | `'media-n-729'` | Remote-ID des Knotens |
| `offset` | `false` | der Ansichtsparameter `(offset)` als ganze Zahl, oder false |
| `viewmode` | `'full'` | Ansichtsmodus |
| `navigation_part_identifier` | `'ezcontentnavigationpart'` | Navigationsteil |
| `node_depth` | `4` | Tiefe des Knotens |
| `url_alias` | `'fitness/why-should-you-choose-strength-training'` | URL-Alias des Knotens |
| `current_language` | `'eng-US'` | Sprache, in der das Objekt gezeigt wird |
| `language_mask` | `2` | Bitmaske der Sprachen des Objekts |
| `main_node_id` | `96` | Hauptknoten des Objekts |
| `main_node_url_alias` | `false` | URL-Alias des Hauptknotens, wenn dies nicht der Hauptknoten ist und der Benutzer ihn lesen darf |
| `persistent_variable` | `false` | was das Ansichts-Template in `$persistent_variable` gelegt hat ([Einzelheiten](die-persistente-variable-von-innen.md)) |
| `class_group` | `false` | IDs der Klassengruppen, wenn `EnableClassGroupOverride` eingeschaltet ist |
| `state` | `array( 2 => 1, 47 => 80 )` | ID der Zustandsgruppe zur Zustands-ID |
| `state_identifier` | ein Array mit 2 Einträgen | Bezeichner `group/state` |
| `section_identifier` | `'standard'` | Bezeichner der Sektion |
| `parent_class_id` | `45` | Klassen-ID des Objekts des Elternknotens |
| `parent_class_identifier` | `'ng_landing_page'` | Klassenbezeichner des Objekts des Elternknotens |
| `parent_node_remote_id` | `'media-n-721'` | Remote-ID des Elternknotens |
| `parent_object_remote_id` | `'media-o-950'` | Remote-ID des Objekts des Elternknotens |

Wenn die Seite aus dem Content-View-Cache kommt, stellt der Kernel dasselbe `$Result` aus der Cache-Datei wieder her und setzt die
Override-Schlüssel erneut aus `content_info` (`eZNodeviewfunctions::contentViewRetrieve()`), sodass `content_info` verfügbar ist, ob die Seite
gecacht war oder nicht.

## Schlüssel von Fehlerergebnissen

Eine Ansicht, die scheitert, ruft `$Module->handleError()` auf, das die Fehleransicht ausführt (`kernel/private/classes/views/error/view.php`).
Auf alpha gab `content/view/full/99999999` zurück:

| Schlüssel | Wert auf alpha |
|---|---|
| `errorType` | `'kernel'` |
| `errorNumber` | `3`, `eZError::KERNEL_NOT_AVAILABLE` (die Nummern sind Konstanten von `eZError`, `kernel/classes/ezerror.php`: 1 Zugriff verweigert, 2 nicht gefunden, 3 nicht verfügbar, 4 verschoben, 5 Sprache nicht gefunden, 6 Formular-Token abgelehnt, 20 Modul nicht gefunden, 21 Ansicht nicht gefunden, 22 Modul ausgeschaltet) |
| `errorCode` | `'404'` |
| `errorMessage` | `'Not Found'` |
| `responseHeaders` | `'HTTP/1.1 404 Not Found'`, `'Status: 404 Not Found'` |
| `path` | `Error` / `kernel (3)` |

`errorType` und `errorNumber` werden außerdem zu den Override-Schlüsseln `error_type` und `error_number`, sodass Fehlerseiten eigene Templates
haben können (siehe [Fehler-Templates](../../../eng-us/help/designers/templates/error-templates.md)).

## Es im Pagelayout lesen

Nicht auf alpha ausgeführt: `$module_result` existiert nur, während der Kernel ein Pagelayout für eine Web-Anfrage rendert, dieses Beispiel
kann also nicht im CLI-Renderer der Beispiele laufen. Die Werte, die es liest, sind die in den Tabellen oben aufgeführten, die aus dem
Ausführen der Ansicht stammen.

```tpl
{* the class of the page, without fetching the node *}
{if is_set( $module_result.content_info )}
    <body class="class-{$module_result.content_info.class_identifier}">
{else}
    <body class="module-{$module_result.ui_component}">
{/if}

{* the breadcrumb *}
{foreach $module_result.path as $item}
    {if $item.url}<a href={cond( is_set( $item.url_alias ), $item.url_alias, $item.url )|ezurl}>{$item.text|wash}</a>{else}{$item.text|wash}{/if}
    {delimiter} / {/delimiter}
{/foreach}

{$module_result.content}
```

`content_info` existiert nur bei Inhaltsansichten: Prüfen Sie es mit `is_set()` in einem Pagelayout, das auch anderen Modulen dient.

## Verwandte Seiten

- [Templates von Modulansichten](templates-von-modulansichten.md)
- [Ansichtsparameter](ansichtsparameter.md)
- [Die persistente Variable, von innen](die-persistente-variable-von-innen.md)
- [Modulergebnis und content_info](../../../eng-us/help/designers/templates/module-result-and-content-info.md)
- [Brotkrumennavigation](../../../eng-us/help/designers/templates/breadcrumbs.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
