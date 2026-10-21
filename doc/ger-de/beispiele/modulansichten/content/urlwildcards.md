---
title: /content/urlwildcards
audience: developer, admin
category: examples
summary: "Die Ansicht content/urlwildcards: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, urlwildcards, content/urlwildcards, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/urlwildcards

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/urlalias_wildcard.php`.

## Adresse

```text
/content/urlwildcards/(offset)/<Offset>/(q)/<Query>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`, `(q)` setzt `Query`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`content/urltranslator`](../../richtlinien/content.md#urltranslator)

Eine Richtlinie auf `content/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |
| Einzelne POST-Aktionen | NewWildcardButton: NewWildcard, RemoveAllWildcardsButton: RemoveAllWildcards, RemoveWildcardButton: RemoveWildcard |
| Parameter der POST-Aktionen | RemoveWildcard (WildcardIDList: WildcardIDList, Offset: Offset), NewWildcard (WildcardType: WildcardType, WildcardSourceText: WildcardSourceText, WildcardDestinationText: WildcardDestinationText, Offset: Offset) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/content/urlwildcards'|ezurl}>content/urlwildcards</a>
<a href={concat( '/content/urlwildcards/(offset)/', 1 )|ezurl}>content/urlwildcards</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/urlwildcards' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/urlalias_wildcard.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
