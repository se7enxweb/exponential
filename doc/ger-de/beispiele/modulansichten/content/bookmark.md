---
title: /content/bookmark
audience: developer, admin
category: examples
summary: "Die Ansicht content/bookmark: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, bookmark, content/bookmark, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/bookmark

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/bookmark.php`.

## Adresse

```text
/content/bookmark/(offset)/<Offset>/(q)/<Query>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`, `(q)` setzt `Query`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`content/bookmark`](../../richtlinien/content.md#bookmark)

Eine Richtlinie auf `content/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezmynavigationpart` |
| Einzelne POST-Aktionen | AddButton: Add, RemoveButton: Remove |
| POST-Aktionen | BrowseActionName |
| Parameter der POST-Aktionen | Remove (DeleteIDArray: DeleteIDArray) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/content/bookmark'|ezurl}>content/bookmark</a>
<a href={concat( '/content/bookmark/(offset)/', 1 )|ezurl}>content/bookmark</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/bookmark' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/bookmark.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
