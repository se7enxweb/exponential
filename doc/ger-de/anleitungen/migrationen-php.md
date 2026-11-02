# PHP-Migrationen

Diese Seite ist die Referenz für eine in PHP geschriebene Migration: die Datei, die Klasse, die Methoden, die der Runner
aufruft, was der Kontext einer Migration gibt, Transaktionen, Fehler, Referenzen zwischen Migrationen und wie man eine
Migration testet. Lesen Sie zuerst [das Tutorial](migrationen-tutorial.md), wenn Sie noch keine Migration geschrieben
haben.

[Anleitungen](README.md) · [Migrationen: der Einstieg](../hilfe/migrationen/README.md) · [Migrationen](migrationen.md) · Siehe auch: [Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md),
[Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md), [Fehlerbehebung](migrationen-fehlerbehebung.md)

## Die Datei und die Klasse

Eine PHP-Migration ist eine Datei `YYYYMMDDHHMMSS_name.php` mit ihrem Kopf und **einer Klasse in einem Namensraum, die
`expMigrationPhp` erweitert**:

```php
<?php
// description: A table for news notes and the News folder
// requires: 20261030090000_news_class
namespace MySite\Migrations;

final class AddNewsNotes extends \expMigrationPhp
{
    public function plan( $context )
    {
        return array( 'create the table news_note', 'create the folder News below the root' );
    }

    public function up( $context )
    {
        // ...
        return array( 'created news_note', 'created the folder News' );
    }
}
```

- Der Klassenname ist frei; der Namensraum ist Ihrer (der der Site oder der Erweiterung), sodass zwei Migrationen
  verschiedener Quellen nie kollidieren. `exp:migration:generate --skeleton` schreibt `Site\Migrations` für die Site
  und `Extension\<Name>\Migrations` für eine Erweiterung und hängt die Zeit an den Klassennamen.
- Die Datei muss genau eine nicht abstrakte Klasse deklarieren, die `expMigrationPhp` erweitert; eine Datei mit keiner,
  zweien oder einer Klasse, die eine andere Datei schon deklariert, wird abgelehnt (`exp:migration:check` meldet das
  vor jedem Lauf).
- Die Datei wird mit `require` geladen, wenn die Migration geladen wird; halten Sie sie frei von Code außerhalb der
  Klasse.
- Der Kopf ist in [dem Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md) beschrieben; in einer PHP-Datei
  wird er mit `//`, `#` oder einem Block `/** ... */` vor der Zeile `namespace` geschrieben.

Das Gerüst für eine neue erzeugen:

```bash
./console exp:migration:generate --skeleton add_news_notes --description="A table for news notes" --apply
./console exp:migration:generate --skeleton news_folder --source=extension:expnews --apply
```

## Die Methoden

| Methode | Aufgerufen von | Muss | Voreinstellung |
|---|---|---|---|
| `up( $context )` | einem Lauf mit `--apply` und der Simulation | die Änderung vornehmen und zurückgeben, was es getan hat, ein String je Zeile; bei einem Fehler werfen | (abstrakt) |
| `plan( $context )` | dem Probelauf | nennen, was `up()` tun würde, ohne etwas zu ändern | die Beschreibung |
| `down( $context )` | `exp:migration:rollback <id>` | `up()` zurücknehmen und zurückgeben, was es getan hat | wirft: nicht umkehrbar |
| `detect( $context )` | jeder Abfrage des Zustands | `DONE`, `PENDING` oder `NOT_NEEDED` zurückgeben, wenn die Installation es sagen kann, sonst `null` | `null` (die Aufzeichnung entscheidet) |
| `transactional()` | dem Runner | `false` für eine Migration, die selbst committet (ein langer Stapel) | `true` |

