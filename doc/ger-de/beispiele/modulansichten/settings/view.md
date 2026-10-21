---
title: /settings/view
audience: developer, admin
category: examples
summary: "Die Ansicht settings/view: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Ansicht, Modul, settings, view, settings/view, module, URL
type: generated
generated: exp:help:examples
---
# /settings/view

Eine Ansicht des Moduls `settings`, definiert in `kernel/settings/module.php` und ausgeführt von `kernel/settings/view.php`.

## Adresse

```text
/settings/view/<SiteAccess>/<INIFile>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `SiteAccess`, `INIFile`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `settings/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/settings/view/siteaccess/inifile'|ezurl}>settings/view</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/settings/view/siteaccess/inifile' );
```

## Siehe auch

- [Alle Ansichten von settings](README.md)
- [Die Richtlinien von settings](../../richtlinien/settings.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/settings/module.php`, `kernel/settings/view.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
