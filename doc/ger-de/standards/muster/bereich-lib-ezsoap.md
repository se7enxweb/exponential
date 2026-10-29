---
title: Muster in lib/ezsoap
audience: developer
category: standards
order: 551
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/ezsoap mit den Entwurfsmustern.
keywords: lib/ezsoap, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/ezsoap

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `lib/ezsoap` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (10)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZSOAPBody` | class | – | `lib/ezsoap/classes/ezsoapbody.php:17` | – |
| `eZSOAPClient` | class | – | `lib/ezsoap/classes/ezsoapclient.php:49` | – |
| `eZSOAPCodec` | class | – | `lib/ezsoap/classes/ezsoapcodec.php:18` | – |
| `eZSOAPEnvelope` | class | – | `lib/ezsoap/classes/ezsoapenvelope.php:21` | – |
| `eZSOAPFault` | class | – | `lib/ezsoap/classes/ezsoapfault.php:19` | – |
| `eZSOAPHeader` | class | – | `lib/ezsoap/classes/ezsoapheader.php:19` | – |
| `eZSOAPParameter` | class | – | `lib/ezsoap/classes/ezsoapparameter.php:20` | – |
| `eZSOAPRequest` | class extends eZSOAPEnvelope | – | `lib/ezsoap/classes/ezsoaprequest.php:19` | – |
| `eZSOAPResponse` | class extends eZSOAPEnvelope | – | `lib/ezsoap/classes/ezsoapresponse.php:19` | – |
| `eZSOAPServer` | class | – | `lib/ezsoap/classes/ezsoapserver.php:50` | – |
