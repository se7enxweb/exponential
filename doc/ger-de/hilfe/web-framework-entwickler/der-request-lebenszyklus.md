---
title: Der Request-Lebenszyklus
audience: web-framework-developer, developer
category: web-frameworks
order: 40
summary: Was mit einem HTTP-Request in Exponential geschieht, Schritt für Schritt - index.php, der Kernel, die Siteaccess-Erkennung, die Einstellungen, die Sitzung, die Übersetzung von URL-Aliasen und Platzhaltern, die Request-Regeln, das Modul und die Richtlinienprüfungen, die View, das Seitenlayout und die Ausgabefilter - im Vergleich mit der Kernel- oder Middleware-Pipeline anderer Frameworks.
keywords: Request-Lebenszyklus, Front Controller, index.php, ezpKernel, ezpKernelWeb, dispatchLoop, Siteaccess, eZURLAliasML, eZURLWildcard, eZModule, Seitenlayout, pagelayout, response/output, request/input, Pipeline, request lifecycle, web framework developer, Web-Framework-Entwickler
type: help
---
# Der Request-Lebenszyklus

Diese Seite richtet sich an Entwickler, die wissen möchten, wo ihr Code bei einem Web-Request ausgeführt wird, so wie
sie die `HttpKernel`-Ereignisse von Symfony, die Middleware-Pipeline von Laravel oder den Request- und Response-Zyklus
von Django kennen. Die maßgebliche Beschreibung ist [Der Request-Lebenszyklus in der Kernel-Architektur](../../standards/architektur/kernel-architektur.md#der-lebenszyklus-einer-anfrage);
diese Seite geht ihn mit den Augen eines Framework-Entwicklers durch.

## Inhalt

1. [Die Schritte](#die-schritte)
2. [Dieselben Schritte als Pipeline](#dieselben-schritte-als-pipeline)
3. [Wo Sie sich einklinken können](#wo-sie-sich-einklinken-können)
4. [Unter persistenten Workern](#unter-persistenten-workern)
5. [Requests auf der Kommandozeile](#requests-auf-der-kommandozeile)

## Die Schritte

1. **`index.php`.** Wenn `var/maintenance.json` existiert, kann `expMaintenance::check()` antworten (Wartungsmodus).
   Ein Request mit `exp_repair` wird an `ezpRepairQueue` übergeben. Danach lädt `autoload.php` den Autoloader von
   Composer und die erzeugten Klassenzuordnungen, `config.env.php` und `config.php` werden gelesen, und
   `$kernel = new ezpKernel( new ezpKernelWeb() ); echo $kernel->run()->getContent();` führt den Request aus.
2. **Aufbau des Kernels.** `ezpKernelWeb` liest `i18n.ini` und `site.ini`, aktiviert die globalen Erweiterungen,
   ermittelt den **Siteaccess** mit `eZSiteAccess::match()` anhand von Request-URI und Host, wechselt mit
   `eZSiteAccess::change()` zu ihm, aktiviert die Erweiterungen des Siteaccess und lädt die Einstellungen neu, damit
   die Siteaccess-Ebene greift ([Siteaccesses](siteaccesses-hosts-und-mandanten.md)).
3. **Initialisierung von Sitzung und Request** (`sessionInit()`, `requestInit()`), danach die Ereignisse
   `request/preinput` und `request/input`. Die CSRF-Prüfung von `ezformtoken` ist ein Listener auf `request/input`.
4. **URL-Übersetzung.** Ein URL-Alias (`/About-us`) wird über `eZURLAliasML` zu einer Systemadresse
   (`content/view/full/58`); URL-Platzhalter werden über `eZURLWildcard` angewendet ([Sprechende URLs](url-aliase-und-platzhalter.md)).
5. **Modulsuche und Zugriffsprüfungen.** Das Modul muss in einem Modul-Repository existieren und erlaubt sein
   (`eZModule::accessAllowed()`); die Anmeldeprüfung des Siteaccess (Richtlinie `user/login`) und die
   Richtlinienfunktionen der View (`eZUser::hasAccessToView()`) laufen vor der View. Request-Regeln entscheiden nach der
   Alias-Übersetzung und nachdem die Richtlinien die View erlaubt haben ([Middleware: Request-Regeln](middleware-und-request-regeln.md)).
6. **Die View läuft.** `$module->run( $functionName, $params, ... )` führt das View-Skript oder die Klasse
   `ModuleView` aus, die ein `$Result`-Array zurückgibt. Eine View kann einen erneuten Durchlauf mit einer anderen URI
   oder eine Weiterleitung anfordern (`eZModule::STATUS_RERUN`, `STATUS_REDIRECT`); die Dispatch-Schleife behandelt
   beides ([Controller](controller-als-modul-views.md)).
7. **Seitenlayout und Ausgabe.** Der `content` des Ergebnisses wird in das `pagelayout.tpl` des Siteaccess eingesetzt
   (sofern die View kein anderes oder gar keines gewählt hat), durch die Ausgabefilter `response/preoutput` und
   `response/output` geleitet und zurückgegeben. `ezformtoken` fügt in einem Listener auf `response/output` sein
   verstecktes Token in Formulare ein.
8. **Abschluss.** Ausstehende Daten werden geschrieben; unter Velocity setzt der Worker seinen Zustand für den nächsten
   Request zurück.

## Dieselben Schritte als Pipeline

| Aufgabe | Symfony | Laravel | Django | Exponential |
|---|---|---|---|---|
| Einstieg | `public/index.php`, `Kernel::handle()` | `public/index.php`, HTTP-Kernel | WSGI- oder ASGI-Handler | `index.php`, `ezpKernel::run()` |
| Frühe Antwort | Listener auf `kernel.request` | globale Middleware | Middleware | Wartungsprüfung, früher Ausstieg des HTTP-Cache, Response-Cache von Velocity |
| Mandant oder Site | Host-Anforderung, eigener Listener | Domain-Routen | `Site`-Framework | Siteaccess-Erkennung |
| Routing | Router | Router | URLconf | Übersetzung von URL-Alias und Platzhaltern, danach `<module>/<view>` |
| Autorisierung vor der Aktion | Firewall, Voter | Middleware `auth` und `can` | Decorators | `functions` der View, Anmeldeprüfung des Siteaccess, Request-Regeln |
| Aktion | Controller | Controller | View-Funktion | Modul-View |
| Darstellung | Twig | Blade | Templates | Template-Sprache und Seitenlayout |
| Response-Filter | `kernel.response` | terminierbare oder nachgelagerte Middleware | Middleware | Filter `response/output` |

## Wo Sie sich einklinken können

| Bedarf | Einstiegspunkt | Lesen |
|---|---|---|
| Vor dem Routing antworten oder ablehnen | Listener auf `request/preinput` oder `request/input` | [Ereignisse und Listener](ereignisse-und-listener.md) |
| Per Regel ablehnen, weiterleiten, umschreiben oder protokollieren, sobald der Alias bekannt ist | Request-Regeln | [Middleware](middleware-und-request-regeln.md) |
| Eine neue Adresse | ein Modul und eine View in Ihrer Erweiterung | [Routing](routing-mit-modulen-und-views.md) |
| Eine Kernel-View ändern | eine Neuimplementierung der View-Klasse (`site.ini [RunnableSettings] Implementation[]`) oder ein Modul-Override | [Controller](controller-als-modul-views.md#eine-kernel-view-ändern) |
| Das endgültige HTML ändern | Filter-Listener auf `response/output` | [Ereignisse und Listener](ereignisse-und-listener.md) |
| Rund um Inhaltsoperationen ausführen | Workflows und Trigger | [Workflows](workflows-trigger-und-domain-events.md) |

## Unter persistenten Workern

Unter Velocity wird die Anwendung einmal in einem Elternprozess geladen und in Worker geforkt, die jeweils viele
Requests bedienen. Die Schritte 2 bis 8 laufen pro Request, aber alles, was eine Klasse in einer statischen
Eigenschaft hält, überlebt bis in den nächsten Request, sofern es nicht zurückgesetzt wird. Die Regeln des Kernels:
kein Request-bezogener Zustand in statischen Eigenschaften ohne Zurücksetzen, kein `exit` zur Ablaufsteuerung außer
über `eZExecution::cleanExit()`, und eine geänderte Klasse oder Einstellung erreicht die Worker erst nach
`./console exp:velocity restart` oder `exp:velocity deploy`
([Server: Velocity, PHP-FPM und FrankenPHP](../../standards/architektur/kernel-architektur.md#server-velocity-php-fpm-und-frankenphp)).
Wenn Sie Laravel Octane oder Symfony auf RoadRunner kennen: Es gilt dieselbe Disziplin.

## Requests auf der Kommandozeile

Konsolenbefehle, Cronjob-Teile und Queue-Worker verwenden `ezpKernelWeb` nicht. Sie starten über `eZScript`, das die
Einstellungen für den mit `-s` angegebenen Siteaccess lädt, und laufen als anonymer Benutzer, sofern der Code keinen
Benutzer anmeldet ([Kommandozeile](cli-und-konsolenbefehle.md)).

## Verwandte Seiten

- [Architektur für Anwendungsentwickler](../ersteller/anwendungsentwickler/architektur-fuer-anwendungsentwickler.md)
- [Das Front-Controller-Muster](../../standards/muster/front-controller.md)
- [Velocity: der Server mit persistenten Workern](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
