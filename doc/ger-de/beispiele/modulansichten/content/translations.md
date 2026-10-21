---
title: /content/translations
audience: developer, admin
category: examples
summary: "Die Ansicht content/translations: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, translations, content/translations, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/translations

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/translations.php`.

## Adresse

```text
/content/translations/<TranslationID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `TranslationID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`content/translations`](../../richtlinien/content.md#translations)

Eine Richtlinie auf `content/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |
| Einzelne POST-Aktionen | RemoveButton: Remove, StoreButton: StoreNew, NewButton: New, ConfirmButton: Confirm |
| Parameter der POST-Aktionen | StoreNew (LocaleID: LocaleID, TranslationName: TranslationName, TranslationLocale: TranslationLocale), Remove (SelectedTranslationList: DeleteIDArray), Confirm (ConfirmList: ConfirmTranlationID) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/content/translations/1'|ezurl}>content/translations</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/translations/1' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/translations.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
