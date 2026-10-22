---
title: Einen Cronjob-Teil schreiben
audience: php-developer, developer
category: php-development
order: 240
summary: Eine Anleitung für zeitlich geplante Arbeit in einer Erweiterung - die Cronjob-Klasse, die Exponential\Runnable\CronjobPart erweitert, die Einstiegsdatei in cronjobs/, der Block [CronjobPart-name] in cronjob.ini, die Ausführung mit runcronjobs.php oder bin/php/console cron:<part>, stille Ausgabe und Regeln für sichere, wiederholbare Läufe.
keywords: Cronjob-Teil, Cronjob, geplante Arbeit, Stapel, Sperren, cronjob part, cronjob, CronjobPart, Exponential\Cronjob\Extension, runcronjobs.php, cronjob.ini, CronjobPart block, Scripts, cron:frequent, crontab:list, isQuiet, scheduled work, description tag, batches, locks
type: help
---
# Einen Cronjob-Teil schreiben

Diese Anleitung richtet sich an PHP-Entwickler, die Arbeit nach Zeitplan ausführen lassen müssen: abgelaufene
Datensätze bereinigen, eine Zusammenfassung versenden, einen Feed importieren. Ein Cronjob-Teil ist eine benannte Gruppe
von Skripten in `cronjob.ini`; die System-Crontab führt `runcronjobs.php <part>` aus, das jedes Skript des Teils
ausführt. Die Anwendungssicht auf dasselbe Thema finden Sie unter
[Cronjobs für Anwendungen](../ersteller/anwendungsentwickler/cronjobs-fuer-anwendungen.md).

## Inhalt

1. [Schritt 1: die Cronjob-Klasse](#schritt-1-die-cronjob-klasse)
2. [Schritt 2: die Einstiegsdatei](#schritt-2-die-einstiegsdatei)
3. [Schritt 3: der Teil in cronjob.ini](#schritt-3-der-teil-in-cronjobini)
4. [Schritt 4: ausführen](#schritt-4-ausführen)
5. [Schritt 5: zeitlich planen](#schritt-5-zeitlich-planen)
6. [Was das Skript erhält](#was-das-skript-erhält)
7. [Regeln für zeitlich geplanten Code](#regeln-für-zeitlich-geplanten-code)

## Schritt 1: die Cronjob-Klasse

`extension/acmereport/classes/runnable/cronjobs/cleanup.php`:

```php
<?php
namespace Exponential\Cronjob\Extension\Acmereport;

class Cleanup extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = $scope['cli'];
        $removed = \acmeReportService::removeExpired( 500 );   // at most 500 per run
        if ( empty( $scope['isQuiet'] ) )
            $cli->output( "acmereport: removed $removed expired items" );
    }
}
```

## Schritt 2: die Einstiegsdatei

`extension/acmereport/cronjobs/cleanup.php`:

```php
<?php
/**
 * @description Remove expired acmereport items
 */
return \Exponential\Cronjob\Extension\Acmereport\Cleanup::main( __FILE__, get_defined_vars() );
```

`list cron` in der Konsole beschreibt jeden Teil mit den `@description`-Tags seiner Skripte.

## Schritt 3: der Teil in cronjob.ini

`extension/acmereport/settings/cronjob.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[CronjobSettings]
ExtensionDirectories[]=acmereport

[CronjobPart-acmereport]
Scripts[]=cleanup.php
*/ ?>
```

`ExtensionDirectories[]` lässt den Runner in `extension/acmereport/cronjobs/` suchen. Sie können Ihr Skript auch einem
vorhandenen Teil hinzufügen (`[CronjobPart-frequent] Scripts[]=cleanup.php`) statt einem eigenen Teil; ein eigener Teil
lässt sich getrennt planen und getrennt pausieren.

## Schritt 4: ausführen

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini
php runcronjobs.php acmereport                # the classic runner
php bin/php/console cron:acmereport           # the same through the console
php bin/php/console list cron                 # every part and its scripts
```

Fügen Sie `-s <siteaccess>` für die Einstellungen eines Siteaccess hinzu und `--allow-root-user` nur, wenn Sie als root
arbeiten müssen.

## Schritt 5: zeitlich planen

Fügen Sie den Teil der Crontab des Site-Benutzers hinzu, neben den Standardteilen (`frequent`, `infrequent`,
`contentjobs` ...):

```text
*/15 * * * * cd /path/to/exponential && php runcronjobs.php -q acmereport
```

`php bin/php/console crontab:list` gibt die Crontab aus, `crontab:edit` öffnet sie. **Setup > Cronjobs** zeigt die Teile
und wann sie zuletzt gelaufen sind ([Cronjob-Konsole](../../funktionen/6.0/cronjobs-in-der-konsole.md),
[Filter für Cronjob-Teile](../../funktionen/6.0/filter-fuer-cronjob-teile.md), [Cronjobs pausieren](../../funktionen/6.0/cronjobs-pausieren.md)).

## Was das Skript erhält

`get_defined_vars()` übergibt, was `eZRunCronjobs::runScript()` definiert:

| In `$scope` | Enthält |
|---|---|
| `cli` | `eZCLI`, das Terminal |
| `isQuiet` | ob `-q` angegeben wurde |

Das Skript läuft im Prozess von `runcronjobs.php`, nachdem Siteaccess, Erweiterungen und Datenbank eingerichtet sind.
Alle Skripte eines Laufs teilen sich diesen Prozess: Ein fataler Fehler in einem stoppt die übrigen, und statischer
Zustand des einen ist für das nächste sichtbar.

## Regeln für zeitlich geplanten Code

- **Begrenzte Arbeit pro Lauf.** Verarbeiten Sie höchstens eine feste Anzahl von Elementen und lassen Sie den nächsten
  Lauf weitermachen. Ein Lauf, der länger dauert als sein Intervall, überschneidet sich mit dem nächsten.
- **Wiederholbar.** Ein Lauf, der auf halbem Weg gestoppt hat, muss sich sicher erneut starten lassen: Markieren Sie
  Elemente unterwegs als erledigt, in kurzen Transaktionen.
- **Still, sofern nicht gewünscht.** Geben Sie mit `-q` bei Erfolg nichts aus; Cron schickt jede Ausgabe per Mail an den
  Benutzer.
- **Logs, nicht Ausgabe, für das, was sich geändert hat.** `eZLog::write( ..., 'acmereport.log' )`.
- **Die Arbeit in einem Dienst.** Der Cronjob-Teil, ein Konsolenbefehl und eine Schaltfläche in der Administration
  können alle `acmeReportService::removeExpired()` aufrufen ([Modul-Views als Klassen](modul-views-als-klassen.md#dienste-eine-implementierung-für-drei-einstiegspunkte)).
- **Schwere Arbeit pro Element** gehört eher in eine Warteschlange oder einen Content-Job als in einen langen Cronjob
  ([Hintergrundarbeit: Warteschlangen, Cronjobs und Content-Jobs](../ersteller/anwendungsentwickler/muster-fuer-hintergrundarbeit.md)).

## Verwandte Seiten

- [Ein Cronjob-Skript schreiben (Anwendungsentwickler)](../ersteller/anwendungsentwickler/einen-cronjob-teil-schreiben.md)
- [Cronjob-Teile (Erweiterungsentwickler)](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/cronjob-teile.md)
- [Leitfaden zu Cronjobs](../../anleitungen/cronjobs.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
