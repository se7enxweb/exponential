---
title: Patterns in lib/ezsoap
audience: developer
category: standards
order: 551
summary: Every class, view, fetch function, operator and plugin point of lib/ezsoap with its design patterns.
keywords: lib/ezsoap, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/ezsoap

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/ezsoap` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (10)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
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
