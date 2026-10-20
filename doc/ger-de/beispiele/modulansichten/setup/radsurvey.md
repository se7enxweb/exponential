---
title: /setup/radsurvey
audience: developer, admin
category: examples
summary: "Die Ansicht setup/radsurvey: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, radsurvey, setup/radsurvey, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/radsurvey

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/radsurvey.php`.

## Adresse

```text
/setup/radsurvey/(show)/<Show>/(offset)/<Offset>/(find)/<Find>/(check)/<Check>/(kind)/<Kind>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(show)` setzt `Show`, `(offset)` setzt `Offset`, `(find)` setzt `Find`, `(check)` setzt `Check`, `(kind)` setzt `Kind`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`setup/setup`](../../richtlinien/setup.md#setup)

Eine Richtlinie auf `setup/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/setup/radsurvey'|ezurl}>setup/radsurvey</a>
<a href={concat( '/setup/radsurvey/(show)/', 1 )|ezurl}>setup/radsurvey</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/radsurvey' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/radsurvey.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
