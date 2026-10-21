---
title: Muster in kernel/classes/classicmenu
audience: developer
category: standards
order: 483
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/classicmenu mit den Entwurfsmustern.
keywords: kernel/classes/classicmenu, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/classicmenu

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/classicmenu` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expClassicMenuSettings` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/classicmenu/expclassicmenusettings.php:22` | Liest und schreibt die klassischen Menüeinstellungen eines Siteaccess: menu.ini [SelectedMenu] CurrentMenu, TopMenu und LeftMenu, die die Seitenlayouts de… |
| `expClassicMenuSiteAccessInspector` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/classicmenu/expclassicmenusiteaccessinspector.php:28` | Gibt für einen Siteaccess an, ob seine Seiten die klassischen Menüeinstellungen verwenden (menu.ini [SelectedMenu]). |
