---
title: /ezoe/engine
audience: developer, admin
category: examples
summary: "Die Ansicht ezoe/engine: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, ezoe, engine, ezoe/engine, module, view, URL
type: generated
generated: exp:help:examples
---
# /ezoe/engine

Eine Ansicht des Moduls `ezoe`, definiert in `extension/ezoe/modules/ezoe/module.php` und ausgeführt von `extension/ezoe/modules/ezoe/engine.php`.

## Adresse

```text
/ezoe/engine
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`ezoe/editor`](../../richtlinien/ezoe.md#editor)

Eine Richtlinie auf `ezoe/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| UI-Kontext | `administration` |
| Einzelne POST-Aktionen | SaveEngineButton: Save |
| Parameter der POST-Aktionen | Save (Engine: Engine) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/ezoe/engine'|ezurl}>ezoe/engine</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/ezoe/engine' );
```

## Siehe auch

- [Alle Ansichten von ezoe](README.md)
- [Die Richtlinien von ezoe](../../richtlinien/ezoe.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezoe/modules/ezoe/module.php`, `extension/ezoe/modules/ezoe/engine.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
