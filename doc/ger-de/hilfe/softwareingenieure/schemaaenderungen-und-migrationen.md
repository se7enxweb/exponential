---
title: Schemaänderungen und Migrationen
audience: software-engineer, developer
category: migrations
order: 210
summary: Wie eine Änderung des Datenbankschemas jede Installation von Exponential erreicht - die .dba-Schemadateien für neue Installationen, Upgrade-Schritte, die exp:upgrade auf jede Installation des Kernels oder einer Erweiterung anwendet, und Migrationen für die eigenen Änderungen einer Site - was Sie wählen, wie jedes angewendet, getestet, geprüft und ausgeliefert wird, und eine Checkliste, mit Links auf jede Anleitung, jedes Rezept und jede Referenzseite.
keywords: Schema, Schemaänderung, Datenbankänderung, Migration, Migrationen, dba, db_schema.dba, Update-SQL, dbupdate, ezsqldumpschema, ezsqlinsertschema, ezsqldiff, Upgrade-Prüfung, Datenbankkonsistenz, Schema-Handler, Upgrade-Schritt, Tabelle anlegen, Spalte hinzufügen, Index hinzufügen, Auslieferung, Softwareingenieur, schema change, database change, migrations, update SQL, upgrade check, database consistency, schema handler, exp:upgrade, exp:migration, exp:migration:migrate, upgrade step, expUpgradeSchemaStep, expMigrationPhp, add table, add column, add index, deploy, CI, software engineer
type: help
---
# Schemaänderungen und Migrationen

Diese Seite richtet sich an Ingenieure, die im Kernel, in einer Erweiterung oder in einer einzelnen Site eine
Tabelle, eine Spalte oder einen Index hinzufügen oder ändern und diese Änderung auf jede Installation auf dieselbe
Weise bringen müssen. Sie ist der Überblick: Sie sagt, welches der drei Werkzeuge Sie nehmen, wie jedes angewendet
wird, und verlinkt die Anleitung, das Rezept und die Referenz zu jedem.

## Inhalt

