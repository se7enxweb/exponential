---
title: Richtlinien des Moduls switchlanguage
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls switchlanguage (0) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, switchlanguage, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls switchlanguage

Die Richtlinienfunktionen von `switchlanguage` und ihre Einschränkungen, aus `$FunctionList` in `kernel/private/modules/switchlanguage/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `switchlanguage/*` erlaubt jede Funktion.

Das Modul definiert keine Richtlinienfunktionen: Eine Rolle braucht `switchlanguage/*` für seine Ansichten (außer sie stehen in `PolicyOmitList[]`).

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von switchlanguage](../modulansichten/switchlanguage/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/private/modules/switchlanguage/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
