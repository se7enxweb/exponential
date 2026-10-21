---
title: Muster in kernel/classes/role
audience: developer
category: standards
order: 505
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/role mit den Entwurfsmustern.
keywords: kernel/classes/role, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/role

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/role` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (3)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expRoleGrantCheck` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/role/exprolegrantcheck.php:37` | Ob ein Benutzer, der Rollen bearbeitet, eine Richtlinie vergeben darf: nur eine, die sein eigener Zugriff abdeckt (site.ini [RoleSettings] PreventPrivilegeEscalation). |
| `expRolePage` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/role/exprolepage.php:19` | Was die Rollenseiten (role/list, role/view, role/edit) neben der Rolle selbst ermitteln: die Zeilen der Rollenliste mit ihrer Suche und Reihenfolge, was e… |
| `expRolePolicySentence` | class | – | `kernel/classes/role/exprolepolicysentence.php:27` | Eine Richtlinie einer Rolle in Worten, für die Rollenseiten (role/view, role/edit): "Darf Inhalte in der Sektion Standard lesen, der Klassen Article und Folder". |
