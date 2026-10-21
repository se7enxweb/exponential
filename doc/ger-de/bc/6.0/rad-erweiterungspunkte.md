# Erweiterungspunkte

Lesen Sie diese Seite, wenn Sie Exponential mit eigenem Code erweitern und wissen möchten, wo jede Art von Erweiterung
andockt, welche INI-Zeile sie registriert und welches Werkzeug unter **Setup > RAD** Ihnen hilft, sie zu bauen.
Exponential wird auf eine Handvoll sich wiederholender Arten erweitert: eine Klasse in einem Verzeichnis, das der
Kernel durchsucht, eine Klasse, die eine INI-Einstellung nennt, eine Datei an einer Stelle, die per Konvention
gefunden wird, oder ein Template in einem Design. Welche davon zutrifft und welche INI-Zeile sie registriert, ließ sich
bisher nur durch Lesen des Kernels herausfinden.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | `kernel/setup/expradcatalogue.php` (`expRADCatalogue::points()`) führt jeden Erweiterungspunkt auf, und **Setup > RAD** wird daraus erzeugt: 70 Erweiterungspunkte, 65 davon mit einem Werkzeug. |
| Wer betroffen ist | Niemand muss etwas ändern. Erweiterungsentwickler erhalten eine Stelle zum Nachschlagen. |
| Wie Sie es prüfen | Öffnen Sie **Setup > RAD** (`/setup/rad`) in der Administration. |
| Wie Sie es beheben | Nichts zu beheben. Ziehen Sie einen registrierten Mechanismus einem Override einer Kernel-Klasse vor (siehe **override** unten). |

Jeder Eintrag wurde gegen den Quelltext geprüft; die Spalte *Kernel* nennt die Datei, in der der Mechanismus liegt.

## Wie Dinge registriert werden

- **directory** — Eine Klasse in einem Verzeichnis, das der Kernel durchsucht. Das Verzeichnis nennt eine Einstellung RepositoryDirectories, und die Klasse wird über ihren Dateinamen gefunden.
- **handler** — Eine Klasse, die eine INI-Einstellung nennt und die über eZExtension::getHandlerClass geladen wird. Die Einstellung gibt einen Alias, der Alias gibt die Klasse.
- **autoload** — Eine Klasse, die über den Autoload-Pfad der Erweiterungen erreicht wird, einmal in einer INI-Datei registriert und dann überall verfügbar.
- **file** — Eine Datei an einer Stelle, an der der Kernel nach Namen sucht. Nichts registriert sie; dort zu liegen ist die Registrierung.
- **design** — Ein Template in einem Design, gefunden über die Design-Kette statt dadurch, dass es irgendwo genannt wird.
- **ini** — Nur Einstellungen. Es wird nichts außer INI geschrieben, und das Verhalten ändert sich.
- **override** — Eine Kernel-Klasse, die über den Override-Autoload-Pfad durch eine eigene ersetzt wird. Der schwerste dieser Mechanismen und der letzte Ausweg: Nichts registriert den Ersatz namentlich, sodass zwei Erweiterungen, die dieselbe Klasse ersetzen, einen Kampf führen, von dem keine von beiden weiß.
- **registry** — Ein Eintrag einer Registry, die rad.ini deklariert: Der Kernel oder eine Erweiterung sagt, welche INI-Blöcke oder welche Klassenliste die Einträge enthalten und was jeder sein muss, und die Übersicht der Erweiterungspunkte zählt und prüft jeden Eintrag, ohne dass der Kernel die Erweiterung nennt.
- **filter** — Ein Listener eines Filter-Ereignisses in site.ini [Event] Listeners[]. Der Kernel übergibt einen Wert und macht mit dem weiter, was der Listener zurückgibt; ein Listener, der vergisst, ihn zurückzugeben, zerstört also den Wert.

## Inhalte

Woraus Inhalte bestehen und wie sie bearbeitet und gespeichert werden.

### Datentyp  
*Werkzeug:* `/setup/datatype`

Eine Art von Wert, die ein Attribut einer Inhaltsklasse halten kann, mit eigener Bearbeitung, Validierung, Speicherung
und Anzeige.

| | |
|---|---|
| Code | `extension/<name>/datatypes/<datatype>/<datatype>type.php` |
| Registriert durch | content.ini [DataTypeSettings] ExtensionDirectories[] und AvailableDataTypes[], dazu design.ini [ExtensionSettings] DesignExtensions[], sonst zeichnet er nichts |
| Vertrag | `extends eZDataType` |
| Mechanismus | directory |
| Kernel | `kernel/classes/ezdatatype.php` |

### Inhaltsklasse  
*Werkzeug:* `/setup/contentextension`

Ein Inhaltstyp: seine Attribute, deren Datentypen und wie eine Instanz davon benannt wird.

| | |
|---|---|
| Code | `extension/<name>/share/package/<name>/ezcontentclass/` |
| Registriert durch | Als Paket installiert oder in der Administrationsoberfläche angelegt und exportiert. |
| Vertrag | `eZContentClass`-Definition, als Klassenpaket |
| Mechanismus | file |
| Kernel | `kernel/classes/ezcontentclass.php` |

### XML-Custom-Tag  
*Werkzeug:* `/setup/contentextension`

Ein Tag, den Autoren in Rich Text verwenden können, mit eigenen Attributen und eigenem Template.

| | |
|---|---|
| Code | `extension/<name>/design/standard/templates/content/datatype/view/ezxmltags/<tag>.tpl` |
| Registriert durch | content.ini [CustomTagSettings] AvailableCustomTags[] und ein Abschnitt [<tag>] |
| Vertrag | Ein Template, dazu INI, die die Attribute beschreibt |
| Mechanismus | design |
| Kernel | `settings/content.ini` |

### Eingabe-Handler für XML-Text  
*Werkzeug:* `/setup/handlerextension/xmlinput`

Was das, was ein Autor eingegeben hat, in das XML verwandelt, das ein Rich-Text-Attribut speichert.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>xmlinput.php` |
| Registriert durch | ezxml.ini [InputSettings] HandlerClass |
| Vertrag | `extends eZXMLInputHandler` |
| Mechanismus | handler |
| Kernel | `kernel/classes/datatypes/ezxmltext/ezxmltext.php` |

### Ausgabe-Handler für XML-Text  
*Werkzeug:* `/setup/handlerextension/xmloutput`

Was gespeicherten Rich Text in das verwandelt, was ein Besucher sieht.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>xmloutput.php` |
| Registriert durch | ezxml.ini [OutputSettings] HandlerClass |
| Vertrag | `extends eZXMLOutputHandler` |
| Mechanismus | handler |
| Kernel | `kernel/classes/datatypes/ezxmltext/ezxmltext.php` |

