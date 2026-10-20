---
title: /setup/toolbarlist
audience: developer, admin
category: examples
summary: "Die Ansicht setup/toolbarlist: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, toolbarlist, setup/toolbarlist, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/toolbarlist

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/toolbarlist.php`.

## Adresse

```text
/setup/toolbarlist/<SiteAccess>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `SiteAccess`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`setup/setup`](../../richtlinien/setup.md#setup)

Eine Richtlinie auf `setup/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/setup/toolbarlist/siteaccess'|ezurl}>setup/toolbarlist</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/toolbarlist/siteaccess' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/toolbarlist.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
