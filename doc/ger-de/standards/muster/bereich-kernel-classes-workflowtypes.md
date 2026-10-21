---
title: Muster in kernel/classes/workflowtypes
audience: developer
category: standards
order: 511
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/workflowtypes mit den Entwurfsmustern.
keywords: kernel/classes/workflowtypes, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/workflowtypes

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/workflowtypes` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (9)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZApproveType` | class extends eZWorkflowEventType | [Workflow-Ereignistyp (Befehl, Plugin)](workflow-ereignis.md) | `kernel/classes/workflowtypes/event/ezapprove/ezapprovetype.php:24` | – |
| `eZFinishUserRegisterType` | class extends eZWorkflowEventType | [Workflow-Ereignistyp (Befehl, Plugin)](workflow-ereignis.md) | `kernel/classes/workflowtypes/event/ezfinishuserregister/ezfinishuserregistertype.php:17` | – |
| `eZMultiplexerType` | class extends eZWorkflowEventType | [Workflow-Ereignistyp (Befehl, Plugin)](workflow-ereignis.md) | `kernel/classes/workflowtypes/event/ezmultiplexer/ezmultiplexertype.php:24` | – |
| `eZPaymentGatewayType` | class extends eZWorkflowEventType | [Registry](registry.md), [Workflow-Ereignistyp (Befehl, Plugin)](workflow-ereignis.md) | `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php:20` | – |
| `eZPaymentLogger` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentlogger.php:16` | – |
| `eZSimpleShippingType` | class extends eZWorkflowEventType | [Workflow-Ereignistyp (Befehl, Plugin)](workflow-ereignis.md) | `kernel/classes/workflowtypes/event/ezsimpleshipping/ezsimpleshippingtype.php:17` | – |
| `eZWaitUntilDate` | class | – | `kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildate.php:17` | – |
| `eZWaitUntilDateType` | class extends eZWorkflowEventType | [Workflow-Ereignistyp (Befehl, Plugin)](workflow-ereignis.md) | `kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatetype.php:17` | – |
| `eZWaitUntilDateValue` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatevalue.php:18` | – |
