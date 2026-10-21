---
title: Warteschlangen mit expqueue
audience: app-creator, developer
category: app-creation
order: 420
summary: Anwendungsarbeit mit der Job-Warteschlange expqueue im Hintergrund ausführen - einschalten, Jobs mit Optionen einreihen, Handler mit Wiederholungen und Checkpoints schreiben, Worker per Cron oder systemd und die Seiten in der Administration.
keywords: Warteschlange, Job-Warteschlange, Hintergrund-Jobs, einreihen, Handler, Wiederholung, Idempotenzschlüssel, Worker, Priorität, Anwendungsentwickler, expqueue, job queue, background jobs, enqueue, handler, expQueueHandler, expQueueResult, retry, idempotency key, worker, cron, systemd, priority, app creator
type: help
---
# Warteschlangen mit expqueue

Diese Seite richtet sich an Anwendungsentwickler, deren Anwendung Arbeit hat, die nicht in einer Web-Anfrage stattfinden
darf: Aufrufe langsamer Dienste, Massenaktualisierungen, Versand, KI-Anfragen. `expqueue` ist eine Job-Warteschlange in
der Datenbank: Erzeuger reihen einen Job mit einem einzigen Aufruf ein; Worker führen ihn auf der Kommandozeile aus
([expqueue](../../../../eng-us/features/6.0/extensions/expqueue.md)). Die vollständige Dokumentation steht in
`extension/expqueue/doc/` (`api.md`, `scheduling.md`, `workers-cron-systemd.md`).

![Die Job-Warteschlange in der Administration: wartende, laufende, erledigte und fehlgeschlagene Jobs je Warteschlange](../../../../eng-us/help/images/creators/job-queue-dashboard.webp)

## Inhalt

1. [Einschalten](#einschalten)
2. [Einen Job einreihen](#einen-job-einreihen)
3. [Einen Handler schreiben](#einen-handler-schreiben)
4. [Worker](#worker)
5. [Beobachten und steuern](#beobachten-und-steuern)
6. [Regeln für Handler](#regeln-für-handler)

## Einschalten

1. Aktivieren Sie die Erweiterung global: `[ExtensionSettings] ActiveExtensions[]=expqueue` in
   `settings/override/site.ini.append.php`.
2. Legen Sie die Tabellen an (idempotent): `./console ext:expqueue:install-schema`.
3. Generieren Sie die Autoloads neu und liefern Sie aus.
4. Ein Worker jede Minute:

   ```text
   * * * * * cd /path/to/installation && php runcronjobs.php -q expqueue >> var/log/cronjob-expqueue.log 2>&1
   ```

5. Prüfen: `./console ext:expqueue:enqueue expqueue.noop`, `./console ext:expqueue:work --once`,
   `./console ext:expqueue:status`.

## Einen Job einreihen

```php
$job = expQueue::enqueue( 'expdemo.archive', array( 'object_id' => $objectID ), array(
    'idempotency_key' => "archive:$objectID:$version",
    'priority' => 50 ) );
```

Zu den Optionen gehören `class` (Prioritätsklasse), `priority`, `group`, `idempotency_key`, `resource` (ein Schlüssel für
Nebenläufigkeit), `not_before`, `max_attempts`, `timeout`, `depends_on`, `user_id`. Die Nutzdaten werden als JSON
gespeichert und in der Administration angezeigt: **Legen Sie niemals ein Geheimnis hinein**; übergeben Sie IDs und lassen
Sie den Handler Zugangsdaten aus den Einstellungen lesen.

## Einen Handler schreiben

Registrieren Sie den Typ:

```ini
# extension/expdemo/settings/expqueue.ini.append.php
[JobType_expdemo.archive]
Handler=expDemoArchiveHandler
MaxAttempts=4
Timeout=300
Description=Sends one published object to the archive
```

Implementieren Sie `expQueueHandler::run( expQueueJob $job, expQueueContext $context )` oder erweitern Sie
`expQueueHandlerBase` (das `validate()` beim Einreihen und `describe()` für die Administration ergänzt). `run()` gibt
nichts oder ein Array zurück (erledigt) oder ein `expQueueResult`: `done()`, `retry( $message, $retryAfter )`, `fail()`,
`defer()`, `hold()`, `cancelled()`. Andere Exceptions gelten als wiederholbar.

```php
class expDemoArchiveHandler extends expQueueHandlerBase
{
    public function run( expQueueJob $job, expQueueContext $context )
    {
        $payload = $context->payload();
        $object = eZContentObject::fetch( (int)$payload['object_id'] );
        if ( !$object )
            return expQueueResult::fail( 'The object is gone' );
        expDemoArchiveClient::send( $object );
        return array( 'sent' => true );
    }
}
```

## Worker

Worker laufen per Cron (`runcronjobs.php expqueue`) oder für kürzere Wartezeiten als systemd-Dienst
(`./console ext:expqueue:service install`), niemals innerhalb einer Web-Anfrage. Wenn Ihre Job-Typen nur in einigen
Siteaccesses aktiv sind, geben Sie dem Worker `-s <siteaccess>` mit.

## Beobachten und steuern

**Setup > Job-Warteschlange** zeigt das Dashboard, die Jobs, Gruppen und Steuerelemente; die Richtlinien sind
`expqueue/view` und `expqueue/manage`. Jobs lassen sich abbrechen, wiederholen und je Warteschlange, Klasse, Ressource
oder Gruppe pausieren.

## Regeln für Handler

- Idempotent: Ein Job kann nach einem Absturz oder einer Wiederholung erneut laufen; verwenden Sie den Checkpoint für
  Schritte, die Geld kosten.
- Keine Datenbanktransaktion um einen langsamen Aufruf.
- Bei langer Arbeit mindestens alle halbe Timeout-Dauer `progress()` oder `heartbeat()` aufrufen.
- Der Job läuft als der Benutzer, der ihn eingereiht hat; prüfen Sie die Rechte als dieser Benutzer.

## Verwandte Seiten

- [Muster für Hintergrundarbeit](muster-fuer-hintergrundarbeit.md)
- [Inhalts-Jobs](inhalts-jobs.md)
- [Exponential Intelligence](exponential-intelligence.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
