---
title: Workflow-Ereignis expdatatablemirror (Auf Datentabellen spiegeln)
audience: developer, admin
category: examples
summary: "Der Workflow-Ereignistyp expdatatablemirror: seine Klasse, die Auslöser, die er akzeptiert, und wie er verwendet wird."
keywords: Ereignis, Auslöser, expdatatablemirror, event_expdatatablemirror, Auf Datentabellen spiegeln, Mirror to data tables, workflow, event, trigger, expDataTableMirrorType
type: generated
generated: exp:help:examples
---
# Das Workflow-Ereignis expdatatablemirror (Auf Datentabellen spiegeln)

Klasse `expDataTableMirrorType` in `kernel/classes/workflowtypes/event/expdatatablemirror/expdatatablemirrortype.php`; in einem Workflow ist es der Ereignistyp `event_expdatatablemirror`, aufgeführt in `settings/workflow.ini` `[EventSettings] AvailableEventTypes[]`.

Es kann nur an diesen Auslösern verwendet werden (`setTriggerTypes()`): `content/publish (after)`.

## Verwenden

1. Setup > Workflows: Legen Sie in einer Gruppe einen Workflow an und fügen Sie das Ereignis „Auf Datentabellen spiegeln“ hinzu; füllen Sie seine Einstellungen aus.
2. Setup > Auslöser: Verbinden Sie den Workflow mit einem Auslöser, zum Beispiel `content / publish / before` (siehe die [Auslöser](ausloeser.md)).
3. Workflows, die warten (eine Freigabe, ein Datum), laufen weiter, wenn der Cronjob-Teil `frequent` `workflow.php` ausführt (siehe die [Cronjobs](../cronjobs/README.md)).

Um es anzubieten (es steht in der ausgelieferten Liste, sofern oben nichts anderes steht):

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/workflow.ini.append.php
[EventSettings]
AvailableEventTypes[]=event_expdatatablemirror
*/ ?>
```

Seine Templates: `design/standard/templates/workflow/eventtype/edit/event_expdatatablemirror.tpl`, `design/standard/templates/workflow/eventtype/view/event_expdatatablemirror.tpl`.

## Siehe auch

- [Alle Workflow-Ereignistypen](README.md)
- [Auslöser](ausloeser.md)
- [Ein eigenes Workflow-Ereignis](../kochbuch/workflow-ereignis.md)
- [Workflows](../../anleitungen/workflows.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/classes/workflowtypes/event/expdatatablemirror/expdatatablemirrortype.php`, `settings/workflow.ini`, `design/standard/templates/workflow/eventtype/edit/event_expdatatablemirror.tpl`, `design/standard/templates/workflow/eventtype/view/event_expdatatablemirror.tpl`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
