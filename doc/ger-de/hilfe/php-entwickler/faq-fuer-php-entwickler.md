---
title: FAQ für PHP-Entwickler
audience: php-developer, developer
category: php-development
order: 490
summary: Kurze Antworten auf die Fragen, die PHP-Entwickler zu Exponential am häufigsten stellen - PHP-Versionen, PSR-4 und Namespaces, Typdeklarationen, Klasse nicht gefunden, nicht angewendete Einstellungen, Änderungen ohne Wirkung unter Velocity, Datenbanken, Tests ohne Datenbank, CI-Fehlschläge und wo Sie beginnen.
keywords: FAQ, häufige Fragen, Fragen von PHP-Entwicklern, Klasse nicht gefunden, Einstellungs-Cache, Velocity-Neustart, Datenbanken, kurze Array-Syntax, PHP developer questions, PSR-4, namespaces, strict_types, class not found, settings cache, Velocity restart, PHP 8.0, databases, PHPUnit, CI, phpstan baseline, Symfony, Composer, short array syntax
type: help
---
# FAQ für PHP-Entwickler

Diese Seite richtet sich an PHP-Entwickler, die eine schnelle Antwort möchten. Jede Antwort verweist auf die Seite mit
den Einzelheiten.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../hosting/control-panels/plesk.md), [cPanel](../hosting/control-panels/cpanel.md), [DirectAdmin](../hosting/control-panels/directadmin.md), [ISPConfig](../hosting/control-panels/ispconfig.md).

## Inhalt

