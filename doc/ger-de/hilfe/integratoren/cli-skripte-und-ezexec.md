---
title: CLI-Skripte und ezexec für Integrationen
audience: integrator, developer
category: integration
order: 150
summary: Integrationscode auf dem Exponential-Server von der Kommandozeile ausführen - schnelle Skripte mit bin/php/ezexec.php, richtige Befehle mit eZScript in Ihrer Erweiterung, wie die Konsole sie findet, Siteaccess und Benutzer, Exit-Codes, Probeläufe und die Ausführung als Benutzer der Website.
keywords: CLI, Kommandozeile, ezexec, ezexec.php, eZScript, eZCLI, bin/php/console, ext:, Exit-Code, Siteaccess, dry-run, allow-root-user, Importskript, serverseitige Integration, command line, exit code, import script, server-side integration
type: help
---
# CLI-Skripte und ezexec für Integrationen

Diese Seite ist für Integrationsentwickler, deren Code **auf dem Exponential-Server** läuft: Importe, Exporte,
Reparaturen, einmalige Migrationen. Auf der Kommandozeile steht die ganze PHP-API zur Verfügung, es gibt kein
Anfrage-Timeout, und der Lauf lässt sich planen, protokollieren und überwachen. Es gibt zwei Wege:
`bin/php/ezexec.php` für ein schnelles Skript und einen Befehl Ihrer Erweiterung für alles, was Sie behalten.

## Inhalt