Deklarieren Sie diese Methoden so, wie die Tabelle sie zeigt: öffentlich, nicht statisch, mit ihrem einen Parameter
`$context` ohne Typ (weitere Parameter nur mit Standardwert). Die übrigen Methoden von `expMigrationPhp`
(`definition()`, `file()`, `id()`, `source()`, `checksum()`, `state()`, `requires()`, `engines()` ...) gehören ihr und
sind final; eine Hilfsmethode darf keinen ihrer Namen tragen. PHP würde eine solche Klasse beim Laden der Datei mit
einem fatalen Fehler ablehnen; `exp:migration:check` und jeder Lauf lesen die Klasse vorher und melden die Methode mit
Namen (zum Beispiel „...::definition() has the name of a method of expMigrationPhp that a migration cannot declare:
give it another name“), ohne die Datei zu laden.

`plan()` läuft bei jedem Probelauf, muss also billig sein und darf nicht schreiben. Bauen Sie es, wo es geht, aus reinen
Funktionen, damit es sich ohne Datenbank testen lässt.

`up()` gibt ein Array von Strings zurück; sie erscheinen in der Ausgabe, im Laufprotokoll, in der Aufzeichnung (die
letzte Meldung) und im Audit-Ereignis. Geben Sie zurück, was ein Leser wissen muss, nie ein Geheimnis.

### down() und reversible()

Eine Klasse, die `down()` definiert, ist umkehrbar (`reversible()` sagt das). Lassen Sie `down()` weg, wenn sich eine
Änderung nicht von selbst zurücknehmen lässt, etwa das Entfernen von Inhalten: `exp:migration:rollback <id>` lehnt dann
ab mit „cannot be undone by itself (no down()); restore the caption taken before it“, und der Weg zurück ist der
Sicherungspunkt des Laufs ([Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md)). Auch das
`down()` einer Migration, die nicht `done` ist, wird abgelehnt. Nach einem erfolgreichen `down()` wird die Aufzeichnung
der Migration entfernt, und sie steht wieder aus.

### detect(): der Zustand ohne die Aufzeichnung

Eine PHP-Migration richtet sich zunächst nach der Aufzeichnung: `done`, sobald ein Lauf sie aufgezeichnet hat, `failed`,
wenn der letzte Lauf scheiterte, sonst `pending`. Eine Klasse, die die Installation selbst befragen kann, gibt aus
`detect()` einen Zustand zurück:

```php
public function detect( $context )
{
    return \expKernelTableSchema::exists( 'exp_news_note', $context->db(), true )
        ? \expMigration::DONE : null;
}
```

`null` fällt auf die Aufzeichnung zurück. Eine Migration, die so `NOT_NEEDED` oder `DONE` meldet, läuft nicht; so
unterscheiden die eigenen Schritte des Kernels (und `expWebsiteSidebarMigration`) eine Installation, die die Änderung
schon hat, von einer, die sie braucht.

## Der Kontext

Jede Methode bekommt den Kontext des Laufs, einen `expMigrationContext`. Nutzen Sie ihn, nie eine eigene Verbindung oder
einen eigenen Benutzer, damit die Transaktion des Runners, die Datenbank eines Tests und der Benutzer des Laufs gelten.

| Aufruf | Gibt |
|---|---|
| `$context->db()` | die Datenbankverbindung (`eZDBInterface`) |
| `$context->engine()` | `mysql`, `postgresql`, `sqlite`, `oracle` oder `mongodb` |
| `$context->isApply()` | `true` in einem Lauf, der schreibt, `false` in einem Probelauf |
| `$context->environment()` | die Umgebung des Laufs (`site.ini [AdminEnvironment] Level`, `EXP_ENV` oder `--environment`) |
| `$context->runId()` | die ID des Laufs, `YYYYMMDD-HHMMSS-<6 hex>` |
| `$context->user()`, `$context->userLogin()` | der Benutzer, als der der Lauf läuft (`eZUser`), und sein Login |
| `$context->setReference( $name, $value )` | hinterlässt einen Wert für die Migrationen nach dieser im selben Lauf |
| `$context->reference( $name, $default )`, `hasReference( $name )`, `references()` | liest sie |
| `$context->log( $level, $text )` | eine Zeile im Laufprotokoll und in `var/log/migration.log` (`debug`, `info`, `notice`, `warning`, `error`), bereinigt von Geheimnissen |
| `$context->option( $name )` | eine Option des Laufs (`force`, `reason`, `only` ...) |
| `$context->schema()`, `$context->table( $name )` | das Kernel-Schema (`share/db_schema.dba`) |

