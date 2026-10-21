# Änderungsverzeichnis: sevenx_valkey_cache

Jede Änderung an `sevenx_valkey_cache` seit Beginn der se7enxweb-Ära, die älteste zuerst: 4 Änderungen an 18 Dateien. Jede Zeile nennt das Datum, den Commit, die Art der Änderung, ihre Commit-Nachricht und wie viele Dateien sie berührt hat (+ hinzugefügte / − entfernte Zeilen). Release-Tags sind markiert. Die erzählende Dokumentation (Funktionen, BC-Hinweise, Spezifikationen, die Chronik) erklärt, was diese Änderungen bringen; dieses Verzeichnis ist die vollständige Aufzeichnung, auf der sie beruht. Die Commit-Nachrichten stehen hier so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Hinzugefügt | 2 |
| Sonstiges | 1 |
| Aktualisiert | 1 |

## 2026-07 (4 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-07-20 | `823d27c` | Hinzugefügt | Added: Initial commit of stable extension sevenx_valkey_cache which replaces file cache with redis/valkey storage for high performance caching page speed loading times. Drastic improvements in speed. Requires changes found only in Exponential 6.0.15 or later. | 14 | +2546 / −0 | 1.0.0.0 |
| 2026-07-20 | `573b201` | Hinzugefügt | Add ezvalkeycacheclear.php CLI script for full cache clearing. | 1 | +278 / −0 |  |
| 2026-07-20 | `815d37b` | Sonstiges | Prepare 1.0.1 release and update documentation for Exponential 6.0.15. | 2 | +42 / −43 | v1.0.2.0 |
| 2026-07-30 | `2bd09c4` | Aktualisiert | Updated: Store compiled INI cache in a single Redis hash | 1 | +38 / −78 |  |
