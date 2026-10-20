---
title: /role/view
audience: developer, admin
category: examples
summary: "Die Ansicht role/view: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Rolle, Ansicht, Modul, role, view, role/view, module, URL
type: generated
generated: exp:help:examples
---
# /role/view

Eine Ansicht des Moduls `role`, definiert in `kernel/role/module.php` und ausgeführt von `kernel/role/view.php`.

## Adresse

```text
/role/view/<RoleID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `RoleID`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `role/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezusernavigationpart` |
| POST-Aktionen | BrowseActionName |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/role/view/1'|ezurl}>role/view</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/role/view/1' );
```

## Siehe auch

- [Alle Ansichten von role](README.md)
- [Die Richtlinien von role](../../richtlinien/role.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/role/module.php`, `kernel/role/view.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
