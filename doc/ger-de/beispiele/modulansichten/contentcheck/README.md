---
title: Das Modul contentcheck
audience: developer, admin
category: examples
order: 10
summary: "Die Ansichten des Moduls contentcheck: Adressen, Parameter und Richtlinienfunktionen."
keywords: Beispiele, Referenz, Das Modul contentcheck, examples, reference
type: generated
generated: exp:help:examples
---
# Das Modul contentcheck

Name `ContentCheck`, definiert in `kernel/contentcheck/module.php`. 7 Ansichten.

| Ansicht | Adresse | Richtlinienfunktionen |
|---|---|---|
| [brokenrelations](brokenrelations.md) | `/contentcheck/brokenrelations/<NodeID>` | brokenrelations |
| [fixfiles](fixfiles.md) | `/contentcheck/fixfiles` | brokenrelations; fixfiles |
| [scope](scope.md) | `/contentcheck/scope/<View>/<NodeID>/<StartNodeID>` | (Modul) |
| [storage](storage.md) | `/contentcheck/storage/<NodeID>` | storage |
| [storageaction](storageaction.md) | `/contentcheck/storageaction` | storage |
| [trash](trash.md) | `/contentcheck/trash` | unreferenced; trash |
| [unreferenced](unreferenced.md) | `/contentcheck/unreferenced/<NodeID>` | unreferenced |

- [Die Richtlinien dieses Moduls](../../richtlinien/contentcheck.md)

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/contentcheck/module.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
