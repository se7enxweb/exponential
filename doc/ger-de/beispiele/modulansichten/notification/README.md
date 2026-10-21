---
title: Das Modul notification
audience: developer, admin
category: examples
order: 10
summary: "Die Ansichten des Moduls notification: Adressen, Parameter und Richtlinienfunktionen."
keywords: Beispiele, Referenz, Das Modul notification, examples, reference
type: generated
generated: exp:help:examples
---
# Das Modul notification

Name `eZNotification`, definiert in `kernel/notification/module.php`. 5 Ansichten.

| Ansicht | Adresse | Richtlinienfunktionen |
|---|---|---|
| [addtonotification](addtonotification.md) | `/notification/addtonotification/<ContentNodeID>` | use |
| [job](job.md) | `/notification/job/<JobID>/<Offset>` | administrate |
| [runfilter](runfilter.md) | `/notification/runfilter` | administrate |
| [settings](settings.md) | `/notification/settings` | use |
| [status](status.md) | `/notification/status` | administrate |

- [Die Richtlinien dieses Moduls](../../richtlinien/notification.md)

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/notification/module.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
