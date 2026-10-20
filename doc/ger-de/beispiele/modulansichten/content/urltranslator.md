---
title: /content/urltranslator
audience: developer, admin
category: examples
summary: "Die Ansicht content/urltranslator: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, urltranslator, content/urltranslator, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/urltranslator

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/urlalias_global.php`.

## Adresse

```text
/content/urltranslator/(offset)/<Offset>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`content/urltranslator`](../../richtlinien/content.md#urltranslator)

Eine Richtlinie auf `content/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |
| Einzelne POST-Aktionen | NewAliasButton: NewAlias, RemoveAllAliasesButton: RemoveAllAliases, RemoveAliasButton: RemoveAlias |
| Parameter der POST-Aktionen | RemoveAlias (ElementList: ElementList), NewAlias (LanguageCode: LanguageCode, AliasSourceText: AliasSourceText, AliasDestinationText: AliasDestinationText) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/content/urltranslator'|ezurl}>content/urltranslator</a>
<a href={concat( '/content/urltranslator/(offset)/', 1 )|ezurl}>content/urltranslator</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/urltranslator' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/urlalias_global.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
