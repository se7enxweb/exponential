---
title: Muster in extension/explayouts_ui
audience: developer
category: standards
order: 422
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/explayouts_ui mit den Entwurfsmustern.
keywords: extension/explayouts_ui, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/explayouts_ui

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/explayouts_ui` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (20)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLayoutsUiRuleInput` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts_ui/classes/explayoutsuiruleinput.php:9` | Die Ziele und Bedingungen einer Zuordnung, wie sie die Regelformulare senden (TargetType[]/TargetValue[], ConditionType[]/ConditionValue[]): nur die Typen, di… |
| `expLayoutsUiSchemaStatement` | class | – | `extension/explayouts_ui/classes/explayoutsuischemastatement.php:10` | Zu welcher explayouts_*-Tabelle eine Anweisung einer Schemadatei gehört, damit die Setup-Seite die Anweisungen einer bereits vorhandenen Tabelle unverändert lassen kann: es… |
| `expLayoutsUiTemplateEditor` | class extends expLayoutsTemplateEditor | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts_ui/classes/explayoutsuitemplateeditor.php:17` | Der Template-Editor von explayouts_ui (explayouts_ui/template_editor), aufbauend auf expLayoutsTemplateEditor von explayouts (der entscheidet, welche Ordner … |
| `expLayoutsUiTemplateOperators` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/explayouts_ui/classes/explayoutsuitemplateoperators.php:11` | Template-Operatoren von explayouts_ui. |
| `explayouts_uiInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts_ui/ezinfo.php:12` | – |
| `Exponential\Command\Extension\ExplayoutsUi\Layouts` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/explayouts_ui/classes/runnable/commands/php_layouts.php:17` | exp:layouts (ext:explayouts_ui:layouts) - Layouts, geteilte Layouts und ihre Zuordnungen auflisten, exportieren und importieren. |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\BlockEdit` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/block_edit.php:10` | Der Code von extension/explayouts_ui/modules/explayouts_ui/block_edit.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Components` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/components.php:42` | Wandelt einen view_type-Bezeichner wie „features_style_2“ in eine lesbare Stilbezeichnung um. |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Dashboard` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/dashboard.php:22` | Der Code von extension/explayouts_ui/modules/explayouts_ui/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\LayoutCreate` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/layout_create.php:10` | Der Code von extension/explayouts_ui/modules/explayouts_ui/layout_create.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\LayoutEdit` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/layout_edit.php:77` | Der Code von extension/explayouts_ui/modules/explayouts_ui/layout_edit.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\LayoutList` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/layout_list.php:91` | Der Code von extension/explayouts_ui/modules/explayouts_ui/layout_list.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Preview` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/preview.php:105` | Wählen Sie einen Knoten, gegen den das Layout in der Vorschau angezeigt wird. |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\RuleEdit` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/rule_edit.php:10` | Der Code von extension/explayouts_ui/modules/explayouts_ui/rule_edit.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\RuleList` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/rule_list.php:10` | Der Code von extension/explayouts_ui/modules/explayouts_ui/rule_list.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Setup` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/setup.php:10` | Der Code von extension/explayouts_ui/modules/explayouts_ui/setup.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\SharedLayoutsList` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/shared_layouts_list.php:91` | Der Code von extension/explayouts_ui/modules/explayouts_ui/shared_layouts_list.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\TemplateEditor` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/template_editor.php:40` | explayouts_ui/template_editor: der Template-Editor der Layout-Templates innerhalb der Seite der Administrationsoberfläche (oberes Menü, Breadcrumb, die linke Layouts-… |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\TransferExport` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/transfer_export.php:16` | explayouts_ui/transfer_export: exportiert Layouts, geteilte Layouts und Regeln in eine Layout-Transferdatei (expLayoutsCoreTransferService) und sendet sie als… |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\TransferImport` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/transfer_import.php:18` | explayouts_ui/transfer_import: importiert eine Layout-Transferdatei (expLayoutsCoreTransferService). |

## Modulansichten (14)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/explayouts_ui/layout_list` | `layout_list.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:7` |
| `/explayouts_ui/layout_create` | `layout_create.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:14` |
| `/explayouts_ui/layout_edit` | `layout_edit.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:21` |
| `/explayouts_ui/rule_list` | `rule_list.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:28` |
| `/explayouts_ui/shared_layouts_list` | `shared_layouts_list.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:35` |
| `/explayouts_ui/rule_edit` | `rule_edit.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:42` |
| `/explayouts_ui/block_edit` | `block_edit.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:49` |
| `/explayouts_ui/dashboard` | `dashboard.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:56` |
| `/explayouts_ui/layout_preview` | `preview.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:63` |
| `/explayouts_ui/setup` | `setup.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:70` |
| `/explayouts_ui/template_editor` | `template_editor.php` | read, edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:77` |
| `/explayouts_ui/transfer_export` | `transfer_export.php` | transfer_export | `extension/explayouts_ui/modules/explayouts_ui/module.php:86` |
| `/explayouts_ui/transfer_import` | `transfer_import.php` | transfer_import | `extension/explayouts_ui/modules/explayouts_ui/module.php:93` |
| `/explayouts_ui/components` | `components.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:100` |

## Richtlinienfunktionen (4)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `explayouts_ui/read` | – | `extension/explayouts_ui/modules/explayouts_ui/module.php:108` |
| `explayouts_ui/edit` | – | `extension/explayouts_ui/modules/explayouts_ui/module.php:109` |
| `explayouts_ui/transfer_export` | – | `extension/explayouts_ui/modules/explayouts_ui/module.php:110` |
| `explayouts_ui/transfer_import` | – | `extension/explayouts_ui/modules/explayouts_ui/module.php:111` |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| explayouts_ui_has_template | `expLayoutsUiTemplateOperators` | `extension/explayouts_ui/autoloads/eztemplateautoload.php:3` |
