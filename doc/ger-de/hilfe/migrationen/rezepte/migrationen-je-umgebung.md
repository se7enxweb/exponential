---
title: "Migrationen je Umgebung: Entwicklung, Staging und Produktion"
audience: developer, software-engineer, admin
category: migrations
order: 10
summary: Eine Migration mit der Kopfzeile environments auf einige Umgebungen beschränken, die Umgebung jeder Installation festlegen und eine Änderung von der Entwicklung über Staging bis in die Produktion verfolgen.
keywords: Migration, Migrationen, Umgebung, Umgebungen, Entwicklung, Test, Staging, Produktion, Schemaänderung, Datenbankänderung, Auslieferung, migration, migrations, environment, environments, development, test, staging, production, EXP_ENV, AdminEnvironment, Level, not-needed, schema change, database change, deploy, exp:migration:status, exp:migration:migrate, --environment
type: help
---
# Migrationen je Umgebung: Entwicklung, Staging und Produktion

Die meisten Migrationen gehören auf jede Installation einer Site: Tabelle, Klasse oder Rolle müssen auf dem Laptop
einer Entwicklerin, auf Staging und in der Produktion gleich sein. Manche nicht: Beispielinhalte, die eine
Entwicklungskopie brauchbar machen, ein Testkonto für das Staging-Team, eine Bereinigung, die nur die Produktion
braucht. Dieses Rezept beschränkt Migrationen auf Umgebungen und führt eine Änderung durch alle drei.

## Was Sie am Ende haben

- jede Installation der Site mit ihrer eigenen Umgebung;
- eine Migration mit Beispieldaten, die nur auf Entwicklung und Test läuft und anderswo `not-needed` ist;
- eine Statusdatei je Umgebung im Migrations-Repository, an der Sie sehen, was jede Umgebung angewendet hat.

## Bevor Sie beginnen

- Exponential 6.0.15 mit den Aufzeichnungstabellen (`./console exp:upgrade:status` zeigt `6.0.15-migration-tables`
  als `done`).
- Die Tabelle `exp_news_note` aus dem [Tabellen-Rezept](eine-datenbanktabelle-anlegen.md) (Migration
  `20261101090000_news_note_table`).

## Schritte

### 1. Jeder Installation ihre Umgebung geben

Die Umgebung ist `site.ini [AdminEnvironment] Level`: `development`, `test`, `staging` oder `production`. Sie ist
zugleich das Umgebungs-Abzeichen der Administration. Setzen Sie sie im Override jeder Installation:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
Level=staging
*/ ?>
```

Mit `Level=auto` kommt die Umgebung aus der Variablen `EXP_ENV` (`dev` und `development`, `test`, `stage` und
`staging`, `prod`, `production` und `live`); das passt zu Containern und Hosts, die sie schon setzen. Eine
Installation mit leerem `Level` hat keine Umgebung: Auf Umgebungen beschränkte Migrationen laufen dort nicht, und
ihre Statusdatei heißt `status/default.json`.

### 2. Eine Migration nur für einige Umgebungen schreiben

```bash
./console exp:migration:generate --skeleton sample_news_notes --description="Sample news notes for development and test installations" --apply
```

Fügen Sie die Kopfzeile `environments:` hinzu und füllen Sie die Klasse:

```php
<?php
// description: Sample news notes for development and test installations
// requires: 20261101090000_news_note_table
// environments: development, test
// author: Site team
namespace Site\Migrations;

final class SampleNewsNotes20261101093000 extends \expMigrationPhp
{
    const FIRST_ID = 9001;
    const COUNT = 3;

    public function plan( $context )
    {
        return array( 'write ' . self::COUNT . ' sample notes (ids ' . self::FIRST_ID . ' and up) into exp_news_note' );
    }

    public function up( $context )
    {
        $db = $context->db();
        for ( $i = 0; $i < self::COUNT; $i++ )
        {
            $id = self::FIRST_ID + $i;
            $db->query( "INSERT INTO exp_news_note ( id, title ) VALUES ( $id, 'Sample note $id' )" );
        }
        return array( 'wrote ' . self::COUNT . ' sample notes' );
    }

    public function down( $context )
    {
        $context->db()->query( 'DELETE FROM exp_news_note WHERE id >= ' . self::FIRST_ID
                               . ' AND id < ' . ( self::FIRST_ID + self::COUNT ) );
        return array( 'removed the sample notes' );
    }
}
```

Die umgekehrte Form lässt Umgebungen aus: `environments: !production` läuft überall außer in der Produktion.
Verwenden Sie in einem Kopf entweder Namen oder `!Namen`, nicht beides.

### 3. Den Unterschied je Umgebung sehen

Auf einer Entwicklungsinstallation:

```text
Exponential migrations: status
Run 20261101-093512-a41c07, environment development, database sqlite (transactions: full)

  done        20261101090000_news_note_table               site               A table for news notes, with a first note
  pending     20261101093000_sample_news_notes             site               Sample news notes for development and test installations

Summary: 2 migration(s): 1 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

Auf Staging, dieselben Dateien:

```text
Exponential migrations: status
Run 20261101-101500-3f9a1c, environment staging, database mysql (transactions: data)

  done        20261101090000_news_note_table               site               A table for news notes, with a first note
  not-needed  20261101093000_sample_news_notes             site               Sample news notes for development and test installations

Summary: 2 migration(s): 1 done, 0 pending, 1 not needed, 0 failed, 0 unknown
PASS
```

`--environment=<name>` fragt für einen Befehl als eine andere Umgebung, etwa um auf Staging zu sehen, was die
Produktion ausführen würde: `./console exp:migration:status --environment=production`. Die Installation ändert das
nicht.

### 4. Eine Änderung durch die Umgebungen führen

1. Eine Entwicklerin schreibt die Migration, führt sie auf der Entwicklung aus (`exp:migration:migrate --apply`),
   committet die Datei und öffnet einen Pull Request; die CI führt `./console exp:migration:check` aus, das die
   Reihenfolge jeder Umgebung prüft.
2. Nach dem Merge holt Staging den Code und führt `./console exp:migration:migrate --apply` aus. Der Lauf committet
   `migrations/status/staging.json` und taggt `migrations/staging/<run-id>`.
3. Die Produktion tut dasselbe; ihr Lauf taggt `migrations/production/<run-id>`.

Jede Umgebung pusht ihre eigene Statusdatei, also zeigt ein Klon des Repositorys, was jede angewendet hat:

```bash
git show origin/main:migrations/status/staging.json | head -20
git tag -l 'migrations/production/*' --sort=version:refname | tail -3
```

## Prüfen

- `./console exp:migration:status` nennt auf jeder Installation die Umgebung in seiner zweiten Zeile.
- Eine Migration, die irgendwo nicht laufen soll, ist dort `not-needed`, nie `pending`.

## Zurücknehmen

Die Beispieldaten sind umkehrbar: `./console exp:migration:rollback 20261101093000_sample_news_notes --apply` auf
einer Entwicklungsinstallation führt `down()` aus. Auf Staging und in der Produktion gibt es nichts zurückzunehmen.

## Nächster Schritt

Migrationen mit einer Erweiterung ausliefern:
[Migrationen in einer Erweiterung oder einem Paket](migrationen-in-einer-erweiterung-oder-einem-paket.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Mit Migrationen ausliefern](mit-migrationen-ausliefern.md), [Das Migrations-Repository](../../../anleitungen/migrationen-repositories.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
