---
title: /content/versionview
audience: developer, admin
category: examples
summary: "Die Ansicht content/versionview: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, versionview, content/versionview, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/versionview

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/versionview.php`.

## Adresse

```text
/content/versionview/<ObjectID>/<EditVersion>/<LanguageCode>/<FromLanguage>/(language)/<Language>/(offset)/<Offset>/(site_access)/<SiteAccess>/(view_mode)/<ViewMode>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ObjectID`, `EditVersion`, `LanguageCode`, `FromLanguage`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(language)` setzt `Language`, `(offset)` setzt `Offset`, `(site_access)` setzt `SiteAccess`, `(view_mode)` setzt `ViewMode`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`content/versionread`](../../richtlinien/content.md#versionread)

Eine Richtlinie auf `content/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezcontentnavigationpart` |
| UI-Kontext | `edit` |
| Einzelne POST-Aktionen | ChangeSettingsButton: ChangeSettings, EditButton: Edit, VersionsButton: Versions, PreviewPublishButton: Publish, PreviewPublishNotNotifyButton: Publish |
| Parameter der POST-Aktionen | ChangeSettings (Language: SelectedLanguage, PlacementID: SelectedPlacement, SiteAccess: SelectedSiteAccess, ViewMode: SelectedViewMode) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/content/versionview/1/1/languagecode/fromlanguage'|ezurl}>content/versionview</a>
<a href={concat( '/content/versionview/1/1/languagecode/fromlanguage/(language)/', 1 )|ezurl}>content/versionview</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/versionview/1/1/languagecode/fromlanguage' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/versionview.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
