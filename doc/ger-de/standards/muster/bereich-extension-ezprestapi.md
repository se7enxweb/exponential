---
title: Muster in extension/ezprestapi
audience: developer
category: standards
order: 457
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezprestapi mit den Entwurfsmustern.
keywords: extension/ezprestapi, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezprestapi

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezprestapi` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (9)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\Ezprestapi\Ezrestcall` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/ezprestapi/classes/runnable/commands/php_ezrestcall.php:24` | Der Code von extension/ezprestapi/bin/php/ezrestcall.php, in eine Klasse verschoben (#207 Stufe 1). |
| `ezp7xRestApiProvider` | class implements ezpRestProviderInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/ezprestapi/classes/rest_provider.php:25` | Die Routen des ezp-Anbieters. |
| `ezp7xRestContentController` | class extends ezpRestMvcController | – | `extension/ezprestapi/classes/controllers/content.php:13` | Dieser Controller dient zum Ausliefern von Inhalten |
| `ezprestapiInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezprestapi/ezinfo.php:10` | – |
| `ezpRestApiViewController` | class implements ezpRestViewControllerInterface | – | `extension/ezprestapi/classes/view_controller.php:12` | – |
| `ezpRestAtomController` | class extends ezcMvcController | – | `extension/ezprestapi/classes/controllers/atom.php:18` | Controller-Klasse zum Erzeugen von Atom-Feeds der Inhaltsstruktur. |
| `ezpRestAtomDecorator` | class extends ezpRestFeedDecorator | [Dekorierer / Filter](dekorierer.md) | `extension/ezprestapi/classes/views/atom_decorator.php:10` | – |
| `ezpRestAtomView` | class extends ezcMvcView | – | `extension/ezprestapi/classes/views/atom.php:13` | Ansicht, die einen Atom-Feed der Ausgabe liefert |
| `ezpRestContentModel` | class extends ezpRestModel | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezprestapi/classes/models/rest_content_model.php:13` | Modellklasse mit der Backend-Methode für den REST-Inhaltscontroller |

## INI-Plugin-Punkte (2)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[ApiProvider] ProviderClass[ezp]` | `ezp7xRestApiProvider` | `extension/ezprestapi/settings/rest.ini.append.php:8` |
| `[ApiProvider] ProviderClass[ezpl]` | `ezp7xRestApiProvider` | `extension/ezprestapi/settings/rest.ini.append.php:9` |
