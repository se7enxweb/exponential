---
title: /visual/menuconfig
audience: developer, admin
category: examples
summary: "Die Ansicht visual/menuconfig: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, visual, menuconfig, visual/menuconfig, module, view, URL
type: generated
generated: exp:help:examples
---
# /visual/menuconfig

Eine Ansicht des Moduls `visual`, definiert in `kernel/visual/module.php` und ausgeführt von `kernel/visual/menuconfig.php`.

## Adresse

```text
/visual/menuconfig/(siteaccess)/<SiteAccess>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(siteaccess)` setzt `SiteAccess`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `visual/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezvisualnavigationpart` |
| Einzelne POST-Aktionen | StoreButton: Store, SelectCurrentSiteAccessButton: SelectCurrentSiteAccess |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/visual/menuconfig'|ezurl}>visual/menuconfig</a>
<a href={concat( '/visual/menuconfig/(siteaccess)/', 1 )|ezurl}>visual/menuconfig</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/visual/menuconfig' );
```

## Siehe auch

- [Alle Ansichten von visual](README.md)
- [Die Richtlinien von visual](../../richtlinien/visual.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/visual/module.php`, `kernel/visual/menuconfig.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
