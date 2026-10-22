---
title: Einen Export oder ausgehenden Abgleich schreiben
audience: integrator, developer
category: integration
order: 310
summary: Inhalte aus Exponential in ein anderes System bringen - ein vollständiger Export eines Teilbaums Seite für Seite, ein Outbox-Sender, der Änderungen mit Wiederholungen zustellt, die Nutzlast, HTTP-Aufrufe mit Timeouts, Signieren und das Markieren des Zugestellten.
keywords: Export, ausgehender Abgleich, Outbox, Sender, Cronjob, Nutzlast, HTTP-Client, Timeout, Wiederholung, Backoff, HMAC-Signatur, Zustellung, mindestens einmal, export, outbound sync, outbox, sender, cronjob, subTreeByNodeID, Limit, Offset, ClassFilterType, payload, JSON, HTTP client, curl, timeout, retry, backoff, HMAC signature, delivery, at least once
type: help
---
# Einen Export oder ausgehenden Abgleich schreiben

Diese Seite richtet sich an Entwickler von Integrationen, die Inhalte aus Exponential an einen Suchdienst, ein CRM,
ein Archiv oder ein Data Warehouse senden. Meist kommen zwei Aufgaben zusammen: ein **vollständiger Export**, der alles
lädt oder neu lädt, und ein **ausgehender Abgleich**, der danach jede Änderung sendet. Beide laufen auf dem Server als
Cronjob-Teile oder Befehle.

## Inhalt

1. [Ein vollständiger Export, Seite für Seite](#ein-vollständiger-export-seite-für-seite)
2. [Der Outbox-Sender](#der-outbox-sender)
3. [Die Nutzlast](#die-nutzlast)
4. [Das andere System aufrufen](#das-andere-system-aufrufen)
5. [Fertige Alternativen](#fertige-alternativen)

## Ein vollständiger Export, Seite für Seite

Lesen Sie den Teilbaum seitenweise mit `eZContentObjectTreeNode::subTreeByNodeID()`; laden Sie nie den ganzen Baum auf
einmal.

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

`subTreeByNodeID()` wendet die Leserechte des aktuellen Benutzers an, der Export enthält also, was das Dienstkonto
lesen darf; so halten Sie unveröffentlichte oder geschützte Inhalte aus einem anderen System heraus.
`subTreeCountByNodeID()` liefert die Gesamtzahl für eine Fortschrittszeile.

## Der Outbox-Sender

Das Workflow-Ereignis oder der Event-Listener hat geänderte Objekt-IDs in Ihre Outbox-Tabelle geschrieben
([Workflows und Trigger](workflows-und-trigger-als-hooks.md#das-outbox-muster-schnell-vermerken-später-senden)). Ein häufig
laufender Cronjob-Teil sendet sie:

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

Das zweite Argument von `send()` ist ein **Idempotenzschlüssel**: Der Empfänger ignoriert einen Schlüssel, den er schon
verarbeitet hat, sodass eine Zeile, die zugestellt, aber nicht als gesendet markiert wurde (ein Absturz zwischen
beidem), kein Duplikat erzeugt. Verschieben Sie eine Zeile nach einer Anzahl von Versuchen in einen Zustand "tot" und
geben Sie Alarm, statt endlos zu wiederholen.

## Die Nutzlast

Senden Sie, was der Empfänger braucht, geschlüsselt nach der Remote-ID, und nicht mehr:

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

Machen Sie aus `url` auf der Empfängerseite eine absolute Adresse oder mit
`eZURI::transformURI( $url, false, 'full', false )`, das `$url` an Ort und Stelle ändert (das letzte Argument schaltet
die HTML-Maskierung ab, die eine JSON-Nutzlast nicht will). Lassen Sie personenbezogene Daten weg, sofern der Empfänger
sie nicht halten darf.

Die Outbox-Tabelle dieses Beispiels hat die Spalten `id`, `object_id`, `version`, `created`, `sent` (0 bis zur
Zustellung), `attempts` und `next_try`; definieren Sie sie im Schema Ihrer Erweiterung.

## Das andere System aufrufen

- Verwenden Sie bei jedem Aufruf Timeouts (Verbindungsaufbau 5 Sekunden, insgesamt 10 bis 30 Sekunden); ein hängender
  Aufruf blockiert den ganzen Teil.
- Werten Sie `2xx` als zugestellt, `4xx` (außer `408` und `429`) als dauerhaften Fehler für diese Zeile, alles andere
  als wiederholbar; beachten Sie `Retry-After`.
- Signieren Sie Anfragen, wenn der Empfänger es unterstützt, zum Beispiel mit einem HMAC über Zeitstempel und Inhalt,
  wie es der Audit-Webhook tut
  ([Ausgehende Webhooks](ausgehende-webhooks-aus-dem-audit-trail.md#die-signatur-auf-der-empfängerseite-prüfen)).
- Halten Sie den Endpunkt und seine Zugangsdaten in `settings/override/myintegration.ini.append.php`.
- Protokollieren Sie je Lauf eine Zusammenfassungszeile: gesendet, fehlgeschlagen, ausstehend.

## Fertige Alternativen

| Bedarf | Ohne Code |
|---|---|
| Eine Datei nach Zeitplan an SFTP, S3, WebDAV oder einen HTTP-Endpunkt, als Delta oder vollständig | Zeitpläne von `xrowextract` ([Seite](massendatenaustausch-mit-xrowextract.md)) |
| Listen der neuesten Einträge | RSS- oder Atom-Exporte ([Seite](rss-atom-und-opml-feeds.md)) |
| Ereignisdatensätze mit Signatur | Das Webhook-Ziel des Audits ([Seite](ausgehende-webhooks-aus-dem-audit-trail.md)) |
| Ein Teilbaum an eine andere Exponential-Installation | `syndication` ([Seite](syndizierung-zwischen-installationen.md)) |
| Eine CSV-Datei je Klasse | `php bin/php/ezcsvexport.php --storage-dir=<dir> <node>` |

## Verwandte Seiten

- [Integrationsmuster](integrationsmuster-push-pull-batch-echtzeit.md)
- [Cronjobs für Integrationen](cronjobs-fuer-integrationen.md)
- [eZContentObjectTreeNode](../erweiterungsentwicklung/teil-8-referenz/ezcontentobjecttreenode.md)
- [Fehlerbehandlung und Protokollierung](fehlerbehandlung-und-protokollierung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
