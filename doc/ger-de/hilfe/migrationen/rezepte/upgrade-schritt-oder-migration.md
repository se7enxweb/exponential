---
title: "Upgrade-Schritt oder Migration?"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 22
summary: Wann eine Änderung in einen Upgrade-Schritt gehört, den exp:upgrade auf jede Installation des Kernels oder einer Erweiterung anwendet, und wann in eine Migration einer Site; wie die Upgrade-Schritte unter den Migrationen erscheinen und wie eine Migration einen Schritt voraussetzt.
keywords: Migration, Migrationen, Upgrade-Schritt, Entscheidung, Voraussetzung, migration, migrations, schema change, database change, upgrade step, exp:upgrade, expUpgradeSchemaStep, expUpgradeScriptStep, ExtensionStepList, upgrade.ini, source kernel, deferred, exp:upgrade:finish, requires
featured: true
type: help
---
# Upgrade-Schritt oder Migration?

Exponential hat zwei Werkzeuge, um eine Installation im Code zu ändern. **Upgrade-Schritte** wendet
`./console exp:upgrade` an, wenn der Kernel oder eine Erweiterung aktualisiert wird. **Migrationen** sind die eigenen
Änderungen einer Site, der Reihe nach angewendet mit `./console exp:migration:migrate`. Beide werden in der Tabelle
`exp_migration` aufgezeichnet und laufen unter derselben Sperre, deshalb verwechselt man sie leicht. Diese Seite hilft
bei der Wahl und zeigt, wo sich beide treffen.

## Was Sie am Ende haben

- eine Entscheidung für Ihre Änderung;
- für einen Schritt: wo er registriert wird; für eine Migration: wohin ihre Datei gehört;
- eine Migration, die mit `requires:` auf einen Upgrade-Schritt wartet.

## Bevor Sie beginnen

Lesen Sie [Upgrade-Schritte für Erweiterungen](../../../anleitungen/upgrade-schritte-fuer-erweiterungen.md) und die
Anleitung [PHP-Migrationen](../../../anleitungen/migrationen-php.md) einmal; diese Seite vergleicht sie.

## Die Entscheidung

| Frage | Upgrade-Schritt | Migration |
|---|---|---|
| Wer braucht die Änderung? | **jede Installation** des Kernels oder der Erweiterung | **eine Site** (Entwicklung, Staging, Produktion davon) |
| Wann läuft sie? | beim Upgrade, mit `exp:upgrade --apply` | wenn der Code der Site eine Installation erreicht, mit `exp:migration:migrate --apply` |
| Woher kennt man ihren Zustand? | `detect()` fragt die Installation selbst (`PRESENT`, `MISSING`, `NOT_APPLICABLE`) | die Aufzeichnung (`done`, sobald gelaufen) oder `detect()`, wenn die Klasse eines hat |
| Ist die Datei geschützt? | keine Prüfsummen-Sperre auf der Klassendatei | eine geänderte Datei einer `done`-Migration wird abgelehnt |
| Rücknahme | kein `down()`; ein Sicherungspunkt stellt wieder her | `down()`, wo geschrieben; sonst ein Sicherungspunkt |
| Sicherungspunkt | `exp:upgrade` hält an, wenn keiner angelegt werden kann | der Lauf warnt und macht weiter |
| Wiederherstellung nach einem Fehler | nur mit `--rollback-on-fail` | voreingestellt |
| Geschrieben als | eine Klasse, die `expUpgradeSchemaStep` (Tabellen, Spalten, Indizes einer `.dba`) oder `expUpgradeScriptStep` (Zeilen, Richtlinien, Einstellungen) erweitert, gelistet in `upgrade.ini [UpgradeSettings] ExtensionStepList[]` | eine Datei `YYYYMMDDHHMMSS_name.php` mit einer Klasse, die `expMigrationPhp` erweitert, in `migrations/` oder `extension/<name>/migrations/` |

Faustregeln:

- Eine Tabelle, Spalte, ein Index oder eine Standardzeile, ohne die der Code einer Erweiterung nicht läuft:
  **Upgrade-Schritt**.