1. [Drei Wege, auf denen ein Schema eine Installation erreicht](#drei-wege-auf-denen-ein-schema-eine-installation-erreicht)
2. [Was Sie nehmen](#was-sie-nehmen)
3. [Die beteiligten Dateien](#die-beteiligten-dateien)
4. [Wie eine Installation eine Änderung anwendet](#wie-eine-installation-eine-änderung-anwendet)
5. [Migrationen für die eigenen Änderungen einer Site](#migrationen-für-die-eigenen-änderungen-einer-site)
6. [Testen, Prüfen und Ausliefern](#testen-prüfen-und-ausliefern)
7. [Die Werkzeuge](#die-werkzeuge)
8. [Checkliste für eine Schemaänderung](#checkliste-für-eine-schemaänderung)
9. [Bekannte Lücken](#bekannte-lücken)
10. [Verwandte Seiten](#verwandte-seiten)

## Drei Wege, auf denen ein Schema eine Installation erreicht

| Werkzeug | Für | Angewendet von | Aufgezeichnet in | Mehr dazu |
|---|---|---|---|---|
| **Das `.dba`-Schema** (`share/db_schema.dba`, das `share/` einer Erweiterung) | eine **neue** Installation: die Tabellen, wie sie heute sind | der Installer, `ezsqlinsertschema.php` | nirgends: es ist der Ausgangspunkt | [Datenbankschema und dba-Dateien](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datenbankschema-und-dba-dateien.md) |
| **Ein Upgrade-Schritt** (`expUpgradeStep`, gelistet in `upgrade.ini`) | jede **bestehende** Installation des Kernels oder einer Erweiterung, beim Upgrade | `./console exp:upgrade --apply` | `exp_migration` (Quelle `kernel`) | [Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md), [Upgrade-Schritte für Erweiterungen](../../anleitungen/upgrade-schritte-fuer-erweiterungen.md) |
| **Eine Migration** (`YYYYMMDDHHMMSS_name.php`, `expMigrationPhp`) | **eine Site**: ihre eigenen Tabellen, Klassen, Inhalte, Rollen, Einstellungen, nacheinander auf Entwicklung, Staging und Produktion | `./console exp:migration:migrate --apply` oder Setup > Migrationen | `exp_migration` und `exp_migration_run` sowie eine Statusdatei in Git | [Migrationen](../migrationen/README.md), [die Anleitung](../../anleitungen/migrationen.md) |

Eine Änderung im Kernel oder in einer Erweiterung braucht meist die ersten beiden: die `.dba`-Änderung, damit neue
Installationen sie haben, und einen Upgrade-Schritt, damit bestehende sie bekommen. Die Änderung einer Site braucht
nur eine Migration. Die Upgrade-Schritte stehen auch unter den Migrationen, als Migrationen der Quelle `kernel`, und
ein Migrationslauf führt die ausstehenden der Stufen `database` und `scripts` zuerst aus; eine Migration der Site
nennt einen Schritt, den sie braucht, in `requires:`
([Die Schritte unter den Migrationen](../../anleitungen/aktualisieren-mit-der-konsole.md#die-schritte-unter-den-migrationen)).

## Was Sie nehmen

| Die Änderung | Nehmen Sie |
|---|---|
| eine neue Tabelle oder Spalte des Kernels, in einem Release | `.dba` + einen Upgrade-Schritt in `kernel/classes/upgrade/steps/` |
| eine Tabelle, Spalte, Standardzeile oder Richtlinie einer Erweiterung, die jede ihrer Installationen braucht | `.dba` der Erweiterung + einen Upgrade-Schritt, eingetragen in `ExtensionStepList[]` ([Upgrade-Schritte für Erweiterungen](../../anleitungen/upgrade-schritte-fuer-erweiterungen.md)) |
| eine Tabelle oder Spalte, die nur eine Site hat | eine Migration in `migrations/` der Site ([Eine Datenbanktabelle anlegen](../migrationen/rezepte/eine-datenbanktabelle-anlegen.md), [Eine Spalte oder einen Index hinzufügen](../migrationen/rezepte/eine-spalte-oder-einen-index-hinzufuegen.md)) |
| eine Inhaltsklasse, Inhalte, eine Rolle oder eine Einstellung einer Site | eine Migration ([die Rezepte](../migrationen/rezepte/README.md)) |
| eine Änderung, auf der die Nutzer einer Erweiterung aufbauen, der Reihe nach mit ihren eigenen Migrationen angewendet | eine Migration in `extension/<name>/migrations/` ([Migrationen in einer Erweiterung oder einem Paket](../migrationen/rezepte/migrationen-in-einer-erweiterung-oder-einem-paket.md)) |
| eine Site auf eine andere Engine oder einen anderen Host umziehen | keines davon: [Migrationen oder Umzug einer Site](../migrationen/migrationen-oder-umzug-einer-site.md) |

Der ausführliche Vergleich: [Upgrade-Schritt oder Migration?](../migrationen/rezepte/upgrade-schritt-oder-migration.md).

## Die beteiligten Dateien

| Datei | Rolle |
|---|---|
| `share/db_schema.dba`, `share/db_data.dba` | Schema und Ausgangsdaten des Kernels als PHP-Arrays; Erweiterungen liefern ihre eigenen in ihrem `share/` |
| `settings/dbschema.ini [SchemaSettings]` | ordnet eine Engine ihrem Schema-Handler zu (`eZMysqlSchema`, `eZPgsqlSchema`, `eZSQLiteSchema`, `expMongoSchema`; `eZOracleSchema` mit `ezoracle`) |
| `kernel/sql/<engine>/` | das je Engine erzeugte SQL |
| `update/database/<driver>/6.0/` | das Upgrade-SQL je Treiber, zum Beispiel `dbupdate-6.0.0-6.0.15.sql` (Dateien für MySQL, PostgreSQL und SQLite existieren), für Installationen, die von Hand aktualisiert werden |
| `update/common/scripts/6.0/` | PHP-Skripte, die ein Upgrade ausführt, etwa `createaudittables.php` |
| `kernel/classes/upgrade/steps/`, `settings/upgrade.ini` | die Upgrade-Schritte des Kernels (`expUpgradeStep`) und ihre Liste; eine Erweiterung fügt ihre eigenen mit `ExtensionStepList[]` hinzu |
| `migrations/`, `extension/<name>/migrations/` | die Migrationen einer Site und einer Erweiterung, `YYYYMMDDHHMMSS_name.php` |
| `migrations/status/<environment>.json`, `migrations/runs/<run-id>.log` | die Aufzeichnung der Migrationsläufe, in Git committet ([Das Migrations-Repository](../../anleitungen/migrationen-repositories.md)) |
| `settings/migration.ini` | wo Migrationen gefunden werden, wie Läufe sich verhalten, Sicherungspunkte, das Repository ([Einstellungen](../migrationen/einstellungen.md)) |

Die Update-Dateien für Oracle liegen in `ezoracle`; MongoDB hat keine, weil es kein SQL-Schema hat.

## Wie eine Installation eine Änderung anwendet

Seit 6.0.15 erledigen zwei Befehle die Arbeit, beide unter derselben Sperre der Installation und mit denselben
Sicherungspunkten:

```bash
./console exp:upgrade --apply              # the kernel's and the extensions' upgrade steps, all stages, then deploy
./console exp:migration:migrate --apply    # pending database and scripts steps, then the site's migrations
```

`exp:upgrade` nimmt einen Sicherungspunkt, wendet jeden ausstehenden Upgrade-Schritt auf jeder Engine an, hält jeden
fest und stellt bereit ([Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md)).
`exp:migration:migrate` nimmt einen Sicherungspunkt, führt die ausstehenden Migrationen der Reihe nach aus, jede in
einer Transaktion, wo die Engine eine hat, stellt nach einem Fehler den Sicherungspunkt wieder her und committet,
taggt und pusht die Aufzeichnung des Laufs ([Migrationen](../../anleitungen/migrationen.md)).

Von Hand, wie vor 6.0.15 ([Kompatibilität, Releases und Bereitstellung](../../standards/architektur/kompatibilitaet-releases-deployment.md#eine-installation-aktualisieren)):

1. Sichern Sie Dateien und Datenbank.
2. Bringen Sie den neuen Code an seinen Platz, erzeugen Sie die Klassenzuordnungen neu und leeren Sie alle Caches; laden
   Sie PHP-FPM neu und starten Sie Velocity neu.
3. Wenden Sie die Datenbankaktualisierung **einmal, vollständig, der Reihe nach** an: die Datei in
   `update/database/<driver>/6.0/`, dann die Skripte in `update/common/scripts/6.0/`, die die Upgrade-Anleitung nennt.
4. Führen Sie die Upgrade-Prüfung aus: Setup > System Upgrade, **Überprüfe Datenbankkonsistenz** vergleicht die Tabellen
   mit den Schemadateien ([Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md)).

Die Update-Datei muss sagen, wie auf einer Datenbank vorzugehen ist, die bereits einen Teil davon hat; bei der Datei
für 6.0.15 schlägt eine Anweisung für eine vorhandene Spalte mit deren Namen fehl, und nur diese Anweisung wird
übersprungen. Ein Team, das von solchen Dateien zu Migrationen wechselt:
[Umstieg von SQL-Update-Dateien](../migrationen/rezepte/umstieg-von-sql-update-dateien.md).

## Migrationen für die eigenen Änderungen einer Site

Eine Migration ist eine PHP-Klasse mit `plan()` (was sie ändern würde, für den Probelauf), `up()` (die Änderung) und,
wo es geht, `down()` (der Weg zurück) und `detect()` (der Zustand, bei der Installation erfragt). Tabellen werden auf
jeder Engine aus einer Definition im `.dba`-Format mit `expKernelTableSchema::install()` angelegt; Spalten und Indizes
über den Schema-Handler der Engine, wie es `expUpgradeSchemaStep` des Kernels tut. Beginnen Sie mit
[dem Tutorial](../../anleitungen/migrationen-tutorial.md), dann [PHP-Migrationen](../../anleitungen/migrationen-php.md).

| Aufgabe | Rezept |
|---|---|
| eine Tabelle der Site | [Eine Datenbanktabelle anlegen](../migrationen/rezepte/eine-datenbanktabelle-anlegen.md) |
| eine Spalte oder ein Index | [Eine Spalte oder einen Index hinzufügen](../migrationen/rezepte/eine-spalte-oder-einen-index-hinzufuegen.md) |
| was je Engine anders ist | [Hinweise zu den Datenbanken](../migrationen/rezepte/hinweise-zu-den-datenbanken.md) |
| Daten in der neuen Spalte | [Daten korrigieren](../migrationen/rezepte/daten-korrigieren.md), [Große Datenmigrationen](../migrationen/rezepte/grosse-datenmigrationen.md) |
| ein Weg zurück | [Ein umkehrbares down() schreiben](../migrationen/rezepte/ein-umkehrbares-down-schreiben.md) |
| nur in manchen Umgebungen | [Migrationen je Umgebung](../migrationen/rezepte/migrationen-je-umgebung.md) |

Die Entwurfsregeln: eine Änderung je Migration; Dinge über Namen oder Remote-ID finden, nie über eine numerische ID;
nur anlegen, was fehlt, damit eine Migration auf jeder Installation sicher ist, die sie nicht aufgezeichnet hat; nie
eine Migration bearbeiten, die irgendwo gelaufen ist (die Prüfsummen-Sperre lehnt sie ab); Geheimnisse heraushalten
(das Laufprotokoll wird committet). Mehr dazu:
[Migrationen für Softwareingenieure](migrationen-fuer-softwareingenieure.md).

## Testen, Prüfen und Ausliefern

| Stufe | Was | Seite |
|---|---|---|
| CI bei jedem Pull Request | `./console exp:migration:check` (ohne Datenbank), ein PHPUnit-Test auf einer Wegwerf-SQLite-Datenbank | [Migrationen testen](../migrationen/rezepte/migrationen-testen.md) |
| vor dem Merge | `./console exp:migration:dry-run --simulate --diff` auf einer PostgreSQL- oder SQLite-Kopie; die Checkliste für das Review | [Eine Migration im Code-Review prüfen](../migrationen/rezepte/eine-migration-pruefen-im-code-review.md) |
| im Team | Zeitstempel, `requires:`, Merge-Konflikte | [Teamarbeit mit Git](../migrationen/rezepte/teamarbeit-mit-git.md) |
| bei der Auslieferung | `./console exp:migration:migrate --apply`, dann `./console exp:velocity deploy` | [Mit Migrationen ausliefern](../migrationen/rezepte/mit-migrationen-ausliefern.md) |
| nach der Auslieferung | der Status, die Aufzeichnung in Git, die Audit-Ereignisse | [Die Audit-Spur der Läufe](../migrationen/rezepte/die-audit-spur-der-laeufe.md) |
| wenn es scheitert | die automatische Wiederherstellung, die Rücknahme | [Wenn eine Migration scheitert](../migrationen/wenn-eine-migration-scheitert.md) |

## Die Werkzeuge

```bash
./console exp:upgrade:status               # every upgrade step and its state
./console exp:migration:status             # every migration (and step) and its state
./console exp:migration:check              # the lint of migration files, for CI
./console exp:migration:dry-run --simulate --diff   # run them in a transaction that is rolled back (PostgreSQL, SQLite)
php bin/php/ezsqldumpschema.php --help     # dump a database schema to a .dba file
php bin/php/ezsqlinsertschema.php --help   # load a .dba schema into an engine (--type)
php bin/php/ezsqldiff.php --help           # compare two schemas
```

Jeder Migrationsbefehl: [Befehle](../migrationen/befehle.md). Eine `.dba`-Änderung anhand ihres Diffs zu prüfen ist
irreführend: Zeilen liegen in einem numerisch geschlüsselten Array, das Entfernen einer Zeile nummeriert also jede
Zeile danach neu. Vergleichen Sie stattdessen das Schema, das die Dateien ergeben, zum Beispiel mit `ezsqldiff.php`
oder einem Test, der die Datei in eine Wegwerf-SQLite-Datenbank lädt
([Schema und dba-Dateien](../ersteller/anwendungsentwickler/schema-und-dba-dateien.md)).

## Checkliste für eine Schemaänderung

- [ ] Die Änderung steht in `share/db_schema.dba` (und `db_data.dba` für Ausgangszeilen) des Kernels oder der
      Erweiterung, damit eine neue Installation sie hat; eine Änderung nur einer Site braucht keine `.dba`.
- [ ] Bestehende Installationen bekommen sie: ein Upgrade-Schritt, der sie auf jeder Engine anwendet und erkennt
      (`detect()`), sodass `exp:upgrade` sie festhält und ein zweiter Lauf nichts ändert; oder, für eine Site, eine
      Migration.
- [ ] Eine Migration nutzt `$context->db()` und die Schema-Handler, läuft auf jeder Engine, auf der die Site laufen
      kann (oder nennt sie in `engines:`), und legt nur an, was fehlt.
- [ ] Für jeden Treiber mit SQL existiert eine Update-Datei, wenn Installationen von Hand aktualisiert werden:
      MySQL/MariaDB, PostgreSQL, SQLite und Oracle in `ezoracle`.
- [ ] MongoDB ist abgedeckt: Der Adapter oder der Installer legt an, was der Code braucht.
- [ ] Die Aktualisierung ist auf einer Datenbank sicher, die bereits einen Teil davon hat.
- [ ] Eine Persistenzklasse (`eZPersistentObject`) beschreibt eine neue Tabelle.
- [ ] Neue Tabellen verwenden das Präfix `exp`; Spalten, die `ez`-Tabellen hinzugefügt werden, behalten deren Stil
      ([Regeln zu Schichtung und Benennung](../../standards/architektur/schichten-und-benennung.md)).
- [ ] `./console exp:migration:check` besteht, und die Migration wurde auf einer Kopie simuliert oder ausgeführt.
- [ ] Das Changelog führt sie unter **Added** oder **Updated**, und die Upgrade-Hinweise in `doc/eng-us/bc/6.0/` sagen,
      was auszuführen ist ([Changelogs und Upgrade-Hinweise](changelogs-und-bc-hinweise.md)).
- [ ] Die CI-Datenbankjobs (MySQL 8.0 und PostgreSQL 15) bestehen.

## Bekannte Lücken

- Eine Installation, die von Hand mit den SQL-Updatedateien aktualisiert wurde, hat keine Aufzeichnung, welche
  Anweisungen gelaufen sind; `exp:upgrade` hält jeden Schritt fest, den es anwendet, und `exp:upgrade:verify` findet
  Abweichungen.
- Migrationen werden in PHP, YAML, SQL oder INI geschrieben; eine Schemaänderung, die auf jeder Engine laufen muss, ist
  am besten ein Schritt `schema` ([Migrationen: SQL und Einstellungen](../../anleitungen/migrationen-sql-und-einstellungen.md)).
- Die Simulation läuft nur auf PostgreSQL und SQLite; auf MySQL/MariaDB, Oracle und MongoDB wird der Plan gelistet.
- MariaDB und Oracle haben im Kernel-Repository keinen CI-Job.

## Verwandte Seiten

- [Migrationen: der Einstieg](../migrationen/README.md), [Alle Rezepte](../migrationen/rezepte/README.md),
  [Häufige Fragen](../migrationen/faq.md)
- [Die Anleitung](../../anleitungen/migrationen.md), [das Tutorial](../../anleitungen/migrationen-tutorial.md),
  [PHP-Migrationen](../../anleitungen/migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](../../anleitungen/migrationen-repositories.md)
- [In der Administration](../migrationen/in-der-administration.md), [Befehle](../migrationen/befehle.md),
  [Einstellungen](../migrationen/einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](../migrationen/richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md),
  [Die Funktion](../../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](../migrationen/wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen/migrationen-oder-umzug-einer-site.md)
- [Persistenz und die Datenbankschicht](persistenz-und-die-datenbankschicht.md)
- [Datenbankschema und dba-Dateien](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datenbankschema-und-dba-dateien.md)
- [Aktualisieren](../../anleitungen/aktualisieren.md), [Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md),
  [Upgrade-Schritte für Erweiterungen](../../anleitungen/upgrade-schritte-fuer-erweiterungen.md)
- [Softwareingenieure: hier beginnen](softwareingenieure-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
