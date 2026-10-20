---
title: /exphelp/pdf
audience: developer, admin
category: examples
summary: "Die Ansicht exphelp/pdf: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, exphelp, pdf, exphelp/pdf, module, view, URL
type: generated
generated: exp:help:examples
---
# /exphelp/pdf

Eine Ansicht des Moduls `exphelp`, definiert in `kernel/exphelp/module.php` und ausgeführt von `kernel/exphelp/pdf.php`.

## Adresse

```text
/exphelp/pdf
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`exphelp/read`](../../richtlinien/exphelp.md#read)

Eine Richtlinie auf `exphelp/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/exphelp/pdf'|ezurl}>exphelp/pdf</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/exphelp/pdf' );
```

## Siehe auch

- [Alle Ansichten von exphelp](README.md)
- [Die Richtlinien von exphelp](../../richtlinien/exphelp.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/exphelp/module.php`, `kernel/exphelp/pdf.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
