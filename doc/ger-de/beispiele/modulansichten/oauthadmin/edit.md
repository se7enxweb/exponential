---
title: /oauthadmin/edit
audience: developer, admin
category: examples
summary: "Die Ansicht oauthadmin/edit: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, oauthadmin, edit, oauthadmin/edit, module, view, URL
type: generated
generated: exp:help:examples
---
# /oauthadmin/edit

Eine Ansicht des Moduls `oauthadmin`, definiert in `kernel/private/modules/oauthadmin/module.php` und ausgeführt von `kernel/private/modules/oauthadmin/edit.php`.

## Adresse

```text
/oauthadmin/edit/<ApplicationID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ApplicationID`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `oauthadmin/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | StoreButton: Store, DiscardButton: Discard |
| Parameter der POST-Aktionen | Store (Name: Name, EndPointURI: EndPointURI, Description: Description) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/oauthadmin/edit/1'|ezurl}>oauthadmin/edit</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/oauthadmin/edit/1' );
```

## Siehe auch

- [Alle Ansichten von oauthadmin](README.md)
- [Die Richtlinien von oauthadmin](../../richtlinien/oauthadmin.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/private/modules/oauthadmin/module.php`, `kernel/private/modules/oauthadmin/edit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
