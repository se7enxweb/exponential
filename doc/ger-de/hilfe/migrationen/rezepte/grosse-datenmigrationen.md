---
title: "Große Datenmigrationen: Stapel, Zeitgrenzen und Fortsetzen"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 17
summary: Tausende Objekte oder Zeilen in einer Migration ändern, ohne dass der Speicher ausgeht oder die Site blockiert - Stapel, transactional() mit false, ein wiederholbares up(), Pausen, Wartungsmodus und das Fortsetzen eines unterbrochenen Laufs.
keywords: Migration, Migrationen, große Datenmengen, Massenänderung, Stapel, Speicher, Zeitgrenze, fortsetzen, unterbrochen, wiederholbar, Wartungsmodus, Schemaänderung, Datenbankänderung, Auslieferung, migration, migrations, large data, bulk, batch, batches, memory, time limit, resume, interrupted, idempotent, transactional, maintenance mode, throttle, schema change, database change, deploy, exp:migration:resume, subTreeByNodeID
type: help
---
# Große Datenmigrationen: Stapel, Zeitgrenzen und Fortsetzen

Die meisten Migrationen sind in Millisekunden fertig. Eine Migration, die jeden News-Artikel einer großen Site
anfasst, nicht: Sie kann Minuten oder Stunden dauern, die ganze Zeit eine Transaktion offen halten und mehr Speicher
brauchen, als PHP erlaubt. Dieses Rezept schreibt eine solche Migration in Stapeln, macht sie wiederholbar und setzt
sie nach einer Unterbrechung fort.

## Was Sie am Ende haben

- eine Migration, die die News-Artikel zu je 200 abarbeitet und jeden Stapel committet;
- ein `up()`, das Erledigtes überspringt, sodass ein zweiter Lauf nur den Rest fertig macht;
- einen Lauf, der unterbrochen und mit `exp:migration:resume --apply` fortgesetzt werden kann.

## Bevor Sie beginnen

- Schätzen Sie die Größe: `./console exp:migration:migrate` listet den Plan, und das `plan()` dieser Migration zählt
  die Objekte.
- Kennen Sie Ihre Engine: Auf PostgreSQL und SQLite wird eine Migration in einer Transaktion bei einem Fehler
  vollständig zurückgenommen; eine Migration in Stapeln gibt das absichtlich auf
  ([Hinweise zu den Datenbanken](hinweise-zu-den-datenbanken.md)).

## Schritte

### 1. Stapel für Stapel committen

Der Runner legt jede Migration in eine Transaktion, wo die Engine eine hat. Bei Tausenden Objekten ist das eine
riesige Transaktion. Geben Sie bei `transactional()` `false` zurück, dann committet die Migration unterwegs, und die
Kernel-Funktionen, die sie aufruft (`updateAndPublishObject()`), committen ihre eigene Arbeit. Der Preis: Ein Fehler
behält die Stapel davor, also muss `up()` wiederholbar sein.

### 2. up() wiederholbar machen

Entscheiden Sie je Objekt am Objekt selbst, ob es die Änderung noch braucht. Hier: Nur ein Artikel mit leerer
Zusammenfassung wird neu veröffentlicht. Ein zweiter Lauf sieht sich alles an und ändert nur, was der erste nicht
erreicht hat.

### 3. Den Speicher flach halten

Laden Sie einen Stapel mit `Limit` und `Offset` und leeren Sie den Objekt-Cache nach jedem mit
`eZContentObject::clearCache()`. Auf der Kommandozeile hat PHP standardmäßig keine Zeitgrenze
(`max_execution_time` ist für die CLI 0), die Dauer des Laufs ist also nicht das Problem; der Speicher ist es.

### 4. Dem Server Luft lassen

Eine Pause zwischen den Stapeln (`usleep()`) verhindert, dass Datenbank und Suchindex überrollt werden, während
Besucher die Site nutzen. `migration.ini [RunSettings] Throttle` wird leer ausgeliefert und vom Runner von 6.0.15
nicht gelesen, pausieren Sie also in Ihrer eigenen Schleife.

## Die Migration