### Verhalten der Informationssammlung  
*Werkzeug:* `/setup/settingsextension`

Was geschieht, wenn ein Besucher ein aus Inhalten gebautes Formular ausfüllt: wie die Einsendung heißt, ob sie
aufbewahrt wird, ob sie per E-Mail verschickt wird und was dem Besucher danach gezeigt wird. Pro Inhaltsklasse
zugeordnet, sodass sich eine Umfrage und ein Kontaktformular, die gleich gebaut sind, unterschiedlich verhalten.

| | |
|---|---|
| Code | `settings/override/collect.ini.append.php` und Templates unter `content/collectedinfo/` |
| Registriert durch | collect.ini [InfoSettings] TypeList[<class>], [EmailSettings] SendEmailList[<class>], [CollectionSettings] CollectAnonymousDataList[<class>], [DisplaySettings] DisplayList[<class>] |
| Vertrag | Keine Klasse zu schreiben: eine Einstellung pro Inhaltsklasse und ein Template pro Typ |
| Mechanismus | ini |
| Kernel | `kernel/classes/ezinformationcollection.php` |

### Regeln für das Leeren des View-Caches  
*Werkzeug:* `/setup/settingsextension`

Welche anderen Seiten neu gebaut werden müssen, wenn ein Objekt veröffentlicht wird. Der Standard leert das Objekt,
seine Elternknoten und das, was sich darauf bezieht; eine Gruppe, die nach der Kennung einer Inhaltsklasse benannt
ist, sagt, was sonst noch - eine Liste, die sich ändern muss, wenn ein Kommentar veröffentlicht wird, ein Objekt an
einer ganz anderen Stelle.

| | |
|---|---|
| Code | `settings/override/viewcache.ini.append.php` |
| Registriert durch | viewcache.ini [ViewCacheSettings] SmartCacheClear=enabled, dann eine Gruppe [<class_identifier>] mit ClearCacheMethod[], DependentClassIdentifier[] und AdditionalObjectIDs[] |
| Vertrag | Keine Klasse zu schreiben: eine Gruppe pro Kennung einer Inhaltsklasse |
| Mechanismus | ini |
| Kernel | `kernel/classes/ezcontentcachemanager.php` |

### Filter für das asynchrone Veröffentlichen  
*Werkzeug:* `/setup/handlerextension/publishfilter`

Entscheidet, ob eine Version in der Anfrage veröffentlicht oder an die Warteschlange übergeben wird. Das
Veröffentlichen eines großen Objekts blockiert den, der die Schaltfläche gedrückt hat; eine Warteschlange, die alles
nimmt, lässt kleine Änderungen kaputt wirken. Hier wird diese Grenze gezogen.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>publishfilter.php` |
| Registriert durch | content.ini [PublishingSettings] AsynchronousPublishingFilters[] |
| Vertrag | Implementiert `ezpAsynchronousPublishingFilterInterface`: eine Methode `accept()` |
| Mechanismus | handler |
| Kernel | `kernel/private/interfaces/asynchronouspublishingfilter.php` |

## Templates und Design

Was ein Template aufrufen kann und was ein Design ersetzen kann.

### Template-Operator  
*Werkzeug:* `/setup/templateoperator`

Etwas, durch das ein Template einen Wert leiten kann: {$value|my_operator()}. Eine Klasse kann auf viele Namen
antworten, und was sie dem Compiler verspricht, entscheidet, ob sie einmal beim Kompilieren oder für immer bei jeder
Anfrage läuft.

| | |
|---|---|
| Code | `extension/<name>/autoloads/<name>operators.php` |
| Registriert durch | site.ini [TemplateSettings] ExtensionAutoloadPath[], über $eZTemplateOperatorArray in autoloads/eztemplateautoload.php - keine INI, die die Klasse nennt |
| Vertrag | `operatorList()`, `namedParameterList()`, `operatorTemplateHints()` und `modify()` |
| Mechanismus | autoload |
| Kernel | `lib/eztemplate/classes/eztemplate.php` |

### Template-Fetch-Funktion  
*Werkzeug:* `/setup/templateoperator`

Etwas, das ein Template bei einem Modul erfragen kann: fetch( 'module', 'thing', hash( ... ) ).

| | |
|---|---|
| Code | `extension/<name>/modules/<module>/function_definition.php` |
| Registriert durch | Im Modulverzeichnis zu liegen ist die Registrierung. |
| Vertrag | Eine `$FunctionList`, die pro Funktion eine Klasse und eine Methode nennt |
| Mechanismus | file |
| Kernel | `lib/ezutils/classes/ezfunctionhandler.php` |

### Attribut-Operator  
*Werkzeug:* `/setup/handlerextension/attributeoperator`

Ein Operator, der für ein Inhaltsattribut eines bestimmten Datentyps gilt.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>attributeoperator.php` |
| Registriert durch | template.ini [AttributeOperator] OutputFormatter[<format>] |
| Vertrag | Implementiert `ezpAttributeOperatorFormatterInterface` |
| Mechanismus | handler |
| Kernel | `kernel/private/eztemplate/ezpattributeoperatorformatterinterface.php` |

### Template-Funktion  
*Werkzeug:* `/setup/templateoperator`

Etwas, das ein Template aufruft, statt einen Wert hindurchzuleiten: {my_function arg=1}, wahlweise mit einem Rumpf, den
sie keinmal, einmal oder viele Male zeichnen kann. So sind {section} und {foreach} gebaut.

| | |
|---|---|
| Code | `extension/<name>/autoloads/<name>functions.php` |
| Registriert durch | site.ini [TemplateSettings] ExtensionAutoloadPath[], über $eZTemplateFunctionArray in autoloads/eztemplateautoload.php |
| Vertrag | `functionList()`, `attributeList()`, `hasChildren()` und `process()` |
| Mechanismus | autoload |
| Kernel | `lib/eztemplate/classes/eztemplatesectionfunction.php` |

### Fetch-Alias  
*Werkzeug:* `/setup/templateoperator`

Ein Name für einen Fetch, der an anderer Stelle vollständig ausgeschrieben ist, damit Templates kurz bleiben können.

| | |
|---|---|
| Code | `extension/<name>/settings/fetchalias.ini.append.php` |
| Registriert durch | fetchalias.ini, ein Abschnitt pro Alias |
| Vertrag | Nur Einstellungen |
| Mechanismus | ini |
| Kernel | `settings/fetchalias.ini` |

### Design-Erweiterung  
*Werkzeug:* `/setup/designextension`

Die Templates, Stylesheets und Bilder, mit denen eine Website gezeichnet wird.

| | |
|---|---|
| Code | `extension/<name>/design/<name>/` |
| Registriert durch | design.ini [ExtensionSettings] DesignExtensions[] |
| Vertrag | Templates, gefunden über die Design-Kette |
| Mechanismus | design |
| Kernel | `kernel/common/eztemplatedesignresource.php` |

