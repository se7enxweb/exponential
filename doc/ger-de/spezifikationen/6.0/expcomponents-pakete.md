# Spezifikation: die expcomponents-Pakete und wofür der Kernel sie verwendet

Diese Seite ist die Referenz zu jeder Bibliothek der **expcomponents**, des Forks der Zeta Components durch Exponential
(Apache License 2.0): was das Paket tut, welche Klassen, Dateien und Funktionen des Kernels es verwenden, welches
Ursprungspaket es ersetzt und wo sein Repository ist. Überblick, Geschichte und Gründe für den Fork stehen auf der
Funktionsseite [Exponential Components (expcomponents)](../../funktionen/6.0/expcomponents.md); was ein Upgrade bedeutet,
in den [Upgrade-Hinweisen](../../bc/6.0/expcomponents.md).

Die Listen der Verwendungen stammen aus dem eigenen Code des Kernels (`kernel/`, `lib/`, `bin/`, `update/`,
`index_webdav.php`), durchsucht nach dem Klassenpräfix jeder Komponente, zum Beispiel
`grep -rlE '\bezcMail' --include=*.php kernel lib`. Wiederholen Sie diese Suche, um eine Liste an Ihrem Release zu
prüfen. Erweiterungen werden genannt, wo eine mitgelieferte Erweiterung von einer Komponente abhängt.

## Inhalt

