---
title: Das Modul ezoe
audience: developer, admin
category: examples
order: 10
summary: "Die Ansichten des Moduls ezoe: Adressen, Parameter und Richtlinienfunktionen."
keywords: Beispiele, Referenz, Das Modul ezoe, examples, reference
type: generated
generated: exp:help:examples
---
# Das Modul ezoe

Name `eZ Online Editor Module Views, for dialoges and internal ajax use!`, definiert in `extension/ezoe/modules/ezoe/module.php`. 8 Ansichten.

| Ansicht | Adresse | Richtlinienfunktionen |
|---|---|---|
| [dialog](dialog.md) | `/ezoe/dialog/<ObjectID>/<ObjectVersion>/<Dialog>` | editor |
| [embed_view](embed_view.md) | `/ezoe/embed_view/<EmbedID>` | editor |
| [engine](engine.md) | `/ezoe/engine` | editor |
| [load](load.md) | `/ezoe/load/<EmbedID>/<DataMap>/<ImagePreGenerateSizes>` | editor |
| [relations](relations.md) | `/ezoe/relations/<ObjectID>/<ObjectVersion>/<ContentType>/<EmbedID>/<EmbedInline>/<EmbedSize>` | editor |
| [spellcheck_rpc](spellcheck_rpc.md) | `/ezoe/spellcheck_rpc` | editor |
| [tags](tags.md) | `/ezoe/tags/<ObjectID>/<ObjectVersion>/<TagName>/<CustomTagName>` | editor |
| [upload](upload.md) | `/ezoe/upload/<ObjectID>/<ObjectVersion>/<ContentType>/<ForcedUpload>` | editor |

- [Die Richtlinien dieses Moduls](../../richtlinien/ezoe.md)

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezoe/modules/ezoe/module.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
