---
title: Muster in extension/ezformtoken
audience: developer
category: standards
order: 440
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezformtoken mit den Entwurfsmustern.
keywords: extension/ezformtoken, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezformtoken

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezformtoken` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (1)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezxFormToken` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezformtoken/event/ezxformtoken.php:23` | Diese Klasse lauscht auf interne Kernel-Ereignisse in Exponential, um Formulare mit sitzungsbezogenen Tokens zu validieren |
