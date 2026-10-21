---
title: Das Modul workflow
audience: developer, admin
category: examples
order: 10
summary: "Die Ansichten des Moduls workflow: Adressen, Parameter und Richtlinienfunktionen."
keywords: Beispiele, Referenz, Das Modul workflow, examples, reference
type: generated
generated: exp:help:examples
---
# Das Modul workflow

Name `eZWorkflow`, definiert in `kernel/workflow/module.php`. 11 Ansichten.

| Ansicht | Adresse | Richtlinienfunktionen |
|---|---|---|
| [down](down.md) | `/workflow/down/<WorkflowID>/<EventID>` | (Modul) |
| [edit](edit.md) | `/workflow/edit/<WorkflowID>/<GroupID>/<GroupName>` | (Modul) |
| [event](event.md) | `/workflow/event/<WorkflowID>/<EventID>` | (Modul) |
| [groupedit](groupedit.md) | `/workflow/groupedit/<WorkflowGroupID>` | (Modul) |
| [grouplist](grouplist.md) | `/workflow/grouplist` | (Modul) |
| [process](process.md) | `/workflow/process/<WorkflowProcessID>` | (Modul) |
| [processlist](processlist.md) | `/workflow/processlist` | (Modul) |
| [run](run.md) | `/workflow/run/<WorkflowProcessID>` | (Modul) |
| [up](up.md) | `/workflow/up/<WorkflowID>/<EventID>` | (Modul) |
| [view](view.md) | `/workflow/view/<WorkflowID>` | (Modul) |
| [workflowlist](workflowlist.md) | `/workflow/workflowlist/<GroupID>` | (Modul) |

- [Die Richtlinien dieses Moduls](../../richtlinien/workflow.md)

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/workflow/module.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
