# 2. Voraussetzungen

Dieses Kapitel führt auf, was ein Rechner braucht, bevor Exponential darauf installiert werden kann, und warum jeder
Punkt nötig ist: die PHP-Version (8.0 bis 8.5, auch für Velocity), jede PHP-Extension mit der Stelle im
Code, die sie verlangt, die `php.ini`-Einstellungen, die der Setup-Assistent prüft, die Datenbankserver und ihre
Mindestversionen, Composer, einen Webserver sowie Hinweise zu Red Hat Enterprise Linux 9 und seinen Nachbauten, zu
Debian und Ubuntu und zu Plesk. Zum Schluss steht, was die Dokumentation über Arbeitsspeicher und Festplatte sagt. Alles
hier stammt aus `composer.json`, `settings/setup.ini`, `settings/site.ini` und `kernel/setup/`; wo ein Dokument und der
Code einander widersprechen, gilt der Code, und das Kapitel sagt es. Prüfen Sie einen Rechner anhand dieses Kapitels,
bevor Sie etwas herunterladen: Eine fehlende Extension, die Sie jetzt finden, kostet eine Paketinstallation; findet
man sie mitten in einer Installation, kostet sie einen Neustart der Installation.

[Inhalt](README.md) · Zurück: [1. Einführung](01-einfuehrung.md) · Weiter: [3. Den Code beschaffen](03-den-code-beschaffen.md)

## 2.1 Die Checkliste

