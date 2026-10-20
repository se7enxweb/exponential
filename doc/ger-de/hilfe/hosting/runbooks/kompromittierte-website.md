---
title: "Runbook: kompromittierte Website"
audience: hosting, admin
category: hosting
order: 60
summary: Was ein Hoster tut, wenn eine Exponential-Website kompromittiert sein könnte - eindämmen, ohne Beweise zu zerstören, das Prüfprotokoll und seine Hash-Kette, das Sicherheitslog und geänderte Dateien prüfen, Zugangsdaten und Sitzungen erneuern, aus bekannt gutem Code und einem sauberen Backup neu aufbauen, berichten und so benachrichtigen, wie das Gesetz es verlangt.
keywords: Runbook, kompromittiert, gehackt, Einbruch, Reaktion auf Vorfälle, Eindämmung, Beweise, Prüfprotokoll, exp:audit verify, security.log, Sitzungen, Passwort zurücksetzen, Zugangsdaten, Neuaufbau, Git, Wiederherstellung, Offenlegung, Benachrichtigung, DSGVO, runbook, compromised, hacked, breach, incident response, containment, evidence, audit trail, sessions, credentials, rebuild, restore, disclosure, notification, GDPR
type: help
---
# Runbook: kompromittierte Website

Für die Sicherheitsverantwortlichen und Systemadministratoren des Hosters, wenn es Anzeichen gibt, dass ein Angreifer die
Exponential-Website eines Kunden verändert hat: verunstaltete Seiten, unbekannte Administratorkonten, Dateien, die es nicht geben
sollte, von der Website versandter Spam oder ein Alarm aus dem Prüfprotokoll. Das ist ein Vorfall mit Schweregrad 1
([Support-Stufen](../geschaeft/support-stufen-fuer-exponential-hosting.md)).

> **Warnung**
> Ein Einbruch kann rechtliche Pflichten nach sich ziehen: den Kunden zu benachrichtigen und in vielen Rechtsordnungen Behörden und die
> Personen, deren Daten betroffen sind, innerhalb fester Fristen. Beziehen Sie den Kunden und Ihren rechtlichen Berater früh ein. Diese
> Seite ist keine Rechtsberatung.

## Inhalt

