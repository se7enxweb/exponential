---
title: Cronjobs für Integrationen
audience: integrator, developer, admin
category: integration
order: 160
summary: Integrationsarbeit mit den Cronjob-Teilen von Exponential planen - ein eigener Teil, das Skript als Klasse, die Crontab-Zeile pro Siteaccess, Sperren gegen überlappende Läufe, einen Teil während der Wartung anhalten und die Läufe unter Setup > Cronjobs verfolgen.
keywords: Cronjob, Cronjob-Teil, runcronjobs.php, cronjob.ini, CronjobPart, ExtensionDirectories, Crontab, Zeitplan, Sperre, Überlappung, Anhalten, Setup > Cronjobs, cron:, zeitgesteuerter Import, zeitgesteuerter Export, cronjob part, schedule, lock, overlap, pause, scheduled import, scheduled export
type: help
---
# Cronjobs für Integrationen

Diese Seite ist für Integrationsentwickler und Betreiber, die Importe, Exporte und die Abarbeitung von Warteschlangen
planen. Exponential führt geplante Arbeit über **Cronjob-Teile** aus: benannte Gruppen von Skripten, die die
System-Crontab mit `runcronjobs.php` startet. Geben Sie Ihrer Integration einen eigenen Teil, damit sie geplant,
angehalten und untersucht werden kann, ohne etwas anderes anzufassen. Die Sicht des Betreibers ist
[Cronjobs](../../anleitungen/cronjobs.md); das Kapitel für Entwickler ist
[Cronjob-Teile](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/cronjob-teile.md).

## Inhalt

