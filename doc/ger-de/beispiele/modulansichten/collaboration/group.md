---
title: /collaboration/group
audience: developer, admin
category: examples
summary: "Die Ansicht collaboration/group: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, collaboration, group, collaboration/group, module, view, URL
type: generated
generated: exp:help:examples
---
# /collaboration/group

Eine Ansicht des Moduls `collaboration`, definiert in `kernel/collaboration/module.php` und ausgeführt von `kernel/collaboration/group.php`.

## Adresse

```text
/collaboration/group/<ViewMode>/<GroupID>/(language)/<Language>/(offset)/<Offset>/(status)/<Status>/(role)/<Role>/(type)/<Type>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ViewMode`, `GroupID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(language)` setzt `Language`, `(offset)` setzt `Offset`, `(status)` setzt `Status`, `(role)` setzt `Role`, `(type)` setzt `Type`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `collaboration/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezmynavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/collaboration/group/viewmode/1'|ezurl}>collaboration/group</a>
<a href={concat( '/collaboration/group/viewmode/1/(language)/', 1 )|ezurl}>collaboration/group</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/collaboration/group/viewmode/1' );
```

## Siehe auch

- [Alle Ansichten von collaboration](README.md)
- [Die Richtlinien von collaboration](../../richtlinien/collaboration.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/collaboration/module.php`, `kernel/collaboration/group.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
