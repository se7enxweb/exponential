---
title: Muster in lib
audience: developer
category: standards
order: 527
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib mit den Entwurfsmustern.
keywords: lib, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `lib` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ExponentialSDK` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/version.php:21` | – |
| `eZPublishSDK` | class extends ExponentialSDK | – | `lib/ezpublishsdk.php:20` | Früherer Name von ExponentialSDK (lib/version.php), aus Kompatibilitätsgründen beibehalten. |