### Satz von Template-Overrides  
*Werkzeug:* `/setup/designextension`

Ein Template, das an Stelle eines anderen verwendet wird, für die Inhalte, auf die es passt, und für nichts sonst.

| | |
|---|---|
| Code | `extension/<name>/design/<design>/override/templates/` |
| Registriert durch | override.ini, ein Abschnitt pro Override |
| Vertrag | Zeilen `Source`, `MatchFile`, `Subdir` und `Match` |
| Mechanismus | design |
| Kernel | `kernel/common/eztemplatedesignresource.php` |

### Icon-Theme in einer Erweiterung  
*Werkzeug:* `/setup/settingsextension`

Ein Satz von Icons, den eine Erweiterung mitbringt und der vor den ausgelieferten durchsucht wird. Die ganze Kette hat
einen Rückfall: das aktuelle Theme, dann alle zusätzlichen Themes, dann das Standard-Theme; und innerhalb jedes Themes
Verzeichnisse von Erweiterungen vor share/icons. Ein fehlendes Icon nimmt den Standard des Themes, statt ein kaputtes
Bild zu zeichnen.

| | |
|---|---|
| Code | `extension/<name>/icons/<theme>/` |
| Registriert durch | icon.ini [ExtensionSettings] IconExtensions[] und [IconSettings] Theme oder AdditionalThemeList[] |
| Vertrag | Bilddateien, benannt nach dem, was sie darstellen. Als statische Dateien ausgeliefert, daher läuft pro Bild kein PHP |
| Mechanismus | ini |
| Kernel | `kernel/common/ezwordtoimageoperator.php` |

## Module und Views

Adressen, unter denen die Website antwortet, und wer sie erreichen darf.

### Modul über vorhandenen Tabellen  
*Werkzeug:* `/setup/moduleextension`

Eine Administration und eine Template-API für Tabellen, die es bereits gibt, hier oder in einer anderen Datenbank.

| | |
|---|---|
| Code | `extension/<name>/modules/<module>/` und `classes/` |
| Registriert durch | module.ini [ModuleSettings] ExtensionRepositories[] und ModuleList[] |
| Vertrag | `eZPersistentObject` oder eine Klasse mit denselben Methoden |
| Mechanismus | directory |
| Kernel | `kernel/classes/ezpersistentobject.php` |

### Modul  
*Werkzeug:* `/setup/modulewizard`

Eine neue Adresse, unter der die Website antwortet, mit eigenen Views und eigenen Richtlinien.

| | |
|---|---|
| Code | `extension/<name>/modules/<module>/module.php` |
| Registriert durch | module.ini [ModuleSettings] ExtensionRepositories[] und ModuleList[] |
| Vertrag | `$Module`, `$ViewList` und `$FunctionList` |
| Mechanismus | directory |
| Kernel | `lib/ezutils/classes/ezmodule.php` |

### View für ein vorhandenes Modul  
*Werkzeug:* `/setup/modulewizard`

Eine weitere Sache, um die ein vorhandenes Modul gebeten werden kann.

| | |
|---|---|
| Code | `extension/<name>/modules/<module>/<view>.php` |
| Registriert durch | Die eigene module.php des Moduls, von der Erweiterung erweitert |
| Vertrag | Ein Skript, das `$Result` setzt |
| Mechanismus | file |
| Kernel | `lib/ezutils/classes/ezmodule.php` |

### Richtlinienfunktion und Einschränkung  
*Werkzeug:* `/setup/modulewizard`

Etwas, das einer Rolle gewährt werden kann, und wodurch es eingeschränkt werden kann.

| | |
|---|---|
| Code | `extension/<name>/modules/<module>/function_definition.php` |
| Registriert durch | Die `$FunctionList` des Moduls |
| Vertrag | Namen und eine Beschreibung der Einschränkungen pro Funktion |
| Mechanismus | file |
| Kernel | `kernel/classes/ezrole.php` |

### Einschränkung für Inhaltsrichtlinien  
*Werkzeug:* `/setup/settingsextension` (wählen Sie das Ereignis module/functionlist: Es schreibt den Listener und einen funktionierenden Handler)

Eine eigene Einschränkung für eine Inhaltsfunktion wie content/read, neben Class, Section und Subtree: Eine
Erweiterung fügt sie der Funktion hinzu, und ihr Handler entscheidet für Objekte, Knoten und Versionen und liefert die
SQL-Bedingung für Fetches von Listen und Bäumen. Ohne Handler verweigert die Einschränkung überall. Siehe
[Einschränkungen für Inhaltsrichtlinien von Erweiterungen](../../../eng-us/features/6.0/content-limitation-handlers.md).

| | |
|---|---|
| Code | `extension/<name>/classes/<class>.php`, z. B. `class myExtLimitationHandler implements ezpContentLimitationHandler`; der Assistent für Einstellungserweiterungen schreibt einen funktionierenden (MaxDepth) mit einem Listener von module/functionlist |
| Registriert durch | site.ini [RoleSettings] LimitationHandlers[<limitation>]=<class>; die Einschränkung wird einer Funktion über den Filter module/functionlist ([Event] Listeners[]) hinzugefügt |
| Vertrag | `implements ezpContentLimitationHandler` (`checkAccess()` für Objekte, Knoten und Versionen, `permissionSQL()` für Fetches); `ezpContentLimitationSolrHandler` fügt `solrFilter()` für Suchen hinzu, die selbst nach den Richtlinien filtern (eZ Find) |
| Mechanismus | handler |
| Kernel | `kernel/private/classes/ezpcontentlimitation.php` |

### REST-Provider  
*Werkzeug:* `/setup/handlerextension/restprovider`

Ein Satz von Adressen, die außerhalb des Template-Systems antworten, damit etwas anderes sie aufrufen kann.

| | |
|---|---|
| Code | `extension/<name>/classes/rest/<name>provider.php` |
| Registriert durch | rest.ini, über die Registry der REST-Provider |
| Vertrag | Implementiert `ezpRestProviderInterface` |
| Mechanismus | handler |
| Kernel | `kernel/private/rest/classes/rest_provider.php` |

### REST-Routenfilter  
*Werkzeug:* `/setup/handlerextension/restroutefilter`

Etwas, das eine REST-Anfrage untersucht oder ändert, bevor sie geroutet wird.

| | |
|---|---|
| Code | `extension/<name>/classes/rest/<name>routefilter.php` |
| Registriert durch | rest.ini [RouteSettings] RouteSettingImpl |
| Vertrag | Erweitert `ezpRestRouteFilterInterface` |
| Mechanismus | handler |
| Kernel | `kernel/private/rest/classes/interfaces/route_filter.php` |

