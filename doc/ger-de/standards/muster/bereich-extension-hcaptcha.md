---
title: Muster in extension/hcaptcha
audience: developer
category: standards
order: 462
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/hcaptcha mit den Entwurfsmustern.
keywords: extension/hcaptcha, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/hcaptcha

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/hcaptcha` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (5)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezRecaptureUtil` | class | – | `extension/hcaptcha/classes/ezrecaptureutil.php:10` | Hilfsklasse |
| `hcaptchaInfo` | class | [Zuständigkeitskette (Chain of Responsibility)](kette.md), [Wertobjekt / Struktur](wertobjekt.md) | `extension/hcaptcha/ezinfo.php:22` | hCaptcha-Extension für Exponential, geschrieben von 7x, basierend auf Arbeiten von: Bruce Morrison |
| `hCaptchaLibrary` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/hcaptcha/classes/hcaptchalib.php:44` | Die Server-URLs von hCAPTCHA |
| `hCaptchaTemplateOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/hcaptcha/autoloads/hcaptchatemplateoperator.php:22` | hCaptcha-Extension für Exponential, geschrieben von 7x, basierend auf Arbeiten von: Bruce Morrison |
| `hcaptchaType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/hcaptcha/datatypes/hcaptcha/hcaptchatype.php:31` | hcaptcha-Extension für Exponential, geschrieben von Bruce Morrison |

## Richtlinienfunktionen (1)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `hcaptcha/bypass_captcha` | – | `extension/hcaptcha/modules/hcaptcha/module.php:8` |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| – | `hCaptchaTemplateOperator` | `extension/hcaptcha/autoloads/eztemplateautoload.php:3` |
