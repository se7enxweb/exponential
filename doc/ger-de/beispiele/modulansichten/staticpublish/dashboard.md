---
title: /staticpublish/dashboard
audience: developer, admin
category: examples
summary: "Die Ansicht staticpublish/dashboard: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, staticpublish, dashboard, staticpublish/dashboard, module, view, URL
type: generated
generated: exp:help:examples
---
# /staticpublish/dashboard

Eine Ansicht des Moduls `staticpublish`, definiert in `kernel/staticpublish/module.php` und ausgeführt von `kernel/staticpublish/dashboard.php`.

## Adresse

```text
/staticpublish/dashboard
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`staticpublish/read`](../../richtlinien/staticpublish.md#read)

Eine Richtlinie auf `staticpublish/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | SwitchButton: Switch |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/staticpublish/dashboard'|ezurl}>staticpublish/dashboard</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/staticpublish/dashboard' );
```

## Siehe auch

- [Alle Ansichten von staticpublish](README.md)
- [Die Richtlinien von staticpublish](../../richtlinien/staticpublish.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/staticpublish/module.php`, `kernel/staticpublish/dashboard.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
