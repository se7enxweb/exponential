---
title: /content/removeassignment
audience: developer, admin
category: examples
summary: "Die Ansicht content/removeassignment: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, removeassignment, content/removeassignment, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/removeassignment

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/removeassignment.php`.

## Adresse

```text
/content/removeassignment
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`content/edit`](../../richtlinien/content.md#edit)

Eine Richtlinie auf `content/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezcontentnavigationpart` |
| UI-Kontext | `edit` |
| UI-Komponente | `content` |
| Einzelne POST-Aktionen | ConfirmRemovalButton: ConfirmRemoval, CancelRemovalButton: CancelRemoval |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/content/removeassignment'|ezurl}>content/removeassignment</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/removeassignment' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/removeassignment.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
