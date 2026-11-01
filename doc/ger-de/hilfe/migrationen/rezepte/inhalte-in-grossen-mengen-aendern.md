---
title: "Inhalte in großen Mengen ändern"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 5
summary: "Eine Migration, die alle Objekte einer Klasse in Stapeln durchgeht, von jedem, das die Änderung braucht, eine neue Version veröffentlicht, je Objekt committet, ihren Fortschritt protokolliert und gefahrlos erneut laufen kann - das Muster für jede Änderung an vielen Objekten."
keywords: Migration, Migrationen, Massenänderung, viele Objekte, Stapel, neue Version, Inhaltsänderung, migration, migrations, bulk change, bulk update, batch, batches, subTreeByNodeID, updateAndPublishObject, transactional, clearCache, exp:migration:migrate, exp:migration:resume
featured: true
type: help
---
# Inhalte in großen Mengen ändern

Dieses Rezept richtet sich an Entwickler, die viele Objekte auf einmal ändern müssen: ein neues Feld füllen, einen
Wert korrigieren, den ein Import falsch geschrieben hat, eine Angabe aus dem Text in ein Attribut übernehmen. Von Hand
in der Administration ist das langsam und auf jeder Installation anders; als Migration wird es einmal geschrieben,
geprüft und auf Staging und Produktion gleich angewendet. Das Beispiel gibt jedem News-Artikel ohne Quelle die Quelle
„Newsroom“.

## Was Sie am Ende haben

- die Migration `20261101130000_news_article_default_source.php`;
- eine neue veröffentlichte Version jedes News-Artikels, dessen Source leer war, mit der früheren Version in seinem
  Verlauf;
- eine Migration, die je Objekt committet, ihren Fortschritt ins Laufprotokoll schreibt und bei einem zweiten Lauf
  nichts ändert.

## Bevor Sie beginnen

- Exponential 6.0.15 mit erledigtem `6.0.15-migration-tables`; die Klasse `news_article`
  ([Eine Inhaltsklasse anlegen oder ändern](eine-inhaltsklasse-anlegen-oder-aendern.md)).
- Eine Kopie der Produktionsdaten auf einer Entwicklungs- oder Staging-Installation, damit Sie sehen, wie lange der
  Lauf dauert. Messen Sie dort, bevor Sie den Lauf auf Produktion planen.
- Ausführen als Benutzer des Webservers (als root mit `--allow-root-user`). Die Objekte werden als Benutzer des Laufs
  veröffentlicht.

## Schritte

### 1. Erzeugen und füllen

```bash
./console exp:migration:generate --skeleton news_article_default_source --description="Every news article without a source gets the source Newsroom (a new version each)" --apply
```

Ergänzen Sie `// requires: 20261101110000_news_article_class` und die Methoden unten.

### 2. Den Plan ansehen

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-130207-91af3c, environment staging, database mysql (transactions: data)

  would-apply 20261101130000_news_article_default_source   site               Every news article without a source gets the source Newsroom (a new version each)
              would look at 2418 news article(s) in batches of 100
              would publish a new version with the source "Newsroom" for each one without a source

Captions: pre would-take, post would-take
Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

### 3. Ausführen

```bash
./console exp:migration:migrate --apply
```

```text
Exponential migrations: run
Run 20261101-131015-2c8e40, environment staging, database mysql (transactions: data), as admin

  applied     20261101130000_news_article_default_source   site               Every news article without a source gets the source Newsroom (a new version each)
              looked at 2418 news article(s)
              gave 312 of them the source Newsroom

Captions: pre 20261101-131015, post 20261101-131322
Repository: commit 0e5a7f1b3c99, tag migrations/staging/20261101-131015-2c8e40, push pushed
Summary: 1 migration(s): 1 done, 0 pending, 0 not needed, 0 failed, 0 unknown; 1 applied
PASS
```

Während er läuft, bekommt das Laufprotokoll `migrations/runs/20261101-131015-2c8e40.log` eine Zeile je Stapel:

```text
2026-11-01T20:10:21Z info batch at offset 0: 100 seen, 14 changed
2026-11-01T20:10:27Z info batch at offset 100: 200 seen, 29 changed
```

In Setup > Migrationen gestartet, läuft er im Hintergrund weiter, und seine Seite zeigt denselben Fortschritt.

## Die Migration

