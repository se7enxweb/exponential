---
title: Das Modul content
audience: developer, admin
category: examples
order: 10
summary: "Die Ansichten des Moduls content: Adressen, Parameter und Richtlinienfunktionen."
keywords: Beispiele, Referenz, Das Modul content, examples, reference
type: generated
generated: exp:help:examples
---
# Das Modul content

Name `eZContentObject`, definiert in `kernel/content/module.php`. 44 Ansichten.

| Ansicht | Adresse | Richtlinienfunktionen |
|---|---|---|
| [action](action.md) | `/content/action` | read |
| [advancedsearch](advancedsearch.md) | `/content/advancedsearch/<ViewMode>` | read |
| [bookmark](bookmark.md) | `/content/bookmark` | bookmark |
| [browse](browse.md) | `/content/browse/<NodeID>/<ObjectID>/<EditVersion>` | read |
| [collectedinfo](collectedinfo.md) | `/content/collectedinfo/<NodeID>` | read |
| [collectinformation](collectinformation.md) | `/content/collectinformation` | read |
| [copy](copy.md) | `/content/copy/<ObjectID>` | read |
| [copysubtree](copysubtree.md) | `/content/copysubtree/<NodeID>` | create |
| [dashboard](dashboard.md) | `/content/dashboard` | dashboard |
| [download](download.md) | `/content/download/<ContentObjectID>/<ContentObjectAttributeID>` | read |
| [draft](draft.md) | `/content/draft` | edit |
| [edit](edit.md) | `/content/edit/<ObjectID>/<EditVersion>/<EditLanguage>/<FromLanguage>` | edit or create |
| [hide](hide.md) | `/content/hide/<NodeID>` | hide |
| [history](history.md) | `/content/history/<ObjectID>/<EditVersion>` | read or edit |
| [job](job.md) | `/content/job/<JobID>` | read |
| [jobs](jobs.md) | `/content/jobs` | read |
| [keyword](keyword.md) | `/content/keyword/<Alphabet>` | read |
| [move](move.md) | `/content/move/<NodeID>` | edit |
| [multiedit](multiedit.md) | `/content/multiedit` | edit |
| [new](new.md) | `/content/new` | read |
| [pdf](pdf.md) | `/content/pdf/<NodeID>` | pdf |
| [pendinglist](pendinglist.md) | `/content/pendinglist` | pendinglist |
| [queued](queued.md) | `/content/queued/<ContentObjectID>/<version>` | edit |
| [removeassignment](removeassignment.md) | `/content/removeassignment` | edit |
| [removeeditversion](removeeditversion.md) | `/content/removeeditversion` | read |
| [removemediaobject](removemediaobject.md) | `/content/removemediaobject` | read |
| [removenode](removenode.md) | `/content/removenode/<ObjectID>/<EditVersion>/<EditLanguage>/<NodeID>` | edit |
| [removeobject](removeobject.md) | `/content/removeobject` | read |
| [removeuserobject](removeuserobject.md) | `/content/removeuserobject` | read |
| [restore](restore.md) | `/content/restore/<ObjectID>` | restore |
| [reverserelatedlist](reverserelatedlist.md) | `/content/reverserelatedlist/<NodeID>` | reverserelatedlist |
| [search](search.md) | `/content/search` | read |
| [subitemsexport](subitemsexport.md) | `/content/subitemsexport/<NodeID>` | read |
| [tipafriend](tipafriend.md) | `/content/tipafriend/<NodeID>` | tipafriend; read |
| [translation](translation.md) | `/content/translation` | read |
| [translations](translations.md) | `/content/translations/<TranslationID>` | translations |
| [trash](trash.md) | `/content/trash` | restore |
| [treemenu](treemenu.md) | `/content/treemenu/<NodeID>/<Modified>/<Expiry>/<Perm>` | read |
| [upload](upload.md) | `/content/upload` | create |
| [urlalias](urlalias.md) | `/content/urlalias/<NodeID>` | edit |
| [urltranslator](urltranslator.md) | `/content/urltranslator` | urltranslator |
| [urlwildcards](urlwildcards.md) | `/content/urlwildcards` | urltranslator |
| [versionview](versionview.md) | `/content/versionview/<ObjectID>/<EditVersion>/<LanguageCode>/<FromLanguage>` | versionread |
| [view](view.md) | `/content/view/<ViewMode>/<NodeID>` | read |

- [Die Richtlinien dieses Moduls](../../richtlinien/content.md)

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
