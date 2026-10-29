---
title: Workflow-Ereignistypen und Auslöser
audience: developer, admin
category: examples
order: 0
summary: Jeder Workflow-Ereignistyp und jeder Auslösepunkt.
keywords: Beispiele, Referenz, Workflow-Ereignistypen und Auslöser, examples, reference
type: generated
generated: exp:help:examples
---
# Workflow-Ereignistypen und Auslöser

Jeder Workflow-Ereignistyp des Kernels und der mitgelieferten Erweiterungen und jeder [Auslöser](ausloeser.md), mit dem ein Workflow verbunden werden kann.

| Ereignis | Name | Klasse | Akzeptierte Auslöser |
|---|---|---|---|
| [expdatatablemirror](expdatatablemirror.md) | Auf Datentabellen spiegeln | `expDataTableMirrorType` | content/publish (after) |
| [expuserapprove](expuserapprove.md) | Benutzerkonten freigeben | `expUserApproveType` | content/publish (before) |
| [ezapprove](ezapprove.md) | Freigeben | `eZApproveType` | content/publish (before) |
| [ezfinishuserregister](ezfinishuserregister.md) | Benutzerregistrierung abschließen | `eZFinishUserRegisterType` | content/publish (after) |
| [ezmultiplexer](ezmultiplexer.md) | Multiplexer | `eZMultiplexerType` | beliebig |
| [ezpaymentgateway](ezpaymentgateway.md) | Zahlungs-Gateway | `eZPaymentGatewayType` | beliebig |
| [ezsimpleshipping](ezsimpleshipping.md) | Einfacher Versand | `eZSimpleShippingType` | beliebig |
| [ezwaituntildate](ezwaituntildate.md) | Bis Datum warten | `eZWaitUntilDateType` | content/publish (before, after) |

- [Alle Beispiele](../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/workflow.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
