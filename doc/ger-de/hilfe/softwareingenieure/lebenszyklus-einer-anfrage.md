---
title: Der Lebenszyklus einer Anfrage
audience: software-engineer, developer
category: software-engineering
order: 110
summary: Was in Exponential zwischen einer Browseranfrage und der Antwort geschieht, Schritt für Schritt - Wartungs- und Reparatur-Hooks, Autoload, der Konstruktor des Kernels, Siteaccess-Zuordnung, URL-Übersetzung, Zugriffsprüfungen, der Modullauf, das Pagelayout, Ausgabefilter und Herunterfahren - und was sich unter Velocity, auf der Kommandozeile und dann ändert, wenn ein Cache zuerst antwortet.
keywords: Lebenszyklus einer Anfrage, Anfrageablauf, index.php, ezpKernel, ezpKernelWeb, Weiterleitungsschleife, Siteaccess, URL-Alias, eZModule, Pagelayout, Ausgabefilter, Events, Herunterfahren, Softwareingenieur, request lifecycle, request flow, dispatch loop, output filter, shutdown, software engineer
type: help
---
# Der Lebenszyklus einer Anfrage

Diese Seite folgt einer Webanfrage durch Exponential, von `index.php` bis zur Antwort, und nennt Klasse und Datei jedes
Schritts. Sie ist die Zusammenfassung von
[Der Lebenszyklus einer Anfrage](../../standards/architektur/kernel-architektur.md#der-lebenszyklus-einer-anfrage) im Standard
zur Kernel-Architektur; lesen Sie diesen Abschnitt, wenn Sie etwas auf diesem Weg ändern.

## Inhalt

1. [Vor dem Kernel](#vor-dem-kernel)
2. [Der Konstruktor des Kernels](#der-konstruktor-des-kernels)
3. [Die Weiterleitungsschleife](#die-weiterleitungsschleife)
4. [Ausgabe und Herunterfahren](#ausgabe-und-herunterfahren)
5. [Abkürzungen: wer zuerst antworten kann](#abkürzungen-wer-zuerst-antworten-kann)
6. [Derselbe Code außerhalb einer Webanfrage](#derselbe-code-außerhalb-einer-webanfrage)
7. [Regeln für Änderungen auf diesem Weg](#regeln-für-änderungen-auf-diesem-weg)

## Vor dem Kernel

1. **Wartungs- und Reparatur-Hooks.** Existiert `var/maintenance.json`, kann `expMaintenance::check()`
   (`kernel/classes/expmaintenance.php`) die Anfrage mit der Wartungsseite beantworten. Eine Anfrage mit `exp_repair`
   wird an `ezpRepairQueue` (`lib/ezutils/classes/ezprepairqueue.php`) übergeben, das eine Installation ohne ihre
   Composer-Bibliotheken reparieren kann ([Reparatur aus dem Browser](../../funktionen/6.0/reparatur-im-browser.md)).
2. **Autoload.** `autoload.php` lädt die erzeugten Klassenzuordnungen: `autoload/ezp_kernel.php` und die Zuordnung der
   Erweiterungen, die `php bin/php/ezpgenerateautoloads.php -e` schreibt.
3. **Kernel-Objekt.** `new ezpKernel( new ezpKernelWeb() )`, dann `$kernel->run()->getContent()`. `ezpKernel` ist eine
   dünne Hülle um einen Handler, der `ezpWebBasedKernelHandler` implementiert.

## Der Konstruktor des Kernels

`ezpKernelWeb` (`kernel/private/classes/ezpkernelweb.php`), der Reihe nach:

| Schritt | Was geschieht |
|---|---|
| Einstellungen | liest `i18n.ini` und `site.ini`, setzt Zeitzone und Dateiberechtigungen, initialisiert `eZSys` |
| Globale Erweiterungen | `eZExtension::activateExtensions( 'default' )` |
| Siteaccess | `eZSiteAccess::match()` auf die Anfrage-URI, sofern keiner injiziert wurde, dann `eZSiteAccess::change()` |
| Siteaccess-Erweiterungen | `eZExtension::activateExtensions( 'access' )`, und die INI-Instanzen werden zurückgesetzt, damit Siteaccess-Einstellungen gelten |

## Die Weiterleitungsschleife

`run()` und `dispatchLoop()`:

1. Initialisierung von Session und Anfrage (`sessionInit()`, `requestInit()`).
2. Die Events `request/preinput` und `request/input` ([Events, Erweiterungspunkte und Plugins](events-erweiterungspunkte-und-plugins.md)).
3. URL-Übersetzung: ein URL-Alias oder -Platzhalter wird zu einer Moduladresse (`eZURLAliasML`, `eZURLWildcard`);
   `/About-us` wird zum Beispiel zu `content/view/full/58`.
4. Die Modulprüfung (`eZModule::accessAllowed()`), `fetchModule()`, die Anmeldeprüfung des Siteaccess (die Richtlinie
   `user/login`) und die Zugriffsprüfung der View (`eZUser::hasAccessToView()`).
5. Anfrageregeln entscheiden, wenn konfiguriert, nach der URL-Übersetzung und der Richtlinienprüfung, ob erlaubt,
   verweigert, umgeleitet, umgeschrieben oder protokolliert wird ([Anfrageregeln](../../funktionen/6.0/anfrageregeln.md)).
6. `$module->run( $functionName, $params, ... )` führt die View aus. Eine View kann einen erneuten Lauf mit einer anderen
   URI oder eine Weiterleitung anfordern (`eZModule::STATUS_RERUN`, `STATUS_REDIRECT`); die Schleife behandelt beides.

## Ausgabe und Herunterfahren

- Das `$Result` der View wird in das Pagelayout gesetzt (`pagelayout.tpl`, sofern die View kein anderes setzt), durch
  `eZDisplayResult()` und die Ausgabefilter `response/preoutput` und `response/output` geleitet
  (`kernel/private/classes/global_functions.php`).
- `shutdown()` schreibt ausstehende Daten. Unter Velocity wird der Worker danach für die nächste Anfrage zurückgesetzt
  ([Persistente Worker von Velocity](persistente-worker-von-velocity.md)).
- Jede Anfrage wird einmal klassifiziert, wie auch immer sie endet, und eine Nicht-gefunden-Antwort oder ein
  Sondierungsversuch geht in ein eigenes Log ([Anfrage-Logs](../../funktionen/6.0/anfrageprotokolle.md)).

## Abkürzungen: wer zuerst antworten kann

| Wer | Wann | Kernel gestartet? |
|---|---|---|
| Browser oder CDN | die Seite oder Datei ist in deren Cache noch frisch | gar keine Anfrage |
| Antwort-Cache von Velocity | eine öffentliche Seite liegt im Serverprozess im Cache | nein |
| Rollenbewusster HTTP-Cache | eingeschaltet, und eine gespeicherte Ansicht passt zum Berechtigungskontext | nein (ein früher Ausstieg aus `config.php` unter PHP-FPM) |
| Wartungs-Hook | der Wartungsmodus ist an | teilweise (vor dem Kernel) |
| View-Cache für Inhalte | eine zwischengespeicherte Kopie dieser Inhaltsansicht existiert | ja, aber die Ansicht wird nicht gerendert |

Details: [Caching-Ebenen für Ingenieure](caching-ebenen-fuer-ingenieure.md).

## Derselbe Code außerhalb einer Webanfrage

Kommandozeilenskripte und Cronjobs verwenden nicht `ezpKernelWeb`: Sie starten über `eZScript`. Die Konsole
(`bin/php/console`) findet Befehle; `runcronjobs.php` führt Cronjob-Teile aus
([Konsole, Cronjobs und Hintergrundarbeit](konsole-cron-und-hintergrundarbeit.md)).

## Regeln für Änderungen auf diesem Weg

- Eine Änderung **muss** unter PHP-FPM, Velocity und FrankenPHP weiter funktionieren.
- Code in `ezpKernelWeb` sichert neuere Klassen mit `class_exists()` ab, weil ein langlaufender Velocity-Worker seine
  Klassenzuordnung geladen haben kann, bevor die Klasse existierte; neue Kernel-Hooks sollten es genauso tun.
- Zustand je Anfrage **darf nicht** ohne Zurücksetzen in statischen Eigenschaften liegen.
- Kein `exit` zur Ablaufsteuerung, außer über `eZExecution::cleanExit()`.

## Verwandte Seiten

- [Architekturüberblick](architekturueberblick.md)
- [Module, Views und Zugriffskontrolle](module-views-und-zugriffskontrolle.md)
- [Nebenläufigkeit, Statics und langlebige Prozesse](nebenlaeufigkeit-statics-und-langlebige-prozesse.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