```php
<?php
// description: Every news article without a source gets the source Newsroom (a new version each)
// requires: 20261101110000_news_article_class
// maintenance: no
// author: Site team
namespace Site\Migrations;

final class NewsArticleDefaultSource20261101130000 extends \expMigrationPhp
{
    const CLASS_IDENTIFIER = 'news_article';
    const DEFAULT_SOURCE = 'Newsroom';
    const BATCH = 100;
    const ROOT_NODE = 2;

    /** @return array the fetch parameters of one batch */
    private static function batch( $offset )
    {
        return array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::CLASS_IDENTIFIER ),
                      'SortBy' => array( 'node_id', true ), 'Limit' => self::BATCH, 'Offset' => $offset,
                      'MainNodeOnly' => true, 'LoadDataMap' => false, 'IgnoreVisibility' => true );
    }

    /** Whether an object still needs the change: pure, so plan(), up() and a second run agree */
    public static function needsSource( $value )
    {
        return trim( (string)$value ) === '';
    }

    /**
     * Commits per object: a run over thousands of articles must not hold one transaction. Safe to run again,
     * because an article that has a source is left alone.
     */
    public function transactional()
    {
        return false;
    }

    public function plan( $context )
    {
        $count = \eZContentObjectTreeNode::subTreeCountByNodeID( array( 'ClassFilterType' => 'include',
                     'ClassFilterArray' => array( self::CLASS_IDENTIFIER ), 'MainNodeOnly' => true, 'IgnoreVisibility' => true ), self::ROOT_NODE );
        return array( "look at $count news article(s) in batches of " . self::BATCH,
                      'publish a new version with the source "' . self::DEFAULT_SOURCE . '" for each one without a source' );
    }

    public function up( $context )
    {
        $changed = 0;
        $seen = 0;
        for ( $offset = 0; ; $offset += self::BATCH )
        {
            $nodes = \eZContentObjectTreeNode::subTreeByNodeID( self::batch( $offset ), self::ROOT_NODE );
            if ( !$nodes )
                break;
            foreach ( $nodes as $node )
            {
                ++$seen;
                $object = $node->object();
                $dataMap = $object->dataMap();
                if ( !isset( $dataMap['source'] ) || !self::needsSource( $dataMap['source']->toString() ) )
                    continue;
                if ( !\eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => array( 'source' => self::DEFAULT_SOURCE ) ), false ) )
                    throw new \RuntimeException( 'the article ' . $object->attribute( 'remote_id' ) . ' could not be published (' . $changed . ' changed before it)' );
                ++$changed;
            }
            // free the objects of the batch: a long run must not grow without limit
            \eZContentObject::clearCache();
            $context->log( 'info', "batch at offset $offset: $seen seen, $changed changed" );
        }
        return array( "looked at $seen news article(s)", "gave $changed of them the source " . self::DEFAULT_SOURCE );
    }
}
```

Warum sie so geschrieben ist:

- **`transactional()` liefert `false`.** Tausende Veröffentlichungen in einer Transaktion hielten minutenlang Sperren
  und machten beim letzten Fehler alles rückgängig. Hier committet jede Veröffentlichung für sich.
- **Sie kann gefahrlos erneut laufen.** `needsSource()` überspringt Artikel mit Quelle; nach einem Fehler auf halbem
  Weg macht der nächste Lauf (oder `./console exp:migration:resume --apply`) dort weiter, wo er aufhörte, und ein
  zweiter Lauf ändert nichts.
- **Stapel mit fester Reihenfolge** (`SortBy node_id`) und `eZContentObject::clearCache()` nach jedem Stapel halten
  den Speicher flach. Der Filter gilt der Klasse, nicht dem geänderten Wert, daher bleiben die Offsets gültig,
  während sie schreibt.
- **Eine neue Version je Objekt** (`updateAndPublishObject()`) behält den Verlauf, aktualisiert den Suchindex und
  leert die Ansichts-Caches wie eine Veröffentlichung im Editor. Das dritte Argument `false` verschickt keine
  Benachrichtigungen an Abonnenten.
- **Kein `down()`.** Die früheren Werte liegen im Versionsverlauf jedes Objekts und im Sicherungspunkt; die Rücknahme
  nur dieser Migration wird mit „cannot be undone by itself“ abgelehnt, und das ist die ehrliche Antwort.

## Prüfen

- Öffnen Sie einige News-Artikel in der Administration: Source zeigt Newsroom, der Versionsverlauf hat eine Version
  mehr.
- Führen Sie den Probelauf erneut aus: `would look at 2418` und nach `--apply` `gave 0 of them`.
- Vergleichen Sie die Zahlen mit dem Laufprotokoll.

## Zurücknehmen

Diese Migration hat kein `down()`. Um zurückzugehen, stellen Sie den Sicherungspunkt des Laufs wieder her (Datenbank,
Speicher und Einstellungen wie vor dem Lauf):

```bash
./console exp:migration:rollback 20261101-131015-2c8e40            # was wiederhergestellt würde
./console exp:migration:rollback 20261101-131015-2c8e40 --apply
```

Planen Sie den Lauf auf Produktion so, dass in der Zeit nichts anderes veröffentlicht wird, sonst nimmt die
Wiederherstellung auch diese Änderungen zurück; geben Sie den Kopf `maintenance: yes`, wenn Redakteure und Besucher
warten sollen.

## Nächster Schritt

Korrigieren Sie Zeilen einer Tabelle statt Inhaltsobjekten: [Daten korrigieren](daten-korrigieren.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Große Datenmigrationen](grosse-datenmigrationen.md), [Sicherungspunkte und Rücknahme in der Praxis](sicherungspunkte-und-ruecknahme-in-der-praxis.md), [Zurücknehmen und Sicherungspunkte](../../../anleitungen/migrationen-rueckgaengig-und-captions.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