- Eine Klasse, Inhalte, eine Rolle, eine Einstellung oder eine Tabelle, auf der eine Site aufbaut: **Migration**.
- Eine Änderung einer Site, die zuerst die Tabelle einer Erweiterung braucht: eine Migration mit `requires:` auf den
  Schritt.
- Eine ganze Site mit Inhalten kopieren: keines von beiden, ein [Site-Paket](../../../anleitungen/pakete.md).

## Schritte

### 1. Die Schritte unter den Migrationen sehen

Jeder Upgrade-Schritt ist auch eine Migration der Quelle `kernel`, mit der Schritt-ID als ID:

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-130000-0a1b2c, environment staging, database mysql (transactions: data)

  done        6.0.15-migration-tables                       kernel             The migration record (tables exp_migration and exp_migration_run), with the rows of expupgrade_step copied over
  deferred    6.0.15-code-update                            kernel             The git checkouts moved to the target release, fast-forward only, through git_manager (--to=, --repos=)
  would-apply 20261101090000_news_note_priority             site               The column priority of exp_news_note
              would add the column exp_news_note.priority on mysql
...
WARN 6.0.15-code-update: pending, not run by a migration run: an upgrade step of the stage code: ./console exp:upgrade:code --apply
PASS
```

(Zeilen ausgelassen.) Ein Migrationslauf wendet zuerst die ausstehenden Schritte der Stufen `database` und `scripts`
an, dann die Migrationen der Site, in einem Lauf unter einer Sperre und einem Sicherungspunkt. Schritte der Stufen
`code`, `libraries` und `finish` sind `deferred`, mit einer Warnung, die den `exp:upgrade`-Befehl nennt, der sie
ausführt. `./console exp:migration:status` listet die Schritte ebenfalls zuerst, mit dem Zustand, den
`exp:upgrade:status` ihnen gibt.

### 2. Einen Schritt in einer Migration voraussetzen

```php
<?php
// description: The news bookmarks of the editors point to the news module view
// requires: 6.0.15-bookmark-view-columns
namespace Site\Migrations;

final class NewsBookmarks20261101140000 extends \expMigrationPhp
{
    public function plan( $context )
    {
        return array( "set module_name 'news' on the bookmarks of /news/list" );
    }

    public function up( $context )
    {
        $db = $context->db();
        $db->query( "UPDATE expbookmark_view SET module_name = 'news', view_name = 'list'"
                    . " WHERE module_name = '' AND module_uri LIKE '/news/list%'" );
        return array( 'set the module and view of the news bookmarks' );
    }
}
```

Die Migration läuft nach dem Schritt, was immer ihre IDs sagen. Eine Migration, die einen zurückgestellten Schritt
voraussetzt, wartet, bis `exp:upgrade` ihn ausgeführt hat.

### 3. Nach einem Lauf, der Schritte angewendet hat

```bash
./console exp:upgrade:finish --apply
```

Er schaltet die angewendeten Schritte live: Deploy, Caches, Rauchtest. Beide Befehle schreiben dieselbe Aufzeichnung,
ein von einem Migrationslauf angewendeter Schritt ist also in `exp:upgrade:status` `done` und umgekehrt.

## Prüfen

`./console exp:upgrade:status` und `./console exp:migration:status` stimmen bei jedem Schritt überein.
`./console exp:upgrade:verify` meldet Abweichungen: einen Schritt, den die Aufzeichnung erledigt nennt, dessen Wirkung
aber fehlt.

## Nächster Schritt

Einen Schritt schreiben: [Upgrade-Schritte für Erweiterungen](../../../anleitungen/upgrade-schritte-fuer-erweiterungen.md).
Eine Migration in einer Erweiterung schreiben:
[Migrationen in einer Erweiterung oder einem Paket](migrationen-in-einer-erweiterung-oder-einem-paket.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und Fehlerbehebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Upgrade mit der Konsole](../../../anleitungen/aktualisieren-mit-der-konsole.md),
  [Upgrade-Schritte für Erweiterungen](../../../anleitungen/upgrade-schritte-fuer-erweiterungen.md),
  [Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)
- [Schemaänderungen und Migrationen](../../softwareingenieure/schemaaenderungen-und-migrationen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
