# Benutzer-Auditprotokoll

Das Benutzer-Auditprotokoll beantwortet für jedes Konto der Installation eine Frage: **Wer hat was daran getan, wann
und von wo** – und wer es versucht hat und abgewiesen wurde. Es ist eine Tabelle in der Datenbank der Website,
`expuser_audit`, mit einer Zeile je Sicherheitsereignis zu einem Benutzerkonto oder einer Benutzergruppe, und eine
Seite in der Administration, **Benutzer > Benutzer-Auditprotokoll** (`user/audit`), die sie filtert und als CSV
exportiert.

Auf dieser Tabelle bauen die Kontoseiten auf: Eine spätere Sitzungsansicht eines Kontos liest dieselbe Tabelle.

## Was aufgezeichnet wird

Jedes Ereignis, das das Audit zu einem Konto aufzeichnet, wird in die Tabelle übernommen, gleich über welche
Oberfläche es ausgelöst wurde:

| Ereignis | Wann |
| --- | --- |
| `access.user.create` | ein Konto wurde angelegt (user/register, der Inhaltseditor, die Mitgliederverwaltung, ein Import) |
| `access.user.change` | Felder des Objekts eines Kontos wurden geändert (user/edit, content/edit eines Benutzerobjekts), mit den Feldern vorher und nachher |
| `access.user.email.change`, `access.user.login.change` | E-Mail-Adresse oder Login wurden geändert |
| `access.user.password.change`, `.password.reset`, `.password.reset.request` | ein Passwort wurde geändert oder zurückgesetzt (nie das Passwort selbst) |
| `access.user.enable`, `.disable`, `.lock`, `.unlock`, `.activate` | der Zustand des Kontos hat sich geändert |
| `access.user.move`, `.remove` | das Konto wurde in eine andere Gruppe verschoben oder entfernt |
| `access.user.approval.*`, `access.registration.*` | eine Registrierung wartete auf Freigabe, wurde freigegeben, abgelehnt oder abgewiesen |
| `access.role.assign`, `access.role.unassign` | eine Rolle wurde dem Konto oder einer Benutzergruppe zugewiesen oder entzogen |
| `access.session.login`, `.login.failed`, `.logout`, `.reauth*`, `.revoke` | Anmeldungen, fehlgeschlagene Anmeldungen, „Bestätigen Sie, dass Sie es sind", beendete Sitzungen |
| `access.passkey.*` | Passkeys hinzugefügt, umbenannt, entfernt, benutzt |
| `access.permission.refused` | eine Ansicht wurde verweigert, während es um ein Konto ging (ein Redakteur wollte einen Administrator bearbeiten) |
| `*.failed`, `*.refused` | jeder abgewiesene oder fehlgeschlagene Versuch der obigen Ereignisse, mit Grund |

Die Mitgliederverwaltung ([mitgliederverwaltung.md](../../bc/6.0/mitgliederverwaltung.md)) ist mit denselben
Ereignissen abgedeckt: ein Redakteur legt ein Mitglied an, ändert, deaktiviert, verschiebt oder entfernt es, und jede
Abweisung, wenn er nach einem Administrator oder einem anderen Redakteur greift.

Jede Zeile enthält:

- **wer**: Benutzer-ID und Login des Handelnden (`system` bei einem Befehl oder Cronjob);
- **wann**: die Zeit auf die Millisekunde;
- **wo**: den Siteaccess, den Host, an den die Anfrage ging, Modul und Ansicht, und die Adresse der Anfrage so, wie die
  Datenschutzregeln des Audits sie lassen (standardmäßig auf ihr /24- oder /48-Netz gekürzt, siehe unten);
- **wen**: das Konto oder die Benutzergruppe, um die es ging, mit ID und Login;
- **was**: den Ereignisnamen, das Ergebnis (erledigt, abgewiesen, fehlgeschlagen) und den Grund;
- **vorher und nachher**: die geänderten Felder, wie das Audit sie aufgezeichnet hat.

Passwörter, Passwort-Hashes, Token und andere Geheimnisse werden **nie** gespeichert: Das Audit zeichnet sie nicht
auf, und jedes Feld, dessen Name danach aussieht, wird vor dem Schreiben einer Zeile noch einmal entfernt.
E-Mail-Adressen werden so gespeichert, wie die Datenschutzregeln des Audits es sagen (`audit.ini
[AuditPrivacySettings] Field[email]`, standardmäßig als Hash).

## Wie es funktioniert

Das Audit ([audit.md](../../bc/6.0/audit.md)) schreibt seine Datensätze in Dateien. Die Senke `useraudit`, für den
Kanal `access` eingetragen, erhält dieselben Datensätze, sobald sie in der Datei stehen, und schreibt die zu einem
Konto in die Tabelle, in derselben Anfrage. Nichts muss doppelt instrumentiert werden: Jeder Weg, den das Audit
abdeckt, steht im Benutzer-Auditprotokoll. Einen Stapel, den die Datenbank abweist, hält der Spool des Audits zurück,
und der Cronjob-Teil des Audits versucht ihn erneut; ein Ereignis wird nie doppelt gespeichert.

