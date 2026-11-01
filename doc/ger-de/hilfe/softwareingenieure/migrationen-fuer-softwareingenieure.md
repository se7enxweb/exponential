---
title: "Migrationen für Softwareingenieure"
audience: software-engineer, developer
category: migrations
order: 1000
summary: Das Migrationsmodell von Exponential 6.0.15 für Ingenieure - geordnete, aufgezeichnete, durch Prüfsummen geschützte, mit Sicherungspunkten abgesicherte und committete Änderungen -, seine Architektur neben den .dba-Schemadateien und den Upgrade-Schritten, die Entwurfsregeln für Migrationen, die auf jeder Installation gleich laufen, und wie man sie testet und prüft.
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, Auslieferung, Softwareingenieur, Architektur, idempotent, Code-Review, Upgrade-Schritt, migration, migrations, schema change, database change, deploy, exp:migration:migrate, software engineer, architecture, expMigrationPhp, exp_migration, detect, remote id, down, CI, code review, upgrade step, db_schema.dba
featured: true
type: help
---
# Migrationen für Softwareingenieure

Diese Seite ist für Ingenieure, die verantworten, wie sich Datenbank, Inhalte und Einstellungen einer Site über die
Zeit ändern: Sie entwerfen die Änderungen, legen die Regeln für Ihr Team fest, binden sie in die CI ein und prüfen
sie. Sie erklärt das Modell hinter den Migrationen von Exponential 6.0.15, ihren Platz in der Architektur und die
Regeln, mit denen eine Migration auf Entwicklung, Staging und Produktion gleich läuft.

## Das Modell in fünf Worten

Eine Migration ist eine Änderung, einmal in einer Datei `YYYYMMDDHHMMSS_name.php` geschrieben und auf jede
Installation einer Site angewendet. Fünf Eigenschaften machen sie sicher:

| Eigenschaft | Was sie garantiert |
|---|---|
| **Geordnet** | Migrationen laufen nach ID (Zeitstempel, dann Name) und nach jeder Migration, die ihr Kopf in `requires:` nennt |
| **Aufgezeichnet** | jede Installation hält in `exp_migration` (eine Zeile je Migration) und `exp_migration_run` (eine Zeile je Lauf) fest, was gelaufen ist; eine erledigte Migration läuft dort nie wieder |
| **Durch Prüfsummen geschützt** | die Aufzeichnung behält eine Prüfsumme der Datei; eine erledigte Migration, deren Datei sich geändert hat, hält den Lauf an, außer `--force --reason=<text>` akzeptiert die Änderung |
| **Mit Sicherungspunkt** | vor einem Lauf wird ein Sicherungspunkt genommen (Datenbank-Dump, `var/`-Speicher, `settings/override`, Commits des Codes) und nach einem Fehler wiederhergestellt |
| **Committet** | Statusdatei und Laufprotokoll werden nach jedem Lauf committet, als `migrations/<umgebung>/<run-id>` getaggt und gepusht |

Das Ergebnis: Jeder Klon des Repositorys sagt, welche Umgebung was angewendet hat, und jede Installation lässt sich in
denselben Zustand bringen, indem man die ausstehenden Migrationen der Reihe nach ausführt.

## Die Architektur

| Schicht | Teile |
|---|---|
| Dateien | `migrations/` der Site, `extension/<name>/migrations/` aktiver Erweiterungen, `migrations/` installierter Pakete (Quellen `site`, `extension:<name>`, `package:<name>`) |
| Kernel-Klassen (`kernel/classes/migration/`) | `expMigrationLocator` findet Dateien, `expMigrationGraph` ordnet sie, `expMigrationPhp` ist die Basisklasse, `expMigrationContext` ist das, woran eine Migration arbeitet, `expMigrationRunner` führt sie aus, `expMigrationRunFlow` umschließt Sicherungspunkte, Wiederherstellung, Commit, Tag und Push |
| Aufzeichnung | die Tabellen `exp_migration` und `exp_migration_run`, angelegt vom Upgrade-Schritt `6.0.15-migration-tables`; `migrations/status/<umgebung>.json` und `migrations/runs/<run-id>.log` in Git |
| Sperre | `var/<site>/upgrade.lock`, geteilt mit `exp:upgrade`, mit einer Halter-Datei, die sagt, wer sie hält |
| Befehle | `exp:migration:status`, `:migrate`, `:dry-run`, `:rollback`, `:resume`, `:caption`, `:push`, `:check`, `:generate` |
| Administration | Setup > Migrationen (Modul `migration`), der Dashboard-Block, das Panel der Website-Seitenleiste |

