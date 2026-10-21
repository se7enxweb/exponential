# Das Prüfprotokoll: wer was getan hat, und der Nachweis, dass niemand den Datensatz verändert hat

Diese Seite richtet sich an Administratoren und Sicherheitsbeauftragte, die wissen müssen, wer auf einer Website was
getan hat, und die nachweisen müssen, dass der Datensatz nicht verändert wurde. Exponential 6.0.15 zeichnet
sicherheitsrelevante Ereignisse in einem manipulationssicheren Protokoll auf: Anmeldungen und Ablehnungen,
Inhaltsänderungen, Schreibvorgänge an Einstellungen, geleerte Caches, Deployments, Bestellungen und Änderungen der
Mehrwertsteuer. Das Protokoll ist standardmäßig eingeschaltet, braucht keine Einrichtung und lässt sich in der
Administrationsoberfläche oder auf der Kommandozeile lesen. Hinzugefügt am 02.10.2026.

Dies ist die Kurzfassung. Die vollständige Referenz (Ereigniskatalog, Datensatzformat, jede Einstellung) ist
[der Audit-Leitfaden](../../bc/6.0/audit.md).

## Warum Sie es wollen

- **Beantworten Sie „Wer hat das entfernt?".** Das Entfernen eines Teilbaums ist ein übergeordnetes Ereignis mit einem
  untergeordneten Ereignis pro Knoten darunter; das Leeren des Papierkorbs ist ein untergeordnetes Ereignis von
  `content.trash.empty`; ein Inhalts-Job ist das übergeordnete Ereignis von allem, was seine Stapel getan haben.
- **Bemerken Sie Probleme früh.** Alarmregeln achten auf Brute-Force-Anmeldungen, die Vergabe einer Administratorrolle,
  außerhalb der Arbeitszeit geschriebene Einstellungen, Massenlöschungen, das Abschalten des Audits und eine
  unterbrochene Kette.
- **Weisen Sie nach, dass der Datensatz unversehrt ist.** Jeder Datensatz trägt den Hash des Datensatzes davor (eine
  Hash-Kette). Tagesdateien werden abgeschlossen, archiviert und in signierten Manifesten aufgeführt. `verify` sagt
  Ihnen, ob etwas bearbeitet oder herausgeschnitten wurde.
- **Halten Sie Datenschutzregeln ein.** Adressen werden gekürzt, Sitzungen gehasht, User Agents verkürzt,
  Abfragewerte entfernt. Passwörter, Tokens, Reparaturschlüssel und Geheimnisse werden nie aufgezeichnet. Eine
  fehlgeschlagene Anmeldung für ein unbekanntes Konto wird nur gehasht aufgezeichnet.

## In zwei Minuten ausprobieren

```bash
./console exp:audit status --allow-root-user
./console exp:audit tail --channel=access --name='access.session.*' --lines=3 --allow-root-user
./console exp:audit verify --allow-root-user        # exit 1 when a chain is broken
```

Melden Sie sich dann als Administrator in der Administrationsoberfläche an, öffnen Sie den Tab **Audit**
(`/audit/dashboard`), melden Sie sich ab und wieder an, und öffnen Sie **Konsole**: Ihre Anmeldung ist das neueste
`access.session.login`.

Drei Dinge sind auf einer neuen Installation einmal zu tun:

1. Stellen Sie sicher, dass die Cronjob-Gruppe `frequent` läuft (`php runcronjobs.php frequent` alle paar Minuten);
   sie enthält den Cronjob-Teil `audit`, der indiziert, Sink-Spools ausliefert, Alarmregeln auswertet und einmal am
   Tag rotiert, prüft und archiviert.
2. Prüfen Sie, wer Alarm-E-Mails erhält: `./console exp:audit alerts recipients` (Standard: die `AdminEmail` der
   Website).
3. Sichern Sie `settings/override/audit.ini.append.php`: Die Datei enthält die Signatur- und Pseudonymschlüssel.

## Was aufgezeichnet wird

