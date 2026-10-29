# Der Kernel-Service-Container (6.0.15, erster Teil)

Lesen Sie diese Seite, wenn Sie am Start des Kernels arbeiten, Code schreiben, der den Kernel nach gemeinsam genutzten
Objekten fragt, Exponential unter Velocity betreiben oder eine Installation ausrollen. Sie beschreibt, was der erste
Teil des Kernel-Service-Containers tut, was er ändert und wie Sie ihn prüfen.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Der Kernel hat einen Service-Container: gemeinsam genutzte Objekte nach Namen (`expKernelServices::get( 'db' )`), definiert in `settings/kernelservices.ini`, beim ersten Gebrauch in einem Gültigkeitsbereich erzeugt (worker, request, prototype). Der Web-Kernel startet ihn und wendet mit einem **kompilierten Container** in `var/cache/kernelservices/` das aufgezeichnete Ergebnis seiner beiden Durchläufe zur Aktivierung der Erweiterungen an, statt bei jeder Anfrage jedes Erweiterungsverzeichnis zu suchen. Unter Exponential Velocity werden die Einstellungsdateien eines Siteaccess einmal pro Worker gebaut. |
| Wer betroffen ist | Jede Installation, ohne dass etwas zu tun ist: Der Container wird vom Deployment und von `ezcache.php --clear-tag=ini` kompiliert und zeichnet sich sonst bei der ersten Web-Anfrage eines Siteaccess selbst auf. Code, der INI-Zustand zurücksetzt oder einspeist, funktioniert weiter. |
| Was gleich bleibt | Ohne kompilierten Container, mit `[ContainerSettings] Mode=dev` oder `Mode=disabled` oder immer dann, wenn eine Anfrage nicht von genau dem aufgezeichneten Zustand ausgeht, startet der Kernel genau wie bisher. Jeder Aufruf `eZ*::instance()` funktioniert weiter. |
| Wie Sie es prüfen | `./console exp:services:check` (Exit-Code 1 bei einem Fehler), `./console exp:services:list`, `./console exp:services:show <id>` |
| Wie Sie einen veralteten Container reparieren | `./console exp:services:compile` oder `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` |

Nicht zu verwechseln mit `ezpKernelWeb::getServiceContainer()` bzw. der Kernel-Einstellung `'service-container'`, dem
eingespeisten Symfony-Container der alten Legacy-Bridge, der seine Bedeutung behält, und auch nicht mit der Erweiterung
`expservices`.

## Warum

Der Konstruktor des Web-Kernels erledigte bei jeder Anfrage dieselbe Verdrahtung:
`eZExtension::activateExtensions( 'default' )` suchte jede der 62 aktiven Erweiterungen dieser Installation auf der Platte (ein Verzeichnis-Scan pro
Erweiterung), der Siteaccess wurde ermittelt, die Access-Erweiterungen wurden aktiviert, und dann wurde jedes bis dahin
gebaute Einstellungsobjekt verworfen und neu gebaut. Gemessen vor dieser Änderung (Phasenmarken in einer Kopie des
Konstruktors): Der Konstruktor brauchte 17 bis 23 ms einer frischen Anfrage, der Durchlauf der Standard-Erweiterungen
allein 8 bis 17 ms. Unter Velocity überlebte nichts davon eine Anfrage, weil der Pool nach jeder Anfrage jede statische
Variable zurücksetzt und jede globale Variable entfernt.

## Was er tut

### Der kompilierte Container ist eine Aufzeichnung, keine Nachahmung

`exp:services:compile` startet einen eigenen PHP-Prozess (`bin/php/services.php --kernel-services-record`), der den
echten Web-Kernel einmal pro Siteaccess (`site.ini [SiteAccessSettings] AvailableSiteAccessList`) im Aufzeichnungsmodus
startet, so wie es die erste Anfrage eines frischen Prozesses täte. Für jeden der beiden Durchläufe von
`eZExtension::activateExtensions()` zeichnet er auf:

- die globalen INI-Override-Verzeichnisse **vor** dem Durchlauf,
- was der Durchlauf außer ihnen liest (`[ExtensionSettings] ActiveExtensions`, `ExtensionDirectory`,
  `AdditionalExtensionDirectories`, `ExtensionOrdering`, und für den Access-Durchlauf `ActiveAccessExtensions` sowie
  den aktuellen Siteaccess),
- die Override-Verzeichnisse **nach** dem Durchlauf sowie die Caches für Erweiterungsnamen, -pfade und die aktive Liste.

Er schreibt `var/cache/kernelservices/index.php` (den Standard-Durchlauf, für jeden Siteaccess gleich, und eine neue
**Generation**) und eine `<siteaccess>.php` pro Siteaccess (ihren Access-Durchlauf sowie die Dienstdefinitionen und
Tags von `kernelservices.ini`, wie dieser Siteaccess sie sieht). Jede Datei ist ein Array, das mit `return [...]`
zurückgegeben wird, und wird aus dem Opcode-Cache geliefert.

### Eine Anfrage wendet eine Aufzeichnung nur von genau dem aufgezeichneten Zustand aus an

`expKernelServices::bootWeb()` (vom Konstruktor von `ezpKernelWeb` aufgerufen, bevor er irgendeine Einstellung liest)
liest den Index. Wird `eZExtension::activateExtensions()` für die globale `site.ini` mit gestartetem Container
aufgerufen, fragt es zuerst `expKernelServices::applyExtensionStage()`. Das wendet die Aufzeichnung an (setzt die
Override-Verzeichnisse, füllt die Erweiterungs-Caches, lädt `site.ini` neu) **nur dann**, wenn die
Override-Verzeichnisse vor dem Durchlauf und die Einstellungen, die der Durchlauf liest, genau die aufgezeichneten sind,
zur Laufzeit keine Erweiterung hinzugefügt wurde (`$GLOBALS['eZActiveExtensions']`) und keine Einstellungen in den
Kernel eingespeist wurden. Andernfalls antwortet es mit nein, und der Durchlauf läuft wie immer.

Ein veralteter Container kann also nicht ändern, was eine Anfrage sieht: Ein geändertes `ActiveExtensions`, ein anderes
Erweiterungsverzeichnis, eine eingespeiste Einstellung oder eine ungewöhnliche Aufrufreihenfolge fallen alle auf den
Live-Durchlauf zurück. Was eine Aufzeichnung nicht sehen kann, ist die Platte: ein Erweiterungsverzeichnis, das
hinzugefügt oder entfernt wurde, während die Einstellungen gleich blieben. Dafür ist das Deployment da (es kompiliert
neu), wie auch für die Autoload-Maps.

### AutoRebuild: Ein Siteaccess zeichnet sich selbst auf

Mit `[ContainerSettings] AutoRebuild=enabled` (der Standard) führt eine Web-Anfrage, deren Siteaccess keine aktuelle
kompilierte Datei hat, die Durchläufe live aus, zeichnet sie auf und schreibt die Datei (und einen neuen Index, wenn auch
der Standard-Durchlauf aufgezeichnet werden musste). Die nächste Anfrage dieses Siteaccess wendet sie an. Das deckt die
erste Anfrage nach einer Installation ab und ein Leeren des INI-Caches aus der Administration, das die kompilierten
Dateien entfernt, aber keine PHP-Prozesse startet. Eine Datei, die existiert, aber nicht zu einer Anfrage passt, bleibt
unberührt (nur `exp:services:compile` ersetzt sie), so dass zwei Arten von Anfragen nie abwechselnd die Aufzeichnung der
anderen überschreiben können. Dateien werden unter einem temporären Namen geschrieben und umbenannt; eine Datei, die root
(Velocity) schreibt, wird dem Eigentümer von `var/` übergeben, damit PHP-FPM sie später ersetzen kann.
Kommandozeilen-Kernel (Tests, Skripte) schreiben nie.

### Der Worker-Gültigkeitsbereich der Einstellungsdateien (Velocity)