### Serverseitige Ajax-Funktion  
*Werkzeug:* `/setup/handlerextension/ajaxfunction`

Etwas, das der Browser aufrufen kann und von dem er JSON zurückbekommt, ohne eine Seite.

| | |
|---|---|
| Code | `extension/<name>/classes/ezjscserverfunctions<name>.php` |
| Registriert durch | ezjscore.ini [ezjscServer] FunctionList[] |
| Vertrag | Statische Methoden, die eine Argumentliste nehmen |
| Mechanismus | handler |
| Kernel | `extension/ezjscore` |

### In rad.ini deklarierte Registry  
*Werkzeug:* `/setup/radsurvey/(show)/registries`

Eine Liste von Implementierungen, die eine Erweiterung aus ihren eigenen Einstellungen oder aus ihrem eigenen Code
liest, etwa KI-Provider und -Werkzeuge, MCP-Werkzeuge oder Typen von Warteschlangen-Jobs. In rad.ini deklariert, wird
sie von der Übersicht der Erweiterungspunkte gezählt und geprüft wie die eigenen Registries des Kernels: Jeder Eintrag
wird aufgeführt, und einer, dessen Klasse, Methode, Template oder Block fehlt, wird als defekt angezeigt. Wie Sie eine
deklarieren: [Die Erweiterungsoberfläche](rad-erweiterungsoberflaeche.md#in-radini-deklarierte-registries).

| | |
|---|---|
| Code | `extension/<name>/settings/rad.ini.append.php` |
| Registriert durch | rad.ini [RegistrySettings] Registries[]=<key> und [Registry_<key>] mit Title, Ini, Section und Entries[<variable>]=<check>, oder List=<class>::<constant or method> |
| Vertrag | Jeder Eintrag besteht seine Prüfung: eine Klasse oder Schnittstelle, `callable`, `listener`, `template`, `section:<prefix>`, `in:<class>::<list>`, `one-of:<words>` oder `name` |
| Mechanismus | registry |
| Kernel | `kernel/setup/expradsurvey.php` |

## Workflow, Ereignisse und Jobs

Was geschieht, wenn etwas veröffentlicht wird, und was von selbst läuft.

### Workflow-Ereignistyp  
*Werkzeug:* `/setup/workflowevent`

Ein Schritt, den ein Workflow ausführen kann, wenn etwas veröffentlicht, verschoben oder entfernt wird.

| | |
|---|---|
| Code | `extension/<name>/eventtypes/event/<event>/<event>type.php` |
| Registriert durch | workflow.ini [EventSettings] ExtensionDirectories[] und AvailableEventTypes[] |
| Vertrag | `extends eZWorkflowEventType` |
| Mechanismus | directory |
| Kernel | `kernel/classes/ezworkfloweventtype.php` |

### Trigger  
*Werkzeug:* `/setup/settingsextension`

Die Stelle in einer Operation, an der ein Workflow die Gelegenheit zu laufen erhält.

| | |
|---|---|
| Code | `extension/<name>/settings/workflow.ini.append.php` |
| Registriert durch | workflow.ini und die Definition der Operation, die den Trigger deklariert |
| Vertrag | Einstellungen und ein Rumpf der Operation mit einem Trigger darin |
| Mechanismus | ini |
| Kernel | `kernel/classes/eztrigger.php` |

### Typ von Benachrichtigungsereignissen  
*Werkzeug:* `/setup/handlerextension/notificationtype`

Eine Art von Sache, über die Personen benachrichtigt werden können.

| | |
|---|---|
| Code | `extension/<name>/notification/event/<type>/<type>type.php` |
| Registriert durch | notification.ini [NotificationEventTypeSettings] RepositoryDirectories[] und AvailableNotificationEventTypes[] |
| Vertrag | `extends eZNotificationEventType` |
| Mechanismus | directory |
| Kernel | `kernel/classes/notification/eznotificationeventtype.php` |

### Benachrichtigungs-Handler  
*Werkzeug:* `/setup/handlerextension/notificationhandler`

Was entscheidet, wer benachrichtigt wird und wie.

| | |
|---|---|
| Code | `extension/<name>/notification/handler/<handler>/<handler>handler.php` |
| Registriert durch | notification.ini [NotificationEventHandlerSettings] ExtensionDirectories[] und AvailableNotificationEventTypes[] (der Kernel liest auch die Variable Types im Abschnitt des Handlers) |
| Vertrag | `extends eZNotificationEventHandler` |
| Mechanismus | directory |
| Kernel | `kernel/classes/notification/eznotificationeventhandler.php` |

### Cronjob-Skript und -Teil  
*Werkzeug:* `/setup/cronjobs`

Etwas, das von selbst nach einem Zeitplan läuft, außerhalb jeder Anfrage.

| | |
|---|---|
| Code | `extension/<name>/cronjobs/<script>.php` |
| Registriert durch | cronjob.ini [CronjobSettings] ExtensionDirectories[] und Scripts[] oder ein eigener Teil |
| Vertrag | Ein von runcronjobs.php ausgeführtes Skript, mit `$cli` und `$sys` verfügbar |
| Mechanismus | directory |
| Kernel | `runcronjobs.php` |

### Ausgelassener Cronjob-Teil  
*Werkzeug:* keines (der Assistent für Einstellungserweiterungen schreibt `[Event] Listeners[]`, das die Kommandozeile nicht liest)

Etwas, das entscheidet, ob ein Cronjob-Teil überhaupt läuft, zum Beispiel ein Stoppschalter, während ein Release
bereitgestellt wird. runcronjobs.php fragt den Filter cronjob/part/run mit dem Teil, dem Siteaccess und den Skripten,
bevor das erste Skript startet; nur true führt den Teil aus. Anleitung:
[Einen Cronjob-Teil auslassen](../../../eng-us/features/6.0/cronjob-part-filter.md).

| | |
|---|---|
| Code | `extension/<name>/classes/<name>listener.php` |
| Registriert durch | site.ini [RunnableSettings] Listeners[]=cronjob/part/run@<callback> |
| Vertrag | Ein Callable, das `( $run, $part, $siteaccess, $scripts, $single )` nimmt und true zurückgibt, um den Teil auszuführen |
| Mechanismus | ini |
| Kernel | `kernel/private/classes/commands/runcronjobs.php` |

### Listener für Kernel-Ereignisse  
*Werkzeug:* `/setup/settingsextension`

Etwas, das aufgerufen wird, wenn der Kernel eine benannte Stelle erreicht, etwa wenn eine Anfrage eintrifft.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>listener.php` |
| Registriert durch | site.ini [Event] Listeners[]=<event>@<callback> |
| Vertrag | Ein Callable, das nimmt, was immer das Ereignis übergibt |
| Mechanismus | ini |
| Kernel | `kernel/private/classes/ezpevent.php` |

### Listener für Filter-Ereignisse  
*Werkzeug:* `/setup/radsurvey/(show)/events`

Ein Listener eines Filter-Ereignisses, dessen Rückgabewert ersetzt, was der Kernel übergeben hat: zum Beispiel
`httpcache/store/headers`, die Header, die ein HTTP-Cache mit einer Seite aufbewahrt. Ein Benachrichtigungsereignis
wie `package/install/drafts`, nachdem ein Paket als Entwürfe installiert wurde, ignoriert, was seine Listener
zurückgeben.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>listener.php` |
| Registriert durch | site.ini [Event] Listeners[]=<filter>@<callback> |
| Vertrag | Ein Callable, das den Wert zurückgibt, den es erhalten hat, geändert oder nicht |
| Mechanismus | filter |
| Kernel | `kernel/private/classes/ezpevent.php` |

## Speicherung und Infrastruktur

Wo Dinge aufbewahrt werden und wie sie dorthin gelangen.

### Datenbank-Handler  
*Werkzeug:* `/setup/handlerextension/dbhandler`

Eine Art von Datenbank, auf der das ganze System laufen kann.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>db.php` |
| Registriert durch | site.ini [DatabaseSettings] ImplementationAlias[<alias>] |
| Vertrag | `extends eZDBInterface` |
| Mechanismus | handler |
| Kernel | `lib/ezdb/classes/ezdb.php` |

### Cluster-Datei-Handler  
*Werkzeug:* `/setup/handlerextension/cluster`

Wo Dateien liegen, wenn mehr als ein Server dieselbe Website ausliefert. Alles, was der Kernel unter var/ liest oder
schreibt, geht hier hindurch; er ist daher der am weitesten reichende der Speicherpunkte und derjenige, bei dem es sich
am meisten lohnt, von eZFSFileHandler statt von der bloßen Schnittstelle abzuleiten.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>filehandler.php` |
| Registriert durch | file.ini [ClusteringSettings] FileHandler |
| Vertrag | Implementiert `eZClusterFileHandlerInterface` oder erweitert `eZFSFileHandler`, das dies bereits tut |
| Mechanismus | handler |
| Kernel | `kernel/classes/ezclusterfilehandler.php` |

### DFS-Backend  
*Werkzeug:* `/setup/handlerextension/dfsbackend`

Wohin der DFS-Cluster-Handler die Bytes legt: eine eingehängte Freigabe, ein Objektspeicher, irgendwo Erreichbares.
Der Index dessen, was existiert, bleibt in der Datenbank; dies bewegt nur Dateiinhalte.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>dfsbackend.php` |
| Registriert durch | file.ini [eZDFSClusteringSettings] DFSBackend |
| Vertrag | Implementiert `eZDFSFileHandlerDFSBackendInterface` |
| Mechanismus | handler |
| Kernel | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendinterface.php` |

### DFS-Datenbank-Backend  
*Werkzeug:* `/setup/handlerextension/dfsdbbackend`

Die andere Hälfte von DFS: der Index, welche Dateien existieren, wie groß sie sind und welche gerade erzeugt werden. Er
verhindert, dass zwei Server gleichzeitig denselben Cache-Eintrag bauen, und ist daher die schwerer zu ersetzende
Hälfte.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>dfsdbbackend.php` |
| Registriert durch | file.ini [eZDFSClusteringSettings] DBBackend |
| Vertrag | Folgt der Form von `eZDFSFileHandlerMySQLiBackend` |
| Mechanismus | handler |
| Kernel | `kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php` |

### Handler für Binärdateien  
*Werkzeug:* `/setup/handlerextension/binaryfile`

Wie eine hochgeladene Datei gespeichert und wieder herausgegeben wird.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>binaryfilehandler.php` |
| Registriert durch | file.ini [BinaryFileSettings] Handler |
| Vertrag | Implementiert die Schnittstelle der Handler für Binärdateien |
| Mechanismus | handler |
| Kernel | `kernel/classes/ezbinaryfilehandler.php` |

### Suchmaschine  
*Werkzeug:* `/setup/handlerextension/search`

Was Inhalte beim Veröffentlichen indiziert und was antwortet, wenn jemand sucht.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>searchengine.php` |
| Registriert durch | site.ini [SearchSettings] SearchEngine |
| Vertrag | `implements ezpSearchEngine` |
| Mechanismus | handler |
| Kernel | `kernel/private/interfaces/ezpsearchengine.php` |

### Session-Handler  
*Werkzeug:* `/setup/handlerextension/session`

Wo Sitzungen aufbewahrt und wie sie aufgeräumt werden.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>sessionhandler.php` |
| Registriert durch | site.ini [Session] Handler |
| Vertrag | `extends ezpSessionHandler` |
| Mechanismus | handler |
| Kernel | `lib/ezsession/classes/ezsession.php` |

### Mail-Transport  
*Werkzeug:* `/setup/handlerextension/mail`

Wie Mails das System verlassen.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>transport.php` |
| Registriert durch | site.ini [MailSettings] TransportAlias[<alias>] |
| Vertrag | `extends eZMailTransport` |
| Mechanismus | handler |
| Kernel | `lib/ezutils/classes/ezmailtransport.php` |

### Handler für den statischen Cache  
*Werkzeug:* `/setup/handlerextension/staticcache`

Was Seiten auf die Festplatte schreibt, damit der Webserver sie ohne PHP ausliefern kann.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>staticcache.php` |
| Registriert durch | site.ini [ContentSettings] StaticCacheHandler |
| Vertrag | Implementiert `ezpStaticCache` |
| Mechanismus | handler |
| Kernel | `kernel/setup/expstaticcacherunner.php` |

### Bild-Handler und Aliase  
*Werkzeug:* `/setup/settingsextension`

Wie ein Bild skaliert wird und welche Größen es gibt.

| | |
|---|---|
| Code | `extension/<name>/settings/image.ini.append.php` |
| Registriert durch | image.ini [AliasSettings] AliasList[] und ein Abschnitt pro Alias |
| Vertrag | Einstellungen und optional eine Klasse `eZImageHandler` |
| Mechanismus | ini |
| Kernel | `lib/ezimage/classes/ezimagemanager.php` |

### Kompilierte Einstellungen und View-Cache in Redis  
*Werkzeug:* `/setup/handlerextension/inicache`

Zwei Stellen, an denen der Kernel seine Caches an Redis oder Valkey übergibt, wenn etwas dafür antwortet: der
kompilierte INI-Cache und der Cache der Inhaltsansichten. Keine davon ist eine INI-Einstellung - der Kernel fragt, ob
eine Klasse existiert, und verwendet sie, wenn ja; eine Installation ohne die Erweiterung verhält sich also genau wie
bisher.

| | |
|---|---|
| Code | Eine Klasse namens `sevenxValkeyINICache` oder `sevenxValkeyCacheBlock`, irgendwo, wo der Autoloader sie sieht |
| Registriert durch | Nichts registriert sie: `class_exists()` entscheidet |
| Vertrag | `instance()`, dann `get()` und `put()` für den Block-Cache; `instance()` und die Schnittstelle der kompilierten Datei für den INI-Cache |
| Mechanismus | handler |
| Kernel | `lib/ezutils/classes/ezini.php` |

### Panel der Debug-Leiste  

Ein Tab der Exp-Debug-Leiste aus einer Erweiterung, nach Memory, mit einem optionalen Chip in der Zusammenfassung und
Daten im JSON der Seite: was die Erweiterung über die Seite weiß, etwa das Layout, aus dem sie gebaut wurde. Das Panel
entscheidet, wer es sehen darf, da die Leiste auch anonymen Besuchern nach IP angezeigt wird.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>debugbarpanel.php` |
| Registriert durch | debugbar.ini [DebugBarSettings] Panels[]=<id> und [Panel_<id>] Class=<class> |
| Vertrag | `implements expDebugBarPanelInterface`: `visible()`, `title()`, `chip()`, `html()` und `data()` |
| Mechanismus | ini |
| Kernel | `lib/ezutils/classes/expdebugbarreport.php` |

## Pakete und Shop

Dinge zwischen Installationen bewegen und sie verkaufen.

### Paket-Handler  
*Werkzeug:* `/setup/handlerextension/packagehandler`

Eine Art von Sache, die in ein Paket gelegt und wieder herausgenommen werden kann.

| | |
|---|---|
| Code | `extension/<name>/packagehandlers/<handler>/<handler>packagehandler.php` |
| Registriert durch | package.ini [PackageSettings] HandlerAlias[<alias>] |
| Vertrag | `extends eZPackageHandler` |
| Mechanismus | handler |
| Kernel | `kernel/classes/ezpackagehandler.php` |

### Handler für die Paketerstellung  
*Werkzeug:* `/setup/handlerextension/packagecreation`

Die Schritte, die die Administrationsoberfläche durchläuft, wenn ein Paket erstellt wird.

| | |
|---|---|
| Code | `extension/<name>/packagehandlers/<handler>/<handler>creationhandler.php` |
| Registriert durch | package.ini [CreationSettings] HandlerAlias[<alias>] |
| Vertrag | `extends eZPackageCreationHandler` |
| Mechanismus | handler |
| Kernel | `kernel/classes/ezpackagecreationhandler.php` |

### Handler für die Paketinstallation  
*Werkzeug:* `/setup/handlerextension/packageinstall`

Was geschieht, wenn ein Paket installiert oder wieder entfernt wird.

| | |
|---|---|
| Code | `extension/<name>/packagehandlers/<handler>/<handler>installhandler.php` |
| Registriert durch | package.ini [InstallerSettings] HandlerAlias[<alias>] |
| Vertrag | `extends eZPackageInstallationHandler` |
| Mechanismus | handler |
| Kernel | `kernel/classes/ezpackageinstallationhandler.php` |

### Zahlungs-Gateway  
*Werkzeug:* `/setup/handlerextension/paymentgateway`

Bringt einen Warenkorb an einen Ort, an dem Geld bezahlt werden kann, und bringt die Antwort zurück. Der Shop
berechnet Preise, Steuern und Lieferung eines Warenkorbs und hört beim Einnehmen von Geld auf: Dies ist das Stück, das
nicht mitgeliefert wird. Es wird für eine Bestellung zweimal aufgerufen - einmal, um den Käufer wegzuschicken, einmal,
wenn er zurückkehrt -, und die beiden Besuche werden durch die Zahlungszeile verbunden, die es dazwischen speichert.

| | |
|---|---|
| Code | `extension/<name>/paymentgateways/<alias>gateway.php` |
| Registriert durch | paymentgateways.ini [GatewaysSettings] AvailableGateways[] und GatewaysDirectories[], dazu eZPaymentGatewayType::registerGateway() am Ende der Klassendatei, dazu ein Workflow mit einem Ereignis Payment Gateway, gebunden an shop_confirmorder |
| Vertrag | `extends eZRedirectGateway` oder `eZPaymentGateway` für eines, das die Zahlung annimmt, ohne die Website zu verlassen |
| Mechanismus | directory |
| Kernel | `kernel/shop/classes/ezredirectgateway.php` |

### Zahlungs-Gateway, transparent  
*Werkzeug:* `/setup/handlerextension/paymentgatewaydirect`

Nimmt die Zahlung an, ohne dass der Käufer die Website je verlässt: Die Karte wird im Browser gegen ein Token
getauscht, und die Belastung erfolgt von Server zu Server, während er wartet. Heute die übliche Form und eine andere
Aufgabe als die Weiterleitungsart - ein Aufruf entscheidet über die Bestellung, es gibt also keinen zweiten Besuch, in
dem eine falsche Antwort korrigiert werden könnte, und ein Timeout ist ein wirklich mehrdeutiger Zustand, nach dem
gefragt statt geraten werden muss.

| | |
|---|---|
| Code | `extension/<name>/paymentgateways/<alias>gateway.php` |
| Registriert durch | paymentgateways.ini [GatewaysSettings] AvailableGateways[] und GatewaysDirectories[], dazu eZPaymentGatewayType::registerGateway() am Ende der Klassendatei, dazu ein Workflow mit einem Ereignis Payment Gateway, gebunden an shop_confirmorder |
| Vertrag | `extends eZPaymentGateway` |
| Mechanismus | directory |
| Kernel | `kernel/shop/classes/ezpaymentgateway.php` |

### Mehrwertsteuer-Handler  
*Werkzeug:* `/setup/handlerextension/vat`

Welcher Steuersatz für was gilt, für wen.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>vathandler.php` |
| Registriert durch | shop.ini [VATSettings] Handler und RepositoryDirectories[] |
| Vertrag | Implementiert die Schnittstelle der Mehrwertsteuer-Handler |
| Mechanismus | directory |
| Kernel | `kernel/classes/ezvatmanager.php` |

### Versand-Handler  
*Werkzeug:* `/setup/handlerextension/shipping`

Was die Lieferung kostet und welche Optionen es gibt.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>shippinghandler.php` |
| Registriert durch | shop.ini [ShippingSettings] Handler und RepositoryDirectories[] |
| Vertrag | Implementiert die Schnittstelle der Versand-Handler |
| Mechanismus | directory |
| Kernel | `kernel/classes/ezshippingmanager.php` |

### Handler für Warenkorbinformationen  
*Werkzeug:* `/setup/handlerextension/basketinfo`

Was eine Zeile des Warenkorbs über sich selbst sagt: Name, Preis und was sie ist.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>basketinfohandler.php` |
| Registriert durch | shop.ini [BasketInfoSettings] Handler und RepositoryDirectories[] |
| Vertrag | Implementiert die Schnittstelle der Handler für Warenkorbinformationen |
| Mechanismus | directory |
| Kernel | `kernel/classes/basketinfohandlers/ezdefaultbasketinfohandler.php` |

### Handler für Wechselkurse  
*Werkzeug:* `/setup/handlerextension/exchangerate`

Woher der Kurs zwischen zwei Währungen kommt.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>exchangeratehandler.php` |
| Registriert durch | shop.ini [ExchangeRatesSettings] Handler und RepositoryDirectories[] |
| Vertrag | Implementiert die Schnittstelle der Handler für Wechselkurse |
| Mechanismus | directory |
| Kernel | `kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php` |

## Benutzer, Zugriff und Sprache

Wer hineinkommt, was er tun darf und in welcher Sprache.

### Filter für URL-Aliase  
*Werkzeug:* `/setup/handlerextension/urlfilter`

Läuft über jede URL, die dieses System erzeugt, bevor sie gespeichert wird, und darf sie umschreiben. Das, was hier
einem Ausgabefilter über Adressen am nächsten kommt: Jeder Alias, für jedes Objekt, in jeder Sprache, geht beim
Erzeugen hindurch.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>urlfilter.php` |
| Registriert durch | site.ini [URLTranslator] FilterClasses[] |
| Vertrag | `extends eZURLAliasFilter` und implementiert `process( $text, &$languageObject, &$caller )` |
| Mechanismus | handler |
| Kernel | `kernel/classes/ezurlaliasfilter.php` |

### Filter für Mobilgeräte  
*Werkzeug:* `/setup/handlerextension/mobilefilter`

Entscheidet, ob eine Anfrage von einem Telefon kam und was damit geschehen soll. Der ausgelieferte gleicht
User-Agent-Zeichenketten mit Mustern ab, was schlecht altert; ein eigener Filter kann einen Header verwenden, den ein
Proxy setzt, oder einen Hinweis, den der Browser gibt.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>mobilefilter.php` |
| Registriert durch | site.ini [SiteAccessSettings] MobileDeviceFilterClass |
| Vertrag | Implementiert `ezpMobileDeviceDetectFilterInterface`: `process`, `isMobileDevice`, `getUserAgentAlias`, `redirect` |
| Mechanismus | handler |
| Kernel | `kernel/private/classes/ezpmobiledevicedetectfilterinterface.php` |

### REST-Filter vor dem Routing  
*Werkzeug:* `/setup/handlerextension/restprerouting`

Die früheste Stelle, an der eine REST-Anfrage zu sehen ist. Noch ist nichts zugeordnet und kein Controller gewählt,
daher kann eine Anfrage umgeschrieben oder abgewiesen werden, bevor sich irgendetwas verpflichtet hat, sie zu
beantworten - und vor der Authentifizierung, also bei einem Aufrufer, den niemand identifiziert hat.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>preroutingfilter.php` |
| Registriert durch | rest.ini [PreRoutingFilters] Filters[] |
| Vertrag | Implementiert `ezpRestPreRoutingFilterInterface` |
| Mechanismus | handler |
| Kernel | `kernel/private/rest/classes/interfaces/prerouting_filter.php` |

### REST-Anfragefilter  
*Werkzeug:* `/setup/handlerextension/restrequestfilter`

Läuft, sobald das Anfrageobjekt gebaut und die Route bekannt ist, und weiß daher, welcher Controller gleich antworten
wird. Hier wird ein Header gelesen, ein Parameter normalisiert oder eine Anfrage aus Gründen abgelehnt, die davon
abhängen, was sie angefragt hat.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>requestfilter.php` |
| Registriert durch | rest.ini [RequestFilters] Filters[] |
| Vertrag | Implementiert `ezpRestRequestFilterInterface` |
| Mechanismus | handler |
| Kernel | `kernel/private/rest/classes/interfaces/request_filter.php` |

### REST-Ergebnisfilter  
*Werkzeug:* `/setup/handlerextension/restresultfilter`

Läuft, nachdem der Controller seine Antwort ermittelt hat und bevor sie zu JSON oder XML wird. Das Ergebnis ist hier
noch gewöhnliches PHP, daher kann es einmal für jedes Format ergänzt oder umgeformt werden statt in jedem Renderer.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>resultfilter.php` |
| Registriert durch | rest.ini [ResultFilters] Filters[] |
| Vertrag | Implementiert `ezpRestResultFilterInterface` |
| Mechanismus | handler |
| Kernel | `kernel/private/rest/classes/interfaces/result_filter.php` |

### REST-Antwortfilter  
*Werkzeug:* `/setup/handlerextension/restresponsefilter`

Der Ausgabefilter der REST-Schicht: das Letzte, was geschieht, bevor eine Antwort hinausgeht. Ein Header bei jeder
Antwort, ein umhüllter Body, ein geänderter Inhaltstyp, ohne einen Controller anzufassen.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>responsefilter.php` |
| Registriert durch | rest.ini [ResponseFilters] Filters[] |
| Vertrag | Implementiert `ezpRestResponseFilterInterface` |
| Mechanismus | handler |
| Kernel | `kernel/private/rest/classes/interfaces/response_filter.php` |

### REST-Präfixfilter  
*Werkzeug:* `/setup/handlerextension/restprefix`

Was entscheidet, wo die API liegt und welche Version davon eine Anfrage angefragt hat. Der ausgelieferte liest einen
regulären Ausdruck; ihn zu ersetzen ist der Weg, die API von /api/ wegzubewegen oder ihre Version anderswoher als aus
dem Pfad zu nehmen.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>prefixfilter.php` |
| Registriert durch | rest.ini [System] PrefixFilterClass |
| Vertrag | Erweitert `ezpRestPrefixFilterInterface`: `parseVersionValue()` und `filter()` |
| Mechanismus | handler |
| Kernel | `kernel/private/rest/classes/prefix_filter.php` |

### Login-Handler für Benutzer  
*Werkzeug:* `/setup/handlerextension/login`

Wogegen ein Benutzer geprüft wird, wenn er sich anmeldet - ein Verzeichnis, ein anderes System, irgendetwas.

| | |
|---|---|
| Code | `extension/<name>/user/<handler>/<handler>user.php` |
| Registriert durch | site.ini [UserSettings] LoginHandler[] und ExtensionDirectory[] |
| Vertrag | `extends eZUser` und implementiert `loginUser()` |
| Mechanismus | directory |
| Kernel | `kernel/classes/datatypes/ezuser` |

### Zusätzliche Wurzelverzeichnisse für Erweiterungen  
*Werkzeug:* `/setup/settingsextension`

Wo Erweiterungen liegen dürfen, über extension/ hinaus. Ein zweites Wurzelverzeichnis - extension_src/ ist der übliche
Name - trennt, was ein Projekt geschrieben hat, von dem, was es von anderswo übernommen hat, sodass die Zuständigkeit
an der Verzeichnisstruktur ablesbar ist statt an einer Liste, die jemand pflegt. Eine Erweiterung gleichen Namens in
einem späteren Wurzelverzeichnis verdeckt die vorherige; so wird ein Paket eines Anbieters geforkt, ohne es zu
bearbeiten.

| | |
|---|---|
| Code | `settings/override/site.ini.append.php` und das Wurzelverzeichnis selbst |
| Registriert durch | site.ini [ExtensionSettings] AdditionalExtensionDirectories[] |
| Vertrag | Nur Einstellungen. Jeder Verwender geht über `eZExtension::extensionPath()` und `expandedPathList()`, daher muss sich nichts ändern, um ein neues Wurzelverzeichnis zu sehen |
| Mechanismus | ini |
| Kernel | `lib/ezutils/classes/ezextension.php` |

### Filter für Wurzelverzeichnisse von Erweiterungen  
*Werkzeug:* `/setup/kerneloverride`

Das letzte Wort darüber, welche Wurzelverzeichnisse durchsucht werden, im Code statt in Einstellungen. Ihn neu zu
definieren ist der Weg, ein Wurzelverzeichnis zur Laufzeit zu ermitteln - aus einer Umgebungsvariablen, daraus, welche
Maschine dies ist, aus dem, was ein Deployment auf die Festplatte gelegt hat -, statt es in eine INI-Datei zu
schreiben, die pro Installation unterschiedlich sein muss.

| | |
|---|---|
| Code | Ein Kernel-Override von `lib/ezutils/classes/ezextension.php` |
| Registriert durch | Nichts registriert ihn: Der Kernel ruft `eZExtension::filterExtensionRootDirectories()` auf und verwendet, was zurückkommt |
| Vertrag | `static filterExtensionRootDirectories( array $roots ) : array` |
| Mechanismus | override |
| Kernel | `lib/ezutils/classes/ezextension.php` |

### Site-Erweiterung  
*Werkzeug:* `/setup/settingsextension`

Eine Erweiterung, die eine ganze Website trägt: ihr Design, ihre Siteaccesses, ihre gruppierten Overrides der
Einstellungen, ihre Module und ihren Code. Was das möglich und nicht nur ordentlich macht, ist, dass eine Erweiterung
jetzt ein eigenes settings/siteaccess/<name>/ enthalten kann, sodass der Siteaccess mit dem reist, was ihn definiert,
statt in settings/ auf einer Maschine zu liegen.

| | |
|---|---|
| Code | `extension/<name>/{design,settings/siteaccess/<sa>,modules,classes,autoloads}/` |
| Registriert durch | site.ini [ExtensionSettings] ActiveExtensions[] und ActiveAccessExtensions[], dazu AdditionalExtensionDirectories[], wenn sie außerhalb von extension/ liegt |
| Vertrag | Keine Klasse: ein Verzeichnis, aufgebaut so, wie der Kernel sucht |
| Mechanismus | directory |
| Kernel | `doc/eng-us/bc/6.0/AdditionalExtensionDirectories.md` |

### Einstellungen aus Code schreiben  
*Werkzeug:* `/setup/settingsextension`

Eine INI-Datei aus PHP statt von Hand ändern: was der Einstellungseditor in der Administration tut und was ein
Installationsprogramm oder ein Upgrade-Skript braucht. Seit 6.0 behält ein Schreiben mit direktem Zugriff die
Kommentare und die Reihenfolge der bearbeiteten Datei, statt sie als bloße Schlüssel und Werte neu zu schreiben, sodass
eine erzeugte Änderung danach von dem gelesen werden kann, der sie pflegen muss.

| | |
|---|---|
| Code | Jedes Skript oder jede Modul-View |
| Registriert durch | Nichts zu registrieren: eZINI mit directAccess true, dann `setVariable()` und `save()` |
| Vertrag | `setVariable( $section, $name, $value )`, dann `save()`; die Datei behält ihre Kommentare |
| Mechanismus | file |
| Kernel | `lib/ezutils/classes/ezini.php` |

### Erweiterung mit Siteaccess-Einstellungen  
*Werkzeug:* `/setup/settingsextension`

Einstellungen, die nur für einen Siteaccess gelten und mit der Erweiterung aufbewahrt werden statt in settings/.

| | |
|---|---|
| Code | `extension/<name>/settings/siteaccess/<siteaccess>/` |
| Registriert durch | site.ini [ExtensionSettings] ActiveAccessExtensions[] |
| Vertrag | Nur Einstellungen |
| Mechanismus | ini |
| Kernel | `lib/ezutils/classes/ezini.php` |

### Übersetzung  
*Werkzeug:* `/setup/contentextension`

Die Wörter, die die Oberfläche verwendet, in einer anderen Sprache.

| | |
|---|---|
| Code | `extension/<name>/translations/<locale>/translation.ts` |
| Registriert durch | i18n.ini und dass die Locale verfügbar ist |
| Vertrag | Eine ts-Datei mit Kontexten und Meldungen |
| Mechanismus | file |
| Kernel | `lib/ezi18n/classes/eztranslationcache.php` |

### Handler für RSS-Importe  
*Werkzeug:* `/rss/list`

Was ein importiertes Element eines Feeds wird, nachdem es abgerufen wurde.

| | |
|---|---|
| Code | `extension/<name>/rss/ezrssimporthandler.php` |
| Registriert durch | site.ini [RSSSettings] ActiveExtensions[] |
| Vertrag | Implementiert die Schnittstelle der Handler für RSS-Importe |
| Mechanismus | directory |
| Kernel | `kernel/classes/ezrssimport.php` |

## Damit es stimmt

Der Katalog ist Daten, keine Prosa: `expRADCatalogue::points()`. Ein dort hinzugefügter Punkt erscheint auf der Seite
RAD, ohne dass die Seite bearbeitet wird. Dieses Dokument wird aus demselben Katalog erzeugt; unterscheiden sich die
beiden, ist **Setup > RAD** aktuell.

## Verwandte Seiten

- [RAD-Erweiterungsoberfläche: die Übersicht](rad-erweiterungsoberflaeche.md)
- [Sicherheit der RAD-Werkzeuge](rad-sicherheit.md)
- [Befehle, Cronjob-Teile und Modul-Views als Klassen](cli-cronjob-und-view-abstraktionen.md)
- [Anleitung zu Erweiterungen](../../anleitungen/extensions.md)
