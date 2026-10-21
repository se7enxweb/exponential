---
title: Kernel-Architektur
audience: developer, admin
category: standards
order: 200
summary: Wie eine Anfrage durch den Kernel von Exponential läuft und welche Regeln für jeden Baustein gelten, nämlich Module, Richtlinien, Datentypen, Persistenz, Einstellungen, Designs, Templates, Caches, Ereignisse und die Server.
keywords: Architektur, Kernel, Modul, Ansicht, Richtlinie, Rolle, Einschränkung, Datentyp, Persistenz, Einstellungen, Siteaccess, Überschreibung, Template, Operator, Fetch-Funktion, Extension, Cache, Workflow, Cronjob, Benachrichtigung, Ereignis, Konsole, architecture, kernel, ezpKernel, ezpKernelWeb, module, view, policy, role, limitation, datatype, eZPersistentObject, eZINI, siteaccess, override, template, operator, fetch, extension, cache, workflow, cronjob, notification, eZDB, ezpEvent, console, Velocity, PHP-FPM, FrankenPHP
type: reference
---
# Kernel-Architektur

Diese Seite richtet sich an Entwickler, die Exponential 6.0.x erweitern oder ändern, und an Administratoren, die wissen
müssen, zu welchem Teil des Systems eine Einstellung oder ein Problem gehört. Sie beantwortet zwei Fragen: was zwischen
einer Anfrage des Browsers und dem zurückkommenden HTML geschieht und, für jeden Baustein des Kernels, wo er liegt, wie
er erweitert wird und welcher Regel eine Änderung folgen muss. Die Regeln verwenden **muss** für das, was eine Änderung
nicht kaputt machen darf, und **sollte** für den erwarteten Standard. Jeder Abschnitt endet mit den bekannten Ausnahmen.

Die Codebasis wurde von eZ Publish 4 übernommen und behält deren Bezeichner (`eZINI`, `eZContentObject`,
`eZPersistentObject`); alles, was seitdem hinzukam, verwendet das Präfix `exp` (siehe
[Regeln für Schichten und Benennung](schichten-und-benennung.md)).

## Die Schichten im Überblick

```text
 Browser / CLI / cron
        |
 +------v-------------------------------------------------------------+
 | Server: Apache or nginx + PHP-FPM  |  Velocity (qbix)  |  FrankenPHP |
 +------+-------------------------------------------------------------+
        |  index.php  /  bin/php/console  /  runcronjobs.php
 +------v-------------------------------------------------------------+
 | ezpKernel -> ezpKernelWeb  (kernel/private/classes/)               |
 |   settings (eZINI) -> extensions -> siteaccess -> session -> module |
 +------+-------------------------------------------------------------+
        |  eZModule::run()  -> view class  (kernel/<module>/, extensions)
 +------v-------------------------------------------------------------+
 | Domain: kernel/classes  (content, users, roles, datatypes, caches)  |
 |   eZPersistentObject  |  eZDataType  |  eZWorkflowEventType  | ...  |
 +------+-----------------------------+-------------------------------+
        |                             |
 +------v------------+   +-----------v----------------------------------+
 | lib/ezdb (eZDB)   |   | Presentation: lib/eztemplate (eZTemplate),    |
 | lib/ezutils, ...  |   | design/<design>/templates, override.ini       |
 +-------------------+   +----------------------------------------------+
```

## Der Lebenszyklus einer Anfrage

Was `index.php` der Reihe nach tut (alle Pfade sind relativ zum Wurzelverzeichnis des Repositorys):

1. **Hooks für Wartung und Reparatur.** Wenn `var/maintenance.json` existiert, darf `expMaintenance::check()`
   (`kernel/classes/expmaintenance.php`) die Anfrage beantworten. Eine Anfrage mit `exp_repair` wird an
   `ezpRepairQueue` (`lib/ezutils/classes/ezprepairqueue.php`) übergeben.
2. **Autoload.** `autoload.php` lädt die generierten Klassen-Maps (`autoload/ezp_kernel.php` und die Map der Extensions).
3. **Kernel.** `new ezpKernel( new ezpKernelWeb() )` und `$kernel->run()->getContent()`. `ezpKernel`
   (`kernel/private/classes/ezpkernel.php`) ist eine dünne Hülle um einen Handler, der
   `ezpWebBasedKernelHandler` (`kernel/private/interfaces/`) implementiert. Der Web-Handler ist `ezpKernelWeb`
   (`kernel/private/classes/ezpkernelweb.php`); er liefert ein `ezpKernelResult`.
