---
title: /rss/edit_import
audience: developer, admin
category: examples
summary: "Die Ansicht rss/edit_import: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, rss, edit_import, rss/edit_import, module, view, URL
type: generated
generated: exp:help:examples
---
# /rss/edit_import

Eine Ansicht des Moduls `rss`, definiert in `kernel/rss/module.php` und ausgeführt von `kernel/rss/edit_import.php`.

## Adresse

```text
/rss/edit_import/<RSSImportID>/<BrowseType>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `RSSImportID`, `BrowseType`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`rss/edit`](../../richtlinien/rss.md#edit)

Eine Richtlinie auf `rss/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `edit` |
| Einzelne POST-Aktionen | StoreButton: Store, RemoveButton: Cancel, AnalyzeFeedButton: AnalyzeFeed, Update_Class: UpdateClass, DestinationBrowse: BrowseDestination, UserBrowse: BrowseUser |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/rss/edit_import/1/browsetype'|ezurl}>rss/edit_import</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/rss/edit_import/1/browsetype' );
```

## Siehe auch

- [Alle Ansichten von rss](README.md)
- [Die Richtlinien von rss](../../richtlinien/rss.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/rss/module.php`, `kernel/rss/edit_import.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
