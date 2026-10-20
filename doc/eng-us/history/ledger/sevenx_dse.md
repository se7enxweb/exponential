# Change ledger: sevenx_dse

Every change made to `sevenx_dse` since the se7enxweb era began, oldest first: 11 changes touching 250 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 8 |
| Added | 2 |
| Other | 1 |

## 2026-04 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-21 | `f320455` | Other | Initial Import of 7x Database Source Editor Exponential Extension sevenx_dse. | 233 | +51337 / −0 |  |
| 2026-04-23 | `baa6a74` | Added | Add screenshots section to README | 1 | +11 / −0 | v1.0.0 |

## 2026-09 (9 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-22 | `e84b3d1` | Updated | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 1 | +23 / −19 | v1.1.0 |
| 2026-09-24 | `84688cc` | Updated | Fixed: Fixed the AdminNeo content security policy nonce staying the same for every request a persistent worker serves. | 1 | +5 / −5 |  |
| 2026-09-24 | `8274510` | Updated | Fixed: Fixed a second DSE dashboard request in the same persistent worker dying on AdminNeo redeclaring its classes. | 1 | +10 / −0 | v1.1.1 |
| 2026-09-27 | `420424c` | Updated | Fixed: The ezinfo.php and extension.xml report the release version and name the license in full, so the about page shows them | 2 | +4 / −4 | v1.1.2 |
| 2026-09-28 | `a8c104f` | Added | Added: GitHub funding metadata, the same as the other se7enxweb packages | 1 | +1 / −1 |  |
| 2026-09-29 | `fdadaae` | Updated | Fixed: The navigation part has its own identifier and the menu texts are translated | 5 | +127 / −1 |  |
| 2026-09-29 | `a02495d` | Updated | Updated: Version 1.1.3 | 2 | +2 / −2 | v1.1.3 |
| 2026-09-30 | `c2c8e9a` | Updated | Updated: The description calls the product Exponential | 1 | +1 / −1 |  |
| 2026-09-30 | `8203c12` | Updated | Updated: Version 1.1.4 | 2 | +2 / −2 | v1.1.4 origin/main origin/HEAD |
