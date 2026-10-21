---
title: Trait (Mixin)
audience: developer
category: standards
order: 336
summary: Gemeinsame Methoden, die in mehrere Klassen eingemischt werden. 1 Instanzen in Exponential.
keywords: Entwurfsmuster, Trait, Struktur, Musterkatalog, design pattern, trait, pattern catalogue
type: catalogue
---
# Trait (Mixin)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Gemeinsame Methoden, die in mehrere Klassen eingemischt werden.

|  |  |
| --- | --- |
| Gruppe | Struktur |
| Instanzen | 1 |
| Erkannt an | Jeder Trait. |

## Klassen nach Bereich

### extension/xrowextract (1)

[Alle Muster von extension/xrowextract](bereich-extension-xrowextract.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `XrowExtractFileModes` | trait | `extension/xrowextract/classes/xrowextractfilemodes.php:14` | Die Modi, die die Extension ihren Dateien und Verzeichnissen gibt, innerhalb der Grenzen EZP_FILE_MODE_MAX / EZP_DIR_MODE_MAX, die der Kernel aus config.php liest (siehe… | – |
