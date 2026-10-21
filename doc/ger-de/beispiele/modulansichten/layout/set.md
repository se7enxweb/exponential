---
title: /layout/set
audience: developer, admin
category: examples
summary: "Die Ansicht layout/set: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, layout, set, layout/set, module, view, URL
type: generated
generated: exp:help:examples
---
# /layout/set

Eine Ansicht des Moduls `layout`, definiert in `kernel/layout/module.php` und ausgeführt von `kernel/layout/set.php`.

## Adresse

```text
/layout/set/<LayoutStyle>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `LayoutStyle`.

## Wer sie öffnen darf

Aufgeführt in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: Es wird keine Richtlinie geprüft, jeder darf sie öffnen.
Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `layout/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/layout/set/layoutstyle'|ezurl}>layout/set</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/layout/set/layoutstyle' );
```

## Siehe auch

- [Alle Ansichten von layout](README.md)
- [Die Richtlinien von layout](../../richtlinien/layout.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/layout/module.php`, `kernel/layout/set.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