1. [Gemeinsame Regeln für jedes Paket](#gemeinsame-regeln-für-jedes-paket)
2. [Übersichtstabelle](#übersichtstabelle)
3. [base](#base)
4. [archive](#archive)
5. [authentication](#authentication)
6. [cache](#cache)
7. [configuration](#configuration)
8. [console-tools](#console-tools)
9. [database](#database)
10. [debug](#debug)
11. [event-log](#event-log)
12. [feed](#feed)
13. [image-analysis](#image-analysis)
14. [image-conversion](#image-conversion)
15. [mail](#mail)
16. [mvc-tools](#mvc-tools)
17. [persistent-object](#persistent-object)
18. [signal-slot](#signal-slot)
19. [system-information](#system-information)
20. [webdav](#webdav)
21. [Forks, die der Kernel nicht verlangt](#forks-die-der-kernel-nicht-verlangt)
22. [Ursprungspakete, die nicht geforkt werden](#ursprungspakete-die-nicht-geforkt-werden)

## Gemeinsame Regeln für jedes Paket

| Eigenschaft | Wert für jeden Fork |
|---|---|
| Paketname | `se7enxweb/expcomponents-<name>`, wobei `<name>` der Name des Ursprungspakets ohne `zetacomponents/` ist |
| Repository | `https://github.com/se7enxweb/expcomponents-<name>`, ein GitHub-Fork von `https://github.com/zetacomponents/<Name>` |
| `replace` | `"zetacomponents/<name>": "self.version"` (webdav auch `se7enxweb/zetacomponents-webdav`) |
| Lizenz | `Apache-2.0`; `LICENSE.txt` mit den Hinweiszeilen des Originals, eine Datei `NOTICE` mit den Hinweisen des Originals und dem Namen des Forks, `CREDITS`, `ChangeLog` |
| Klassenpräfix | `ezc<Komponente>`, unverändert |
| Autoload | `"classmap": ["src"]` in der `composer.json` des Pakets, geladen von `vendor/autoload.php` |
| Installiert unter | `vendor/se7enxweb/expcomponents-<name>/` |
| Tags | Ohne Präfix `v` (`1.5.2`); dauerhaft, sobald sie gepusht sind |
| Bedingung im Kernel | Tilde-Form (`~1.5.3`), Caret-Form für base und console-tools (`^1.9.6`, `^1.7.6`), im `require` der `composer.json` des Kernels |
| Tests | `tests/` mit `phpunit.xml.dist`; Entwicklungsabhängigkeiten `zetacomponents/unit-test` und PHPUnit |

Versionsstand: Jeder Fork auf dieser Seite wurde am 2026-11-06 veröffentlicht (ein Tag, ein GitHub-Release und die
Version auf Packagist); archive 1.5.2 und webdav 1.2.2 erschienen einige Tage vorher. Jeder Fork verlangt die anderen
mit ihren Fork-Namen (`se7enxweb/expcomponents-base ~1.8`, nicht `zetacomponents/base`).

## Übersichtstabelle

| Komponente | Fork-Paket | Ersetzt | Der Kernel braucht sie für | Fork-Release (Bedingung im Kernel) |
|---|---|---|---|---|
| base | `se7enxweb/expcomponents-base` | `zetacomponents/base` | jede Komponente; den Verzeichnisdurchlauf des Autoload-Generators; Ausnahmen und Optionen der REST-Schicht | 1.9.6 (`^1.9.6`) |
| archive | `se7enxweb/expcomponents-archive` | `zetacomponents/archive` | Import, Export und Upload von Paketen (`.ezpkg`, tar und zip) | 1.5.3 (`~1.5.3`); 1.5.2 am 2026-11-03 |
| authentication | `se7enxweb/expcomponents-authentication` | `zetacomponents/authentication` | die Authentifizierungsfilter der REST-Schicht: Basic, API-Schlüssel, OAuth | 1.4.2 (`~1.4.2`) |
| cache | `se7enxweb/expcomponents-cache` | `zetacomponents/cache` | das Caching von REST-Antworten und -Routen | 1.6.4 (`~1.6.4`) |
| configuration | `se7enxweb/expcomponents-configuration` | `zetacomponents/configuration` | SQLI Import 2.0.x (eine Ausnahmeklasse) | 1.4.1 (`~1.4.1`) |
| console-tools | `se7enxweb/expcomponents-console-tools` | `zetacomponents/console-tools` | `ezpgenerateautoloads.php`, den Beispielgenerator der Hilfe, ein Update-Skript | 1.7.6 (`^1.7.6`) |
| database | `se7enxweb/expcomponents-database` | `zetacomponents/database` | die Datenbankverbindung der REST-Schicht; die Liste der OAuth-Clients | 1.5.5 (`~1.5.5`) |
| debug | `se7enxweb/expcomponents-debug` | `zetacomponents/debug` | die Debug-Ausgabe von REST | 1.3.1 (`~1.3.1`) |
| event-log | `se7enxweb/expcomponents-event-log` | `zetacomponents/event-log` | die Log-Stufen von REST-Router und -Controller | 1.5.1 (`~1.5.1`) |
| feed | `se7enxweb/expcomponents-feed` | `zetacomponents/feed` | RSS-1.0-, RSS-2.0- und Atom-Exporte; Feed-Views von REST | 1.4.7 (`~1.4.7`) |
| image-analysis | `se7enxweb/expcomponents-image-analysis` | `zetacomponents/image-analysis` | von image-conversion verlangt; den Bildeditor ezie | 1.2.1 (über image-conversion) |
| image-conversion | `se7enxweb/expcomponents-image-conversion` | `zetacomponents/image-conversion` | den Bildeditor ezie | 1.5.2 (`~1.5.2`) |
| mail | `se7enxweb/expcomponents-mail` | `zetacomponents/mail` | jede E-Mail: `eZMail`, die Transporte SMTP und sendmail, die E-Mail-Schranke, Bounces | 1.10.2 (`~1.10.2`) |
| mvc-tools | `se7enxweb/expcomponents-mvc-tools` | `zetacomponents/mvc-tools` | die REST-API und die REST-Endpunkte von GraphQL, Hilfe und Migrationen | 1.2.5 (`~1.2.5`) |
| persistent-object | `se7enxweb/expcomponents-persistent-object` | `zetacomponents/persistent-object` | OAuth-Clients, Token und Autorisierungscodes | 1.8.3 (`~1.8.3`) |
| signal-slot | `se7enxweb/expcomponents-signal-slot` | `zetacomponents/signal-slot` | die Hooks der Veröffentlichungswarteschlange | 1.2.1 (`~1.2.1`) |
| system-information | `se7enxweb/expcomponents-system-information` | `zetacomponents/system-information` | Setup > Systeminformationen | 1.1.3 (`~1.1.3`) |
| webdav | `se7enxweb/expcomponents-webdav` | `zetacomponents/webdav`, `se7enxweb/zetacomponents-webdav` | den WebDAV-Server (`index_webdav.php`) | 1.2.3 (`~1.2.3`); 1.2.2 am 2026-11-01 |

## base

- **Zweck:** die Infrastruktur, auf die sich jede Komponente stützt: die Basis-Ausnahmeklassen (`ezcBaseException`,
  `ezcBaseValueException`, `ezcBasePropertyNotFoundException`, `ezcBaseFileNotFoundException` ...), die
  Options-Basisklasse `ezcBaseOptions`, die Struct-Basisklasse, `ezcBaseFile` (rekursive Verzeichnisdurchläufe,
  relative Pfade) und der ursprüngliche Autoloader `ezcBase::autoload`.
- **Verwendung im Kernel:**
  - `kernel/private/classes/ezautoloadgenerator.php`, der Motor von `php bin/php/ezpgenerateautoloads.php`, durchläuft
    die Verzeichnisse mit `ezcBaseFile::findRecursive()` und `walkRecursive()` und berechnet Pfade mit
    `calculateRelativePath()`;
  - `lib/ezutils/classes/ezsys.php` wirft `ezcBasePropertyNotFoundException` für unbekannte Eigenschaften;
  - Options- und Struct-Klassen in `kernel/private/options/` und `kernel/private/structs/`, die Inhaltsklassen der
    öffentlichen API (`kernel/private/api/content/`), die Cluster-Ausnahmen (`kernel/private/classes/exceptions/cluster/`),
    das DFS-Backend für MySQLi, die OAuth-Module und die REST-Schicht (`kernel/private/rest/classes/`).
  - Die Prüfung `setup.ini [ezcversion]` des Setup-Assistenten (`TestClass=ezcBaseFile`, `TestFunction=walkrecursive`)
    prüft, dass die Bibliothek installiert ist.
- **Hinweis zum Autoload:** `autoload.php` registriert `ezcBase::autoload` nur, wenn `EZCBASE_ENABLED` als `true`
  definiert ist; mit Composer ist es weder nötig noch registriert.
- **Fork:** `se7enxweb/expcomponents-base`, [Repository](https://github.com/se7enxweb/expcomponents-base), ersetzt
  `zetacomponents/base`. Jeder andere Fork verlangt es unter diesem Namen (`se7enxweb/expcomponents-base ~1.8`).

## archive

- **Zweck:** tar-Archive (ustar, v7, pax, GNU) und zip-Archive erstellen, ändern und entpacken.
- **Verwendung im Kernel:**
  - `kernel/classes/ezpackage.php`: Der Export schreibt `.ezpkg`-Dateien mit
    `ezcArchive::open( ..., ezcArchive::TAR_USTAR )` oder `ezcArchive::ZIP`; der Import entpackt sie;
  - `kernel/classes/exppackagearchive.php` und `kernel/classes/exppackagearchivemodes.php`: das Lesen von
    Paketarchiven und die Dateimodi ihrer Einträge;
  - `kernel/classes/ezpackageuploadinspector.php`: prüft ein hochgeladenes Paket vor dem Import;
  - `kernel/classes/exppackagejson.php`: das JSON-Paketformat liest dieselben Archive.
- **Funktionen:** Setup > Pakete (Import, Export, Upload), `ezpm` auf der Kommandozeile, die Installation von
  Site-Paketen.
- **Fork:** `se7enxweb/expcomponents-archive`, [Repository](https://github.com/se7enxweb/expcomponents-archive),
  ersetzt `zetacomponents/archive`. Vom Kernel seit 2026-11-03 verlangt (`~1.5.2`, seit 2026-11-06 `~1.5.3`). Release 1.5.2 (vom Original 1.5.1):
  keine Deprecations unter PHP 8.4 und 8.5, GNU-tar-Einträge mit langen Namen oder langen Links werden wieder
  entpackt, Tests auf PHP 7.4 bis 8.5.

## authentication

- **Zweck:** Benutzer mit Filtern für verschiedene Anmeldedaten und Anbieter identifizieren und authentifizieren.
- **Verwendung im Kernel:** die Authentifizierung der REST-Schicht: `kernel/private/rest/classes/auth/` (die Basis
  der Authentifizierungsstile, der Filter für native Benutzer, der Passwortfilter, der API-Schlüssel-Filter und
  `apikey_rest.php`), die Stile in `kernel/private/rest/classes/auth/styles/` (`basic_auth.php`, `no_auth.php`,
  `oauth.php`) und `kernel/private/rest/classes/oauth/oauth_filter.php`. Verwendete Klassen: `ezcAuthentication`,
  `ezcAuthenticationFilter`, `ezcAuthenticationIdCredentials`, `ezcAuthenticationPasswordCredentials`.
- **Funktionen:** die Anmeldung an REST-API v1 und v2, API-Schlüssel, OAuth.
- **Fork:** `se7enxweb/expcomponents-authentication`,
  [Repository](https://github.com/se7enxweb/expcomponents-authentication), ersetzt `zetacomponents/authentication`.

## cache

- **Zweck:** ein Cache-Manager mit mehreren Speicher-Backends (Dateien, Speicher, APC und andere).
- **Verwendung im Kernel:** der Cache der REST-Schicht: `kernel/private/rest/classes/cache/` (`object.php`,
  `storage.php`, `apc.php`, `cluster.php` mit Optionen und Ausnahme, ein clusterfähiger Speicher von Exponential selbst),
  der Router (`kernel/private/rest/classes/router.php`) und der REST-Controller. Verwendete Klassen: `ezcCacheManager`,
  `ezcCacheStorage`, `ezcCacheStorageFile`, `ezcCacheStorageMemory`, `ezcCacheStorageOptions`,
  `ezcCacheInvalidDataException`.
- **Funktionen:** zwischengespeicherte REST-Routen und -Antworten.
- **Fork:** `se7enxweb/expcomponents-cache`, [Repository](https://github.com/se7enxweb/expcomponents-cache), ersetzt
  `zetacomponents/cache`.

## configuration

- **Zweck:** Konfigurationsdateien in mehreren Formaten lesen (INI und array-basiert).
- **Verwendung im Kernel:** keine; der Kernel hat sein eigenes INI-System (`eZINI`). Das Paket bleibt verlangt, weil
  7x SQLI Import 2.0.x eine seiner Ausnahmeklassen wirft
  ([Verhaltensänderungen vom Oktober 2026](../../bc/6.0/verhaltensaenderungen-2026-10.md)).
- **Fork:** `se7enxweb/expcomponents-configuration`,
  [Repository](https://github.com/se7enxweb/expcomponents-configuration), ersetzt `zetacomponents/configuration`.
  Das Repository ist mit dem offiziellen Fork neu; erstes Fork-Release am 2026-11-06.

## console-tools

- **Zweck:** Eingaben auf der Kommandozeile (Optionen und Argumente), formatierte Ausgabe, Fortschrittsbalken,
  Statusleisten und Tabellen.
- **Verwendung im Kernel:**
  - `kernel/private/classes/commands/ezpgenerateautoloads.php` und `kernel/private/classes/ezpautoloadclioutput.php`:
    die Optionen und die Fortschrittsausgabe von `php bin/php/ezpgenerateautoloads.php` (`ezcConsoleInput`,
    `ezcConsoleOption`, `ezcConsoleOutput`, `ezcConsoleProgressbar`, `ezcConsoleStatusbar`);
  - `kernel/classes/help/exphelpexamplesgenerator.php`: der Beispielgenerator der Hilfe;
  - `update/common/scripts/4.2/fixorphanimages.php`: ein Update-Skript.
- **Funktionen:** die Erzeugung der Klassen-Maps (auch durch `./console exp:velocity deploy` und die
  Post-Install-Hooks von Composer).
- **Fork:** `se7enxweb/expcomponents-console-tools`,
  [Repository](https://github.com/se7enxweb/expcomponents-console-tools), ersetzt `zetacomponents/console-tools`.
  Das Repository ist mit dem offiziellen Fork neu; erstes Fork-Release am 2026-11-06.

## database

- **Zweck:** eine Datenbankschicht auf PDO mit einer Abfrage-Abstraktion, die Unterschiede zwischen SQL-Dialekten
  verbirgt.
- **Verwendung im Kernel:** `kernel/private/rest/classes/lazy.php` erzeugt die Datenbankverbindung der REST-Schicht
  mit `ezcDbFactory` und `ezcDbInstance` (einschließlich `ezcDbHandlerSqlite`);
  `kernel/private/modules/oauthadmin/list.php` listet OAuth-Clients mit `ezcQuerySelect`. Die eigene Datenbankschicht
  des Kernels (`eZDB`) verwendet es nicht.
- **Funktionen:** die Speicherung von REST und OAuth; persistent-object braucht es.
- **Fork:** `se7enxweb/expcomponents-database`, [Repository](https://github.com/se7enxweb/expcomponents-database),
  ersetzt `zetacomponents/database`.

## debug

- **Zweck:** Zeitmesser und strukturierte Debug-Ausgabe in mehreren Formaten.
- **Verwendung im Kernel:** `kernel/private/rest/classes/debug/debug.php` und
  `kernel/private/rest/classes/debug/formatters/php_formatter.php`: die Debug-Ausgabe der REST-Schicht (`ezcDebug`,
  `ezcDebugStructure`, `ezcDebugOutputFormatter`, `ezcDebugStacktraceIterator`, `ezcDebugTimerStruct`). Die
  Debug-Ausgabe der Seiten des Kernels (`eZDebug`, die Exp-Debug-Leiste) verwendet es nicht.
- **Fork:** `se7enxweb/expcomponents-debug`, [Repository](https://github.com/se7enxweb/expcomponents-debug), ersetzt
  `zetacomponents/debug`. Das Repository ist mit dem offiziellen Fork neu; erstes Fork-Release am 2026-11-06.

## event-log

- **Zweck:** Ereignisse in Dateien oder andere Speicher protokollieren, mit Schweregraden (`ezcLog::ERROR` ...).
- **Verwendung im Kernel:** `kernel/private/rest/classes/router.php` und
  `kernel/private/rest/classes/controllers/rest_controller.php` protokollieren REST-Fehler mit den Schweregraden von
  `ezcLog` über die REST-Debug-Klasse. Die eigenen Logs des Kernels (`eZLog`, `var/log/`) verwenden es nicht.
- **Fork:** `se7enxweb/expcomponents-event-log`, [Repository](https://github.com/se7enxweb/expcomponents-event-log),
  ersetzt `zetacomponents/event-log`. Das Repository ist mit dem offiziellen Fork neu; erstes Fork-Release am 2026-11-06.

## feed

- **Zweck:** RSS-1.0-, RSS-2.0- und Atom-Feeds mit ihren Modulen (Dublin Core, content, iTunes ...) lesen und schreiben.
- **Verwendung im Kernel:**
  - `kernel/classes/ezrssexport.php`: `rssXmlContent()` schreibt RSS-1.0-, RSS-2.0- und Atom-Exporte über
    `generateFeed()`, das ein `ezcFeed` aufbaut; Apple Podcasts und OPML schreibt der eigene Code des Kernels;
  - `kernel/private/rest/classes/views/feed_decorator.php`: Feed-Views der REST-Schicht (`ezcFeedEntryElement`).
- **Funktionen:** RSS-Exporte unter `/rss/feed/<adresse>` ([RSS-, Atom- und OPML-Feeds](../../hilfe/integratoren/rss-atom-und-opml-feeds.md)).
- **Fork:** `se7enxweb/expcomponents-feed`, [Repository](https://github.com/se7enxweb/expcomponents-feed), ersetzt
  `zetacomponents/feed`. Das Repository ist mit dem offiziellen Fork neu; erstes Fork-Release am 2026-11-06.

## image-analysis

- **Zweck:** den MIME-Typ und Eigenschaften (Größe, EXIF bei JPEG und TIFF) einer Bilddatei ermitteln.
- **Verwendung im Kernel:** keine direkte. `image-conversion` verlangt es, und die Bildeditor-Erweiterung ezie
  verwendet es (`extension/ezie/classes/image_analyzer.php`).
- **Fork:** `se7enxweb/expcomponents-image-analysis`,
  [Repository](https://github.com/se7enxweb/expcomponents-image-analysis), ersetzt `zetacomponents/image-analysis`.
  Als Abhängigkeit des Forks von image-conversion installiert, nicht im `require` des Kernels aufgeführt.

## image-conversion

- **Zweck:** Filter auf Bilder anwenden (skalieren, zuschneiden, Farben, Effekte) über GD oder ImageMagick.
- **Verwendung im Kernel:** keine in `kernel/` oder `lib/`; der Kernel verlangt es für ezie, den Bildeditor des
  Bearbeitungsformulars, der es selbst nicht angibt (`extension/ezie/classes/image_converter.php`,
  `image_pre_action.php`, `image_tool_resize.php`) ([ezie](../../funktionen/6.0/erweiterungen/ezie.md)). Die
  Bildvarianten des Kernels verwenden ihre eigenen Handler von `eZImageManager`, nicht diese Bibliothek.
- **Fork:** `se7enxweb/expcomponents-image-conversion`,
  [Repository](https://github.com/se7enxweb/expcomponents-image-conversion), ersetzt `zetacomponents/image-conversion`.
  Release 1.5.2 enthält die Änderung des Originals, ImageMagick über die Argumentlisten-Form von `proc_open()`
  aufzurufen (keine Shell wertet den Befehl aus), und behebt, was diese Änderung kaputt machte: Skalieren und
  Zuschneiden mit ImageMagick scheiterten mit „unrecognized option `-resize '“, weil die Filter Optionsnamen mit einem
  Leerzeichen am Ende übergaben. Die Korrektur ist dem Original als
  [Pull-Request #9 von zetacomponents/ImageConversion](https://github.com/zetacomponents/ImageConversion/pull/9)
  angeboten. Das Release zeigt außerdem den Befehl in der Fehlermeldung eines gescheiterten ImageMagick-Aufrufs,
  scheitert sofort, wenn das Zielverzeichnis nicht beschreibbar ist, weist mit jedem GD-Build ein JPEG ab, für das
  libjpeg einen Fehler meldet, und gibt unter PHP 8.5 keine Deprecations aus.

## mail

- **Zweck:** E-Mails erstellen, auswerten und versenden: MIME-Teile, Anhänge, HTML-Mail, SMTP (mit STARTTLS und
  Authentifizierung), sendmail, IMAP, POP3 und mbox.
- **Verwendung im Kernel:**
  - `lib/ezutils/classes/ezmail.php`: `eZMail` baut jede Nachricht auf `ezcMail`, `ezcMailAddress`, `ezcMailText` und
    `ezcMailPart` auf;
  - `lib/ezutils/classes/ezsmtptransport.php`: `eZSMTPTransport` sendet über `ezcMailSmtpTransport` mit
    `ezcMailSmtpTransportOptions`; in dieser Bibliothek bedeutet `tls` STARTTLS;
  - `lib/ezutils/classes/ezsendmailtransport.php`: der sendmail-Transport;
  - `kernel/private/classes/ezpmail.php`, `kernel/classes/mail/expmailtemplatebody.php` (Inhalte aus Templates);
  - `kernel/classes/mailpreferences/expmailgate.php` (die E-Mail-Schranke) und `expmailbouncereader.php` (Bounces,
    aus einem Postfach gelesen mit `ezcMailImapTransport` oder `ezcMailPop3Transport` und `ezcMailTools`).
- **Funktionen:** jede E-Mail der Site: Benachrichtigungen, Registrierung, Passwort, Formulare, Newsletter über
  cjw_newsletter (das `ezcMail` in `CjwNewsletterMailComposer` erweitert), die E-Mail-Einstellungen und ihre Schranke.
- **Fork:** `se7enxweb/expcomponents-mail`, [Repository](https://github.com/se7enxweb/expcomponents-mail), ersetzt
  `zetacomponents/mail`. Das Repository ist mit dem offiziellen Fork neu; erstes Fork-Release am 2026-11-06.
  Release 1.10.2 enthält die Änderungen des Originals nach 1.10.1, darunter den Schutz vor Header-Injection: Eine
  Adresse mit Leerraum oder einem der Zeichen `< > , ; "` wird abgewiesen, bevor ein Header geschrieben wird,
  Header-Werte, -Namen und Adressen können keine Header mehr hinzufügen, leere Header-Namen und leere Folgezeilen
  werden abgewiesen, `ezcMailTools::validateEmailAddress()` nimmt keinen abschließenden Zeilenumbruch mehr an, und
  Content-IDs sind zufällig. `ezpMailMessageIdTest` (MI-04, MI-05) prüft den Kernel damit.

## mvc-tools

- **Zweck:** Auswerten von Anfragen, Routing, Verteilen, Views und das Schreiben von Antworten für eine
  MVC-Anwendung.
- **Verwendung im Kernel:** die ganze REST-Schicht in `kernel/private/rest/classes/` (Router, Routen,
  Anfrage-Parser, Controller, Statusobjekte, Views, Antwort-Schreiber, die Schnittstellen der
  Authentifizierungsstile) und die darauf gebauten REST-Endpunkte: GraphQL (`kernel/classes/graphql/rest/`),
  Hilfeinhalte (`kernel/classes/help/content/rest/`) und Migrationen (`kernel/classes/migration/rest/`). Die am
  häufigsten verwendeten Klassen: `ezcMvcRequest`, `ezcMvcResult`, `ezcMvcRoutingInformation`,
  `ezcMvcInternalRedirect`, `ezcMvcController`, `ezcMvcResponseWriter`, `ezcMvcHttpResponseWriter`.
- **Funktionen:** [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md) mit der Erweiterung ezprestapi.
- **Fork:** `se7enxweb/expcomponents-mvc-tools`, [Repository](https://github.com/se7enxweb/expcomponents-mvc-tools),
  ersetzt `zetacomponents/mvc-tools`. Das Repository ist mit dem offiziellen Fork neu; erstes Fork-Release am 2026-11-06.

## persistent-object

- **Zweck:** PHP-Objekte auf Datenbanktabellen abbilden und sie abrufen, speichern und löschen (auf der
  Datenbankkomponente).
- **Verwendung im Kernel:** der OAuth-Server: `kernel/private/oauth/classes/` (`restclient.php`,
  `restauthorizedclient.php` und die Definitionen der persistenten Objekte in `persistentobjects/`), die REST-Token und
  Autorisierungscodes (`kernel/private/rest/classes/models/`, `po_maps/`, `oauth/utility.php`) und die OAuth-Module
  (`kernel/private/modules/oauth/authorize.php`, `kernel/private/modules/oauthadmin/`). Verwendete Klassen:
  `ezcPersistentObjectDefinition`, `ezcPersistentObjectProperty`, `ezcPersistentObjectIdProperty`. Das eigene
  `eZPersistentObject` des Kernels ist eine andere Klasse und verwendet es nicht.
- **Fork:** `se7enxweb/expcomponents-persistent-object`,
  [Repository](https://github.com/se7enxweb/expcomponents-persistent-object), ersetzt `zetacomponents/persistent-object`.

## signal-slot

- **Zweck:** Signale und Slots: Objekte senden benannte Signale, verbundene Callbacks laufen.
- **Verwendung im Kernel:** `kernel/private/classes/ezpcontentpublishingqueue.php`: `ezpContentPublishingQueue` hält
  eine `ezcSignalCollection`, verbindet die für die asynchrone Veröffentlichungswarteschlange eingestellten Hooks und
  sendet `preQueue`, bevor eine Version in die Warteschlange kommt.
- **Fork:** `se7enxweb/expcomponents-signal-slot`, [Repository](https://github.com/se7enxweb/expcomponents-signal-slot),
  ersetzt `zetacomponents/signal-slot`. Das Repository ist mit dem offiziellen Fork neu; erstes Fork-Release am 2026-11-06.

## system-information

- **Zweck:** Systemwerte lesen: CPU-Typ und -Takt, Speicher, das Betriebssystem, den PHP-Beschleuniger.
- **Verwendung im Kernel:** `kernel/private/classes/views/setup/info.php`: Setup > Systeminformationen
  (`/setup/info`) liest `ezcSystemInfo::getInstance()`; lässt sich das Betriebssystem nicht lesen
  (`ezcSystemInfoReaderCantScanOSException`), zeigt die Seite keine Systemwerte, und ein Debug-Hinweis wird geschrieben.
- **Fork:** `se7enxweb/expcomponents-system-information`,
  [Repository](https://github.com/se7enxweb/expcomponents-system-information), ersetzt
  `zetacomponents/system-information`. Das Repository ist mit dem offiziellen Fork neu; erstes Fork-Release am 2026-11-06.

## webdav

- **Zweck:** ein WebDAV-Server (RFC 2518 und RFC 4918) mit austauschbaren Backends, Sperren und Authentifizierung.
- **Verwendung im Kernel:** `index_webdav.php` im Wurzelverzeichnis der Installation führt `ezcWebdavServer` mit dem
  Inhalts-Backend `kernel/private/classes/webdav/ezwebdavcontentbackend.php` aus (`eZWebDAVContentBackend extends
  ezcWebdavSimpleBackend implements ezcWebdavLockBackend`), mit seiner Authentifizierung
  `ezwebdavcontentbackendauth.php` und der Pfad-Factory `expwebdavpathfactory.php`.
- **Funktionen:** Inhalte als Dateien über WebDAV bearbeiten ([WebDAV und SOAP](../../hilfe/integratoren/webdav-und-soap.md)).
- **Fork:** `se7enxweb/expcomponents-webdav`, [Repository](https://github.com/se7enxweb/expcomponents-webdav),
  ersetzt `zetacomponents/webdav` und `se7enxweb/zetacomponents-webdav`. Vom Kernel seit 1.2.2 verlangt (`~1.2.2`, seit 2026-11-06 `~1.2.3`).
  Release 1.2.1 erschien als `se7enxweb/zetacomponents-webdav` (WebDAV auf PHP 8); Release 1.2.2, das erste unter dem
  neuen Namen, hält Anfragepfade und das Ziel von COPY und MOVE innerhalb der Wurzel des Backends, folgt keinen
  symbolischen Links aus ihr heraus, weist Anfragekörper mit einer Dokumenttyp-Deklaration ab, vergleicht die
  Digest-Authentifizierung in konstanter Zeit und erzeugt zufällige Sperr-Token.

## Forks, die der Kernel nicht verlangt

| Fork | Ersetzt | Hinweis |
|---|---|---|
| `se7enxweb/expcomponents-authentication-database-tiein` ([Repository](https://github.com/se7enxweb/expcomponents-authentication-database-tiein)) | `zetacomponents/authentication-database-tiein` | Seit Oktober 2026 nicht mehr verlangt; nichts im Kernel oder in den Paketen, die er verlangt, verwendet `ezcAuthenticationDatabase*`. Release 1.2.1 (2026-11-06). |
| `se7enxweb/expcomponents-graph` ([Repository](https://github.com/se7enxweb/expcomponents-graph)) | `zetacomponents/graph` | Diagramme; weder vom Kernel noch von einer mitgelieferten Erweiterung verwendet. Release 1.7.3 (2026-11-06). |
| `se7enxweb/expcomponents-workflow` ([Repository](https://github.com/se7enxweb/expcomponents-workflow)) | `zetacomponents/workflow` | Eine Workflow-Engine; der Kernel hat seine eigene (`eZWorkflow`). Release 1.5.2 (2026-11-06). |

Eine Erweiterung, die einen davon braucht, verlangt den Fork mit seinem Namen.

## Ursprungspakete, die nicht geforkt werden

| Paket | Warum |
|---|---|
| `zetacomponents/mvc-authentication-tiein` | Seit Oktober 2026 nicht mehr verlangt; nichts verwendet es. |
| `zetacomponents/php-generator` | War eine Entwicklungsanforderung; im Oktober 2026 entfernt. |
| `zetacomponents/unit-test`, `zetacomponents/database-schema`, `zetacomponents/coding-standard` | Entwicklungsabhängigkeiten der eigenen Testsuiten der Forks; auf einer Site nicht installiert. |

## Verwandte Seiten

- [Exponential Components (expcomponents)](../../funktionen/6.0/expcomponents.md)
- [Upgrade-Hinweise: der Wechsel zu expcomponents](../../bc/6.0/expcomponents.md)
- [Zu expcomponents beitragen](../../anleitungen/zu-expcomponents-beitragen.md)
- [APIs und Protokolle](../../standards/apis-und-protokolle.md)
