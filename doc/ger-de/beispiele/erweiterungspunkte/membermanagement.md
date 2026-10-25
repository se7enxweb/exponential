---
title: Erweiterungspunkte in membermanagement.ini
audience: developer, admin
category: examples
summary: Einstellungen von membermanagement.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (1, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, membermanagement.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in membermanagement.ini

Einstellungen von `settings/membermanagement.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/membermanagement.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## MemberGuardSettings GuardedClasses

Ausgeliefert: eine Liste mit 1: `user_group`. [Die Einstellung](../ini-einstellungen/membermanagement/MemberGuardSettings.md#guardedclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/membermanagement.ini.append.php
[MemberGuardSettings]
GuardedClasses[]=expExampleGuardedClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von membermanagement.ini](../ini-einstellungen/membermanagement/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/membermanagement.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
