---
title: /visual/templatelist
audience: developer, admin
category: examples
summary: "Die Ansicht visual/templatelist: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, visual, templatelist, visual/templatelist, module, view, URL
type: generated
generated: exp:help:examples
---
# /visual/templatelist

Eine Ansicht des Moduls `visual`, definiert in `kernel/visual/module.php` und ausgeführt von `kernel/visual/templatelist.php`.

## Adresse

```text
/visual/templatelist/(offset)/<Offset>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `visual/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezvisualnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/visual/templatelist'|ezurl}>visual/templatelist</a>
<a href={concat( '/visual/templatelist/(offset)/', 1 )|ezurl}>visual/templatelist</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/visual/templatelist' );
```

## Siehe auch

- [Alle Ansichten von visual](README.md)
- [Die Richtlinien von visual](../../richtlinien/visual.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/visual/module.php`, `kernel/visual/templatelist.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
