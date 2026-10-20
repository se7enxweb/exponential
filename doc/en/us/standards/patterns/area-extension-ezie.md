---
title: Patterns in extension/ezie
audience: developer
category: standards
order: 442
summary: Every class, view, fetch function, operator and plugin point of extension/ezie with its design patterns.
keywords: extension/ezie, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezie

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezie` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (32)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezie\Ezie\FilterBrightness` | class extends ModuleView | [Decorator / filter](decorator.md), [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/filter_brightness.php:23` | The code of extension/ezie/modules/ezie/filter_brightness.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezie\Ezie\FilterBw` | class extends ModuleView | [Decorator / filter](decorator.md), [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/filter_bw.php:23` | The code of extension/ezie/modules/ezie/filter_bw.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezie\Ezie\FilterContrast` | class extends ModuleView | [Decorator / filter](decorator.md), [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/filter_contrast.php:23` | The code of extension/ezie/modules/ezie/filter_contrast.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezie\Ezie\FilterSepia` | class extends ModuleView | [Decorator / filter](decorator.md), [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/filter_sepia.php:23` | The code of extension/ezie/modules/ezie/filter_sepia.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezie\Ezie\NoSaveAndQuit` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/no_save_and_quit.php:26` | The code of extension/ezie/modules/ezie/no_save_and_quit.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezie\Ezie\Prepare` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/prepare.php:24` | The code of extension/ezie/modules/ezie/prepare.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezie\Ezie\SaveAndQuit` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/save_and_quit.php:27` | The code of extension/ezie/modules/ezie/save_and_quit.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezie\Ezie\ToolCrop` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/tool_crop.php:23` | The code of extension/ezie/modules/ezie/tool_crop.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezie\Ezie\ToolFlipHor` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/tool_flip_hor.php:23` | The code of extension/ezie/modules/ezie/tool_flip_hor.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezie\Ezie\ToolFlipVer` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/tool_flip_ver.php:23` | The code of extension/ezie/modules/ezie/tool_flip_ver.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezie\Ezie\ToolPixelate` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/tool_pixelate.php:23` | The code of extension/ezie/modules/ezie/tool_pixelate.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezie\Ezie\ToolRotation` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/tool_rotation.php:23` | The code of extension/ezie/modules/ezie/tool_rotation.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezie\Ezie\ToolWatermark` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezie/classes/runnable/views/ezie/tool_watermark.php:23` | The code of extension/ezie/modules/ezie/tool_watermark.php, moved into a class (#207 stage 1). |
| `eZIEEzcConversions` | interface | [Interface (contract)](interface.md) | `extension/ezie/classes/interfaces/conversions.php:11` | – |
| `eZIEEzcGDHandler` | class extends ezcImageGdHandler implements eZIEEzcConversions | [Strategy / handler](strategy-handler.md) | `extension/ezie/classes/handlers/gd.php:11` | – |
| `eZIEezcImageConverter` | class | [Strategy / handler](strategy-handler.md) | `extension/ezie/classes/image_converter.php:12` | – |
| `eZIEEzcImageMagickHandler` | class extends ezcImageImagemagickHandler implements eZIEEzcConversions | [Strategy / handler](strategy-handler.md) | `extension/ezie/classes/handlers/magick.php:13` | Implements the methods used by the editor (rotate, flip, etc) for image magick. |
| `eZIEImageAction` | class | – | `extension/ezie/classes/image_action.php:11` | – |
| `eZIEImageAnalyzer` | class extends ezcImageAnalyzer | – | `extension/ezie/classes/image_analyzer.php:12` | This class overrides ezcImageAnalyzer in order to support the Exponential cluster constraints |
| `eZIEImageFilterBrightness` | class extends eZIEImageAction | [Decorator / filter](decorator.md) | `extension/ezie/classes/image_filter_brightness.php:11` | – |
| `eZIEImageFilterBW` | class extends eZIEImageAction | [Decorator / filter](decorator.md) | `extension/ezie/classes/image_filter_bw.php:12` | – |
| `eZIEImageFilterContrast` | class extends eZIEImageAction | [Decorator / filter](decorator.md) | `extension/ezie/classes/image_filter_contrast.php:11` | – |
| `eZIEImageFilterSepia` | class extends eZIEImageAction | [Decorator / filter](decorator.md) | `extension/ezie/classes/image_filter_sepia.php:11` | – |
| `eZIEImagePreAction` | class | – | `extension/ezie/classes/image_pre_action.php:11` | – |
| `eZIEImageToolCrop` | class extends eZIEImageAction | – | `extension/ezie/classes/image_tool_crop.php:11` | – |
| `eZIEImageToolFlipHorizontally` | class extends eZIEImageAction | – | `extension/ezie/classes/image_tool_flip_horizontally.php:11` | – |
| `eZIEImageToolFlipVertically` | class extends eZIEImageAction | – | `extension/ezie/classes/image_tool_flip_vertically.php:11` | – |
| `eZIEImageToolPixelate` | class extends eZIEImageAction | – | `extension/ezie/classes/image_tool_pixelate.php:11` | – |
| `eZIEImageToolResize` | class extends eZIEImageAction | [Utility (static helper)](utility.md) | `extension/ezie/classes/image_tool_resize.php:11` | – |
| `eZIEImageToolRotation` | class extends eZIEImageAction | – | `extension/ezie/classes/image_tool_rotation.php:11` | – |
| `eZIEImageToolWatermark` | class extends eZIEImageAction | [Utility (static helper)](utility.md) | `extension/ezie/classes/image_tool_watermark.php:11` | – |
| `ezieInfo` | class | [Value object / struct](value-object.md) | `extension/ezie/ezinfo.php:11` | – |

## Module views (13)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezie/prepare` | `prepare.php` | – | `extension/ezie/modules/ezie/module.php:16` |
| `/ezie/filter_bw` | `filter_bw.php` | – | `extension/ezie/modules/ezie/module.php:21` |
| `/ezie/filter_sepia` | `filter_sepia.php` | – | `extension/ezie/modules/ezie/module.php:22` |
| `/ezie/filter_contrast` | `filter_contrast.php` | – | `extension/ezie/modules/ezie/module.php:23` |
| `/ezie/filter_brightness` | `filter_brightness.php` | – | `extension/ezie/modules/ezie/module.php:24` |
| `/ezie/tool_flip_hor` | `tool_flip_hor.php` | – | `extension/ezie/modules/ezie/module.php:27` |
| `/ezie/tool_flip_ver` | `tool_flip_ver.php` | – | `extension/ezie/modules/ezie/module.php:28` |
| `/ezie/tool_rotation` | `tool_rotation.php` | – | `extension/ezie/modules/ezie/module.php:29` |
| `/ezie/tool_pixelate` | `tool_pixelate.php` | – | `extension/ezie/modules/ezie/module.php:30` |
| `/ezie/tool_crop` | `tool_crop.php` | – | `extension/ezie/modules/ezie/module.php:31` |
| `/ezie/tool_watermark` | `tool_watermark.php` | – | `extension/ezie/modules/ezie/module.php:32` |
| `/ezie/no_save_and_quit` | `no_save_and_quit.php` | – | `extension/ezie/modules/ezie/module.php:35` |
| `/ezie/save_and_quit` | `save_and_quit.php` | – | `extension/ezie/modules/ezie/module.php:36` |
