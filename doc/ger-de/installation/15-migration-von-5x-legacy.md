# 15. Migration vom 5.x-Legacy-Stack

Dieses Kapitel überführt eine Site aus der 5.x-Ära nach Exponential 6.0: eZ Publish 5.0 bis 5.4 und die Community-Releases
2013.x und 2014.x, die zwei Kernel nebeneinander betrieben (den Symfony-„New Stack“ in `ezpublish/` und den Legacy-Kernel
in `ezpublish_legacy/`, verbunden durch das Legacy-Bundle, aus dem später LegacyBridge wurde), sowie die darauf folgende
5.90-Linie. Exponential ist die Fortführung dieses Legacy-Kernels, daher ist der Umzug eine Herauslösung und keine
Neuentwicklung: Sie nehmen `ezpublish_legacy/` aus der Symfony-Anwendung heraus, halten in INI-Dateien fest, was die
Symfony-Seite zur Laufzeit in ihn eingespeist hat, und aktualisieren die Datenbank an Ort und Stelle. Das Kapitel behandelt
jeden Punkt der Migrationsanleitung des Herstellers für diesen Weg und nennt die Stellen, an denen der Code von Exponential
etwas anders macht; danach geht es weiter: die gemeinsamen Datenbanktabellen, Feldtypen und Datentypen, Benutzer und
Passwort-Hashes, Bildvariationen und Bild-Aliase, Siteaccess-Konfiguration in YAML und in `site.ini`, Routen, Caches, Twig
und TPL, REST, Cron, Suche, Clustering, die Datenbankschritte je Engine, Prüfung, Rollback, ein Zeitplan und eine
Checkliste. Für eine Site, die nie die Symfony-Seite betrieben hat, lesen Sie stattdessen [Kapitel 14](14-migration-von-4x.md);
die Update-Dateien Version für Version finden Sie in [Kapitel 11](11-aktualisieren.md).

[Inhalt](README.md) · Zurück: [14. Migration von der 4.x-Linie](14-migration-von-4x.md) · Weiter: [16. Migration von eZ Platform und Ibexa](16-migration-von-ez-platform-und-ibexa.md)

## 15.1 Was sich ändert und was die Anleitung des Herstellers sagt

Der Hersteller hat diese Migration für ein anderes Ziel dokumentiert: eZ Platform (später Ibexa DXP), ein reines
Symfony-Produkt ohne den Legacy-Kernel. Seine Seiten sind
[Migrating from eZ Publish](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/),
[Migrating from eZ Publish Platform](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish_platform/)
und [Common migration issues](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/).
Exponential geht den umgekehrten Weg: Es behält den Legacy-Kernel, seine Datentypen, seine Templates und seine
Administration und lässt die Symfony-Seite weg. Die meisten Schritte des Herstellers gelten daher entweder nicht oder in
umgekehrter Richtung. Jeder seiner Punkte ist hier aufgeführt, zusammen mit der Stelle, an der dieses Kapitel ihn behandelt.

