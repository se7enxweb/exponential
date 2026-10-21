# Befehle, Cronjob-Teile und Modul-Views als Klassen

Lesen Sie diese Seite, wenn Sie Code pflegen, der Skripte, Cronjob-Teile oder Modul-Views des Kernels liest, einbindet
oder kopiert, oder wenn Sie eigene schreiben. Ab Exponential 6.0.15 liegt der Code jedes Kommandozeilenskripts, jedes
Cronjob-Teils und jeder Modul-View in einer **Klasse**. Die Datei, die Sie kennen (`bin/php/ezcache.php`,
`cronjobs/workflow.php`, `kernel/content/view.php`), behält ihren Pfad und tut genau eines: Sie ruft diese Klasse auf.
Eine View, ein Befehl und ein Cronjob-Teil können sich jetzt eine Implementierung teilen, getestet werden und von einer
Website oder Erweiterung neu implementiert werden, ohne Dateien zu kopieren.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Der Code ist nach `kernel/private/classes/{commands,cronjobs,views}/` umgezogen (Namensräume `Exponential\Command\Kernel`, `Exponential\Cronjob\Kernel`, `Exponential\View\Kernel`). Gemeinsame Arbeit ist in Dienste umgezogen. Neuer Einstellungsblock `site.ini [RunnableSettings]` (`Implementation[]`, `Listeners[]`) und die Ereignisse `runnable/<kind>/before` und `runnable/<kind>/after`. |
| Wer betroffen ist | Code, der ein Skript, einen Cronjob-Teil oder eine View-Datei des Kernels wegen ihrer Logik eingebunden oder geparst hat: Die Logik steht jetzt in der Klasse. Websites, die eine kopierte und bearbeitete View- oder Cronjob-Datei des Kernels behalten. Aufruf auf der Kommandozeile, Ausgabe von `--help` und Exit-Codes sind unverändert. |
| Wie Sie es prüfen | `php vendor/bin/phpunit tests/tests/kernel/classes/runnable/` |
| Wie Sie es beheben | Suchen Sie den Code in `kernel/private/classes/`. Ersetzen Sie eine kopierte Kernel-Datei durch eine Unterklasse, die in `[RunnableSettings] Implementation[]` registriert ist (siehe [Eine Implementierung von einer Website oder Erweiterung aus ersetzen](#eine-implementierung-von-einer-website-oder-erweiterung-aus-ersetzen)). Erzeugen Sie nach dem Update die Autoloads neu. |

> **Der alte Weg funktioniert weiterhin.** Ein Skript, ein Cronjob-Teil oder eine View, als einfache PHP-Datei
> geschrieben, läuft genau wie bisher; nichts im Kernel verlangt die neue Form. Es ist nur nicht mehr das, was das
> Produkt verwendet oder dokumentiert, weil eine einfache Datei sich nicht erweitern, für sich aufrufen oder testen
> lässt. Schreiben Sie neuen Code als Klassen.

## Die Klassenhierarchie

```
Exponential\Runnable\Runnable            kernel/private/classes/runnable/runnable.php
├── Exponential\Runnable\Command          a command line script      run()
├── Exponential\Runnable\CronjobPart      a cronjob part             run( array $scope )
└── Exponential\Runnable\ModuleView       a module view              run( array $scope )
```

| Art | Die Datei | Die Klasse |
|---|---|---|
| Befehl | `bin/php/<name>.php` | `Exponential\Command\Kernel\<Name>` in `kernel/private/classes/commands/<name>.php` |
| Cronjob-Teil | `cronjobs/<name>.php` | `Exponential\Cronjob\Kernel\<Name>` in `kernel/private/classes/cronjobs/<name>.php` |
| Modul-View | `kernel/<module>/<view>.php` | `Exponential\View\Kernel\<Module>\<View>` in `kernel/private/classes/views/<module>/<view>.php` |
| Befehl einer Erweiterung | `extension/<ext>/bin/.../<name>.php` | `Exponential\Command\Extension\<Ext>\<Name>` in `extension/<ext>/classes/runnable/commands/` |
| Cronjob-Teil einer Erweiterung | `extension/<ext>/cronjobs/<name>.php` | `Exponential\Cronjob\Extension\<Ext>\<Name>` in `extension/<ext>/classes/runnable/cronjobs/` |
| Modul-View einer Erweiterung | `extension/<ext>/modules/<module>/<view>.php` | `Exponential\View\Extension\<Ext>\<Module>\<View>` in `extension/<ext>/classes/runnable/views/<module>/` |

Arbeit, die sich eine View, ein Befehl und ein Cronjob-Teil teilen, liegt in einer **Dienst**-Klasse daneben, in
`kernel/private/classes/services/` (`Exponential\Service\...`) oder in einer vorhandenen Kernel-Klasse; siehe
[Dienste](#dienste-eine-implementierung-für-die-view-den-befehl-und-den-cronjob-teil).

## Wie die Dateien jetzt aussehen

Ein Befehl (`bin/php/ezcache.php`):

```php
#!/usr/bin/env php
<?php
/** ...the script's header... */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezcache.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezcache::main( __FILE__ );
```

Ein Cronjob-Teil (`cronjobs/workflow.php`) und eine Modul-View (`kernel/content/history.php`):

```php
<?php
return \Exponential\Cronjob\Kernel\Workflow::main( __FILE__, get_defined_vars() );
```

```php
<?php
return \Exponential\View\Kernel\Content\History::main( __FILE__, get_defined_vars() );
```

`get_defined_vars()` übergibt der Klasse die Variablen, die der Kernel einem Teil oder einer View gibt: `$cli`,
`$isQuiet` bei einem Cronjob-Teil (aus `eZRunCronjobs::runScript()`), `$Params`, `$Module` und die View-Parameter bei
einer View (aus `eZProcess::runFile()`). Die Klasse einer View gibt zurück, was der Kernel früher aus der Datei nahm: ihr
`$Result` oder das, was sie zurückgab (`ModuleView::viewResult()`).

## Eine neue schreiben

### Ein Befehl

```php
<?php
// extension/myext/classes/runnable/commands/report.php
namespace Exponential\Command\Extension\Myext;

class Report extends \Exponential\Runnable\Command
{
    public function run()
    {
        $script = $this->script( array( 'description' => "Writes the monthly report",
                                        'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
        $options = $this->startup( '[month:]', '', array( 'month' => 'The month, YYYY-MM' ) );

        $this->output( \myReportService::build( $options['month'] ) );   // the work: a class you can call anywhere

        $this->shutdown( 0 );
    }
}
```

```php
#!/usr/bin/env php
<?php
// extension/myext/bin/php/report.php
require_once 'autoload.php';
\Exponential\Command\Extension\Myext\Report::main( __FILE__ );
```

Dann `php bin/php/ezpgenerateautoloads.php -e` und `php extension/myext/bin/php/report.php --help`.

### Ein Cronjob-Teil

```php
<?php
// extension/myext/classes/runnable/cronjobs/cleanup.php
namespace Exponential\Cronjob\Extension\Myext;

class Cleanup extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = $scope['cli'];
        $removed = \myCleanupService::removeExpired();
        if ( empty( $scope['isQuiet'] ) )
            $cli->output( "Removed $removed expired items" );
    }
}
```

```php
<?php
// extension/myext/cronjobs/cleanup.php
return \Exponential\Cronjob\Extension\Myext\Cleanup::main( __FILE__, get_defined_vars() );
```

Registrieren Sie den Teil wie bisher in `cronjob.ini` (`[CronjobPart-cleanup] Scripts[]=cleanup.php`).

### Eine Modul-View

```php
<?php
// extension/myext/classes/runnable/views/report/show.php
namespace Exponential\View\Extension\Myext\Report;

class Show extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Module = $scope['Params']['Module'];
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'report', \myReportService::build( $scope['Params']['Month'] ) );
        return array( 'content' => $tpl->fetch( 'design:report/show.tpl' ),
                      'path' => array( array( 'text' => 'Report', 'url' => false ) ) );
    }
}
```

```php
<?php
// extension/myext/modules/report/show.php (named in module.php ViewList as before)
return \Exponential\View\Extension\Myext\Report\Show::main( __FILE__, get_defined_vars() );
```

`$scope` ist das, was `eZProcess::runFile()` definiert: `$scope['Params']` (mit dem Modul als
`$scope['Params']['Module']`) und `$scope['Result']` immer, die Parameter der View als eigene Variablen (darunter
`$scope['Module']`) nur, wenn die `module.php` des Moduls `'variable_params' => true` setzt. Lesen Sie das Modul als
`$scope['Params']['Module']`, das jedes Modul hat.

## Gemeinsames Parsen von Optionen, Hilfe und Ausgabe

Was jeder Befehl auf dieselbe Weise tut, steht einmal in `Exponential\Runnable\Command`. Die Hilfsmethoden geben ihre
Argumente unverändert an `eZScript` und `eZCLI` weiter, sodass `--help`, `-q`/`--quiet`, `-s`/`--siteaccess`,
`-l`/`--login`, `-d`/`--debug`, `-v`/`--verbose` und die Exit-Codes genau das sind, was `eZScript::getOptions()` und
`eZScript::shutdown()` daraus machen.

| Hilfsmethode | Tut |
|---|---|
| `$this->cli()` | das Terminal, `eZCLI::instance()` |
| `$this->script( array $settings )` | erzeugt das Skript, `eZScript::instance( $settings )` (description, `use-session`, `use-modules`, `use-extensions`, `site-access`, ...); ohne Einstellungen gibt sie das Skript zurück |
| `$this->options( $config, $argumentConfig, $optionHelp, ... )` | parst die Kommandozeile, `eZScript::getOptions()`: zeigt `--help` und beendet, wendet die Standardoptionen an |
| `$this->startup( $config, $argumentConfig, $optionHelp, ... )` | die drei Schritte nach `script()`: das `startup()` des Skripts, `options()` und das `initialize()` des Skripts (Siteaccess, Erweiterungen, Sitzung, Datenbank) |
| `$this->start( $settings, $config, ... )` | `script( $settings )` und `startup()` in einem Aufruf |
| `$this->output()`, `error()`, `warning()` | schreiben eine Zeile, einen Fehler, eine Warnung; `output()` schreibt mit `-q` nichts |
| `$this->isQuiet()` | `-q` wurde angegeben |
| `$this->log( $message, $logName = null )` | `eZLog::write()` standardmäßig nach `var/log/<script name>.log` |
| `$this->shutdown( $exitCode, $exitText )` | `eZScript::shutdown()`, das beendet |

Vorher und nachher, in einem der Befehle des Kernels (`bin/php/trashpurge.php`):

```php
// before
$script = eZScript::instance( array( 'description' => "Empty Exponential trash.", 'use-session' => false,
                                     'use-modules' => false, 'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( "[iteration-sleep:][iteration-limit:][memory-monitoring][trashed-days:]", "", $help );
$script->initialize();

// after
$script = $this->script( array( 'description' => "Empty Exponential trash.", 'use-session' => false,
                                'use-modules' => false, 'use-extensions' => true ) );
$options = $this->startup( "[iteration-sleep:][iteration-limit:][memory-monitoring][trashed-days:]", "", $help );
```

Ein Befehl, der zwischen den Schritten etwas tut (vor `initialize()` eine Debug-Option setzt, den Siteaccess selbst
wählt), ruft weiterhin `$script->startup()`, `$this->options()` und `$script->initialize()` einzeln auf. Die 61
Befehle des Kernels, die ein Skript erzeugen, tun es mit `$this->script()` und parsen ihre Optionen mit
`$this->options()` oder `$this->startup()` (40 davon); sie und `kickstarter.php` erhalten ihr Terminal mit
`$this->cli()`. (Der Autoload-Generator, die Installationsprogramme und das Vorwärmen von Velocity erzeugen kein
Skript.) Ihre Ausgabe von `--help` und ihr Exit-Code sind dieselben wie bisher, Byte für Byte.

## Dienste: eine Implementierung für die View, den Befehl und den Cronjob-Teil

Wo eine View und ein Befehl oder Cronjob-Teil dieselbe Arbeit tun, liegt die Arbeit in einer Klasse, und alle rufen sie
auf.

| Arbeit | Dienst | Aufgerufen von |
|---|---|---|
| Den Papierkorb leeren | `Exponential\Service\Trash` (`kernel/private/classes/services/trash.php`) | `content/trash`, `bin/php/trashpurge.php`, `cronjobs/trashpurge.php` |
| Abgelaufene Sitzungen entfernen | `Exponential\Service\SessionGarbageCollector` (`kernel/private/classes/services/sessiongarbagecollector.php`) | `setup/session`, `bin/php/ezsessiongc.php`, `cronjobs/session_gc.php` |
| Caches leeren | `expCacheManager` (`kernel/classes/expcachemanager.php`) | `setup/cache`, `bin/php/cache.php` (`exp:cache`), `setup/cachetoolbar` (die Werkzeugleiste zum Leeren des Caches) |
| Alte Entwürfe entfernen | `Exponential\Service\DraftsCleanup` (`kernel/private/classes/services/draftscleanup.php`) | `cronjobs/old_drafts_cleanup.php` (Entwürfe der Benutzer), `cronjobs/internal_drafts_cleanup.php` (interne Entwürfe) |
| Der statische Cache | `expStaticCacheRunner`, `expCacheManager::regenerateStaticCache()` | `setup/cache`, `setup/staticcachestream`, `bin/php/makestaticcache.php` |
| Vorladen | `expPreloadRunner`, `expPreloadJob` | `setup/preload`, `setup/preloadjob`, `bin/php/preload.php`, `bin/php/preloadjob.php` |
| Wartungsmodus | `expMaintenance` | `setup/maintenance`, `bin/php/maintenance.php` |
| Cronjobs aus der Administration ausführen | `expCronjobRunner` | `setup/cronjobs`, `setup/cronjobsstream` |

```php
// the trash
if ( \Exponential\Service\Trash::canEmpty( \eZUser::currentUser() ) )   // content/cleantrash
    \Exponential\Service\Trash::purgeObjects( $objectIDs );                // the selected objects
\Exponential\Service\Trash::emptyTrash();                                 // the Empty button: all of it, in batches
\Exponential\Service\Trash::purgeInBatches( 100, 1, $trashedBefore );     // 100 at a time, a transaction each, 1 s between
\Exponential\Service\Trash::purge( $cli, false, false, $script, 100, 1, 30 );  // the command: the same batches with progress,
                                                                          // only what has been in the trash 30 days

// expired sessions
\Exponential\Service\SessionGarbageCollector::collect();          // and the baskets they leave (view, command, cronjob part)
\Exponential\Service\SessionGarbageCollector::collect( false );   // the sessions only
```

Die Schaltfläche zum Leeren in der Ansicht des Papierkorbs und der Befehl löschen auf dieselbe Weise:
`purgeInBatches()`, jede Gruppe von 100 in einer eigenen Transaktion, mit Leeren des Inhalts-Caches und einer Pause von
einer Sekunde zwischen den Gruppen (`eZScriptTrashPurge`, hinter dem Befehl und dem Cronjob-Teil, führt seine Gruppen
ebenfalls über den Dienst aus). Nach den Gruppen löscht die Schaltfläche außerdem jedes archivierte Objekt ohne Eintrag
im Papierkorb, sodass der Papierkorb danach leer ist. Das Entfernen abgelaufener Sitzungen in `setup/session` entfernt
die Warenkörbe, die sie hinterlassen, wie es der Befehl und der Cronjob-Teil tun. Der Befehl `bin/php/ezcache.php`
behält `eZCacheHelper`, das die Fortschrittsausgabe schreibt, die es immer geschrieben hat.

## Erweiterungspunkte: Ereignisse, die Registry in site.ini, Neuimplementierung

### Ereignisse um run()

`main()` jedes Befehls, Cronjob-Teils und jeder View führt `run()` zwischen zwei Ereignissen des Ereignis-Hubs des
Kernels (`ezpEvent`) aus. `<kind>` ist `command`, `cronjob` oder `view`:

| Ereignis | Typ | Der Listener erhält | Der Listener gibt zurück |
|---|---|---|---|
| `runnable/<kind>/before` | notify | `$runnable, $class, $scope` | nichts |
| `runnable/<kind>/after` | filter | `$result, $runnable, $class, $scope` | das zu verwendende Ergebnis |

`$class` ist die Klasse, die läuft (die Neuimplementierung, falls es eine gibt); `$scope` ist das, was dem Teil oder
der View übergeben wurde (`$Params`, `$Module`, ...; leer bei einem Befehl). Ein Befehl, der mit `$this->shutdown()`
endet, beendet innerhalb von `run()`, daher feuert nur sein Ereignis before. Ohne Listener gibt `main()` zurück, was
`run()` zurückgegeben hat.

```php
class myViewTimer
{
    private static $start = array();

    public static function before( $runnable, $class, array $scope )
    {
        self::$start[$class] = microtime( true );
    }

    public static function after( $result, $runnable, $class, array $scope )
    {
        \eZDebug::writeNotice( sprintf( '%s took %.1f ms', $class, 1000 * ( microtime( true ) - self::$start[$class] ) ), __METHOD__ );
        return $result;   // a filter: return the result, or the view loses it
    }
}
```

### Die Registry in site.ini

```ini
# settings/override/site.ini.append.php, or an extension's settings/site.ini.append.php
[RunnableSettings]
# a subclass to run instead of a runnable class
Implementation[Exponential\View\Kernel\Content\History]=myHistoryView
# listeners of the events above
Listeners[]=runnable/view/before@myViewTimer::before
Listeners[]=runnable/view/after@myViewTimer::after
```

`settings/site.ini` dokumentiert beides, ohne dass etwas gesetzt ist. `[RunnableSettings] Listeners[]` wird für die
Runnables in jedem Kontext angehängt, auch auf der Kommandozeile, einmal pro Prozess; `[Event] Listeners[]` bleibt wie
bisher nur an Web-Anfragen angehängt, sodass vorhandene Listener nicht plötzlich in Skripten laufen. Ein Listener kann
auch im Code angehängt werden: `ezpEvent::getInstance()->attach( 'runnable/view/after', $callback )`.

### Eine Implementierung von einer Website oder Erweiterung aus ersetzen

Jedes Runnable wird über `Runnable::create()` erzeugt, das zuerst `Implementation[<class>]` nachschlägt. Der Ersatz
muss die Klasse erweitern, die er ersetzt (`is_subclass_of`); ein Eintrag, der etwas anderes nennt, wird ignoriert. So
überschreibt der Ersatz eine Methode und behält den Rest:

```php
class myHistoryView extends \Exponential\View\Kernel\Content\History
{
    public function run( array $scope )
    {
        $result = parent::run( $scope );
        $result['content'] .= '<p class="note">Versions older than a year are archived.</p>';
        return $result;
    }
}

// Implementation[Exponential\Cronjob\Kernel\Trashpurge]=mySundayTrashpurge: empty the trash on Sundays only
class mySundayTrashpurge extends \Exponential\Cronjob\Kernel\Trashpurge
{
    public function run( array $scope )
    {
        if ( date( 'N' ) == 7 )
            return parent::run( $scope );
    }
}
```

Keine Datei des Kernels wird kopiert oder bearbeitet, daher aktualisiert das nächste Release das Original darunter. Das
funktioniert für alle drei Arten. Eines sollten Sie zu Befehlen wissen: Ein Befehl wird erzeugt, bevor sein Skript die
Einstellungen gelesen hat, daher werden bei einem Befehl `Implementation[]` und `Listeners[]` nur aus
`settings/site.ini` und `settings/override/site.ini.append.php` gelesen (`Runnable::settings()` liest sie selbst, ohne
die Einstellungsinstanz anzulegen, die das Skript später einrichtet). Views und Cronjob-Teile sehen jede Einstellung,
auch die von Siteaccesses und Erweiterungen.

### Sie sichtbar machen

`setup/rad` und seine Übersicht (`setup/radsurvey`, Abschnitt „Commands, cronjob parts and views as classes“) zählen
die Runnables als Erweiterungspunkte: jedes pro Art und Besitzer, welche neu implementiert sind, und jeden Eintrag in
`Implementation[]`, der nicht funktionieren kann (er nennt keine Runnable-Klasse, der Ersatz existiert nicht, oder er
erweitert die Klasse nicht). Die sechs Ereignisse stehen in der Ereignisliste der Übersicht. Der Katalog auf
`setup/rad` erklärt den Mechanismus unter „Command, cronjob part or view re-implemented“.

## Eines aus Ihrem eigenen Code aufrufen

```php
// a view's work from a script, or a command from a cronjob part
$result = \Exponential\View\Kernel\Content\History::create( 'kernel/content/history.php' )
            ->run( array( 'Params' => $params, 'Module' => $module ) );
```

`create()` beachtet `Implementation[]`; ein direkt aufgerufenes `run()` feuert die Ereignisse nicht (`main()` tut es).

## Wie der Code umgezogen ist

Der Code ist zuerst **unverändert** umgezogen (Stufe 1), sodass das Verhalten nachweislich dasselbe ist; fünf Stufen
der Verfeinerung folgten: die gemeinsame Behandlung von Optionen (Stufe 2), die Dienste (Stufe 3), die
Erweiterungspunkte (Stufe 4), das Aufräumen (Stufe 5) und eine weitere Welle von Diensten und kürzeren
run()-Methoden (Stufe 6, siehe unten).

- Die Variablen eines Befehls waren global, und Funktionen eines Skripts lasen sie mit `global $cli`; der umgezogene
  Code bindet seine Variablen an `$GLOBALS`, sodass diese Funktionen unverändert funktionieren. Funktionen und
  Klassen, die das Skript deklariert hat, ziehen in die Klassendatei um, im globalen Namensraum.
- `__FILE__` und `__DIR__` bedeuten im umgezogenen Code weiterhin die ursprüngliche Datei (`$this->scriptFile()`,
  `$this->scriptDir()`).
- Klassennamen im umgezogenen Code sind voll qualifiziert (`\eZCLI`), weil die Klasse in einem Namensraum lebt.
- Drei Einstiegspunkte haben eine eigene Form:
  - `bin/php/ezpgenerateautoloads.php` registriert die Klassen und kann sich daher nicht auf sie verlassen: Er bindet
    die Basisklassen und seine eigene Klassendatei direkt ein, bevor er `main()` aufruft.
  - `bin/php/ezasynchronouspublisher.php` behält `declare( ticks=1 )` im Einstiegspunkt, wo die Signal-Handler es
    brauchen.
  - `bin/php/velocity-router.php`, der Router des eingebauten Webservers von PHP, läuft, bevor es einen Autoloader
    gibt, und muss den Front-Controller auf oberster Ebene einbinden: Er bindet seine Klassendatei ein
    (`Exponential\Command\Kernel\VelocityRouter`, kein Runnable) und ruft das statische `route()` auf, das mit true,
    false oder dem einzubindenden Front-Controller antwortet.
- Absichtlich nicht umgezogen: `bin/php/exprepair.php`, weil es ohne die Composer-Bibliotheken läuft.
- Das Aufräumen entfernte, was die Umzüge hinterlassen hatten: Der Beginn von 40 Befehlen wurde zu `$this->startup()`,
  der Hook für die Warenkörbe der Sitzungen, den zwei Klassendateien als dieselbe globale Funktion deklarierten
  (`eZSessionBasketGarbageCollector`, eine Neudeklaration, die nur darauf wartete), ist jetzt
  `SessionGarbageCollector::cleanupBaskets()`, und Variablen, die kein Code mehr verwendete, verließen die globalen
  Bindungen.

### Stufe 6: mehr gemeinsamer Code, kürzere run()-Methoden

**Dienste.** Zwei weitere Paare taten dieselbe Arbeit doppelt:

- `cronjobs/old_drafts_cleanup.php` und `cronjobs/internal_drafts_cleanup.php` waren derselbe Code mit anderen
  Einstellungen. Beide rufen jetzt `Exponential\Service\DraftsCleanup::cleanup( DraftsCleanup::USER_DRAFTS )` oder
  `( DraftsCleanup::INTERNAL_DRAFTS )` auf. Der Dienst liest content.ini `[VersionManagement]`
  (`DraftsDuration`/`DraftsCleanUpLimit`, `InternalDraftsDuration`/`InternalDraftsCleanUpLimit`, mit denselben
  Standardwerten von 90 Tagen und 24 Stunden) und gibt null zurück, wenn keine Lebensdauer gesetzt ist. Die Teile
  geben aus, was sie bisher ausgegeben haben.
- Die Werkzeugleiste zum Leeren des Caches (`setup/cachetoolbar`) leert Alles, Template, Inhalt, Template und Inhalt
  sowie INI über `expCacheManager::clear()`, wie es `setup/cache` und `exp:cache` tun. Ihre Auswahlmöglichkeiten
  Static, ContentNode und ContentSubtree sind unverändert.

Nicht geteilt, weil die beiden Seiten unterschiedliche Arbeit tun: der Suchindex (`updatesearchindex.php` indiziert
jedes Objekt neu; der Teil `indexcontent` arbeitet die Warteschlange anstehender Änderungen ab; keine View tut eines
davon) und die URL-Aliase (`updateniceurls.php` erzeugt sie alle neu; `content/urlalias` bearbeitet einzelne Aliase).
Der statische Cache, das Leeren von Caches, das Löschen im Cluster und das Aufräumen von RSS-Importen hatten bereits je
eine Implementierung (`expStaticCacheRunner`, `expCacheManager`, `eZScriptClusterPurge`, `expCleanupRSS`).

**Kürzere run()-Methoden.** Die längsten run()-Methoden wurden in benannte geschützte Methoden aufgeteilt, als reines
Refactoring durch Extrahieren von Methoden. Jede Methode enthält ganze Anweisungen der obersten Ebene von run(),
unverändert verschoben:

| Runnable | run() vorher | nachher | Methoden |
|---|---|---|---|
| `setup/info` | 1114 | 153 | `systemInfo()`, `webserverInfo()`, `siteInfo()`, `engineSourceInfo()`, `engineArchiveCheck()`, `phpCachesInfo()`, `velocityServerInfo()`, `responseCacheInfo()`, `httpCacheInfo()`, `sqlProfileInfo()` |
| `ezpm.php` | 1049 | 124 | `parseArguments()`, `checkCommands()`, `connectDatabase()`, `runCommands()` |
| `shop/dashboard` | 906 | 128 | `currencies()`, `recentOrders()`, `allTimeFigures()`, `waitingOrders()`, `unfinishedBaskets()`, `products()`, `shopConfiguration()`, `nextSteps()` |
| `class/edit` | 863 | 278 | 11, von `fetchClassVersion()` bis `newAttribute()` |
| `role/edit` | 808 | 237 | `applyRole()`, `addLimitation()`, `customFunction()`, `selectLimitationValues()`, `discardLimitation()`, `createPolicy()` |
| `updateniceurls.php` | 787 | 376 | `importOldUrls()` |
| `content/edit` | 524 | 163 | `cancelDraft()`, `newDraft()`, `selectLanguage()`, `chooseEditLanguage()`, `findEditVersion()` |
| `rss/edit_export` | 493 | 145 | `storeExport()`, `changeItemCount()`, `loadExport()`, `opmlImport()` |
| `preload.php` | 473 | 309 | `warmSections()`, `spiderSite()` |
| `cache.php` (`exp:cache`) | 439 | 225 | `runGroup()` |

Die Regeln, die es sicher machen:

- Die Variablen von run() erreichen die Methode **als Referenz** (`protected function newDraft( &$http, &$obj, ... )`),
  sodass die Methode dieselben Variablen liest und ändert. Eine Variable, die nur die Methode verwendet, bleibt nur dann
  lokal für sie, wenn nichts anderes ihr einen Wert gegeben haben könnte: bei einer View kein Name der Parameter der
  View (module.php) oder der Variablen, die `eZProcess::runFile()` übergibt.
- Die Variablen eines Befehls sind global, und Funktionen des Skripts lesen sie mit `global`. Die Methode bindet diese
  so, wie es run() tut (`${$__name} = &$GLOBALS[$__name]`), statt sie als Parameter zu nehmen.
- Ein Block, der aus run() zurückkehrt (das `return $Module->redirectToView( ... )` einer View), gibt aus der Methode
  denselben Wert zurück, und `$this`, wenn er ohne Rückgabe endet. Der Aufruf gibt alles andere weiter:
  `if ( ( $__return = $this->newDraft( ... ) ) !== $this ) return $__return;`. `$this` ist der eine Wert, den run()
  vorher nie zurückgegeben hat, weil es das Objekt damals nicht gab.
- Blöcke mit `global`, `static`-Variablen, variablen Variablen, `compact()`/`extract()`, `__LINE__`/`__METHOD__`,
  `break`/`continue`, die den Block verlassen, oder die eine Variable neu binden (`= &`, `unset()`), die run() auch
  hat, bleiben in run(). Deshalb sind `velocity.php` (nur durch Ausführen bewiesen), `ezsubtreecopy.php` und
  `content/action` (beide an anderer Stelle überarbeitet) und das Aktualisieren der Knoten-Aliase in
  `updateniceurls.php` (es entfernt eine globale Variable mit unset) nicht aufgeteilt.

Bewiesen dadurch, dass der Rumpf jeder Methode wieder an die Stelle ihres Aufrufs gesetzt wurde, was für alle zehn Byte
für Byte die Datei ergibt, die run() vorher hatte. Die Seiten jeder aufgeteilten View sind vorher und nachher dieselben
(Text des Hauptinhalts und Formularfelder, abgesehen von Zahlen), ebenso die echten Läufe von `ezpm.php list|help`,
`exp:cache list|tags|status|clear --dry-run` und das `--help` jedes Kernel-Befehls.

**Befehle von Erweiterungen** beginnen jetzt genauso wie die des Kernels: In cjw_newsletter, enhancedselection2,
explayouts, ezprestapi, eztags, ezupdate, git_manager und xrowextract (25 Befehle) verwendet run() `$this->cli()`,
`$this->script()`, `$this->options()` und, wo die drei Schritte aufeinander folgen, `$this->startup()`. Ihre Ausgabe
von `--help` und ihre Exit-Codes sind dieselben wie bisher.

Nachweis, pro Art und nach jeder Stufe:

| Art | Bewiesen durch |
|---|---|
| Befehle | Ausgabe von `--help` und Exit-Code vorher und nachher identisch, für jeden Kernel-Befehl; Befehle echt ausgeführt, wo das nichts ändert (`maintenance.php status`, `ezsessiongc.php`, `trashpurge.php --trashed-days=100000`, `ezcache.php --list-tags`, ...) |
| Cronjob-Teile | `runcronjobs.php` führt die Gruppen default und frequent wie bisher aus; Teile in keiner Gruppe werden über `eZRunCronjobs::runScript()` ausgeführt |
| Modul-Views | die Seiten jedes Moduls vorher und nachher abgerufen (Status, Titel, Fehler, Inhalt); der Test des Bearbeitungsmodus jeder Inhaltsklasse |

## Die Stream-Views unter Velocity

Die Views, die mit einem eigenen Body statt einer Seite antworten: setup/preloadstream, setup/staticcachestream,
setup/cronjobsstream und die Ajax-Antwort von setup/cronjobs. Sie leeren die Ausgabepuffer mit
`eZExecution::discardOutputBuffers()` (seit 2026-10-05). Die Schleife, die sie vorher verwendeten,
`while ( ob_get_level() > 0 ) ob_end_clean();`, endete unter Velocity nie, weil der Worker einen Puffer behält, der
sich nicht entfernen lässt, und die Anfrage hing.

Unter PHP-FPM streamen die Fortschrittsereignisse, während sie geschehen. **Unter Velocity (:8080) wird eine Antwort
gesendet, wenn das Skript endet**, daher zeigt eine Fortschrittsseite nichts, solange die Arbeit läuft, und erhält dann
alle Ereignisse auf einmal. Das Ergebnis ist vollständig; nur der Live-Fortschritt fehlt. Verwenden Sie die
PHP-FPM-Adresse der Website, um einen langen Lauf zu verfolgen.

## Tests

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/runnable/
```

| Test | Deckt ab |
|---|---|
| `RunnableTest` | die Basisklassen: `main()` jeder Art, `create()`, die Skriptdatei, die Regel für das Ergebnis einer View |
| `MovedEntryPointsTest` | jeden umgezogenen Einstiegspunkt: Die Klasse, die er aufruft, existiert, ist in den Autoload-Arrays zugeordnet, lässt sich parsen und erweitert die richtige Basisklasse; die Form des Routers |
| `CommandHelpersTest` | die gemeinsamen Hilfsmethoden, und dass kein Kernel-Befehl `eZScript` oder `eZCLI` selbst erzeugt |
| `ServicesTest` | die Dienste für Papierkorb und Sitzungen, den Dienst zum Aufräumen von Entwürfen und dass ihre Aufrufer (auch die Cache-Werkzeugleiste) sie verwenden |
| `ExtractedRunMethodsTest` | die aufgeteilten run()-Methoden: geschützt, als Referenz, einmal aufgerufen, frühe Rückgaben weitergegeben, die globalen Bindungen eines Befehls, wie lang run() sein darf |
| `RunnableExtensionPointsTest` | die Ereignisse jeder Art, die Neuimplementierung jeder Art über die Einstellungen, die Listener von `[RunnableSettings]`, das eigene Lesen der Einstellungen durch einen Befehl |
| `RadSurveyRunnablesTest` | die Runnables in der Übersicht der Erweiterungspunkte und ihre Zählungen |

## Verwandte Seiten

- [Exponential-Konsole — `bin/php/console`](konsole.md)
- [Konsole für Cronjobs](../../../eng-us/features/6.0/cronjobs-console.md)
- [Juni 2026, erste Hälfte (1. bis 15. Juni)](../../../eng-us/history/2026/2026-06a.md)
- [Spezifikation: Befehle, Cronjob-Teile und Views](../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md)
- [RAD-Erweiterungspunkte](rad-erweiterungspunkte.md)
- [Verhaltensänderungen vom 1. und 2. Oktober 2026](verhaltensaenderungen-2026-10.md)