Mit `[ContainerSettings] WorkerScope=auto` (der Standard) und `EZP_INI_FILEMTIME_CHECK` false in `config.php` (auch der
INI-Cache schaut nicht auf Dateizeiten) hängt der Container zu zwei Zeitpunkten einen Speicher an `eZINI` an
(`eZINI::attachSharedStore()`): beim Start, solange die Override-Verzeichnisse die Standardwerte sind, und direkt nach
`eZINI::resetAllInstances( false )` im Konstruktor, wenn der Access-Durchlauf aus der Aufzeichnung angewendet wurde.
Solange er angehängt ist, behält `eZINI::instance()` eine unberührte Kopie jedes Einstellungsobjekts, das es baut, und
gibt danach Kopien aus; eine Anfrage, die ihr Objekt ändert, ändert nur ihre Kopie.

Der Speicher ist ein Objekt in `$GLOBALS['expKernelServicesWorker']` (`expKernelServiceScope::WORKER_GLOBAL`), das
`expVelocity::DEFAULT_KEEP_GLOBALS` über Anfragen hinweg behält. Ein Velocity-Worker baut also jede Einstellungsdatei
eines Siteaccess einmal statt bei jeder Anfrage. Unter PHP-FPM dauert ein Worker eine Anfrage lang, und nichts ändert
sich.

Der Speicher wird abgehängt (das nächste `instance()` liest die Dateien wieder) durch jede Änderung der globalen
Override-Verzeichnisse (`prependOverrideDir()`, `appendOverrideDir()`, `removeOverrideDir()`, `setOverrideDirs()` auf
einer globalen Instanz, `resetGlobalOverrideDirs()`) und durch `resetAllInstances()`; `resetInstance()` verwirft die
Kopien dieser Datei; `save()` und das Leeren des INI-Caches leeren ihn. Eine neue Generation (ein Kompilieren, ein
aufgezeichneter Index) leert den Speicher des Workers bei seiner nächsten Anfrage. Der Schlüssel ist der Siteaccess und
der Hash der Override-Verzeichnisse: Ein Worker, der mehrere Siteaccesses bedient (Zuordnung über host_uri), gibt nie
die Einstellungen einer Website an eine andere.

### Request-Gültigkeitsbereich: die sieben Zurücksetzungen an einer Stelle

