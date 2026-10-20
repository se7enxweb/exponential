# Spezifikation: das Audit-Ereignismodell

Diese Seite ist die technische Zusammenfassung des am 2026-10-02 eingeführten Audit-Protokolls: Ereignisnamen, das
Datensatzformat, Kanäle, Dateien, Checkpoints, Archive, der Datenbankindex, die Klassen, die Einstellungen und die
Erweiterungspunkte. Lesen Sie sie, wenn Sie das Audit-Protokoll betreiben, es an ein Log-System anbinden oder Ereignisse
aus Ihrer eigenen Extension aufzeichnen. Der maßgebliche, deutlich längere Text ist [Audit](../../../en/us/bc/6.0/audit.md); die
Einführung für Anwender ist [das Audit-Protokoll](../../../en/us/features/6.0/audit-trail.md).

## Kurz gesagt

- Jedes Ereignis hat einen Namen mit Punkten, `domain.subject.action[.detail]`, und landet in einem von fünf Kanälen.
- Jeder Kanal ist eine Hash-Kette aus JSON-Zeilen, eine Datei je Kanal und UTC-Tag, unter `var/<site>/log/audit/`.
- `./console exp:audit verify` weist nach, dass eine Kette intakt ist; Exit-Code 0 bedeutet intakt.
- Passwörter, Tokens und Geheimnisse werden nie aufgezeichnet.

## Beispiel: das Log verfolgen und prüfen

Im Wurzelverzeichnis der Installation ausführen:

```bash
./console exp:audit status                       # the channels and their state
./console exp:audit tail --channel=access --lines=20
./console exp:audit verify --channel=content     # exit code 0 intact, 1 broken chain, 2 refused or error
```

Dieselben Befehle laufen als `php bin/php/audit.php <action>`. Weitere Aktionen: `channels`, `show <event id>`,
`checkpoint`; `tail` akzeptiert außerdem `--name=access.*`, `--follow` und `--json`, und `verify` akzeptiert `--date=YYYY-MM-DD`.

## Datenmodell

| Element | Definition |
|---|---|
| Ereignisname | `domain.subject.action[.detail]`, aus einem Taxonomie-Register mit 135 Namen (`expAuditTaxonomy`). Lässt sich auf jeder Ebene ein- oder ausschalten (`content.*`, `content.node.remove.*`) |
| Datensatz | Ein JSON-Objekt: Akteur, Verb, Objekt, Ziel, Ergebnis, Vorher- und Nachher-Werte, IDs des übergeordneten und der untergeordneten Ereignisse, Anfrage-ID, Sitzung, Job- und Lauf-IDs. Kanonisches JSON; trägt den Hash des vorherigen Datensatzes seines Kanals |
| Kanal | `content`, `access`, `system`, `commerce` (enthält auch `data.*`), `read`. Zugeordnet über `[AuditChannelSettings] Route[]`: `content.*` nach `content`, `content.node.view` nach `read`, `data.*` nach `commerce`; Standardkanal `system` |
| Datei | Eine JSON-Lines-Datei je Kanal und UTC-Tag, `var/<site>/log/audit/<channel>-<date>.jsonl`; ein neuer Teil nach Überschreiten von `MaxFileSize`. Der erste Schreibvorgang eines neuen Tages hängt `system.audit.file.close` an die vorherige Datei an |
| Checkpoint | Signierter Kopf der Kette jedes Kanals (tägliche Wartung, `exp:audit checkpoint`) |
| Archiv | Ein komprimierter Tag mit einem per HMAC signierten Manifest (Schlüssel-ID, Verweis auf das vorherige Manifest). Formate: gzip, bzip2, xz, zstd, zip |
| Index | Tabellen `expaudit_event`, `expaudit_cursor`, `expaudit_file` in der Datenbank der Site |

`expaudit_event` enthält eine Zeile je Datensatz, mit den Spalten `id`, `channel`, `seq`, `name`, `severity` (0 bis 7),
`time_ms`, `request_id`, `user`, `object`, `target`, `result`, `parent_id`, `depth`, `job_id`, `run_id`,
`imported`, `pseudonymised`, `record`, `search_text`.

Schlüssel (Installations-ID, Signaturschlüssel, Pseudonymschlüssel) werden bei der ersten Verwendung in `settings/override` erzeugt.

Datenschutz-Standards: Adressen werden gekürzt, Sitzungen gehasht, User-Agents verkürzt und Query-Werte entfernt.
Passwörter, Tokens und Geheimnisse werden nie aufgezeichnet.

## Klassen (`kernel/classes/audit/`)

| Klasse | Rolle |
|---|---|
| `expAudit` | Ein Ereignis aufzeichnen |
| `expAuditBuffer`, `expAuditWriter` | Ein Anhängevorgang je Kanal am Ende der Anfrage, mit einer Sperre je Kanal, sodass sich PHP-FPM, Velocity und Befehle nie vermischen |
| `expAuditHook` | Der eine abgesicherte Weg, auf dem Kernel-Code ein Ereignis aufzeichnet; baut die Daten nur auf, wenn der Name eingeschaltet ist |
| `expAuditBridge` | Zeichnet vorhandene `ezpEvent`-Ereignisse auf, zugeordnet über `[AuditBridgeSettings] Bridge[]` |
| `expAuditVerifier` | Prüft die Hash-Ketten |
| `expAuditKeys`, `expAuditConfig`, `expAuditPrivacy`, `expAuditReader`, `expAuditGuard` | Schlüssel, Einstellungen, Datenschutzregeln, Lesen, Zugriffsprüfungen |

