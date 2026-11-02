---
title: Das Modul migration
audience: developer, admin
category: examples
order: 10
summary: "Die Ansichten des Moduls migration: Adressen, Parameter und Richtlinienfunktionen."
keywords: Beispiele, Referenz, Das Modul migration, examples, reference
type: generated
generated: exp:help:examples
---
# Das Modul migration

Name `Migrations`, definiert in `kernel/migration/module.php`. 14 Ansichten.

| Ansicht | Adresse | Richtlinienfunktionen |
|---|---|---|
| [deploy](deploy.md) | `/migration/deploy` | read |
| [deployment](deployment.md) | `/migration/deployment/<DeployID>` | read |
| [dryrun](dryrun.md) | `/migration/dryrun` | read |
| [editor](editor.md) | `/migration/editor/<MigrationID>` | edit |
| [history](history.md) | `/migration/history` | read |
| [log](log.md) | `/migration/log/<RunID>` | read |
| [recording](recording.md) | `/migration/recording/<RecordingID>` | edit |
| [remote](remote.md) | `/migration/remote/<RemoteID>` | remotes |
| [remotes](remotes.md) | `/migration/remotes` | read |
| [rollback](rollback.md) | `/migration/rollback/<Target>` | rollback |
| [run](run.md) | `/migration/run` | run |
| [status](status.md) | `/migration/status` | read |
| [stream](stream.md) | `/migration/stream/<RunID>` | read |
| [trigger](trigger.md) | `/migration/trigger` | (Modul) |

- [Die Richtlinien dieses Moduls](../../richtlinien/migration.md)

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/migration/module.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