1. [Plattform und Sprache](#plattform-und-sprache)
2. [Klassen und Einstellungen](#klassen-und-einstellungen)
3. [Velocity und Deployments](#velocity-und-deployments)
4. [Datenbanken](#datenbanken)
5. [Tests und CI](#tests-und-ci)
6. [Wo Sie beginnen](#wo-sie-beginnen)

## Plattform und Sprache

**Welche PHP-Versionen muss mein Code unterstützen?**
PHP 8.0 bis 8.5 (PHP 7.4 ohne Support nutzbar). PHP 8.0 ist die älteste, weil es das Standard-PHP von RHEL 9 ist
([Unterstützte PHP-Versionen: 8.0 bis 8.5](unterstuetzte-php-versionen.md)).

**Darf ich Enums, readonly-Eigenschaften oder `foo(...)` verwenden?**
Nicht in Code, der mit oder für Exponential ausgeliefert wird: PHP 8.0 kann sie nicht parsen
([Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md)).

**Warum kein `declare( strict_types=1 )` und keine Rückgabetypen?**
Der Coding-Standard verbietet sie in neuem Code, zur Konsistenz mit dem vorhandenen Code, zur Kompatibilität von
Unterklassen und wegen PHP 8.0 ([Warum der Code keine Typdeklarationen hat](warum-keine-typdeklarationen.md)).

**Warum `array()` und nicht `[]`?**
Der Hausstil, am vorhandenen Code gemessen und von `phpcs.xml.dist` geprüft ([Der Coding-Standard](der-coding-standard.md)).

**Folgt Exponential PSR-4 oder PSR-12?**
Nein. Klassen werden über generierte Klassen-Maps gefunden, und der Code-Stil ist der eigene des Kernels
([PHP-, PHP-FIG- und Composer-Standards](../../standards/php-und-composer.md)).

**Darf ich Namespaces verwenden?**
Ja. Der Kernel verwendet sie für Befehle, Cronjob-Teile, Views und Dienste (`Exponential\Command\...`); andere Klassen
sind global mit einem Präfix ([Klassenbenennung und das Präfix exp](klassenbenennung-und-das-praefix-exp.md)).

**Gibt es eine Symfony-Schicht?**
Exponential 6 läuft auf seinem eigenen Kernel. Die Legacy Bridge führt es innerhalb einer Installation der
Symfony-Plattform aus ([Symfony und die Legacy Bridge](../ersteller/anwendungsentwickler/symfony-und-die-legacy-bridge.md)).

## Klassen und Einstellungen

**Meine neue Klasse wird „nicht gefunden“.**
Führen Sie `php bin/php/ezpgenerateautoloads.php -e` aus; unter Velocity starten Sie außerdem die Worker neu
(`exp:velocity deploy`) ([Autoloads und Klassen-Maps](autoloads-und-klassen-maps.md)).

**Ich habe eine Einstellung geändert, und nichts ist passiert.**
Leeren Sie den INI-Cache mit `--clear-tag=ini` (nicht `--clear-id=ini`), prüfen Sie mit `exp:ini where`, welche Schicht
gewinnt, und starten Sie die Worker von Velocity neu ([Einstellungen mit eZINI lesen](einstellungen-mit-ezini-lesen.md)).

**Wie lege ich `.htaccess` und `config.php` an?**
`./console exp:setup:htaccess --apply` und `./console exp:setup:config --apply` kopieren sie aus `.htaccess_root` und
`config.php-RECOMMENDED`; eine vorhandene, abweichende Datei wird als Diff gezeigt und ohne `--force` behalten
([.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)).

**Wie ändere ich eine Einstellung in `config.php` ohne Editor?**
`./console exp:config:set <NAME> <value> [--type=bool|int|octal|float|string|null] --apply`. Die Datei wird als
PHP-Tokens gelesen, nie ausgeführt; nur der Wert dieses einen `define()` ändert sich, und das Ergebnis muss `php -l`
bestehen. Eine Konstante Ihrer eigenen Extension braucht `--allow-unknown`. Unter Velocity starten Sie danach die
Worker neu ([.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)).

**Sollte ich `eZContentObject` und die anderen `eZ`-Klassen umbenennen?**
Niemals. Es sind echte Bezeichner, die jede Erweiterung verwendet; neuer Code verwendet `exp` oder Ihr eigenes Präfix.

**Wie ändere ich eine Kernel-View, ohne sie zu kopieren?**
Leiten Sie von ihrer Klasse ab und registrieren Sie die Unterklasse in `site.ini [RunnableSettings] Implementation[]`
([Modul-Views als Klassen](modul-views-als-klassen.md#eine-kernel-view-neu-implementieren)).

## Velocity und Deployments

**Meine Änderung an einer PHP-Datei bewirkt nichts.**
Unter Velocity behalten die Worker geladene Klassen: `exp:velocity deploy`. Mit eingeschaltetem Engine-Archiv braucht
eine Kernel-Änderung einen Neubau, den das Deployment erledigt
([Das Engine-Archiv (phar) für PHP-Entwickler](das-engine-phar-fuer-php-entwickler.md)).

**Eine Seite zeigt die Daten eines anderen Benutzers oder Siteaccess, nur unter Velocity.**
Zustand, der zwischen Anfragen erhalten bleibt: eine statische Variable in einer Funktion, ein zu knapp geschlüsselter
Cache, ein Flag für einmaliges Registrieren ([Fallen durch statischen Zustand](fallen-durch-statischen-zustand.md)).

**Meine Download-View gibt nach der Datei die Seite aus.**
`eZExecution::cleanExit()` steht innerhalb eines `catch ( Exception )`. Unter Velocity beendet es die Anfrage mit einer
Exception; fangen Sie sie nicht ab ([Persistente Velocity-Worker für PHP-Entwickler](persistente-velocity-worker-fuer-php-entwickler.md)).

**Behält FrankenPHP Zustand zwischen Anfragen?**
Nein. Es läuft im klassischen Modus, mit einem sauberen Zustand pro Anfrage ([FrankenPHP für PHP-Entwickler](frankenphp-fuer-php-entwickler.md)).

## Datenbanken

**Auf welcher Datenbank sollte ich entwickeln?**
SQLite 3 ist der Standard und braucht nichts; testen Sie vor einem Release auch auf einer Server-Datenbank
([Code für jede unterstützte Datenbank](code-fuer-jede-unterstuetzte-datenbank.md)).

**Wie vermeide ich SQL-Injection?**
`escapeString()` für Strings, `(int)` für Zahlen oder Bedingungen von `eZPersistentObject` ([SQL mit eZDB, sicher](sql-mit-ezdb.md)).

**Mein SQL liefert unter MongoDB nichts.**
Der Adapter übersetzt nur die SQL-Formen des Kernels; nicht übersetzbares SQL liefert ein leeres Ergebnis und
protokolliert `MONGO TODO`.

## Tests und CI

**Brauche ich eine Datenbank, um die Tests auszuführen?**
Nein. `vendor/bin/phpunit` führt die Suites ohne Datenbank aus; Datenbanktests laufen separat mit
`tests/runtests.php --dsn ...` ([PHPUnit ohne Datenbank](phpunit-ohne-datenbank.md), [Datenbank- und Live-Tests](datenbank-und-live-tests.md)).

**PHPStan schlägt bei Code fehl, den ich nicht angefasst habe.**
Nur Befunde, die in `phpstan-baseline.neon` fehlen, schlagen fehl; prüfen Sie, ob Ihre Änderung diesen Code verschoben
oder geändert hat ([Statische Analyse mit PHPStan](statische-analyse-mit-phpstan.md)).

**Die CI ist rot, aber lokal besteht alles.**
Meistens liest ein Test die Einstellungen Ihrer Installation, oder die CI verwendet ein älteres PHPUnit oder PHP
([CI und das Nachstellen einer roten Prüfung](ci-und-eine-rote-pruefung-nachstellen.md)).

**Der commit-msg-Hook lehnt meinen Commit ab.**
Beginnen Sie den Betreff mit `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder `Renamed: `
([Git-Hooks und make-Ziele](git-hooks-und-make-ziele.md)).

## Wo Sie beginnen

Lesen Sie [Exponential für PHP-Entwickler](exponential-fuer-php-entwickler.md), richten Sie einen Checkout ein, und
arbeiten Sie dann [Schreiben Sie Ihre erste Modul-View](ihre-erste-modul-view-schreiben.md) durch. Die vollständige
Reihenfolge ist [Der Lesepfad für PHP-Entwickler](lesepfad-fuer-php-entwickler.md).

## Verwandte Seiten

- [PHP-Entwickler: hier beginnen](php-entwickler-hier-beginnen.md)
- [Anwendungsentwickler: häufig gestellte Fragen](../ersteller/anwendungsentwickler/faq-fuer-anwendungsentwickler.md)
- [Software-Ingenieure: hier beginnen](../softwareingenieure/softwareingenieure-hier-beginnen.md),
  [Web-Framework-Entwickler: hier beginnen](../web-framework-entwickler/web-framework-entwickler-hier-beginnen.md),
  [das Buch für Erweiterungsentwickler](../erweiterungsentwicklung/README.md)
- [Glossar für PHP-Entwickler](glossar-fuer-php-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
