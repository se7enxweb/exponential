---
title: /content/translation
audience: developer, admin
category: examples
summary: "Die Ansicht content/translation: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, translation, content/translation, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/translation

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/translation.php`.

## Adresse

```text
/content/translation
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`content/read`](../../richtlinien/content.md#read)

Eine Richtlinie auf `content/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezcontentnavigationpart` |
| Einzelne POST-Aktionen | CancelButton: Cancel, UpdateInitialLanguageButton: UpdateInitialLanguage, UpdateAlwaysAvailableButton: UpdateAlwaysAvailable, RemoveTranslationButton: RemoveTranslation |
| Parameter der POST-Aktionen | Cancel (NodeID: ContentNodeID, ViewMode: ViewMode, LanguageCode: ContentObjectLanguageCode), UpdateInitialLanguage (InitialLanguageID: InitialLanguageID, NodeID: ContentNodeID, ObjectID: ContentObjectID, ViewMode: ViewMode, LanguageCode: ContentObjectLanguageCode), UpdateAlwaysAvailable (AlwaysAvailable: AlwaysAvailable, NodeID: ContentNodeID, ObjectID: ContentObjectID, ViewMode: ViewMode, LanguageCode: ContentObjectLanguageCode), RemoveTranslation (LanguageID: LanguageID, ConfirmRemoval: ConfirmRemoval, NodeID: ContentNodeID, ObjectID: ContentObjectID, ViewMode: ViewMode, LanguageCode: ContentObjectLanguageCode) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/content/translation'|ezurl}>content/translation</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/translation' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/translation.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
