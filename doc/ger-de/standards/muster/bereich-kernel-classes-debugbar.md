---
title: Muster in kernel/classes/debugbar
audience: developer
category: standards
order: 498
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/debugbar mit den Entwurfsmustern.
keywords: kernel/classes/debugbar, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/debugbar

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/debugbar` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (8)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expDebugBarIPList` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/debugbar/expdebugbariplist.php:28` | Eine Zeile von DebugIPList[] ist ein Eintrag: |
| `expDebugBarLog` | class | – | `kernel/classes/debugbar/expdebugbarlog.php:19` | Ein JSON-Objekt pro Zeile in var//log/debugbar.log ([DebugBarSettings] LogFile in debugbar.ini): wer (Benutzer-ID, Login, Adresse), was (Einstellung, Datei, B… |
| `expDebugBarPanelInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/classes/debugbar/expdebugbarpanelinterface.php:26` | Ein Panel der Exp-Debug-Leiste aus einer Extension: ein eigener Reiter nach Memory, optional ein Chip in der Zusammenfassung und Daten im JSON der Seite. |
| `expDebugBarRegistry` | class | [Registry](registry.md) | `kernel/classes/debugbar/expdebugbarregistry.php:20` | Liest settings/debugbar.ini (und die debugbar.ini.append.php jeder Extension, zusammengeführt von eZINI): |
| `expDebugBarServerFunctions` | class extends ezjscServerFunctions | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/debugbar/expdebugbarserverfunctions.php:28` | Die ezjscore-Serverfunktionen der Exp-Debug-Leiste, registriert als [ezjscServer_expdebugbar] in extension/ezjscore/settings/ezjscore.ini: |
| `expDebugBarSettings` | class | – | `kernel/classes/debugbar/expdebugbarsettings.php:25` | Für einen Siteaccess (standardmäßig den aktuellen): |
| `expDebugBarSummary` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/debugbar/expdebugbarsummary.php:20` | Sammelt, was die Anfrage gekostet hat, aus dem, was eZDebug bereits gemessen hat: Seitenzeit, SQL-Anweisungen und ihre Zeit (die *_query-Akkumulatoren der Datenb… |
| `expRenderStats` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/debugbar/exprenderstats.php:19` | Sammelt für die gerade bediente Anfrage, was der Reiter Rendering der Debug-Leiste zeigt: den Controller (Modul, Ansicht, Skript und Ansichtsklasse), was der Con… |
