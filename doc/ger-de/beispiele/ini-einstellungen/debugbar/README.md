---
title: debugbar.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von debugbar.ini.
keywords: Beispiele, Referenz, debugbar.ini, examples, reference
type: generated
generated: exp:help:examples
---
# debugbar.ini

Die ausgelieferten Einstellungen von `settings/debugbar.ini`: 42 Blöcke, 288 Einstellungen.

> Das Register der Exp-Debug-Leiste (die angeheftete Leiste am unteren Rand einer Seite mit Debug-Ausgabe).  
> Anleitung: doc/eng-us/bc/6.0/debug-bar.md  
>
> Jede Einstellung, die die Leiste zeigt und ändert, ist ein Block \[Setting\_<id>\]:  
>
>   \[Setting\_<id>\]  
>   File=site.ini            die INI-Datei  
>   Block=DebugSettings      ihr Block  
>   Variable=DebugOutput     ihre Variable  
>   Type=bool                bool | enum | list | text | iplist | userlist  
>   Label=Debug output  
>   Group=output             eine aus \[DebugBarSettings\] Groups\[\]  
>   Help=One sentence on what it does.  
>   Values\[\]                 nur enum: die Werte, die sie annehmen darf  
>   On=enabled               nur bool: die für an und aus geschriebenen Wörter; Standard: die Wörter, die der  
>   Off=disabled             eigene Standard der Einstellung verwendet (enabled/disabled oder true/false)  
>
> Jeder Schreibvorgang läuft über die Engine von exp:ini (expIniEditor): Zeile für Zeile, mit Sicherung, unter Beibehaltung  
> des Eigentümers der Datei. Eine Erweiterung fügt ihre eigenen Einstellungen in ihrer settings/debugbar.ini.append.php hinzu:  
>
>   \[DebugBarSettings\]  
>   Settings\[\]=myext\_debug  
>   \[Setting\_myext\_debug\]  
>   File=myext.ini  
>   ...  

| Block | Einstellungen |
|---|---|
| [DebugBarSettings](DebugBarSettings.md) | 14 |
| [Setting_debug_output](Setting_debug_output.md) | 7 |
| [Setting_debug_mode](Setting_debug_mode.md) | 8 |
| [Setting_debug_redirection](Setting_debug_redirection.md) | 7 |
| [Setting_display_debug_warnings](Setting_display_debug_warnings.md) | 7 |
| [Setting_debug_log_only](Setting_debug_log_only.md) | 7 |
| [Setting_display_included_files](Setting_display_included_files.md) | 7 |
| [Setting_always_log](Setting_always_log.md) | 8 |
| [Setting_deprecated_log](Setting_deprecated_log.md) | 7 |
| [Setting_script_debug_output](Setting_script_debug_output.md) | 7 |
| [Setting_debug_toolbar](Setting_debug_toolbar.md) | 7 |
| [Setting_debug_bar_theme](Setting_debug_bar_theme.md) | 8 |
| [Setting_debug_by_ip](Setting_debug_by_ip.md) | 7 |
| [Setting_debug_ip_list](Setting_debug_ip_list.md) | 7 |
| [Setting_debug_by_user](Setting_debug_by_user.md) | 7 |
| [Setting_debug_user_list](Setting_debug_user_list.md) | 7 |
| [Setting_template_debug](Setting_template_debug.md) | 7 |
| [Setting_template_show_xhtml_code](Setting_template_show_xhtml_code.md) | 7 |
| [Setting_template_show_used_templates](Setting_template_show_used_templates.md) | 7 |
| [Setting_template_path_comments](Setting_template_path_comments.md) | 7 |
| [Setting_template_show_method_debug](Setting_template_show_method_debug.md) | 7 |
| [Setting_template_development_mode](Setting_template_development_mode.md) | 7 |
| [Setting_template_compile](Setting_template_compile.md) | 7 |
| [Setting_template_cache](Setting_template_cache.md) | 7 |
| [Setting_template_compile_accumulators](Setting_template_compile_accumulators.md) | 7 |
| [Setting_template_compile_timing_points](Setting_template_compile_timing_points.md) | 7 |
| [Setting_template_compile_comments](Setting_template_compile_comments.md) | 7 |
| [Setting_sql_output](Setting_sql_output.md) | 7 |
| [Setting_sql_query_analysis](Setting_sql_query_analysis.md) | 7 |
| [Setting_sql_slow_queries](Setting_sql_slow_queries.md) | 7 |
| [Setting_sql_debug_transactions](Setting_sql_debug_transactions.md) | 7 |
| [Setting_regional_debug](Setting_regional_debug.md) | 7 |
| [Setting_regional_development_mode](Setting_regional_development_mode.md) | 7 |
| [Setting_mail_debug_sending](Setting_mail_debug_sending.md) | 7 |
| [Setting_mail_debug_receiver](Setting_mail_debug_receiver.md) | 7 |
| [Setting_condition_debug](Setting_condition_debug.md) | 7 |
| [Setting_rest_debug](Setting_rest_debug.md) | 7 |
| [Setting_content_view_caching](Setting_content_view_caching.md) | 7 |
| [Preset_template_work](Preset_template_work.md) | 3 |
| [Preset_sql_tuning](Preset_sql_tuning.md) | 3 |
| [Preset_everything](Preset_everything.md) | 3 |
| [Preset_off](Preset_off.md) | 3 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