Unterverzeichnisse: `hook/`, `bridge/`, `sinks/`, `alerts/`, `archive/`, `format/`, `index/`, `console/`.

## Einstellungen (`settings/audit.ini`)

| Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|
| `AuditSettings` | `Audit` | `enabled` | Installation; `disabled` löst eine Warnmeldung aus |
| `AuditSettings` | `LogDir` | `log/audit` | Installation |
| `AuditSettings` | `OnWriteFailure` | `continue` | Installation |
| `AuditSettings` | `RefuseExemptViews[]` | `user/login`, `user/logout` | Installation |
| `AuditEventSettings` | `Enabled[]` / `Disabled[]` | an: `access.*`, `system.*`, ausgewählte `content.*`, `commerce.*`, `data.*`; aus: `access.session.regenerate`, `access.session.expire` | Installation |
| `AuditChannelSettings` | `Channels[]`, `Route[]`, `DefaultChannel` | die fünf Kanäle; `system` | Installation |
| `AuditChannel_<name>` | `LiveDays`, `ArchiveDays`, `MaxFileSize`, `ArchiveFormat` | `90`, `730`, `64M`, `gzip` (`read`: `30`, `90`, `256M`, `zstd`) | je Kanal |
| `AuditChannel_<name>` | `Sinks[]` | `access` und `system` verwenden den Sink `syslog` | je Kanal |

Weitere Blöcke derselben Datei: `[AuditRecordSettings]`, `[AuditPrivacySettings]`, `[AuditBufferSettings]`,
`[AuditChainSettings]`, `[AuditKeySettings]`, `[AuditReadSettings]`, `[AuditSinkSettings]`,
`[AuditAlertSettings]`; Sink-Blöcke `[AuditSink_<name>]` (`syslog`, `webhook`, `mail`); Blöcke für Warnregeln
`[AlertRule_<name>]` (zum Beispiel `[AlertRule_brute_force]`); Empfängergruppen `[AlertRecipients_<name>]`.

Einen Wert lesen Sie so:

```bash
./console exp:ini get audit.ini/AuditChannel_read/LiveDays --allow-root-user
```

Erwartete Ausgabe: `30`, sofern kein Override den Wert ändert.

## Erweiterungspunkte

| Punkt | Wie |
|---|---|
| Taxonomie-Zweig | `[AuditEventSettings] Branches[<name>]=<class implementing expAuditTaxonomyBranch>`. Eine Extension registriert ihre eigenen Ereignisnamen; die RAD-Übersicht zählt sie als `auditbranches` |
| Sink | Eine Klasse, die `expAuditSink` implementiert |
| Warnregel | Eine Klasse, die `expAuditAlertRule` implementiert (Schwellenwert, Treffer, Zeitplan) |
| Archivformat | `[AuditArchiveSettings] FormatHandlers[]`. Jeder Handler meldet, was er benötigt; das Register greift auf gzip zurück |

Die RAD-Übersicht (**Setup > RAD**, `setup/rad`) zählt die vier Audit-Register.

## Schnittstellen

| Schnittstelle | Details |
|---|---|
| Konsole | `./console exp:audit <action>`; Exit-Codes 0 intakt, 1 unterbrochene Kette, 2 abgelehnt oder Fehler |
| Views der Administration | `audit/dashboard`, `audit/console`, `audit/event`, Diagramme, Warnmeldungen, Export, Archive, Einstellungen, `audit/recent` |
| Richtlinien | `audit/read` (Einschränkung `Channel`), `audit/manage` |
| Template-Fetch-Funktionen | `events`, `event`, `count`, `chain_status`, `can_read` (leer ohne `audit/read`) |
| Template-Operator | `audit_label` |
| Antwort-Header | Jede Web-Antwort trägt `X-Exp-Request-Id`; jeder Datensatz, den die Anfrage geschrieben hat, speichert dieselbe ID |
| Cronjob-Teil | `cronjobs/audit.php` (Klasse `Exponential\Cronjob\Kernel\Audit`), in einer eigenen Gruppe und in `frequent` |
| Remote-Dienste | Die schreibgeschützte Domäne `expaudit` von [expservices](expservices.md) |

## Die Tabellen auf einer bestehenden Installation anlegen

```bash
php update/common/scripts/6.0/createaudittables.php
```

Das funktioniert auf jeder Engine, einschließlich Oracle und MongoDB. Vorhandene Tabellen bleiben unberührt, und die bis
dahin geschriebenen Dateien werden indiziert.

## Verwandte Seiten

- [Audit](../../../en/us/bc/6.0/audit.md), [das Audit-Protokoll](../../../en/us/features/6.0/audit-trail.md), [Remote-Dienste](../../../en/us/features/6.0/remote-services-expservices.md)
- [Befehle, Cronjob-Teile und Modul-Views als Klassen](ausfuehrbare-befehle-cronjobs-ansichten.md), [Spezifikation von expservices](expservices.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md), [Chronik Oktober 2026](../../../en/us/history/2026/2026-10.md)