4. **Konstruktor von `ezpKernelWeb`.** Liest `i18n.ini` und `site.ini`, setzt Zeitzone und Dateiberechtigungen,
   initialisiert `eZSys`, aktiviert die globalen Extensions (`eZExtension::activateExtensions( 'default' )`), ermittelt
   den Siteaccess (`eZSiteAccess::match()` auf `eZURI::instance( eZSys::requestURI() )`, sofern keiner injiziert wird),
   wechselt zu ihm (`eZSiteAccess::change()`), aktiviert dann die Extensions des Siteaccess
   (`eZExtension::activateExtensions( 'access' )`) und setzt die INI-Instanzen zurück, damit die Einstellungen des
   Siteaccess gelten.
5. **`run()` und `dispatchLoop()`.** Initialisierung von Sitzung und Anfrage (`sessionInit()`, `requestInit()`), die
   Ereignisse `request/preinput` und `request/input`, Übersetzung von URL-Aliassen und Wildcards (`eZURLAliasML`,
   `eZURLWildcard`), die Modulprüfung (`eZModule::accessAllowed()`), `fetchModule()`, die Anmeldeprüfung des
   Siteaccess (Richtlinie `user/login`), die Zugriffsprüfung der Ansicht (`eZUser::hasAccessToView()`) und schließlich
   `$module->run( $functionName, $params, ... )`. Eine Ansicht kann einen erneuten Durchlauf mit einer anderen URI oder
   eine Weiterleitung anfordern (`eZModule::STATUS_RERUN`, `STATUS_REDIRECT`); die Schleife behandelt beides.
6. **Seitenlayout und Ausgabe.** Das Ergebnis des Moduls wird in das Seitenlayout eingesetzt (`pagelayout.tpl`, sofern
   die Ansicht kein anderes setzt), durch `eZDisplayResult()` und die Ausgabefilter `response/preoutput` und
   `response/output` (`kernel/private/classes/global_functions.php`) geleitet und zurückgegeben.
7. **Beenden.** `shutdown()` schreibt ausstehende Daten; unter Velocity setzt der Worker seinen Zustand für die nächste
   Anfrage zurück.

Regeln:

- Eine Änderung am Lebenszyklus **muss** unter allen drei Servern (PHP-FPM, Velocity, FrankenPHP) weiter funktionieren.
  Code in `ezpKernelWeb` schützt neuere Klassen mit `class_exists()`, weil ein lang laufender Velocity-Worker seine
  Klassen-Map geladen haben kann, bevor die Klasse existierte; neue Kernel-Hooks **sollten** dasselbe tun.
- Zustand pro Anfrage **darf nicht** ohne Zurücksetzen in statischen Eigenschaften gehalten werden, weil Velocity den
  Prozess wiederverwendet ([Worker-Pool, Zurücksetzen zwischen Anfragen](../../spezifikationen/6.0/velocity-worker-pool.md)).

