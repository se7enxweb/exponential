---
title: Writing an export or an outbound sync
audience: integrator, developer
category: integration
order: 310
summary: Getting content out of Exponential into another system - a full export of a subtree page by page, an outbox sender that delivers changes with retries, the payload, HTTP calls with timeouts, signing, and marking what was delivered.
keywords: export, outbound sync, outbox, sender, cronjob, subTreeByNodeID, Limit, Offset, ClassFilterType, payload, JSON, HTTP client, curl, timeout, retry, backoff, HMAC signature, delivery, at least once
type: help
---
# Writing an export or an outbound sync

This page is for integration developers who send content from Exponential to a search service, a CRM, an archive or a
data warehouse. Two jobs usually come together: a **full export** that loads or reloads everything, and an **outbound
sync** that sends each change afterwards. Both run on the server as cronjob parts or commands.

## Contents

1. [A full export, page by page](#a-full-export-page-by-page)
2. [The outbox sender](#the-outbox-sender)
3. [The payload](#the-payload)
4. [Calling the other system](#calling-the-other-system)
5. [Ready-made alternatives](#ready-made-alternatives)

## A full export, page by page

Read the subtree in pages with `eZContentObjectTreeNode::subTreeByNodeID()`; never load the whole tree at once.

```php
<?php
/** Writes every product below $rootNodeID as one JSON line each. Run as a service account with content/read. */
function myIntegrationExportProducts( $rootNodeID, $outFile )
{
    $out = fopen( $outFile, 'w' );
    $params = array( 'ClassFilterType' => 'include',
                     'ClassFilterArray' => array( 'product' ),
                     'SortBy' => array( 'node_id', true ),
                     'Limit' => 200,
                     'LoadDataMap' => true );
    $offset = 0;
    do
    {
        $params['Offset'] = $offset;
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params, $rootNodeID );
        foreach ( (array)$nodes as $node )
        {
            fwrite( $out, json_encode( myIntegrationPayload( $node->object() ) ) . "\n" );
        }
        $offset += 200;
        eZContentObject::clearCache();
    }
    while ( is_array( $nodes ) && count( $nodes ) === 200 );
    fclose( $out );
}
```

`subTreeByNodeID()` applies the current user's read rights, so the export contains what the service account may read;
that is how you keep unpublished or restricted content out of another system. `subTreeCountByNodeID()` gives the total
for a progress line.

## The outbox sender

The workflow event or event listener has written changed object ids into your outbox table
([Workflows and triggers](workflows-and-triggers-as-hooks.md#the-outbox-pattern-notice-fast-send-later)). A frequent
cronjob part sends them:

```php
<?php
/** Sends up to 100 pending rows; a failed row stays pending and is retried with a growing delay. */
function myIntegrationSendOutbox( myIntegrationHubClient $client )
{
    $db = eZDB::instance();
    $now = time();
    $rows = $db->arrayQuery( 'SELECT id, object_id, attempts FROM myintegration_outbox'
                           . ' WHERE sent = 0 AND next_try <= ' . $now . ' ORDER BY id', array( 'limit' => 100 ) );
    foreach ( $rows as $row )
    {
        $object = eZContentObject::fetch( (int)$row['object_id'] );
        $payload = $object instanceof eZContentObject
            ? myIntegrationPayload( $object )
            : array( 'remote_id' => null, 'object_id' => (int)$row['object_id'], 'deleted' => true );
        if ( $client->send( $payload, 'outbox-' . (int)$row['id'] ) )
        {
            $db->query( 'UPDATE myintegration_outbox SET sent = ' . $now . ' WHERE id = ' . (int)$row['id'] );
        }
        else
        {
            $attempts = (int)$row['attempts'] + 1;
            $delay = min( 3600, 60 * ( 2 ** $attempts ) );   // 2, 4, 8 ... minutes, at most an hour
            $db->query( 'UPDATE myintegration_outbox SET attempts = ' . $attempts . ', next_try = ' . ( $now + $delay )
                      . ' WHERE id = ' . (int)$row['id'] );
            eZDebug::writeWarning( "Outbox row {$row['id']} failed (attempt $attempts)", __METHOD__ );
        }
    }
}
```

The second argument of `send()` is an **idempotency key**: the receiver ignores a key it has already processed, so a
row that was delivered but not marked sent (a crash between the two) causes no duplicate. After a number of attempts,
move a row to a "dead" state and alert, rather than retrying forever.

## The payload

Send what the receiver needs, keyed by the remote id, and nothing more:

```php
<?php
function myIntegrationPayload( eZContentObject $object )
{
    $dataMap = $object->dataMap();
    $node = $object->mainNode();
    return array(
        'remote_id'  => $object->attribute( 'remote_id' ),
        'object_id'  => (int)$object->attribute( 'id' ),
        'class'      => $object->attribute( 'class_identifier' ),
        'name'       => $object->attribute( 'name' ),
        'modified'   => (int)$object->attribute( 'modified' ),
        'url'        => $node ? $node->urlAlias() : null,
        'sku'        => isset( $dataMap['sku'] ) ? $dataMap['sku']->toString() : null,
    );
}
```

Turn `url` into an absolute address on the receiving side, or with `eZURI::transformURI( $url, false, 'full', false )`,
which changes `$url` in place (the last argument switches HTML escaping off, which a JSON payload does not want).
Leave out personal data unless the receiver is allowed to hold it.

The outbox table of this example has the columns `id`, `object_id`, `version`, `created`, `sent` (0 until delivered),
`attempts` and `next_try`; define it in your extension's schema.

## Calling the other system

- Use timeouts (connect 5 seconds, total 10 to 30 seconds) on every call; a hanging call blocks the whole part.
- Treat `2xx` as delivered, `4xx` (except `408` and `429`) as a permanent error for that row, everything else as
  retryable; honor `Retry-After`.
- Sign requests when the receiver supports it, for example an HMAC of timestamp and body as the audit webhook does
  ([Outgoing webhooks](outgoing-webhooks-from-the-audit-trail.md#checking-the-signature-on-the-receiving-side)).
- Keep the endpoint and its credentials in `settings/override/myintegration.ini.append.php`.
- Log one summary line per run: sent, failed, pending.

## Ready-made alternatives

| Need | Without code |
|---|---|
| A file to SFTP, S3, WebDAV or an HTTP endpoint on a schedule, delta or full | `xrowextract` schedules ([page](bulk-data-exchange-with-xrowextract.md)) |
| Lists of the newest items | RSS or Atom exports ([page](rss-atom-and-opml-feeds.md)) |
| Event records with a signature | The audit webhook sink ([page](outgoing-webhooks-from-the-audit-trail.md)) |
| A subtree to another Exponential installation | `syndication` ([page](syndication-between-installations.md)) |
| One CSV file per class | `php bin/php/ezcsvexport.php --storage-dir=<dir> <node>` |

## Related pages

- [Integration patterns](integration-patterns-push-pull-batch-real-time.md)
- [Cronjobs for integrations](cronjobs-for-integrations.md)
- [eZContentObjectTreeNode](../extension-development/part-8-reference/ezcontentobjecttreenode.md)
- [Error handling and logging](error-handling-and-logging.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