| Kanal | Beispiele | Ablageort der Dateien |
|---|---|---|
| `content` | Veröffentlichen, Verschieben, Kopieren, Entfernen, Verbergen, Sektionen, Zustände, Papierkorb, Versionen, URL-Aliase, Klassen, Inhalts-Jobs | `var/<site>/log/audit/` |
| `access` | Anmeldung, fehlgeschlagene Anmeldung, abgelehnte Ansicht, Kontosperre, Rollenzuweisung, Zurücksetzen des Passworts, Ablehnung eines Formular-Tokens | ebenso |
| `system` | Schreibvorgänge an Einstellungen, geleerte Caches, Cronjob-Läufe, Befehle, Paketinstallationen, Wartung, Deployments, Reparatureinstellungen | ebenso |
| `commerce` | Bestellungen, Zahlungen (niemals Kartendaten), Mehrwertsteuer, Währungen, Rabatte, CSV-/Paketexporte und -importe | ebenso |
| `read` | stichprobenartig erfasste Knotenansichten, Suchen und Downloads, nur wenn Lesezugriffe eingeschaltet sind | ebenso |

Standardwerte von `settings/audit.ini` (`[AuditSettings]`, `[AuditChannelSettings]`):

| Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|
| `AuditSettings` | `Audit` | `enabled` | Installation |
| `AuditSettings` | `LogDir` | `log/audit` | Installation |
| `AuditSettings` | `OnWriteFailure` | `continue` | Installation |
| `AuditChannel_content`, `_access`, `_system`, `_commerce` | `LiveDays` / `ArchiveDays` / `MaxFileSize` / `ArchiveFormat` | 90 / 730 / 64M / gzip | pro Kanal |
| `AuditChannel_read` | `LiveDays` / `ArchiveDays` / `MaxFileSize` / `ArchiveFormat` | 30 / 90 / 256M / zstd | pro Kanal |

Überschreiben Sie sie in `settings/override/audit.ini.append.php` oder in der `settings/audit.ini.append.php` einer
Extension.

### Wenn das Audit nicht schreiben kann (`OnWriteFailure`)

| Wert | Was geschieht, wenn das Protokoll nicht geschrieben werden kann (Festplatte voll, Berechtigungen) |
|---|---|
| `continue` (Standard) | die Anfrage läuft weiter; der Fehler steht als `AUDIT-UNWRITTEN` in `error.log`, und das Dashboard warnt, sodass eine volle Festplatte die Website nicht lahmlegt |
| `refuse` | Aktionen der Art „immer sofort geschrieben" (`ImmediateEvents[]`) werden abgelehnt, solange ihr Kanal nicht geschrieben werden kann, sodass nichts Sicherheitsrelevantes unaufgezeichnet geschieht: ein POST an eine Ansicht von `AlwaysModules[]` durch einen angemeldeten Benutzer (503 mit einer Fehlerseite), ein Schreibvorgang an Einstellungen (`exp:ini`, die Debug-Leiste) und die Verwaltungsaktionen von `exp:audit` (Exit-Code 2). Seiten, das Bearbeiten von Inhalten, GET-Anfragen, anonyme Besucher, Lesen und Prüfen werden nie abgelehnt. Jede Ablehnung wird als `AUDIT-REFUSED` protokolliert |

Wählen Sie `refuse`, wo eine unaufgezeichnete Änderung schlimmer ist als eine abgelehnte. Prüfen Sie den mitgelieferten
Text mit `grep -n "OnWriteFailure" -B12 settings/audit.ini`.

Die Ansicht `audit/recent` des Tabs listet die neuesten Ereignisse und den Zustand der Kette jedes Kanals auf;
Kernel-Code zeichnet Ereignisse über einen einzigen abgesicherten Hook auf, und ein
vorhandenes `ezpEvent` kann allein über Einstellungen auditiert werden: `audit.ini [AuditBridgeSettings] Bridge[<ezpEvent name>]=<audit name>`
(eine Extension registriert ihren eigenen Zweig in `[AuditEventSettings] Branches[]`).

## Wer hineinsehen darf