Kommandozeilenskripte und Cronjobs verwenden `ezpKernelWeb` nicht: Sie starten über `eZScript` (siehe
[Die Konsole](#die-konsole) und [Cronjobs](#cronjobs)).

## Module, Ansichten und Funktionen

**Was es ist.** Ein Modul ist ein URL-Namensraum (`content`, `user`, `exphelp`); eine Ansicht ist eine Seite davon
(`content/view`); eine Funktion ist eine Berechtigung, die die Ansichten verlangen.

**Wo es liegt.** `kernel/<module>/module.php` definiert `$Module`, `$ViewList` (jede Ansicht: `script`, `params`,
`functions`, `default_navigation_part`, ...) und `$FunctionList` (Richtlinienfunktionen und ihre Einschränkungen). Die
nach Modulen durchsuchten Repositorys sind `settings/module.ini [ModuleSettings] ModuleRepositories[]` (`kernel`,
`kernel/private/modules`), und erreichbar sind nur Module in `ModuleList[]`. Seit 6.0.15 ist der Code einer Ansicht
eine Klasse: `kernel/content/view.php` ist eine Zeile, die `\Exponential\View\Kernel\Content\View::main()` in
`kernel/private/classes/views/content/view.php` aufruft, einer Unterklasse von `Exponential\Runnable\ModuleView`
(`kernel/private/classes/runnable/moduleview.php`).

**Erweiterungspunkt.** Eine Extension fügt Module hinzu, indem sie sich in
`module.ini [ModuleSettings] ExtensionRepositories[]` einträgt und `extension/<ext>/modules/<module>/module.php`
mitliefert. Eine Ansichtsklasse lässt sich über `site.ini [RunnableSettings] Implementation[]` ersetzen, und ein ganzes
Kernel-Modul kann durch eine Extension ersetzt werden
([Modulüberschreibung durch Extensions](../../funktionen/6.0/modulueberschreibung-durch-extensions.md)).

**Regel.**

- Jede Ansicht **muss** die Richtlinienfunktionen, die sie braucht, in `functions` nennen; eine Ansicht ohne sie steht
  jedem Benutzer offen, der die Prüfung des Siteaccess besteht.
- Neue Ansichten **sollten** als Unterklassen von `ModuleView` geschrieben werden, nicht als einfache Skriptdateien
  ([Befehle, Cronjob-Teile und Modulansichten als Klassen](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md),
  [Spezifikation](../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md)).

**Ausnahmen.** Ansichten als einfache Dateien laufen weiterhin (der Kernel behält diesen Weg für Extensions bei).
Ansichten, die in `site.ini [RoleSettings] PolicyOmitList[]` stehen, überspringen die Richtlinienprüfung absichtlich.

## Richtlinien, Rollen und Einschränkungen

**Was es ist.** Eine Rolle (`eZRole`, `kernel/classes/ezrole.php`) ist eine Menge von Richtlinien (`eZPolicy`,
`kernel/classes/ezpolicy.php`); eine Richtlinie gewährt eine Modulfunktion, optional durch Einschränkungen begrenzt
(`eZPolicyLimitation`, `eZPolicyLimitationValue`). Rollen werden Benutzern oder Gruppen zugewiesen, optional begrenzt
auf einen Teilbaum oder eine Sektion.

**Wo es liegt.** Die Einschränkungen, die eine Funktion akzeptiert, werden in `$FunctionList` des Moduls deklariert:
Beispielsweise definiert `kernel/content/module.php` `$ClassID = array( 'name' => 'Class', 'class' => 'eZContentClass',
'function' => 'fetchList', ... )` und verwendet es in `$FunctionList['read']`. Die Prüfung erfolgt über
`eZUser::hasAccessTo( $module, $function )` und `eZUser::hasAccessToView()`
(`kernel/classes/datatypes/ezuser/ezuser.php`); Templates verwenden `fetch( 'user', 'has_access_to', ... )`
(`kernel/user/function_definition.php`).

**Erweiterungspunkt.** Neue Funktionen und Definitionen von Einschränkungen im `module.php` einer Extension; eigene
Inhaltseinschränkungen über Limitation-Handler
([Handler für Inhaltseinschränkungen](../../funktionen/6.0/handler-fuer-inhaltseinschraenkungen.md)).
Kernel-Code, der eine Entscheidung von außen braucht, verwendet `ezpEvent`-Filter wie `content/edit/access`
(`kernel/classes/ezcontentobject.php`) und `content/download/access`.

**Regel.** Über den Zugriff **müssen** Richtlinien entscheiden, nie eine Prüfung einer Benutzer- oder Gruppen-ID in
Code oder Templates. Ein neues Modul **sollte** seine Funktion in ein Installations- oder Aktualisierungsskript
aufnehmen, damit bestehende Sites sie erhalten (zum Beispiel `bin/php/addexphelpreadpolicy.php` für `exphelp/read`).
Anleitung: [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md).

## Datentypen

**Was es ist.** Ein Datentyp speichert, validiert, bearbeitet und indiziert eine Art von Inhaltsattribut (Textzeile,
Bild, Relationsliste).

**Wo es liegt.** Unterklassen von `eZDataType` (`kernel/classes/ezdatatype.php`) in
`kernel/classes/datatypes/<name>/<name>type.php`; jede Datei registriert sich selbst, z. B.
`eZDataType::register( eZStringType::DATA_TYPE_STRING, 'eZStringType' )`. Die Suche wird in
`settings/content.ini [DataTypeSettings]` konfiguriert: `RepositoryDirectories[]=kernel/classes/datatypes`,
`ExtensionDirectories[]` und `AvailableDataTypes[]` (35 ausgelieferte Einträge). Die Templates für Anzeige und
Bearbeitung liegen in `design/standard/templates/content/datatype/`.

**Erweiterungspunkt.** Eine Extension trägt ihren Namen in `ExtensionDirectories[]` und ihren Datentyp in
`AvailableDataTypes[]` in ihrer eigenen `content.ini.append.php` ein.

**Regel.** Ein Datentyp **muss** Eingaben in `validateObjectAttributeHTTPInput()` validieren und Ausgaben in seinen
Templates maskieren; er **darf** das gespeicherte Format bestehender Attribute **nicht** ohne Aktualisierungsskript
ändern. Hintergrund: [Härtung der Eingaben von Datentypen](../../spezifikationen/6.0/datentyp-eingabehaertung.md).

## Persistente Objekte

**Was es ist.** Die objektrelationale Schicht des Kernels: eine PHP-Klasse pro Tabelle.

**Wo es liegt.** `eZPersistentObject` (`kernel/classes/ezpersistentobject.php`). Eine Unterklasse liefert ihre
Tabellenbeschreibung aus `static function definition()` (`fields`, `keys`, `increment_key`, `class_name`, `name`,
`function_attributes`); Lesezugriffe verwenden `eZPersistentObject::fetchObject()` und `fetchObjectList()`,
Schreibzugriffe `store()` und `remove()`.

**Regel.** Neue Tabellen **sollten** eine persistente Klasse erhalten statt handgeschriebenem SQL; wo SQL nötig ist,
**müssen** Werte mit `$db->escapeString()` maskiert und Ganzzahlen gecastet werden. Schemaänderungen **müssen**
`share/db_schema.dba`, die Aktualisierungsskripte in `update/database/<driver>/6.0/` und jeden unterstützten Treiber
erreichen (siehe [Kompatibilität, Releases und Deployment](kompatibilitaet-releases-deployment.md)).

## Die Kaskade der INI-Einstellungen

**Was es ist.** Die gesamte Konfiguration besteht aus INI-Dateien, die über `eZINI`
(`lib/ezutils/classes/ezini.php`) gelesen werden.

**Wo sie liegen und in welcher Reihenfolge.** Die Standardwerte stehen in `settings/<file>.ini`.
`eZINI::overrideDirs()` führt dann die Überschreibungsverzeichnisse nach Geltungsbereich in dieser Reihenfolge
zusammen, wobei eine spätere Datei gewinnt:

| Reihenfolge | Geltungsbereich | Verzeichnisse |
|---|---|---|
| 1 | `sa-extension` | Siteaccess-spezifische Verzeichnisse in Extensions (`extension/<ext>/settings/siteaccess/<sa>/` und verwandte) |
| 2 | `siteaccess` | `settings/siteaccess/<sa>/` |
| 3 | `extension` | `extension/<ext>/settings/` jeder aktiven Extension |
| 4 | `override` | `settings/override/` |

Überschreibungsdateien heißen `<file>.ini.append.php` (die Endung `.php` verhindert, dass sie als Text ausgeliefert
werden). Extensions werden in `site.ini [ExtensionSettings] ActiveExtensions[]` (global) und
`ActiveAccessExtensions[]` (pro Siteaccess) eingeschaltet. Beachten Sie die Reihenfolge: Das `settings/` einer
Extension gewinnt gegenüber `settings/siteaccess/<sa>/`, und `settings/override/` gewinnt gegenüber allem. Die
vollständige Referenz ist [INI-Überschreibungsverzeichnisse und Platzierungen](../../spezifikationen/6.0/ini-override-platzierungen.md);
Site-spezifische Einstellungen in Extensions beschreibt
[INI-Überschreibungen für mehrere Sites](../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md).

**Erweiterungspunkt.** Jede Extension kann `settings/*.ini.append.php` mitliefern; `./console exp:ini` liest und
schreibt jeden Geltungsbereich ([exp:ini](../../bc/6.0/console-exp-ini.md)).

**Regel.** Einstellungen **müssen** über `eZINI::instance( '<file>.ini' )` gelesen werden; kein Code in Kernel,
Bibliotheken oder Befehlen parst INI-Dateien selbst (eine Suche nach `parse_ini_file` und `parse_ini_string` in
`kernel/`, `lib/` und `bin/` findet nichts). Eine neue Einstellung **muss** ihren Standardwert mit einem Kommentar in
`settings/<file>.ini` haben. Geheimnisse gehören nach `settings/override/`, das nie committet wird.
`ActiveExtensions[]` und `ActiveAccessExtensions[]` lassen sich nicht aus einer Extension heraus setzen.

## Siteaccess

**Was es ist.** Ein Siteaccess ist eine benannte Konfiguration (eine Site, die Administrationsoberfläche, eine
Sprachversion), die pro Anfrage ausgewählt wird.

**Wo es liegt.** `eZSiteAccess` (`kernel/classes/ezsiteaccess.php`) mit den Abgleichstypen `TYPE_URI`,
`TYPE_HTTP_HOST`, `TYPE_PORT`, `TYPE_HTTP_HOST_URI`, `TYPE_SERVER_VAR` und weiteren. Konfiguration in
`site.ini [SiteAccessSettings]`: `MatchOrder` (ausgeliefert `uri;host;port`), `AvailableSiteAccessList[]`,
`RelatedSiteAccessList[]`, `HostMatchMapItems[]`, `AnonymousAccessList[]`. Abgleich nach Host und Sprache:
[Siteaccess-Abgleich nach Host und Sprache](../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md).

**Regel.** Nur Siteaccesses in `AvailableSiteAccessList[]` werden abgeglichen. Code **muss** den aktuellen Siteaccess
aus `eZSiteAccess::current()` nehmen, statt die URL selbst zu parsen.

## Design- und Überschreibungssystem

**Was es ist.** Ein Design ist ein Verzeichnis mit Templates, Stylesheets und Bildern; Designs werden gestapelt, sodass
eine Site nur enthält, was sich von der Basis unterscheidet.

**Wo es liegt.** `site.ini [DesignSettings]`: `SiteDesign`, `AdditionalSiteDesignList[]`, `StandardDesign`
(`standard`). Extensions fügen Designverzeichnisse über `design.ini [ExtensionSettings] DesignExtensions[]` hinzu.
`eZTemplateDesignResource` (`kernel/common/eztemplatedesignresource.php`) baut die Kette auf (zuerst die
Designverzeichnisse der Extensions, dann `design/<design>/`) und löst Überschreibungen aus Blöcken der `override.ini`
auf (`Source`, `MatchFile`, `Subdir`, `Match[...]`). Seit Juli 2026 werden Überschreibungen nach `Priority` geordnet
(niedrigere zuerst) ([Reihenfolge der Template-Überschreibungen](../../funktionen/6.0/reihenfolge-der-template-ueberschreibungen.md)). Die
Admin-Seite Design > Templates schreibt nur `settings/siteaccess/<sa>/override.ini.append.php`
(`ezpTemplateOverrides`, `kernel/private/classes/ezptemplateoverrides.php`).

**Regel.** Eine Site oder Extension **sollte** ein Template über `override.ini` oder ihr eigenes Designverzeichnis
überschreiben und **darf** Dateien in `design/standard/` oder `design/admin*/` des Kernels **nicht** bearbeiten.
Anleitung: [Templates und Design](../../anleitungen/templates-und-design.md).

## Die Template-Engine, Operatoren und Funktionen

**Was es ist.** `eZTemplate` (`lib/eztemplate/classes/eztemplate.php`) kompiliert `.tpl`-Dateien zu PHP und führt sie
aus.

**Wo es liegt.** Operatoren und Funktionen sind PHP-Klassen, die in `eztemplateautoload.php`-Dateien als Einträge von
`$eZTemplateOperatorArray` und `$eZTemplateFunctionArray` registriert werden. Die Engine lädt sie aus den Pfaden in
`site.ini [TemplateSettings] AutoloadPathList[]` (`lib/eztemplate/classes/`, `kernel/common/`, `lib/ezpdf/classes/`,
`kernel/private/eztemplate/`) und aus den in `ExtensionAutoloadPath[]` aufgeführten Extensions
(`extension/<ext>/autoloads/eztemplateautoload.php`). In `kernel/common/eztemplateautoload.php` registrierte Beispiele:
`ezurl`, `ezdesign`, `i18n`, `audit_label` (`expAuditTemplateOperator`), `exp_environment`, `csp_nonce`.

**Regel.** Ein Operator **muss** maskieren oder Daten liefern, die das Template mit `|wash` maskiert; in PHP erzeugtes
HTML **muss** dort maskiert werden. Templates **dürfen** kein SQL enthalten; Daten kommen aus Fetch-Funktionen und
Operatoren.

## Fetch-Funktionen

**Was es ist.** Der Aufruf `fetch( 'module', 'function', hash( ... ) )` in Templates.

**Wo es liegt.** `kernel/<module>/function_definition.php` ordnet jedem Fetch eine `call_method` zu (zum Beispiel
`eZContentFunctionCollection` in `kernel/content/function_definition.php`) und deklariert seine Parameter.
`eZFunctionHandler` (`lib/ezutils/classes/ezfunctionhandler.php`) führt sie aus; `settings/fetchalias.ini` definiert
Kurzformen für `fetch_alias()`.

**Regel.** Ein Fetch, der Inhalte liefert, **muss** die Berechtigungen des Benutzers anwenden (die Content-Fetches tun
das über `eZContentObjectTreeNode` und das SQL der Einschränkungen). Eine Extension fügt Fetches in
`extension/<ext>/modules/<module>/function_definition.php` hinzu.

## Extensions

**Was es ist.** Ein Plugin-Verzeichnis `extension/<name>/`, das Einstellungen, Module, Designs, Datentypen, Operatoren,
Workflow-Ereignisse, Cronjobs, Übersetzungen und Klassen enthalten kann.

**Wo es liegt.** `eZExtension` (`lib/ezutils/classes/ezextension.php`) aktiviert sie;
`site.ini [ExtensionSettings] ActiveExtensions[]` und `ActiveAccessExtensions[]` entscheiden, welche eingeschaltet sind
und in welcher Reihenfolge (Abhängigkeiten können das mit `ExtensionOrdering=enabled` entscheiden; siehe
[Ladereihenfolge der Extensions](../../funktionen/6.0/ladereihenfolge-der-extensions.md)). Die Identität steht in `ezinfo.php` und
`extension.xml`, gelesen von `expInfo` (`lib/ezutils/classes/expinfo.php`;
[Metadaten von Extensions](../../spezifikationen/6.0/extension-metadaten.md)). Klassen werden über die Klassen-Map der
Extensions gefunden, die `php bin/php/ezpgenerateautoloads.php -e` erzeugt.

**Regel.** Eine Extension **muss** beide Metadatendateien mit derselben Version mitbringen, **darf** keine
Kernel-Dateien ändern, um zu funktionieren, und **sollte** Verhalten über die Erweiterungspunkte auf dieser Seite
hinzufügen. Anleitung: [Extensions](../../anleitungen/extensions.md).

## Caches

**Was es ist.** Mehrere Caches, jeder mit einer ID und Tags, verwaltet von `eZCache` (`kernel/classes/ezcache.php`).

**Wo es liegt.** Zu den Cache-IDs in `eZCache` gehören `content` (Ansichts-Cache), `template` (kompilierte Templates),
`template-block`, `template-override`, `ini`, `global_ini`, `translation`, `design_base`, `active_extensions`,
`classid`, `urlalias`, `imagealias`, `user_info_cache`, `exphttpcache`, `querycache` und weitere; die Tags sind
`content`, `template`, `ini`, `i18n`, `image` und `user`. Das Caching der Inhaltsansichten steuert `site.ini [ContentSettings]
ViewCaching` (`enabled`); geleert wird pro Knoten durch `eZContentCacheManager` (`kernel/classes/ezcontentcachemanager.php`,
das auch das Ereignis `content/cache` auslöst). Die Ablaufzeitstempel der Caches werden in `expiry.php` im
Cache-Verzeichnis gehalten (`eZExpiryHandler`, `lib/ezutils/classes/ezexpiryhandler.php`). Alle Cache-Dateien werden
über den Cluster-Datei-Handler geschrieben: `eZClusterFileHandler` (`kernel/classes/ezclusterfilehandler.php`) mit
ausgeliefertem `file.ini [ClusteringSettings] FileHandler=eZFSFileHandler` oder `eZDFSFileHandler`
(`kernel/private/classes/clusterfilehandlers/`) für mehrere Webserver.

**Regel.** Cache-Dateien **müssen** über `eZClusterFileHandler::instance()` geschrieben werden, nicht mit direkten
Dateiaufrufen, damit geclusterte Installationen funktionieren. Geleert wird mit `bin/php/ezcache.php` oder
`./console exp:cache`; verwenden Sie für Einstellungen `--clear-tag=ini` ([Anleitung zu Caches](../../anleitungen/caches.md)).

## Workflows und Trigger

**Was es ist.** Workflows führen Ereignistypen vor oder nach einer Operation aus (Veröffentlichen, in den Warenkorb
legen, Bestellabschluss).

**Wo es liegt.** Ereignistypen erweitern `eZWorkflowEventType` (`kernel/classes/ezworkfloweventtype.php`), gefunden über
`settings/workflow.ini [EventSettings] RepositoryDirectories[]=kernel/classes/workflowtypes`,
`ExtensionDirectories[]` und `AvailableEventTypes[]` (`event_ezapprove`, `event_ezwaituntildate`, ...). Operationen,
in die man sich einhängen kann, stehen in `[OperationSettings] AvailableOperationList[]`; Trigger (`eZTrigger`,
`kernel/classes/eztrigger.php`) verbinden eine Operation mit einem Workflow. Aufgeschobene Workflows laufen aus dem
Cronjob-Teil `workflow.php`. Anleitung: [Workflows](../../anleitungen/workflows.md).

## Cronjobs

**Wo es liegt.** `runcronjobs.php` führt die in `settings/cronjob.ini` konfigurierten Teile aus: `[CronjobSettings]
Scripts[]` für den Standardteil und `[CronjobPart-<name>] Scripts[]` (zum Beispiel `frequent`, `infrequent`), gesucht
in `ScriptDirectories[]=cronjobs` und den Extensions in `ExtensionDirectories[]`. `eZRunCronjobs`
(`kernel/classes/ezruncronjobs.php`) führt ein Skript aus; der Code eines Teils ist eine Klasse
`Exponential\Runnable\CronjobPart` in `kernel/private/classes/cronjobs/`. Die Konsole führt einen Teil als
`cron:<part>` aus.

**Regel.** Ein Cronjob-Teil **muss** gefahrlos zweimal laufen und unterbrochen werden können. Anleitung:
[Cronjobs](../../anleitungen/cronjobs.md), [Cronjobs über die Konsole](../../funktionen/6.0/cronjobs-in-der-konsole.md).

## Benachrichtigungen und Ereignis-Handler

**Wo es liegt.** `settings/notification.ini`: `[NotificationEventTypeSettings]` (Ereignistypen wie `ezpublish`,
`eznewcontent`, `ezcollaboration`) und `[NotificationEventHandlerSettings]` (Handler `ezgeneraldigest`,
`ezcollaborationnotification`, `ezsubtree` in `kernel/classes/notification/handler/`), jeweils mit
`ExtensionDirectories[]`. Handler erweitern `eZNotificationEventHandler`
(`kernel/classes/notification/eznotificationeventhandler.php`); der Cronjob-Teil `notification.php` versendet die
E-Mails. Spezifikation: [Benachrichtigungen](../../spezifikationen/6.0/benachrichtigungen.md).

## Die Datenbankschicht (eZDB)

**Wo es liegt.** `lib/ezdb/classes/`: `eZDB` (Factory, `eZDB::instance()`), `eZDBInterface` und die Treiber
`eZMySQLiDB`, `eZPostgreSQLDB`, `eZSQLite3DB`, `expMongoDB`, `eZNullDB` sowie `eZDBQueryCache`. Der Treiber ist
`site.ini [DatabaseSettings] DatabaseImplementation` (ausgeliefert `ezmysqli`), aufgelöst über `ImplementationAlias[]`.
Transaktionen: `Transactions=enabled`. Der Oracle-Treiber kommt aus der separaten Extension `ezoracle`. Schemadateien
verarbeitet `lib/ezdbschema`. Treiber: [Datenbanktreiber](../../spezifikationen/6.0/datenbanktreiber-2026-09.md).

**Regel.** Kernel-Code **muss** über `eZDB::instance()` und portables SQL gehen, Schreibvorgänge mit mehreren
Anweisungen in `begin()`/`commit()` einschließen und **darf sich nicht** ohne Rückfall für die anderen Treiber auf
treiberspezifische Funktionen verlassen.

## Ereignisse (ezpEvent)

**Was es ist.** Ein kleiner Ereignisbus im Prozess: `ezpEvent::getInstance()->notify( $name, $params )` für
Benachrichtigungen und `filter( $name, $value )` für Werte, die Listener ändern dürfen.

**Wo es liegt.** `kernel/private/classes/ezpevent.php` (nicht in `lib/`). Globale Listener werden in
`site.ini [Event] Listeners[]=<event>@<callback>` konfiguriert; die Datei kennzeichnet die API als privat und
änderbar. Zu den vom Kernel ausgelösten Ereignissen gehören `request/preinput`, `request/input`, `response/preoutput`,
`response/output`, `content/cache`, `content/cache/all`, `content/class/cache`, `image/removeAliases`, die
Zugriffsfilter `content/edit/access`, `content/download/access`, `collaboration/item/access` und
`runnable/<kind>/before|after`.

**Regel.** Registrierungen **müssen** idempotent sein: Unter Velocity sammelt sich ein Listener, der in einem
langlebigen Worker einmal pro Anfrage angehängt wird, an. Listener-Namen **sollten** Zeichenketten der Form
`Class::method` sein, damit sie sich konfigurieren lassen.

## Die Konsole

**Wo es liegt.** `bin/php/console` (Installationen legen meist eine Abkürzung `./console` im Wurzelverzeichnis an).
Sie erkennt Befehle zur Laufzeit: `bin/php/<name>.php` wird zu `exp:<name>`, `bin/shell/<name>.sh` zu `shell:<name>`,
`extension/<ext>/bin/php/<name>` zu `ext:<ext>:<name>`, Cronjob-Teile sind `cron:<part>`. Ein Skript kann mit einem
Tag `@alias` in seinem Docblock zusätzliche Namen deklarieren (`bin/php/exphelp.php` deklariert `@alias exp:help`).
Der Code eines Kernel-Befehls ist eine Klasse `Exponential\Runnable\Command` in `kernel/private/classes/commands/`.
Referenz: [Exponential Console](../../bc/6.0/konsole.md).

**Regel.** Ein Befehl **muss** seinen `--help`-Text und seine Exit-Codes stabil halten (Skripte und Cron hängen davon
ab) und **sollte** `--dry-run` anbieten, wenn er Daten ändert.

## Server: Velocity, PHP-FPM und FrankenPHP

Der Kernel läuft unter drei Servern, alle gesteuert über `./console exp:velocity` und `settings/velocity.ini`
([Velocity-Engines](../../bc/6.0/velocity-engines.md), [Deployment](../../anleitungen/bereitstellung.md)):

| Server | Engine-Wert | Prozessmodell |
|---|---|---|
| Apache oder nginx mit PHP-FPM | (keine Engine) | eine Anfrage pro PHP-Prozesslauf, nichts wird behalten |
| Exponential Velocity | `qbix` (empfohlen) | ein Elternprozess lädt die Anwendung einmal (Warm-up) und forkt persistente Worker, die viele Anfragen bedienen und nach jeder zurückgesetzt werden; ein Antwort-Cache beantwortet wiederholte öffentliche Anfragen, bevor ein Worker verwendet wird |
| FrankenPHP | `frankenphp` | Caddy mit eingebettetem PHP, eine Binärdatei |

Einzelheiten: [Velocity-Webserver](../../funktionen/6.0/velocity-webserver.md),
[persistente Worker](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md),
[Worker-Pool](../../spezifikationen/6.0/velocity-worker-pool.md),
[Antwort-Cache](../../funktionen/6.0/velocity-antwort-cache.md), [FrankenPHP](../../bc/6.0/frankenphp.md).

Regeln, die aus dem persistenten Modell folgen:

- Code **darf nicht** von einem frischen Prozess ausgehen: keine Daten pro Anfrage in statischen Variablen ohne
  Zurücksetzen, kein `exit` zur Ablaufsteuerung außer über `eZExecution::cleanExit()`.
- Eine Änderung an einer Klasse oder INI erreicht Velocity erst nach einem Neustart (`./console exp:velocity restart`
  oder `exp:velocity deploy`), weil die Worker erben, was das Warm-up geladen hat.
- Gecachte Seiten **müssen** über das Cache-Leeren des Kernels invalidiert werden (`eZContentCacheManager`, `eZCache`),
  und der Antwort-Cache von Velocity wird nach einem Deployment mit `./console exp:velocity cache clear` geleert.

## Bekannte Ausnahmen und technische Schulden

- `lib/` ist nicht frei von Abhängigkeiten zum Kernel (Beispiele in
  [Regeln für Schichten und Benennung](schichten-und-benennung.md#lib-allgemeine-bibliotheken)).
- `ezpEvent` ist in `site.ini` als private API dokumentiert, dennoch verlassen sich Extensions darauf.
- Einige Ansichten lesen `$_POST` und `$_GET` noch direkt statt über `eZHTTPTool` (10 der 255 Ansichtsklassen in
  `kernel/private/classes/views/` zum Zeitpunkt des Schreibens).
- Ansichten, Cronjob-Teile und Befehle als einfache Dateien laufen weiterhin neben der klassenbasierten Form.

## Verwandte Seiten

- [Katalog der Entwurfsmuster](../muster/README.md): jede Factory, jeder Handler, jedes persistente Objekt, jeder Datentyp, Operator, jede Fetch-Funktion, Ansicht und jeder Plugin-Punkt, aus dem Code generiert
- [Regeln für Schichten und Benennung](schichten-und-benennung.md)
- [Kompatibilität, Releases und Deployment](kompatibilitaet-releases-deployment.md)
- [Templates und Design](../../anleitungen/templates-und-design.md), [Extensions](../../anleitungen/extensions.md),
  [Caches](../../anleitungen/caches.md)
