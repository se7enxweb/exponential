---
title: /settings/edit
audience: developer, admin
category: examples
summary: "Die Ansicht settings/edit: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, settings, edit, settings/edit, module, view, URL
type: generated
generated: exp:help:examples
---
# /settings/edit

Eine Ansicht des Moduls `settings`, definiert in `kernel/settings/module.php` und ausgeführt von `kernel/settings/edit.php`.

## Adresse

```text
/settings/edit/<SiteAccess>/<INIFile>/<Block>/<Setting>/<Placement>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `SiteAccess`, `INIFile`, `Block`, `Setting`, `Placement`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `settings/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `edit` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/settings/edit/siteaccess/inifile/block/setting/placement'|ezurl}>settings/edit</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/settings/edit/siteaccess/inifile/block/setting/placement' );
```

## Siehe auch

- [Alle Ansichten von settings](README.md)
- [Die Richtlinien von settings](../../richtlinien/settings.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/settings/module.php`, `kernel/settings/edit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
