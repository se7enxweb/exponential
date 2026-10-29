# Upgrade-Schritte für Erweiterungen

Diese Anleitung zeigt Entwicklern einer Erweiterung, wie das eigene Upgrade einer Erweiterung, etwa eine neue
Tabelle, eine neue Spalte, eine Standardzeile oder eine Richtlinie, Teil von `./console exp:upgrade` wird. Ist der
Schritt registriert, sieht ein Administrator ihn beim Upgrade in `exp:upgrade:status`, erhält ihn mit
`exp:upgrade --apply` und hat ihn aufgezeichnet, genauso wie die Schritte des Kernels.

[Anleitungen](README.md) · Siehe auch: [Upgrade mit der Konsole](aktualisieren-mit-der-konsole.md),
[Erweiterungen](extensions.md)

## Der Schritt

Ein Schritt ist eine Klasse, die `expUpgradeStep` implementiert (`kernel/classes/upgrade/expupgradestep.php`). Drei
Basisklassen erledigen den größten Teil:

| Basisklasse | Für | Sie schreiben |
|---|---|---|
| `expUpgradeSchemaStep` | Tabellen, Spalten und Indizes aus einem `.dba`-Schema | `tables()`, `fields()`, `indexes()` |
| `expUpgradeScriptStep` | alles andere: Zeilen, Richtlinien, Einstellungen, Dateien | `changes()` (was fehlt, je eine Zeile) und `write()` |
| `expUpgradeBaseStep` | die Vorgaben (jede Engine, keine Abhängigkeit, Version 6.0.15) | alles Übrige |

Jeder Schritt hat:

- `id()`: dauerhaft gleich, `<Version>-<Name>`, zum Beispiel `1.4.0-myext-widget-table`. Sie ist das, was die
  Aufzeichnung behält und was Administratoren an `--only` und `--skip` übergeben. Benennen Sie sie nie um.
- `stage()`: `database` oder `scripts` (`finish` gehört dem Kernel).
- `version()`: das Release der Erweiterung, das ihn gebracht hat. Schritte laufen nach Stufe, dann nach Version, dann
  in der aufgelisteten Reihenfolge.
- `engines()`: `array( '*' )` oder die Engines, für die er gilt (`mysql`, `postgresql`, `sqlite`, `oracle`,
  `mongodb`).
- `after()`: Schritt-IDs, die vorher erledigt sein müssen.
- `detect( $context )`: was die Installation jetzt enthält: `PRESENT`, `MISSING`, `NOT_APPLICABLE` oder `UNKNOWN`,
  mit einem Detail. Sehen Sie in der Installation selbst nach (im Katalog der Datenbank, in der Rolle, in den
  Einstellungen), nie nur in der Aufzeichnung: Ein Administrator kann den Schritt von Hand erledigt haben.
- `plan( $context )`: was `apply()` ändern würde, je eine Zeile, für den Probelauf. Er darf nichts ändern.
- `apply( $context )`: nimmt die Änderung vor und gibt zurück, was er getan hat; im Fehlerfall wirft er eine
  Ausnahme. Er muss wiederholbar sein und einen halb ausgeführten Zustand vervollständigen: `exp:upgrade` kann ihn
  nach einem Fehler erneut ausführen.

Verwenden Sie `$context->db()` für die Datenbank, nie ein eigenes `eZDB::instance()`: Tests führen Schritte auf
anderen Verbindungen aus.

## Eine Tabelle und eine Spalte

Die Erweiterung hält ihre Tabellen in `share/db_schema.dba`, wie der Kernel. Der Schema-Schritt liest die
Definitionen aus der Datei, die Sie nennen, legt fehlende Tabellen über die Schema-Klasse der Engine an und ergänzt
fehlende Spalten und Indizes:

```php
<?php
class myextUpgradeStepWidgetTable extends expUpgradeSchemaStep
{
    public function id()          { return '1.4.0-myext-widget-table'; }
    public function version()     { return '1.4.0'; }
    public function description() { return 'myext: the widget table and its owner column'; }
    public function tables()      { return array( 'myext_widget' ); }
    public function fields()      { return array( 'myext_item' => array( 'owner_id' ) ); }
    public function indexes()     { return array( 'myext_item' => array( 'myext_item_owner' ) ); }
}
```

