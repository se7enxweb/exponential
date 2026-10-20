---
title: /ezjscore/run
audience: developer, admin
category: examples
summary: "Die Ansicht ezjscore/run: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, ezjscore, run, ezjscore/run, module, view, URL
type: generated
generated: exp:help:examples
---
# /ezjscore/run

Eine Ansicht des Moduls `ezjscore`, definiert in `extension/ezjscore/modules/ezjscore/module.php` und ausgeführt von `extension/ezjscore/modules/ezjscore/run.php`.

## Adresse

```text
/ezjscore/run
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`ezjscore/run`](../../richtlinien/ezjscore.md#run)

Eine Richtlinie auf `ezjscore/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/ezjscore/run'|ezurl}>ezjscore/run</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/ezjscore/run' );
```

## Siehe auch

- [Alle Ansichten von ezjscore](README.md)
- [Die Richtlinien von ezjscore](../../richtlinien/ezjscore.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/modules/ezjscore/module.php`, `extension/ezjscore/modules/ezjscore/run.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
