---
title: Muster in extension/explayouts_standard
audience: developer
category: standards
order: 420
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/explayouts_standard mit den Entwurfsmustern.
keywords: extension/explayouts_standard, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/explayouts_standard

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/explayouts_standard` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (23)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLayoutsStandardAccordionBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardaccordionblockhandler.php:3` | – |
| `expLayoutsStandardAlertBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardalertblockhandler.php:3` | – |
| `expLayoutsStandardBadgeBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardbadgeblockhandler.php:3` | – |
| `expLayoutsStandardButtonBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardbuttonblockhandler.php:3` | – |
| `expLayoutsStandardCardBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardcardblockhandler.php:3` | – |
| `expLayoutsStandardCarouselBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardcarouselblockhandler.php:3` | – |
| `expLayoutsStandardDividerBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandarddividerblockhandler.php:3` | – |
| `expLayoutsStandardGalleryBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardgalleryblockhandler.php:3` | – |
| `expLayoutsStandardGridBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardgridblockhandler.php:3` | – |
| `expLayoutsStandardHtmlSnippetBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardhtmlsnippetblockhandler.php:2` | – |
| `expLayoutsStandardImageBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardimageblockhandler.php:3` | – |
| `expLayoutsStandardListBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardlistblockhandler.php:3` | – |
| `expLayoutsStandardMapBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardmapblockhandler.php:3` | – |
| `expLayoutsStandardMarkdownBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardmarkdownblockhandler.php:2` | – |
| `expLayoutsStandardProgressBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardprogressblockhandler.php:3` | – |
| `expLayoutsStandardQuoteBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardquoteblockhandler.php:3` | – |
| `expLayoutsStandardSingleBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardsingleblockhandler.php:3` | – |
| `expLayoutsStandardSpacerBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardspacerblockhandler.php:3` | – |
| `expLayoutsStandardTabsBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardtabsblockhandler.php:3` | – |
| `expLayoutsStandardTextBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardtextblockhandler.php:3` | – |
| `expLayoutsStandardTitleBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardtitleblockhandler.php:3` | – |
| `expLayoutsStandardVideoBlockHandler` | class implements expLayoutsBlockHandlerInterface | [Strategie / Handler](strategie-handler.md) | `extension/explayouts_standard/classes/explayoutsstandardvideoblockhandler.php:3` | – |
| `explayouts_standardInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts_standard/ezinfo.php:12` | – |
