---
title: Muster in kernel/classes/uiswitch
audience: developer
category: standards
order: 508
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/uiswitch mit den Entwurfsmustern.
keywords: kernel/classes/uiswitch, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/uiswitch

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/uiswitch` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (6)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expUISwitch` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/uiswitch/expuiswitch.php:28` | Das UI-Umschalt-Framework: eine Registry von Anbietern (uiswitch.ini [UISwitchSettings] ProviderList[]), die in der Sitzung gespeicherten Auswahlen des Benutzers und die ea… |
| `expUISwitchDesignProvider` | class extends expUISwitchProvider | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/uiswitch/expuiswitchdesignprovider.php:25` | Das Design der Administrationsoberfläche (das „UI-Chrome“): admin4l, admin4, das klassische Admin-Design, AdminUI oder eine andere installierte Administrations… |
| `expUISwitchLanguageCatalogue` | class | – | `kernel/classes/uiswitch/expuiswitchlanguagecatalogue.php:24` | Was die Auswahl der Oberflächensprache über jede Locale der Installation weiß: ihre Namen (share/locale/.ini LanguageName und InternationalLanguageN… |
| `expUISwitchLanguageProvider` | class extends expUISwitchProvider | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/uiswitch/expuiswitchlanguageprovider.php:27` | Die Anzeigesprache der Administrationsoberfläche: was site.ini [RegionalSettings] Locale des Siteaccess festlegt (die Übersetzungen der Oberfl… |
| `expUISwitchProvider` | abstract class implements expUISwitchProviderInterface | [Strategie / Handler](strategie-handler.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/classes/uiswitch/expuiswitchprovider.php:18` | Der gemeinsame Teil eines UI-Umschalt-Anbieters: seine Einstellungsgruppe uiswitch.ini [UISwitch_], die Erlaubnisliste aus AllowedValues[] (jeder Wert geprüft durch die… |
| `expUISwitchProviderInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/classes/uiswitch/expuiswitchproviderinterface.php:19` | Eine umschaltbare Einstellung der Administrationsoberfläche (das "UI-Chrome"): die Anzeigesprache, das Design oder was eine Extension hinzufügt. |
