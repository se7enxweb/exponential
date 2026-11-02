---
title: /migration/editor
audience: developer, admin
category: examples
summary: "Die Ansicht migration/editor: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, migration, editor, migration/editor, module, view, URL
type: generated
generated: exp:help:examples
---
# /migration/editor

Eine Ansicht des Moduls `migration`, definiert in `kernel/migration/module.php` und ausgeführt von `kernel/migration/editor.php`.

## Adresse

```text
/migration/editor/<MigrationID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `MigrationID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`migration/edit`](../../richtlinien/migration.md#edit)

Eine Richtlinie auf `migration/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | ChooseTypeButton: ChooseType, AddStepButton: AddStep, EditStepButton: EditStep, RemoveStepButton: RemoveStep, MoveUpButton: MoveUp, MoveDownButton: MoveDown, CheckButton: Check, SaveButton: Save |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/migration/editor/1'|ezurl}>migration/editor</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/migration/editor/1' );
```

## Siehe auch

- [Alle Ansichten von migration](README.md)
- [Die Richtlinien von migration](../../richtlinien/migration.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/migration/module.php`, `kernel/migration/editor.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
