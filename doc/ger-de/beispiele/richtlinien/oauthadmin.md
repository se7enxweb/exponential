---
title: Richtlinien des Moduls oauthadmin
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls oauthadmin (0) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, oauthadmin, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls oauthadmin

Die Richtlinienfunktionen von `oauthadmin` und ihre Einschränkungen, aus `$FunctionList` in `kernel/private/modules/oauthadmin/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `oauthadmin/*` erlaubt jede Funktion.

Das Modul definiert keine Richtlinienfunktionen: Eine Rolle braucht `oauthadmin/*` für seine Ansichten (außer sie stehen in `PolicyOmitList[]`).

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von oauthadmin](../modulansichten/oauthadmin/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/private/modules/oauthadmin/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
