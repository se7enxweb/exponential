---
title: /collaboration/action
audience: developer, admin
category: examples
summary: "Die Ansicht collaboration/action: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, collaboration, action, collaboration/action, module, view, URL
type: generated
generated: exp:help:examples
---
# /collaboration/action

Eine Ansicht des Moduls `collaboration`, definiert in `kernel/collaboration/module.php` und ausgeführt von `kernel/collaboration/action.php`.

## Adresse

```text
/collaboration/action
```

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `collaboration/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezmynavigationpart` |
| Parameter der POST-Aktionen | Custom (TypeIdentifer: CollaborationTypeIdentifier, ItemID: CollaborationItemID) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/collaboration/action'|ezurl}>collaboration/action</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/collaboration/action' );
```

## Siehe auch

- [Alle Ansichten von collaboration](README.md)
- [Die Richtlinien von collaboration](../../richtlinien/collaboration.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/collaboration/module.php`, `kernel/collaboration/action.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
