---
title: Der Kernel-Service-Container
audience: php-developer, developer
category: php-development
order: 445
summary: Wie PHP-Code den Kernel nach gemeinsam genutzten Objekten über ihren Namen fragt (expKernelServices::get()), wie Sie einen Dienst in kernelservices.ini mit einer Klasse oder Factory, Argumenten, einem Gültigkeitsbereich und Tags definieren, was die Gültigkeitsbereiche worker, request und prototype unter PHP-FPM und Velocity bedeuten und wie Sie den Container kompilieren und prüfen.
keywords: Service-Container, Dienst, Gültigkeitsbereich, Dependency Injection, kompilierter Container, service container, expKernelServices, kernelservices.ini, Service_, scope, worker scope, request scope, prototype, tags, tagged, dependency injection, Arguments, Factory, exp:services:compile, exp:services:check, exp:services:list, exp:services:show, onRequestReset, Velocity, compiled container
type: help
---
# Der Kernel-Service-Container

Diese Seite richtet sich an PHP-Entwickler, die Kernel-Code oder Erweiterungen schreiben und gemeinsam genutzte Objekte
des Kernels verwenden oder anbieten möchten. Ab 6.0.15 hat der Kernel einen Service-Container: Objekte nach Namen, in
den Einstellungen definiert, beim ersten Abruf erzeugt und in einem Gültigkeitsbereich gehalten. Die Referenz für
Mitwirkende mit allen Einzelheiten ist [der Kernel-Service-Container](../../bc/6.0/kernel-dienste.md).

## Inhalt

