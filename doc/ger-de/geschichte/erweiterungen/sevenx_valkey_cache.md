# sevenx_valkey_cache (Cache mit Redis und Valkey): Chronik

Das Cache-Backend für Redis und Valkey wurde am 20. Juli 2026 veröffentlicht, mit einem Script zum Leeren des Caches, der Vorbereitung des Releases für Exponential 6.0.15 und, am 30. Juli, einem einzigen Hash für den kompilierten INI-Cache. Siehe die [Funktionsseite](../../funktionen/6.0/erweiterungen/sevenx_valkey_cache.md).

Diese Seite führt **jede der 4 Änderungen** des Repositorys `sevenx_valkey_cache` zwischen 2026-07-20 und 2026-07-30 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/sevenx_valkey_cache.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/sevenx_valkey_cache.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../funktionen/6.0/erweiterungen/sevenx_valkey_cache.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 1 |
| Leistung | 2 |
| Release | 1 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2026-07-20 | 1.0.0.0 | [`823d27c`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/823d27c) |
| 2026-07-20 | v1.0.2.0 | [`815d37b`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/815d37b) |

## Zeitleiste

### 2026-07

Der Monat über alle Erweiterungen: [Juli 2026](monate/2026-07.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/sevenx_valkey_cache.md#2026-07-4-änderungen).

- 2026-07-20 [`823d27c`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/823d27c) (Leistung) Initial commit of stable extension sevenx_valkey_cache which replaces file cache with redis/valkey storage for high performance caching page speed loading times. Drastic improvements in speed. Requires changes found only in Exponential 6.0.15 or later. **Release 1.0.0.0.**
- 2026-07-20 [`573b201`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/573b201) (Funktion) Add ezvalkeycacheclear.php CLI script for full cache clearing.
- 2026-07-20 [`815d37b`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/815d37b) (Release) Prepare 1.0.1 release and update documentation for Exponential 6.0.15. **Release v1.0.2.0.**
- 2026-07-30 [`2bd09c4`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/2bd09c4) (Leistung) Store compiled INI cache in a single Redis hash

## Verwandte Seiten

- [Funktionsseite](../../funktionen/6.0/erweiterungen/sevenx_valkey_cache.md)
- [Release Notes](../../../changelogs/extensions/sevenx_valkey_cache.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/sevenx_valkey_cache.md)
- [Verhaltensänderungen der Erweiterungen](../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