Der Konstruktor setzte sieben Zustände der Anfrage von Hand zurück. Sie sind jetzt die Reset-Hooks des
Request-Gültigkeitsbereichs, `expKernelServiceScope::DEFAULT_RESET_HOOKS`, ausgeführt von
`expKernelServiceScope::resetRequest()` an derselben Stelle, in derselben Reihenfolge, jeder nur, wenn seine Klasse
existiert, wie bisher: `eZDBQueryCache::resetRequest`, `eZDBInterface::resetSQLProfile`,
`expContentSecurityPolicy::reset`, `expAudit::resetRequest`, `expRenderStats::resetRequest`, `expRequestLog::reset`,
`expRequestClassifier::reset` und seit 6.0.15 `expUserApproval::resetRequest` (die Konfigurationen von "Benutzerkonten
freigeben", die ein Worker zuvor behielt). Code fügt eigene mit
`expKernelServiceScope::onRequestReset( 'name', $callable )` hinzu (ein erneut registrierter Name ersetzt den alten; er bleibt beim Worker und läuft unter Velocity also
auch am Anfang der nächsten Anfrage).

## Die Dienste

`settings/kernelservices.ini`, ein Block `[Service_<id>]` pro Dienst:

| Einstellung | Bedeutung |
|---|---|
| `Class` | die Klasse (oder das Interface) des Dienstes |
| `Factory` | `Class::method`, die aufgerufen wird; ohne sie `new Class( Arguments )` |
| `Arguments[]` | `@<id>` ein anderer Dienst, `%ini:<file>/<Section>/<Variable>%` eine Einstellung, alles andere so, wie es geschrieben ist (`@@`, `%%` maskieren ein führendes `@` oder `%`) |
| `Scope` | `worker` (einer pro Worker und Siteaccess; darf nichts von einer Anfrage halten), `request` (der Standard), `prototype` |
| `Lazy` | `true` (der Standard): erzeugt, wenn zum ersten Mal danach gefragt wird |
| `Tags[]` | Namen, die Dienste sammeln (`expKernelServices::tagged( '<tag>' )`) |
| `Description` | eine Zeile für `exp:services:list` / `show` |
| `Enabled` | `false` schaltet einen Dienst ab |

Ausgeliefert: `db` (`eZDB::instance()`, request), `http` (`eZHTTPTool::instance()`, request), `ini.site` und `ini.i18n`
(nur zur Dokumentation: jedes `ini.<name>` ist `eZINI::instance( '<name>.ini' )`), `extensions.overrides`
(`expKernelExtensionOverrideList`, worker: die kompilierten Override-Verzeichnisse und Erweiterungspfade des Siteaccess
und ob diese Anfrage sie angewendet hat). `[Tags] TagList[]` listet die Tags; `exp:services:check` warnt bei anderen.

```php
$db = expKernelServices::get( 'db' );
$site = expKernelServices::get( 'ini.site' );
foreach ( expKernelServices::tagged( 'kernel.core' ) as $id => $service ) { /* ... */ }
expKernelServices::set( 'my.thing', $object );   // this request only, never compiled or kept
```

Eine Erweiterung fügt Dienste in `extension/<name>/settings/kernelservices.ini.append.php` hinzu. Führen Sie danach
`./console exp:services:compile` aus (oder rollen Sie aus); `exp:services:check` prüft die Definitionen jedes
Siteaccess: unbekannter Gültigkeitsbereich, fehlende Klasse oder Factory, ein Argument, das keinen Dienst nennt, Zyklen,
ein Dienst mit Gültigkeitsbereich worker, der einen mit Gültigkeitsbereich request nimmt (er würde das Objekt einer
Anfrage für jede spätere Anfrage behalten), ein Dienst `ini.<name>`, der kein `eZINI` ist, nicht deklarierte Tags.

## Einstellungen

```ini
[ContainerSettings]
Mode=compiled          # compiled (default) | dev (nothing compiled is used) | disabled (no part in the boot)
CacheDir=var/cache/kernelservices
AutoRebuild=enabled    # a web request records a siteaccess that has no current compiled file
WorkerScope=auto       # auto (on when EZP_INI_FILEMTIME_CHECK is false) | enabled | disabled
```

`[ContainerSettings]` wird gelesen, bevor die Erweiterungen aktiv sind: Setzen Sie es in `settings/kernelservices.ini`
oder `settings/override/kernelservices.ini.append.php`.

## Konsole

```bash
./console exp:services:compile [--only=site,admin]   # record every siteaccess (or these) and write the container
./console exp:services:list [--tag=<tag>] [-s <siteaccess>]
./console exp:services:show <id> [-s <siteaccess>]
./console exp:services:check                         # definitions + compiled files; exit 1 on an error
```

Alle vier sind `bin/php/services.php` (`exp:services`), das Verb wird aus dem Namen genommen, unter dem es aufgerufen
wurde; `--json` gibt das Ergebnis als JSON aus.

## Wann er neu gebaut wird

| Ereignis | Was geschieht |
|---|---|
| `exp:velocity deploy` | Schritt 3 (`ezcache.php --clear-tag=ini`) entfernt und kompiliert ihn; die neue Generation erreicht jeden Velocity-Worker bei seiner nächsten Anfrage |
| `php bin/php/ezcache.php --clear-tag=ini` oder `--clear-all` | der Cache-Eintrag `kernelservices` (Tag `ini`, der letzte der INI-Caches) entfernt ihn und kompiliert ihn auf der Kommandozeile neu |
| INI-Cache in der Administration geleert | die Dateien werden entfernt; jeder Siteaccess zeichnet sich bei seiner nächsten Anfrage selbst auf (AutoRebuild) |
| `./console exp:services:compile` | neu kompiliert |
| Eine Einstellungsdatei von Hand bearbeitet | wie heute beim INI-Cache: Leeren Sie den INI-Cache (mit `EZP_INI_FILEMTIME_CHECK` false bemerkt sonst nichts die Änderung) |

## Diese Änderung ausrollen

Der Teil fügt Kernel-Klassen hinzu: Rollen Sie mit `exp:velocity deploy --kernel --allow-root-user` aus (Kernel-Autoloads,
Engine-Archiv, PHP-FPM, Neustart von Velocity). Ein Velocity-Worker, der noch mit einer älteren Klassen-Map läuft, kennt
die neuen Klassen nicht; der Konstruktor lädt dann die Klasse des Request-Gültigkeitsbereichs über ihren Pfad und lässt
den Container weg (Wächter mit `class_exists()`), so dass sich ein veralteter Worker bis zum Neustart wie bisher verhält.

## Gemessen

Auf der Testinstallation (62 aktive Erweiterungen, 18 Siteaccesses, SQLite), 21. Oktober 2026, Last des Hosts 4 bis 8 auf
12 Kernen, `nice -n 15`. Fünf Anfragen (eine gecachte und eine ungecachte öffentliche Seite, das Dashboard der
Administration, eine große Hilfeseite, ein JSON-Aufruf) liefen auf der Kommandozeile durch den Kernel, mit Phasenmarken in
einer Kopie des Konstruktors, in zwei Wegwerf-Installationen neben der Live-Website, die sich nur in dieser Änderung
unterscheiden: der Code davor und der Code damit und mit einem kompilierten Container. Mediane aus 10, vorher und nachher
abwechselnd.

- **Wie PHP-FPM**: ein Kernel pro PHP-Prozess, OPcache-Dateicache (einmal kompiliert).
- **Wie Velocity**: ein Prozess, 11 Anfragen hintereinander, wobei das, was der Pool von Velocity zwischen Anfragen tut,
  nachgebildet wird (jede statische Variable zurück auf ihren Schnappschuss oder deklarierten Standardwert, jede globale
  Variable außer der Behalten-Liste entfernt, eine neue Anfragezeit); die erste Anfrage nicht mitgezählt.

Konstruktor = `ezpKernelWeb::__construct()`; Start = vom Prozessstart bis zum Lauf des Moduls (Konstruktor,
Initialisierung der Anfrage, Pre-Dispatch).

| Anfrage | PHP-FPM Konstruktor | PHP-FPM Start | Velocity Konstruktor | Velocity Start |
|---|---|---|---|---|
| gecachte Seite `/` | 25.6 -> 13.4 ms (-48%) | 49.4 -> 34.2 ms (-31%) | 22.3 -> 5.4 ms (-76%) | 30.8 -> 14.0 ms (-55%) |
| ungecachte Seite | 21.6 -> 13.1 ms (-40%) | 41.9 -> 35.6 ms (-15%) | 19.8 -> 5.1 ms (-74%) | 27.4 -> 13.5 ms (-51%) |
| Dashboard der Administration | 23.1 -> 12.6 ms (-46%) | 38.3 -> 26.5 ms (-31%) | 18.2 -> 4.7 ms (-74%) | 20.8 -> 6.8 ms (-67%) |
| Hilfeseite | 30.0 -> 13.9 ms (-54%) | 49.5 -> 32.3 ms (-35%) | 22.9 -> 5.1 ms (-78%) | 26.3 -> 7.5 ms (-72%) |
| JSON-Aufruf | 25.8 -> 12.9 ms (-50%) | 41.8 -> 27.8 ms (-34%) | (beendet den Prozess: so nicht messbar) | |

Die größte einzelne Einsparung ist der Durchlauf der Standard-Erweiterungen (vorher 8 bis 17 ms, danach etwa 0,5 ms:
keine Verzeichnissuchen). Unter Velocity entfernen die behaltenen Einstellungsdateien den größten Teil des restlichen
Konstruktors. Ganze Anfragen: wenige Prozent unter PHP-FPM; unter Velocity wurden die nachgebildeten Anfragen auch
schneller gerendert (die gecachte Seite 272 -> 206 ms insgesamt), aber diese Summen schwanken mit dem Inhalt und werden
hier nicht behauptet.

Ein Regressionsdurchlauf für Velocity auf dieselbe Weise: Ein nachgebildeter Worker lieferte `/`, `/bold`, `/classic`,
`/starter`, `/admin/user/login`, die Anmeldung des Editor-Hosts, `/Fit-Healthy`, `/bold/user/login` und eine Seite mit
Query-String jeweils zweimal; jede Seite stimmte mit der Seite überein, die ein frischer Prozess für dieselbe Anfrage
renderte, mit dem richtigen Siteaccess pro Anfrage (site, bold, classic, starter, admin, editor) und beiden angewendeten
Durchläufen. Der eine Unterschied, `/starter`, ist ein zufällig gewählter Teaser und unterschied sich vor der Änderung
auf dieselbe Weise.

Über HTTP vor der Änderung (von einem anderen Host, Mediane aus 10): eine ungecachte öffentliche Seite 176 ms
Serverzeit unter PHP-FPM und 178 ms unter Velocity, das Dashboard der Administration 93 und 161 ms. Die HTTP-Werte
danach lassen sich erst messen, wenn die Änderung ausgerollt ist.

## Klassen

| Klasse | Datei | Was |
|---|---|---|
| `expKernelServices` | `kernel/classes/expkernelservices.php` | die Fassade (`get`, `has`, `tagged`, `set`, `reset`) und der kompilierte Start des Web-Kernels |
| `expKernelServiceContainer` | `kernel/classes/expkernelservicecontainer.php` | Definitionen, Gültigkeitsbereiche, Argumente; keine Reflection zur Laufzeit |
| `expKernelServiceScope` | `kernel/classes/expkernelservicescope.php` | worker / request / prototype, der Worker-Speicher, die Reset-Hooks der Anfrage |
| `expKernelServiceCompiler` | `kernel/classes/expkernelservicecompiler.php` | Definitionen aus der INI, Prüfung, Aufzeichnung, kompilierte Dateien, der Cache-Eintrag |
| `expKernelExtensionOverrideList` | `kernel/classes/expkernelextensionoverridelist.php` | der Dienst `extensions.overrides` |
| `Exponential\Command\Kernel\Services` | `kernel/private/classes/commands/services.php` | `exp:services:*` |

Hooks in vorhandenem Code: `eZINI` (`attachSharedStore()`, `detachSharedStore()`, `releaseSharedStore()`,
`flushSharedStore()`, `sharedStoreState()`; `instance()` verwendet den Speicher, solange er angehängt ist),
`eZExtension` (`activateExtensions()` fragt den gestarteten Container; `exportCaches()`, `seedCaches()`,
`clearCaches()`), `ezpKernelWeb::__construct()` (Request-Gültigkeitsbereich, `bootWeb()`, `enterSiteAccess()`),
`eZCache` (Eintrag `kernelservices`), `expVelocity::DEFAULT_KEEP_GLOBALS` (`expKernelServicesWorker`),
`expCacheCatalogue`.

## Nicht in diesem Teil

Der eigene Start der Konsole (eine kompilierte Befehls-Map, Befehle im Prozess der Konsole ausgeführt), verzögerte
Event-Listener und die Erkennung mobiler Geräte, Tags für Template-Operatoren sowie Design- und Override-Suchen mit
Worker-Gültigkeitsbereich unter Velocity sind die nächsten Kandidaten, in dieser Reihenfolge des gemessenen Nutzens.
`eZScript` (die Kommandozeile) startet den Container noch nicht.

## Tests

`tests/tests/kernel/classes/expKernelServicesTest.php` (ohne Datenbank, Suite `kernel-classes`): Definitionen aus der
INI, Prüfung (und dass die ausgelieferte `kernelservices.ini` keinen Fehler hat), Gültigkeitsbereiche, Argumente und
Fehler, die Reset-Hooks der Anfrage und ihre Reihenfolge, der Worker-Speicher von eZINI (anhängen, Kopien, abhängen bei
jeder Änderung, leeren), kompilierte Dateien (Stempel, Generation, Format), dass `applyExtensionStage()` nur vom
aufgezeichneten Zustand aus anwendet, die Verdrahtung des Kernels und dass sich ohne gestarteten Container nichts ändert.
