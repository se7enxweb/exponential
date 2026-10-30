---
title: /graphql/playground
audience: developer, admin
category: examples
summary: "Die Ansicht graphql/playground: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, graphql, playground, graphql/playground, module, view, URL
type: generated
generated: exp:help:examples
---
# /graphql/playground

Eine Ansicht des Moduls `graphql`, definiert in `kernel/graphql/module.php` und ausgeführt von `kernel/graphql/playground.php`.

## Adresse

```text
/graphql/playground
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`graphql/playground`](../../richtlinien/graphql.md#playground)

Eine Richtlinie auf `graphql/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/graphql/playground'|ezurl}>graphql/playground</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/graphql/playground' );
```

## Siehe auch

- [Alle Ansichten von graphql](README.md)
- [Die Richtlinien von graphql](../../richtlinien/graphql.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/graphql/module.php`, `kernel/graphql/playground.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
