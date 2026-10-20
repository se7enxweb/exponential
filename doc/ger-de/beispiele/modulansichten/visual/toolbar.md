---
title: /visual/toolbar
audience: developer, admin
category: examples
summary: "Die Ansicht visual/toolbar: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, visual, toolbar, visual/toolbar, module, view, URL
type: generated
generated: exp:help:examples
---
# /visual/toolbar

Eine Ansicht des Moduls `visual`, definiert in `kernel/visual/module.php` und ausgeführt von `kernel/visual/toolbar.php`.

## Adresse

```text
/visual/toolbar/<SiteAccess>/<Position>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `SiteAccess`, `Position`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `visual/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezvisualnavigationpart` |
| UI-Kontext | `edit` |
| Einzelne POST-Aktionen | BackToToolbarsButton: BackToToolbars, NewToolButton: NewTool, UpdatePlacementButton: UpdatePlacement, BrowseButton: Browse, RemoveButton: Remove, StoreButton: Store |
| POST-Aktionen | BrowseActionName |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/visual/toolbar/siteaccess/position'|ezurl}>visual/toolbar</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/visual/toolbar/siteaccess/position' );
```

## Siehe auch

- [Alle Ansichten von visual](README.md)
- [Die Richtlinien von visual](../../richtlinien/visual.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/visual/module.php`, `kernel/visual/toolbar.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