Referenzen leben einen Lauf lang. Ein späterer Lauf, der einen Wert braucht, findet ihn wieder in der Installation (über
die Remote-ID, über einen Bezeichner), nicht in einer Referenz; das ist auch, was eine Migration auf jeder Installation
funktionieren lässt, wo die IDs andere sind.

Während `up()` und `down()` ist der aktuelle Benutzer der Benutzer des Laufs, Inhalte werden also von ihm angelegt und
gehören ihm, und Richtlinien gelten wie für ihn. Auf der Konsole ist das `migration.ini [RunSettings] RunAsUser`
(`admin`) oder `--login=<login>`.

## Transaktionen

Jede Migration läuft in ihrer eigenen Transaktion, wo die Datenbank eine hat:

| Datenbank | `transactions:` in der Ausgabe | Was eine gescheiterte Migration hinterlässt |
|---|---|---|
| PostgreSQL, SQLite | `full` | nichts: Schema und Daten werden zurückgenommen |
| MySQL/MariaDB, Oracle | `data` | ihre Schemaänderungen (sie werden sofort committet); ihre Daten werden zurückgenommen |
| MongoDB | `none` | alles, was sie geschrieben hat |

Die Transaktion wird committet, wenn `up()` zurückkehrt. Solange sie offen ist, wirft eine scheiternde Anweisung, statt
den Prozess zu beenden. Eine Migration, die selbst committen muss, etwa ein Stapel von Tausenden Objekten, gibt aus
`transactional()` `false` zurück und muss selbst gefahrlos erneut laufen können. Was eine Transaktion nicht zurücknehmen
kann, kann der Sicherungspunkt vor dem Lauf: Nach einem Fehler wird er standardmäßig wiederhergestellt.

