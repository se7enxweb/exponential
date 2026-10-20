---
title: /content/edit
audience: developer, admin
category: examples
summary: "Die Ansicht content/edit: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, edit, content/edit, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/edit

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/edit.php`.

## Adresse

```text
/content/edit/<ObjectID>/<EditVersion>/<EditLanguage>/<FromLanguage>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ObjectID`, `EditVersion`, `EditLanguage`, `FromLanguage`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`content/edit`](../../richtlinien/content.md#edit) oder [`content/create`](../../richtlinien/content.md#create)

Eine Richtlinie auf `content/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezcontentnavigationpart` |
| UI-Kontext | `edit` |
| Einzelne POST-Aktionen | PreviewButton: Preview, TranslateButton: Translate, VersionsButton: VersionEdit, PublishButton: Publish, PublishNotNotifyButton: Publish, DiscardButton: Discard, BrowseNodeButton: BrowseForNodes, RemoveAssignmentButton: RemoveAssignments, EditLanguageButton: EditLanguage, FromLanguageButton: FromLanguage, TranslateLanguageButton: TranslateLanguage, BrowseObjectButton: BrowseForObjects, UploadFileRelationButton: UploadFileRelation, NewButton: NewObject, DeleteRelationButton: DeleteRelation, StoreButton: Store, StoreExitButton: StoreExit, MoveNodeID: MoveNode, RemoveNodeID: DeleteNode, ConfirmButton: ConfirmAssignmentDelete, SectionEditButton: SectionEdit, StateEditButton: StateEdit |
| POST-Aktionen | BrowseActionName |
| Parameter der POST-Aktionen | EditLanguage (SelectedLanguage: EditSelectedLanguage), FromLanguage (FromLanguage: FromLanguage), TranslateLanguage (SelectedLanguage: EditSelectedLanguage), UploadFileRelation (UploadRelationLocation: UploadRelationLocationChoice), SectionEdit (RedirectRelativeURI: RedirectRelativeURI) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/content/edit/1/1/editlanguage/fromlanguage'|ezurl}>content/edit</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/edit/1/1/editlanguage/fromlanguage' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/edit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
