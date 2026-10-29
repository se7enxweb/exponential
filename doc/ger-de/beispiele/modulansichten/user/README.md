---
title: Das Modul user
audience: developer, admin
category: examples
order: 10
summary: "Die Ansichten des Moduls user: Adressen, Parameter und Richtlinienfunktionen."
keywords: Beispiele, Referenz, Das Modul user, examples, reference
type: generated
generated: exp:help:examples
---
# Das Modul user

Name `User management`, definiert in `kernel/user/module.php`. 20 Ansichten.

| Ansicht | Adresse | Richtlinienfunktionen |
|---|---|---|
| [activate](activate.md) | `/user/activate/<Hash>/<MainNodeID>` | login |
| [audit](audit.md) | `/user/audit` | audit |
| [deactivate](deactivate.md) | `/user/deactivate` | deactivate |
| [edit](edit.md) | `/user/edit/<UserID>` | login |
| [forgotpassword](forgotpassword.md) | `/user/forgotpassword/<HashKey>` | password |
| [hygiene](hygiene.md) | `/user/hygiene` | hygiene |
| [login](login.md) | `/user/login` | login |
| [logout](logout.md) | `/user/logout` | login |
| [megamenu](megamenu.md) | `/user/megamenu/<Tab>` | login |
| [passkeys](passkeys.md) | `/user/passkeys` | login |
| [password](password.md) | `/user/password/<UserID>` | password |
| [preferences](preferences.md) | `/user/preferences/<Function>/<Key>/<Value>` | login |
| [register](register.md) | `/user/register/<redirect_number>` | register |
| [registration](registration.md) | `/user/registration/<Token>` | login |
| [security](security.md) | `/user/security/<UserID>` | login |
| [sessionreport](sessionreport.md) | `/user/sessionreport` | sessions_manage |
| [sessions](sessions.md) | `/user/sessions/<UserID>` | login |
| [setting](setting.md) | `/user/setting/<UserID>` | preferences |
| [success](success.md) | `/user/success` | register |
| [unactivated](unactivated.md) | `/user/unactivated/<SortField>/<SortOrder>` | activation |

- [Die Richtlinien dieses Moduls](../../richtlinien/user.md)

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/module.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
