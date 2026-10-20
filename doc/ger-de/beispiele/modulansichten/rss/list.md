---
title: /rss/list
audience: developer, admin
category: examples
summary: "Die Ansicht rss/list: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, rss, list, rss/list, module, view, URL
type: generated
generated: exp:help:examples
---
# /rss/list

Eine Ansicht des Moduls `rss`, definiert in `kernel/rss/module.php` und ausgeführt von `kernel/rss/list.php`.

## Adresse

```text
/rss/list/(language)/<Language>/(offset)/<Offset>/(sort)/<Sort>/(dir)/<Dir>/(importoffset)/<ImportOffset>/(importsort)/<ImportSort>/(importdir)/<ImportDir>/(limit)/<Limit>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(language)` setzt `Language`, `(offset)` setzt `Offset`, `(sort)` setzt `Sort`, `(dir)` setzt `Dir`, `(importoffset)` setzt `ImportOffset`, `(importsort)` setzt `ImportSort`, `(importdir)` setzt `ImportDir`, `(limit)` setzt `Limit`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`rss/edit`](../../richtlinien/rss.md#edit)

Eine Richtlinie auf `rss/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/rss/list'|ezurl}>rss/list</a>
<a href={concat( '/rss/list/(language)/', 1 )|ezurl}>rss/list</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/rss/list' );
```

## Siehe auch

- [Alle Ansichten von rss](README.md)
- [Die Richtlinien von rss](../../richtlinien/rss.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/rss/module.php`, `kernel/rss/list.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
