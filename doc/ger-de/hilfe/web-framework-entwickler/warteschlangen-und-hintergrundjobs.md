---
title: Warteschlangen und Hintergrundjobs
audience: web-framework-developer, developer
category: web-frameworks
order: 320
summary: Hintergrundarbeit in Exponential für Entwickler, die Symfony Messenger, Laravel-Queues, Celery, Sidekiq oder @Async von Spring gewohnt sind - die Job-Warteschlange expqueue mit Handlern, Wiederholungen, Idempotenzschlüsseln und Workern, Inhaltsjobs für große Inhaltsoperationen, Cronjob-Teile und auf Cron verschobene Workflow-Ereignisse sowie eine Entscheidungstabelle, welches Mittel Sie wann verwenden.
keywords: Warteschlange, Jobs, Hintergrundjobs, Worker, expqueue, expQueue::enqueue, expQueueHandlerBase, expQueueResult, Wiederholung, Idempotenzschlüssel, Inhaltsjobs, Messenger, Laravel-Queues, Celery, Sidekiq, Active Job, Web-Framework-Entwickler, queue, background jobs, retry, idempotency key, content jobs, web framework developer
type: help
---
# Warteschlangen und Hintergrundjobs

Diese Seite richtet sich an Entwickler, die Nachrichten oder Jobs an eine Warteschlange übergeben (Symfony Messenger,
Laravel-Queues, Celery, Sidekiq, Active Job, Spring `@Async`). Die Job-Warteschlange von Exponential ist die
Erweiterung **`expqueue`**: datenbankgestützt, mit Handlern, Wiederholungen, Prioritäten und Workern auf der
Kommandozeile. Zwei weitere Mechanismen decken Arbeit des Kernels ab: **Inhaltsjobs** für sehr große
Inhaltsoperationen und **auf Cron verschobene Workflow-Ereignisse**
([Hintergrundarbeit: Warteschlangen, Cronjobs und Inhaltsjobs](../ersteller/anwendungsentwickler/muster-fuer-hintergrundarbeit.md)).

## Inhalt

