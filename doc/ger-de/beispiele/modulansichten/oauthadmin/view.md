---
title: /oauthadmin/view
audience: developer, admin
category: examples
summary: "Die Ansicht oauthadmin/view: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Ansicht, Modul, oauthadmin, view, oauthadmin/view, module, URL
type: generated
generated: exp:help:examples
---
# /oauthadmin/view

Eine Ansicht des Moduls `oauthadmin`, definiert in `kernel/private/modules/oauthadmin/module.php` und ausgeführt von `kernel/private/modules/oauthadmin/view.php`.

## Adresse

```text
/oauthadmin/view/<ApplicationID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ApplicationID`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `oauthadmin/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/oauthadmin/view/1'|ezurl}>oauthadmin/view</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/oauthadmin/view/1' );
```

## Siehe auch

- [Alle Ansichten von oauthadmin](README.md)
- [Die Richtlinien von oauthadmin](../../richtlinien/oauthadmin.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/private/modules/oauthadmin/module.php`, `kernel/private/modules/oauthadmin/view.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
