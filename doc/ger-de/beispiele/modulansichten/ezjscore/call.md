---
title: /ezjscore/call
audience: developer, admin
category: examples
summary: "Die Ansicht ezjscore/call: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, ezjscore, call, ezjscore/call, module, view, URL
type: generated
generated: exp:help:examples
---
# /ezjscore/call

Eine Ansicht des Moduls `ezjscore`, definiert in `extension/ezjscore/modules/ezjscore/module.php` und ausgeführt von `extension/ezjscore/modules/ezjscore/call.php`.

## Adresse

```text
/ezjscore/call/<function_arguments>/<type>/<interval>/<debug>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `function_arguments`, `type`, `interval`, `debug`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`ezjscore/call`](../../richtlinien/ezjscore.md#call)

Eine Richtlinie auf `ezjscore/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/ezjscore/call/function-arguments/type/interval/debug'|ezurl}>ezjscore/call</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/ezjscore/call/function-arguments/type/interval/debug' );
```

## Siehe auch

- [Alle Ansichten von ezjscore](README.md)
- [Die Richtlinien von ezjscore](../../richtlinien/ezjscore.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/modules/ezjscore/module.php`, `extension/ezjscore/modules/ezjscore/call.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
