---
title: Workflow-Ereignistyp (Befehl, Plugin)
audience: developer
category: standards
order: 312
summary: Ein Schritt eines Workflows, der läuft, wenn ein Trigger auslöst (vor oder nach dem Veröffentlichen, Lesen, Hinzufügen zum Warenkorb ...). 12 Instanzen in Exponential.
keywords: Entwurfsmuster, Workflow-Ereignis, Plugins, Musterkatalog, design pattern, workflow-event, pattern catalogue
type: catalogue
---
# Workflow-Ereignistyp (Befehl, Plugin)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Ein Schritt eines Workflows, der läuft, wenn ein Trigger auslöst (vor oder nach dem Veröffentlichen, Lesen, Hinzufügen zum Warenkorb ...).

|  |  |
| --- | --- |
| Gruppe | Plugins |
| Instanzen | 12 |
| Erkannt an | Erweitert eZWorkflowEventType. |
| Erweiterungspunkt | workflow.ini [EventSettings] ExtensionDirectories[] und AvailableEventTypes[]; Trigger verbinden Workflows mit Moduloperationen. |

## Klassen nach Bereich

### extension/autorss (1)

[Alle Muster von extension/autorss](bereich-extension-autorss.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `AutoRSSType` | class extends eZWorkflowEventType | `extension/autorss/eventtypes/event/autorss/autorsstype.php:27` | – | – |

### extension/bcwebsitestatistics (1)

[Alle Muster von extension/bcwebsitestatistics](bereich-extension-bcwebsitestatistics.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZReceiptType` | class extends eZWorkflowEventType | `extension/bcwebsitestatistics/workflowtypes/event/ezreceipt/ezreceipttype.php:38` | – | – |

### extension/ezflow (1)

[Alle Muster von extension/ezflow](bereich-extension-ezflow.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZPageSwapType` | class extends eZWorkflowEventType | `extension/ezflow/eventtypes/event/ezpageswap/ezpageswaptype.php:28` | – | – |

### extension/ezssp (1)

[Alle Muster von extension/ezssp](bereich-extension-ezssp.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `SubtreeSkeletonPublishType` | class extends eZWorkflowEventType | `extension/ezssp/eventtypes/event/subtreeskeletonpublish/subtreeskeletonpublishtype.php:27` | – | – |

### extension/swark (2)

[Alle Muster von extension/swark](bereich-extension-swark.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `AutoPriorityType` | class extends eZWorkflowEventType | `extension/swark/eventtypes/event/autopriority/autoprioritytype.php:22` | – | – |
| `DeferToCronType` | class extends eZWorkflowEventType | `extension/swark/eventtypes/event/defertocron/defertocrontype.php:22` | – | – |

### kernel/classes/workflowtypes (6)

[Alle Muster von kernel/classes/workflowtypes](bereich-kernel-classes-workflowtypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZApproveType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezapprove/ezapprovetype.php:24` | – | – |
| `eZFinishUserRegisterType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezfinishuserregister/ezfinishuserregistertype.php:17` | – | – |
| `eZMultiplexerType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezmultiplexer/ezmultiplexertype.php:24` | – | – |
| `eZPaymentGatewayType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php:20` | – | – |
| `eZSimpleShippingType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezsimpleshipping/ezsimpleshippingtype.php:17` | – | – |
| `eZWaitUntilDateType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatetype.php:17` | – | – |
