---
title: Richtlinien des Moduls collaboration
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls collaboration (0) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, collaboration, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls collaboration

Die Richtlinienfunktionen von `collaboration` und ihre Einschränkungen, aus `$FunctionList` in `kernel/collaboration/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `collaboration/*` erlaubt jede Funktion.

Das Modul definiert keine Richtlinienfunktionen: Eine Rolle braucht `collaboration/*` für seine Ansichten (außer sie stehen in `PolicyOmitList[]`).

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von collaboration](../modulansichten/collaboration/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/collaboration/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
