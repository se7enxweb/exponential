---
title: Patterns in extension/explayouts_ui
audience: developer
category: standards
order: 422
summary: Every class, view, fetch function, operator and plugin point of extension/explayouts_ui with its design patterns.
keywords: extension/explayouts_ui, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/explayouts_ui

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/explayouts_ui` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (20)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expLayoutsUiRuleInput` | class | [Utility (static helper)](utility.md) | `extension/explayouts_ui/classes/explayoutsuiruleinput.php:9` | The targets and conditions of a mapping as posted by the rule forms (TargetType[]/TargetValue[], ConditionType[]/ConditionValue[]): only the types th… |
| `expLayoutsUiSchemaStatement` | class | – | `extension/explayouts_ui/classes/explayoutsuischemastatement.php:10` | Which explayouts_* table a statement of a schema file belongs to, so the setup page can leave the statements of a table that exists already alone: it… |
| `expLayoutsUiTemplateEditor` | class extends expLayoutsTemplateEditor | [Utility (static helper)](utility.md) | `extension/explayouts_ui/classes/explayoutsuitemplateeditor.php:17` | The template editor of explayouts_ui (explayouts_ui/template_editor), on top of explayouts' expLayoutsTemplateEditor (which decides which folders may… |
| `expLayoutsUiTemplateOperators` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/explayouts_ui/classes/explayoutsuitemplateoperators.php:11` | Template operators of explayouts_ui. |
| `explayouts_uiInfo` | class | [Value object / struct](value-object.md) | `extension/explayouts_ui/ezinfo.php:12` | – |
| `Exponential\Command\Extension\ExplayoutsUi\Layouts` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/explayouts_ui/classes/runnable/commands/php_layouts.php:17` | exp:layouts (ext:explayouts_ui:layouts) - list, export and import layouts, shared layouts and their mappings. |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\BlockEdit` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/block_edit.php:10` | The code of extension/explayouts_ui/modules/explayouts_ui/block_edit.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Components` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/components.php:42` | Converts a view_type identifier like "features_style_2" into a human readable style label. |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Dashboard` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/dashboard.php:22` | The code of extension/explayouts_ui/modules/explayouts_ui/dashboard.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\LayoutCreate` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/layout_create.php:10` | The code of extension/explayouts_ui/modules/explayouts_ui/layout_create.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\LayoutEdit` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/layout_edit.php:77` | The code of extension/explayouts_ui/modules/explayouts_ui/layout_edit.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\LayoutList` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/layout_list.php:91` | The code of extension/explayouts_ui/modules/explayouts_ui/layout_list.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Preview` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/preview.php:105` | Pick a node to preview the layout against. |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\RuleEdit` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/rule_edit.php:10` | The code of extension/explayouts_ui/modules/explayouts_ui/rule_edit.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\RuleList` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/rule_list.php:10` | The code of extension/explayouts_ui/modules/explayouts_ui/rule_list.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Setup` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/setup.php:10` | The code of extension/explayouts_ui/modules/explayouts_ui/setup.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\SharedLayoutsList` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/shared_layouts_list.php:91` | The code of extension/explayouts_ui/modules/explayouts_ui/shared_layouts_list.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\TemplateEditor` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/template_editor.php:40` | explayouts_ui/template_editor: the template editor of the layout templates, inside the administration's page (top menu, breadcrumb, the Layouts left… |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\TransferExport` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/transfer_export.php:16` | explayouts_ui/transfer_export: exports layouts, shared layouts and rules into a layouts transfer file (expLayoutsCoreTransferService) and sends it as… |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\TransferImport` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/transfer_import.php:18` | explayouts_ui/transfer_import: imports a layouts transfer file (expLayoutsCoreTransferService). |

## Module views (14)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
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

## Policy functions (4)

| Function | Limitations | Defined in |
|---|---|---|
| `explayouts_ui/read` | – | `extension/explayouts_ui/modules/explayouts_ui/module.php:108` |
| `explayouts_ui/edit` | – | `extension/explayouts_ui/modules/explayouts_ui/module.php:109` |
| `explayouts_ui/transfer_export` | – | `extension/explayouts_ui/modules/explayouts_ui/module.php:110` |
| `explayouts_ui/transfer_import` | – | `extension/explayouts_ui/modules/explayouts_ui/module.php:111` |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| explayouts_ui_has_template | `expLayoutsUiTemplateOperators` | `extension/explayouts_ui/autoloads/eztemplateautoload.php:3` |