Auch die Upgrade-Schritte des Kernels sind Migrationen, mit der Quelle `kernel`. `exp:migration:status` listet sie
zuerst, und ein Lauf wendet die ausstehenden der Stufen `database` und `scripts` vor den Migrationen der Site an; die
Stufen `code`, `libraries` und `finish` bleiben bei `exp:upgrade` und werden als `deferred` gelistet. Eine Migration
der Site, die die Tabelle eines Schritts braucht, nennt den Schritt: `requires: 6.0.15-migration-tables`.

## Wo Migrationen hingehören

| Änderung | Werkzeug |
|---|---|
| das Schema, aus dem eine **neue** Installation entsteht | `share/db_schema.dba` des Kernels oder der Erweiterung |
| was **jede** Installation des Kernels oder einer Erweiterung beim Upgrade braucht | ein Upgrade-Schritt, ausgeführt von `exp:upgrade` ([Upgrade-Schritte für Erweiterungen](../../anleitungen/upgrade-schritte-fuer-erweiterungen.md)) |
| was **eine Site** einmal ändert: ihre Tabellen, Klassen, Inhalte, Rollen, Einstellungen | eine Migration |
| eine ganze Installation umziehen oder aktualisieren | keine Migration: [Migrationen oder Umzug einer Site](../migrationen/migrationen-oder-umzug-einer-site.md) |

[Upgrade-Schritt oder Migration?](../migrationen/rezepte/upgrade-schritt-oder-migration.md) führt durch die Wahl,
[Schemaänderungen und Migrationen](schemaaenderungen-und-migrationen.md) behandelt die `.dba`-Seite.

## Entwurfsregeln für verlässliche Migrationen

1. **Eine Änderung je Migration.** Kleine Migrationen scheitern klein, lassen sich leicht prüfen und leicht
   zurücknehmen. Trennen Sie auf MySQL/MariaDB, wo DDL sofort committet wird, eine Schemaänderung und die Daten, die
   sie füllen, in zwei Migrationen.
2. **Finden Sie Dinge über Remote-ID oder Bezeichner, nie über numerische IDs.** Objekt-, Knoten- und Klassen-IDs
   unterscheiden sich zwischen Installationen. Legen Sie Objekte mit einer selbst gewählten `remote_id` an; suchen Sie
   Klassen über ihren Bezeichner und Rollen über ihren Namen.
3. **Lassen Sie `up()` einen halb erledigten Zustand vertragen.** Prüfen Sie vor dem Anlegen
   (`expKernelTableSchema::exists()`, `eZContentObject::fetchByRemoteID()`), damit ein zweiter Lauf nach einem
   Teilfehler die Änderung vervollständigt.
4. **Geben Sie `detect()` an, wenn die Installation es erkennen kann.** Eine Migration, die ihre Änderung sieht (eine
   Tabelle, eine Klasse), gibt `\expMigration::DONE` aus `detect()` zurück; Installationen, die die Änderung auf
   anderem Weg bekommen haben, führen sie nicht aus.
5. **Schreiben Sie `down()`, wenn sich die Änderung selbst rückgängig machen lässt**, und lassen Sie es weg, wenn
   nicht (das Entfernen von Inhalten). Ohne `down()` ist der Sicherungspunkt der Weg zurück.
6. **Halten Sie `plan()` billig und rein.** Es läuft bei jedem Probelauf und jeder Statusseite; es darf nicht
   schreiben.
7. **Keine Geheimnisse, keine personenbezogenen Daten.** Die Ausgabe geht ins Laufprotokoll, in die Aufzeichnung und
   ins Audit-Protokoll; das Protokoll wird committet und gepusht. Lesen Sie Zugangsdaten aus Einstellungen, schreiben
   Sie sie nie in eine Migration.
8. **Nutzen Sie den Kontext.** `$context->db()` ist die Verbindung des Laufs in seiner Transaktion; eine eigene
   Verbindung liegt außerhalb.
9. **Ändern Sie nie eine gelaufene Migration.** Schreiben Sie eine neue. Die Prüfsummen-Sperre setzt das durch.

