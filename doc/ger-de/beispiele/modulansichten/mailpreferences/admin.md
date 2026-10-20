---
title: /mailpreferences/admin
audience: developer, admin
category: examples
summary: "Die Ansicht mailpreferences/admin: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, mailpreferences, admin, mailpreferences/admin, module, view, URL
type: generated
generated: exp:help:examples
---
# /mailpreferences/admin

Eine Ansicht des Moduls `mailpreferences`, definiert in `kernel/mailpreferences/module.php` und ausgeführt von `kernel/mailpreferences/admin.php`.

## Adresse

```text
/mailpreferences/admin/<Page>/<ID>/(offset)/<Offset>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `Page`, `ID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`mailpreferences/administrate`](../../richtlinien/mailpreferences.md#administrate)

Eine Richtlinie auf `mailpreferences/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/mailpreferences/admin/page/1'|ezurl}>mailpreferences/admin</a>
<a href={concat( '/mailpreferences/admin/page/1/(offset)/', 1 )|ezurl}>mailpreferences/admin</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/mailpreferences/admin/page/1' );
```

## Siehe auch

- [Alle Ansichten von mailpreferences](README.md)
- [Die Richtlinien von mailpreferences](../../richtlinien/mailpreferences.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/mailpreferences/module.php`, `kernel/mailpreferences/admin.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
