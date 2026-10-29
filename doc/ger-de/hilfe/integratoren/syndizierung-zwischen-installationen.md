---
title: Syndizierung zwischen Installationen
audience: integrator, admin
category: integration
order: 110
summary: Inhalte zwischen Exponential-Installationen mit der Erweiterung syndication teilen - Syndizierung in eine und in beide Richtungen, Feeds über SOAP, die Cronjob-Teile export_feed und import_feed, die Quelle schützen, der Betrieb aus der Administration und der Konsole und wann Sie etwas anderes wählen.
keywords: Syndizierung, Inhalte teilen, mehrere Installationen, Portalnetzwerk, gemeinsame Inhalte, syndication, content sharing, multi-installation, SOAP, export_feed, import_feed, ext:syndication, one-way, two-way, portal network, shared content
type: help
---
# Syndizierung zwischen Installationen

Diese Seite richtet sich an Integratoren, deren Landschaft **mehrere Exponential-Installationen** umfasst, die Inhalte
teilen müssen: eine Konzernwebsite und die Websites ihrer Mitglieder, ein Portalnetzwerk, ein zentraler Produktkatalog,
der in mehreren Shops veröffentlicht wird. Die Erweiterung `syndication` (von der `composer.json` im
Wurzelverzeichnis als `~1.3.14` verlangt) exportiert einen Teil des Inhaltsbaums einer Installation als Feed und lässt
eine andere Installation ihn importieren, per Cron im Gleichschritt gehalten. Die Funktionsseite ist
[syndication](../../funktionen/6.0/erweiterungen/syndication.md); jede Option steht in der
[Spezifikation](../../spezifikationen/6.0/syndikation.md).

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