1. [Einen Dienst abrufen](#einen-dienst-abrufen)
2. [Einen Dienst definieren](#einen-dienst-definieren)
3. [Gültigkeitsbereiche](#gültigkeitsbereiche)
4. [Hooks zum Zurücksetzen pro Anfrage](#hooks-zum-zurücksetzen-pro-anfrage)
5. [Kompilieren und prüfen](#kompilieren-und-prüfen)
6. [Testen](#testen)

## Einen Dienst abrufen

```php
$db = expKernelServices::get( 'db' );              // eZDB::instance()
$site = expKernelServices::get( 'ini.site' );      // eZINI::instance( 'site.ini' )
if ( expKernelServices::has( 'my.mailer' ) ) { /* ... */ }
foreach ( expKernelServices::tagged( 'kernel.core' ) as $id => $service ) { /* ... */ }
```

Die vorhandenen Aufrufe wie `eZDB::instance()` und `eZINI::instance()` funktionieren weiterhin und liefern dieselben
Objekte; wechseln Sie zu `expKernelServices::get()`, wenn es Ihnen passt. Jede ID `ini.<name>` ist
`eZINI::instance( '<name>.ini' )` und braucht keine Definition.

## Einen Dienst definieren

In `extension/<yours>/settings/kernelservices.ini.append.php` (oder in `settings/kernelservices.ini` des Kernels):

```ini
[Service_my.mailer]
Class=myMailer
Arguments[]=@db
Arguments[]=%ini:mymailer.ini/MailSettings/Sender%
Arguments[]=plain text
Scope=request
Tags[]=my.senders
Description=The mailer of my extension.

[Tags]
TagList[]=my.senders
```

- `Class` wird mit `new Class( ...Arguments )` erzeugt; mit `Factory=myMailer::create` wird stattdessen diese statische
  Methode aufgerufen.
- `@<id>` ist ein anderer Dienst, `%ini:<file>/<Section>/<Variable>%` eine Einstellung (null, wenn sie nicht gesetzt
  ist), alles andere wird so übergeben, wie es geschrieben ist (`@@x` und `%%x` für ein wörtliches `@x` oder `%x`).
- Ein späterer Block gleichen Namens gewinnt, wie überall in den Einstellungen; `Enabled=false` schaltet einen Dienst ab.
- Nichts wird zur Laufzeit per Reflection oder Durchsuchen gefunden: Die Definition sagt alles.

## Gültigkeitsbereiche

| Gültigkeitsbereich | PHP-FPM, Apache | Velocity (persistente Worker) | Verwenden Sie ihn für |
|---|---|---|---|
| `worker` | einer pro Anfrage | einer pro Worker und Siteaccess, erhalten für jede Anfrage, die der Worker bedient | einstellungsartige Objekte, die **nichts von einer Anfrage** halten |
| `request` (Standard) | einer pro Anfrage | einer pro Anfrage | alles, was den Besucher, die Sitzung, die URI oder das Datenbank-Handle der Anfrage kennt |
| `prototype` | jedes Mal neu | jedes Mal neu | kleine Wertobjekte |

Ein Dienst mit Gültigkeitsbereich `worker`, der die Daten einer Anfrage behält, liefert sie unter Velocity an den
nächsten Besucher aus. `exp:services:check` lehnt einen Dienst mit Gültigkeitsbereich `worker` ab, der einen Dienst mit
Gültigkeitsbereich `request` als Argument nimmt. Im Zweifel verwenden Sie `request`; lesen Sie
[Fallen durch statischen Zustand](fallen-durch-statischen-zustand.md) und
[Persistente Velocity-Worker für PHP-Entwickler](persistente-velocity-worker-fuer-php-entwickler.md).

Für ein wirklich dynamisches Objekt gibt es `expKernelServices::set( 'my.thing', $object )`: nur für den Rest dieser
Anfrage, nie erhalten und nie kompiliert.

## Hooks zum Zurücksetzen pro Anfrage

Der Web-Kernel beginnt jede Anfrage mit `expKernelServiceScope::resetRequest()`: zuerst die eigenen Zurücksetzungen des
Kernels (Abfrage-Cache, SQL-Profil, Nonce der Content Security Policy, Audit, Render-Statistiken, Anfrage-Log und
Klassifizierer), dann Ihre:

```php
expKernelServiceScope::onRequestReset( 'my.extension', array( 'myCache', 'resetRequest' ) );
```

Eine erneute Registrierung unter demselben Namen ersetzt den Hook, sodass eine Registrierung bei jeder Anfrage in
Ordnung ist; der Hook bleibt mit dem Worker erhalten und läuft zu Beginn jeder späteren Anfrage, auch unter Velocity.

## Kompilieren und prüfen

```bash
./console exp:services:compile      # also done by exp:velocity deploy and ezcache.php --clear-tag=ini
./console exp:services:check        # every siteaccess: definitions and compiled files, exit 1 on an error
./console exp:services:list --tag=my.senders -s site
./console exp:services:show my.mailer
```

Der kompilierte Container (`var/cache/kernelservices/`) enthält auch die aufgezeichnete Aktivierung der Erweiterungen
jedes Siteaccess, die der Web-Kernel anwendet, statt jedes Erweiterungsverzeichnis nachzuschlagen: Wie das korrekt
bleibt, steht unter [der Kernel-Service-Container](../../bc/6.0/kernel-dienste.md).

## Testen

Tests ohne Datenbank brauchen keinen kompilierten Container: Ohne einen liest der Container `kernelservices.ini`, wie es
ist. Rufen Sie `expKernelServices::reset()` in `tearDown()` auf, wenn ein Test Dienste oder Worker-Zustand setzt. Die
eigenen Tests des Kernels stehen in `tests/tests/kernel/classes/expKernelServicesTest.php`
([PHPUnit ohne Datenbank](phpunit-ohne-datenbank.md)).

## Mehr erfahren

- [Der Kernel-Service-Container (Referenz für Mitwirkende)](../../bc/6.0/kernel-dienste.md)
- [Einstellungen mit eZINI lesen](einstellungen-mit-ezini-lesen.md)
- [Einen Konsolenbefehl schreiben](einen-konsolenbefehl-schreiben.md)
- [Wie eine Anfrage in PHP abläuft](wie-eine-anfrage-in-php-ablaeuft.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
