---
title: Das Modul state
audience: developer, admin
category: examples
order: 10
summary: "Die Ansichten des Moduls state: Adressen, Parameter und Richtlinienfunktionen."
keywords: Beispiele, Referenz, Das Modul state, examples, reference
type: generated
generated: exp:help:examples
---
# Das Modul state

Name `eZContentObjectState`, definiert in `kernel/state/module.php`. 6 Ansichten.

| Ansicht | Adresse | Richtlinienfunktionen |
|---|---|---|
| [assign](assign.md) | `/state/assign/<ObjectID>/<SelectedStateID>` | assign |
| [edit](edit.md) | `/state/edit/<GroupIdentifier>/<StateIdentifier>` | administrate |
| [group](group.md) | `/state/group/<GroupIdentifier>/<Language>` | administrate |
| [group_edit](group_edit.md) | `/state/group_edit/<GroupIdentifier>` | administrate |
| [groups](groups.md) | `/state/groups` | administrate |
| [view](view.md) | `/state/view/<GroupIdentifier>/<StateIdentifier>/<Language>` | administrate |

- [Die Richtlinien dieses Moduls](../../richtlinien/state.md)

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/state/module.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
