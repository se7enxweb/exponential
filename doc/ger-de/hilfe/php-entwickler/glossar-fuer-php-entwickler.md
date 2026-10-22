---
title: Glossar für PHP-Entwickler
audience: php-developer, developer
category: php-development
order: 500
summary: Die Begriffe, denen ein PHP-Entwickler in Code und Dokumentation von Exponential begegnet, von Akkumulator und append.php bis Worker und Zygote, jeweils mit einer Bedeutung in einer Zeile und der Seite oder Datei, die ihn erklärt.
keywords: Glossar, Begriffe, Definitionen, Siteaccess, Modul, View, Fetch-Funktion, Datentyp, persistentes Objekt, Klassen-Map, Formular-Token, Worker, Warm-up, Engine-Archiv, Baseline, Erweiterungspunkt, Design-Kette, glossary, terms, definitions, siteaccess, module, view, fetch function, datatype, persistent object, class map, runnable, ezpEvent, form token, Velocity, worker, warm-up, engine archive, baseline, extension point, design chain
type: help
---
# Glossar für PHP-Entwickler

Diese Seite richtet sich an PHP-Entwickler, die in Code oder Dokumentation von Exponential auf einen Begriff stoßen und
seine Bedeutung in einer Zeile möchten. Die Begriffe stehen in alphabetischer Reihenfolge; jeder nennt, wo Sie mehr
lesen können.

