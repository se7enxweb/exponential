---
title: /collaboration/view
audience: developer, admin
category: examples
summary: "Die Ansicht collaboration/view: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Ansicht, Modul, collaboration, view, collaboration/view, module, URL
type: generated
generated: exp:help:examples
---
# /collaboration/view

Eine Ansicht des Moduls `collaboration`, definiert in `kernel/collaboration/module.php` und ausgeführt von `kernel/collaboration/view.php`.

## Adresse

```text
/collaboration/view/<ViewMode>/(language)/<Language>/(offset)/<Offset>/(status)/<Status>/(role)/<Role>/(type)/<Type>/(group)/<Group>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ViewMode`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(language)` setzt `Language`, `(offset)` setzt `Offset`, `(status)` setzt `Status`, `(role)` setzt `Role`, `(type)` setzt `Type`, `(group)` setzt `Group`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `collaboration/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezmynavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/collaboration/view/viewmode'|ezurl}>collaboration/view</a>
<a href={concat( '/collaboration/view/viewmode/(language)/', 1 )|ezurl}>collaboration/view</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/collaboration/view/viewmode' );
```

## Siehe auch

- [Alle Ansichten von collaboration](README.md)
- [Die Richtlinien von collaboration](../../richtlinien/collaboration.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/collaboration/module.php`, `kernel/collaboration/view.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