| Bedarf | Minimum | Empfohlen | Geprüft durch |
|---|---|---|---|
| PHP | 8.0 | das neueste 8.x, das Ihr System anbietet (8.5 wird unterstützt) | Composer (`composer.json`, `require.php`); den Setup-Assistenten (`settings/setup.ini`, `[phpversion] MinimumVersion=8.0.0`) |
| PHP für Exponential Velocity | 8.0, mit `sockets` (ab dem Release 0.0.4.54; bis dahin 8.1) | wie oben, mit `pcntl` und `openssl` | Composer (`se7enxweb/exponential-velocity`: `"php": ">=8.0"`, `"ext-sockets": "*"`) |
| PHP-Extensions | siehe [2.3](#23-php-extensions) | zusätzlich die empfohlenen | Composer (`ext-*`), den Systemtest des Setup-Assistenten |
| `php.ini` | `memory_limit` 64M, `max_execution_time` 30, `date.timezone` gesetzt, `file_uploads` an, `allow_url_fopen` an | | den Systemtest des Setup-Assistenten |
| Datenbank | SQLite 3 (kein Server) | in der offiziellen Reihenfolge: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL ([2.6](#26-datenbanken)) | den Datenbankschritt des Setup-Assistenten |
| Composer | 2.x | das aktuelle 2.x | Sie |
| Webserver | keiner (Velocity oder der eingebaute Server von PHP) | Velocity oder Apache/nginx mit PHP-FPM | Sie |
| Bildkonvertierung | GD (`imagegd2`) oder `convert` von ImageMagick | beides | den Systemtest des Setup-Assistenten |

Am schnellsten prüfen Sie einen Rechner, indem Sie das dem Setup-Assistenten überlassen: Seine Seite **Systemtest**
führt jeden kritischen Test aus und gibt für jeden Fehler die Ursache und die Abhilfe aus (bei Berechtigungen die
genauen Befehle `chmod` und `chown`). Die Tests sind in [2.4](#24-der-systemtest-des-setup-assistenten) beschrieben. Der
Kickstarter und `exp:install` führen dieselben Tests in ihrem Schritt `SystemCheck` aus und halten dort mit denselben
Meldungen an; eine Installation über die Kommandozeile ist daher eine ebenso gute Prüfung (für das PHP der
Kommandozeile).

## 2.2 PHP

### Versionen

| PHP | Status |
|---|---|
| 8.0 | Die älteste unterstützte Version. Der Kernel und die mitgelieferten Extensions laufen darauf unter Apache mit mod_php oder PHP-FPM. Sie ist das Standard-PHP von Red Hat Enterprise Linux 9, AlmaLinux 9 und Rocky Linux 9. Velocity läuft darauf ab seinem Release 0.0.4.54 (was sich unterscheidet, steht in [2.9](#php-80-und-red-hat-enterprise-linux)). |
| 8.1 bis 8.4 | Unterstützt, einschließlich Velocity. |
| 8.5 | Unterstützt; die neueste getestete Version. |
| 7.4 und älter | Von 6.0.8 an nicht mehr unterstützt. Eine Site, die bei PHP 7.4 bleiben muss, bleibt bei 6.0.7. |

`composer.json` deklariert:

```json
"php": "^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4 || ^8.5 || ^8.6 || ^8.7 || ^8.8"
```

Composer verweigert daher die Installation auf allem, was älter als 8.0 ist. Die Versionen nach 8.5 werden von der
Bedingung akzeptiert, sind aber weder erschienen noch getestet. Verwenden Sie das neueste PHP, das Ihr System anbietet:
Jede Version bringt mehr Geschwindigkeit, und die älteren verlieren zuerst die Sicherheitsunterstützung (siehe die
[unterstützten Versionen](https://www.php.net/supported-versions.php) von PHP).

Die Unterstützung für PHP 8.0 ist neu in 6.0.15. Die veröffentlichten Releases `v6.0.8` bis `v6.0.14` verlangen in ihrer
`composer.json` PHP 8.1, und `v6.0.7` verlangt 7.4 oder 8.1; installieren Sie auf PHP 8.0 daher die Linie 6.0.15
(`dev-main`, bis ihr Tag veröffentlicht ist; [Kapitel 3](03-den-code-beschaffen.md#31-versionen-tags-und-branches)).

Unter PHP 8.0 lädt `autoload.php` die Datei `lib/phpcompat.php`, die die wenigen Funktionen definiert, die PHP 8.0
fehlen (darunter `array_is_list()`); unter 8.1 und neuer wird diese Datei nicht einmal geöffnet. Die Einzelheiten und die
Stellen, die PHP 8.1 benötigten und in 6.0.15 für 8.0 lauffähig gemacht wurden, stehen in
[PHP-8.0-Unterstützung](../bc/6.0/php-8.0-unterstuetzung.md). Die Geschichte der Arbeit an PHP 8 seit 6.0.0 steht in
[PHP-8-Unterstützung](../bc/6.0/php8.md).

**So prüfen Sie es.** Die erste Zeile von `php -v` nennt die Version:

```text
PHP 8.5.11 (cli) (built: Sep 22 2026 13:32:06) (NTS gcc x86_64)
```

**Die PHP-Prüfung des Setup-Assistenten.** Der kritische Test `phpversion` vergleicht das laufende PHP mit
`settings/setup.ini`, `[phpversion] MinimumVersion=8.0.0` (dieselbe Untergrenze wie in `composer.json`) und lehnt die in
`UnstableVersions` aufgeführten Versionen ab (`6`, die PHP-Version, die nie erschienen ist). Da Composer 8.0 bereits
erzwingt, schlägt dieser Test in der Praxis nur fehl, wenn das PHP des Webservers älter ist als das PHP der
Kommandozeile, mit dem Composer lief – und genau diesen Fall soll er abfangen.

**64-Bit-PHP.** `composer.json` schlägt `php-64bit` vor: „For support of more than 30 languages, a 64bit php
installation on all involved prod/dev machines is required“. Der Grund ist, dass die Sprachen eines Objekts als Bits
einer einzigen Ganzzahl gespeichert werden. Die PHP-Pakete der aktuellen Linux-Distributionen sind 64-Bit-Builds; unter
PHP 8.5 gibt `php -i | grep "Integer Size"` auf einem solchen Build `PHP Integer Size => 64 bits` aus.

### PHP auf der Kommandozeile und im Web

Exponential verwendet PHP zweimal: Das PHP des Webservers (PHP-FPM, mod_php oder die Worker von Velocity) liefert die
Seiten aus, und das PHP der Kommandozeile (`php`) führt Composer, die Installer, die Konsole, die Cronjobs und Velocity
selbst aus. Beide müssen in einer unterstützten Version vorliegen und dieselben Extensions haben. Auf Rechnern mit
mehreren PHP-Versionen (Plesk, Remi) ist das `php` im `PATH` oft nicht dasjenige, das der Webserver verwendet; rufen Sie
die richtige Programmdatei über ihren Pfad auf, zum Beispiel `/opt/plesk/php/8.5/bin/php bin/php/console ...`.

Was schiefgeht, wenn sie sich unterscheiden: Composer wählt, mit einem neueren PHP auf der Kommandozeile ausgeführt,
Paketversionen, die das Web-PHP nicht ausführen kann, oder die Installation über die Kommandozeile gelingt, und der
Browser zeigt danach eine weiße Seite, weil dem Web-PHP eine Extension fehlt. Vergleichen Sie vor der Installation
`php -v` und `php -m` mit der PHP-Informationsseite des Webservers.

## 2.3 PHP-Extensions

Drei Quellen bestimmen, welche Extensions benötigt werden: `composer.json` (`require` und `suggest`) sowie die Tests des
Setup-Assistenten, aufgeführt in `settings/site.ini`, `[SetupSettings] CriticalTests` (müssen bestanden werden) und
`OptionalTests` (werden nur gemeldet), mit ihren Parametern in `settings/setup.ini`.

### Erforderlich

| Extension | Verlangt von | Warum |
|---|---|---|
| `dom` | `composer.json`; kritischer Test `dom_extension` | XML: Das Rich-Text-Feld wird als XML gespeichert und mit DOM geparst; auch Pakete und Werkzeuge für Einstellungen lesen XML |
| `libxml` | `composer.json` | die XML-Bibliothek unter `dom`, `simplexml` und `xsl` |
| `simplexml` | `composer.json` | Lesen von XML-Dateien, zum Beispiel der Metadaten von Extensions (`kernel/private/classes/ezpextension.php`) und von Bildattributen |
| `xsl` | kritischer Test `xsl_extension` | der Setup-Assistent fährt ohne sie nicht fort |
| `mbstring` | `composer.json`; kritischer Test `mbstring_extension` | Multibyte-Zeichenketten in jeder Sprache; der Test listet außerdem die Zeichensätze auf, die mbstring umwandeln kann (`eZMBStringMapper`) |
| `iconv` | `composer.json`; kritischer Test `iconv_extension` | Umwandlung von Zeichensätzen („Without it Exponential will not work.“) |
| `intl` | kritischer Test `intl_extension` | der Setup-Assistent fährt ohne sie nicht fort; der Kernel verwendet sie außerdem für internationale Hostnamen (`idn_to_ascii()`) |
| `pcre` | `composer.json` | reguläre Ausdrücke überall (Teil von PHP) |
| `json` | `composer.json` | JSON in der Administration, in der REST-API und in der Konsole (seit 8.0 Teil von PHP) |
| `reflection`, `spl` | `composer.json` | verwendet vom Kernel und von den Zeta Components (Teil von PHP) |
| `session` | `composer.json`; kritischer Test `php_session` | Benutzersitzungen |
| `zlib` | kritischer Test `zlib_extension` | gzip: Paketarchive, die gzip-Datei-Handler in `lib/ezfile/`, komprimierte Audit-Dateien |
| eine Datenbank-Extension | kritischer Test `database_extensions` | `sqlite3`, `mysqli`, `pgsql`, `mongodb` oder `oci8`; mindestens eine (`Require=one`) |
| `gd` oder ImageMagick | kritischer Test `image_conversion` | Bildvarianten; entweder `imagegd2()` aus GD oder das Programm `convert` (`Require=one`) |

Die Meldungen des Assistenten für eine fehlende Extension lauten zum Beispiel „Missing DOM extension“, „Missing xsl
extension“, „Missing intl extension“ oder „Missing database handler“ („No supported database handlers were found.
Exponential requires a database to store it's data, without one the system will fail.“). Sie stammen aus
`design/standard/templates/setup/tests/`.

**Für Velocity** fügt das Engine-Paket eigene hinzu: Es verlangt `sockets` (TCP-Optionen bei Client-Verbindungen) und
schlägt `pcntl` (geordnetes Herunterfahren und der Worker-Pool) und `openssl` (HTTPS) vor. In der Praxis sind für ein
produktives Velocity alle drei nötig.

### Datenbank-Extensions

| Datenbank | PHP-Extension | Hinweise |
|---|---|---|
| 1. SQLite 3 | `sqlite3` | Der Standard; sonst wird nichts benötigt. |
| 2. PostgreSQL | `pgsql` | |
| 3. MongoDB | `mongodb` (PECL) | Der Treiber (`lib/ezdb/classes/expmongodb.php`) benötigt außerdem die PHP-Bibliothek `mongodb/mongodb` (`MongoDB\Client`), die `composer.json` vorschlägt, aber nicht verlangt: Fügen Sie sie mit `composer require mongodb/mongodb` hinzu. Die [MongoDB-Seite](../funktionen/6.0/mongodb-datenbankunterstuetzung.md) nennt PHP 8.2 oder neuer als Voraussetzung. |
| 4. MariaDB | `mysqli` | Derselbe Treiber wie für MySQL. |
| 5. Oracle | `oci8` | Über die Extension `ezoracle`, die `composer.json` verlangt; `oci8` selbst kommt von Ihrem Anbieter, aus EPEL oder von Remi. |
| 6. MySQL | `mysqli` | `composer.json` schlägt `ext-mysqli` vor (seine Beschreibung nennt mysqli noch den Standard-Handler, aus der Zeit, bevor SQLite der Standard wurde). |

Der optionale Test `database_all_extensions` meldet, welche von `sqlite3`, `mysqli`, `pgsql` und `mongodb` fehlen; das
ist nur eine Information. Installieren Sie nur die Extension der Datenbank, die Sie verwenden.

### Empfohlen

| Extension | Quelle | Warum |
|---|---|---|
| `curl` | `composer.json` suggest; optionaler Test `curl_extension` | „better support for interacting with other servers, like downloading packages over SSL“ |
| `gd` | `composer.json` suggest; optionaler Test `imagegd_extension` | Bildbearbeitung, sofern ImageMagick nicht installiert ist |
| `openssl` | `composer.json` suggest | „cryptographically secure random bytes“; HTTPS in Velocity |
| `pcntl` | `composer.json` suggest | asynchrones Veröffentlichen (`bin/php/ezasynchronouspublisher.php` forkt mit `pcntl_fork()`); der Worker-Pool von Velocity |
| `opcache` | (Performance) | der Cache für kompilierten Code von PHP; ab PHP 8.5 immer einkompiliert. Siehe [Velocity-Opcode-Cache](../funktionen/6.0/velocity-opcode-cache-und-profil.md) |
| `redis` | `composer.json` suggest (`se7enxweb/sevenx_valkey`, `sevenx_valkey_cache`) | nur für die Valkey/Redis-Cache-Extensions |

Der optionale Test `texttoimage_functions` prüft `imagettftext()` und `imagettfbbox()` (GD mit FreeType), die der
Template-Operator für Text-zu-Bild benötigt. `imagemagick_program` sucht `convert` in `/bin`, `/sbin`, `/usr/bin`,
`/usr/sbin`, `/usr/local/bin` und `/usr/local/sbin`.

### Herausfinden, was fehlt

```bash
php -m
```

listet die Extensions auf, die das PHP der Kommandozeile hat. Ist Velocity installiert, vergleicht die Konsole den
Rechner außerdem mit dem Extension-Satz des Velocity-Pakets:

```bash
php bin/php/console exp:velocity ext check
```

gibt je Extension eine Zeile aus, markiert mit `ok` oder als fehlend, mit dem jeweiligen Zweck, zum Beispiel:

```text
PHP 8.5 on linux-x86_64: provides the 'lite' set; checked against 'standard'
  ok  sockets          server       TCP options (no-delay, keep-alive) on client connections
  ok  dom              required     XML documents: rich text, packages, feeds, WebDAV
  ok  intl             required     Locale-aware formatting, collation and transliteration
```

und `exp:velocity ext install-hint <extension> ...` gibt den Befehl zur Paketinstallation für Ihr System aus, zum
Beispiel `php bin/php/console exp:velocity ext install-hint intl xsl`. (`exp:velocity ext
list|check|plan|install-hint|build` übergibt an `qbixctl ext:<action>` der Engine und benötigt daher das
Velocity-Paket, siehe [Kapitel 3](03-den-code-beschaffen.md#34-das-velocity-paket); als `root` fügen Sie
`--allow-root-user` hinzu.)

## 2.4 Der Systemtest des Setup-Assistenten

Der Assistent führt die Tests aus `[SetupSettings] CriticalTests` auf seiner Seite **Systemtest** aus und die aus
`OptionalTests` auf **Feineinstellung des Systems**. Ein kritischer Fehler hält den Assistenten an, bis er behoben ist
(oder der Test mit seinem Kontrollkästchen ignoriert wird, was nicht empfohlen ist: Ein ignorierter Test schlägt später
meist erneut fehl, dann als defekte Seite).

### Kritische Tests

| Test | Prüft | Einstellung in `settings/setup.ini` | Abhilfe |
|---|---|---|---|
| `directory_permissions` | Der Webserver kann in die Verzeichnisse aus `CheckList` schreiben | `[directory_permissions] CheckList`: `design`, `extension`, `settings`, `settings/override`, `settings/siteaccess`, `settings/siteaccess/admin`, `var`, `var/cache` und seine Unterverzeichnisse, `var/log`, `var/storage` und seine Unterverzeichnisse, `var/autoload` | die Befehle `chmod`/`chown`, die die Seite ausgibt |
| `phpversion` | PHP mindestens `MinimumVersion`, keine aus `UnstableVersions` | `[phpversion] MinimumVersion=8.0.0` | ein neueres PHP |
| `database_extensions` | mindestens eine Datenbank-Extension | `[database_extensions] Extensions=sqlite3;mysqli;pgsql;mongodb;oci8`, `Require=one` | eine installieren |
| `image_conversion` | `imagegd2()` von GD oder `convert` von ImageMagick | `[image_conversion] TestList`, `Require=one` | `gd` oder ImageMagick installieren |
| `safe_mode` | `safe_mode` aus | | (aus PHP entfernt; immer bestanden) |
| `memory_limit` | `memory_limit` mindestens `MinMemoryLimit`; `-1` (unbegrenzt) besteht | `[memory_limit] MinMemoryLimit=64M` | in der `php.ini` erhöhen |
| `execution_time` | `max_execution_time` mindestens `MinExecutionTime`; `0` (unbegrenzt) besteht | `[execution_time] MinExecutionTime=30` | in der `php.ini` erhöhen |
| `magic_quotes_runtime` | Magic Quotes aus | | (aus PHP entfernt; immer bestanden) |
| `allow_url_fopen` | `allow_url_fopen` an | | in der `php.ini` einschalten |
| `php_session` | die Extension `session` | `[php_session]` | installieren |
| `file_upload` | `file_uploads` an, und das Upload-Verzeichnis existiert und ist beschreibbar | `[file_upload]` | siehe [2.5](#25-einstellungen-in-der-phpini) |
| `zlib_extension`, `dom_extension`, `iconv_extension`, `mbstring_extension`, `intl_extension`, `xsl_extension` | die Extension ist geladen | je ein Block | installieren |
| `timezone` | eine Zeitzone wurde gewählt | | `date.timezone` setzen |
| `ezcversion` | die Zeta Components sind vorhanden: Klasse `ezcBaseFile` mit der Methode `walkrecursive` | `[ezcversion]` | `composer install` |

Der Verzeichnistest läuft als der Benutzer, der die Prüfung ausführt: im Browser der Benutzer des Webservers, auf der
Kommandozeile derjenige, der den Kickstarter startet. Eine Installation über die Kommandozeile durch einen anderen
Benutzer kann den Test bestehen und trotzdem Dateien hinterlassen, die der Webserver nicht beschreiben kann;
[Kapitel 3](03-den-code-beschaffen.md#37-rechte-nach-dem-beschaffen-des-codes) zeigt, wie Sie sie übergeben.

### Optionale Tests

`variables_order` (muss `E` enthalten, damit PHP Umgebungsvariablen registriert), `php_magicquotes`, `curl_extension`,
`imagegd_extension`, `imagemagick_program`, `database_all_extensions`, `php_register_globals`, `texttoimage_functions`
und `open_basedir`. Der letzte warnt, wenn `open_basedir` gesetzt ist: „open_basedir is in use and can give problems
running Exponential due to bugs in some PHP versions.“

Alle Testfunktionen stehen in `kernel/setup/ezsetuptests.php`; die Meldungen stehen in
`design/standard/templates/setup/tests/<test>_error.tpl`.

## 2.5 Einstellungen in der php.ini

| Einstellung | Wert | Warum |
|---|---|---|
| `memory_limit` | mindestens `64M` | kritischer Test `memory_limit` („Insufficient memory allocated to install Exponential“). Das ist eine Untergrenze: Paketinstallationen, Importe und Bildbearbeitung brauchen mehr; geben Sie PHP also, was der Rechner erübrigen kann. |
| `max_execution_time` | mindestens `30` | kritischer Test `execution_time` („Insufficient execution time allowed to install Exponential“). Das PHP der Kommandozeile hat standardmäßig keine Begrenzung. |
| `date.timezone` | Ihre Zeitzone, zum Beispiel `Europe/Berlin` | kritischer Test `timezone` |
| `file_uploads` | `On` | kritischer Test `file_upload` |
| `upload_tmp_dir` | ein beschreibbares Verzeichnis oder leer | wenn leer, nimmt der Test `TMPDIR` oder `/tmp` an und prüft, ob dort eine Datei angelegt werden kann |
| `allow_url_fopen` | `On` | kritischer Test `allow_url_fopen` („allow_url_fopen ini setting is disabled“) |
| `variables_order` | enthält `E`, zum Beispiel `EGPCS` | optionaler Test `variables_order` („PHP does not register environment variables“) |
| `open_basedir` | leer | optionaler Test `open_basedir` |
| `upload_max_filesize`, `post_max_size` | so groß wie die Dateien, die Redakteure hochladen | nicht getestet; PHP lehnt größere Uploads ab |

Ein minimaler Satz für eine produktive `php.ini` (oder die `php_admin_value`-Zeilen eines PHP-FPM-Pools):

```ini
memory_limit = 256M
max_execution_time = 60
date.timezone = Europe/Berlin
file_uploads = On
upload_max_filesize = 64M
post_max_size = 64M
allow_url_fopen = On
variables_order = "EGPCS"
```

Die Werte oberhalb der Mindestwerte sind Vorschläge, keine Anforderungen: Bemessen Sie sie nach der größten Datei, die
Ihre Redakteure hochladen, und nach dem längsten Import, den Sie über den Browser ausführen.

**Die Zeitzone im Detail.** `index.php` setzt UTC, wenn die `php.ini` kein `date.timezone` enthält. Nur in diesem Fall
schlägt der Test fehl: Eine `php.ini`, die `date.timezone` setzt, selbst auf UTC, ist eine Entscheidung und besteht
(`eZSetupTestTimeZone()`). Die Site kann außerdem ihre eigene Zeitzone in `settings/site.ini`,
`[TimeZoneSettings] TimeZone` setzen (vom Kernel bei jeder Anfrage angewendet), und `config.php` kann
`date_default_timezone_set()` aufrufen; das auskommentierte Beispiel steht in `config.php-RECOMMENDED`. Die gültigen
Namen sind in der [Liste der unterstützten Zeitzonen](https://www.php.net/manual/en/timezones.php) des PHP-Handbuchs
aufgeführt.

Starten Sie nach dem Bearbeiten der `php.ini` neu oder laden Sie neu, was PHP ausführt: PHP-FPM, Apache mit mod_php oder
Velocity (`php bin/php/console exp:velocity restart`). `php --ini` zeigt, welche Dateien das PHP der Kommandozeile
liest; das Web-PHP liest unter Umständen andere (PHP-FPM-Pools haben ihre eigenen `php_admin_value`-Zeilen). Eine
Änderung, die „nicht funktioniert“, ist meistens eine Änderung an der Datei, die das jeweils andere PHP liest.

## 2.6 Datenbanken

> **Unterstützte Datenbanken (Exponential 6.0.x), nach Präferenz geordnet:** 1. SQLite 3 (Standard) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlichem
> Aufwand möglich. Einzelheiten: [Datenbankunterstützung](../hilfe/datenbankunterstuetzung.md).

| Datenbank | `DatabaseImplementation` und Aliase (`settings/site.ini`, `[DatabaseSettings] ImplementationAlias[]`) | Vom Assistenten geprüfte Mindestversion des Servers | Praktisches Minimum |
|---|---|---|---|
| 1. SQLite 3 | `sqlite3` | 3.0.1 | 3.33 (`UPDATE ... FROM`) |
| 2. PostgreSQL | `pgsql`, `postgresql`, `ezpostgresql` | 8.0 | 13 (`pgcrypto` als vertrauenswürdige Extension) |
| 3. MongoDB | `mongodb` | 4.0 | 6.0, mit PHP 8.2 |
| 4. MariaDB | `mysqli`, `mysql`, `ezmysqli`, `ezmysql` | 4.1.1 | eine Release, die Ihr Anbieter unterstützt |
| 5. Oracle | `oracle`, `ezoracle` (aus `extension/ezoracle/settings/site.ini.append.php`) | 19 | 19c |
| 6. MySQL | `mysqli`, `mysql`, `ezmysqli`, `ezmysql` | 4.1.1 | eine Release, die Ihr Anbieter unterstützt |

Die Mindestversionen, die der Assistent prüft, sind die aus `eZSetupDatabaseMap()` in `kernel/setup/ezsetupcommon.php`.
Sie sind Untergrenzen, keine Empfehlungen: Betreiben Sie eine Version, die Ihr Anbieter noch unterstützt. Die
praktischen Mindestwerte ergeben sich aus den Treibern;
[Datenbankunterstützung](../hilfe/datenbankunterstuetzung.md#versionen-am-code-überprüft) erläutert jeden einzelnen und
führt die Versionen auf, die auf dem Entwicklungsserver und in der CI nachweislich funktionieren.

- **SQLite** ist der Standard (`settings/setup.ini`, `[DatabaseSettings] DefaultType=sqlite3`) und benötigt keinen
  Server: Die Datenbank ist eine einzelne Datei unter `var/storage/sqlite3/`. Sie ist eine vollwertige
  Produktionsdatenbank; siehe die [Funktionsseite zu SQLite](../funktionen/6.0/sqlite-datenbank.md).
- **PostgreSQL** benötigt die Extension `pgcrypto` in der Datenbank (ihre Funktion `digest`). Der Assistent versucht,
  sie anzulegen; wenn er das nicht kann, sagt er es und bittet darum, dass der Eigentümer der Datenbank oder ein
  Superuser `CREATE EXTENSION pgcrypto;` ausführt.
- **MongoDB** und **Oracle**: siehe ihre Seiten, [MongoDB](../funktionen/6.0/mongodb-datenbankunterstuetzung.md) und
  [Treiber für SQLite und Oracle](../spezifikationen/6.0/datenbanktreiber-sqlite-oracle.md).
- **MariaDB oder MySQL**: Die Datenbank muss UTF-8 verwenden. Wenn der Zeichensatz der Datenbank von dem abweicht, den
  der Assistent möchte, hält der Assistent mit „The database [...] cannot be used, the setup wizard wants to create the
  site in [...] but the database has been created using character set [...]“ an. Legen Sie die Datenbank mit
  `CHARACTER SET utf8mb4` an, dann kann das nicht passieren.
- **Microsoft SQL Server** hat in 6.0.x keinen Treiber; was dafür nötig wäre, steht in
  [Datenbankunterstützung](../hilfe/datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand).

Legen Sie bei einem Datenbankserver vor der Installation die Datenbank und einen Benutzer an, der darin Tabellen anlegen
darf; die Installer legen weder Datenbanken noch Benutzer an. Die Auswahl und das Tuning einer Datenbank sind Thema von
[Kapitel 9](09-datenbanken.md).

## 2.7 Composer

Exponential wird mit [Composer](https://getcomposer.org/) 2 installiert. Composer lädt die Zeta Components, die
Extensions, die `composer.json` verlangt, und, wenn Sie es wünschen, Velocity herunter. Ohne Composer oder ohne dass er
ausgeführt wurde, fehlt `vendor/`, und die Site kann nicht starten.

- Installieren Sie ihn wie auf [getcomposer.org/download](https://getcomposer.org/download/) beschrieben und prüfen Sie
  ihn mit `composer --version`; das gibt die Composer-Version und das PHP aus, mit dem er läuft, zum Beispiel
  `Composer version 2.8.12 2025-09-19 13:41:59` und `PHP version 8.5.11 (/usr/bin/php)`.
- `composer.json` führt das Installer-Plugin `se7enxweb/exponential-legacy-installer` unter `config.allow-plugins` auf.
  Diese Einstellung gibt es seit Composer 2.2; ein älteres Composer 2 ignoriert sie. Verwenden Sie ein aktuelles
  Composer 2.
- Composer läuft mit dem PHP der Kommandozeile. Gegen dessen Version und Extensions prüft Composer die Anforderungen
  `ext-*` und `php`.
- Die Entwicklungsabhängigkeiten sind so gewählt, dass sie zu jedem unterstützten PHP passen. `composer.json` hat in
  `require-dev` `"phpunit/phpunit": "^9.6 || ^10.5 || ^11.5 || ^12.0 || ^13.4"`, und Composer wählt das PHPUnit, das
  zum verwendeten PHP passt (9.6 auf 8.0, 10.5 auf 8.1, 11.5 auf 8.2, 12 auf 8.3, 13.4 auf 8.4 und 8.5). Das ist
  wichtig, weil das Repository keine `composer.lock` mitliefert, und ohne Lock-Datei löst Composer `require-dev` auch
  mit `--no-dev` auf. Die Testsuite selbst benötigt PHPUnit 10 oder neuer und läuft daher auf PHP 8.1 und neuer. Die
  Release-Tags `v6.0.13` und `v6.0.14` verlangen noch PHPUnit 13.0.0, das PHP 8.4.1 benötigt; wie Sie diese Tags auf
  einem älteren PHP installieren, zeigt [Kapitel 3](03-den-code-beschaffen.md#33-installation-unter-php-80-bis-83).

`composer install` führt anschließend das Skript `legacy-scripts` aus (`post-install-cmd` und `post-update-cmd` in
`composer.json`), also `php bin/php/ezpgenerateautoloads.php`: Die Klassen-Maps werden für Sie erzeugt.

## 2.8 Webserver

| Möglichkeit | PHP | Wann |
|---|---|---|
| **Exponential Velocity** (Engine `qbix`) | 8.0+ | Empfohlen für jede Umgebung. Liefert HTTP und HTTPS selbst aus; kein weiterer Webserver nötig. |
| Apache mit `mod_rewrite` und PHP-FPM (oder mod_php) | 8.0+ | Die klassische Einrichtung; Shared Hosting. Die Rewrite-Regeln werden in `.htaccess_root` mitgeliefert. |
| nginx, LiteSpeed, Caddy und andere mit PHP-FPM | 8.0+ | Jeder Server, der eine URL auf `index.php` umschreiben kann; aufgeführt in der [README](../../../README.md#the-traditional-options). |
| FrankenPHP (über `exp:velocity`) | das der Programmdatei | Produktionsreife alternative Engine. |
| Der eingebaute Server von PHP (`exp:velocity start --engine=php`) | 8.0+ | Nur für die Entwicklung. |

Für eine Installation mit dem Kickstarter oder über die Konsole wird keiner benötigt; der Setup-Assistent braucht einen,
weil er im Browser läuft. Die Einrichtung der einzelnen Server ist Thema von [Kapitel 8](08-die-website-ausliefern.md) und der
[Anleitung zur Bereitstellung](../anleitungen/bereitstellung.md).

## 2.9 Hinweise zu Betriebssystemen

Exponential läuft überall, wo PHP läuft; die Installation besteht aus gewöhnlichen Dateien. Die folgenden Hinweise
behandeln die Systeme, die die Dokumentation und der Code nennen. Die Paketnamen sind diejenigen, die das
Extension-Manifest von Velocity (`vendor/se7enxweb/exponential-velocity/build/extensions.json`) angibt, also das, was
`exp:velocity ext install-hint` ausgibt.

### Red Hat Enterprise Linux 9, AlmaLinux 9, Rocky Linux 9

- Das Standard-PHP des AppStream-Repositorys ist **8.0**, mit dem Exponential unter Apache, PHP-FPM oder Velocity läuft. Der
  Befehl aus [PHP-8.0-Unterstützung](../bc/6.0/php-8.0-unterstuetzung.md):

  ```bash
  dnf install php php-fpm php-mysqlnd php-gd php-intl php-mbstring php-xml php-pdo php-opcache
  ```

  `php-xml` bringt `dom`, `simplexml`, `xsl` und `libxml` mit; `php-pdo` bringt `sqlite3`; `php-common` (eine
  Abhängigkeit) bringt `iconv`, `zlib`, `session` und `openssl`; `php-process` bringt `pcntl`; `php-pgsql` fügt
  PostgreSQL hinzu.
- Velocity braucht keinen neueren Stream: Es läuft ab seinem Release 0.0.4.54 auf dem Standard-PHP 8.0 (siehe
  [PHP 8.0 und Red Hat Enterprise Linux](#php-80-und-red-hat-enterprise-linux) weiter unten). Für ein neueres PHP
  wechseln Sie zu einem anderen AppStream-Modul-Stream: `dnf module list php`, dann `dnf module reset php`,
  `dnf module enable php:8.3` und `dnf distro-sync`. Mit den Streams `php:8.1`, `php:8.2` und `php:8.3` laufen sowohl
  Exponential als auch Velocity.
- Das Remi-Repository (Modul-Streams `php:remi-8.x` oder die parallelen Pakete `php<XY>` unter `/opt/remi`) stellt jede
  Version von 8.0 bis 8.5 bereit. PECL-Extensions (`php-pecl-redis`, `php-pecl-apcu`, `php-pecl-mongodb`) und `oci8`
  kommen aus EPEL, von Remi oder vom Anbieter.
- Der Dienst des Standard-PHP-FPM heißt `php-fpm`; bei einer parallelen Remi-Installation heißt er `php<XY>-php-fpm`.

### PHP 8.0 und Red Hat Enterprise Linux

Ab dem Release 0.0.4.54 (20. Oktober 2026) läuft Exponential Velocity unter PHP 8.0 bis 8.5: Die `composer.json` der
Engine verlangt `"php": ">=8.0"` (bisher `>=8.1`), und unter 8.0 ist nichts zusätzlich nötig, weder ein Polyfill-Paket
noch eine Erweiterung. Die `composer.json` von Exponential verlangt die Engine als `~0.0.4.57`, sodass
`composer install` sie auch unter PHP 8.0 installiert.

- **RHEL 9 und seine Nachbauten**: Das Standard-PHP 8.0 aus AppStream führt Velocity aus. Das el9-RPM
  (`exponential-velocity-<release>-1.el9.noarch.rpm`) hängt von `php-cli >= 8.0` ab und lässt sich installieren, ohne
  einen Modul-Stream zu aktivieren; ein neuerer Stream funktioniert weiterhin. Mit Composer geht es genauso.
- **RHEL 8**: Das Standard-PHP dort ist 7.2. Aktivieren Sie den Stream 8.0 (`dnf module enable php:8.0`) und verwenden
  Sie das Phar oder Composer; ein el8-RPM gibt es nicht.

Was sich unter PHP 8.0 unterscheidet, und nur das: Die Bildausgabe als AVIF braucht PHP 8.1 (die Formatauswahl hört bei
WebP auf); die Spitze des Speicherverbrauchs je Anfrage im Dashboard braucht PHP 8.2 (die Spalte bleibt leer); `$_FILES`
hat kein `full_path`, wie in PHP 8.0 selbst; und eine mit `eval()` deklarierte Klasse mit statischen Eigenschaften führt
dazu, dass ihr Worker nach dieser Anfrage ersetzt wird (das ist korrekt und kostet einen Fork). Alles andere erledigt
die Engine selbst, und ihre Zygote läuft unter 8.0. Erweiterungen, systemd, SELinux und wie Sie die Installation
prüfen: die Seite
[PHP 8.0 and Red Hat Enterprise Linux](https://github.com/se7enxweb/exponential-velocity/blob/main/docs/rhel.md) der
Engine und ihre [Anforderungen](https://github.com/se7enxweb/exponential-velocity/blob/main/docs/requirements.md).

### Debian und Ubuntu

- Die Pakete sind nach der PHP-Version benannt: `php<version>-<extension>`, zum Beispiel `php8.3-intl`. Die
  Extensions, die Exponential benötigt, stecken in `php8.3-xml` (`dom`, `simplexml`, `xsl`, `libxml`),
  `php8.3-mbstring`, `php8.3-intl`, `php8.3-common` (`iconv` und die anderen gängigen Extensions), `php8.3-sqlite3`,
  `php8.3-mysql` (`mysqli`), `php8.3-pgsql`, `php8.3-gd`, `php8.3-curl` und `php8.3-opcache`; `pcntl` steckt in
  `php8.3-cli`.
- Ersetzen Sie `8.3` durch die Version, die Ihre Distributionsversion mitliefert.
- Der PHP-FPM-Dienst heißt `php<version>-fpm`, mit seinen Pools in `/etc/php/<version>/fpm/pool.d/`.
- Das Velocity-Paket wird auch als `.deb` veröffentlicht; siehe
  [Velocity-Pakete und -Programmdateien](../funktionen/6.0/velocity-pakete-und-binaerdateien.md).

### Plesk

- Plesk installiert jede PHP-Version unter `/opt/plesk/php/<version>/` (zum Beispiel `/opt/plesk/php/8.5/bin/php`)
  neben dem PHP des Systems. Eine Domain verwendet die Version, die in ihren Hosting-Einstellungen gewählt ist; das `php`
  der Kommandozeile ist das des Systems. Führen Sie Composer und die Konsole mit dem PHP der Domain aus.
- Die mitgelieferten Extensions kommen mit dem PHP von Plesk und werden unter **Tools & Einstellungen >
  PHP-Einstellungen** eingeschaltet; PECL-Extensions werden mit `/opt/plesk/php/<version>/bin/pecl install <extension>`
  installiert.
- Jede Domain hat ihren eigenen PHP-FPM-Pool in `/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf`, betrieben vom
  Dienst `plesk-php<XY>-fpm` (zum Beispiel `plesk-php85-fpm`), nicht von `php-fpm`. Laden Sie nach einer Änderung an PHP
  diesen Dienst neu. `exp:velocity deploy` findet ihn selbst (`settings/velocity.ini`,
  `[DeploySettings] PhpFpmService=auto` sucht in `/opt/plesk/php/*/etc/php-fpm.d`, `/etc/php-fpm.d`,
  `/etc/php/*/fpm/pool.d` und `/etc/opt/remi/php*/php-fpm.d`).

### Andere Systeme

Das Velocity-Manifest kennt außerdem Alpine (`apk`), FreeBSD (`pkg`), macOS (Homebrew) und Windows. Die Setup-Tests des
Kernels haben Windows-Pfade für ImageMagick (`convertim.exe`, `convert.exe`), dieses Handbuch ist aber für Linux
geschrieben.

Exponential läuft auf fast jedem Betriebssystem mit PHP 8.0 oder neuer und einem Webserver. [Unterstützung von
Betriebssystemen](../hilfe/betriebssystemunterstuetzung.md) teilt sie in Stufen ein (hier getestet, bekanntermaßen
funktionierend, voraussichtlich funktionierend, derzeit nicht möglich), von den Linux-Distributionen, den BSDs, macOS
und Windows bis zu IBM i, Haiku und den 8-Bit-Rechnern, und führt die Webserver und PHP-SAPIs auf.

## 2.10 Arbeitsspeicher, CPU und Festplatte

Die Dokumentation nennt nur wenige feste Zahlen, und dieses Handbuch erfindet keine weiteren. Was sie angibt:

| Punkt | Wert | Quelle |
|---|---|---|
| PHP `memory_limit` | mindestens 64M | `settings/setup.ini`, `[memory_limit] MinMemoryLimit` |
| Ein Velocity-Worker | etwa 10 MB privater Speicher (gemessen) | [Velocity-Webserver](../funktionen/6.0/velocity-webserver.md) |
| Velocity-Pool | `Workers=4` ausgeliefert (`settings/velocity.ini`); der eigene Standard der Engine ist, was in den RAM passt, höchstens 8 pro Kern und 64, nie weniger als 4 | `settings/velocity.ini`, [Velocity-Webserver](../funktionen/6.0/velocity-webserver.md) |
| Eine MongoDB-Installation | RAM mindestens 512 MB, empfohlen 2 GB; Festplatte mindestens 2 GB, empfohlen 10 GB | [MongoDB-Unterstützung im Kernel](../bc/6.0/mongodb-kernel-unterstuetzung.md), Abschnitt 24.1 |

Der Platzbedarf auf der Festplatte wächst mit `var/storage` (hochgeladene Dateien und Bildvarianten), `var/cache` und
`var/log`; die Datenbank wächst mit den Inhalten. Messen Sie eine laufende Site, statt zu raten: Der Befehl
[Benchmark](../funktionen/6.0/benchmark.md) misst die Zeiten von Seiten, und das [Kontrollzentrum](../funktionen/6.0/velocity-control-panel.md)
von Velocity zeigt den Speicherverbrauch. Lesen Sie den Speicher von Velocity so, wie das Kontrollzentrum ihn zeigt, als
Proportional Set Size (PSS) des Elternprozesses und seiner Worker: Die Resident Set Size jedes geforkten Workers zählt
den gemeinsam genutzten Speicher immer wieder mit und summiert sich auf weit mehr, als der Rechner tatsächlich
verbraucht.

**Umgebungsvariablen.** Exponential liest einige Umgebungsvariablen, aufgeführt unter [Umgebungsvariablen](../anleitungen/umgebungsvariablen.md). Eine davon, `EXP_DEMO_MODE=1`, kennzeichnet eine Installation als öffentliche Demo ([Demo-Modus](../bc/6.0/demomodus.md)); ein Produktionsserver darf sie für keinen Prozess einer echten Site setzen.

## Quellen

In diesem Repository:

- [Installationsübersicht](../INSTALL.md), Abschnitt 2.
- [Datenbankunterstützung](../hilfe/datenbankunterstuetzung.md) und [Unterstützung von Betriebssystemen](../hilfe/betriebssystemunterstuetzung.md).
- [PHP-8.0-Unterstützung](../bc/6.0/php-8.0-unterstuetzung.md) und [PHP-8-Unterstützung](../bc/6.0/php8.md).
- [SQLite-Datenbankunterstützung](../funktionen/6.0/sqlite-datenbank.md),
  [SQLite-3-Treiber](../spezifikationen/6.0/sqlite3-datenbanktreiber.md),
  [MongoDB](../funktionen/6.0/mongodb-datenbankunterstuetzung.md),
  [Treiber für SQLite und Oracle](../spezifikationen/6.0/datenbanktreiber-sqlite-oracle.md),
  [Datenbanktreiber, September 2026](../spezifikationen/6.0/datenbanktreiber-2026-09.md).
- [Velocity-Engines](../bc/6.0/velocity-engines.md), [Velocity-Webserver](../funktionen/6.0/velocity-webserver.md),
  [Velocity-Pakete und -Programmdateien](../funktionen/6.0/velocity-pakete-und-binaerdateien.md).
- [Bereitstellung](../anleitungen/bereitstellung.md).
- [Qualitätsprüfungen](../funktionen/6.0/qualitaetspruefungen.md) und [Continuous Integration](../spezifikationen/6.0/kontinuierliche-integration.md):
  wie PHP 8.0 getestet wird.
- Code: `composer.json`, `settings/setup.ini`, `settings/site.ini` (`[SetupSettings]`), `kernel/setup/ezsetuptests.php`,
  `kernel/setup/ezsetupcommon.php`, `design/standard/templates/setup/tests/`,
  `vendor/se7enxweb/exponential-velocity/composer.json`.

Extern:

- PHP: [unterstützte Versionen](https://www.php.net/supported-versions.php),
  [Installation und Konfiguration](https://www.php.net/manual/en/install.php),
  [php.ini-Direktiven](https://www.php.net/manual/en/ini.list.php),
  [Datei-Uploads](https://www.php.net/manual/en/ini.core.php#ini.file-uploads),
  [Liste der unterstützten Zeitzonen](https://www.php.net/manual/en/timezones.php),
  [Migration auf PHP 8.0](https://www.php.net/manual/en/migration80.php).
- PHP-Extensions: [DOM](https://www.php.net/manual/en/book.dom.php), [XSL](https://www.php.net/manual/en/book.xsl.php),
  [intl](https://www.php.net/manual/en/book.intl.php), [mbstring](https://www.php.net/manual/en/book.mbstring.php),
  [SQLite3](https://www.php.net/manual/en/book.sqlite3.php), [mysqli](https://www.php.net/manual/en/book.mysqli.php),
  [PostgreSQL](https://www.php.net/manual/en/book.pgsql.php), [GD](https://www.php.net/manual/en/book.image.php),
  [MongoDB](https://www.php.net/manual/en/book.mongodb.php), [OCI8](https://www.php.net/manual/en/book.oci8.php),
  [Sockets](https://www.php.net/manual/en/book.sockets.php), [PCNTL](https://www.php.net/manual/en/book.pcntl.php).
- Composer: [Download](https://getcomposer.org/download/),
  [allow-plugins](https://getcomposer.org/doc/06-config.md#allow-plugins).
- PHPUnit: [unterstützte Versionen](https://phpunit.de/supported-versions.html).
- Velocity auf Packagist: [se7enxweb/exponential-velocity](https://packagist.org/packages/se7enxweb/exponential-velocity).
- Remi-Repository: [rpms.remirepo.net](https://rpms.remirepo.net/).

[Inhalt](README.md) · Zurück: [1. Einführung](01-einfuehrung.md) · Weiter: [3. Den Code beschaffen](03-den-code-beschaffen.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
