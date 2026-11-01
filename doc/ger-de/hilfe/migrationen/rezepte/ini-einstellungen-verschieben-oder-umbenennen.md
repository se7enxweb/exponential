---
title: "INI-Einstellungen verschieben oder umbenennen"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 7
summary: "Eine PHP-Migration, die Einstellungen aus dem Override einer Installation von site.ini in eine eigene Datei verschiebt und dabei eine umbenennt, den Wert jeder Installation behält, die neue Datei vor dem Entfernen des alten Eintrags schreibt und sie in down() zurückverschiebt."
keywords: Migration, Migrationen, INI, Einstellungen, Einstellung verschieben, Einstellung umbenennen, Konfigurationsänderung, migration, migrations, settings, move setting, rename setting, settings/override, site.ini.append.php, eZINI, setVariable, removeSetting, save, resetAllInstances, exp:migration:migrate
type: help
---
# INI-Einstellungen verschieben oder umbenennen

Dieses Rezept richtet sich an Entwickler, die Einstellungen neu ordnen: Ein Block wandert aus `site.ini` in eine
eigene Datei, eine Einstellung bekommt einen klareren Namen. Der Code liest nach der Änderung die neue Stelle, aber
jede Installation hält ihre eigenen Werte in `settings/override/`, und jede ist anders (eine Seitengröße auf Staging,
eine andere auf Produktion). Eine Migration verschiebt den Wert, den jede Installation hat, im selben Release wie der
Code, der die neue Stelle liest. Das Beispiel verschiebt `site.ini [NewsSettings]` nach `news.ini [ListSettings]` und
benennt `SourceLimit` in `PageLimit` um.

Migrationsdateien vom Typ `.ini` werden von der Engine gefunden und geprüft, aber der Runner von 6.0.15 führt sie
noch nicht aus; deshalb ist diese Änderung in PHP geschrieben.

## Was Sie am Ende haben

- die Migration `20261101150000_move_news_settings.php`;
- auf jeder Installation die Werte, die in `settings/override/site.ini.append.php [NewsSettings]` standen, in
  `settings/override/news.ini.append.php [ListSettings]` und nicht mehr in `site.ini.append.php`;
- ein `down()`, das sie zurückverschiebt.

## Bevor Sie beginnen

- Exponential 6.0.15 mit erledigtem `6.0.15-migration-tables`.
- Der Code, der `news.ini [ListSettings]` liest, ist im selben Commit, und die Erweiterung oder Site liefert eine
  `news.ini` mit den Vorgaben (eine Migration verschiebt die Werte der Installation, nie die ausgelieferten Vorgaben).
- Die Einstellungen der Installation werden nicht von der Auslieferung verwaltet (`site.ini
  [ConfigurationSettings] ManagedByDeployment`): Ist das so, ändern Sie die Dateien der Auslieferung, statt diese
  Migration auszuführen.
- Ausführen als der Benutzer, dem `settings/override/` gehört (als root mit `--allow-root-user`).

## Schritte

### 1. Erzeugen und füllen

```bash
./console exp:migration:generate --skeleton move_news_settings --description="The news settings move from site.ini [NewsSettings] to news.ini [ListSettings] (SourceLimit -> PageLimit)" --apply
```

### 2. Probelauf

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-150144-b8e0d1, environment production, database mysql (transactions: data)

  would-apply 20261101150000_move_news_settings            site               The news settings move from site.ini [NewsSettings] to news.ini [ListSettings] (SourceLimit -> PageLimit)
              would move site.ini [NewsSettings/SourceLimit] = 25 to news.ini [ListSettings/PageLimit]
              would move site.ini [NewsSettings/ShowSource] = enabled to news.ini [ListSettings/ShowSource]

Captions: pre would-take, post would-take
Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

Auf einer Installation, die `[NewsSettings]` nie gesetzt hat, sagt der Plan `nothing: settings/override/site.ini.append.php
has no [NewsSettings] to move`, und der Lauf zeichnet die Migration ohne Änderung als erledigt auf.

### 3. Ausführen und ausliefern

```bash
./console exp:migration:migrate --apply
./console exp:velocity deploy
```

```text
  applied     20261101150000_move_news_settings            site               The news settings move from site.ini [NewsSettings] to news.ini [ListSettings] (SourceLimit -> PageLimit)
              moved [NewsSettings/SourceLimit] to news.ini [ListSettings/PageLimit]
              moved [NewsSettings/ShowSource] to news.ini [ListSettings/ShowSource]
```

Die Einstellungen sind zwischengespeichert: Die Auslieferung leert den INI-Cache und lädt PHP-FPM und Velocity neu,
damit Webanfragen die neue Datei lesen. Nur den INI-Cache leeren Sie mit `php bin/php/ezcache.php --clear-tag=ini`.

## Die Migration

