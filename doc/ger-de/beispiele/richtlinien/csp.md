---
title: Richtlinien des Moduls csp
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls csp (0) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, csp, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls csp

Die Richtlinienfunktionen von `csp` und ihre Einschränkungen, aus `$FunctionList` in `kernel/csp/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `csp/*` erlaubt jede Funktion.

Das Modul definiert keine Richtlinienfunktionen: Eine Rolle braucht `csp/*` für seine Ansichten (außer sie stehen in `PolicyOmitList[]`).

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von csp](../modulansichten/csp/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/csp/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