1. [Welcher Mechanismus](#welcher-mechanismus)
2. [expqueue in fünf Schritten](#expqueue-in-fünf-schritten)
3. [Ein Handler](#ein-handler)
4. [Inhaltsjobs](#inhaltsjobs)
5. [Regeln für Jobs](#regeln-für-jobs)
6. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Welcher Mechanismus

| Die Arbeit | Ort |
|---|---|
| Nach jedem Veröffentlichen einen externen Dienst aufrufen | ein Workflow-Ereignis oder Listener stellt einen expqueue-Job in die Warteschlange |
| Morgen um 9 Uhr eine Erinnerungs-E-Mail senden | ein expqueue-Job mit `not_before` |
| Stündlich einen Feed importieren | ein Cronjob-Teil ([Geplante Aufgaben](geplante-aufgaben-und-cronjobs.md)) |
| Das Veröffentlichen anhalten, bis jemand freigibt | ein auf Cron verschobenes Workflow-Ereignis ([Workflows](workflows-trigger-und-domain-events.md)) |
| Einen Teilbaum mit 50 000 Knoten entfernen oder verschieben | ein Inhaltsjob |

## expqueue in fünf Schritten

Aus [Warteschlangen mit expqueue](../ersteller/anwendungsentwickler/warteschlangen-mit-expqueue.md):

1. Aktivieren Sie die Erweiterung: `[ExtensionSettings] ActiveExtensions[]=expqueue` in `settings/override/site.ini.append.php`.
2. Legen Sie ihre Tabellen an (idempotent): `./console ext:expqueue:install-schema`.
3. Erzeugen Sie die Autoloads neu und deployen Sie.
4. Starten Sie jede Minute einen Worker per Cron (`php runcronjobs.php -q expqueue`) oder betreiben Sie ihn als
   systemd-Dienst (`./console ext:expqueue:service install`) für kürzere Wartezeiten.
5. Prüfen Sie: `./console ext:expqueue:enqueue expqueue.noop`, `./console ext:expqueue:work --once`,
   `./console ext:expqueue:status`.

Aus beliebigem Code in die Warteschlange stellen:

```php
$job = expQueue::enqueue( 'expdemo.archive', array( 'object_id' => $objectID ), array(
    'idempotency_key' => "archive:$objectID:$version",
    'priority' => 50 ) );
```

Zu den Optionen gehören `class`, `priority`, `group`, `idempotency_key`, `resource` (ein Nebenläufigkeitsschlüssel),
`not_before`, `max_attempts`, `timeout`, `depends_on` und `user_id`. Die Nutzdaten werden als JSON gespeichert und in
der Administrationsoberfläche angezeigt: Legen Sie niemals ein Geheimnis darin ab.

## Ein Handler

```ini
# extension/expdemo/settings/expqueue.ini.append.php
[JobType_expdemo.archive]
Handler=expDemoArchiveHandler
MaxAttempts=4
Timeout=300
Description=Sends one published object to the archive
```

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

`run()` gibt nichts oder ein Array zurück (erledigt) oder ein `expQueueResult`: `done()`,
`retry( $message, $retryAfter )`, `fail()`, `defer()`, `hold()`, `cancelled()`; andere Exceptions gelten als
wiederholbar. **Setup > Job queue** zeigt das Dashboard und lässt Administratoren Jobs abbrechen, wiederholen
und pausieren; die Richtlinien sind `expqueue/view` und `expqueue/manage`
([expqueue](../../funktionen/6.0/erweiterungen/expqueue.md)).

## Inhaltsjobs

Das Entfernen, Kopieren, Verschieben, Verbergen oder Einblenden großer Teilbäume, das Ändern von Sektionen oder
Zuständen sowie das Hinzufügen oder Entfernen von Platzierungen kann als **Inhaltsjob** in Stapeln laufen, jeder
Stapel in einer eigenen Transaktion, gestartet aus der Administrationsoberfläche oder von der Kommandozeile und mit
Fortschrittsanzeige verfolgbar ([Inhaltsjobs](../../funktionen/6.0/inhaltsauftraege.md),
[Inhaltsjobs für Anwendungen](../ersteller/anwendungsentwickler/inhalts-jobs.md)).

## Regeln für Jobs

- Idempotent: Ein Job kann nach einem Absturz oder einer Wiederholung erneut laufen.
- Keine Datenbanktransaktion um einen langsamen Aufruf herum.
- Ein Job läuft als der Benutzer, der ihn eingereiht hat; prüfen Sie Berechtigungen als dieser Benutzer.
- Worker laufen auf der Kommandozeile, niemals innerhalb eines Web-Requests.

## Im Vergleich mit anderen Frameworks

| Framework | Konzept | Exponential |
|---|---|---|
| Symfony Messenger | Nachrichten, Handler, Transports | Jobtypen, Handler, ein Datenbank-Transport |
| Laravel | Jobs, `dispatch()`, Horizon | `expQueue::enqueue()`, **Setup > Job queue** |
| Celery, Sidekiq | Broker (Redis, RabbitMQ), Worker | die Datenbank; Worker per Cron oder systemd |
| Spring | `@Async`, Task Executors | keine prozessinterne Asynchronität; stellen Sie die Arbeit in die Warteschlange |

Es muss kein Broker betrieben werden: Die Warteschlange liegt in der Datenbank der Installation. Das hält
Installationen einfach, kostet aber Durchsatz im Vergleich zu einem dedizierten Broker.

## Verwandte Seiten

- [Hintergrundarbeit: Warteschlangen, Cronjobs und Inhaltsjobs](../ersteller/anwendungsentwickler/muster-fuer-hintergrundarbeit.md)
- [Exponential Intelligence für Anwendungen](../ersteller/anwendungsentwickler/exponential-intelligence.md)
- [Ereignisse und Listener](ereignisse-und-listener.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
