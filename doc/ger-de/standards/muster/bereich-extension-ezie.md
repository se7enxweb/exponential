---
title: Muster in extension/ezie
audience: developer
category: standards
order: 442
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezie mit den Entwurfsmustern.
keywords: extension/ezie, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezie

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezie` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (32)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezie\Ezie\FilterBrightness` | class extends ModuleView | [Dekorierer / Filter](dekorierer.md), [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/filter_brightness.php:23` | Der Code von extension/ezie/modules/ezie/filter_brightness.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezie\Ezie\FilterBw` | class extends ModuleView | [Dekorierer / Filter](dekorierer.md), [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/filter_bw.php:23` | Der Code von extension/ezie/modules/ezie/filter_bw.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezie\Ezie\FilterContrast` | class extends ModuleView | [Dekorierer / Filter](dekorierer.md), [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/filter_contrast.php:23` | Der Code von extension/ezie/modules/ezie/filter_contrast.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezie\Ezie\FilterSepia` | class extends ModuleView | [Dekorierer / Filter](dekorierer.md), [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/filter_sepia.php:23` | Der Code von extension/ezie/modules/ezie/filter_sepia.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezie\Ezie\NoSaveAndQuit` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/no_save_and_quit.php:26` | Der Code von extension/ezie/modules/ezie/no_save_and_quit.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezie\Ezie\Prepare` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/prepare.php:24` | Der Code von extension/ezie/modules/ezie/prepare.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezie\Ezie\SaveAndQuit` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/save_and_quit.php:27` | Der Code von extension/ezie/modules/ezie/save_and_quit.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezie\Ezie\ToolCrop` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/tool_crop.php:23` | Der Code von extension/ezie/modules/ezie/tool_crop.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezie\Ezie\ToolFlipHor` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/tool_flip_hor.php:23` | Der Code von extension/ezie/modules/ezie/tool_flip_hor.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezie\Ezie\ToolFlipVer` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/tool_flip_ver.php:23` | Der Code von extension/ezie/modules/ezie/tool_flip_ver.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezie\Ezie\ToolPixelate` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/tool_pixelate.php:23` | Der Code von extension/ezie/modules/ezie/tool_pixelate.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezie\Ezie\ToolRotation` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/tool_rotation.php:23` | Der Code von extension/ezie/modules/ezie/tool_rotation.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezie\Ezie\ToolWatermark` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezie/classes/runnable/views/ezie/tool_watermark.php:23` | Der Code von extension/ezie/modules/ezie/tool_watermark.php, in eine Klasse verschoben (#207 Stufe 1). |
| `eZIEEzcConversions` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/ezie/classes/interfaces/conversions.php:11` | – |
| `eZIEEzcGDHandler` | class extends ezcImageGdHandler implements eZIEEzcConversions | [Strategie / Handler](strategie-handler.md) | `extension/ezie/classes/handlers/gd.php:11` | – |
| `eZIEezcImageConverter` | class | [Strategie / Handler](strategie-handler.md) | `extension/ezie/classes/image_converter.php:12` | – |
| `eZIEEzcImageMagickHandler` | class extends ezcImageImagemagickHandler implements eZIEEzcConversions | [Strategie / Handler](strategie-handler.md) | `extension/ezie/classes/handlers/magick.php:13` | Implementiert die Methoden, die der Editor verwendet (drehen, spiegeln usw.), für ImageMagick. |
| `eZIEImageAction` | class | – | `extension/ezie/classes/image_action.php:11` | – |
| `eZIEImageAnalyzer` | class extends ezcImageAnalyzer | – | `extension/ezie/classes/image_analyzer.php:12` | Diese Klasse überschreibt ezcImageAnalyzer, um die Cluster-Einschränkungen von Exponential zu unterstützen |
| `eZIEImageFilterBrightness` | class extends eZIEImageAction | [Dekorierer / Filter](dekorierer.md) | `extension/ezie/classes/image_filter_brightness.php:11` | – |
| `eZIEImageFilterBW` | class extends eZIEImageAction | [Dekorierer / Filter](dekorierer.md) | `extension/ezie/classes/image_filter_bw.php:12` | – |
| `eZIEImageFilterContrast` | class extends eZIEImageAction | [Dekorierer / Filter](dekorierer.md) | `extension/ezie/classes/image_filter_contrast.php:11` | – |
| `eZIEImageFilterSepia` | class extends eZIEImageAction | [Dekorierer / Filter](dekorierer.md) | `extension/ezie/classes/image_filter_sepia.php:11` | – |
| `eZIEImagePreAction` | class | – | `extension/ezie/classes/image_pre_action.php:11` | – |
| `eZIEImageToolCrop` | class extends eZIEImageAction | – | `extension/ezie/classes/image_tool_crop.php:11` | – |
| `eZIEImageToolFlipHorizontally` | class extends eZIEImageAction | – | `extension/ezie/classes/image_tool_flip_horizontally.php:11` | – |
| `eZIEImageToolFlipVertically` | class extends eZIEImageAction | – | `extension/ezie/classes/image_tool_flip_vertically.php:11` | – |
| `eZIEImageToolPixelate` | class extends eZIEImageAction | – | `extension/ezie/classes/image_tool_pixelate.php:11` | – |
| `eZIEImageToolResize` | class extends eZIEImageAction | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezie/classes/image_tool_resize.php:11` | – |
| `eZIEImageToolRotation` | class extends eZIEImageAction | – | `extension/ezie/classes/image_tool_rotation.php:11` | – |
| `eZIEImageToolWatermark` | class extends eZIEImageAction | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezie/classes/image_tool_watermark.php:11` | – |
| `ezieInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezie/ezinfo.php:11` | – |

## Modulansichten (13)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
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