| Punkt des Herstellers | Für Exponential | Abschnitt |
|---|---|---|
| Stufenweise aktualisieren: 5.4 oder 2014.11 auf eZ Platform 1.7, dann 1.13, dann 2.x | Ein einziger Weg: die 5.4-Datenbank auf 6.0.0 und dann auf 6.0.15, an Ort und Stelle | [15.7](#157-die-datenbank-schritt-für-schritt) |
| Voraussetzungen: PHP 7.1, MySQL 5.5 oder MariaDB, ein Browser von 2017 | PHP 8.0 bis 8.5 (auch für Velocity); MySQL, MariaDB, PostgreSQL, SQLite, Oracle, MongoDB | [15.4](#154-planung-und-zeitplan) |
| Sichern; die Site vom Netz nehmen | Ebenso, mit `maintenance.php` | [15.7](#157-die-datenbank-schritt-für-schritt) |
| Inhalte der 4. und 5. Generation bleiben kompatibel | Ja, und ohne Konvertierung: Es ist dasselbe Schema | [15.3](#153-was-übernommen-wird-die-gemeinsame-datenbank) |
| XmlText wird durch RichText ersetzt; Konvertierung mit `ezxmltext:convert-to-richtext` (Probelauf, Export der fehlschlagenden Felder, Korrektur von Hand, `ezxmltext:import-xml`), nachdem das XmlText-Feldtyp-Bundle registriert wurde | **Widerlegt.** XmlText (`ezxmltext`) ist der Rich-Text-Datentyp des Kernels. Es wird nichts konvertiert. | [15.8](#158-feldtypen-und-datentypen) |
| Das Page-Feld (ezflow) wird durch die Enterprise-LandingPage ersetzt; Migration mit `ezflow-migration-toolkit` | **Widerlegt.** `ezpage` bleibt, über das Paket `ezflow`, das Exponential standardmäßig installiert | [15.8](#158-feldtypen-und-datentypen) |
| Star Rating wird nicht unterstützt | **Widerlegt.** `ezsrrating` bleibt, über das Paket `ezstarrating`, standardmäßig installiert | [15.8](#158-feldtypen-und-datentypen) |
| Sortierung nach Klassenbezeichner (6) oder Klassenname (7) wird nicht unterstützt; mit SQL ermitteln | **Widerlegt.** Beide Sortierfelder existieren (`eZContentObjectTreeNode::SORT_FIELD_CLASS_IDENTIFIER = 6`, `SORT_FIELD_CLASS_NAME = 7`). Die Abfrage ist trotzdem nützlich | [15.20](#1520-häufige-probleme) |
| Interne Entwürfe vor der Migration entfernen (`InternalDraftsCleanUpLimit`, `InternalDraftsDuration` auf 0) | Dieselben Einstellungen und derselbe Cronjob existieren; empfohlen | [15.7](#157-die-datenbank-schritt-für-schritt) |
| Eigene Feldtypen, Frontend und Administrationsmodule für den Symfony-Stack neu schreiben | **Umgekehrt.** Legacy-Datentypen, `.tpl`-Templates und Module funktionieren weiter; neu zu schreiben sind Symfony-Bundles, Twig-Templates und Code für die Public API | [15.12](#1512-templates-von-twig-zu-tpl), [15.11](#1511-routen-und-url-aliase) |
| `src/`, Composer-Pakete und Bundle-Registrierungen in das neue Projekt verschieben | Bundles haben in Exponential keinen Platz; ihre Logik wird zu Extensions | [15.5](#155-den-legacy-teil-herauslösen) |
| `parameters.yaml` und `config.yaml` in das neue `app/config/` verschieben, `ezpublish.yaml` als `ezplatform.yaml` (die 2.5-Fassung der Seite schreibt sie `.yml`) | **Umgekehrt.** Ihre Werte kommen in INI-Dateien | [15.6](#156-die-symfony-konfiguration-in-ini-übertragen) |
| Die SiteAccess-Namen beibehalten, sonst funktionieren `user/login`-Richtlinien nicht mehr | Dieselbe Regel, derselbe Grund (die Einschränkung speichert eine CRC32 des Namens) | [15.6](#156-die-symfony-konfiguration-in-ini-übertragen), [15.20](#1520-häufige-probleme) |
| Die Legacy-Bild-Aliase erneut in YAML definieren | **Umgekehrt.** Die YAML-`image_variations` müssen zu `image.ini`-Aliasen werden, weil die Bridge die Legacy-Aliasliste durch sie ersetzt hat | [15.10](#1510-bildvariationen-und-bild-aliase) |
| Optional: Legacy Bridge installieren und `src/legacy_files` per Symlink in `ezpublish_legacy/` einbinden | Nicht nötig; Exponential läuft eigenständig. Eine Bridge zu einer Symfony-Plattform gibt es weiterhin, wenn Sie eine möchten | [15.5](#155-den-legacy-teil-herauslösen) |
| Designs, Siteaccess-Einstellungen, Override-Einstellungen, Extensions (nicht die mitgelieferten), `config.php`, `config.cluster.php`, `var/storage/packages` kopieren | Dieselbe Liste, mit dem Exponential-Paket für jede mitgelieferte Extension | [15.5](#155-den-legacy-teil-herauslösen) |
| Binärdateien aus `web/var/<site>/storage` kopieren (ein Symlink auf `ezpublish_legacy/var`) | Ebenso | [15.5](#155-den-legacy-teil-herauslösen) |
| Berechtigungen erneut setzen; `composer update --prefer-dist` | Berechtigungen wie in Kapitel 8; niemals `composer update` über Ihre eigenen Checkouts | [15.5](#155-den-legacy-teil-herauslösen) |
| `dbupdate-5.4.0-to-6.13.0.sql` aus dem neuen Kernel anwenden | **Nicht tun.** Wenden Sie die Exponential-Dateien an; die eine Anweisung dieser Datei, die Exponential braucht, die breitere Spalte `password_hash`, steht in der Exponential-Datei von 5.4 auf 6.0 | [15.7](#157-die-datenbank-schritt-für-schritt), [15.9](#159-benutzer-passwort-hashes-und-sitzungen) |
| Enterprise-Schemas (datumsbasierter Publisher, Form Builder, Benachrichtigungen) | Nicht anwendbar | keiner |
| Custom Tags und ihre Attributtypen in RichText | Nicht anwendbar: Custom Tags bleiben `ezxmltext`-Custom-Tags, mit jedem Typ aus `ezoe_attributes.ini` | [15.8](#158-feldtypen-und-datentypen) |
| Varnish-VCL; Virtual-Host-Dateien in `doc/apache2`, `doc/nginx` | Exponential sendet keine Purges an einen Proxy; seine eigenen Caches ersetzen ihn. Serverkonfiguration in Kapitel 8 | [15.14](#1514-caches), [15.18](#1518-die-site-ausliefern) |
| `assets:install`, `assetic:dump` | Nicht nötig: Der Kernel liefert die Design-Dateien selbst aus | [15.18](#1518-die-site-ausliefern) |
| Anmeldebildschirm ohne Styles: alle Siteaccesses zur `user/login`-Einschränkung der Rolle Anonymous hinzufügen | Dieselbe Ursache und dieselbe Lösung | [15.20](#1520-häufige-probleme) |
| URL-Aliase im alten Stil: den Slug-Konverter auf `urlalias_compat` oder `urlalias_iri` umstellen | Die Legacy-Einstellung `[URLTranslator] TransformationGroup` | [15.11](#1511-routen-und-url-aliase) |
| Häufige Probleme: URL-Aliase neu erzeugen, Bildpfade normalisieren, „Unknown relation type 0“, das Always-Available-Flag auf allen Feldern, leerer `sort_key_string` | Jeweils mit Ursache und Lösung in Exponential; die `ezpublish:update:*`-Befehle gibt es hier nicht | [15.20](#1520-häufige-probleme) |
| Die neue Plattform neben Legacy zu betreiben ist „praktisch unmöglich“ | Hier nicht das Ziel; die se7enxweb-Bridge behält das Legacy-Schema bei und betreibt tatsächlich beides | [15.5](#155-den-legacy-teil-herauslösen) |

## 15.2 Welche Variante betreiben Sie?

Aus der 5.x-Ära sind drei Arten von Sites hervorgegangen. Sehen Sie sich das Wurzelverzeichnis der alten Installation an:

| Sie sehen | Sie betreiben | Lesen Sie |
|---|---|---|
| `ezpublish/` (mit `EzPublishKernel.php`, `console`, `config/ezpublish.yml`), `ezpublish_legacy/`, `web/`, `src/` | den Stack mit zwei Kerneln: 5.0 bis 5.4 oder Community 2013.x und 2014.x | dieses Kapitel |
| nur den Legacy-Baum (`index.php`, `kernel/`, `lib/`, `settings/`, `extension/` im Wurzelverzeichnis), `lib/version.php` mit `VERSION_MAJOR = 5` und `VERSION_MINOR = 90` | die reine Legacy-Linie 5.90 (2015.01, 2017.x und später) | [Kapitel 11](11-aktualisieren.md#115-von-54-oder-590-auf-600), dazu [15.9](#159-benutzer-passwort-hashes-und-sitzungen) |
| den Baum mit zwei Kerneln, aber das Document Root des Webservers zeigt auf `ezpublish_legacy/` | den Legacy-Kernel, betrieben ohne Symfony | dieses Kapitel; die Teile über eingespeiste Einstellungen sind weniger wichtig |

Die Prüfungen, ausgeführt im alten Wurzelverzeichnis:

```bash
ls ezpublish/EzPublishKernel.php ezpublish/config/ezpublish.yml ezpublish_legacy/lib/version.php
grep -n "const VERSION_\|const EDITION" ezpublish_legacy/lib/version.php
grep -n '"symfony-app-dir"\|ezpublish-kernel\|ezpublish-legacy' composer.json
composer show ezsystems/ezpublish-kernel ezsystems/ezpublish-legacy
```

Was die Antworten bedeuten, geprüft anhand der Upstream-Repositories:

| Release | `lib/version.php` des Legacy-Kernels | `ezsite_data` `ezpublish-version` | `ezuser.password_hash` | Bekannte Hash-Typen |
|---|---|---|---|---|
| Community 2014.11 | `VERSION_MAJOR = 2014`, `VERSION_MINOR = 11`, `VERSION_ALIAS = '5.4'`, Edition „eZ Publish Community Project“ | `5.4.0alpha1` | `varchar(50)` | 1 bis 5 |
| 5.4 (Enterprise) | lesen Sie es in Ihrer Kopie nach | `5.4.0alpha1` nach der Update-Datei von 5.3 auf 5.4 (sie schreibt diesen Wert) | `varchar(50)`, sofern nicht neu mit einer breiteren Spalte installiert: Keine Datei der 4.x- und 5.x-Kette verbreitert sie (die Exponential-Datei von 5.4 auf 6.0.0 tut es, [15.7](#157-die-datenbank-schritt-für-schritt)) | suchen Sie nach `PASSWORD_HASH_PHP_DEFAULT` ([15.21](#1521-rollback)) |
| 2015.01, 2017.08 | `5`, `90`, Alias `5.90` oder `2017.08` | | `varchar(50)` | 1 bis 5 |
| 2017.12 und später | `5`, `90`, Alias `2017.12` und höher | | `varchar(255)` | 1 bis 7 |

Die `composer.json` des Community-Projekts (`ezsystems/ezpublish-community`, Tag `v2014.11.0`) verlangt
`ezsystems/ezpublish-kernel` und `ezsystems/ezpublish-legacy` und setzt `"symfony-app-dir": "ezpublish"`; dieser Schlüssel
bewirkt, dass das Installer-Plugin den Legacy-Kernel in `ezpublish_legacy/` ablegt. Lesen Sie die Datenbankversion wie in
[11.2](11-aktualisieren.md#112-welche-version-betreiben-sie) beschrieben aus. Ein `5.4.0alpha1` ist dort für eine 5.4-Datenbank
normal und bedeutet nicht, dass eine Vorabversion installiert wurde.

## 15.3 Was übernommen wird: die gemeinsame Datenbank

**Der New Stack von 5.x hatte keine eigenen Tabellen.** Seine Persistenzschicht, die „Legacy Storage Engine“, las und
schrieb direkt das Legacy-Schema. Tabelle für Tabelle am Tag `v2014.11.0` verglichen, enthält die `data/mysql/schema.sql`
des New Stack jede der 118 Tabellen der Legacy-Datei `kernel/sql/mysql/kernel_schema.sql` und zehn weitere, die zu
Legacy-Extensions gehören, nicht zu Symfony: `ezcomment`, `ezcomment_notification`, `ezcomment_subscriber`,
`ezcomment_subscription` (ezcomments), `ezgmaplocation` (ezgmaplocation), `ezm_block`, `ezm_pool` (ezflow), `ezstarrating`,
`ezstarrating_data` (ezstarrating) und `ezsearch_return_count`, die von der Update-Datei von 5.3 auf 5.4 entfernt wird.

Inhalte, die über den New Stack erstellt wurden (die Public API, REST API v2, ein Symfony-Importbefehl), bestehen also aus
denselben Zeilen wie Inhalte, die in der Legacy-Administration erstellt wurden: `ezcontentobject`,
`ezcontentobject_attribute`, `ezcontentobject_tree`, `ezcontentobject_name`, `ezurlalias_ml`, `ezuser` und so weiter.
Exponential liest all das. Ausnahmen sind Feldtypen, die es nur auf der Symfony-Seite gab
([15.8](#158-feldtypen-und-datentypen)).

**Das Schema von Exponential ist dieses Legacy-Schema plus zehn Tabellen**, die alle von den Update-Schritten angelegt
werden: `expaudit_cursor`, `expaudit_event`, `expaudit_file` (Audit-Index), `expbookmark_folder` (Lesezeichenordner),
`expmail_category`, `expmail_consent_log`, `expmail_pending`, `expmail_preference`, `expmail_suppression`
(E-Mail-Einstellungen) und `ezrss_export_opml_item` (OPML-Export). Keine Legacy-Tabelle wurde entfernt.

**Tabellen, die Exponential nicht kennt, werden ignoriert.** Eine Symfony-Sitzungstabelle, eine Tabelle Ihres eigenen
Bundles oder Tabellen eines bereits begonnenen eZ-Platform-Updates bleiben unberührt; der Kernel liest sie nie. Listen Sie
auf, was Sie haben, und vergleichen Sie es mit den obigen Listen:

```bash
mysql -u USER -p DATABASE -e "SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE() ORDER BY table_name;"
psql -U USER DATABASE -c "SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename;"
```

Oder lassen Sie das Schema-Werkzeug die Datenbank mit dem Schema dieser Release (`share/db_schema.dba`) vergleichen. Es gibt
das SQL aus, das sein zweites Argument in sein erstes überführen würde; mit der Referenz an erster Stelle nennt also jedes
`CREATE TABLE` eine Tabelle, die Exponential hat und Ihrer Datenbank fehlt, und jedes `DROP TABLE` eine Tabelle, die nur
Ihre Datenbank hat. Lesen Sie die Ausgabe, führen Sie sie hier aber nicht aus: Die Update-Dateien aus 15.7 nehmen die
Änderungen in der richtigen Reihenfolge vor.

```bash
php bin/php/ezsqldiff.php --type=mysql --user=USER --password=PASS share/db_schema.dba DATABASE
```

Wenn jemand bereits die eZ-Platform-Datei `dbupdate-5.4.0-to-6.13.0.sql` angewendet hat, trägt die Datenbank auch deren
Änderungen: `ezsite_data` meldet `6.13.0`, `ezuser_login` wurde zu einem eindeutigen Index, `ezuser`-Attribute sind als
nicht durchsuchbar markiert, und Rollen mit `content/create` oder `content/edit` haben `content/publish`-Richtlinien
erhalten. Nichts davon hält Exponential auf (der Kernel hat eine eigene Richtlinienfunktion `content/publish`), setzen Sie
aber die Versionszeile wie in [15.7](#157-die-datenbank-schritt-für-schritt) beschrieben, damit die Update-Dateien greifen. Eine
Site, die weiter gegangen ist und auf eZ Platform oder Ibexa läuft, behandelt [Kapitel 16](16-migration-von-ez-platform-und-ibexa.md).

## 15.4 Planung und Zeitplan

**Voraussetzungen des Ziels.** PHP 8.0 bis 8.5 (`composer.json`: `^8.0`), auch für Velocity (ab seinem Release 0.0.4.54); die alte Site lief
auf PHP 5.3.17 oder höher (das Minimum des Community-Projekts), daher durchläuft jede PHP-Datei Ihrer eigenen Extensions
den Sprung über PHP 7 und 8. Siehe [Kapitel 2](02-voraussetzungen.md), [PHP-8-Unterstützung](../bc/6.0/php8.md) und die
PHP-Migrationsanleitungen in den Referenzen. Datenbanken: [Kapitel 9](09-datenbanken.md).

**Zuerst eine Bestandsaufnahme.** Listen Sie vor jeder Änderung aus der alten Site auf:

1. Die Siteaccesses und wie jeder davon erkannt wird (`ezpublish.yml`, Block `siteaccess:`).
2. Alles, was die Symfony-Seite in Legacy eingespeist hat ([15.6](#156-die-symfony-konfiguration-in-ini-übertragen)).
3. Die verwendeten Datentypen ([15.8](#158-feldtypen-und-datentypen)), insbesondere jedes `ezrichtext`.
4. Ihre Symfony-Bundles in `src/` und in `composer.json`, mit dem, was jedes tut: Routen, Controller, Twig-Templates,
   Event- oder Signal-Slot-Listener, Konsolenbefehle, REST-Erweiterungen.
5. Ihre Legacy-Extensions und Designs in `ezpublish_legacy/`.
6. Cron-Einträge, die Suchmaschine, das Cluster-Setup, den HTTP-Cache oder Varnish davor.

**Zeitplan.** Die Dauer hängt vom Umfang des Symfony-Codes ab, nicht von der Menge der Inhalte. Eine praktikable Reihenfolge:

| Phase | Was | Erledigt, wenn |
|---|---|---|
| 1. Bestandsaufnahme | die obige Liste | jedes Bundle hat einen Verantwortlichen und eine Entscheidung: als Extension neu schreiben, verwerfen oder auf einer Bridge behalten |
| 2. Probeinstallation | ein neues Exponential neben der alten Site, eine Kopie der Datenbank, Abschnitte 15.5 bis 15.7 | die Kopie öffnet sich in der Administration und die Startseite wird dargestellt |
| 3. Neuentwicklung | Twig-Frontend als TPL, Bundle-Logik als Extensions, Routen als Module | die Probe-Site besteht [15.19](#1519-prüfen) |
| 4. Generalprobe | das ganze Verfahren erneut aus einem frischen Dump, mit Zeitmessung | Sie kennen die Länge des Freeze-Zeitfensters |
| 5. Freeze | Redakteure hören auf; letzter Dump; Wartungsmodus auf der alten Site | |
| 6. Umstellung | Datenbankschritte am letzten Dump, Abgleich des Speichers, Umstellung von DNS oder Virtual Host | [15.19](#1519-prüfen) besteht unter der Live-Adresse |
| 7. Beobachten | Logs, Anmeldungen, Suche, Cron eine Woche lang; die alte Site bereithalten | [15.21](#1521-rollback) wird nicht mehr gebraucht |

Führen Sie Phase 2 mehr als einmal durch; ihr Datenbankteil ist der Maßstab für Phase 6.

## 15.5 Den Legacy-Teil herauslösen

Installieren Sie Exponential nicht in das alte Projekt. Die alte `composer.json` setzt `symfony-app-dir`, sodass ein
`composer require` dort Exponential in das `ezpublish_legacy/` einer Symfony-Anwendung legen würde, die nicht mehr dazu
passt. Legen Sie ein neues Verzeichnis an:

```bash
composer create-project se7enxweb/exponential exponential
```

(Andere Wege in [Kapitel 3](03-den-code-beschaffen.md).) Kopieren Sie dann aus dem alten Baum, wobei `<old>` dessen
Wurzelverzeichnis ist:

| Aus `<old>/` | In das neue Wurzelverzeichnis | Hinweis |
|---|---|---|
| `ezpublish_legacy/settings/override/` | `settings/override/` | ergänzen Sie dann, was [15.6](#156-die-symfony-konfiguration-in-ini-übertragen) aufführt |
| `ezpublish_legacy/settings/siteaccess/<yours>/` | `settings/siteaccess/<yours>/` | die Namen beibehalten |
| `ezpublish_legacy/extension/<yours>/` | `extension/<yours>/` | nicht die mitgelieferten (siehe unten) |
| `ezpublish_legacy/design/<yours>/` | `design/<yours>/` | nicht `admin`, `admin2`, `base`, `standard` |
| `ezpublish_legacy/var/<site>/storage/` (auch erreichbar als `web/var/<site>/storage`) | `var/<site>/storage/` | Bilder und Dateien; denselben Verzeichnisnamen `<site>` beibehalten |
| `ezpublish_legacy/var/storage/packages/` | `var/storage/packages/` | wenn Sie Pakete verwenden |
| `ezpublish_legacy/config.php`, `config.cluster.php` | das neue Wurzelverzeichnis | zuerst mit `config.php-RECOMMENDED` vergleichen |

Symlinks: Viele Projekte hielten Einstellungen und Designs außerhalb von `ezpublish_legacy/` in der Versionsverwaltung und
verlinkten sie hinein (von Hand oder mit dem Befehl `ezpublish:legacy:symlink` und `src/legacy_files/` der späteren
Legacy Bridge). Kopieren Sie die Dateien, auf die die Links zeigen, nicht die Links selbst: `cp -rL` oder `rsync -aL`.

Die mitgelieferten Extensions des 5.x-Legacy-Baums werden nicht kopiert; Exponential hat eigene Versionen davon, die
meisten werden mit ihm installiert (die `require`-Liste der `composer.json`):

| In 5.x mitgeliefert | Exponential |
|---|---|
| `ezjscore`, `ezoe`, `ezformtoken` | im Kernel-Repository |
| `ezflow`, `ezodf`, `ezie`, `ezmultiupload`, `ezmbpaex`, `ezprestapiprovider`, `ezwebin`, `ezdemo`, `ezstarrating`, `ezgmaplocation` | standardmäßig installiert (`se7enxweb/<name>`); in admin4 tritt `expimageeditor` (im Kernel-Repository) an die Stelle von `ezie` |
| `ezfind`, `ezscriptmonitor`, `ezsi` | empfohlen: `composer require se7enxweb/<name>` |
| `ezcomments` | `composer require se7enxweb/ezcomments` |
| `ez_network` | keine Entsprechung (es band die Site an den Supportdienst des Herstellers); entfernen Sie es aus `ActiveExtensions` |

Aktivieren Sie anschließend Ihre eigenen Extensions in `settings/override/site.ini.append.php` (`[ExtensionSettings]
ActiveExtensions[]`), erzeugen Sie die Class Maps und setzen Sie die Berechtigungen:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/console exp:checkclasses
```

`exp:checkclasses` listet jede Klasse auf, die PHP nicht laden will; so zeigt sich PHP-5-Code in einer alten Extension.
Dateiberechtigungen: [8.7](08-die-website-ausliefern.md#87-dateiberechtigungen-und-eigentümer).

Wenn Sie lieber ein Symfony-Frontend behalten und Exponential darin betreiben möchten, leistet die se7enxweb-Legacy-Bridge
das auf aktuellen Symfony-Versionen: [Legacy-Bridge](../funktionen/6.0/legacy-bridge.md). Der Rest dieses Kapitels geht
davon aus, dass Exponential eigenständig läuft.

## 15.6 Die Symfony-Konfiguration in INI übertragen

**Unter dem Stack mit zwei Kerneln hat Legacy mehrere seiner eigenen Einstellungen nicht gelesen.** Bevor der
Legacy-Kernel für jede Anfrage aufgebaut wurde, überschrieb der Konfigurations-Mapper des Legacy-Bundles
(`LegacyMapper\Configuration`, geprüft am Tag `v2014.11.0`; die se7enxweb-Bridge macht heute dasselbe, siehe die
[Bridge-Spezifikation](../spezifikationen/6.0/legacy-bridge-bundle.md)) sie mit Werten aus der Symfony-Konfiguration.
Nach der Herauslösung überschreibt sie nichts mehr; was in den Legacy-INI-Dateien steht, ist also das, was läuft, und in
einem 5.x-Projekt waren diese oft leer oder veraltet. Tragen Sie jede davon ein:

| Legacy-Einstellung | Stammte aus (Symfony-Seite) | Wo eintragen |
|---|---|---|
| `site.ini [DatabaseSettings] Server`, `Port`, `User`, `Password`, `Database`, `Socket` | Doctrine-Verbindung: `host`, `port`, `user`, `password`, `dbname`, `unix_socket` (ohne Socket: `disabled`) in `parameters.yml` / `ezpublish.yml` | `settings/override/site.ini.append.php` |
| `site.ini [DatabaseSettings] DatabaseImplementation` | Doctrine-Treiber: `pdo_mysql` → `ezmysqli`, `pdo_pgsql` → `ezpostgresql`, `oci8` → `ezoracle` | ebenda |
| `site.ini [FileSettings] VarDir`, `StorageDir` | `var_dir`, `storage_dir` | je Siteaccess, oder global bei nur einer Site |
| `site.ini [UserSettings] AnonymousUserID` | `anonymous_user_id` | global |
| `site.ini [SiteAccessSettings] PathPrefix`, `PathPrefixExclude[]` | `content.tree_root.location_id`, `content.tree_root.excluded_uri_prefixes` | je Siteaccess |
| `site.ini [SiteSettings] IndexPage`, `DefaultPage` | `/content/view/full/<root location>/` oder `default_page` | je Siteaccess |
| `content.ini [NodeSettings] RootNode` | `content.tree_root.location_id` | je Siteaccess |
| `logfile.ini [AccessLogFileSettings] PathPrefix[]` | das obige Pfadpräfix | global |
| `image.ini [AliasSettings] AliasList[]` und für jeden `[<alias>] Reference`, `Filters[]` | **alle** `image_variations` (die Legacy-Liste wurde ersetzt, nicht erweitert) | [15.10](#1510-bildvariationen-und-bild-aliase) |
| `image.ini [FileSettings] TemporaryDir`, `PublishedImages`, `VersionedImages` | `image.temporary_dir`, `image.published_images_dir`, `image.versioned_images_dir` | global |
| `image.ini [ImageMagick] IsEnabled`, `ExecutablePath`, `Executable`, `PreParameters`, `PostParameters`, `Filters[]` | `imagemagick:`-Optionen und `imagemagick.pre_parameters`, `post_parameters` | global |
| das Geheimnis des Formular-Tokens (`site.ini [HTMLForms] Secret`) | Symfony `kernel.secret` (`secret` in `parameters.yml`), oder das Token wurde abgeschaltet, wenn der CSRF-Schutz von Symfony aus war | global; ein langer Zufallswert |
| `file.ini [ClusteringSettings]`, `[eZDFSClusteringSettings]` (spätere Bridge-Versionen) | die DFS-Konfiguration | [15.17](#1517-cluster-und-dfs) |

`site.ini [ContentSettings] ViewCaching` wurde von der späteren Bridge auf `enabled` erzwungen; in Exponential ist es
standardmäßig `enabled`. Das Geheimnis ist wichtig: Schon die Hinweise zu 5.1 sagten, dass eine Site ohne Symfony
`[HTMLForms] Secret` selbst setzen muss ([Änderungen in 5.1](../../eng-us/bc/5.1/changes-5.1.txt)), und seit 6.0.15 wird ein POST
ohne gültiges Formular-Token mit HTTP 403 abgewiesen.

Einen Wert über die Kommandozeile setzen und sehen, welche Datei gewinnt:

```bash
php bin/php/console exp:ini set site.ini/HTMLForms/Secret "<long random value>" global
php bin/php/console exp:ini where site.ini/DatabaseSettings/Database
```

### Siteaccesses: von `ezpublish.yml` zu `site.ini`

In YAML wird ein Siteaccess unter `ezpublish: siteaccess: list` aufgeführt, unter `groups` gruppiert, über `match`
ausgewählt und unter `system: <siteaccess or group>` konfiguriert. In Exponential sind die Liste und die Erkennung globale
Einstellungen (`settings/override/site.ini.append.php`, `[SiteAccessSettings]`), und die Konfiguration jedes Siteaccess
liegt in `settings/siteaccess/<name>/`. Für Gruppen gibt es keine direkte Entsprechung: Legen Sie gemeinsame Werte in das
globale Override oder in das `settings/` einer Extension und Site-spezifische Werte in den jeweiligen Siteaccess.

| YAML (`ezpublish.yml`) | `site.ini` |
|---|---|
| `siteaccess: list: [a, b]` | `[SiteAccessSettings] AvailableSiteAccessList[]=a`, `AvailableSiteAccessList[]=b`; außerdem `[SiteSettings] SiteList[]` |
| `siteaccess: default_siteaccess: a` | `[SiteSettings] DefaultAccess=a` |
| `match: URIElement: 1` | `MatchOrder=uri`, `URIMatchType=element`, `URIMatchElement=1` |
| `match: Map\URI: { foo: a }` | `MatchOrder=uri`, `URIMatchType=map`, `URIMatchMapItems[]=foo;a` |
| `match: URIText: { prefix: .., suffix: .. }` | `URIMatchType=text`, `URIMatchSubtextPre=`, `URIMatchSubtextPost=` |
| `match: Regex\URI: { regex: .., itemNumber: 1 }` | `URIMatchType=regexp`, `URIMatchRegexp=`, `URIMatchRegexpItem=1` |
| `match: Map\Host: { www.example.com: a }` | `MatchOrder=host`, `HostMatchType=map`, `HostMatchMapItems[]=www.example.com;a` |
| `match: HostElement: 2` | `HostMatchType=element`, `HostMatchElement=2` |
| `match: HostText: { prefix: .., suffix: .. }` | `HostMatchType=text`, `HostMatchSubtextPre=`, `HostMatchSubtextPost=` |
| `match: Regex\Host: { regex: .., itemNumber: 1 }` | `HostMatchType=regexp`, `HostMatchRegexp=`, `HostMatchRegexpItem=1` |
| `match: Map\Port: { 8080: a }` | `MatchOrder=port`, `[PortAccessSettings] 8080=a` |
| `match: Compound\LogicalAnd` aus einem Host und einer URI | `MatchOrder=host_uri`, `HostUriMatchMapItems[]=www.example.com;en;a` |
| mehrere Matcher in Reihenfolge | `MatchOrder=uri;host;port` (von links nach rechts versucht) |
| `system: a: languages: [eng-GB, fre-FR]` | `settings/siteaccess/a/site.ini.append.php`: `[RegionalSettings] SiteLanguageList[]=eng-GB`, `SiteLanguageList[]=fre-FR`, `Locale=eng-GB`, `ContentObjectLocale=eng-GB` |
| `system: a: session: name: ..` | `[Session] SessionNameHandler=custom`, `SessionNamePrefix=`, `SessionNamePerSiteAccess=enabled` |
| `system: a: legacy_mode: true` | nichts: Legacy verarbeitet die URLs jetzt immer |
| `system: a: content: view_cache: ..` | `[ContentSettings] ViewCaching` |

Die Siteaccess-Erkennung im Detail, mit den anhand von `settings/site.ini` geprüften Einstellungsnamen:
[10.2](10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site).

## 15.7 Die Datenbank, Schritt für Schritt

Führen Sie dies zuerst an einer Kopie durch ([15.4](#154-planung-und-zeitplan), Phase 2), dann am letzten Dump.

**1. Sichern und Schreibzugriffe stoppen.**

```bash
mysqldump -u USER -p DATABASE > before-exponential.sql
pg_dump -U USER DATABASE > before-exponential.sql
```

Stoppen Sie auf der alten Site die Bearbeitung (mit der bisherigen Wartungsmethode Ihres Projekts); auf der neuen, bevor
sie Verkehr übernimmt:

```bash
php bin/php/maintenance.php on --message="Migration in progress" --until=2h
```

**2. Interne Entwürfe aufräumen (empfohlen).** Interne Entwürfe sind Versionen, die ein Redakteur geöffnet und nie
gespeichert hat. Der Hersteller entfernt sie, weil eines seiner Werkzeuge an Entwürfen ohne Namenszeile scheitert; in
Exponential sind sie harmlos, aber in einer Migration sind sie Ballast. Setzen Sie auf der alten Site oder auf der neuen,
bevor sie live geht, in `settings/override/content.ini.append.php`:

```ini
[VersionManagement]
InternalDraftsCleanUpLimit=0
InternalDraftsDuration[]
InternalDraftsDuration[days]=0
InternalDraftsDuration[hours]=0
InternalDraftsDuration[minutes]=0
InternalDraftsDuration[seconds]=0
```

```bash
php runcronjobs.php --script=internal_drafts_cleanup.php
```

Prüfen Sie den Blocknamen in Ihrer `content.ini`, bevor Sie ihn eintragen (die Einstellungen stehen in der
`settings/content.ini` des Kernels, Zeilen 45 bis 51 in 6.0.15), und entfernen Sie das Override danach wieder: Die
Standardwerte sind ein Tag und 100 pro Lauf.

**3. Die Datenbank auf 5.4 bringen.** Eine Datenbank von 5.0 bis 5.3 durchläuft zuerst die alte Kette aus
[11.3](11-aktualisieren.md#113-die-update-dateien) bis `5.4/dbupdate-5.3.0-to-5.4.0.sql` (dazu die `dbupdate-cluster-`-Dateien,
wenn die Site den Datenbank-Cluster verwendete), mit den Datenskripten jedes Schritts aus
[11.4](11-aktualisieren.md#114-von-310-bis-53-die-alte-kette). Eine Datenbank von 5.4 oder 2014.11 ist bereits so weit.

**4. 6.0.0 eintragen.** Die Datei von 5.4 auf 6.0.0 schreibt die Versionszeilen und bringt das Schema auf den Stand, den
der 6.0-Kernel verwendet (Schritt 5):

```bash
mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql
psql -U USER -d DATABASE -f update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql
```

Wenden Sie die ganze Datei an. Die MySQL-Datei beginnt mit `SET default_storage_engine=InnoDB;`, was jedes MySQL ab 5.5.3
und MariaDB akzeptieren. Kopien von vor Oktober 2026 begannen mit `SET storage_engine=InnoDB;`, was MySQL ab 5.7.5 und
MariaDB ab 12.0 ablehnen, und nahmen keine der Schemaänderungen aus Schritt 5 vor; verwenden Sie die aktuellen Dateien.

**5. Die Schemaänderungen nimmt die Update-Datei vor.** Der 6.0-Kernel verwendet drei Änderungen, die ein 5.x-Schema nicht
hat und die Upstream nur in seinen Dateien in `6.12/`, `7.2/` und `7.3/` ausgeliefert hat
([11.3](11-aktualisieren.md#die-verzeichnisse-612-72-und-73)):

- **`ezuser.password_hash`**: Jedes 5.x-Schema bis 2017.08 hat `password_hash varchar(50)`. Exponential speichert
  `php_default`-Hashes (bcrypt, 60 Zeichen) und schreibt mit `UpdateHash=true` den Hash jedes Benutzers bei der ersten
  Anmeldung neu ([15.9](#159-benutzer-passwort-hashes-und-sitzungen)). Die Datei verbreitert die Spalte auf 255, die Breite
  im Kernel-Schema.
- **`ezcontentobject_trash.trashed`**: der Zeitpunkt, zu dem Inhalt in den Papierkorb kam; ohne diese Spalte schlägt das
  Verschieben von Inhalt in den Papierkorb fehl. Die Datei fügt sie hinzu, wenn sie fehlt.
- **PostgreSQL-Sequenznamen**: Der Kernel liest neue IDs aus `<table>_<column>_seq`; eine 5.x-Datenbank hat
  `<table>_s`. Die Datei benennt sie um, wenn der alte Name existiert und der neue frei ist.

Die Datei von 6.0.0 auf 6.0.15 aus Schritt 6 nimmt dieselben Änderungen vor, für eine Site, die eine ältere Kopie der
Datei aus Schritt 4 verwendet hat. Auf einer Datenbank, die eine Änderung bereits hat, bleibt sie, wie sie ist. Von Hand
ist nichts auszuführen, außer:

- **Oracle**: `ezoracle` liefert dafür keine Datei; führen Sie `ALTER TABLE ezuser MODIFY ( password_hash VARCHAR2(255) );`
  (Standardsyntax) mit Ihrem Oracle-Client aus, und `ALTER TABLE ezcontentobject_trash ADD ( trashed INTEGER DEFAULT 0
  NOT NULL );`, wenn der Tabelle die Spalte fehlt.
- **Eine Site, die bereits eine ältere Kopie der Datei von 6.0.0 auf 6.0.15** ohne diese Änderungen angewendet hat: Wenden
  Sie diese Datei nicht erneut an. Wenden Sie die aktuelle Datei aus Schritt 4 an, die erneut laufen kann, und setzen Sie
  die Versionszeile zurück:

```sql
UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version';
```

**6. Die 6.0-Linie anwenden.**

```bash
mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
psql -U USER -d DATABASE -f update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql
php update/common/scripts/6.0/createaudittables.php --dry-run
php update/common/scripts/6.0/createaudittables.php
```

Was die Datei hinzufügt und was zu tun ist, wenn eine Anweisung fehlschlägt, steht in [11.6](11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand).

**7. Die 5.4-Datenskripte ausführen.** Selbst eine 5.4-Site profitiert davon, weil der New Stack Zeilen geschrieben hat,
die der Legacy-Kernel nicht auf dieselbe Weise geschrieben hätte. Jedes hat einen Probelaufmodus; lesen Sie zuerst seine
`--help`-Ausgabe:

```bash
php update/common/scripts/5.4/cleanupfieldvaluerelations.php --help
php update/common/scripts/5.4/cleanuntranslatablerelations.php --help
php update/common/scripts/5.4/fixremovedezurlobjectlinks.php --help
php update/common/scripts/5.4/fixtrashedimagereferences.php --help
php update/common/scripts/5.3/recreateimagesreferences.php --help
php update/common/scripts/5.3/updatenodeassignmentparentremoteids.php --help
```

**8. Konten ohne Einstellungszeile.** Seit 6.0.15 kann sich ein Benutzer ohne `ezuser_setting`-Zeile nicht anmelden.
Benutzern, die über die Public API oder einen Import angelegt wurden, kann sie fehlen; finden Sie sie mit der Abfrage in
der [Sicherheitsspezifikation vom August 2026](../spezifikationen/6.0/sicherheitshaertung-2026-08.md#f-06-konten-finden-die-sich-nicht-anmelden-können).

**9. Die Version prüfen** ([11.2](11-aktualisieren.md#112-welche-version-betreiben-sie)): `6.0.15stable`.

### Je Engine

| Engine | Hinweis |
|---|---|
| MySQL, MariaDB | Wenden Sie die Update-Dateien vollständig an (sie enthalten `SET default_storage_engine`). Konvertieren Sie bei Bedarf alte `utf8`-Tabellen (drei Byte): `php bin/php/ezconvertdbcharset.php`; MyISAM-Tabellen: `php bin/php/ezconvertmysqltabletype.php --newtype=InnoDB`. Der strikte SQL-Modus (Standard seit MySQL 5.7) macht aus einem zu langen `password_hash` einen Fehler statt einer stillen Kürzung; die Datei von 5.4 auf 6.0.0 verbreitert die Spalte, bevor sich ein Benutzer anmeldet. |
| PostgreSQL | Die Erweiterung `pgcrypto` muss vorhanden sein ([9.4](09-datenbanken.md#94-postgresql)). |
| Oracle | Die `ezoracle`-Kette endet bei 5.3; danach die Schritte 4 bis 6 mit Ihrem Oracle-Client, die Audit-Tabellen über `createaudittables.php`. |
| SQLite, MongoDB | 5.x lief nie darauf. Migrieren Sie zuerst auf MySQL oder PostgreSQL und wechseln Sie dann die Engine: [9.9](09-datenbanken.md#99-eine-bestehende-site-auf-eine-andere-engine-umstellen). |

## 15.8 Feldtypen und Datentypen

Die Symfony-Feldtypen von 5.x waren Implementierungen der Legacy-Datentypen über denselben Zeilen, mit denselben
Bezeichnern in `ezcontentclass_attribute.data_type_string`. Listen Sie auf, was Ihre Klassen verwenden:

```sql
SELECT data_type_string, COUNT(DISTINCT contentclass_id) AS classes
FROM ezcontentclass_attribute WHERE version = 0
GROUP BY data_type_string ORDER BY data_type_string;
```

und vergleichen Sie es mit den Datentypen, die Exponential hat:

```bash
php bin/php/console exp:ini get content.ini/DataTypeSettings/AvailableDataTypes
```

| Bezeichner | In 5.x | In Exponential |
|---|---|---|
| `ezxmltext` (XmlText) | in Legacy bearbeitet; der New Stack hat es dargestellt | der Datentyp des Kernels; nichts zu konvertieren |
| `ezpage` (Page, ezflow) | Legacy-ezflow | Paket `ezflow` (Standard); Blockdefinitionen in Ihrer `block.ini.append.php` funktionieren weiter |
| `ezsrrating` (Star rating) | Legacy-ezstarrating | Paket `ezstarrating` (Standard) |
| `ezgmaplocation` | Legacy-Extension | Paket `ezgmaplocation` (Standard) |
| `ezcomcomments` und die `ezcomment*`-Tabellen | Legacy-ezcomments | `composer require se7enxweb/ezcomments` |
| `ezrichtext` (RichText, DocBook-XML) | nur im New Stack; der 2014.11-Kernel hatte den Feldtyp bereits | **kein Datentyp**: konvertieren oder entfernen (siehe unten) |
| Ihr eigener Feldtyp | eine Symfony-Klasse, oft mit einem Legacy-Datentyp daneben | der Legacy-Datentyp, auf PHP 8 portiert; ein Feldtyp ohne einen solchen muss als Datentyp geschrieben werden |

**RichText zurück zu XmlText.** Exponential liefert keinen Konverter von `ezrichtext` nach `ezxmltext`, und das
`ezxmltext:convert-to-richtext` des Herstellers geht nur in die andere Richtung. Klassen mit `ezrichtext` sind auf einer
5.x-Site selten (die Legacy-Administration konnte sie nicht bearbeiten), und Sie entscheiden je Attribut:

- Wenn sich der Inhalt neu erstellen lässt, ändern Sie das Attribut in einer Kopie der Klasse auf `ezxmltext` und geben
  Sie ihn erneut ein.
- Andernfalls konvertieren Sie das gespeicherte DocBook mit einem eigenen Skript und speichern das Ergebnis über den Kernel
  (`eZContentObjectAttribute::setAttribute( 'data_text', ... )` und `store()`), damit Sortierschlüssel und Relationen
  erhalten bleiben. Die übliche Zuordnung der Elemente:

| DocBook (RichText) | `ezxmltext` |
|---|---|
| `<section xmlns="http://docbook.org/ns/docbook" ...>` | `<section xmlns:image="..." xmlns:xhtml="..." xmlns:custom="...">` |
| `<para>` | `<paragraph>` |
| `<emphasis role="strong">` / `<emphasis>` | `<strong>` / `<emphasize>` |
| `<title ezxhtml:level="2">` | `<header level="2">` (innerhalb verschachtelter `<section>`-Elemente) |
| `<itemizedlist>` / `<orderedlist>`, `<listitem>` | `<ul>` / `<ol>`, `<li>` (jeweils mit einem `<paragraph>`) |
| `<link xlink:href="ezlocation://42">`, `ezcontent://42`, eine externe URL | `<link node_id="42">`, `object_id="42"`, `url_id` (eine Zeile in `ezurl`) |
| `<ezembed xlink:href="ezcontent://42" view="embed">` | `<embed object_id="42" view="embed">` |
| `<informaltable>`, `<tr>`, `<td>` | `<table>`, `<tr>`, `<td>` |
| `<eztemplate name="x">` | `<custom name="x">` |
| `<literallayout>` | `<paragraph>` mit einem `<line>` je Textzeile |
| `<programlisting>` | `<literal>` (mit seiner `class`) |
| `<eztemplate name="rawhtml">` | `<literal class="html">` |

Die Zuordnung ist die Umkehrung des Vorwärts-Stylesheets im Konverter des Herstellers
(`eZXml2Docbook_core.xsl` in `ezsystems/ezplatform-xmltext-fieldtype`); die ausführlichere Tabelle, mit Links, Ankern und
Custom Tags, steht in [16.6.3](16-migration-von-ez-platform-und-ibexa.md#richtext-zu-xmltext).

Prüfen Sie jeden konvertierten Wert in einer Kopie: Öffnen Sie das Objekt im Online-Editor und veröffentlichen Sie es
einmal. Die XHTML-Ausgabe des Kernels stellt außerdem drei DocBook-Elemente dar, wenn sie in importiertem `ezxmltext`
vorkommen: `ezembed` (mit `xlink:href`), `para` (unter Beibehaltung seiner `ezxhtml:class`) und Zeilenumbrüche innerhalb
von `literallayout` (hinzugefügt am 30. Juli 2026, Commit `d7bebcb39b`,
`kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php`). Das hält halb konvertierten Text lesbar; ein
Konverter ist es nicht.

Custom Tags und ihre Attribute bleiben, wie sie in `ezxmltext` waren (`content.ini [CustomTagSettings]`, Attribute in
`ezoe_attributes.ini`), daher gilt die Tabelle des Herstellers mit den Attributtypen, die RichText nicht unterstützt, hier
nicht.

## 15.9 Benutzer, Passwort-Hashes und Sitzungen

Benutzer sind Inhaltsobjekte plus eine Zeile in `ezuser` (`login`, `email`, `password_hash`, `password_hash_type`) und
`ezuser_setting` (`is_enabled`). Der New Stack verwendete dieselben Zeilen, daher wird jedes Konto übernommen.

| `password_hash_type` | Name | 5.x (bis 2017.08) | Exponential |
|---|---|---|---|
| 0 | `empty` (kein eigenes Passwort: LDAP, Textdatei, Single Sign-on) | nein (2019 zum Legacy-Kernel hinzugefügt, EZP-29703) | ja |
| 1 | `md5_password` | ja | ja |
| 2 | `md5_user` | ja (der alte Standard) | ja |
| 3 | `md5_site` (abhängig von `[UserSettings] SiteName`) | ja | ja |
| 4 | `mysql` | ja | ja |
| 5 | `plaintext` | ja | ja |
| 6 | `bcrypt` | nein | ja |
| 7 | `php_default` | nein | ja, der Standard (`[UserSettings] HashType=php_default`) |

Mit `[UserSettings] UpdateHash=true` (Standard) erhält ein Benutzer, der sich mit einem alten Hash-Typ anmeldet, einen
neuen `php_default`-Hash, der in derselben Anfrage in `ezuser` geschrieben wird
(`kernel/classes/datatypes/ezuser/ezuser.php`). Deshalb muss die Spalte zuerst breit genug sein
([15.7](#157-die-datenbank-schritt-für-schritt), Schritt 5). Behalten Sie `SiteName` unverändert bei, solange noch ein Konto
Typ 3 hat. Zwei Folgen für ein Rollback stehen in [15.21](#1521-rollback).

**Sitzungen.** Unter dem Stack mit zwei Kerneln gehörte die Sitzung Symfony, und der Legacy-Kernel nutzte sie über
`ezpSessionHandlerSymfony`. Exponential verwendet seinen eigenen Handler (ein leeres `[Session] Handler=` bedeutet die
Session-Verwaltung von PHP), daher meldet sich nach der Umstellung jeder Besucher neu an; zu migrieren ist nichts. Wenn Sie
auf die Sitzungszählung oder die Sitzungsbereinigung in der Administration angewiesen waren, lesen Sie die Kommentare über
`Handler` in `settings/site.ini` zu `ezpSessionHandlerDB`.

**Formular-Token.** Das Legacy-Bundle gab der Formular-Token-Extension das Geheimnis und den Token-Feldnamen von Symfony
oder schaltete sie ab, wenn der CSRF-Schutz von Symfony aus war. Exponential prüft das Token immer; setzen Sie
`[HTMLForms] Secret` ([15.6](#156-die-symfony-konfiguration-in-ini-übertragen)) und stellen Sie sicher, dass jedes eigene
Formular das Token mitsendet.

## 15.10 Bildvariationen und Bild-Aliase

Die 2014.11-Bridge baute die Legacy-Aliasliste aus den YAML-`image_variations` auf: Sie setzte
`image.ini [AliasSettings] AliasList` auf genau die Namen der Variationen und schrieb `reference` und `filters` jeder
Variation als `[<alias>] Reference` und `Filters[]` (nur Filter, die sie als ImageMagick-Filter kannte). Aliase, die nur
in `image.ini`-Dateien existierten, waren auf der alten Site daher nicht wirksam. Tragen Sie jede Variation als INI-Alias
ein:

```yaml
# ezpublish.yml, 5.x
ezpublish:
    system:
        my_group:
            image_variations:
                articleimage:
                    reference: ~
                    filters:
                        - { name: geometry/scalewidth, params: [770] }
                articlethumbnail:
                    reference: ~
                    filters:
                        - { name: geometry/scaledownonly, params: [170, 220] }
```

```ini
; settings/siteaccess/<each siteaccess of my_group>/image.ini.append.php, or settings/override/
[AliasSettings]
AliasList[]=articleimage
AliasList[]=articlethumbnail

[articleimage]
Reference=
Filters[]=geometry/scalewidth=770

[articlethumbnail]
Reference=
Filters[]=geometry/scaledownonly=170;220
```

Parameter werden durch `;` getrennt. Die Filternamen (`geometry/scalewidth`, `geometry/scaledownonly`,
`geometry/scalewidthdownonly`, `geometry/crop` und weitere) sind in `settings/image.ini` definiert, ihre
ImageMagick-Form unter `[ImageMagick] Filters[]`.

**Die Dateien.** Der Legacy-Kernel erfasst jeden erzeugten Alias im XML des Attributs (`data_text`) und in
`ezimagefile`. Der New Stack von 2014.11 schrieb seine Variationen neben das Original als
`<name>_<variation>.<extension>` (`IORepositoryResolver::getFilePath()`), mit derselben Benennung wie Legacy, aber ohne sie
zu erfassen. Nach der Umstellung werden Aliase bei der ersten Verwendung neu erzeugt. Damit jeder Alias mit den neuen
Definitionen neu erstellt wird, lassen Sie alle ablaufen oder entfernen die Alias-Dateien (die Originale bleiben erhalten):

```bash
php bin/php/ezcache.php --clear-id=imagealias
php bin/php/ezcache.php --clear-id=imagealias --purge
```

Halten Sie `VarDir` und den Namen des Speicherverzeichnisses identisch mit der alten Site: Gespeicherte Bildpfade
enthalten sie. Mussten sie sich ändern, verschiebt `php update/common/scripts/5.1/fiximagesoutsidevardir.php` die Dateien
und aktualisiert die Verweise.

## 15.11 Routen und URL-Aliase

**URL-Aliase** liegen in `ezurlalias_ml`, das beide Kernel gemeinsam nutzen, daher funktioniert jeder Alias weiter. Neue
Aliase werden nach den Legacy-Regeln erzeugt: `site.ini [URLTranslator] TransformationGroup` (standardmäßig `urlalias`;
`urlalias_iri` für Unicode in URLs; `urlalias_compat` für den alten Stil) und `WordSeparator`. Verwenden Sie die Gruppe,
die die alten Legacy-Einstellungen hatten; sie ist das Legacy-Gegenstück zur Slug-Konverter-Einstellung des Herstellers.
Prüfen und reparieren Sie die Tabelle:

```bash
php bin/php/verify_aliases.php
php bin/php/verify_aliases.php --fix
php bin/php/updateniceurls.php --update-nodes
```

Führen Sie sie zuerst an der Kopie aus und vergleichen Sie einige Adressen vorher und nachher.

**Symfony-Routen** haben im Kernel keine Entsprechung. Eine Route zu einem eigenen Controller wird zu einer Modul-View einer
Extension (`extension/<name>/modules/<module>/module.php` und die View-Datei; siehe
[Extensions](../anleitungen/extensions.md)), erreichbar als `/<module>/<view>`; eine sprechende Adresse dafür ist ein eigener
URL-Alias oder ein Platzhalter (**Setup > URL-Übersetzer**). Eine Route, die nur weitergeleitet hat, wird zu einem
URL-Alias oder einer [Anfrageregel](../funktionen/6.0/anfrageregeln.md), die nach der Übersetzung des Alias erlauben,
verweigern, weiterleiten, umschreiben oder protokollieren kann. Die eigenen Routen des New Stack
(`/content/location/...`, `/_fragment`, `/api/ezp/v2/...`) verschwinden; suchen Sie in Logs und externen Links danach.

## 15.12 Templates: von Twig zu TPL

Die `.tpl`-Templates Ihrer Legacy-Designs funktionieren weiter; nur Twig-Templates müssen neu geschrieben werden. In einer
5.x-Site hat meist die Symfony-Seite die öffentlichen Seiten dargestellt (`legacy_mode: false`), mit den Legacy-Templates
als Rückfallebene, daher sind die Twig-Templates oft das gesamte Frontend.

| Symfony (`ezpublish.yml`, Twig) | Exponential |
|---|---|
| `system: <sa>: location_view: full: article: { template: ..., match: { Identifier\ContentType: article } }` | `settings/siteaccess/<sa>/override.ini.append.php`: ein Block mit `Source=node/view/full.tpl`, `MatchFile=full/article.tpl`, `Subdir=templates`, `Match[class_identifier]=article` |
| Matcher `Id\Location`, `Id\ParentLocation`, `Identifier\Section`, `Id\Remote`, `Depth`, `UrlAlias` | `Match[node]`, `Match[parent_node]`, `Match[section_identifier]`, `Match[remote_id]`, `Match[depth]`, `Match[url_alias]` (`kernel/classes/eznodeviewfunctions.php`) |
| `{% extends pagelayout %}` | `pagelayout.tpl` des Designs; die View schreibt `$module_result.content` |
| `{{ ez_render_field(content, 'body') }}` | `{attribute_view_gui attribute=$node.data_map.body}` |
| `{{ ez_field_value(content, 'title') }}` | `{$node.data_map.title.content}` |
| `{{ ez_content_name(content) }}` | `{$node.name\|wash}` |
| `{{ path(location) }}` | `{$node.url_alias\|ezurl}` |
| `{{ ez_image_alias(field, versionInfo, 'large').uri }}` | `{$node.data_map.image.content.large.url\|ezroot}` |
| `{{ ez_is_field_empty(content, 'image') }}` | `{$node.data_map.image.has_content}` (negiert) |
| `{{ render(controller(...)) }}`, `render_esi` | `{include uri='design:...'}`, ein `fetch()` oder eine Modul-View |
| `{% for x in list %}`, `{% if %}`, `{% set %}` | `{foreach $list as $x}`, `{if}`, `{def}` / `{set}` |
| `\|escape`, `\|raw` | `\|wash`; Ausgabe ohne `\|wash` |
| `asset('...')` | `'...'\|ezdesign`, `'...'\|ezimage` |
| Übersetzungen (`\|trans`) | `'...'\|i18n('context')` |

`ngsymfonytools` (`symfony_include`) stellt ein Twig-Template aus einer `.tpl` heraus dar, aber nur innerhalb einer
Symfony-Anwendung über die Bridge; einer eigenständigen Site hilft es nicht. Wie Templates gefunden, überschrieben und
untersucht werden: [Templates und Design](../anleitungen/templates-und-design.md); welches Template welches Markup geschrieben
hat, zeigen die [Template-Pfad-Kommentare](../funktionen/6.0/template-pfad-kommentare.md).

## 15.13 REST

REST API v2 des New Stack (`/api/ezp/v2/`, Medientypen wie `application/vnd.ez.api.Content+json`) verschwindet mit
Symfony. Exponential hat eine eigene REST-Schicht (`settings/rest.ini`, `ApiPrefix=/api`, standardmäßig OAuth:
`[Authentication] AuthenticationStyle=ezpRestOauthAuthenticationStyle`) und zwei Inhalts-Provider, beide standardmäßig
installiert:

| Paket | Routen | Version in der URL |
|---|---|---|
| `ezprestapiprovider` | lesend: `content/node/:nodeId` mit `/list`, `/listAtom`, `/fields`, `/field/:fieldIdentifier`, `/childrenCount`; `content/object/:objectId` mit `/fields`, `/field/:fieldIdentifier` | `v1` |
| `ezprestapi` (seit 6.0.9) | dieselben Lesezugriffe plus `content/node/create`, `content/node/delete/:nodeId` und eine Aktualisierung per POST an `content/node/:nodeId` | `v2` |

Beide registrieren den Provider `ezp`, daher antworten die Routen von `ezprestapi` unter `/api/ezp/v2/content/node/<id>`
(geprüft durch `tests/tests/kernel/classes/rest/ezpRestRoutesTest.php`). Das Präfix ist dasselbe wie bei REST API v2 des
New Stack, die API ist es nicht: andere Ressourcen, andere Nutzdaten, andere Authentifizierung. Jeder Client der alten API
muss neu geschrieben werden; nichts daran ist kompatibel. Für die Administration aus Skripten und Apps gibt es außerdem die
Remote-Dienste: [Remote-Dienste und Apps](../anleitungen/entfernte-dienste-und-apps.md).

## 15.14 Caches

| 5.x | Exponential |
|---|---|
| Symfony-HTTP-Cache (der PHP-Reverse-Proxy in `ezpublish/`) oder Varnish, vom Legacy-Bundle bei `content/cache`-Ereignissen gepurgt | der Inhalts-View-Cache und der Template-Block-Cache in `var/`; optional der rollenbewusste HTTP-Cache ([HTTP-Cache](../bc/6.0/httpcache.md), standardmäßig aus) und unter Velocity der Antwort-Cache ([8.3.8](08-die-website-ausliefern.md#838-der-antwort-cache)) |
| der Persistenz-Cache (SPI), Stash | nicht nötig; der Kernel hat eigene Caches innerhalb der Anfrage |
| `ezpublish/console cache:clear` | `php bin/php/ezcache.php --clear-all` oder `./console exp:cache` ([Cache-Konsole](../bc/6.0/cache-konsole.md)) |

**Varnish.** Das Legacy-Bundle hängte den Purger des HTTP-Caches an die Kernel-Ereignisse `content/cache` und
`content/cache/all`. Exponential sendet keine Purge- oder Ban-Anfragen an einen Proxy (kein `PURGE` oder `BAN` im Kernel),
daher liefert ein für 5.x konfiguriertes Varnish weiterhin veraltete Seiten aus. Verzichten Sie entweder auf Varnish (der
HTTP-Cache oder der Antwort-Cache von Velocity erledigt dieselbe Aufgabe innerhalb von Exponential), oder lassen Sie es
anonyme Seiten nur kurz cachen ([HTTP-Caching für anonyme
Besucher](../bc/6.0/http-caching.md)), oder purgen Sie es selbst: Der HTTP-Cache kann die Purge-Tags jeder Seite in einem Header nennen (`[HttpCacheSettings] TagHeader=xkey` für das
Varnish-Modul xkey), und der Kernel löst bei jeder Veröffentlichung weiterhin `content/cache` mit den Knoten- und
Objekt-IDs aus (`kernel/classes/ezcontentcachemanager.php`), was ein Listener in `site.ini [Event] Listeners[]` in Purges
umsetzen kann. Die Reihenfolge nach jeder Codeänderung und welcher Cache wann zu leeren ist:
[10.6](10-nach-der-installation.md#106-caches).

## 15.15 Suche

Der New Stack von 5.x suchte über die Legacy-Suchtabellen oder, mit `ezfind`, über Solr. Exponential sucht mit seiner
eingebauten Suchmaschine (`site.ini [SearchSettings] SearchEngine=eZSearchEngine`), bis Sie `ezfind` installieren
(`composer require se7enxweb/ezfind`) und darauf umstellen. Der Suchindex wird neu aufgebaut, nicht migriert: Ein Index der
Suchmaschine des New Stack oder eines alten ezfind gegen ein altes Solr-Schema wird nicht weiterverwendet.

```bash
php bin/php/updatesearchindex.php --clean
```

`exp:solr` startet, stoppt und prüft einen lokalen Solr-Server ([Solr-Befehle](../funktionen/6.0/befehle-fuer-webserver-und-solr.md));
er ist aus, bis `settings/solr.ini [SolrSettings] Enabled=true` gesetzt ist. Einzelheiten zur Indizierung:
[10.8](10-nach-der-installation.md#108-suchindex-und-bildverarbeitung).

## 15.16 Cron, Workflows und Ereignisse

Cron-Zeilen einer 5.x-Site führten Legacy-Skripte über Symfony aus, zum Beispiel
`php ezpublish/console ezpublish:legacy:script runcronjobs.php frequent`. In Exponential laufen die Skripte direkt:

```bash
php runcronjobs.php                    # the default part
php runcronjobs.php frequent           # a named part
php runcronjobs.php --list
```

Die Cronjob-Teile und eine Crontab: [10.3](10-nach-der-installation.md#103-cronjobs); Ausführen und Überwachen aus der
Administration: [Cronjob-Konsole](../funktionen/6.0/cronjobs-in-der-konsole.md).

Legacy-Workflows (`ezworkflow*`-Tabellen, Trigger, der Cronjob `workflow.php`) werden unverändert übernommen.
Reaktionen auf Inhaltsänderungen auf der Symfony-Seite (Signal-Slot-Listener, Event-Subscriber Ihrer Bundles) nicht:
Schreiben Sie jede als Workflow-Ereignistyp einer Extension neu oder als Listener in
`site.ini [Event] Listeners[]=<event>@<callback>` auf eines der Kernel-Ereignisse (`content/cache`, `content/cache/all`,
`content/class/cache`, `content/section/cache`, `content/state/assign`, `request/input`, `request/preinput`,
`user/cache/all` und weitere). Eigene Symfony-Konsolenbefehle werden zu Skripten Ihrer Extension im Stil von `bin/php/`.

## 15.17 Cluster und DFS

Der DFS-Cluster behält seine Tabellen (`ezdfsfile` und, seit 5.1, eine separate Cache-Tabelle) und seinen NFS-Mount,
daher wird ein 5.x-Cluster übernommen. In 5.x konnte die Symfony-Seite DFS konfigurieren, und das Legacy-Bundle speiste
die Konfiguration ein; tragen Sie sie in die globale `settings/override/file.ini.append.php` ein
(Cluster-Einstellungen werden vor den Siteaccess- und Extension-Einstellungen gelesen und gehören daher dorthin):

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/path/to/nfs
DBBackend=eZDFSFileHandlerMySQLiBackend
DBHost=dbhost
DBPort=3306
DBName=cluster
DBUser=USER
DBPassword=PASS
```

`DBBackend` ist `eZDFSFileHandlerMySQLiBackend`, `eZDFSFileHandlerPostgresqlBackend` (Paket `ezpostgresqlcluster`) oder
`eZDFSFileHandlerOracleBackend` (`ezoracle`). `index_cluster.php` im Wurzelverzeichnis liefert die Dateien aus und liest
die `CLUSTER_*`-Konstanten aus `config.cluster.php` (Beispiele in `config.php-RECOMMENDED`). Wenden Sie die
`dbupdate-cluster-`-Dateien jedes Schritts an, den Sie durchlaufen ([11.3](11-aktualisieren.md#113-die-update-dateien)); prüfen
Sie dann:

```bash
php bin/php/dfscleanup.php -S
php bin/php/dfscleanup.php -B
```

`-S` prüft die Dateien auf der Freigabe gegen die Datenbank, `-B` in umgekehrter Richtung; fügen Sie `-D` erst hinzu,
nachdem Sie das Ergebnis gelesen haben. `php bin/php/clusterize.php` verschiebt `var/` in den Cluster und mit `-u` wieder
heraus. Hintergrund: [konfigurierbares DFS-Backend](../funktionen/5.4/konfigurierbares-dfs-cluster-backend.md),
[Cluster-Index](../funktionen/4.7/cluster-index.md).

## 15.18 Die Site ausliefern

Das Document Root von 5.x war `web/`, mit den Front-Controller-Wrappern `index_rest.php` und `index_cluster.php`, die
`ezpublish:legacy:assets_install` dort installierte, und Assets, die `assets:install` und `assetic:dump` kopierten.
Exponential wird aus seinem eigenen Wurzelverzeichnis ausgeliefert: kein Asset-Schritt, die Design-Dateien werden so
ausgeliefert, wie sie sind, und vom ezjscore-Packer zusammengefasst. Wie Sie es ausliefern, wählen Sie in
[Kapitel 8](08-die-website-ausliefern.md): Exponential Velocity
([8.3](08-die-website-ausliefern.md#83-exponential-velocity-der-empfohlene-weg)), FrankenPHP oder Apache bzw. nginx mit
PHP-FPM und den Rewrite-Regeln aus `.htaccess_root` ([8.5](08-die-website-ausliefern.md#85-apache-mit-php-fpm)). Verwenden Sie
den 5.x-Virtual-Host nicht weiter: Seine Rewrite-Regeln zeigen auf `web/` und auf den Front-Controller von Symfony.

Die Rewrite-Regeln legen Sie mit `./console exp:setup:htaccess --apply` an (eine Kopie von `.htaccess_root`, oder
`cp .htaccess_root .htaccess`). Vergleichen Sie die aus `ezpublish_legacy/` kopierte `config.php` ebenso mit der neuen
Vorlage: `./console exp:setup:status`, `./console exp:setup:config` (der Diff, nichts wird geändert) und
`./console exp:config:list` (Einstellungen, die neu in `config.php-RECOMMENDED` sind, erscheinen als `(not set)`);
eine setzen Sie mit `./console exp:config:set <NAME> <value> --apply`. Siehe
[.htaccess und config.php von der Konsole aus einrichten](../anleitungen/setup-dateien-von-der-konsole.md).

## 15.19 Prüfen

```bash
php bin/php/ezcache.php --clear-all
php bin/php/console exp:checkdbfiles --no-verify-branches
php bin/php/console exp:checkclasses
php bin/php/console --version
```

Dann auf der neuen Site:

- [ ] Die Versionszeile lautet `6.0.15stable`; `ezuser.password_hash` ist 255 breit.
- [ ] Jeder Siteaccess der alten `siteaccess: list` antwortet unter seiner alten Adresse.
- [ ] Ein Redakteur, ein Administrator und ein Frontend-Benutzer melden sich jeweils zweimal an (die zweite Anmeldung verwendet den neu geschriebenen Hash).
- [ ] Ein eigenes Formular sendet ohne 403.
- [ ] Ein Objekt jeder Klasse öffnet sich in der Administration und auf der Site; Attribute jedes Datentyps aus
      [15.8](#158-feldtypen-und-datentypen) werden dargestellt.
- [ ] Bilder erscheinen in jedem Alias der alten `image_variations`.
- [ ] Eine Stichprobe alter URLs, entnommen dem Zugriffsprotokoll der alten Site, antwortet mit 200 oder derselben Weiterleitung wie zuvor.
- [ ] Die Suche findet ein Wort aus einem kürzlich veröffentlichten Objekt.
- [ ] Das Veröffentlichen eines Objekts aktualisiert die Seite für einen anonymen Besucher (Caches, Proxy).
- [ ] `php runcronjobs.php --list` zeigt die Cronjob-Teile, die Ihre Crontab aufruft; ein Lauf jedes Teils endet ohne Fehler.
- [ ] **Setup > Upgrade-Prüfung**, Dateikonsistenz, listet nur Dateien auf, die Sie absichtlich geändert haben
      ([Dateikonsistenzprüfung](../funktionen/6.0/dateikonsistenzpruefung.md)).

## 15.20 Häufige Probleme

Zuerst die Liste des Herstellers, jeweils mit der Ursache in Exponential; die darin genannten Befehle
`ezpublish:update:legacy_storage_*` und `ibexa:urls:*` gibt es hier nicht.

| Symptom | Ursache in Exponential | Lösung |
|---|---|---|
| URL-Aliase fehlen oder sind falsch (Hersteller: URL-Aliase neu erzeugen) | Aliase, die der New Stack mit anderen Transformationsregeln erzeugt hat, oder beschädigte Zeilen | `php bin/php/verify_aliases.php` (dann `--fix`); `php bin/php/updateniceurls.php --update-nodes`; [15.11](#1511-routen-und-url-aliase) |
| Bilder werden nicht angezeigt (Hersteller: Bildpfade mit nicht druckbaren Zeichen normalisieren) | `VarDir` oder das Speicherverzeichnis weicht von der alten Site ab, oder Dateinamen mit Zeichen, die das Dateisystem verstümmelt hat | `VarDir` wie bisher beibehalten; `php update/common/scripts/5.1/fiximagesoutsidevardir.php`; `php update/common/scripts/5.3/recreateimagesreferences.php` |
| „Unknown relation type 0“ (Hersteller: REST, nach einer Bearbeitung) | Zeilen in `ezcontentobject_link`, deren `relation_type`-Bits alle gelöscht wurden; der Kernel selbst tut das, wenn er den letzten Relationstyp einer Zeile entfernt (`removeContentObjectRelation()`), und jede Relationsabfrage filtert nach den Bits, daher zeigt Exponential sie nie an | nichts nötig; zum Aufräumen: `DELETE FROM ezcontentobject_link WHERE relation_type = 0;` auf einer gesicherten Datenbank |
| Das Always-Available-Flag ist auf den Feldern jeder Sprache gesetzt, nicht nur der Hauptsprache | Inhalte, die der New Stack mit mehreren Übersetzungen erzeugt hat; der Kernel setzt Bit 1 von `ezcontentobject_attribute.language_id` und `ezcontentobject_name.language_id` nur bei der Hauptsprache | finden: `SELECT a.contentobject_id, a.version, a.language_code FROM ezcontentobject_attribute a JOIN ezcontentobject o ON o.id = a.contentobject_id WHERE (a.language_id & 1) = 1 AND (a.language_id & ~1) <> (o.initial_language_id & ~1);` und dann für diese Objekte in der Administration **Immer verfügbar** aus- und wieder einschalten, was beide Tabellen neu schreibt (Oracle: `bitand()` statt `&`) |
| Unterelementlisten sortieren falsch oder Suchen übersehen Objekte (Hersteller: leerer `sort_key_string`) | Attribute, die der New Stack ohne Sortierschlüssel gespeichert hat | finden: `SELECT contentobject_id, data_type_string FROM ezcontentobject_attribute WHERE sort_key_string = '' AND data_text <> '' AND data_type_string IN ('ezstring', 'ezemail', 'ezcountry', 'ezidentifier', 'ezselection', 'ezpackage');` (die Datentypen, deren Sortierschlüssel eine Zeichenkette ist; `eztext` hat keinen) und die Objekte dann erneut veröffentlichen: Der Kernel berechnet Sortierschlüssel, sobald er ein Attribut speichert (`eZContentObjectAttribute::updateSortKey()`); einen Massenbefehl gibt es nicht |
| Der Tab „Unterelemente“ meldet Fehler bei Sortierung nach Klassenbezeichner oder Klassenname (Hersteller) | tritt nicht auf: Die Sortierfelder 6 und 7 existieren | nichts; `SELECT node_id, parent_node_id, sort_field FROM ezcontentobject_tree WHERE sort_field IN (6, 7);` listet sie auf, wenn Sie es wissen möchten |
| Anmeldeseite ohne Styles oder „kein Zugriff“ auf einem Siteaccess (Hersteller) | die `user/login`-Richtlinie der Rolle Anonymous (oder des Benutzers) ist über die CRC32 ihrer Namen auf Siteaccesses eingeschränkt; ein umbenannter oder neuer Siteaccess ist darin nicht enthalten | **Rollen**: die `user/login`-Richtlinie bearbeiten und die Siteaccesses hinzufügen; besser: die alten Namen beibehalten |

Und die Probleme des 5.x-Wegs, die der Hersteller nicht aufführt:

| Symptom | Ursache | Lösung |
|---|---|---|
| Die Anmeldung funktioniert einmal, danach nie wieder; oder ein Datenbankfehler bei der Anmeldung | `password_hash` ist noch `varchar(50)`, der neu geschriebene bcrypt-Hash (60) wurde abgeschnitten oder abgewiesen | [15.7](#157-die-datenbank-schritt-für-schritt), Schritt 5; die betroffenen Passwörter zurücksetzen (`php bin/php/resetuserpassword.php --help`) |
| Manche Konten können sich überhaupt nicht anmelden | keine `ezuser_setting`-Zeile (Konten, die per API oder Import angelegt wurden) | [15.7](#157-die-datenbank-schritt-für-schritt), Schritt 8 |
| Jeder POST eines eigenen Formulars antwortet mit 403 | kein Formular-Token: Unter 5.x war das Token aus oder verwendete das Geheimnis von Symfony | `[HTMLForms] Secret` setzen; das Token in das Formular aufnehmen |
| Datenbankfehler bei der ersten Anfrage | die Legacy-`[DatabaseSettings]` wurden nie ausgefüllt; die Bridge hat sie eingespeist | [15.6](#156-die-symfony-konfiguration-in-ini-übertragen) |
| Die Startseite eines Siteaccess zeigt den falschen Baum | `PathPrefix`, `IndexPage`, `RootNode` stammten aus `content.tree_root` | [15.6](#156-die-symfony-konfiguration-in-ini-übertragen) |
| Ein Bild-Alias ist unbekannt oder hat andere Abmessungen | die Aliasliste stammte aus YAML | [15.10](#1510-bildvariationen-und-bild-aliase) |
| Ein Attribut wird in einer Klasse als nichts dargestellt | `ezrichtext` oder ein Feldtyp ohne Legacy-Datentyp | [15.8](#158-feldtypen-und-datentypen) |
| `Class ... not found` oder ein fataler Fehler in einer Extension | PHP-5-Code oder keine Class Map | `php bin/php/ezpgenerateautoloads.php -e`, `exp:checkclasses`, [PHP-8-Unterstützung](../bc/6.0/php8.md) |
| `SET storage_engine=InnoDB;` schlägt fehl | Eine Update-Datei von vor Oktober 2026 auf MySQL ab 5.7.5 oder MariaDB ab 12.0 | die aktuelle Datei verwenden, die `SET default_storage_engine` enthält ([15.7](#157-die-datenbank-schritt-für-schritt)) |
| Seiten bleiben hinter Varnish veraltet | Exponential sendet keine Purges | [15.14](#1514-caches) |
| Cron tut nichts | die Crontab ruft noch `ezpublish/console` auf | [15.16](#1516-cron-workflows-und-ereignisse) |
| Clients der alten REST-API erhalten 404 | REST API v2 des New Stack gibt es nicht mehr | [15.13](#1513-rest) |

Weitere Symptome und Lösungen: [Kapitel 12](12-fehlerbehebung.md).

## 15.21 Rollback

Lassen Sie die alte Installation und ihre Datenbank unangetastet, bis die Beobachtungsphase vorbei ist. Die Migration
ändert die Kopie, nie das Original; ein Rollback besteht also darin, die Adresse wieder auf die alte Site umzustellen,
sofern auf der neuen Site nichts geschrieben wurde, das erhalten bleiben muss.

Wenn die neue Site live war und Sie mit ihren Daten zurück müssen:

- **Inhalte**, die Exponential geschrieben hat, liegen in denselben Tabellen und sind für den alten Kernel lesbar,
  ausgenommen Zeilen der zehn neuen Tabellen (die er ignoriert) und Attribute von Datentypen, die der alten Site fehlten.
- **Passwörter.** Jeder Benutzer, der sich bei Exponential angemeldet hat, hat jetzt einen Hash vom Typ 7, den ein
  5.x-Kernel bis 2017.08 nicht kennt: Diese Benutzer können sich bei der alten Site nicht anmelden. Prüfen Sie den alten
  Kernel (`grep -n "PASSWORD_HASH_PHP_DEFAULT" ezpublish_legacy/kernel/classes/datatypes/ezuser/ezuser.php`). Soll ein
  Rollback einige Wochen lang möglich bleiben, setzen Sie auf der neuen Site, bevor sie live geht:

  ```ini
  [UserSettings]
  HashType=md5_user
  UpdateHash=false
  ```

  und stellen Sie wieder auf `php_default` und `true` um, sobald die alte Site stillgelegt ist. Verwenden Sie den Typ, den
  die alte Site verwendet hat.
- **Die Versionszeile** lautet `6.0.15stable`; der alte Kernel prüft sie nicht, setzen Sie aber der Klarheit halber
  `5.4.0alpha1` (oder Ihren alten Wert) zurück.
- **Die Spalten und Tabellen von 6.0.15** bleiben; der alte Kernel ignoriert sie. Das verbreiterte `password_hash` ist
  harmlos.

Der saubere Weg zurück ist eine Wiederherstellung der Datenbank: der Dump aus Schritt 1 von
[15.7](#157-die-datenbank-schritt-für-schritt), dazu ein Abgleich von `var/<site>/storage/`, falls Redakteure Dateien hochgeladen
haben.

## 15.22 Checkliste

- [ ] Variante ermittelt ([15.2](#152-welche-variante-betreiben-sie)); Datenbankversion ausgelesen.
- [ ] Bestandsaufnahme erledigt: Siteaccesses, eingespeiste Einstellungen, Datentypen, Bundles, Extensions, Cron, Suche, Cluster, Cache.
- [ ] Für jedes Symfony-Bundle ist entschieden: Extension, verwerfen oder hinter einer Bridge behalten.
- [ ] Neues Exponential in einem eigenen Verzeichnis installiert; Ihre Extensions, Designs, Einstellungen und der Speicher
      kopiert, Links aufgelöst; mitgelieferte Extensions durch ihre Pakete ersetzt.
- [ ] Datenbank-, Datei-, Bild-, Siteaccess-, Benutzer- und Formular-Token-Einstellungen in INI eingetragen ([15.6](#156-die-symfony-konfiguration-in-ini-übertragen)).
- [ ] `image_variations` als `image.ini`-Aliase eingetragen.
- [ ] Datenbank: Sicherung, interne Entwürfe, Kette bis 5.4, Datei für 6.0.0 (`password_hash` verbreitert, `trashed`, PostgreSQL-Sequenzen), Datei für 6.0.15, Audit-Tabellen,
      Datenskripte von 5.3/5.4, Prüfung von `ezuser_setting`.
- [ ] `ezrichtext` und eigene Feldtypen konvertiert oder entfernt.
- [ ] Twig-Frontend als TPL-Overrides neu geschrieben; Routen als Module, Aliase oder Anfrageregeln.
- [ ] REST-Clients für die REST-Schicht von Exponential oder die Remote-Dienste neu geschrieben.
- [ ] Cache und Proxy entschieden; Varnish-Purging gelöst oder Varnish entfernt.
- [ ] Suchmaschine gewählt; Index neu aufgebaut.
- [ ] Crontab auf `runcronjobs.php` umgeschrieben; Listener als Workflows oder Event-Listener neu geschrieben.
- [ ] Cluster-Einstellungen im globalen `file.ini`-Override; `dfscleanup.php` ohne Befund.
- [ ] Ausgeliefert durch Velocity, FrankenPHP, Apache oder nginx; kein 5.x-Virtual-Host weiterverwendet.
- [ ] [15.19](#1519-prüfen) besteht an einer Kopie, mit Zeitmessung; dann unter der Live-Adresse.
- [ ] Rollback entschieden: Hash-Einstellungen für das Zeitfenster, alte Site aufbewahrt.

## Referenzen

In diesem Repository:

- [11. Upgrade](11-aktualisieren.md): die Update-Dateien, die alte Kette, die 6.0-Linie; [14. Migration von der 4.x-Linie](14-migration-von-4x.md);
  [12. Fehlerbehebung](12-fehlerbehebung.md); [8. Die Site ausliefern](08-die-website-ausliefern.md);
  [9. Datenbanken](09-datenbanken.md); [10. Nach der Installation](10-nach-der-installation.md); [3. Den Code beschaffen](03-den-code-beschaffen.md).
- Anleitungen: [Upgrade](../anleitungen/aktualisieren.md), [Templates und Design](../anleitungen/templates-und-design.md),
  [Extensions](../anleitungen/extensions.md), [Remote-Dienste und Apps](../anleitungen/entfernte-dienste-und-apps.md),
  [Betrieb einer Site](../anleitungen/betrieb-einer-website.md).
- Die Legacy-Bridge: [Legacy-Bridge](../funktionen/6.0/legacy-bridge.md),
  [Bridge-Spezifikation](../spezifikationen/6.0/legacy-bridge-bundle.md),
  [Paket-Forks und Umbenennungen von Befehlen](../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md).
- Hinweise zu 5.x: [Änderungen in 5.1](../../eng-us/bc/5.1/changes-5.1.txt), [5.2](../../eng-us/bc/5.2/changes-5.2.txt),
  [5.4](../../eng-us/bc/5.4/changes-5.4.txt), [5.90](../bc/5.90/README.md),
  [Passwortlänge](../bc/5.90/passwortlaenge.md), [PHP 7](../bc/5.90/php7.md).
- 6.0: [Changelog 6.0.0](../../changelogs/6.0/6.0.0.md), [6.0.9](../../changelogs/6.0/6.0.9.md),
  [6.0.15](../../changelogs/6.0/6.0.15.md); [PHP-8-Unterstützung](../bc/6.0/php8.md),
  [Unterstützung für PHP 8.0](../bc/6.0/php-8.0-unterstuetzung.md); [HTTP-Cache](../bc/6.0/httpcache.md),
  [HTTP-Caching für anonyme Besucher](../bc/6.0/http-caching.md),
  [Antwort-Cache von Velocity](../funktionen/6.0/velocity-antwort-cache.md),
  [Cache-Konsole](../bc/6.0/cache-konsole.md); [Anfrageregeln](../funktionen/6.0/anfrageregeln.md);
  [Cronjob-Konsole](../funktionen/6.0/cronjobs-in-der-konsole.md);
  [Solr-Befehle](../funktionen/6.0/befehle-fuer-webserver-und-solr.md);
  [Standardverteilung der Extensions](../funktionen/6.0/standard-extension-auslieferung.md);
  [Sicherheitsspezifikation vom August 2026](../spezifikationen/6.0/sicherheitshaertung-2026-08.md);
  [Dateikonsistenzprüfung](../funktionen/6.0/dateikonsistenzpruefung.md);
  [Wartungsmodus](../funktionen/6.0/wartungsmodus.md).
- Cluster: [konfigurierbares DFS-Backend](../funktionen/5.4/konfigurierbares-dfs-cluster-backend.md),
  [Cluster-Index](../funktionen/4.7/cluster-index.md).
- Code: `update/database/`, `update/common/scripts/`, `kernel/classes/datatypes/ezuser/ezuser.php`,
  `kernel/classes/ezcontentobjecttreenode.php`, `kernel/classes/eznodeviewfunctions.php`,
  `kernel/classes/ezcontentcachemanager.php`, `kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php`,
  `lib/ezsession/classes/ezpsessionhandlersymfony.php`, `extension/ezprestapi/classes/rest_provider.php`,
  `extension/ezprestapiprovider/classes/rest_provider.php`, `settings/site.ini`, `settings/image.ini`,
  `settings/file.ini`, `settings/rest.ini`, `share/db_schema.dba`, `kernel/sql/`.

Die Anleitung des Herstellers (mit eZ Platform und Ibexa DXP als Ziel):

- [Migrating from eZ Publish Platform](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish_platform/)
- [Migrating from eZ Publish](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/)
- [Common migration issues](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/)
- [Update-Datei der Plattform von 5.4.0 auf 6.13.0](https://github.com/ezsystems/ezpublish-kernel/blob/v6.13.0/data/update/mysql/dbupdate-5.4.0-to-6.13.0.sql),
  [Kernel-API-Änderungen in 6.0](https://github.com/ezsystems/ezpublish-kernel/blob/6.13/doc/bc/changes-6.0.md)

Quell-Repositories des Systems, von dem migriert wird:

- [ezsystems/ezpublish-kernel](https://github.com/ezsystems/ezpublish-kernel) (der New Stack und das Legacy-Bundle),
  [ezsystems/ezpublish-legacy](https://github.com/ezsystems/ezpublish-legacy),
  [ezsystems/ezpublish-community](https://github.com/ezsystems/ezpublish-community),
  [ezsystems/LegacyBridge](https://github.com/ezsystems/LegacyBridge),
  [ezsystems/ezplatform-xmltext-fieldtype](https://github.com/ezsystems/ezplatform-xmltext-fieldtype).

Exponential:

- GitHub: [se7enxweb/exponential](https://github.com/se7enxweb/exponential),
  [se7enxweb/legacyBridge](https://github.com/se7enxweb/legacyBridge),
  [se7enxweb/ezflow](https://github.com/se7enxweb/ezflow), [se7enxweb/ezstarrating](https://github.com/se7enxweb/ezstarrating),
  [se7enxweb/ezfind](https://github.com/se7enxweb/ezfind), [se7enxweb/ezcomments](https://github.com/se7enxweb/ezcomments).
- Packagist: [se7enxweb/exponential](https://packagist.org/packages/se7enxweb/exponential),
  [se7enxweb/legacy-bridge](https://packagist.org/packages/se7enxweb/legacy-bridge),
  [se7enxweb/ezfind](https://packagist.org/packages/se7enxweb/ezfind),
  [se7enxweb/ezcomments](https://packagist.org/packages/se7enxweb/ezcomments),
  [se7enxweb/ezprestapi](https://packagist.org/packages/se7enxweb/ezprestapi).

Extern:

- PHP-Migrationsanleitungen, vom PHP einer 5.x-Site bis heute: [5.6](https://www.php.net/manual/en/migration56.php),
  [7.0](https://www.php.net/manual/en/migration70.php), [7.1](https://www.php.net/manual/en/migration71.php),
  [7.2](https://www.php.net/manual/en/migration72.php), [7.3](https://www.php.net/manual/en/migration73.php),
  [7.4](https://www.php.net/manual/en/migration74.php), [8.0](https://www.php.net/manual/en/migration80.php),
  [8.1](https://www.php.net/manual/en/migration81.php).
- Hier genannte Symfony-Konzepte: [Bundles](https://symfony.com/doc/current/bundles.html),
  [Konfiguration](https://symfony.com/doc/current/configuration.html), [Routing](https://symfony.com/doc/current/routing.html),
  [Templates](https://symfony.com/doc/current/templates.html), [HTTP-Cache](https://symfony.com/doc/current/http_cache.html),
  [Sitzungen](https://symfony.com/doc/current/session.html); [Twig 3](https://twig.symfony.com/doc/3.x/).
- Datenbanken: [SQL-Modi von MySQL](https://dev.mysql.com/doc/refman/8.4/en/sql-mode.html),
  [mysqldump](https://dev.mysql.com/doc/refman/8.4/en/mysqldump.html),
  [PostgreSQL ALTER TABLE](https://www.postgresql.org/docs/current/sql-altertable.html),
  [pg_dump](https://www.postgresql.org/docs/current/app-pgdump.html),
  [Oracle ALTER TABLE](https://docs.oracle.com/en/database/oracle/oracle-database/19/sqlrf/ALTER-TABLE.html).
- [Varnish: Purging und Banning](https://www.varnish.org/docs/users-guide/purging.html);
  [Composer create-project](https://getcomposer.org/doc/03-cli.md#create-project).

[Inhalt](README.md) · Zurück: [14. Migration von der 4.x-Linie](14-migration-von-4x.md) · Weiter: [16. Migration von eZ Platform und Ibexa](16-migration-von-ez-platform-und-ibexa.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
