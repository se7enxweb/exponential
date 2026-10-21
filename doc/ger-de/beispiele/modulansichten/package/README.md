---
title: Das Modul package
audience: developer, admin
category: examples
order: 10
summary: "Die Ansichten des Moduls package: Adressen, Parameter und Richtlinienfunktionen."
keywords: Beispiele, Referenz, Das Modul package, examples, reference
type: generated
generated: exp:help:examples
---
# Das Modul package

Name `eZPackage`, definiert in `kernel/package/module.php`. 9 Ansichten.

| Ansicht | Adresse | Richtlinienfunktionen |
|---|---|---|
| [compare](compare.md) | `/package/compare/<PackageName>` | read |
| [create](create.md) | `/package/create` | create |
| [export](export.md) | `/package/export/<PackageName>/<RepositoryID>` | export |
| [install](install.md) | `/package/install/<PackageName>` | install |
| [list](list.md) | `/package/list/<RepositoryID>` | list |
| [uninstall](uninstall.md) | `/package/uninstall/<PackageName>` | install |
| [upload](upload.md) | `/package/upload` | import |
| [view](view.md) | `/package/view/<ViewMode>/<PackageName>/<RepositoryID>` | read |
| [viewfile](viewfile.md) | `/package/viewfile/<PackageName>/<FileIndex>/<RepositoryID>` | read |

- [Die Richtlinien dieses Moduls](../../richtlinien/package.md)

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/module.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
