---
title: Wie eine Anfrage in PHP abläuft
audience: php-developer, developer
category: php-development
order: 30
summary: Der Weg einer Webanfrage durch den PHP-Code von Exponential - index.php, autoload.php, ezpKernel und ezpKernelWeb, Initialisierung der Anfrage, URL-Übersetzung, Zugriffsprüfungen, die Modul-View, das Pagelayout und das Herunterfahren - mit den Ereignissen, auf die Ihr Code unterwegs hören kann.
keywords: Lebenszyklus einer Anfrage, Anfrage, Front-Controller, Pagelayout, Ereignisse, request lifecycle, index.php, autoload.php, ezpKernel, ezpKernelWeb, dispatchLoop, eZModule, eZURLAliasML, pagelayout, module_result, request/input, response/output, eZExecution, front controller
type: help
---
# Wie eine Anfrage in PHP abläuft

Diese Seite richtet sich an PHP-Entwickler, die wissen möchten, was zwischen dem Moment passiert, in dem der Webserver
eine Anfrage an PHP übergibt, und dem Moment, in dem die Seite PHP verlässt. Wenn Sie die Reihenfolge kennen, wissen Sie,
wo Ihr Code läuft, was bereits eingerichtet ist, wenn er läuft, und in welche Ereignisse Sie sich einklinken können. Die
folgenden Namen sind die echten Klassen und Methoden; öffnen Sie die Dateien neben dieser Seite.

## Inhalt

