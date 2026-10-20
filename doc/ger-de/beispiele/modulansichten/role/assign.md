---
title: /role/assign
audience: developer, admin
category: examples
summary: "Die Ansicht role/assign: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Rolle, Modul, Ansicht, role, assign, role/assign, module, view, URL
type: generated
generated: exp:help:examples
---
# /role/assign

Eine Ansicht des Moduls `role`, definiert in `kernel/role/module.php` und ausgeführt von `kernel/role/assign.php`.

## Adresse

```text
/role/assign/<RoleID>/<LimitIdent>/<LimitValue>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `RoleID`, `LimitIdent`, `LimitValue`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `role/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezusernavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/role/assign/1/limitident/limitvalue'|ezurl}>role/assign</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/role/assign/1/limitident/limitvalue' );
```

## Siehe auch

- [Alle Ansichten von role](README.md)
- [Die Richtlinien von role](../../richtlinien/role.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/role/module.php`, `kernel/role/assign.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
