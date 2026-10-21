---
title: Richtlinien des Moduls class
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls class (0) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, class, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls class

Die Richtlinienfunktionen von `class` und ihre Einschränkungen, aus `$FunctionList` in `kernel/class/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `class/*` erlaubt jede Funktion.

Das Modul definiert keine Richtlinienfunktionen: Eine Rolle braucht `class/*` für seine Ansichten (außer sie stehen in `PolicyOmitList[]`).

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von class](../modulansichten/class/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/class/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