```php
<?php
// description: The news settings move from site.ini [NewsSettings] to news.ini [ListSettings] (SourceLimit -> PageLimit)
// author: Site team
namespace Site\Migrations;

final class MoveNewsSettings20261101150000 extends \expMigrationPhp
{
    const DIRECTORY = 'settings/override';
    /** old block and name => new block and name */
    const MOVES = array(
        'NewsSettings/SourceLimit' => 'ListSettings/PageLimit',
        'NewsSettings/ShowSource' => 'ListSettings/ShowSource',
    );

    /** @return \eZINI an override file, read and written directly (no merged settings, no cache) */
    private static function override( $name )
    {
        return \eZINI::instance( $name, self::DIRECTORY, null, false, null, true );
    }

    /** @return array[] the settings that are still in the old place: array( old, new, value ) */
    private static function pending()
    {
        $site = self::override( 'site.ini.append.php' );
        $pending = array();
        foreach ( self::MOVES as $old => $new )
        {
            list( $block, $name ) = explode( '/', $old );
            if ( $site->hasVariable( $block, $name ) )
                $pending[] = array( $old, $new, $site->variable( $block, $name ) );
        }
        return $pending;
    }

    public function plan( $context )
    {
        $lines = array();
        foreach ( self::pending() as $move )
            $lines[] = "move site.ini [{$move[0]}] = {$move[2]} to news.ini [{$move[1]}]";
        return $lines ? $lines : array( 'nothing: settings/override/site.ini.append.php has no [NewsSettings] to move' );
    }

    public function up( $context )
    {
        $pending = self::pending();
        if ( !$pending )
            return array( 'nothing to move' );
        $site = self::override( 'site.ini.append.php' );
        $news = self::override( 'news.ini.append.php' );
        $done = array();
        foreach ( $pending as $move )
        {
            list( $oldBlock, $oldName ) = explode( '/', $move[0] );
            list( $newBlock, $newName ) = explode( '/', $move[1] );
            $news->setVariable( $newBlock, $newName, $move[2] );
            $site->removeSetting( $oldBlock, $oldName );
            $done[] = "moved [{$move[0]}] to news.ini [{$move[1]}]";
        }
        // the new file first: a failure between the two saves leaves the value in both places, never in none
        if ( !$news->save() || !$site->save() )
            throw new \RuntimeException( 'settings/override could not be written' );
        \eZINI::resetAllInstances();
        return $done;
    }

    public function down( $context )
    {
        $site = self::override( 'site.ini.append.php' );
        $news = self::override( 'news.ini.append.php' );
        $done = array();
        foreach ( self::MOVES as $old => $new )
        {
            list( $oldBlock, $oldName ) = explode( '/', $old );
            list( $newBlock, $newName ) = explode( '/', $new );
            if ( !$news->hasVariable( $newBlock, $newName ) )
                continue;
            $site->setVariable( $oldBlock, $oldName, $news->variable( $newBlock, $newName ) );
            $news->removeSetting( $newBlock, $newName );
            $done[] = "moved news.ini [$new] back to site.ini [$old]";
        }
        if ( !$site->save() || !$news->save() )
            throw new \RuntimeException( 'settings/override could not be written' );
        \eZINI::resetAllInstances();
        return $done ? $done : array( 'nothing to move back' );
    }
}
```

- `eZINI::instance( <datei>, 'settings/override', null, false, null, true )` liest genau eine Datei: nicht die
  zusammengeführten Einstellungen aller Verzeichnisse und nicht den Cache. Genau das soll die Migration verschieben.
- Die neue Datei wird gespeichert, bevor der alte Eintrag entfernt wird; ein Fehler lässt den Wert so an beiden
  Stellen, nie an keiner.
- `settings/override/` gehört zu jedem Sicherungspunkt (`[CaptionSettings] RestoreParts[]=settings`), daher stellt
  ein gescheiterter Lauf die Dateien wieder her, wie sie waren.
- Nicht committete Änderungen in `settings/` halten einen Lauf an (`settings` steht in `[CaptionSettings]
  CodeTreeList[]`), aber `settings/override/` liegt meist nicht in Git; die Dateien, die die Migration dort schreibt,
  sind kein Grund, den nächsten Lauf abzulehnen.
- Eine Einstellung mit einem Geheimnis (ein Passwort, ein Token) gehört gar nicht in eine INI-Datei; nutzen Sie den
  Geheimnisspeicher.

## Prüfen

- `cat settings/override/news.ini.append.php` zeigt `[ListSettings]` mit den verschobenen Werten;
  `site.ini.append.php` hat kein `[NewsSettings]` mehr.
- Setup > Ini-Einstellungen zeigt `news.ini` mit den zusammengeführten Werten von der neuen Stelle.
- Die News-Liste der Site nutzt die Seitengröße dieser Installation.

## Zurücknehmen

```bash
./console exp:migration:rollback 20261101150000_move_news_settings --apply
./console exp:velocity deploy
```

```text
  undone      20261101150000_move_news_settings            site               The news settings move from site.ini [NewsSettings] to news.ini [ListSettings] (SourceLimit -> PageLimit)
              moved news.ini [ListSettings/PageLimit] back to site.ini [NewsSettings/SourceLimit]
              moved news.ini [ListSettings/ShowSource] back to site.ini [NewsSettings/ShowSource]
```

Nehmen Sie gleichzeitig den Code zurück, der die neue Stelle liest; sonst fällt die Site auf die ausgelieferten
Vorgaben zurück.

## Nächster Schritt

Ändern Sie auf dieselbe Weise, wer was darf: [Rollen und Richtlinien hinzufügen](rollen-und-richtlinien-hinzufuegen.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Migrationen je Umgebung](migrationen-je-umgebung.md), [migration.ini [CaptionSettings]](../../../beispiele/ini-einstellungen/migration/CaptionSettings.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