```php
<?php
// description: Every news article without a summary gets "Read more" (in batches)
// author: Site team
namespace Site\Migrations;

final class FillNewsSummaries20261101120000 extends \expMigrationPhp
{
    const PARENT_NODE = 2;
    const BATCH = 200;
    const PAUSE_MICROSECONDS = 200000;

    /** Commits batch by batch: a failure keeps the batches before it, and a second run skips them */
    public function transactional()
    {
        return false;
    }

    public function plan( $context )
    {
        $count = \eZContentObjectTreeNode::subTreeCountByNodeID( self::filter(), self::PARENT_NODE );
        return array( "look at $count news articles in batches of " . self::BATCH . ' and fill the empty summaries' );
    }

    public function up( $context )
    {
        $offset = 0;
        $seen = 0;
        $filled = 0;
        while ( true )
        {
            $nodes = \eZContentObjectTreeNode::subTreeByNodeID( self::filter() + array(
                'Limit' => self::BATCH, 'Offset' => $offset, 'SortBy' => array( 'node_id', true ) ), self::PARENT_NODE );
            if ( !$nodes )
                break;
            foreach ( $nodes as $node )
            {
                $object = $node->attribute( 'object' );
                $dataMap = $object->dataMap();
                $seen++;
                if ( !isset( $dataMap['summary'] ) || trim( $dataMap['summary']->toString() ) !== '' )
                    continue;
                if ( !\eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => array( 'summary' => 'Read more' ) ), false ) )
                    throw new \RuntimeException( 'object ' . $object->attribute( 'id' ) . ' could not be published' );
                $filled++;
            }
            $context->log( 'info', "offset $offset: $seen looked at, $filled filled" );
            $offset += self::BATCH;
            \eZContentObject::clearCache();
            usleep( self::PAUSE_MICROSECONDS );
        }
        return array( "looked at $seen news articles", "filled $filled summaries" );
    }

    private static function filter()
    {
        return array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'news_article' ),
                      'MainNodeOnly' => true, 'IgnoreVisibility' => true );
    }
}
```

Das letzte Argument `false` von `updateAndPublishObject()` veröffentlicht ohne Benachrichtigungen, Abonnenten
bekommen also keine Mail für eine technische Änderung. `$context->log()` schreibt jeden Stapel ins Laufprotokoll,
sodass sich ein langer Lauf verfolgen lässt.

### 5. Ausführen

```text
Exponential migrations: run
Run 20261101-150000-6c2f90, environment production, database mysql (transactions: data), as admin

  applied     20261101120000_fill_news_summaries           site               Every news article without a summary gets "Read more" (in batches)
              looked at 18400 news articles
              filled 3127 summaries

Captions: pre 20261101-145952, post 20261101-153411
Repository: commit 9f0c3b7e21d4, tag migrations/production/20261101-150000-6c2f90, push pushed
Summary: 1 migration(s): 1 done, 0 pending, 0 not needed, 0 failed, 0 unknown; 1 applied
PASS
```

In Setup > Migrationen gestartet, läuft derselbe Lauf im Hintergrund weiter, wenn die Seite geschlossen wird, und
seine Seite zeigt den Fortschritt.

### 6. Nach einer Unterbrechung fortsetzen

Wird der Prozess beendet (eine Auslieferung, ein Neustart), sagt die Aufzeichnung des Laufs noch `running`, aber
kein Prozess hält die Sperre. `exp:migration:resume` findet ihn:

```bash
./console exp:migration:resume            # was es ausführen würde
./console exp:migration:resume --apply    # den alten Lauf als gescheitert schließen, das Ausstehende in einem neuen Lauf ausführen
```

Weil `up()` wiederholbar ist, überspringt der neue Lauf die schon erledigten Artikel.

## Lieber teilen als wachsen lassen

Mehrere Migrationen mittlerer Größe lassen sich leichter verfolgen, fortsetzen und prüfen als eine, die alles tut.
Ordnen Sie sie mit `requires:`. Markieren Sie eine mit `maintenance: yes`, wenn Besucher keinen halb fertigen Zustand
sehen dürfen; der Wartungsmodus ist dann an, solange diese Migration läuft
([Wartungsmodus](../../../anleitungen/wartungsmodus.md)).

## Prüfen

- `./console exp:migration:dry-run --simulate --diff` auf einer SQLite-Kopie mit einigen hundert Artikeln zeigt die
  Zeilen, die sich ändern.
- Führen Sie sie auf einer Entwicklungskopie zweimal aus: Der zweite Lauf füllt 0 Zusammenfassungen.

## Zurücknehmen

Eine Migration in Stapeln hat selten ein `down()`: Die alten Werte sind weg. Der Weg zurück ist der vor dem Lauf
genommene Sicherungspunkt: `./console exp:migration:rollback 20261101-150000-6c2f90 --apply`. Auf MySQL/MariaDB,
Oracle und MongoDB ist diese Wiederherstellung auch das, was einen Fehler mittendrin zurücknimmt, weil die
committeten Stapel bleiben.

## Nächster Schritt

[Hinweise zu den Datenbanken](hinweise-zu-den-datenbanken.md): was jede Datenbank zurücknehmen kann und was nicht.

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Inhalte in großen Mengen ändern](inhalte-in-grossen-mengen-aendern.md), [exp:migration:resume](../../../beispiele/befehle/expmigrationresume.md),
  [migration.ini \[RunSettings\]](../../../beispiele/ini-einstellungen/migration/RunSettings.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