## Die Seite

**Benutzer > Benutzer-Auditprotokoll** (`user/audit`) listet die Ereignisse, die neuesten zuerst, und grenzt sie ein
nach:

- **Benutzer**: ein Login oder eine Benutzer-ID. Gefunden wird das Konto, um das es ging, **oder** der Benutzer, der
  gehandelt hat; eine Suche zeigt also, was einem Konto geschah und was dieser Benutzer tat. Ein Klick auf ein Login
  in der Liste sucht danach.
- **Aktion**: ein Ereignisname oder ein Zweig davon (`access.user` findet jedes `access.user.*`-Ereignis).
- **Ergebnis**: erledigt, abgewiesen oder fehlgeschlagen.
- **Von** und **Bis**: Tage, jeweils eingeschlossen.

**Liste als CSV exportieren** lädt die ganze gefilterte Liste herunter (eine Zeile je Ereignis, die Spalten der
Tabelle, vorher und nachher als JSON). Der Export selbst wird aufgezeichnet (`data.export.csv`). Zellen, die eine
Tabellenkalkulation als Formel ausführen würde, werden als Text geschrieben.

Die Seite braucht die Richtlinie **user/audit**. Wer sie hat, sieht die Ereignisse jedes Kontos; geben Sie sie nur
Rollen, die das dürfen: Administratoren haben sie über `*/*`, ein Datenschutzbeauftragter bekommt eine Rolle mit
`user/audit` allein.

## Einstellungen

`settings/audit.ini`:

```ini
[UserAuditSettings]
UserAudit=enabled
Events[]
Events[]=access.user.*
Events[]=access.role.assign
Events[]=access.role.unassign
Events[]=access.session.*
Events[]=access.passkey.*
Events[]=access.registration.*
Events[]=access.permission.refused
RecordIP=enabled
RetentionDays=730
MaxValueLength=2000
```

- `UserAudit=disabled` schreibt keine Zeilen mehr; die Seite zeigt weiter die vorhandenen, und die Audit-Dateien
  behalten jedes Ereignis.
- `Events[]` grenzt ein, was behalten wird. Ein Ereignis, das das Audit selbst nicht aufzeichnet
  (`[AuditEventSettings]`), erreicht auch die Tabelle nicht.
- `RecordIP=disabled` speichert die Adresse nie. Mit `enabled` wird sie so gespeichert, wie `[AuditPrivacySettings]
  Field[actor.ip]` sie lässt: `truncate` (Standard) behält nur das Netz, `hash` einen Hash, `off` nichts.
- `RetentionDays` ist, wie lange eine Zeile bleibt; `0` behält alle. Die Audit-Dateien haben ihre eigene
  Aufbewahrung (`[AuditChannel_access] LiveDays` und `ArchiveDays`).
- `MaxValueLength` kürzt einen langen Wert vorher oder nachher.

## Aufbewahrung

Der Cronjob-Teil `useraudit` entfernt die Zeilen, die älter als `RetentionDays` sind. Er läuft in der Gruppe
**infrequent** (`php runcronjobs.php infrequent`) und für sich allein:

```bash
php runcronjobs.php useraudit
```

## Installation

Eine neue Installation hat die Tabelle: Sie steht in `share/db_schema.dba` (MySQL und MariaDB, PostgreSQL, SQLite und
Oracle über ezoracle) und in den Kernel-Schemas. Eine Installation von vor 6.0.15 erhält sie aus den Update-Dateien
(`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`) oder, auf jeder Datenbank, mit:

```bash
php update/common/scripts/6.0/createuseraudittable.php --dry-run
php update/common/scripts/6.0/createuseraudittable.php
```

Das Skript lässt eine vorhandene Tabelle unverändert und kann daher erneut laufen. Solange die Tabelle fehlt, wird
nichts gespeichert, und die Seite sagt, wie sie angelegt wird. MongoDB hat keine Tabelle für das
Benutzer-Auditprotokoll; dort halten die Audit-Dateien die Ereignisse.

## Beispiel

Auf einer Entwicklungsinstallation unter `https://www.example.com` öffnet ein Administrator
`https://www.example.com/admin/user/audit?user=member1&from=2026-10-01` und sieht, dass ein Redakteur die
E-Mail-Adresse des Mitglieds im Siteaccess editor geändert hat, dass sich das Mitglied zweimal aus demselben Netz
angemeldet hat und dass derselbe Redakteur abgewiesen wurde, als er ein Administratorkonto bearbeiten wollte.
