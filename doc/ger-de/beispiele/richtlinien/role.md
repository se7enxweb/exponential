---
title: Richtlinien des Moduls role
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls role (0) und ihre Einschränkungen.
keywords: Rolle, Richtlinie, Richtlinien, Einschränkung, Berechtigung, role, policy, policies, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls role

Die Richtlinienfunktionen von `role` und ihre Einschränkungen, aus `$FunctionList` in `kernel/role/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `role/*` erlaubt jede Funktion.

Das Modul definiert keine Richtlinienfunktionen: Eine Rolle braucht `role/*` für seine Ansichten (außer sie stehen in `PolicyOmitList[]`).

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von role](../modulansichten/role/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/role/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
