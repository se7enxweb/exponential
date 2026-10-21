---
title: Datensicherung aus der Administration mit git_manager
audience: government, admin, hosting
category: public-sector
order: 70
summary: Für Website-Administratorinnen und -Administratoren in öffentlichen Stellen, die keine Shell auf dem Server haben. Wie die Seite Setup > Backup der Extension git_manager Datenbank- und Dateisicherungen erstellt, sie verschlüsselt und bereinigte Dumps erzeugt; ihre Grenzen (nur MySQL und MariaDB, auf demselben Server gespeichert) und wie sie in eine offizielle Sicherungsrichtlinie passt.
keywords: git_manager, Sicherungsverwaltung, Setup > Backup, captions, verschlüsselte Datensicherung, GnuPG, AES-256, bereinigter Dump, AGPL-Dump, backup-create.php, mysqldump, MaxBackups, keine Shell, backup manager, encrypted backup, sanitized dump, AGPL dump, no shell
type: help
---
# Datensicherung aus der Administration mit git_manager

Diese Seite richtet sich an Website-Administratorinnen und -Administratoren in öffentlichen Stellen, die vor einer
Änderung eine Datensicherung brauchen, aber keine Shell auf dem Server haben, zum Beispiel weil eine zentrale
IT-Abteilung die Maschinen betreibt. Sie beantwortet, was die Sicherungsseite der Extension `git_manager` tut, wie Sie
sie verwenden und warum sie das Sicherungssystem der Organisation ergänzt, aber nicht ersetzt. Die Funktionsseite ist
[git_manager](../../../../../eng-us/features/6.0/extensions/git_manager.md).

## Inhalt

