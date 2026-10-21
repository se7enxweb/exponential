---
title: Writing an import script
audience: integrator, developer
category: integration
order: 300
summary: A complete, worked import for Exponential - reading a CSV export of an ERP, running as a service account, create-or-update by remote_id with change detection, a dry run, batches and memory, a safety stop, logging and a summary, and how the same class runs from a command, a cronjob part and a test.
keywords: import, import script, CSV, ERP, PIM, eZContentFunctions, createAndPublishObject, updateAndPublishObject, remote_id, dry run, service account, batch, memory, clearCache, eZLog, summary, safety stop, worked example
type: help
---
# Writing an import script

This page is for integration developers. It builds a complete import step by step: a nightly CSV export of an ERP
becomes product objects. The pieces come from the pages before it; here they are put together in one class that a
command, a cronjob part and a test can all call.

## Contents

1. [The task](#the-task)
2. [The class](#the-class)
3. [Running it](#running-it)
4. [What makes it production-ready](#what-makes-it-production-ready)
5. [Variations](#variations)

## The task

- Input: `articles.csv`, UTF-8, header line, columns `ARTNR;NAME;PRICE;STOCK`.
- Output: one object of the class `product` per article below a node set in `myintegration.ini`, with the remote id
  `erp-product-<ARTNR>`.
- Unchanged articles make no new version; articles missing from the file are hidden; nothing is mailed to
  subscribers.
- A dry run reports what would change; the run ends with a summary line in `var/log/myintegration.log`.

`extension/myintegration/settings/myintegration.ini.append.php` (your extension's own settings file):

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

## The class

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

## Running it

The command (see [CLI scripts and ezexec](cli-scripts-and-ezexec.md#a-command-in-your-extension)) and the cronjob part
(see [Cronjobs for integrations](cronjobs-for-integrations.md#the-script)) both create the class and call `run()`:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/console ext:myintegration:importproducts --file=var/import/articles.csv --dry-run -s <siteaccess>
php bin/php/console ext:myintegration:importproducts --file=var/import/articles.csv -s <siteaccess>
tail -n 3 var/log/myintegration.log
```

A first dry run on a copy of production data shows the numbers to expect; a second real run right after the first
must report only `unchanged`. That second run is the test of idempotency. If it reports `updated`, a datatype's
`toString()` writes the value differently from what you send (number formatting of a price, for example): normalize
the source value to that format before comparing.

## What makes it production-ready

| Property | Where in the class |
|---|---|
| Runs as a service account, not as administrator or anonymous | `signInServiceAccount()` |
| Validates every row before using it | `readRows()` |
| Refuses a truncated source | `safeSize()` |
| Finds before it creates; no new version for unchanged data | `upsert()` |
| Publishes without notification | the `false` argument of both content functions |
| Keeps memory flat | `eZContentObject::clearCache()` every 100 records |
| Handles removals by policy | `hideMissing()` |
| Leaves a trace and an exit status | `finish()`, `eZLog::write()`, the return value |

Add for a real project: a lock against overlapping runs, a time budget, the skipped rows in the log, and an alert
when the summary line is missing ([Monitoring integrations](monitoring-integrations.md)).

## Variations

- **Languages**: pass `'language' => 'ger-DE'` to the content functions for each further language.
- **Images**: download to `var/import/` first and give the path; compare a checksum before downloading again.
- **Very large files**: stream rows instead of reading them all, and keep the seen remote ids in a table of your own.
- **Queue per record**: queue one `expqueue` job per changed record when each needs remote calls
  ([AI providers, the job queue and MCP](ai-job-queue-and-mcp.md#expqueue-a-job-queue-for-integrations)).

## Related pages

- [Idempotent imports keyed by remote_id](idempotent-imports-keyed-by-remote-id.md)
- [Mapping content classes to external data](mapping-content-classes-to-external-data.md)
- [Creating content in code for app creators](../creators/app-creators/creating-content-in-code.md)
- [Testing integrations](testing-integrations.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
