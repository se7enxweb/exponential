---
title: /url/edit
audience: developer, admin
category: examples
summary: "Die Ansicht url/edit: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, url, edit, url/edit, module, view, URL
type: generated
generated: exp:help:examples
---
# /url/edit

Eine Ansicht des Moduls `url`, definiert in `kernel/url/module.php` und ausgeführt von `kernel/url/edit.php`.

## Adresse

```text
/url/edit/<ID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ID`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `url/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `edit` |
| Einzelne POST-Aktionen | Cancel: Cancel, Store: Store |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/url/edit/1'|ezurl}>url/edit</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/url/edit/1' );
```

## Siehe auch

- [Alle Ansichten von url](README.md)
- [Die Richtlinien von url](../../richtlinien/url.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/url/module.php`, `kernel/url/edit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
