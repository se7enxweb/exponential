---
title: FAQ für CMS-Entwickler
audience: cms-developer
category: cms-development
order: 48
summary: Für CMS-Entwickler kurze Antworten auf die Fragen, die Entwickler in ihren ersten Wochen mit Exponential stellen. Sie behandeln das Modell, Templates, Caches, APIs, Multisite, die Produktfamilie und das Projekt, jeweils mit einem Link auf die Seite, die es erklärt.
keywords: FAQ, Fragen, Antworten, CMS-Entwickler, Inhaltsmodell, Knoten, Objekt, Template-Änderung erscheint nicht, Cache, REST, Headless, GraphQL, Multisite, Exponential Platform, Velocity, PHP-Version, Datenbank, Lizenz, wer führt Exponential, questions, answers, CMS developer, content model, node, object, template change not showing, database, license, who leads Exponential
type: help
---
# FAQ für CMS-Entwickler

Diese Seite richtet sich an Entwickler, die neu bei Exponential sind und schnelle Antworten möchten. Jede Antwort ist
kurz und verweist auf die Seite mit der ganzen Geschichte.

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
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

## Inhalt

1. [Das Modell](#das-modell)
2. [Templates und Caches](#templates-und-caches)
3. [Code und APIs](#code-und-apis)
4. [Websites und Server](#websites-und-server)
5. [Das Projekt](#das-projekt)

## Das Modell

**Was ist der Unterschied zwischen einem Knoten und einem Objekt?**
Das Objekt ist der Inhalt, mit seinen Versionen und Übersetzungen; ein Knoten ist ein Platz dieses Objekts im Baum. Ein
Objekt kann mehrere Knoten haben; einer davon ist der Hauptknoten. Eine Seitenansicht zeigt einen Knoten.
[Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md).

**Wo lege ich Inhaltstypen fest?**
Unter **Setup** > **Klassen** (`/class/grouplist`). Übertragen Sie sie in Paketen in andere Installationen.
[Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md).

**Kann ich den Typ eines Feldes später ändern?**
Nicht direkt: ein neues Attribut hinzufügen, die Werte mit einem Skript kopieren, das alte entfernen.
[Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md#eine-klasse-ändern-die-inhalte-hat).

**Gibt es ein Feld zum Wiederholen ("Repeater")?**
Keinen verschachtelten Repeater-Datentyp. Verwenden Sie Unterobjekte einer kleinen Klasse, `ezmatrix` für kleine
Tabellen oder Blöcke von Layouts.
[Inhalte auf Exponential abbilden](../../installation/migration/inhaltsmodell-zuordnen.md#2-inhaltsklassen-und-datentypen).

**Wie richte ich einen Bereich nur für Mitglieder ein?**
Eine Sektion auf dem Teilbaum und eine Rolle mit `content/read` auf dieser Sektion, der Mitgliedergruppe zugewiesen.
[Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md#sektionen-der-wichtigste-hebel).

## Templates und Caches

**Meine Template-Änderung erscheint nicht. Warum?**
Ein Cache hält die alte Ausgabe oder Zuordnung: der Template-Override-Cache für neue Dateien und Regeln, der View-Cache
oder der Antwort-Cache von Velocity.
[Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md#welcher-cache-nach-welcher-änderung) und
[Warum erscheint meine Änderung nicht?](../zwei-wege-zum-bauen/warum-erscheint-meine-aenderung-nicht.md).

**Welches Template zeichnet diesen Teil der Seite?**
Schalten Sie auf einem Entwicklungsrechner `ShowUsedTemplates` und `ShowTemplatePathComments` ein.
[Templates und Overrides für CMS-Entwickler](templates-und-overrides-fuer-cms-entwickler.md#das-template-hinter-einer-seite-finden).

**Kann ich PHP in Templates schreiben?**
Nein. Templates rufen Fetch-Funktionen und Operatoren auf; schreiben Sie einen Operator oder eine Fetch-Funktion in
Ihrer Erweiterung, wenn Sie mehr brauchen. [Erweiterungen, die Sie schreiben werden](erweiterungen-die-sie-schreiben-werden.md).

**Templates oder der Layout-Editor?**
Templates für jede Seite einer Klasse; Layouts für Seiten, die Redakteure zusammenstellen. Beides verbindet sich über
Template-Blöcke. [Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md#layouts-oder-templates).

## Code und APIs

**Wie lege ich Inhalte aus Code an?**
`eZContentFunctions::createAndPublishObject()` und `updateAndPublishObject()`, mit einer Remote-ID je Objekt.
[Inhalte aus PHP](inhalte-aus-php.md).

**Gibt es eine REST-API?**
Ja: Die Erweiterung `ezprestapi` bedient Routen v1 und v2 für Knoten, Unterelemente und Felder, mit Anlegen und
Entfernen in v2; Authentifizierung über persönlichen API-Schlüssel oder OAuth2. Aktualisierungen antworten vorerst mit
501. [Die REST-API](die-rest-api.md).

**Gibt es GraphQL?**
Nicht in Exponential 6. Die Linie Exponential Platform auf dem Symfony-Stack hat es.
[Headless-Auslieferung](headless-auslieferung.md#was-es-nicht-gibt).

**Wohin gehören Hooks?**
Workflow-Events an Auslösern, Event-Listener in `site.ini [Event]` oder Template-Operatoren, je nach Bedarf.
[Workflows und Auslöser](workflows-und-ausloeser.md),
[Erweiterungspunkte auf einen Blick](../erweiterungsentwicklung/teil-8-referenz/erweiterungspunkte-auf-einen-blick.md).

**Kann ich eine CSV-Datei importieren?**
Ja: mit xrowextract, standardmäßig als Probelauf, oder mit dem einfachen `ezcsvimport` des Kernels.
[Inhalte importieren](inhalte-importieren.md).

## Websites und Server

**Kann eine Installation mehrere Websites betreiben?**
Ja, über Siteaccesses, die eine Datenbank und einen Baum teilen. [Siteaccesses und Multisite](siteaccesses-und-multisite.md).

**Welchen Webserver sollte ich verwenden?**
Exponential Velocity ist die empfohlene Engine; FrankenPHP ist die produktionsreife Alternative; Apache oder nginx mit
PHP-FPM funktionieren ebenfalls. [Eine Website bereitstellen](eine-website-bereitstellen.md#wählen-wie-die-website-ausgeliefert-wird).

**Welches PHP und welche Datenbanken?**
PHP 8.0 bis 8.5 (PHP 7.4 ohne Support nutzbar) laut `composer.json` im Hauptverzeichnis. Datenbanken in der Reihenfolge der Präferenz des Projekts:
SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL. [Datenbankunterstützung](../datenbankunterstuetzung.md),
[Anforderungen](../../installation/02-voraussetzungen.md).

**Wie lege ich .htaccess und config.php an?**
`./console exp:setup:htaccess --apply` kopiert `.htaccess_root` (mit `--static`: `.htaccess_root_static`), und
`./console exp:setup:config --apply` kopiert `config.php-RECOMMENDED`. Ohne `--apply` zeigen sie nur, was sie tun
würden, und eine vorhandene, abweichende Datei wird als Diff gezeigt und behalten, solange Sie nicht `--force`
angeben. [.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md).

**Wie ändere ich eine Einstellung in config.php ohne Editor?**
`./console exp:config:set EZP_INI_FILEMTIME_CHECK false --apply`: Die Datei wird gesichert, mit `php -l` geprüft und
in einem Schritt geschrieben. `exp:config:list` zeigt jede dokumentierte Einstellung, `exp:config:get <NAME>` einen
Wert, und `exp:config:unset <NAME> --apply` kommentiert sie wieder aus.
[.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md).

**Wie starte ich ein Projekt schnell?**
`exp:install` mit dem Standardpaket `sevenx_multisite` auf SQLite, dann Ihre eigene Erweiterung.
[Entwicklungsumgebung](entwicklungsumgebung.md).

## Das Projekt

**Wer führt Exponential?**
7x, das Unternehmen, das es führt, und die Exponential Foundation, die gemeinnützige Organisation. Exponential steht
nicht in Verbindung mit eZ Systems oder Ibexa. [Wer ist 7x?](../wer-ist-7x.md),
[Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md).

**Wie ist die Geschichte?**
Exponential führt den Code eines Produkts fort, das sein Hersteller nach fast 18 Jahren eingestellt hat; 7x
veröffentlicht die Releases 6.0.x seit 2024. [Exponential für CMS-Entwickler](exponential-fuer-cms-entwickler.md).

**Welche Lizenz?**
Der Kernel steht unter GPL 2.0 oder später (`composer.json` im Hauptverzeichnis); diese Dokumentation ist frei unter
der GNU FDL 1.3. [Freie Dokumentation](../freie-dokumentation.md).

**Wo frage ich nach?**
[Die Gemeinschaft fragen](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/die-community-fragen.md).

## Verwandte Seiten

- [CMS-Entwickler: hier beginnen](cms-entwickler-hier-beginnen.md)
- [Glossar für CMS-Entwickler](glossar-fuer-cms-entwickler.md)
- [FAQ für Trainer](../trainer/trainer-faq.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