Der Kontext liest standardmäßig das Kernel-Schema. Für das eigene `.dba` der Erweiterung geben Sie dem Schritt einen
Kontext mit dieser Datei:

```php
    public function detect( expUpgradeContext $context ) { return parent::detect( $this->own( $context ) ); }
    public function plan( expUpgradeContext $context )   { return parent::plan( $this->own( $context ) ); }
    public function apply( expUpgradeContext $context )  { return parent::apply( $this->own( $context ) ); }

    private function own( expUpgradeContext $context )
    {
        return new expUpgradeContext( $context->db(), $context->isApply(), array(), 'extension/myext/share/db_schema.dba' );
    }
```

## Zeilen, Richtlinien und Einstellungen

```php
<?php
class myextUpgradeStepDefaultCategory extends expUpgradeScriptStep
{
    public function id()          { return '1.4.0-myext-default-category'; }
    public function version()     { return '1.4.0'; }
    public function description() { return 'myext: the category "General" exists'; }
    public function after()       { return array( '1.4.0-myext-widget-table' ); }

    protected function changes( expUpgradeContext $context )
    {
        $rows = $context->db()->arrayQuery( "SELECT id FROM myext_category WHERE identifier = 'general'" );
        return $rows ? array() : array( 'create the category "General"' );
    }

    protected function write( expUpgradeContext $context )
    {
        $context->db()->query( "INSERT INTO myext_category ( identifier, name ) VALUES ( 'general', 'General' )" );
        return array( 'created the category "General"' );
    }
}
```

`expUpgradeScriptStep` macht aus `changes()` den Zustand (eine leere Liste heißt vorhanden), verwendet sie als
Probelauf und fragt nach `write()` erneut `changes()`: Was dann noch aufgelistet wird, lässt den Schritt scheitern.
Setzen Sie `$this->notApplicable = true` mit einem `$this->reason`, wenn der Schritt nicht gilt (die Funktion ist
abgeschaltet), oder geben Sie `null` mit einem Grund zurück, wenn es sich nicht feststellen lässt.

Hat die Erweiterung schon einen Setup-Befehl, rufen Sie die Klasse auf, die dieser Befehl verwendet, mit den
Standardoptionen des Befehls, so wie es die Richtlinien-Schritte des Kernels tun
(`kernel/classes/upgrade/steps/expupgradestepeditorpolicies.php`). Starten Sie in einem Schritt nicht das Skript des
Befehls.

## Die Schritte registrieren

In der `settings/upgrade.ini.append.php` der Erweiterung:

```ini
[UpgradeSettings]
ExtensionStepList[]=myextUpgradeStepWidgetTable
ExtensionStepList[]=myextUpgradeStepDefaultCategory
```

Erzeugen Sie die Autoloads (`php bin/php/ezpgenerateautoloads.php -e`) und prüfen Sie:

```bash
./console exp:upgrade:steps --stage=database
./console exp:upgrade:status
```

Eine Klasse, die nicht im Autoload-Array steht oder `expUpgradeStep` nicht implementiert, wird als Warnung gelistet
und weggelassen; zwei Schritte mit derselben ID, ein `after()`, das keinen Schritt nennt, und ein Zyklus sind
ebenfalls Warnungen.

Liefern Sie die Schritte im Repository der Erweiterung und in ihrem `-ezpackage`-Repository gleichermaßen aus: Beide
werden installiert, und beide werden aktualisiert.

## Einen Schritt testen

Testen Sie jeden Schritt auf vier Arten auf einer Wegwerf-Datenbank (immer SQLite; MySQL und PostgreSQL, wo
möglich):

1. eine neue Installation: `not-needed`;
2. Schema und Daten des vorigen Releases: `pending`, dann `exp:upgrade --apply`, dann `done`;
3. ein zweites `--apply`: nichts ändert sich;
4. halb ausgeführt (eine von zwei Tabellen, eine von zwei Spalten): der Lauf vervollständigt es.

`expUpgrade::run( array( 'database', 'scripts' ), array( 'db' => $db, 'apply' => true ) )` führt die Stufen auf einer
Verbindung Ihres Tests aus. Die Tests des Kernels liegen in `tests/tests/kernel/classes/upgrade/`.
