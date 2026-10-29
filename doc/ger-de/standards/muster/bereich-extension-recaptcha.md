---
title: Muster in extension/recaptcha
audience: developer
category: standards
order: 472
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/recaptcha mit den Entwurfsmustern.
keywords: extension/recaptcha, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/recaptcha

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/recaptcha` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `recaptchaInfo` | class | [Zuständigkeitskette (Chain of Responsibility)](kette.md), [Wertobjekt / Struktur](wertobjekt.md) | `extension/recaptcha/ezinfo.php:22` | reCAPTCHA-Extension für Exponential, geschrieben von Bruce Morrison |
| `ReCaptchaResponse` | class | – | `extension/recaptcha/classes/recaptchalib.php:142` | recaptcha_check_answer() gibt eine ReCaptchaResponse zurück |
| `reCAPTCHATemplateOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/recaptcha/autoloads/recaptchatemplateoperator.php:22` | reCAPTCHA-Extension für Exponential, geschrieben von Bruce Morrison |
| `recaptchaType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/recaptcha/datatypes/recaptcha/recaptchatype.php:31` | reCAPTCHA-Extension für Exponential, geschrieben von Bruce Morrison |

## Richtlinienfunktionen (1)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `recaptcha/bypass_captcha` | – | `extension/recaptcha/modules/recaptcha/module.php:8` |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| – | `reCAPTCHATemplateOperator` | `extension/recaptcha/autoloads/eztemplateautoload.php:23` |
