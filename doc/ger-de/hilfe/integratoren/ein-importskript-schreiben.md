---
title: Ein Importskript schreiben
audience: integrator, developer
category: integration
order: 300
summary: Ein vollständiger, ausgearbeiteter Import für Exponential - einen CSV-Export eines ERP lesen, als Dienstkonto laufen, Anlegen oder Aktualisieren per remote_id mit Änderungserkennung, ein Probelauf, Stapel und Speicher, ein Sicherheitsstopp, Protokollierung und eine Zusammenfassung, und wie dieselbe Klasse aus einem Befehl, einem Cronjob-Teil und einem Test läuft.
keywords: Import, Importskript, Probelauf, Dienstkonto, Stapel, Speicher, Zusammenfassung, Sicherheitsstopp, ausgearbeitetes Beispiel, import, import script, CSV, ERP, PIM, eZContentFunctions, createAndPublishObject, updateAndPublishObject, remote_id, dry run, service account, batch, memory, clearCache, eZLog, summary, safety stop, worked example
type: help
---
# Ein Importskript schreiben

Diese Seite richtet sich an Entwickler von Integrationen. Sie baut Schritt für Schritt einen vollständigen Import auf:
Ein nächtlicher CSV-Export eines ERP wird zu Produktobjekten. Die Teile stammen von den vorherigen Seiten; hier werden
sie zu einer Klasse zusammengesetzt, die ein Befehl, ein Cronjob-Teil und ein Test gleichermaßen aufrufen können.

## Inhalt