1. [Die Kurzfassung](#die-kurzfassung)
2. [index.php](#indexphp)
3. [autoload.php](#autoloadphp)
4. [ezpKernelWeb: Initialisierung](#ezpkernelweb-initialisierung)
5. [Die Dispatch-Schleife](#die-dispatch-schleife)
6. [Das Pagelayout und die Ausgabe](#das-pagelayout-und-die-ausgabe)
7. [Herunterfahren](#herunterfahren)
8. [Ereignisse unterwegs](#ereignisse-unterwegs)
9. [Die Kommandozeile ist anders](#die-kommandozeile-ist-anders)

## Die Kurzfassung

```text
index.php
  -> autoload.php                    EXP_ROOT_DIR, phpcompat on PHP < 8.1, phar decision, class map autoloader
  -> new ezpKernel( new ezpKernelWeb() )->run()
       ezpKernelWeb::run()
         requestInit()               settings, siteaccess, session, request/preinput and request/input events
         headers                     defaults plus security headers
         dispatchLoop()              URL alias translation, module and view lookup, policy checks, eZModule::run()
         pagelayout                  eZTemplate with $module_result, unless the view switched it off
         response/output filter      the form token is added to POST forms here
         shutdown()
  -> echo $result->getContent()
```

## index.php

`index.php` (der Web-Front-Controller) tut selbst wenig:

1. Setzt eine Standard-Zeitzone, wenn `php.ini` keine hat, `ignore_user_abort( true )` und `error_reporting( E_ALL )`.
2. Beantwortet den Wartungsmodus aus `var/maintenance.json`, bevor Einstellungen oder die Datenbank gelesen werden
   (`expMaintenance::check()`).
3. Bindet `autoload.php` ein.
4. Führt eine Anfragefilter-Datei aus, wenn `config.php` `EXP_REQUEST_PREPEND_FILE` definiert. Sie läuft bei jeder
   Anfrage, auch in einem persistenten Worker, in dem `config.php` selbst nur einmal läuft.
5. Zeigt PHP-Fehler im Browser, wenn `eZINI::environment()` `dev` oder `test` ist (aus `EXP_ENV` in `config.env.php`).
6. Führt den Kernel aus: `$kernel = new ezpKernel( new ezpKernelWeb() ); echo $kernel->run()->getContent();`

## autoload.php

Jeder Einstiegspunkt beginnt hier, im Web wie auf der Kommandozeile:

| Schritt | Warum er für Sie wichtig ist |
|---|---|
| definiert `EXP_ROOT_DIR` | das Wurzelverzeichnis der Installation als absoluter Pfad; verwenden Sie es statt Pfaden, die aus `__DIR__` einer Kernel-Datei berechnet werden, denn diese brechen, wenn der Kernel aus dem Engine-Archiv läuft ([Das Engine-Archiv](das-engine-phar-fuer-php-entwickler.md)) |
| lädt `lib/phpcompat.php`, wenn `PHP_VERSION_ID < 80100` | Funktionen, die neuer als PHP 8.0 sind, gibt es auch unter 8.0 ([Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md)) |
| liest `EXP_ENGINE_PHAR` | ob Kernel- und Bibliotheksklassen aus `dist/engine.phar` kommen |
| hebt die Registrierung des Stream-Wrappers `phar://` auf | sofern die Laufzeitumgebung nicht selbst darüber gelesen wird; ein Bild-Upload kann so niemals als phar eingebunden werden |
| registriert den Autoloader | `autoload/ezp_kernel.php`, `var/autoload/ezp_extension.php` und, mit `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE`, `var/autoload/ezp_override.php` ([Autoloads und Klassen-Maps](autoloads-und-klassen-maps.md)) |

## ezpKernelWeb: Initialisierung

`ezpKernelWeb` (`kernel/private/classes/ezpkernelweb.php`) wird mit der Anfrage konstruiert und bereitet sie vor:

- liest `site.ini` und ermittelt den **Siteaccess** (nach Host, Port oder URI), der die geltenden Einstellungen bestimmt;
- startet die Sitzung (`sessionInit()`) und die Anfrage (`requestInit()`), die `request/preinput` und dann
  `request/input` meldet (`ezpEvent::getInstance()->notify( 'request/input', ... )`); die Formular-Token-Prüfung von
  `ezformtoken` ist ein Listener von `request/input`;
- sendet die Standard-Header (`Cache-Control`, `Content-Type`, `Content-language`, die Sicherheits-Header von
  `ezpKernelWeb::securityHeaders()`), die sich je Pfad über `eZHTTPHeader` überschreiben lassen.

Ein POST ohne gültiges Formular-Token endet hier mit einer 403-Seite, bevor irgendein Modul läuft
([CSRF-Schutz und ezformtoken](csrf-und-ezformtoken.md)).

## Die Dispatch-Schleife

`ezpKernelWeb::dispatchLoop()` macht aus der Adresse ein Modulergebnis:

1. **URL-Übersetzung.** Wenn der URL-Übersetzer aktiviert ist, macht `eZURLAliasML::translate()` aus `/about-us`
   `content/view/full/<node>`, danach werden Platzhalter versucht (`eZURLWildcard::translate()`).
2. **Modulsuche.** `eZModule::accessAllowed()` prüft die Modul- und View-Listen des Siteaccess, `eZModule::exists()`
   lädt die `module.php` des Moduls.
3. **Zugriff.** Der Benutzer braucht `user/login` für den Siteaccess, danach prüft `hasAccessToView()` die `functions`
   der View gegen die Richtlinien des Benutzers. Views in `[RoleSettings] PolicyOmitList[]` überspringen das.
4. **Die View.** `$module->run( $functionName, $params )` führt die View-Datei über `eZProcess::runFile()` aus, das ihr
   `$Params` (mit `$Params['Module']`) übergibt und `$Result` zurücknimmt. Eine klassenbasierte View gibt dasselbe
   Array zurück ([Modul-Views als Klassen](modul-views-als-klassen.md)).
5. **Weiterleitungen und Fehler.** Eine View kann weiterleiten (`$Module->redirectTo()`) oder einen Fehler zurückgeben
   (`$Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' )`), was die Schleife noch einmal durch das Fehlermodul führt.

## Das Pagelayout und die Ausgabe

Sofern die View es nicht ausgeschaltet hat (`$Result['pagelayout'] = false`; ein Template-Name dort wählt ein anderes
Pagelayout), erzeugt der Kernel `eZTemplate::factory()`, setzt `$module_result` (Ihr `$Result` mit `content`, `path`
und mehr) und rendert das `pagelayout.tpl` des Siteaccess
([View-Ergebnisse und das Pagelayout](../ersteller/anwendungsentwickler/ergebnisse-von-ansichten-und-das-pagelayout.md)). Die Ausgabe
durchläuft dann den Filter `response/output`, in dem `ezformtoken` jedem `method="post"`-Formular das Token hinzufügt,
und wird zu einem `ezpKernelResult`.

## Herunterfahren

`shutdown()` schließt die Zeile der Anfrage in den Anfrage-Logs ab (`expRequestLog::finish()`), führt die mit
`eZExecution::addCleanupHandler()` registrierten Aufräum-Handler aus (`eZExecution::cleanup()`), markiert das Ende als
sauber und speichert die Ablaufzeitstempel (`eZExpiryHandler::shutdown()`). Eine View, die ihre eigene Ausgabe sendet
(einen Download, JSON), endet vorzeitig mit `eZExecution::cleanExit()`; unter Velocity beendet dieser Aufruf die Anfrage
durch eine Exception, die der Server behandelt, also umschließen Sie ihn niemals mit `catch ( Exception $e )`
([Persistente Velocity-Worker für PHP-Entwickler](persistente-velocity-worker-fuer-php-entwickler.md)).

## Ereignisse unterwegs

| Ereignis | Art | Wann |
|---|---|---|
| `request/preinput` | notify | bevor die Eingabe der Anfrage verarbeitet wird |
| `request/input` | notify | die Anfrage ist bekannt; Eingabeprüfungen wie das Formular-Token |
| `module/functionlist` | filter | die Liste der Views und Funktionen eines Moduls |
| `content/view` | filter | die Knoten-ID einer Inhaltsansicht, bevor sie geladen wird |
| `response/preoutput`, `response/output` | filter | die gerenderte Seite, bevor sie gesendet wird; geben Sie den Wert zurück |

Wie Sie zuhören: [Ereignisse, Listener und Filter](ereignisse-listener-und-filter.md).

## Die Kommandozeile ist anders

Kommandozeilenskripte führen `ezpKernelWeb` nicht aus. Sie beginnen mit `autoload.php` und dann `eZScript`: `startup()`,
`getOptions()`, `initialize()` (Siteaccess, Erweiterungen, Sitzung, Datenbank) und `shutdown()`. `[Event] Listeners[]`
werden nur an Webanfragen angehängt; `[RunnableSettings] Listeners[]` erreichen auch Befehle
([Einen Konsolenbefehl schreiben](einen-konsolenbefehl-schreiben.md)).

## Verwandte Seiten

- [Ein Rundgang durch den Quellbaum](ein-rundgang-durch-den-quellbaum.md)
- [Das Muster Front-Controller](../../standards/muster/front-controller.md)
- [Kernel-Architektur](../../standards/architektur/kernel-architektur.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
