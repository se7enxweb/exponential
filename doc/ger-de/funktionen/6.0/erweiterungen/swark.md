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
- [Template-Operatoren für Zeichenketten](../template-operatoren-fuer-zeichenketten.md)
- [Abstraktionen für Kommandozeile, Cronjobs und Ansichten](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/swark.md) und [Versionshinweise](../../../../changelogs/extensions/swark.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/swark.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- [Monat: 2026-09 (alle Extensions)](../../../geschichte/erweiterungen/monate/2026-09.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
