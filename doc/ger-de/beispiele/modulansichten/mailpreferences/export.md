---
title: /mailpreferences/export
audience: developer, admin
category: examples
summary: "Die Ansicht mailpreferences/export: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, mailpreferences, export, mailpreferences/export, module, view, URL
type: generated
generated: exp:help:examples
---
# /mailpreferences/export

Eine Ansicht des Moduls `mailpreferences`, definiert in `kernel/mailpreferences/module.php` und ausgeführt von `kernel/mailpreferences/export.php`.

## Adresse

```text
/mailpreferences/export/<Format>/(token)/<Token>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `Format`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(token)` setzt `Token`.

## Wer sie öffnen darf

Aufgeführt in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: Es wird keine Richtlinie geprüft, jeder darf sie öffnen.
Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `mailpreferences/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezmynavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/mailpreferences/export/format'|ezurl}>mailpreferences/export</a>
<a href={concat( '/mailpreferences/export/format/(token)/', 1 )|ezurl}>mailpreferences/export</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/mailpreferences/export/format' );
```

## Siehe auch

- [Alle Ansichten von mailpreferences](README.md)
- [Die Richtlinien von mailpreferences](../../richtlinien/mailpreferences.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/mailpreferences/module.php`, `kernel/mailpreferences/export.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
