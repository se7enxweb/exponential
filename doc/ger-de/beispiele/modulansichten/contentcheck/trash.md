---
title: /contentcheck/trash
audience: developer, admin
category: examples
summary: "Die Ansicht contentcheck/trash: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, contentcheck, trash, contentcheck/trash, module, view, URL
type: generated
generated: exp:help:examples
---
# /contentcheck/trash

Eine Ansicht des Moduls `contentcheck`, definiert in `kernel/contentcheck/module.php` und ausgeführt von `kernel/contentcheck/trash.php`.

## Adresse

```text
/contentcheck/trash
```

## Wer sie öffnen darf

Der Benutzer braucht alle diese (die Einträge werden mit „und“ verknüpft); ein Eintrag mit „oder“ braucht eine seiner Funktionen:

- [`contentcheck/unreferenced`](../../richtlinien/contentcheck.md#unreferenced)
- [`contentcheck/trash`](../../richtlinien/contentcheck.md#trash)

Eine Richtlinie auf `contentcheck/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/contentcheck/trash'|ezurl}>contentcheck/trash</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/contentcheck/trash' );
```

## Siehe auch

- [Alle Ansichten von contentcheck](README.md)
- [Die Richtlinien von contentcheck](../../richtlinien/contentcheck.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/contentcheck/module.php`, `kernel/contentcheck/trash.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
