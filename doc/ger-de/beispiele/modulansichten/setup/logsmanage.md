---
title: /setup/logsmanage
audience: developer, admin
category: examples
summary: "Die Ansicht setup/logsmanage: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, logsmanage, setup/logsmanage, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/logsmanage

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/logsmanage.php`.

## Adresse

```text
/setup/logsmanage/<Key>/(op)/<op>/(days)/<days>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `Key`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(op)` setzt `op`, `(days)` setzt `days`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`setup/managelogs`](../../richtlinien/setup.md#managelogs)

Eine Richtlinie auf `setup/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/setup/logsmanage/key'|ezurl}>setup/logsmanage</a>
<a href={concat( '/setup/logsmanage/key/(op)/', 1 )|ezurl}>setup/logsmanage</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/logsmanage/key' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/logsmanage.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
