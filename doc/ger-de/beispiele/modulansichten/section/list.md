---
title: /section/list
audience: developer, admin
category: examples
summary: "Die Ansicht section/list: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, section, list, section/list, module, view, URL
type: generated
generated: exp:help:examples
---
# /section/list

Eine Ansicht des Moduls `section`, definiert in `kernel/section/module.php` und ausgeführt von `kernel/section/list.php`.

## Adresse

```text
/section/list/(offset)/<Offset>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`section/view`](../../richtlinien/section.md#view) oder [`section/edit`](../../richtlinien/section.md#edit) oder [`section/assign`](../../richtlinien/section.md#assign)

Eine Richtlinie auf `section/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/section/list'|ezurl}>section/list</a>
<a href={concat( '/section/list/(offset)/', 1 )|ezurl}>section/list</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/section/list' );
```

## Siehe auch

- [Alle Ansichten von section](README.md)
- [Die Richtlinien von section](../../richtlinien/section.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/section/module.php`, `kernel/section/list.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
