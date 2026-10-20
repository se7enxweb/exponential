# swark: Template-Operatoren und Workflow-Ereignisse, die fehlen

Diese Seite ist für Template-Autoren und Website-Entwickler. `swark` fügt viele Template-Operatoren und zwei
Workflow-Ereignisse hinzu, die oft gebraucht werden, im Kernel aber fehlen. Sie stammt von Seeds Consulting AS (2008)
und Brookins Consulting.

## Operatoren

`add_view_parameters`, `array_search`, `arsort`, `asort`, `charset`, `clear_object_cache`, `cookie`,
`current_layout`, `current_siteaccess`, `debug`, `debug_attributes`, `is_post_request`, `json_encode`, `krsort`,
`ksort`, `ltrim`, `modify_view_parameter`, `preg_match`, `preg_replace`, `range`, `redirect`,
`remove_array_element`, `return`, `rsort`, `rtrim`, `serialize`, `server`, `set_array_element`, `shortenw`,
`shuffle`, `sort`, `split_by_length`, `str_replace`, `strpos`, `strrpos` und weitere.

Jeder Operator hat eine Seite im Ordner `doc/operators` der Extension.

## Workflow-Ereignisse

| Ereignis | Was es tut | Einstellung |
|---|---|---|
| **AutoPriority** | Gibt einem Objekt beim Veröffentlichen eine Priorität eine Stufe über der seiner Geschwister. | `swark.ini [AutoPriority] PriorityIncrement=10` |
| **DeferToCron** | Verschiebt einen Workflow in den Cronjob. | keine |

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.0.2 | 7. Januar 2024 | `composer.json`. |
| 1.0.3 | 27. September 2026 | `ezinfo.php` hat ein statisches `info()` und gibt Version, Lizenz und Website an; die Dokumentation schreibt ihren Zeilenumbruch als `<br>`. |
| 1.0.4 | 30. September 2026 | Die Beschreibung nennt Exponential. |

## Verwandte Seiten

- [owsimpleoperator](owsimpleoperator.md)
- [Template-Operatoren für Zeichenketten](../../../../eng-us/features/6.0/string-template-operators.md)
- [Abstraktionen für Kommandozeile, Cronjobs und Ansichten](../../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../../eng-us/history/extensions/swark.md) und [Versionshinweise](../../../../changelogs/extensions/swark.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/swark.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- [Monat: 2026-09 (alle Extensions)](../../../../eng-us/history/extensions/months/2026-09.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