1. [Wie es funktioniert](#wie-es-funktioniert)
2. [Einrichten](#einrichten)
3. [Die Quelle schützen](#die-quelle-schützen)
4. [Betreiben](#betreiben)
5. [Syndizierung oder etwas anderes](#syndizierung-oder-etwas-anderes)
6. [Checkliste](#checkliste)

## Wie es funktioniert

```text
 main installation                                   second installation
 -----------------                                   -------------------
 export feed (a subtree)                             import (feed address + target node)
   |  cronjob part export_feed                          ^  cronjob part import_feed
   v                                                    |
 cached XML + binary files, one file per object  --->  SOAP request to the main installation
 served through index_soap.php (EnableSOAP=true)
```

- **Syndizierung in eine Richtung**: gemeinsame Inhalte oder Authentifizierungsdaten zwischen getrennten Datenbanken
  und Kunden-Websites.
- **Syndizierung in beide Richtungen**: Jede Seite exportiert und importiert, für Foren, Portalnetzwerke und große
  E-Commerce-Netzwerke.

## Einrichten

Auf **beiden** Installationen:

1. Installieren Sie die Tabellen der Erweiterung (`sql/mysql.sql` oder, seit 1.3.1 für jede Datenbank,
   `share/db_schema.dba`) und aktivieren Sie sie.
2. Schalten Sie für die exportierende Seite den SOAP-Server ein, in `settings/override/soap.ini.append.php`:

   ```ini
   [GeneralSettings]
   EnableSOAP=true

   [ExtensionSettings]
   SOAPExtensions[]=syndication
   ```

3. Lassen Sie den SOAP-Server durch den Webserver der exportierenden Seite. Er heißt seit Exponential 6.0.15
   `index_soap.php` (vorher `soap.php`; dieser Name leitet nur noch weiter): Nehmen Sie in der `.htaccess` das
   Kommentarzeichen vor `RewriteRule ^index_soap\.php - [L]` weg, oder ergänzen Sie die Zeile im HTTPS-Virtual-Host,
   oder eine `location = /index_soap.php` in nginx. `curl -i https://<host>/index_soap.php` muss mit „only understand
   POST methods“ antworten, nicht mit `404` oder einer Seite der Website. Die Zeilen für jeden Server und den
   vollständigen Test finden Sie unter
   [Das SOAP-Einstiegsskript heißt jetzt index_soap.php](../../bc/6.0/soap-einstiegsskript-index-soap.md).

Auf der **Hauptinstallation**: Öffnen Sie `/syndication/menu`, wählen Sie **Export**, dann **New Feed** und schließen
Sie den Assistenten ab. Eine Teilbaumquelle schließt die Wurzel des Teilbaums ein. Planen Sie den Export-Teil ein;
alle 3 Minuten wird empfohlen:

```bash
php runcronjobs.php export_feed
```

Auf der **zweiten Installation**: Öffnen Sie `/syndication/menu`, wählen Sie **Import**, geben Sie die Serveradresse
des Feeds (`https://export.example.com/index_soap.php`, oder `.../soap.php`, wenn die Hauptinstallation 6.0.14 oder
älter betreibt) und den Knoten an, der die Inhalte aufnimmt, und führen Sie den Import-Teil zuerst von Hand aus:

```bash
php runcronjobs.php import_feed
```

Prüfen Sie den ersten Import und planen Sie ihn dann ein. Nach dem ersten Lauf sind Importe schneller.

## Die Quelle schützen

Seit 1.2.0 sendet der Import einen Login und ein Passwort, sodass der SOAP-Endpunkt der exportierenden Website hinter
einer HTTP-Zugriffskontrolle liegen kann. Geben Sie sie in der Serveradresse an (`https://<login>:<password>@<host>/`)
oder in den Feldern **Login** und **Password** im ersten Schritt des Imports. Der Port ist für `https` standardmäßig
443. Halten Sie diese Zugangsdaten aus der Versionskontrolle heraus und beschränken Sie `index_soap.php` (und `soap.php`,
solange ein Import es noch aufruft) am Webserver auf die Adressen der importierenden Installationen.

## Betreiben

Die Startseite **Syndication** in der Administration zeigt, was exportiert und importiert wird, wann jeder
Cronjob-Teil zuletzt lief und was Aufmerksamkeit braucht (ein Feed ohne Quelle, ein Import ohne Ort, fehlgeschlagene
Einträge, SOAP aus). **Export all active feeds** und **Fetch all active imports** starten den Cronjob-Code im
Hintergrund, mit Fortschritt auf der Seite. Die Oberfläche der Erweiterung ist bisher nur englisch.

Aus der Shell (`php bin/php/console list ext:syndication`):

```bash
php bin/php/console ext:syndication:status
php bin/php/console ext:syndication:export --dry-run
php bin/php/console ext:syndication:import --import=3 --fetch-only
```

## Syndizierung oder etwas anderes

| Situation | Wählen Sie |
|---|---|
| Installationen teilen fortlaufend einen Teilbaum | `syndication` |
| Eine Partner-Website zeigt nur Schlagzeilen | Einen RSS-Export ([Feeds](rss-atom-und-opml-feeds.md)) |
| Eine Website oder einen Teilbaum einmal umziehen | Ein Paket ([Pakete](pakete-importieren-und-exportieren.md)) |
| Ein anderes System, das nicht Exponential ist, liest die Inhalte | Die REST-API oder `expservices` |
| Eine Installation bedient mehrere Websites | Überhaupt keine Syndizierung: Siteaccesses einer Installation ([Planung von Multisite](../fuehrung/multisite-und-mehrsprachigkeit-planen.md)) |

## Checkliste

- [ ] Tabellen installiert und die Erweiterung auf jeder beteiligten Installation aktiv.
- [ ] `EnableSOAP=true` und `SOAPExtensions[]=syndication` auf jeder exportierenden Installation.
- [ ] Der SOAP-Endpunkt geschützt (HTTP-Authentifizierung, Adressbeschränkung, HTTPS).
- [ ] `export_feed` und `import_feed` eingeplant; der erste Import von Hand geprüft.
- [ ] Überwachung des letzten Laufs der Teile (die Startseite Syndication, `ext:syndication:status`).

## Verwandte Seiten

- [syndication](../../funktionen/6.0/erweiterungen/syndication.md) und seine [Spezifikation](../../spezifikationen/6.0/syndikation.md)
- [WebDAV und SOAP](webdav-und-soap.md)
- [Cronjobs für Integrationen](cronjobs-fuer-integrationen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