1. [Eindämmen](#eindämmen)
2. [Beweise sichern](#beweise-sichern)
3. [Untersuchen](#untersuchen)
4. [Beseitigen und neu aufbauen](#beseitigen-und-neu-aufbauen)
5. [Wiederherstellen](#wiederherstellen)
6. [Berichten und benachrichtigen](#berichten-und-benachrichtigen)
7. [Eine Wiederholung verhindern](#eine-wiederholung-verhindern)

## Eindämmen

1. Nehmen Sie die öffentliche Website mit dem Wartungsmodus vom Netz und lassen Sie nur Ihre eigenen Adressen hinein:

   ```bash
   php bin/php/maintenance.php on --message="Maintenance" --allow-ip=<your address> --allow-root-user
   ```

2. Wenn der Angreifer ein Administratorkonto oder eine Sitzung halten könnte, schränken Sie die Administration jetzt auf Netzwerkebene
   ein ([Die Administration einschränken](../sicherheit/die-administration-einschraenken.md)).
3. Wenn die Maschine selbst kompromittiert sein könnte (unbekannte Prozesse, geänderte Systemdateien), isolieren Sie sie auf
   Netzwerkebene und arbeiten Sie mit einer Kopie.
4. Löschen Sie noch nichts.

## Beweise sichern

- Kopieren Sie die aktuellen Dateien und einen Datenbank-Dump an einen Ort, den der Benutzer der Website nicht schreiben kann.
- Kopieren Sie die Logs: `var/log/` (mit `error.log`, `security.log`, `auth.log`, `audit/`), `var/vc/qbix/log/` von Velocity und die
  Logs von Webserver und PHP-FPM.
- Notieren Sie die Uhrzeit jeder Handlung, die Sie vornehmen.

## Untersuchen

| Frage | Wo Sie nachsehen |
|---|---|
| Wer hat sich angemeldet, Rollen, Einstellungen oder Passwörter geändert? | das Prüfprotokoll: **Audit** in der Administration, `./console exp:audit tail` ([Prüfprotokoll](../sicherheit/pruefprotokoll.md)) |
| Wurde das Prüfprotokoll selbst manipuliert? | `./console exp:audit verify` (oder `php bin/php/audit.php verify --allow-root-user`): Exit-Status 0, wenn die Kette jedes Kanals unversehrt ist, 1, wenn gebrochen; vergleichen Sie mit der von der Maschine weitergeleiteten Kopie, falls eine Senke konfiguriert war |
| Welche Anfragen sahen wie Angriffe aus, und hat eine ein 200 bekommen? | `var/log/security.log` ([Sicherheitslog](../betrieb/sicherheitslog.md)) |
| Welche Dateien haben sich geändert? | vergleichen Sie den Code mit seinem Git-Tag: `git status` und `git diff` in einem Git-Checkout; bei Installationen mit Composer vergleichen Sie mit einem frischen `composer install` derselben Lock-Datei |
| Wurden Dateien dort abgelegt, wo PHP schreiben konnte? | `var/`, `settings/` und `design/` oder `extension/`, wenn sie beschreibbar waren; jede `.php`-Datei unter `var/storage` ist verdächtig |
| Hat die Website Mail versandt? | die Mail-Logs des Servers |
| Welche Version lief? | die Seite Über oder `composer show se7enxweb/exponential`; vergleichen Sie mit den Korrekturen in späteren Release Notes |

Das Prinzip hinter der Prüfung der Dateien, aus [13.11](../../../installation/13-sicherheitshaertung.md#1311-dateiberechtigungen-und-eigentümer):
Wenn der PHP-Prozess nur `var/` und `settings/` ändern kann und der Webserver sich weigert, irgendetwas unterhalb der Wurzel auszuführen,
kann eine Schwachstelle in einer Erweiterung keinen Code einschleusen.

## Beseitigen und neu aufbauen

1. **Code aus einer bekannt guten Quelle**: ein frischer Checkout des Tags der Version und `composer install`, dazu die eigenen
   Erweiterungen aus ihren Repositories. Vertrauen Sie keinen Dateien aus dem kompromittierten Baum.
2. **Daten aus einem sauberen Backup**: das neueste Backup von vor der ersten Änderung des Angreifers, bei der Untersuchung gefunden
   ([Aus einem Backup wiederherstellen](aus-einem-backup-wiederherstellen.md)). Wenn es kein sauberes Backup gibt, prüfen Sie die aktuelle Datenbank
   auf unbekannte Konten, Rollen und Inhalte, bevor Sie sie behalten.
3. **Aktualisieren** Sie Exponential und Erweiterungen vor dem Wiederöffnen auf das neueste 6.0.x, wenn die Lücke eine bekannte sein
   könnte ([Kunden-Websites aktualisieren](../betrieb/kunden-websites-aktualisieren.md)).

## Wiederherstellen

1. **Zugangsdaten**: Ändern Sie die Administratorpasswörter; das Datenbankpasswort (und aktualisieren Sie
   `settings/override/site.ini.append.php`); API-Schlüssel und Tokens, die die Website hält; Passwörter für SFTP und Panel.
2. **Sitzungen**: Eine Passwortänderung meldet jede andere Sitzung dieses Benutzers ab (`[PasswordSettings] EndOtherSessions=enabled`);
   nach einem vermuteten Einbruch entfernen Sie außerdem alle Sitzungen (**Setup** > **Sitzungen**, mit dem Sitzungs-Handler für die
   Datenbank) ([Sicherheitshärtung](../../sicherheitshaertung.md#sitzungsübernahme)).
3. **Konten**: Entfernen Sie Konten und Rollenzuweisungen, die der Angreifer angelegt hat.
4. **Caches**: Leeren Sie alles, damit keine zwischengespeicherte Seite aus dem kompromittierten Zustand ausgeliefert wird:
   `php bin/php/ezcache.php --clear-all --allow-root-user`, und mit Velocity starten Sie es neu und leeren seinen Antwort-Cache.
5. Prüfen Sie, und schalten Sie dann den Wartungsmodus aus.

## Berichten und benachrichtigen

- **An den Kunden**: was geschehen ist, welche Daten betroffen sein könnten, was Sie getan haben.
- **An das Projekt**, wenn eine Schwachstelle in Exponential oder einer seiner Erweiterungen die Ursache war: vertraulich, wie
  `SECURITY.md` es beschreibt, nie in einem öffentlichen Issue
  ([Sicherheitsupdates und Offenlegung](../sicherheit/sicherheitsupdates-und-offenlegung.md)).
- **An Behörden und betroffene Personen**, wo das Gesetz es verlangt: Der Kunde ist meist der Verantwortliche für die
  personenbezogenen Daten und entscheidet mit seinem rechtlichen Berater; Sie unterstützen ihn mit Fakten und Logs
  ([Compliance und DSGVO](../sicherheit/compliance-und-dsgvo.md)).

## Eine Wiederholung verhindern

- Gehen Sie die [Checkliste für die Sicherheitsprüfung](../checklisten/checkliste-fuer-die-sicherheitspruefung.md) durch.
- Machen Sie den Code für den Benutzer der Website schreibgeschützt (13.11).
- Leiten Sie das Prüfprotokoll mit einer Senke von der Maschine weg (`[AuditSink_syslog]`, `[AuditSink_webhook]`), damit eine
  kompromittierte Maschine ihre eigene Geschichte nicht stillschweigend umschreiben kann
  ([Sicherheitshärtung](../../sicherheitshaertung.md#beobachten-logs-der-request-klassifizierer-und-das-audit-protokoll)).
- Erwägen Sie request-shield davor ([request-shield und Request-Regeln](../sicherheit/request-shield-und-request-regeln.md)).

## Verwandte Seiten

- [Härtung für Hoster](../sicherheit/haertung-fuer-hoster.md)
- [Aus einem Backup wiederherstellen](aus-einem-backup-wiederherstellen.md)
- [Kapitel 13: Sicherheitshärtung](../../../installation/13-sicherheitshaertung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
