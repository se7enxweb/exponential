---
title: Front Controller
audience: developer
category: standards
order: 318
summary: Ein Einstiegspunkt empfängt jede Anfrage, ermittelt den Siteaccess und leitet sie an eine Modulansicht weiter. 10 Instanzen in Exponential.
keywords: Entwurfsmuster, Front Controller, Anfrageverarbeitung, Musterkatalog, design pattern, front-controller, pattern catalogue
type: catalogue
---
# Front Controller

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Ein Einstiegspunkt empfängt jede Anfrage, ermittelt den Siteaccess und leitet sie an eine Modulansicht weiter.

| | |
| --- | --- |
| Gruppe | Verarbeitung von Anfragen |
| Instanzen | 10 |
| Erkannt an | Die Kernel-Klassen ezpKernel, ezpKernelWeb, ezpKernelTreeMenu, ezpKernelRest, eZModule und ihre Art. |
| Erweiterungspunkt | index.php erzeugt ezpKernel mit ezpKernelWeb; Velocity und FrankenPHP halten den Kernel im Speicher. |

## Klassen nach Bereich

### kernel/classes (1)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZSiteAccess` | class | `kernel/classes/ezsiteaccess.php:17` | Stellt Funktionen für die Siteaccess-Verarbeitung bereit | – |

### kernel/private (7)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpKernel` | class | `kernel/private/classes/ezpkernel.php:16` | Basisklasse des Exponential-Kernels. | – |
| `ezpKernelHandler` | interface | `kernel/private/interfaces/ezpkernelhandler.php:14` | Schnittstelle für Kernel-Handler | – |
| `ezpKernelRest` | class | `kernel/private/rest/classes/ezpkernelrest.php:11` | – | – |
| `ezpKernelTreeMenu` | class | `kernel/private/classes/ezpkerneltreemenu.php:11` | – | – |
| `ezpKernelWeb` | class | `kernel/private/classes/ezpkernelweb.php:16` | Stellt einen Kernel-Handler im Web-Kontext bereit | – |
| `ezpMvcRegexpRoute` | class extends ezcMvcRegexpRoute | `kernel/private/rest/classes/routes/regexp.php:16` | Überschreibung von ezcMvcRegexpRoute. | – |
| `ezpRestMvcController` | abstract class extends ezcMvcController | `kernel/private/rest/classes/controllers/rest_controller.php:15` | Abstrakte Klasse, die jeder REST-Controller erweitern muss | – |

### lib/ezutils (2)

[Alle Muster von lib/ezutils](bereich-lib-ezutils.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZModule` | class | `lib/ezutils/classes/ezmodule.php:36` | Die Klasse eZModule dient zum Instanziieren und Verwenden von Modulen und Ansichten. | – |
| `eZURI` | class | `lib/ezutils/classes/ezuri.php:32` | Bietet Zugriff auf die HTTP-URI | – |
