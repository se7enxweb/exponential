---
title: /role/edit
audience: developer, admin
category: examples
summary: "Die Ansicht role/edit: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Rolle, Modul, Ansicht, role, edit, role/edit, module, view, URL
type: generated
generated: exp:help:examples
---
# /role/edit

Eine Ansicht des Moduls `role`, definiert in `kernel/role/module.php` und ausgeführt von `kernel/role/edit.php`.

## Adresse

```text
/role/edit/<RoleID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `RoleID`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `role/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezusernavigationpart` |
| UI-Kontext | `edit` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/role/edit/1'|ezurl}>role/edit</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/role/edit/1' );
```

## Siehe auch

- [Alle Ansichten von role](README.md)
- [Die Richtlinien von role](../../richtlinien/role.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/role/module.php`, `kernel/role/edit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