## Testen und CI

| Prüfung | Befehl | Braucht eine Datenbank |
|---|---|---|
| die Prüfung: Namen, Kopf, Reihenfolge, PHP-Syntax, eine Klasse | `./console exp:migration:check` | nein |
| was laufen würde | `./console exp:migration:dry-run` | ja |
| ein echter Lauf, zurückgerollt (PostgreSQL, SQLite) | `./console exp:migration:dry-run --simulate --diff` | ja |
| nichts ausstehend nach einer Auslieferung | `./console exp:migration:status --fail-on-pending` | ja |
| `up()`, `down()` und wieder `up()` | ein PHPUnit-Test auf einer Wegwerf-SQLite-Datenbank | SQLite |

Jeder Befehl nimmt `--format=json` (`exp-migration/1`) und endet mit 0, 1 (gescheitert oder ausstehend), 2
(Bedienung), 3 (Datenbank nicht erreichbar) oder 4 (intern). [Migrationen testen](../migrationen/rezepte/migrationen-testen.md)
enthält den CI-Job und den PHPUnit-Test.

## Review

Ein Prüfer liest eine Migration auf Reihenfolge (`requires`), Umfang (eine Änderung), IDs (Remote-IDs, Bezeichner),
den Weg zurück (`down()` oder der Sicherungspunkt), Engines (was MySQL/MariaDB sofort committet, was MongoDB nie
zurückrollt) und Ausgabe (keine Geheimnisse).
[Eine Migration im Code-Review prüfen](../migrationen/rezepte/eine-migration-pruefen-im-code-review.md) ist die
Checkliste.

## Ein Lesepfad durch die Rezepte

1. [Eine Datenbanktabelle anlegen](../migrationen/rezepte/eine-datenbanktabelle-anlegen.md) und
   [eine Spalte oder einen Index hinzufügen](../migrationen/rezepte/eine-spalte-oder-einen-index-hinzufuegen.md)
2. [Ein umkehrbares down() schreiben](../migrationen/rezepte/ein-umkehrbares-down-schreiben.md)
3. [Teamarbeit mit Git](../migrationen/rezepte/teamarbeit-mit-git.md)
4. [Migrationen testen](../migrationen/rezepte/migrationen-testen.md) und
   [mit Migrationen ausliefern](../migrationen/rezepte/mit-migrationen-ausliefern.md)
5. [Große Datenmigrationen](../migrationen/rezepte/grosse-datenmigrationen.md) und
   [Hinweise zu den Datenbanken](../migrationen/rezepte/hinweise-zu-den-datenbanken.md)
6. [Sicherungspunkte und Rücknahme in der Praxis](../migrationen/rezepte/sicherungspunkte-und-ruecknahme-in-der-praxis.md)

YAML-Migrationen bekommen eigene Seiten, wenn die YAML-Engine kommt.

## Wie es weitergeht

- [Alle Rezepte](../migrationen/rezepte/README.md)
- [PHP-Migrationen](../../anleitungen/migrationen-php.md): jede Methode und jeder Aufruf des Kontexts
- [Das Migrationsdateiformat](../../spezifikationen/6.0/migrationsdateiformat.md): die verbindlichen Regeln
- Jetzt beginnen: [das Tutorial](../../anleitungen/migrationen-tutorial.md)

## Verwandte Seiten

- [Migrationen: der Einstieg](../migrationen/README.md)
- [Migrationen: die Anleitung](../../anleitungen/migrationen.md)
- [Migrationen: das Tutorial](../../anleitungen/migrationen-tutorial.md)
- [PHP-Migrationen](../../anleitungen/migrationen-php.md)
- [Migrationen in der Administration](../migrationen/in-der-administration.md)
- [Die Migrationsbefehle](../migrationen/befehle.md)
- [Die Migrationseinstellungen](../migrationen/einstellungen.md)
- [Das Migrationsdateiformat](../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../migrationen/wenn-eine-migration-scheitert.md)
- [Fehlerbehebung bei Migrationen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen/migrationen-oder-umzug-einer-site.md)
- [Softwareingenieure: hier beginnen](softwareingenieure-hier-beginnen.md)
- [Schemaänderungen und Migrationen](schemaaenderungen-und-migrationen.md)
- [Persistenz und die Datenbankschicht](persistenz-und-die-datenbankschicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
