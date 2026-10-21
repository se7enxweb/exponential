---
title: Muster in extension/sevenx_themes_media
audience: developer
category: standards
order: 470
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/sevenx_themes_media mit den Entwurfsmustern.
keywords: extension/sevenx_themes_media, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/sevenx_themes_media

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/sevenx_themes_media` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (7)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\SevenxThemesMedia\InfoCollection\Submit` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_themes_media/classes/runnable/views/info-collection/submit.php:10` | Der Code von extension/sevenx_themes_media/modules/info-collection/submit.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\SevenxThemesMedia\InfoCollection\Viewmodal` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_themes_media/classes/runnable/views/info-collection/viewmodal.php:10` | Der Code von extension/sevenx_themes_media/modules/info-collection/viewmodal.php, in eine Klasse verschoben (#207 Stufe 1). |
| `sevenxThemesMediaField` | class implements ArrayAccess | [Iterator](iterator.md) | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php:3` | – |
| `sevenxThemesMediaOperators` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php:58` | – |
| `sevenxThemesMediaXHTMLXMLOutput` | class extends eZXHTMLXMLOutput | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/sevenx_themes_media/classes/sevenxthemesmediaxhtmlxmloutput.php:11` | Erweitert eZXHTMLXMLOutput, um die alten XML-Tag-Namen zu registrieren, die der importierte Inhalt verwendet (title, itemizedlist, listitem, orderedlist, literallayo… |
| `sevenxThemesMediaYouTube` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/sevenx_themes_media/classes/sevenxthemesmediayoutube.php:22` | YouTube, ohne Google zu kontaktieren, bevor der Besucher das Abspielen wählt. |
| `sevenx_themes_mediaInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/sevenx_themes_media/ezinfo.php:13` | – |

## Modulansichten (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/info-collection/view-modal` | `viewmodal.php` | read | `extension/sevenx_themes_media/modules/info-collection/module.php:6` |
| `/info-collection/submit` | `submit.php` | read | `extension/sevenx_themes_media/modules/info-collection/module.php:13` |

## Richtlinienfunktionen (1)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `info-collection/read` | – | `extension/sevenx_themes_media/modules/info-collection/module.php:21` |

## INI-Plugin-Punkte (3)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[BlockDefinition_product_highlight] Handler` | `expLayoutsListBlockHandler` | `extension/sevenx_themes_media/settings/explayouts.ini.append.php:16` |
| `[OutputSettings] HandlerClass` | `sevenxThemesMediaXHTMLXMLOutput` | `extension/sevenx_themes_media/settings/ezxml.ini.append.php:4` |
| `[ezjscServer_expajaxloadmore] Class` | `expLayoutsAjaxLoadMoreServer` | `extension/sevenx_themes_media/settings/siteaccess/site/ezjscore.ini.append.php:15` |