1. [ezexec: ein schnelles Skript mit geladenem Kernel](#ezexec-ein-schnelles-skript-mit-geladenem-kernel)
2. [Ein Befehl in Ihrer Erweiterung](#ein-befehl-in-ihrer-erweiterung)
3. [Siteaccess, Benutzer und Umgebung](#siteaccess-benutzer-und-umgebung)
4. [Exit-Codes, Probeläufe und Ausgabe](#exit-codes-probeläufe-und-ausgabe)
5. [Mitgelieferte Befehle, die Integrationen helfen](#mitgelieferte-befehle-die-integrationen-helfen)

## ezexec: ein schnelles Skript mit geladenem Kernel

`php bin/php/ezexec.php <script.php>` erledigt den Bootstrap (Einstellungen, Datenbank, Erweiterungen, Module) und
bindet dann Ihre Datei ein. Das Skript findet `$cli` (ein `eZCLI`) und `$script` (ein `eZScript`) einsatzbereit vor:

```php
<?php
// var/tmp/count_imported.php: how many objects carry our remote id prefix?
$db = eZDB::instance();
$rows = $db->arrayQuery( "SELECT COUNT(*) AS n FROM ezcontentobject WHERE remote_id LIKE 'erp-product-%'" );
$cli->output( 'Imported products: ' . (int)$rows[0]['n'] );
```

```bash
php bin/php/ezexec.php var/tmp/count_imported.php -s <siteaccess>
```

Gibt die eingebundene Datei etwas anderes als `1` zurück (eine einfache Datei gibt `1` zurück), ist der Exit-Code
`1`. Verwenden Sie `ezexec` zum Nachsehen und für einmalige Korrekturen; alles, was zweimal läuft, gehört als Befehl in
Ihre Erweiterung.

## Ein Befehl in Ihrer Erweiterung

Die Konsole (`bin/php/console`) findet Skripte zur Laufzeit: Aus `extension/<ext>/bin/php/<name>.php` wird der Befehl
`ext:<ext>:<name>`, mit der einzeiligen Beschreibung aus dem Tag `@description` des ersten Docblocks.

```php
#!/usr/bin/env php
<?php
/**
 * @description Import products from the ERP export file (--file, --dry-run)
 */
require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Import products from the ERP export file.\n",
                                     'use-session' => false,
                                     'use-modules' => true,
                                     'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[file:][dry-run]', '',
                                array( 'file' => 'The export file to read',
                                       'dry-run' => 'Report what would change, change nothing' ) );
$script->initialize();

if ( !$options['file'] || !is_readable( $options['file'] ) )
{
    $cli->error( 'Give a readable --file' );
    $script->shutdown( 1 );
}

$importer = new myIntegrationProductImport( $cli, (bool)$options['dry-run'] );
$ok = $importer->run( $options['file'] );

$script->shutdown( $ok ? 0 : 1 );
```

Speichern Sie es als `extension/myintegration/bin/php/importproducts.php`, machen Sie es ausführbar und führen Sie es
aus:

```bash
php bin/php/console ext:myintegration:importproducts --file=var/import/products.csv --dry-run -s <siteaccess>
php extension/myintegration/bin/php/importproducts.php --help
```

Halten Sie die Arbeit in einer Klasse (oben `myIntegrationProductImport`), damit derselbe Code aus dem Befehl, einem
Cronjob-Teil und einem Test läuft. Der Rumpf eines solchen Importers steht unter
[Ein Importskript schreiben](ein-importskript-schreiben.md).

## Siteaccess, Benutzer und Umgebung

| Thema | Was zu tun ist |
|---|---|
| Siteaccess | Übergeben Sie `-s <siteaccess>`; es gelten die Einstellungen dieses Siteaccess und seiner Access-Erweiterungen. Ohne die Option wird `DefaultAccess` verwendet |
| Benutzer | Ein Skript läuft als anonymer Benutzer, bis Sie mit `eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) )` einen anderen Benutzer zum aktuellen machen; verwenden Sie ein Dienstkonto ([wie](dienstkonten-rollen-und-richtlinien.md)) |
| Benutzer des Betriebssystems | Führen Sie es als der Benutzer aus, unter dem der Webserver läuft; als root geschriebene Dateien sind für die Website danach womöglich nicht beschreibbar. Als root verweigern Skripte die Ausführung, sofern nicht `--allow-root-user` angegeben ist |
| Zugangsdaten des anderen Systems | Lesen Sie sie aus der Umgebung oder aus der INI Ihrer Erweiterung in `settings/override`, nie von der Kommandozeile, wo sie in der Shell-Historie und der Prozessliste landen |
| Debug-Ausgabe | `-d` gibt am Ende die Debug-Ausgabe aus; `--logfiles` schreibt Protokolldateien |

## Exit-Codes, Probeläufe und Ausgabe

- Beenden Sie mit `0` bei Erfolg und mit einem anderen Wert bei Fehlern, damit Cron, Deployments und Überwachung
  reagieren können.
- Bieten Sie für jedes Skript, das Daten ändert, `--dry-run` an, und sagen Sie, was sich ändern würde.
- Beachten Sie `-q`: `$cli->output()` gibt damit nichts aus; verwenden Sie für Fehler `$cli->error()`, das immer
  ausgibt.
- Zeigen Sie bei langen Schleifen den Fortschritt mit `$script->resetIteration( $total )` und
  `$script->iterate( $cli, $status, $text )`.
- Geben Sie nie Passwörter, Schlüssel oder personenbezogene Daten aus; maskieren Sie sie.

## Mitgelieferte Befehle, die Integrationen helfen

| Befehl | Verwendung in einer Integration |
|---|---|
| `php bin/php/ezcsvimport.php --class=<class> <node> <file>` | Objekte einer Klasse aus CSV importieren (`--creator`, `--storage-dir`) |
| `php bin/php/ezcsvexport.php --storage-dir=<dir> <node>` | Einen Teilbaum in eine CSV-Datei pro Klasse exportieren |
| `php bin/php/updatesearchindex.php` | Den Suchindex nach einer Massenänderung neu aufbauen |
| `php bin/php/ezcache.php --clear-id=<ids>` | Caches nach einer Massenänderung leeren ([welche](caching-nach-importen.md)) |
| `php bin/php/expcontentjob.php list` | Inhaltsjobs (große Löschungen, Kopien, Verschiebungen) ausführen, auflisten, fortsetzen und abbrechen |
| `php bin/php/ezsubtreeremove.php` | Teilbäume über die Knoten-ID entfernen, in den Papierkorb, außer mit `--ignore-trash` |
| `php bin/php/console exp:logs` | Die Protokolldateien durchsuchen und verfolgen |
| `php bin/php/console exp:audit tail` | Das Audit-Protokoll lesen |
| `php bin/php/maintenance.php on\|off\|status` | Die Website für ein Migrationsfenster vom Netz nehmen |

`php bin/php/console list` zeigt jeden Befehl der Installation.

## Verwandte Seiten

- [CLI-Skripte und Konsolenbefehle](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/cli-skripte-und-console-befehle.md)
- [eZScript und eZCLI](../erweiterungsentwicklung/teil-8-referenz/ezscript-und-ezcli.md)
- [Exponential Console](../../bc/6.0/konsole.md)
- [Cronjobs für Integrationen](cronjobs-fuer-integrationen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