1. [Ein eigener Teil](#ein-eigener-teil)
2. [Das Skript](#das-skript)
3. [Die Crontab-Zeile](#die-crontab-zeile)
4. [Überlappende Läufe und Zeitlimits](#überlappende-läufe-und-zeitlimits)
5. [Anhalten während der Wartung](#anhalten-während-der-wartung)
6. [Die Läufe verfolgen](#die-läufe-verfolgen)
7. [Die Teile des Kernels, von denen eine Integration abhängt](#die-teile-des-kernels-von-denen-eine-integration-abhängt)

## Ein eigener Teil

`extension/myintegration/settings/cronjob.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[CronjobSettings]
ExtensionDirectories[]=myintegration

[CronjobPart-myintegration_import]
Scripts[]=importproducts.php

[CronjobPart-myintegration_outbox]
Scripts[]=sendoutbox.php

*/ ?>
```

`ExtensionDirectories[]` lässt `runcronjobs.php` in `extension/myintegration/cronjobs/` suchen. Mit zwei Teilen können
Betreiber den nächtlichen Import und die häufige Outbox in unterschiedlichen Rhythmen ausführen.

## Das Skript

Die empfohlene Form ist eine Klasse mit einer einzeiligen Datei in `cronjobs/`:

```php
<?php
// extension/myintegration/classes/runnable/cronjobs/importproducts.php
namespace Exponential\Cronjob\Extension\Myintegration;

class ImportProducts extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = $scope['cli'];
        $file = \eZINI::instance( 'myintegration.ini' )->variable( 'ImportSettings', 'File' );
        $importer = new \myIntegrationProductImport( $cli, false );
        $ok = $importer->run( $file );
        \eZLog::write( $ok ? 'Import finished' : 'Import failed', 'myintegration.log' );
        if ( empty( $scope['isQuiet'] ) )
        {
            $cli->output( $ok ? 'Import finished' : 'Import failed' );
        }
    }
}
```

```php
<?php
// extension/myintegration/cronjobs/importproducts.php
return \Exponential\Cronjob\Extension\Myintegration\ImportProducts::main( __FILE__, get_defined_vars() );
```

`myintegration.ini` ist die eigene Einstellungsdatei Ihrer Erweiterung; `File` ist eine Beispieleinstellung, die Sie
dort definieren.

## Die Crontab-Zeile

Führen Sie es als Benutzer des Webservers aus, aus dem Wurzelverzeichnis der Installation, pro Siteaccess:

```cron
# nightly product import at 02:15
15 2 * * *  cd /path/to/installation && php runcronjobs.php -q -s <siteaccess> myintegration_import >> var/log/cronjob-myintegration.log 2>&1
# outbox every 5 minutes
*/5 * * * * cd /path/to/installation && php runcronjobs.php -q -s <siteaccess> myintegration_outbox >> var/log/cronjob-myintegration.log 2>&1
```

Testen Sie zuerst von Hand:

```bash
php runcronjobs.php --list | grep -A2 myintegration
php runcronjobs.php -s <siteaccess> --script=importproducts.php
php bin/php/console cron:myintegration_import -s <siteaccess>
```

Jeder Teil ist auch ein Konsolenbefehl `cron:<part>`. **Setup > Cronjobs** zeigt den genauen Befehl pro Teil und
schlägt Crontab-Zeilen vor.

## Überlappende Läufe und Zeitlimits

Ein langsamer Lauf kann den nächsten überlappen. Schützen Sie jeden Teil:

| Maßnahme | Wie |
|---|---|
| Eine Sperre | Zu Beginn eine Sperrdatei unter `var/` anlegen (zum Beispiel mit PHPs `flock()` auf eine Datei Ihrer Erweiterung) und abbrechen, wenn sie belegt ist |
| Ein Zeitbudget | Nach einer Anzahl Minuten aufhören und beim nächsten Mal an einer gespeicherten Position weitermachen |
| Stapel | In Stapeln von einigen hundert Datensätzen arbeiten und dazwischen den Objekt-Cache im Speicher leeren (`eZContentObject::clearCache()`) |
| Eine globale Grenze | `cronjob.ini [CronjobSettings] MaxScriptExecutionTime` (standardmäßig 43200 Sekunden) begrenzt jedes Skript |

## Anhalten während der Wartung

**Setup > Cronjobs** kann einen Teil (**Anhalten** auf seiner Karte) oder alle Teile anhalten, mit einer Notiz;
`runcronjobs.php` überspringt sie dann, bis jemand sie fortsetzt ([Cronjobs anhalten](../../funktionen/6.0/cronjobs-pausieren.md)).
Halten Sie die Teile Ihrer Integration vor einer Migration, einem Upgrade oder einer Wiederherstellung an und setzen
Sie sie nach der Prüfung fort. `cronjob.ini [PauseSettings] Pause=disabled` schaltet die Prüfung ab.

## Die Läufe verfolgen

- **Setup > Cronjobs** zeigt die letzten Läufe jedes Teils, wie Probleme gezählt werden, und die Ausgabe, während ein
  Job läuft.
- Mit `--logfiles` schreibt `runcronjobs.php` Protokolldateien; `cronjob.ini [AdminSettings] LogFile` und `ErrorFile`
  benennen die Dateien, die die Seite aufbewahrt.
- Die Audit-Taxonomie hat das Ereignis `system.cronjob.run` (standardmäßig festgehalten); der
  Benachrichtigungsteil des Kernels löst es aus. Ihr eigener Teil kann seine Läufe auf dieselbe Weise über die
  Audit-API festhalten ([Audit, die Entwickler-API](../../bc/6.0/audit.md#35-die-entwickler-api)).
- Schreiben Sie pro Lauf mit `eZLog::write()` eine eigene Zusammenfassungszeile und lösen Sie einen Alarm aus, wenn sie
  fehlt ([Integrationen überwachen](integrationen-ueberwachen.md)).

## Die Teile des Kernels, von denen eine Integration abhängt

| Teil | Skripte | Warum er zählt |
|---|---|---|
| global (ohne Namen) | `unpublish.php`, `rssimport.php`, `indexcontent.php`, `hide.php`, `internal_drafts_cleanup.php` | RSS-Importe und verzögerte Suchindizierung laufen hier |
| `frequent` | `notification.php`, `workflow.php`, `contentjobs.php`, `audit.php`, `mailbounces.php` | Wartende Workflow-Prozesse, Benachrichtigungen, Inhaltsjobs und Audit-Senken (der Webhook) werden hier abgearbeitet |
| `infrequent` | `basket_cleanup.php`, `linkcheck.php`, `mailpreferences.php` | Linkprüfungen importierter URLs |

Wenn eine Integration Workflows nutzt, die an den Cronjob abgeben, oder den Audit-Webhook, muss der Teil `frequent`
laufen (alle 5 Minuten ist der übliche Zeitplan).

## Verwandte Seiten

- [Cronjobs](../../anleitungen/cronjobs.md)
- [Cronjob-Teile](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/cronjob-teile.md)
- [Einen Cronjob-Teil schreiben](../ersteller/anwendungsentwickler/einen-cronjob-teil-schreiben.md)
- [Cronjobs für Hoster](../hosting/betrieb/cronjobs.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
