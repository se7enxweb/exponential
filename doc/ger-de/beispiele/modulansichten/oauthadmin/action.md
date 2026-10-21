---
title: /oauthadmin/action
audience: developer, admin
category: examples
summary: "Die Ansicht oauthadmin/action: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, oauthadmin, action, oauthadmin/action, module, view, URL
type: generated
generated: exp:help:examples
---
# /oauthadmin/action

Eine Ansicht des Moduls `oauthadmin`, definiert in `kernel/private/modules/oauthadmin/module.php` und ausgeführt von `kernel/private/modules/oauthadmin/action.php`.

## Adresse

```text
/oauthadmin/action
```

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `oauthadmin/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | NewApplicationButton: NewApplication, DeleteApplicationListButton: DeleteApplicationList |
| Parameter der POST-Aktionen | DeleteApplicationList (ApplicationIDList: DeleteIDArray, ConfirmDelete: ConfirmDelete) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/oauthadmin/action'|ezurl}>oauthadmin/action</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/oauthadmin/action' );
```

## Siehe auch

- [Alle Ansichten von oauthadmin](README.md)
- [Die Richtlinien von oauthadmin](../../richtlinien/oauthadmin.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/private/modules/oauthadmin/module.php`, `kernel/private/modules/oauthadmin/action.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
