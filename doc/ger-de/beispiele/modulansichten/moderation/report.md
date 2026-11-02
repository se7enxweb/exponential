---
title: /moderation/report
audience: developer, admin
category: examples
summary: "Die Ansicht moderation/report: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, moderation, report, moderation/report, module, view, URL
type: generated
generated: exp:help:examples
---
# /moderation/report

Eine Ansicht des Moduls `moderation`, definiert in `kernel/moderation/module.php` und ausgeführt von `kernel/moderation/report.php`.

## Adresse

```text
/moderation/report/<JobID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `JobID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`moderation/act`](../../richtlinien/moderation.md#act)

Eine Richtlinie auf `moderation/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezmynavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/moderation/report/1'|ezurl}>moderation/report</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/moderation/report/1' );
```

## Siehe auch

- [Alle Ansichten von moderation](README.md)
- [Die Richtlinien von moderation](../../richtlinien/moderation.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/moderation/module.php`, `kernel/moderation/report.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
