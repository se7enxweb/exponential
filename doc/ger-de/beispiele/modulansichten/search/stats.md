---
title: /search/stats
audience: developer, admin
category: examples
summary: "Die Ansicht search/stats: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, search, stats, search/stats, module, view, URL
type: generated
generated: exp:help:examples
---
# /search/stats

Eine Ansicht des Moduls `search`, definiert in `kernel/search/module.php` und ausgeführt von `kernel/search/stats.php`.

## Adresse

```text
/search/stats/(offset)/<Offset>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `search/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | ResetSearchStatsButton: ResetSearchStats |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/search/stats'|ezurl}>search/stats</a>
<a href={concat( '/search/stats/(offset)/', 1 )|ezurl}>search/stats</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/search/stats' );
```

## Siehe auch

- [Alle Ansichten von search](README.md)
- [Die Richtlinien von search](../../richtlinien/search.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/search/module.php`, `kernel/search/stats.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