1. [Was es ist](#was-es-ist)
2. [Eine Datensicherung erstellen](#eine-datensicherung-erstellen)
3. [Verschlüsselung und bereinigte Dumps](#verschlüsselung-und-bereinigte-dumps)
4. [Grenzen](#grenzen)
5. [Einbindung in eine Sicherungsrichtlinie](#einbindung-in-eine-sicherungsrichtlinie)
6. [Checkliste](#checkliste)

## Was es ist

`git_manager` ist eine Extension (von der `composer.json` der Installation vorausgesetzt), die der Administration
zwei Werkzeuge hinzufügt: ein Git-Dashboard und eine **Sicherungsverwaltung** unter **Setup > Backup**
(`/git_manager/backup`). Datensicherungen sind mit Zeitstempel versehene Sätze von Archiven der Datenbank und der
Dateien. Sie braucht PHP 8.1 oder neuer, GnuPG für verschlüsselte Datensicherungen und `mysqldump` für
Datenbanksicherungen.

| Typ | Erstellt | Verwendung |
|---|---|---|
| Vollständige Sicherung der Website | SQL-Dump, `var/`-Archiv und Website-Dateien (`extension/`, `settings/`, `config.php`) | die Website auf einem neuen Server wieder aufbauen |
| Datenbank und Dateien | SQL-Dump und `var/`-Archiv | die übliche Datensicherung vor einer Änderung |
| Nur Datenbank | SQL-Dump | vor Datenbankänderungen |
| Nur Dateien | `var/`-Archiv | vor umfangreichen Dateiänderungen |

## Eine Datensicherung erstellen

1. Öffnen Sie **Setup > Backup**.
2. Wählen Sie den Typ und fügen Sie eine Beschreibung hinzu, etwa „Vor der Aktualisierung, Änderungsantrag 123“.
3. Setzen Sie optional das Häkchen bei **Encrypt the archives (GPG, AES-256)** (Archive verschlüsseln) und geben Sie
   eine Passphrase ein.
4. Erstellen Sie die Sicherung. Sie erscheint unter **Existing backups** (vorhandene Datensicherungen) mit ihren
   Archiven zum Herunterladen.

Die Seite zeigt eine Warnung, wenn die neueste Datensicherung alt ist (orange nach 7 Tagen), und bei jeder
Datensicherung eine Altersanzeige. Dasselbe auf der Kommandozeile:

```bash
php extension/git_manager/bin/php/backup-create.php full -d "Before upgrade"
php extension/git_manager/bin/php/backup-list.php -v -s
```

Für den Zugriff ist die Richtlinienfunktion `backup` der Extension nötig; geben Sie sie nur den Personen, die
Datensicherungen erstellen.

## Verschlüsselung und bereinigte Dumps

- **Verschlüsselung** verwendet GnuPG mit AES-256. „Für eine verlorene Passphrase gibt es keine Wiederherstellung.“
  Bewahren Sie die Passphrase in Ihrem Geheimnisspeicher auf, nicht bei der Datensicherung. Auf der Kommandozeile
  übergeben Sie sie mit `--passphrase-file` oder der Umgebungsvariablen `GIT_MANAGER_BACKUP_PASSPHRASE`, nicht mit
  `-p`, was sie in die Prozessliste schreibt.
- **Bereinigte Dumps**: Die Option auf der Seite (als AGPL-kompatibler Dump bezeichnet) schreibt einen zusätzlichen
  Dump, aus dem Passwort-Hashes, E-Mail-Adressen, Schlüssel, Zugangsdaten, Pfade und IP-Adressen entfernt und Tabellen
  mit Geheimnissen oder personenbezogenen Daten weggelassen sind. Nützlich, wenn eine Entwicklerin, ein Entwickler
  oder ein Lieferant eine realistische Datenbank ohne personenbezogene Daten braucht; prüfen Sie ihn vor der Weitergabe
  wie jede Datenfreigabe.
- Dateien werden nur für den Eigentümer lesbar angelegt, der Sicherungsordner verweigert Webzugriffe, und Downloads
  laufen über einen authentifizierten Controller.

## Grenzen

- **Nur MySQL und MariaDB** für den Datenbankteil, weil er `mysqldump` verwendet. Websites unter SQLite, PostgreSQL,
  MongoDB oder Oracle brauchen das eigene Werkzeug der Engine
  ([Datensicherung je Datenbank-Engine](datensicherungen-je-datenbank-engine.md)).
- **Auf demselben Server gespeichert**: Datensicherungen gehen nach `var/site/backups/captions` (`BackupPath`). Eine
  Datensicherung auf derselben Festplatte übersteht den Verlust dieses Servers nicht. Laden Sie die Archive auf
  freigegebenen Speicher herunter oder lassen Sie Ihr Sicherungssystem das Verzeichnis kopieren.
- **Anzahl der aufbewahrten Sicherungen**: `MaxBackups` (36 ausgeliefert) entfernt die ältesten darüber hinaus.
- **Kein eigener Zeitplan** ist in ihrer Dokumentation beschrieben; planmäßige Datensicherungen gehören in Ihr
  Sicherungssystem.

## Einbindung in eine Sicherungsrichtlinie

Verwenden Sie `git_manager` für das, was es gut kann: einen schnellen, dokumentierten Wiederherstellungspunkt direkt
vor einer Änderung, erstellt von der Person, die die Änderung vornimmt. Verweisen Sie im Änderungsantrag auf den
Zeitstempel der Datensicherung. Behalten Sie die planmäßigen Datensicherungen außer Haus der Organisation als Rückgrat
bei ([Sicherungspläne und Aufbewahrung](sicherungsplaene-und-aufbewahrung.md)).

## Checkliste

- [ ] Die Datenbank der Website ist MySQL oder MariaDB, oder ein anderes Verfahren deckt die Datenbank ab.
- [ ] Nur Personen, die Datensicherungen erstellen, haben die Richtlinienfunktion `backup`.
- [ ] Bei verschlüsselten Datensicherungen liegt die Passphrase im Geheimnisspeicher.
- [ ] Archive werden vom Server weg kopiert.
- [ ] Bereinigte Dumps werden vor der Weitergabe geprüft.

## Verwandte Seiten

- [Datensicherung und Wiederherstellung](README.md): Übersicht dieses Ordners
- [Datensicherung: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
