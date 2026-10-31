---
title: Muster in extension/cjw_themes_jumper
audience: developer
category: standards
order: 407
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/cjw_themes_jumper mit den Entwurfsmustern.
keywords: extension/cjw_themes_jumper, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/cjw_themes_jumper

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/cjw_themes_jumper` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (6)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `cjwJumperFormHoneypot` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/cjw_themes_jumper/classes/cjwjumperformhoneypot.php:20` | Der Honeypot der Feedbackformulare des Design-Starters (Spamschutz, zusammen mit dem Formular-Token von ezformtoken). |
| `cjwJumperImagineImageFactory` | class extends eZImageFactory | [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/cjw_themes_jumper/classes/cjwjumperimagineimagefactory.php:10` | Bild-Handler-Fabrik des Konverters cjwImagine (image.ini [cjwImagine] Handler=cjwJumperImagineImageFactory): erzeugt cjwJumperImagineImageHandle… |
| `cjwJumperImagineImageHandler` | class extends eZImageHandler | [Fabrik und statische Fabrikmethoden](fabrik.md), [Strategie / Handler](strategie-handler.md) | `extension/cjw_themes_jumper/classes/cjwjumperimagineimagehandler.php:28` | Bildkonverter für die cjw_*-Aliase des Design-Starters: zeichnet sie genau so, wie der Nexus-v2-Starter seine eigenen Aliase zeichnet (Imagine 1.x mit… |
| `cjwJumperPagedCollection` | class | – | `extension/cjw_themes_jumper/classes/cjwjumperpagedcollection.php:23` | Eine Seite eines seitenweise geblätterten Layouts-Listen- oder Rasterblocks des Design-Starters (v2: die Ajax-Block-Route seiner Layouts-Engine). |
| `cjwOsmTileProxy` | class | [Adapter / Bridge / Wrapper](adapter.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/cjw_themes_jumper/classes/cjwosmtileproxy.php:13` | Die Kartenkacheln des Moduls osm_proxy: Validierung, der lokale Kachel-Cache und der Abruf vom Kachelserver. |
| `cjw_themes_jumperInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/cjw_themes_jumper/ezinfo.php:7` | – |

## Modulansichten (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/cjw_paging/page` | `page.php` | – | `extension/cjw_themes_jumper/modules/cjw_paging/module.php:16` |
| `/osm_proxy/tiles` | `tiles.php` | tiles | `extension/cjw_themes_jumper/modules/osm_proxy/module.php:16` |

## Richtlinienfunktionen (1)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `osm_proxy/tiles` | – | `extension/cjw_themes_jumper/modules/osm_proxy/module.php:23` |

## INI-Plugin-Punkte (2)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[cjwImagine] Handler` | `cjwJumperImagineImageFactory` | `extension/cjw_themes_jumper/settings/image.ini.append.php:40` |
| `[BlockDefinition_ezcontent_field] Handler` | `expLayoutsTitleBlockHandler` | `extension/cjw_themes_jumper/settings/siteaccess/cjw_starter/explayouts.ini.append.php:50` |
