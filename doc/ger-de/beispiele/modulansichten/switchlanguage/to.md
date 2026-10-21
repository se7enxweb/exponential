---
title: /switchlanguage/to
audience: developer, admin
category: examples
summary: "Die Ansicht switchlanguage/to: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, switchlanguage, to, switchlanguage/to, module, view, URL
type: generated
generated: exp:help:examples
---
# /switchlanguage/to

Eine Ansicht des Moduls `switchlanguage`, definiert in `kernel/private/modules/switchlanguage/module.php` und ausgeführt von `kernel/private/modules/switchlanguage/to.php`.

## Adresse

```text
/switchlanguage/to/<SiteAccess>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `SiteAccess`.

## Wer sie öffnen darf

Aufgeführt in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: Es wird keine Richtlinie geprüft, jeder darf sie öffnen.
Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `switchlanguage/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/switchlanguage/to/siteaccess'|ezurl}>switchlanguage/to</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/switchlanguage/to/siteaccess' );
```

## Siehe auch

- [Alle Ansichten von switchlanguage](README.md)
- [Die Richtlinien von switchlanguage](../../richtlinien/switchlanguage.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/private/modules/switchlanguage/module.php`, `kernel/private/modules/switchlanguage/to.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
