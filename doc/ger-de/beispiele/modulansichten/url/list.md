---
title: /url/list
audience: developer, admin
category: examples
summary: "Die Ansicht url/list: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, url, list, url/list, module, view, URL
type: generated
generated: exp:help:examples
---
# /url/list

Eine Ansicht des Moduls `url`, definiert in `kernel/url/module.php` und ausgeführt von `kernel/url/list.php`.

## Adresse

```text
/url/list/<ViewMode>/(offset)/<Offset>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ViewMode`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `url/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | SetValid: SetValid, SetInvalid: SetInvalid |
| Parameter der POST-Aktionen | SetValid (URLSelection: URLSelection), SetInvalid (URLSelection: URLSelection) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/url/list/viewmode'|ezurl}>url/list</a>
<a href={concat( '/url/list/viewmode/(offset)/', 1 )|ezurl}>url/list</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/url/list/viewmode' );
```

## Siehe auch

- [Alle Ansichten von url](README.md)
- [Die Richtlinien von url](../../richtlinien/url.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/url/module.php`, `kernel/url/list.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
