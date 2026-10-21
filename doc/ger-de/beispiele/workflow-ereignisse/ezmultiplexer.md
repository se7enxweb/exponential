---
title: Workflow-Ereignis ezmultiplexer (Multiplexer)
audience: developer, admin
category: examples
summary: "Der Workflow-Ereignistyp ezmultiplexer: seine Klasse, die Auslöser, die er akzeptiert, und wie er verwendet wird."
keywords: Ereignis, Auslöser, ezmultiplexer, event_ezmultiplexer, workflow, event, trigger, eZMultiplexerType
type: generated
generated: exp:help:examples
---
# Das Workflow-Ereignis ezmultiplexer (Multiplexer)

Klasse `eZMultiplexerType` in `kernel/classes/workflowtypes/event/ezmultiplexer/ezmultiplexertype.php`; in einem Workflow ist es der Ereignistyp `event_ezmultiplexer`, aufgeführt in `settings/workflow.ini` `[EventSettings] AvailableEventTypes[]`.

## Verwenden

1. Setup > Workflows: Legen Sie in einer Gruppe einen Workflow an und fügen Sie das Ereignis „Multiplexer“ hinzu; füllen Sie seine Einstellungen aus.
2. Setup > Auslöser: Verbinden Sie den Workflow mit einem Auslöser, zum Beispiel `content / publish / before` (siehe die [Auslöser](ausloeser.md)).
3. Workflows, die warten (eine Freigabe, ein Datum), laufen weiter, wenn der Cronjob-Teil `frequent` `workflow.php` ausführt (siehe die [Cronjobs](../cronjobs/README.md)).

Um es anzubieten (es steht in der ausgelieferten Liste, sofern oben nichts anderes steht):

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/workflow.ini.append.php
[EventSettings]
AvailableEventTypes[]=event_ezmultiplexer
*/ ?>
```

Seine Templates: `design/admin/templates/workflow/eventtype/edit/event_ezmultiplexer.tpl`, `design/admin4/templates/workflow/eventtype/edit/event_ezmultiplexer.tpl`, `design/standard/templates/workflow/eventtype/edit/event_ezmultiplexer.tpl`, `design/standard/templates/workflow/eventtype/view/event_ezmultiplexer.tpl`.

## Siehe auch

- [Alle Workflow-Ereignistypen](README.md)
- [Auslöser](ausloeser.md)
- [Ein eigenes Workflow-Ereignis](../kochbuch/workflow-ereignis.md)
- [Workflows](../../anleitungen/workflows.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/classes/workflowtypes/event/ezmultiplexer/ezmultiplexertype.php`, `settings/workflow.ini`, `design/admin/templates/workflow/eventtype/edit/event_ezmultiplexer.tpl`, `design/admin4/templates/workflow/eventtype/edit/event_ezmultiplexer.tpl`, `design/standard/templates/workflow/eventtype/edit/event_ezmultiplexer.tpl`, `design/standard/templates/workflow/eventtype/view/event_ezmultiplexer.tpl`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
