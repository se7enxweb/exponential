# Verhaltensänderungen vom 16. bis 30. September 2026

Lesen Sie diese Seite, bevor Sie die Änderungen vom 16. bis 30. September 2026 in eine bestehende Installation von
Exponential 6.0 übernehmen. Sie führt auf, was Besucher, Redakteure, Installierende und Betreiber bemerken werden und
was dagegen zu tun ist. Jede Zeile nennt die Änderung, wer betroffen ist, die Einstellung, die sie steuert, und die zu
ergreifende Maßnahme; längere Erklärungen sind verlinkt. Dies ist der Begleiter zum Upgrade für die
[Chronik des Zeitraums](../../geschichte/2026/2026-09b.md). Der vorherige Zeitraum steht in der
[Chronik vom 1. bis 15. September](../../geschichte/2026/2026-09a.md), der nächste in den
[Verhaltensänderungen vom Oktober 2026](verhaltensaenderungen-2026-10.md).

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
> [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../../hilfe/hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

## Reihenfolge beim Bereitstellen

Erledigen Sie zuerst Folgendes, in dieser Reihenfolge, auf jeder Website, in die Sie die Änderungen übernehmen:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
systemctl reload <php-fpm service>      # the PHP-FPM service that serves your site
./console exp:velocity deploy --dry-run # if you run Velocity: shows every step
```

Die drei Änderungen, bei denen die meisten Websites handeln müssen:

1. **Sicherheits-Header und das Sitzungs-Cookie** (die ersten beiden Zeilen unten): Prüfen Sie jeden Host, der die
   Administration in einen Frame einbettet, und jedes Skript, das das Sitzungs-Cookie liest.
2. **Der Inhaltsbaum der Administration beginnt beim obersten Knoten:** Setzen Sie `RootNodeID=2`, wenn Redakteure den
   alten Baum möchten.
3. **Fügen Sie den Cronjob-Teil `cache_cleanup`** Ihrer crontab hinzu (siehe „Caches und Webserver“).

## Für Besucher und Redakteure sichtbar

| Änderung | Wer betroffen ist | Einstellung | Was zu tun ist |
|---|---|---|---|
| Jede Seite wird mit Sicherheits-Headern gesendet; die Administration kann nicht von anderen Websites in einen Frame eingebettet werden. | Alle, die die Administration oder die Website einbetten; Proxys, die Header erneut hinzufügen. | `site.ini [HTTPHeaderSettings] SecurityHeaders[]` | Bettet ein Redaktions-Host die Administration ein, überschreiben Sie `Content-Security-Policy` (siehe [Sicherheitsstandards](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)). |
| Das Sitzungs-Cookie ist `HttpOnly`, `SameSite=Lax` und über HTTPS `Secure`. | Integrationen, die das Cookie per Skript lesen oder seitenübergreifend verwenden. | `site.ini [Session] CookieHttponly`, `CookieSameSite`, `CookieSecure=auto` | Setzen Sie die Werte ausdrücklich, wenn Sie das alte Verhalten brauchen. |
| Ein POST mit fehlendem oder falschem Formular-Token antwortet mit 403 und der Seite „Formular abgelaufen“, nicht mit 500. | Überwachung, die bei 500 alarmiert; eigene Fehler-Templates. | `site.ini [HTMLForms] RefusalLogCollapseSeconds`; `design:error/kernel/6.tpl` | Siehe [Seite „Formular abgelaufen“](../../funktionen/6.0/seite-abgelaufenes-formular.md). Alarmieren Sie stattdessen bei der Menge an 403. |
| „Passwort vergessen?“ gibt für unbekannte und bekannte Adressen dieselbe Antwort. | Redakteure, die sich auf die Meldung „kein solcher Benutzer“ verlassen haben. | keine | Keine. |
| Seiten geben `lang="nb-NO"`, `sv-SE`, `nl-NL` ... statt `no-bokmaal`, `swe-SE`, `dut-NL` aus. | CSS oder JavaScript, das nach den alten Werten von `lang` auswählt. | Locale-Dateien `[HTTP] ContentLanguage` | Passen Sie die Selektoren an; Tabelle unter [Übersetzungen](../../funktionen/6.0/uebersetzungen-und-sprachen.md). |
| Der Inhaltsbaum der Administration beginnt beim obersten Knoten, und die Tabs im Kopf heißen Medien, Benutzer und Shop. | Redakteure; Dokumentation mit Screenshots; Skripte, die auf Tab-Namen klicken. | `contentstructuremenu.ini [TreeMenu] RootNodeID` (jetzt `1`), `MaxDepth` (jetzt beachtet, `0` = alle) | Setzen Sie `RootNodeID=2` für den alten Baum. Siehe [Navigation](../../funktionen/6.0/oberster-knoten-des-inhaltsbaums-im-admin.md). |
| Der Tab Shop öffnet das Shop-Dashboard. | Shop-Mitarbeiter. | keine | Die Bestellliste ist der zweite Eintrag des Shop-Menüs. |
| Ein ausgeschaltetes Modul antwortet mit 404 wie ein unbekanntes Modul. | Tests, die die Seite „Modul ist ausgeschaltet“ erwarten. | `site.ini [SiteAccessRules] DisabledModuleResult=disabled` bringt die alte Seite zurück | Setzen Sie es, wenn Sie das alte Verhalten brauchen. |
| Ein fataler Fehler, eine nicht abgefangene Ausnahme oder eine fehlgeschlagene Datenbanktransaktion zeigt die statische Fehlerseite der Website mit 500, oder 503, wenn die Datenbank nicht erreichbar ist (abgelehnt, unbekannter Host, falsches Passwort, Host nicht erlaubt, zu viele Verbindungen, Verbindung verloren). Details nur mit eingeschalteter Debug-Ausgabe. | Alle, die den alten nackten Text „Fatal error“ auswerten. | `error.ini [ErrorSettings] StaticErrorPage[<code>]` (zum Beispiel `[500]`, `[503]`, `[default]`) | Alarmieren Sie bei 503. Neben die Details wird eine Referenz in das Fehlerprotokoll geschrieben. |
| Bestell-E-Mails und Bestätigungslinks können auf einen dauerhaften Beleg zeigen. | Kunden; E-Mail-Templates. | `shop.ini [OrderViewSettings] OrderLinkView` (Standard `orderview`); `AnonymousOrderLinkView` wurde entfernt | Siehe [Bestellbelege](../../funktionen/6.0/bestellbelege.md). |
| Paketversionen beginnen bei `1.0.0`; die Lizenz ist eine strikte Auswahl. | Paketautoren. | `package.ini [LicenseSettings]` | Siehe [Lizenzen und Versionen](../../funktionen/6.0/paketlizenzen-und-versionen.md). |
| `Setup > Extensions` speichert nur die Erweiterungen, die es anzeigt. | Administratoren. | keine | Prüfen Sie `ActiveExtensions` nach der Änderung einmal. Vor jedem Speichern wird eine Kopie der Datei in `var/<site>/backups/settings-override` abgelegt. |
| `Design > Templates` schreibt nur Zeilen mit `Priority` und geänderte Bedingungen. | Teams, die `override.ini.append.php` von Hand bearbeitet haben. | keine | Siehe [Reihenfolge der Overrides](../../funktionen/6.0/reihenfolge-der-template-ueberschreibungen.md). |
| Die Richtlinien einer Rolle werden in der Reihenfolge ihrer IDs aufgeführt. | Redakteure, die die alphabetische Reihenfolge gewohnt sind. | keine | Verwenden Sie die neuen Sortierüberschriften. |

## Installierende und Betreiber

| Änderung | Einstellung oder Befehl | Maßnahme |
|---|---|---|
| Der Kickstarter und der Setup-Assistent verwenden standardmäßig SQLite. | `kickstart.ini [database_choice] Type` | Nennen Sie Ihre Engine ausdrücklich. |
| Kickstart endet nicht mehr mit einem erratbaren Administratorpasswort. | `var/log/initial-admin-password` | Lesen Sie es nach der Installation einmal und löschen Sie die Datei. |
| `DatabaseImplementation` wird aus der laufenden `site.ini` übernommen, nicht aus `Type`. | `settings/override/site.ini.append.php` | Wenn Sie auf eine andere Engine installieren, setzen Sie es zuerst (siehe [Seite zur Installation](../../funktionen/6.0/installation-mit-einem-befehl.md)). |
| `./console exp:install --apply` installiert mit einem Befehl. | `exp:install --help` | Neu; nichts zu migrieren. |
| Der Wartungsmodus umschließt Installationen. | `var/maintenance.json` | Ist ein Lauf fehlgeschlagen, beenden Sie ihn mit `php bin/php/maintenance.php off`. Siehe [Wartungsmodus](../../funktionen/6.0/wartungsmodus.md). |
| Eine Standardinstallation konvertiert Bilder zuerst mit GD. | `image.ini [ImageConverterSettings] ImageConverters[]` (ausgelieferte Reihenfolge `GD`, `ImageMagick`) | Stellen Sie ImageMagick in `settings/override/image.ini.append.php` an die erste Stelle, wenn Sie seine Filter brauchen. |
| Neue Installationen beginnen mit den Ordnern Konfiguration und Archive und der Klassengruppe Konfiguration; Setup hat den Alias `x-setup`. | Seed-Daten | Bestehende Installationen werden nicht angefasst. Siehe [Seed-Daten](../../spezifikationen/6.0/installer-protokolle-und-startdaten.md). |
| Vierzehn Bestellstatus kommen hinzu. | `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` | Führen Sie das Datenbank-Update für 6.0.15 aus; eigene Status behalten ihre Zeilen. |
| Die Versionsklasse ist `ExponentialSDK`; die Beispiel-crontab ist `exponential.cron`. Die alten Namen funktionieren weiterhin. | | Siehe [ExponentialSDK und exponential.cron](exponentialsdk-und-exponential-cron.md). |
| Ausgehende Anfragen und E-Mails geben sich als Exponential zu erkennen. | | Ändern Sie Webserver-Regeln, die auf den früheren User-Agent des Generators für den statischen Cache passten. |
| `ExtensionSettingsSiteAccess` lässt einen Übersetzungs-Siteaccess die Erweiterungseinstellungen eines anderen Siteaccess verwenden. | `site.ini [SiteAccessSettings]` | Neu; siehe [Ladereihenfolge](../../funktionen/6.0/ladereihenfolge-der-extensions.md). |
| Protokollzeilen nennen den Siteaccess und die vollständige Anfrageadresse, auch unter Velocity. | `error.log`, `warning.log` | Passen Sie Log-Parser an, die nur die IP des Clients erwartet haben. |
| Die Konsole versteht `--version`, `--about` und `--copyright` vor dem Namen des Befehls. | `./console --version` | Neu. |

## Caches und Webserver

| Änderung | Einstellung | Maßnahme |
|---|---|---|
| Das Leeren von Caches schiebt Verzeichnisse durch Umbenennen beiseite. | `site.ini [FileSettings] RenameBeforeDelete`, `RenameExpiredCaches` | Keine; setzen Sie `disabled`, um zurückzukehren. Siehe [Leeren von Caches](../../funktionen/6.0/cache-leeren-durch-umbenennen.md). |
| Neuer Cronjob-Teil `cache_cleanup`. | `cronjob.ini [CacheCleanupSettings]` | Fügen Sie Ihrer crontab für jedes var-Verzeichnis `php runcronjobs.php -s <siteaccess> cache_cleanup` hinzu. |
| Neuer rollenabhängiger HTTP-Cache (standardmäßig aus). | `httpcache.ini [HttpCacheSettings] Enabled` | Siehe [HTTP-Cache](httpcache.md). Header mit Purge-Tags sind jetzt aus, sofern `TagHeader` keinen nennt. |
| Neuer SQL-Abfrage-Cache (standardmäßig aus). | `querycache.ini [QueryCacheSettings] Mode` | Siehe [SQL-Abfrage-Cache](sql-abfrage-cache.md). |
| Die ausgelieferten Apache-Regeln sperren `.php` unterhalb des Wurzelverzeichnisses, Punktdateien und den größten Teil von `var/storage/packages`. | `.htaccess_root`, `.htaccess_root_static` | Übernehmen Sie sie in Ihre eigenen Regeln. |
| Der Service Worker der Website ist `/index.js`; `/sw.js` ist ein einzeiliges Skript, das ihn lädt. | Document Root | Lassen Sie `/sw.js`, wo es ist: Browser, die es registriert haben, führen es weiter aus und prüfen diese Adresse auf Aktualisierungen. Die Cache-Version `exp-nav-v4` verwirft Seiten, die `v3` gespeichert hat. Sie hält sich außerdem von den eigenen Ansichten `/Q/` und der API von Velocity fern. |
| Velocity: `Engine=php` ist der ausgelieferte Standard; qbix wird empfohlen; Worker sind persistent (`ForkPerRequest=disabled`). | `velocity.ini` | Siehe [Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md). |
| Das Engine-Paket von Velocity wurde in `se7enxweb/exponential-velocity` umbenannt. | `velocity.ini [ServerSettings] ScriptPath` | Der Standard zeigt auf `vendor/se7enxweb/exponential-velocity`; das alte Verzeichnis wird weiterhin gefunden. |

## Korrekturen, die Ergebnisse ändern

- **Zählungen von Teilbäumen** mit einer Sprache finden einen Startknoten, der in dieser Sprache keine Übersetzung hat,
  und lassen diese Sprache nicht mehr gesetzt zurück; Zählungen, die früher leer zurückkamen, stimmen jetzt mit dem
  Fetch überein.
- **Pakete**: Inhalte werden vollständig und unter den richtigen Elternknoten installiert; alte V7-tar-Pakete werden
  importiert (kein Hängen); alte Klassenpakete werden installiert.
- **MongoDB**: Jedes SELECT COUNT, nach Datum sortierte Listen, `$inc` auf einer Zahl in Anführungszeichen und die
  Neuindizierung der Suche funktionieren jetzt; siehe
  [Unterstützung für MongoDB im Kernel](mongodb-kernel-unterstuetzung.md).
- **Die Suchindizierung** nimmt den gebündelten Weg auf SQLite und PostgreSQL ebenso wie auf MySQL (auf SQLite etwa
  doppelt so schnell, derselbe Index).
- **Absolute URLs** behalten den Port, unter dem eine Installation erreicht wird; der Abgleich der Hosts ignoriert den
  Port, sodass Links den Namen des Siteaccess nicht mehr tragen, wenn die Website auf einem anderen als dem
  Standardport ausgeliefert wird.

## Details zu den Änderungen an Cache, Archiv und Engine

Die Tabellen oben sagen, was zu tun ist. Hier stehen die Fakten dahinter, für diejenigen, die einen Server
feinabstimmen. Die Messungen stammen von einer Referenzinstallation an den angegebenen Tagen und wurden für diese Seite
nicht wiederholt.

### HTTP-Cache (`httpcache.ini`)

- **Einmal komprimiert.** Ein Treffer setzte früher die Werte des Besuchers in die gespeicherte Seite ein und
  komprimierte die ganze Seite bei jeder Anfrage, der größte Kostenfaktor eines Treffers. Die Teile des gespeicherten
  Bodys zwischen den Platzhaltern werden einmal komprimiert (jeder endet mit einem vollständigen Flush, sodass jeder
  für sich steht) und in APCu neben dem Eintrag gehalten; ein Treffer komprimiert nur seine Platzhalterwerte (ein
  Formular-Token) und fügt die Stücke zu einem gzip-Member mit dem CRC-32 der Seite zusammen. Ohne APCu komprimiert er
  die Seite wie bisher. Die ausgelieferten Bytes sind identisch (Test HC-11). Gemessen am 27. September, 8
  gleichzeitig, gecachte Startseite auf FrankenPHP: 4.048 Seiten pro Sekunde anonym (vorher etwa 2.800) und 3.413
  angemeldet (3.010).
- **Sicherheits-Header auf gecachten Seiten.** Der Cache antwortet ohne den Kernel, daher fehlten einer gecachten
  Seite früher die Header, die eine gerenderte Seite trägt. Die konfigurierten
  `[HTTPHeaderSettings] SecurityHeaders[]` werden mit der Seite gespeichert, und der Cache-Schlüssel enthält das
  Schema, sodass ein Header nur für HTTPS nie eine HTTP-Antwort erreicht.
- **Zuordnung von Siteaccesses.** Über URI und Host plus URI zugeordnete Siteaccesses werden ausgeliefert, und ein
  Siteaccess, den der Cache nicht ausliefert, wird als `X-Exp-Cache: MISS (siteaccess)` gemeldet und aus den Zählern
  für Fehlschläge herausgehalten. `RemoveSiteAccessIfDefaultAccess` und `PathPrefix` brauchen nichts Eigenes (eine
  Seite wird unter der vollständigen Anfrage-URI gehalten). Hinter einem Load Balancer, der TLS beendet, muss die
  Engine die Anfrage-Header weitergeben (Engine-Release 0.0.4.35 und neuer).
- **Purge-Tags** werden nur gesendet, wenn `[HttpCacheSettings] TagHeader` einen Header nennt (siehe
  [Sicherheitsstandards](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)).
- Setup > Caches leert den Abfrage-Cache und den HTTP-Cache, und Setup > Systeminformationen zeigt beide.

### SQL-Abfrage-Cache (`querycache.ini`)

- **Ein Treffer wird beantwortet, ohne die Anweisung zu parsen.** Der Cache las die Tabellen aus dem Text der
  Anweisung, bevor er nach einem gespeicherten Ergebnis suchte, jedes Mal; das war ein Drittel der CPU einer
  gerenderten Seite. Ein gespeicherter Eintrag trägt seine Tabellen bereits, daher wird zuerst der Eintrag gesucht und
  der Text nur bei einem Fehlschlag geparst; aus einem Eintrag, dessen Tabellen inzwischen ausgeschlossen oder temporär
  sind, wird nicht geantwortet (Test QC-13). Ein einzelnes Rendering unter Velocity ging bei denselben Bytes von 211,6
  auf 141,2 ms CPU zurück.
- **Schlüssel verwenden `xxh128`** der Anweisung und ihrer Parameter (0,84 gegenüber 1,55 Mikrosekunden pro
  Schlüssel; `md5`, wo PHP `xxh128` fehlt).
- **Der eigene Katalog der Datenbank wird nie gecacht** (`sqlite_master`, `sqlite_stat*`, `pg_*`,
  `information_schema`, und bei Oracle `USER_*`, `ALL_*` und dergleichen): `ANALYZE` und Schemaänderungen sind keine
  Schreibzugriffe, die der Cache sieht, und ein gecachtes Lesen von `sqlite_master` wurde innerhalb derselben Anfrage
  veraltet beantwortet (Test QC-14).
- **Das SQL-Profil zählt unter einem persistenten Worker jede Anfrage für sich**: Jede Zeile von
  `var/tmp/sql_profile.log` wiederholte früher die Zahlen des Vorwärmens; eine Anfrage, die nichts an die Datenbank
  sendet, schreibt keine Zeile.
- **Größe von APCu**: Der Cache und der Antwort-Cache teilen sich APCu; das PHP von Velocity erhält
  `apc.shm_size=256M` (siehe [Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)). Mehrere
  Server auf einem Port (`[ServerSettings] Instances`) sind in
  [Velocity-Engines](velocity-engines.md#mehrere-qbix-server-auf-einem-port-instances) beschrieben.

### Engine-Archiv (`exp:phar`)

- Der Bau schreibt das Archiv unter einem temporären Namen daneben und benennt es an seinen Platz um. Bisher löschte
  er `dist/engine.phar` und schrieb das neue an Ort und Stelle, sodass jeder Bau ein Zeitfenster ohne Archiv hinterließ,
  und zwei gleichzeitige Bauvorgänge (ein Neustart, der ein veraltetes Archiv neu baut, während Sie von Hand bauen)
  hinterließen keines, worauf der Server nicht startete.
- Die Syntaxprüfung vor dem Bau verwendet das PHP, das den Bau ausführt (sie führte `php -l` aus dem `PATH` aus, sodass
  dort, wo kein `php` im `PATH` ist, jede Datei „fehlschlug“ und nichts gebaut wurde), und prüft jeweils 200 Dateien mit
  einem Aufruf von `php -n -l` (1.049 Dateien: vorher neun Minuten, jetzt etwa zwei Sekunden für die Prüfungen und
  etwa 26 für den ganzen Bau). Eine fehlgeschlagene Gruppe wird Datei für Datei geprüft, sodass eine fehlerhafte Datei
  trotzdem genannt und nichts geschrieben wird.
- Ein veraltetes Archiv sagt, wie es sich selbst repariert (`exp:phar check`, `exp:phar build`); siehe
  [Engine-Archiv](phar.md).

### Engines und die Serverbefehle

- Die drei Engines (php, qbix, frankenphp) folgen denselben Asset-Regeln wie `.htaccess_root`;
  `[ServerSettings] FollowSymlinks=disabled` lehnt eine Datei ab, zu der ein Link innerhalb des Document Root nach
  außerhalb führt (Qbix antwortet mit 403, der eingebaute Server mit 404). Caddy folgt Links immer.
- FrankenPHP liefert standardmäßig HTTPS aus (`[FrankenPHPSettings] HTTPS=enabled`), bietet Kompression an
  (`zstd br gzip`; die Startseite ging unkomprimiert mit 95,7 KB hinaus, gzip-komprimiert sind es etwa 10 KB) und
  betreibt standardmäßig doppelt so viele PHP-Threads wie CPU-Kerne (ein leeres `[FrankenPHPSettings] Workers`), nicht
  die `Workers` des mitgelieferten Servers, die überwiegend untätige Prozesse zählen (gemessen mit 64 gleichzeitigen
  Seiten: 16,0 Anfragen pro Sekunde mit 12 bis 48 Threads, 14,6 mit 96 oder 590).
- `exp:webserver`, `exp:frankenphp` und `exp:solr`:
  [Befehle zur Serversteuerung](../../funktionen/6.0/befehle-fuer-webserver-und-solr.md).

### Vorladen

„Vorladen starten“ unter Setup > Vorladen führt das Crawlen als Hintergrundprozess aus, sodass es hinter Velocity und
hinter Proxys funktioniert, die gestreamte Antworten puffern. Nichts zu konfigurieren; siehe
[Seiten vorladen](../../funktionen/6.0/ansicht-websites-vorladen.md#es-läuft-im-hintergrund).

## Verwandte Seiten

- [Chronik, 16. bis 30. September 2026](../../geschichte/2026/2026-09b.md)
- [Sicherheitsstandards](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)
- [Härtung von Datentypen und Eingaben](../../spezifikationen/6.0/datentyp-eingabehaertung.md)
- [Datenbanktreiber und Installationsprogramme, September 2026](../../spezifikationen/6.0/datenbanktreiber-2026-09.md)
- [Befehle zur Serversteuerung](../../funktionen/6.0/befehle-fuer-webserver-und-solr.md)
- [Wartungsmodus](../../funktionen/6.0/wartungsmodus.md), [Bestellbelege](../../funktionen/6.0/bestellbelege.md), [Shop-Dashboard](../../funktionen/6.0/shop-dashboard.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