1. [Die Aufgabe](#die-aufgabe)
2. [Die Klasse](#die-klasse)
3. [Ausführen](#ausführen)
4. [Was sie produktionsreif macht](#was-sie-produktionsreif-macht)
5. [Varianten](#varianten)

## Die Aufgabe

- Eingabe: `articles.csv`, UTF-8, Kopfzeile, Spalten `ARTNR;NAME;PRICE;STOCK`.
- Ausgabe: ein Objekt der Klasse `product` je Artikel unterhalb eines in `myintegration.ini` festgelegten Knotens, mit
  der Remote-ID `erp-product-<ARTNR>`.
- Unveränderte Artikel erzeugen keine neue Version; Artikel, die in der Datei fehlen, werden verborgen; Abonnenten
  erhalten keine Mail.
- Ein Probelauf meldet, was sich ändern würde; der Lauf endet mit einer Zusammenfassungszeile in
  `var/log/myintegration.log`.

`extension/myintegration/settings/myintegration.ini.append.php` (die eigene Einstellungsdatei Ihrer Erweiterung):

```php
<?php /* #?ini charset="utf-8"?

[ImportSettings]
ParentNodeID=2
ClassIdentifier=product
ServiceAccount=svc-erp-import
# stop when the file has fewer rows than this share of the products already imported
MinimumShare=0.8

*/ ?>
```

## Die Klasse

`extension/myintegration/classes/myintegrationproductimport.php`:

```php
<?php
/**
 * Imports the ERP article export into product objects, keyed by remote id erp-product-<ARTNR>.
 */
class myIntegrationProductImport
{
    const PREFIX = 'erp-product-';

    protected $cli;
    protected $dryRun;
    protected $counts = array( 'created' => 0, 'updated' => 0, 'unchanged' => 0, 'hidden' => 0, 'failed' => 0 );

    public function __construct( eZCLI $cli, $dryRun = false )
    {
        $this->cli = $cli;
        $this->dryRun = (bool)$dryRun;
    }

    /** @return bool true when the run finished without failures */
    public function run( $file )
    {
        $ini = eZINI::instance( 'myintegration.ini' );
        $parentNodeID = (int)$ini->variable( 'ImportSettings', 'ParentNodeID' );
        $classIdentifier = $ini->variable( 'ImportSettings', 'ClassIdentifier' );

        if ( !$this->signInServiceAccount( $ini->variable( 'ImportSettings', 'ServiceAccount' ) ) )
            return false;

        $rows = $this->readRows( $file );
        if ( $rows === false )
            return false;

        if ( !$this->safeSize( count( $rows ), (float)$ini->variable( 'ImportSettings', 'MinimumShare' ) ) )
            return false;

        $seen = array();
        $i = 0;
        foreach ( $rows as $row )
        {
            $remoteID = self::PREFIX . $row['ARTNR'];
            $seen[$remoteID] = true;
            $attributes = array( 'sku' => $row['ARTNR'],
                                 'name' => trim( $row['NAME'] ),
                                 'price' => $row['PRICE'] . '|1|1',
                                 'stock' => (string)(int)$row['STOCK'] );
            $result = $this->upsert( $remoteID, $parentNodeID, $classIdentifier, $attributes );
            $this->counts[$result]++;
            if ( $result === 'failed' )
                eZDebug::writeError( "Import of $remoteID failed", __METHOD__ );

            // memory: objects are cached in-process; drop the cache every 100 records
            if ( ++$i % 100 === 0 )
                eZContentObject::clearCache();
        }

        $this->hideMissing( $seen );
        return $this->finish();
    }

    protected function signInServiceAccount( $login )
    {
        $user = eZUser::fetchByName( $login );
        if ( !$user instanceof eZUser )
        {
            $this->cli->error( "Service account $login not found" );
            return false;
        }
        eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
        return true;
    }

    /** @return array|false rows as column => value */
    protected function readRows( $file )
    {
        $handle = @fopen( $file, 'r' );
        if ( !$handle )
        {
            $this->cli->error( "Cannot read $file" );
            return false;
        }
        $header = fgetcsv( $handle, 0, ';', '"', '\\' );
        $rows = array();
        while ( ( $line = fgetcsv( $handle, 0, ';', '"', '\\' ) ) !== false )
        {
            if ( count( $line ) !== count( $header ) )
                continue;                                    // logged as skipped in a real import
            $row = array_combine( $header, $line );
            if ( !preg_match( '/^[A-Za-z0-9_-]{1,60}$/', $row['ARTNR'] ) || !is_numeric( $row['PRICE'] ) )
                continue;
            $rows[] = $row;
        }
        fclose( $handle );
        return $rows;
    }

    /** Refuses to run when the file is suspiciously small compared with what is already imported */
    protected function safeSize( $rowCount, $minimumShare )
    {
        $db = eZDB::instance();
        $existing = $db->arrayQuery( "SELECT COUNT(*) AS n FROM ezcontentobject WHERE remote_id LIKE '"
                                   . $db->escapeString( self::PREFIX ) . "%' AND status = 1" );
        $existing = (int)$existing[0]['n'];
        if ( $existing > 0 && $rowCount < $existing * $minimumShare )
        {
            $this->cli->error( "The file has $rowCount rows, $existing products exist: stopped" );
            eZLog::write( "Stopped: $rowCount rows for $existing products", 'myintegration.log' );
            return false;
        }
        return true;
    }

    protected function upsert( $remoteID, $parentNodeID, $classIdentifier, array $attributes )
    {
        $object = eZContentObject::fetchByRemoteID( $remoteID );
        if ( !$object instanceof eZContentObject )
        {
            if ( $this->dryRun )
                return 'created';
            $created = eZContentFunctions::createAndPublishObject( array(
                'class_identifier' => $classIdentifier, 'parent_node_id' => $parentNodeID,
                'remote_id' => $remoteID, 'attributes' => $attributes ), false );
            return $created instanceof eZContentObject ? 'created' : 'failed';
        }
        $changed = array();
        $dataMap = $object->dataMap();
        foreach ( $attributes as $identifier => $value )
        {
            if ( !isset( $dataMap[$identifier] ) || $dataMap[$identifier]->toString() !== (string)$value )
                $changed[$identifier] = $value;
        }
        if ( !$changed )
            return 'unchanged';
        if ( $this->dryRun )
            return 'updated';
        return eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => $changed ), false )
            ? 'updated' : 'failed';
    }

    /** Hides products whose article is no longer in the file */
    protected function hideMissing( array $seen )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT remote_id FROM ezcontentobject WHERE remote_id LIKE '"
                               . $db->escapeString( self::PREFIX ) . "%' AND status = 1" );
        foreach ( $rows as $row )
        {
            if ( isset( $seen[$row['remote_id']] ) )
                continue;
            $object = eZContentObject::fetchByRemoteID( $row['remote_id'] );
            $node = $object ? $object->mainNode() : null;
            if ( !$node || $node->attribute( 'is_hidden' ) )
                continue;
            if ( !$this->dryRun )
                eZContentObjectTreeNode::hideSubTree( $node );
            $this->counts['hidden']++;
        }
    }

    protected function finish()
    {
        $summary = ( $this->dryRun ? 'Dry run: ' : '' ) . http_build_query( $this->counts, '', ', ' );
        $this->cli->output( $summary );
        eZLog::write( $summary, 'myintegration.log' );
        return $this->counts['failed'] === 0;
    }
}
```

## Ausführen

Der Befehl (siehe [CLI-Skripte und ezexec](cli-skripte-und-ezexec.md#ein-befehl-in-ihrer-erweiterung)) und der
Cronjob-Teil (siehe [Cronjobs für Integrationen](cronjobs-fuer-integrationen.md#das-skript)) legen beide die Klasse an
und rufen `run()` auf:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/console ext:myintegration:importproducts --file=var/import/articles.csv --dry-run -s <siteaccess>
php bin/php/console ext:myintegration:importproducts --file=var/import/articles.csv -s <siteaccess>
tail -n 3 var/log/myintegration.log
```

Ein erster Probelauf auf einer Kopie der Produktionsdaten zeigt die zu erwartenden Zahlen; ein zweiter echter Lauf
direkt nach dem ersten darf nur `unchanged` melden. Dieser zweite Lauf ist der Test der Idempotenz. Meldet er
`updated`, schreibt das `toString()` eines Datentyps den Wert anders, als Sie ihn senden (etwa die Zahlenformatierung
eines Preises): Normalisieren Sie den Quellwert vor dem Vergleich auf dieses Format.

## Was sie produktionsreif macht

| Eigenschaft | Wo in der Klasse |
|---|---|
| Läuft als Dienstkonto, nicht als Administrator oder anonym | `signInServiceAccount()` |
| Prüft jede Zeile vor der Verwendung | `readRows()` |
| Lehnt eine abgeschnittene Quelle ab | `safeSize()` |
| Sucht, bevor sie anlegt; keine neue Version für unveränderte Daten | `upsert()` |
| Veröffentlicht ohne Benachrichtigung | das Argument `false` beider Inhaltsfunktionen |
| Hält den Speicher flach | `eZContentObject::clearCache()` alle 100 Datensätze |
| Behandelt Entfernungen nach einer Regel | `hideMissing()` |
| Hinterlässt eine Spur und einen Exit-Status | `finish()`, `eZLog::write()`, der Rückgabewert |

Ergänzen Sie für ein echtes Projekt: eine Sperre gegen überlappende Läufe, ein Zeitbudget, die übersprungenen Zeilen im
Protokoll und einen Alarm, wenn die Zusammenfassungszeile fehlt
([Integrationen überwachen](integrationen-ueberwachen.md)).

## Varianten

- **Sprachen**: Übergeben Sie den Inhaltsfunktionen für jede weitere Sprache `'language' => 'ger-DE'`.
- **Bilder**: Laden Sie sie zuerst nach `var/import/` herunter und geben Sie den Pfad an; vergleichen Sie vor einem
  erneuten Herunterladen eine Prüfsumme.
- **Sehr große Dateien**: Lesen Sie Zeilen als Datenstrom statt alle auf einmal, und halten Sie die gesehenen
  Remote-IDs in einer eigenen Tabelle.
- **Warteschlange je Datensatz**: Reihen Sie je geändertem Datensatz einen `expqueue`-Job ein, wenn jeder entfernte
  Aufrufe braucht
  ([KI-Anbieter, die Job-Warteschlange und MCP](ki-jobwarteschlange-und-mcp.md#expqueue-eine-jobwarteschlange-für-integrationen)).

## Verwandte Seiten

- [Idempotente Importe, geschlüsselt nach remote_id](idempotente-importe-ueber-die-remote-id.md)
- [Inhaltsklassen externen Daten zuordnen](inhaltsklassen-auf-externe-daten-abbilden.md)
- [Inhalte im Code anlegen, für App-Ersteller](../ersteller/anwendungsentwickler/inhalte-im-code-anlegen.md)
- [Integrationen testen](integrationen-testen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
