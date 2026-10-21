---
title: Muster in kernel/private/classes/webdav
audience: developer
category: standards
order: 526
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/private/classes/webdav mit den Entwurfsmustern.
keywords: kernel/private/classes/webdav, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/private/classes/webdav

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/private/classes/webdav` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZWebDAVContentBackend` | class extends ezcWebdavSimpleBackend implements ezcWebdavLockBackend | [Strategie / Handler](strategie-handler.md) | `kernel/private/classes/webdav/ezwebdavcontentbackend.php:30` | WebDAV-Backend für Exponential, basierend auf der Webdav-Komponente der eZ Components. |
| `eZWebDAVContentBackendAuth` | class implements ezcWebdavAnonymousAuthenticator, ezcWebdavBasicAuthenticator, ezcWebdavAuthorizer, ezcWebdavLockAuthorizer | – | `kernel/private/classes/webdav/ezwebdavcontentbackendauth.php:22` | Basic-Authentifizierung für WebDAV. |
