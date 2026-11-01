---
title: Schritttypen von Migrationen
audience: developer, admin
category: examples
order: 0
summary: Jeder Schritttyp von YAML-Migrationen mit seinen Modi, Schlüsseln, Referenzwerten und einem Beispiel.
keywords: Beispiele, Referenz, Schritttypen von Migrationen, examples, reference
type: generated
generated: exp:help:examples
---
# Schritttypen von Migrationen

Jeder Schritttyp von YAML-Migrationen, den der Kernel in `settings/migration.ini` `[StepSettings] StepTypeList` registriert, je eine Seite, erzeugt aus dem Schema des Typs: seine Modi, Schlüssel, die Werte, die `references:` nehmen kann, ob er sich selbst zurücknimmt, die Caches, die er berührt, und ein Beispiel. Eine Erweiterung registriert ihre eigenen Typen genauso ([wie](../../anleitungen/migrationen-yaml-schritte.md)).

| Schritttyp | Modi | Was er tut |
|---|---|---|
| [reference](reference.md) |  | Setzt Referenzen (${ref.<name>}) für die Schritte und Migrationen nach ihm im Lauf |
| [assert](assert.md) |  | Lässt die Migration scheitern, wenn eine Bedingung falsch ist |
| [content](content.md) | create, update, ensure, delete | Legt ein Inhaltsobjekt an, ändert, sichert oder löscht es, mit seinen Feldern in einer oder mehreren Sprachen |
| [location](location.md) | create, update, delete, move, copy, hide, unhide | Fügt einen Ort (Knoten) eines Inhaltsobjekts hinzu, ändert, entfernt, verschiebt, kopiert, verbirgt oder zeigt ihn |
| [content_version](content_version.md) | restore, cleanup | Stellt eine frühere Version eines Inhaltsobjekts wieder her oder entfernt seine alten Versionen |

- [Alle Beispiele](../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
