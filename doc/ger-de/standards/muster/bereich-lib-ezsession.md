---
title: Muster in lib/ezsession
audience: developer
category: standards
order: 537
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/ezsession mit den Entwurfsmustern.
keywords: lib/ezsession, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/ezsession

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `lib/ezsession` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (6)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezpSessionHandler` | abstract class | [Strategie / Handler](strategie-handler.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `lib/ezsession/classes/ezpsessionhandler.php:27` | Abstrakte Session-Handler-Klasse zum Erweitern |
| `ezpSessionHandlerAdapter` | class implements SessionHandlerInterface | [Adapter / Bridge / Wrapper](adapter.md), [Strategie / Handler](strategie-handler.md) | `lib/ezsession/classes/ezpsessionhandleradapter.php:24` | Übergibt PHP einen ezpSessionHandler als Objekt, das SessionHandlerInterface implementiert. |
| `ezpSessionHandlerDB` | class extends ezpSessionHandler | – | `lib/ezsession/classes/ezpsessionhandlerdb.php:20` | Klasse für den DB-Session-Handler |
| `ezpSessionHandlerPHP` | class extends ezpSessionHandler | – | `lib/ezsession/classes/ezpsessionhandlerphp.php:21` | Klasse für den PHP-Session-Handler. Registriert sich im Gegensatz zu den meisten anderen Handlern nicht selbst, denn der Sinn ist, PHP das meiste erledigen zu lassen |
| `ezpSessionHandlerSymfony` | class extends ezpSessionHandler | – | `lib/ezsession/classes/ezpsessionhandlersymfony.php:22` | Symfony-Session-Handler. |
| `eZSession` | class | [Registry](registry.md) | `lib/ezsession/classes/ezsession.php:67` | Session-Schnittstellenklasse von Exponential |

## Ereignis-Hooks (11)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `session/destroy` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php:144` |
| `session/regenerate` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php:174` |
| `session/gc` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php:205` |
| `session/cleanup` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php:270` |
| `session/destroy` | notify | `lib/ezsession/classes/ezpsessionhandlerphp.php:72` |
| `session/regenerate` | notify | `lib/ezsession/classes/ezpsessionhandlerphp.php:85` |
| `session/gc` | notify | `lib/ezsession/classes/ezpsessionhandlerphp.php:104` |
| `session/cleanup` | notify | `lib/ezsession/classes/ezpsessionhandlerphp.php:116` |
| `session/destroy` | notify | `lib/ezsession/classes/ezpsessionhandlersymfony.php:57` |
| `session/regenerate` | notify | `lib/ezsession/classes/ezpsessionhandlersymfony.php:81` |
| `session/gc` | notify | `lib/ezsession/classes/ezpsessionhandlersymfony.php:102` |