Zwei Richtlinien im Modul `audit`: `audit/read` (der Tab, Dashboard, Konsole, Ereignis, Diagramme, Alarme, Export;
optional eingeschränkt durch `Channel`) und `audit/manage` (Archive, Einstellungen, „Jetzt prüfen"). In einer neuen
Installation besitzt nur Administrator sie. Ein Benutzer ohne `audit/read` sieht keinen Tab, keinen Link und keinen
Block, und eine eingetippte URL wird abgelehnt und ihrerseits als `access.permission.refused` aufgezeichnet.

## Von der Kommandozeile

`exp:audit` hat die Aktionen `status`, `channels`, `tail`, `show <event id>`, `verify`, `checkpoint`, `search`,
`rotate`, `archive`, `restore`, `purge`, `reindex`, `pseudonymise`, `export`, `import`, `key`, `sinks`, `alerts`
und `cron`. Die Volltextsuche nimmt `--query=<text>` (ein einfaches `-q` ist bei jedem Skript die Option für den
stillen Modus). Namensmuster sind `*`, ein auf `.*` endendes Präfix oder ein vollständiger Name; alles andere wird mit
Exit-Code 2 abgelehnt. `--from` und `--to` akzeptieren die Versätze `Z` oder `+HH:MM`; ohne einen solchen gelten sie
auf der Kommandozeile als UTC und in der Konsole als Zeit der Website.

## Sinks, Alarme und Archive

- **Sinks:** syslog (RFC 5424; mit `Transport=local` der native Socket von journald, sodass
  `journalctl -t exponential` die Datensätze findet), ein signierter Webhook (HMAC-SHA-256 über Zeitstempel und Body,
  mit Wiederholungen zwischengespeichert) und E-Mail bei kritischen Ereignissen.
- **Empfänger von Alarmen:** die `Recipients[]` einer Regel, sonst `[AuditAlertSettings] Recipients[]`, sonst
  `[AuditSink_mail] Receivers[]`, sonst die `AdminEmail` der Website; benannte Gruppen und Benutzer per ID oder Login
  funktionieren ebenfalls.
- **Archive:** Tage, die älter als `LiveDays` sind, werden komprimiert (gzip, bzip2, xz, zstd oder zip), wieder
  eingelesen und verglichen, bevor die Live-Datei entfernt wird, und in einem mit einem HMAC signierten Manifest
  aufgeführt. Wiederherstellen und der Import der Textprotokolle von 4.x werden unterstützt.
- **Index:** Drei Tabellen (`expaudit_event`, `expaudit_cursor`, `expaudit_file`) enthalten auf jeder Engine eine
  durchsuchbare Kopie. Auf einer bestehenden Installation legen Sie sie an mit
  `php update/common/scripts/6.0/createaudittables.php`.

## Exporte und die Kanäle von Extensions

- **Export** (`audit/export`): der Filter der Konsole als CSV, JSON Lines oder JSON. **Jetzt herunterladen** wird
  gestreamt, sodass ein großer Filter unter Apache wie unter Velocity sofort beginnt; **Im Hintergrund exportieren**
  führt den Inhalts-Job `auditexport` aus und bietet die Datei nach Abschluss auf der Exportseite an, und zwar nur dem
  Benutzer, der sie angefordert hat.
- **Quellen von Extensions**: Eine Extension, die ihre eigenen Ereignisse in einem eigenen Kanal aufzeichnet,
  registriert eine Quelle (`audit.ini [AuditSourceSettings] Sources[]`) und erhält für diesen Kanal ein
  Dashboard-Panel, Links, Diagramme, Ereignisdetails und CSV-Spalten; das linke Menü lässt einen Link in einen Kanal
  weg, den der Benutzer nicht lesen darf. Exponential Intelligence zeichnet auf diese Weise im Kanal `ai` auf
  ([expai](erweiterungen/expai.md)).
- **Wiederholte Datensätze**: `exp:audit verify` nennt Zeilen, die eine Datensatz-ID wiederholen (`repeated_ids`, vor
  dem 12.10.2026 von geforkten Prozessen geschrieben), als Hinweis; der Index und die Exporte zeigen jeden Datensatz nur
  einmal.

Einzelheiten: [der Audit-Leitfaden](../../bc/6.0/audit.md).

## Grenzen und Wissenswertes

- Die Aufrufe von `eZAudit::writeAudit()` aus 4.x funktionieren weiter; zehn Namen aus 4.x werden auf die neuen
  Ereignisnamen abgebildet.
- Knotenansichten, Suchen und Downloads werden nur aufgezeichnet, wenn Lesezugriffe eingeschaltet sind.
- `ReauthForManage` (`disabled` in `settings/audit.ini`, Block `[AuditConsoleSettings]`) lässt „Jetzt prüfen" erneut nach dem Passwort fragen.
- Das Dashboard hält seine 7-Tage-Werte eine Minute lang vor, sodass es in etwa 25 ms statt in 170 ms öffnet.

## Verwandte Seiten

- [Der Audit-Leitfaden](../../bc/6.0/audit.md), [Spezifikation des Audits](../../spezifikationen/6.0/audit-ereignismodell.md)
- [Entfernte Audit-Dienste](entfernte-dienste-expservices.md), [Inhalts-Jobs](inhaltsauftraege.md), [die Exp-Debug-Leiste](exp-debug-leiste.md), [der INI-Befehl](exp-ini-befehl.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../bc/6.0/verhaltensaenderungen-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik Oktober 2026](../../geschichte/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