| Begriff | Bedeutung |
|---|---|
| Akkumulator (accumulator) | ein Zeitmesser von `eZDebug`, der Zeit und Aufrufe eines Codestücks über eine Anfrage hinweg summiert ([Debugging mit eZDebug](debugging-mit-ezdebug.md)) |
| Anfrageschlüssel (request key) | `eZExecution::requestKey()`, das `REQUEST_TIME_FLOAT` der Anfrage, für Arbeit, die einmal pro Anfrage geschieht |
| `append.php`-Datei | eine INI-Datei namens `*.ini.append.php`, eingeschlossen in einen PHP-Kommentar, damit der Webserver sie niemals anzeigt ([Einstellungen mit eZINI lesen](einstellungen-mit-ezini-lesen.md)) |
| `autoload.php` | die erste Datei jedes Einstiegspunkts: definiert `EXP_ROOT_DIR`, lädt PHP-Kompatibilitätsfunktionen, registriert den Klassen-Map-Autoloader ([Wie eine Anfrage in PHP abläuft](wie-eine-anfrage-in-php-ablaeuft.md)) |
| Baseline (PHPStan) | `phpstan-baseline.neon`, die Befunde, die beim Start der Prüfung akzeptiert wurden; nur neue Befunde schlagen fehl ([Statische Analyse mit PHPStan](statische-analyse-mit-phpstan.md)) |
| `cleanExit()` | `eZExecution::cleanExit()`, beendet eine Anfrage, die ihre eigene Ausgabe gesendet hat; unter Velocity durch eine Exception |
| Cronjob-Teil (cronjob part) | eine benannte Gruppe von Skripten in `cronjob.ini`, ausgeführt von `runcronjobs.php <part>` ([Einen Cronjob-Teil schreiben](einen-cronjob-teil-schreiben.md)) |
| Datentyp (datatype) | die PHP-Klasse hinter einem Feldtyp von Inhaltsklassen, die `eZDataType` erweitert ([Einen Datentyp schreiben](einen-datentyp-schreiben.md)) |
| `.dba`-Datei | eine Datei mit Datenbankschema oder Daten, die der Schema-Handler für jede Engine in DDL umwandelt |
| Debug-Bedingung (debug condition) | ein Schalter in `debug.ini [GeneralCondition]` für Meldungen von `eZDebugSetting::writeDebug()` |
| Deployment (`exp:velocity deploy`) | ein Befehl, der in der richtigen Reihenfolge Autoloads neu generiert, Caches leert, PHP-FPM neu lädt und Velocity neu startet |
| Design-Kette (design chain) | die geordnete Liste der Designs, die nach einem Template durchsucht werden, endend bei `standard` |
| Dienst (Kernel) (service) | eine Klasse, die sich eine View, ein Befehl und ein Cronjob-Teil teilen, etwa `Exponential\Service\Trash` |
| Engine (Velocity) | das Programm, das die Site ausliefert: `qbix` (empfohlen), `frankenphp` oder `php` ([Velocity-Engines](../../bc/6.0/velocity-engines.md)) |
| Engine-Archiv (engine archive) | `dist/engine.phar`, ein optionales Archiv von `kernel/`, `lib/` und `autoload/` ([Das Engine-Archiv (phar) für PHP-Entwickler](das-engine-phar-fuer-php-entwickler.md)) |
| Erweiterung (extension) | ein Verzeichnis unter `extension/` mit eigenen Einstellungen, Klassen, Modulen, Designs; die Einheit für alles, was dem Kernel hinzugefügt wird |
| Erweiterungspunkt (extension point) | eine Stelle, an der sich Code einklinkt, ohne den Kernel zu bearbeiten, aufgelistet unter **Setup > RAD** ([Erweiterungspunkte aus PHP](erweiterungspunkte-aus-php.md)) |
| `EXP_ENV` | der Umgebungsname einer Maschine, in `config.env.php`; wählt INI-Varianten wie `site.ini.dev.append.php` |
| `EXP_ROOT_DIR` | das Wurzelverzeichnis der Installation als absoluter Pfad, definiert von `autoload.php` |
| expservices | eine Erweiterung mit Dienstklassen über ezjscore, mit Auffindbarkeit, einer Hülle und API-Tokens |
| `ezjscore` | die Erweiterung, die registrierte statische PHP-Methoden über HTTP aufruft und in JSON antwortet ([ezjscore-JSON-Endpunkte in PHP](ezjscore-json-endpunkte-in-php.md)) |
| `ezpEvent` | der Ereignis-Hub des Kernels: Ereignisse `notify()` und `filter()`, Listener in `site.ini [Event]` ([Ereignisse, Listener und Filter](ereignisse-listener-und-filter.md)) |
| Fetch-Funktion (fetch function) | eine PHP-Methode, die Templates mit `fetch( 'module', 'function', hash( ... ) )` aufrufen, deklariert in `function_definition.php` ([Eine Fetch-Funktion schreiben](eine-fetch-funktion-schreiben.md)) |
| filter-Ereignis (filter event) | ein Ereignis, dessen Listener einen Wert erhalten und ihn zurückgeben müssen |
| Formular-Token (form token) | das CSRF-Token, das `ezformtoken` POST-Formularen hinzufügt und bei POST prüft ([CSRF-Schutz und ezformtoken](csrf-und-ezformtoken.md)) |
| Handler | eine durch eine Einstellung benannte Klasse, geladen mit `eZExtension::getHandlerClass()` |
| Inhaltsklasse, Inhaltsobjekt, Knoten (content class, object, node) | der Typ eines Inhalts, der Inhalt mit seinen Versionen und Sprachen, ein Ort davon im Baum ([Die Content-API in PHP](../ersteller/anwendungsentwickler/die-content-api.md)) |
| Kernel-Override (kernel override) | das Ersetzen einer Kernel-Klasse über `var/autoload/ezp_override.php`; das letzte Mittel |
| Klassen-Map (class map) | ein generiertes Array von Klassenname zu Datei: `autoload/ezp_kernel.php`, `var/autoload/ezp_extension.php` ([Autoloads und Klassen-Maps](autoloads-und-klassen-maps.md)) |
| Manifest | `share/filelist.md5`, die Prüfsummen, mit denen die Upgrade-Prüfung Dateien vergleicht ([Git-Hooks und make-Ziele](git-hooks-und-make-ziele.md#das-datei-manifest)) |
| Modul, View (module, view) | der erste und zweite Teil einer Systemadresse (`/module/view/...`), deklariert in `module.php` ([Schreiben Sie Ihre erste Modul-View](ihre-erste-modul-view-schreiben.md)) |
| Pagelayout | das Template um den Inhalt jeder Seite (`pagelayout.tpl`) |
| persistentes Objekt (persistent object) | eine Klasse, die `eZPersistentObject` erweitert und eine Tabelle abbildet ([Definitionen persistenter Objekte im Detail](definitionen-persistenter-objekte-im-detail.md)) |
| `phpcompat.php` | `lib/phpcompat.php`, PHP-Funktionen, die neuer als 8.0 sind, nur dort definiert, wo PHP sie nicht hat |
| Präfix `exp` (exp prefix) | das Präfix neuen Projektcodes; `eZ`-Bezeichner des Kernels werden nie umbenannt ([Klassenbenennung und das Präfix exp](klassenbenennung-und-das-praefix-exp.md)) |
| Richtlinie, Einschränkung (policy, limitation) | eine Berechtigung (`module/function`) einer Rolle und die Beschränkung darauf (Abschnitt, Teilbaum, Klasse) |
| Runnable | eine Klassenform eines Befehls, Cronjob-Teils oder einer Modul-View, die `Exponential\Runnable\Runnable` erweitert ([Modul-Views als Klassen](modul-views-als-klassen.md)) |
| Schicht (Einstellungen) (layer) | einer der Orte, aus denen ein INI-Wert stammen kann: Standardwerte, Erweiterungen, Siteaccess, Überschreibungen |
| Siteaccess | ein benannter Satz von Einstellungen, ausgewählt nach Host, Port oder Pfad; bestimmt Design, Sprache und Datenbank |
| statischer Zustand (static state) | Werte in statischen Eigenschaften, statischen Variablen von Funktionen oder globalen Variablen, die die Anfrage in manchen Situationen überleben ([Fallen durch statischen Zustand](fallen-durch-statischen-zustand.md)) |
| Template-Operator (template operator) | eine PHP-Klasse, die die Template-Engine für `{$value\|operator( ... )}` aufruft ([Einen Template-Operator in PHP schreiben](einen-template-operator-in-php-schreiben.md)) |
| Trigger | die Verbindung eines Workflows mit einer Operation, etwa `content/publish/before` |
| Velocity | der Anwendungsserver von Exponential mit persistenten Workern, ab 6.0.15 Teil von Exponential ([Persistente Velocity-Worker für PHP-Entwickler](persistente-velocity-worker-fuer-php-entwickler.md)) |
| Warm-up | der erste Boot des Kernels durch Velocity, den die Worker erben |
| `wash` | der Template-Operator, der einen Wert für HTML maskiert |
| Worker | ein Velocity-Prozess, der viele Anfragen bedient |
| Workflow-Ereignistyp (workflow event type) | ein Schritt, den Administratoren in Workflows einsetzen können, eine Klasse, die `eZWorkflowEventType` erweitert ([Einen Workflow-Ereignistyp schreiben](einen-workflow-ereignistyp-schreiben.md)) |
| Zygote | der Velocity-Prozess, aus dem neue Worker geforkt werden, ohne eigene Besucherverbindung |

## Verwandte Seiten

- [Glossar für Erweiterungsentwickler](../erweiterungsentwicklung/teil-8-referenz/glossar-fuer-erweiterungsentwickler.md)
- [FAQ für PHP-Entwickler](faq-fuer-php-entwickler.md)
- [PHP-Entwickler: hier beginnen](php-entwickler-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
