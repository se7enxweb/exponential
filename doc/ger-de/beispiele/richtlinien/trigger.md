---
title: Richtlinien des Moduls trigger
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls trigger (0) und ihre Einschränkungen.
keywords: Auslöser, Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, trigger, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls trigger

Die Richtlinienfunktionen von `trigger` und ihre Einschränkungen, aus `$FunctionList` in `kernel/trigger/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `trigger/*` erlaubt jede Funktion.

Das Modul definiert keine Richtlinienfunktionen: Eine Rolle braucht `trigger/*` für seine Ansichten (außer sie stehen in `PolicyOmitList[]`).

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von trigger](../modulansichten/trigger/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/trigger/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
