---
title: /content/action
audience: developer, admin
category: examples
summary: "Die Ansicht content/action: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, action, content/action, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/action

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/action.php`.

## Adresse

```text
/content/action
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`content/read`](../../richtlinien/content.md#read)

Eine Richtlinie auf `content/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezcontentnavigationpart` |
| UI-Kontext | `edit` |
| Einzelne POST-Aktionen | RemoveAssignmentButton: RemoveAssignment, AddAssignmentButton: SelectAssignmentLocation, AddAssignmentAction: AddAssignment, UpdateMainAssignmentButton: UpdateMainAssignment, ClearViewCacheButton: ClearViewCache, ClearViewCacheSubtreeButton: ClearViewCacheSubtree, MoveNodeButton: MoveNodeRequest, MoveNodeAction: MoveNode, SwapNodeButton: SwapNodeRequest, SwapNodeAction: SwapNode, UploadFileAction: UploadFile |
| POST-Aktionen | BrowseActionName |
| Parameter der POST-Aktionen | SelectAssignmentLocation (AssignmentIDSelection: AssignmentIDSelection, NodeID: ContentNodeID, ObjectID: ContentObjectID, ViewMode: ViewMode, LanguageCode: ContentObjectLanguageCode), AddAssignment (AssignmentIDSelection: AssignmentIDSelection, NodeID: ContentNodeID, ObjectID: ContentObjectID, ViewMode: ViewMode, LanguageCode: ContentObjectLanguageCode), RemoveAssignment (AssignmentIDSelection: AssignmentIDSelection, LocationIDSelection: LocationIDSelection, NodeID: ContentNodeID, ObjectID: ContentObjectID, ViewMode: ViewMode, LanguageCode: ContentObjectLanguageCode), UpdateMainAssignment (MainAssignmentID: MainAssignmentCheck, HasMainAssignment: HasMainAssignment, NodeID: ContentNodeID, ObjectID: ContentObjectID, ViewMode: ViewMode, LanguageCode: ContentObjectLanguageCode), ClearViewCache (NodeID: NodeID, ObjectID: ObjectID, ViewMode: ViewMode, LanguageCode: ContentObjectLanguageCode, CurrentURL: CurrentURL), ClearViewCacheSubtree (NodeID: NodeID, ObjectID: ObjectID, ViewMode: ViewMode, LanguageCode: ContentObjectLanguageCode, CurrentURL: CurrentURL), MoveNodeRequest (NodeID: ContentNodeID, ViewMode: ViewMode, LanguageCode: ContentObjectLanguageCode), MoveNode (NodeID: ContentNodeID, ViewMode: ViewMode, NewParentNode: NewParentNode, LanguageCode: ContentObjectLanguageCode), CopyNode (NodeID: ContentNodeID, ViewMode: ViewMode, NewParentNode: NewParentNode, LanguageCode: ContentObjectLanguageCode), SwapNodeRequest (NodeID: ContentNodeID, ViewMode: ViewMode, LanguageCode: ContentObjectLanguageCode), SwapNode (NodeID: ContentNodeID, ViewMode: ViewMode, NewNode: NewNode, LanguageCode: ContentObjectLanguageCode), UploadFile (UploadActionName: UploadActionName, UploadParentNodes: UploadParentNodes, UploadRedirectBack: UploadRedirectBack) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/content/action'|ezurl}>content/action</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/action' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/action.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