Für einen solchen Stapel committet `$this->batches( $context, $fetch, $handle, $size )` jeden Stapel für sich und hält
im Checkpoint der Migration fest, wie weit sie gekommen ist, so dass der nächste Lauf dort weitermacht:
[PHP-Migrationen in Stapeln](migrationen-pakete-deploy-und-lange-laeufe.md#php-migrationen-in-stapeln).

Ordnen Sie Ihre eigenen Schritte so, dass ein Fehler auf halbem Weg den geringsten Schaden anrichtet: eine Tabelle
anlegen, bevor in sie geschrieben wird; auf MySQL Daten schreiben vor der Schemaänderung, die von ihnen abhängt.
Teilen Sie eine große Änderung auf mehrere Migrationen auf.

## Fehler

Werfen Sie in `up()` eine Ausnahme. Der Runner:

1. rollt die Transaktion der Migration zurück (soweit die Datenbank kann);
2. zeichnet die Migration als `failed` mit der Meldung der Ausnahme auf;
3. hält den Lauf an (`[RunSettings] StopOnFailure=enabled`); die Migrationen danach sind `not-run`;
4. legt einen Sicherungspunkt des gescheiterten Zustands an (`migration-failure`) und stellt den Sicherungspunkt von
   vorher wieder her;
5. schreibt, committet und pusht die Aufzeichnung des gescheiterten Laufs; endet mit Exit-Code 1.

Mit `StopOnFailure=disabled` geht der Lauf mit den Migrationen weiter, die die gescheiterte nicht verlangen; die, die
sie verlangen, sind `blocked`. Eine gescheiterte Migration läuft beim nächsten Lauf erneut oder mit
`exp:migration:resume`.

## Wartungsmodus

Eine Migration, deren Kopf `maintenance: yes` sagt, schaltet den Wartungsmodus ein, bevor sie läuft, und danach wieder
aus (nur wenn der Lauf ihn eingeschaltet hat; eine Site, die schon im Wartungsmodus war, bleibt darin). Nehmen Sie das
für Änderungen, die Besucher nicht halb fertig sehen dürfen.

## Ereignisse und Audit

Der Runner benachrichtigt `ezpEvent`-Listener, eingetragen in `site.ini [Event] Listeners[]=<event>@<callback>`; ein
Listener, der wirft, hält den Lauf nicht an. Die Parameter, nach Position:

| Ereignis | Parameter |
|---|---|
| `migration/run/before` | `string $runId`, `string[] $ids`, `expMigrationContext $context` |
| `migration/step/before` | `string $runId`, `expMigration $migration`, `expMigrationContext $context` |
| `migration/step/after` | `string $runId`, `expMigration $migration`, `array $entry`, `expMigrationContext $context` |
| `migration/run/after` | `string $runId`, `array $report`, `expMigrationContext $context` |

Das Audit-Protokoll hält `system.migration.run`, `system.migration.apply` (auch eine mit `--force --reason`
angenommene geänderte Datei), `system.migration.fail`, `system.migration.rollback`, `system.migration.caption` und
`system.migration.push` fest (eingeschaltet in `settings/audit.ini`).

## Migrationen in einer Erweiterung

Legen Sie sie in `extension/<name>/migrations/` ab (der Ordnername ist `[MigrationSettings] ExtensionDirectory`). Sie
werden gefunden, solange die Erweiterung aktiv ist, mit der Quelle `extension:<name>`. Eine Migration der Site, die
eine davon braucht, nennt sie: `requires: 20261101090000_expnews_tables` oder `requires: extension:expnews` (die
Erweiterung muss aktiv sein). Für Änderungen, die jede Installation einer Erweiterung beim Upgrade braucht, ist ein
[Upgrade-Schritt](upgrade-schritte-fuer-erweiterungen.md) weiterhin das richtige Werkzeug.

## Eine Migration testen

- `./console exp:migration:check` (oder `--path=<Datei oder Ordner>`) in CI: Syntax, Kopf, Reihenfolge, die Klasse
  lädt.
- `./console exp:migration:dry-run --simulate --diff` auf einer PostgreSQL- oder SQLite-Kopie der Site: Die Migration
  läuft wirklich und wird zurückgerollt.
- Ein PHPUnit-Test kann die Datei laden und auf einer Wegwerf-SQLite-Datenbank ausführen, wie es die eigenen Tests des
  Kernels tun (`tests/tests/kernel/classes/migration/expMigrationRunnerSqliteTest.php`):

```php
$locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => __DIR__ . '/migrations' ) ) );
$errors = array();
$migrations = expMigrationRunner::load( ( new expMigrationGraph( $locator->find() ) )->migrations(), $errors );
$context = new expMigrationContext( $db, true, array( 'environment' => 'testing', 'no-mirror' => true,
                                                      'user' => eZUser::fetchByName( 'admin' ) ) );
$report = ( new expMigrationRunner( $context ) )->run( $migrations );
$this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
```

Testen Sie `plan()` ohne Datenbank, indem Sie es aus reinen Funktionen bauen, und führen Sie `up()` und `down()` in
einem Test zweimal aus, um zu zeigen, dass sich eine Migration zurücknehmen und erneut anwenden lässt.

## Verwandte Seiten

- [Migrationen: der Einstieg](../hilfe/migrationen/README.md), [Alle Rezepte](../hilfe/migrationen/rezepte/README.md),
  [Häufige Fragen](../hilfe/migrationen/faq.md)
- [Die Anleitung](migrationen.md), [das Tutorial](migrationen-tutorial.md),
  [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](migrationen-repositories.md)
- [In der Administration](../hilfe/migrationen/in-der-administration.md), [Befehle](../hilfe/migrationen/befehle.md),
  [Einstellungen](../hilfe/migrationen/einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](../hilfe/migrationen/richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md), [Die Funktion](../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](../hilfe/migrationen/wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../hilfe/migrationen/migrationen-oder-umzug-einer-site.md)
