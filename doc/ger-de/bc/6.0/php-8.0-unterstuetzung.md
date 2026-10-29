# Unterstützung für PHP 8.0

Exponential 6 läuft unter PHP 8.0 und neuer. PHP 8.0 ist die älteste unterstützte Version, weil es das
Standard-PHP von Red Hat Enterprise Linux 9 ist (und von AlmaLinux 9 und Rocky Linux 9). Lesen Sie diese Seite, wenn
Sie Exponential unter RHEL 9 ohne ein neueres PHP betreiben, wenn Sie Code für den Kernel oder für Erweiterungen
schreiben oder wenn Sie sich fragen, warum eine Funktion, die neuer als 8.0 ist, in `function_exists()` eingeschlossen
ist.

## Kurz gesagt

| | |
|---|---|
| Ältestes PHP | 8.0 für den Kernel und die mitgelieferten Erweiterungen, unter Apache mit mod_php, PHP-FPM oder Velocity. |
| Velocity | Exponential Velocity, der Anwendungsserver, läuft ab seinem Release 0.0.4.54 ebenfalls unter PHP 8.0 (bis dahin brauchte er 8.1; siehe unten). |
| Was sich in 6.0.15 geändert hat | Die wenigen Stellen, die PHP 8.1 oder neuer brauchten, funktionieren jetzt unter 8.0; die CI prüft 8.0 bei jedem Push. |
| Verhalten unter 8.1 und neuer | Unverändert. Jeder Ersatz wird nur dort verwendet, wo PHP die Funktion fehlt. |
| Composer | `composer.json` deklariert `^8.0` und verlangt Velocity in `~0.0.4.57`; ein einfaches `composer install` funktioniert unter 8.0 und installiert die Engine mit. |

## Was gefunden und wie es behoben wurde

Der ganze Baum wurde mit PHPCompatibility für PHP 8.0 geprüft (`testVersion` 8.0), jede PHP-Datei mit PHP 8.0.30
gelintet, jede Klasse des Kernels und von 63 Erweiterungen mit PHP 8.0.30 geladen, und eine vollständige Installation
wurde von PHP 8.0.30 ausgeliefert: die Startseite, 35 öffentliche Seiten, die Administrationsoberfläche angemeldet
(Dashboard, Inhaltsansichten, Setup, die Seiten für Benachrichtigungen und E-Mail-Einstellungen, der Layout-Editor) und
der Siteaccess editor, ohne PHP-Fehler, Warnung oder Deprecation. Die Befunde:

| Wo | Was | PHP | Lösung |
|---|---|---|---|
| `kernel/classes/expvelocityconfiglayout.php` | `array_is_list()` | 8.1 | in `lib/phpcompat.php` definiert, wenn es fehlt |
| `kernel/classes/subitems/expsubitemscsvexport.php` | `fputcsv()` mit `$eol` (ein `ArgumentCountError` unter 8.0) | 8.1 | unter 8.0 wird die Zeile ohne `$eol` geschrieben und ihr `"\n"` durch `"\r\n"` ersetzt |
| `lib/eztemplate/classes/eztemplatestringoperator.php` (`wash`), `kernel/common/ezsimpletagsoperator.php` und 26 weitere Dateien aus Kernel, Bibliotheken, ezjscore, ezoe und expservices | `htmlspecialchars()`, `htmlentities()`, `html_entity_decode()` und `htmlspecialchars_decode()` ohne `$flags` | 8.1 hat den Standard geändert | der Standard von PHP 8.1, `ENT_QUOTES \| ENT_SUBSTITUTE \| ENT_HTML401`, wird ausdrücklich übergeben, sodass einfache Anführungszeichen auch unter 8.0 escapt werden |
| `kernel/classes/contentjob/*`, `kernel/classes/ini/expinieditor.php` | `fsync()` | 8.1 | bereits hinter `function_exists()`; unter 8.0 werden die Daten geleert (`fflush()`) und dem Betriebssystem überlassen |
| `kernel/classes/contentjob/expcontentjobworker.php` | `memory_reset_peak_usage()` | 8.2 | bereits hinter `function_exists()` |
| `lib/eztemplate/classes/eztemplatestringsoperator.php` | `str_increment()`, `str_decrement()` | 8.3 | bereits hinter `function_exists()`; unter älterem PHP geben die Operatoren ihre Eingabe zurück |
| `lib/ezdb/classes/ezdbquerycache.php` | `hash( 'xxh128' )` | 8.1 | prüft bereits `hash_algos()`; md5 unter 8.0 |

Die Änderung an `htmlspecialchars()` ist diejenige, die für die Sicherheit zählt: Der Standard von PHP 8.0 lässt `'`
unverändert, daher wäre ein `{$value|wash}` innerhalb eines HTML-Attributs in einfachen Anführungszeichen unter 8.0
nicht sicher gewesen. Die Übergabe der Flags gibt jeder PHP-Version dieselbe Ausgabe, die 8.1 und neuer bereits
erzeugten. Haben Sie kompilierte Templates von vor der Änderung, leeren Sie den Template-Cache einmal
(`php bin/php/ezcache.php --clear-id=template`).

Unterwegs gefunden und unter jedem PHP 8 defekt, nicht nur unter 8.0 (in derselben Änderung oder im Repository der
Erweiterung behoben):

| Wo | Was | Lösung |
|---|---|---|
| `kernel/private/modules/oauth/authorize.php` | `implode( $pieces, $glue )` (ein `TypeError` seit 8.0) | Argumente vertauscht |
| `kernel/classes/datatypes/ezuser/ezldapuser.php` | `crypt()` ohne Salt (ein `ArgumentCountError` seit 8.0) | ein zufälliges Passwort aus `random_bytes()` |
| syndication: `eZSyndicateType::onPublish()` | Referenzparameter gegenüber dem Wertparameter der Elternklasse (ein fataler Fehler beim Laden der Klasse) | `&` entfernt |
| ezstarrating: `ezsrRatingObjectTreeNode::makeObjectsArray()` | ihr fehlen die Parameter, die die Methode des Kernels hat (ein fataler Fehler beim Laden der Klasse) | Parameter hinzugefügt |
| ezstarrating `ezsrratingobject.php`, swark `SwarkJSONEncodeOperator`, `SwarkVariableNamesOperator` | `implode( $pieces, $glue )` | Argumente vertauscht |
| birthday `ezbirthday.php` | `each()`, in 8.0 entfernt | `key()` und `current()` |
| enhancedezbinaryfile | `split()`, in 7.0 entfernt | `explode()` |

Nicht geändert, weil sie sich unter 8.0 genauso verhalten wie unter 8.5: optionale Parameter vor erforderlichen (seit
8.0 eine Deprecation in einigen alten Signaturen), der Fehlermodus von mysqli (8.0 wirft standardmäßig keine
Ausnahmen, und der Treiber prüft Rückgabewerte, was er auch erwartet), `never` als Rückgabetyp von Closures in
xrowextract (8.0 liest es als Klassennamen; die Closures beenden immer, daher wird es nie geprüft).

## Wie die Kompatibilitätsfunktionen arbeiten

`lib/phpcompat.php` definiert eine Funktion nur, wenn PHP sie nicht hat, mit demselben Ergebnis wie die eigene von
PHP:

```php
if ( !function_exists( 'array_is_list' ) )
{
    function array_is_list( array $array ) { ... }
}
```

`autoload.php` liest die Datei vor allem anderen, und nur unter PHP älter als 8.1 (`PHP_VERSION_ID < 80100`), sodass
die Datei unter 8.1 und neuer gar nicht erst geöffnet wird und die eingebauten Funktionen laufen. Jeder Einstiegspunkt
(index.php, die CLI-Skripte, Cronjobs, die Tests) beginnt mit `autoload.php`, daher haben alle die Funktionen.

Regeln für neuen Code:

- Eine Funktion, die neuer als PHP 8.0 ist und sich mit demselben Ergebnis in PHP schreiben lässt, kommt in
  `lib/phpcompat.php`.
- Eine, bei der das nicht geht (`fsync()`, `memory_reset_peak_usage()`, `imageavif()` ...), wird hinter
  `function_exists()` aufgerufen, mit einem Ersatz, der unter 8.0 korrekt ist.
- Keine Syntax, die neuer als 8.0 ist: Enums, `readonly`, `never`, `new` in Initialisierern, die First-Class-Callable-
  Syntax `foo(...)`, Intersection-Typen, typisierte Klassenkonstanten. `#[\Override]` und andere Attribute sind
  harmlos.
- Ein Argument, das eine Funktion später erhalten hat (`$eol` von `fputcsv()` in 8.1), ist unter 8.0 ein fataler
  Fehler: Verzweigen Sie anhand von `PHP_VERSION_ID`.
- `phpcs.xml.dist` führt PHPCompatibility mit `testVersion` `8.0-` aus, und der Job `php80` des Quality-Workflows
  lintet jede Datei und startet den Kernel unter PHP 8.0 ([Qualitätsprüfungen](../../funktionen/6.0/qualitaetspruefungen.md)).

## Exponential Velocity unter PHP 8.0

Ab dem Release 0.0.4.54 (20. Oktober 2026) läuft Velocity (`vendor/se7enxweb/exponential-velocity`) unter PHP 8.0 bis
8.5: Seine `composer.json` verlangt `"php": ">=8.0"` (bisher `>=8.1`). Unter 8.0 wird nichts zusätzlich gebraucht,
kein Polyfill-Paket und keine Erweiterung. Bis 0.0.4.54 gab die Engine 8.1 als Minimum an, daher musste eine Website
unter PHP 8.0 von Apache oder PHP-FPM ausgeliefert werden; diese Grenze ist weg. Also:

- Apache oder PHP-FPM liefern die Website aus: PHP 8.0 oder neuer.
- Velocity: PHP 8.0 oder neuer, einschließlich des Standard-PHP 8.0 von RHEL 9.

Was sich unter PHP 8.0 unterscheidet, und nur das:

- Die Bildausgabe in AVIF braucht PHP 8.1; unter 8.0 endet die Aushandlung der Bilder bei WebP.
- Der Speicherhöchstwert pro Anfrage im Dashboard braucht PHP 8.2; unter 8.0 ist die Spalte leer.
- `$_FILES` hat kein `full_path`, wie in PHP 8.0 selbst.
- Eine mit `eval()` deklarierte Klasse mit statischen Eigenschaften führt dazu, dass ihr Worker nach dieser Anfrage
  ersetzt wird; das ist korrekt und kostet einen Fork.

Die anderen Unterschiede erledigt die Engine selbst: Sie liest deklarierte statische Standardwerte aus dem Quelltext
(das `getDefaultValue()` von PHP 8.0 gibt den aktuellen Wert zurück), ruft unter 8.0 `setAccessible()` für private und
geschützte statische Eigenschaften auf und leert den Stat-Cache für das Log-Ende im Dashboard. Die Zygote läuft unter
8.0. Erweiterungen, systemd, SELinux und wie man eine Installation überprüft, stehen auf der Seite
[PHP 8.0 and Red Hat Enterprise Linux](https://github.com/se7enxweb/exponential-velocity/blob/main/docs/rhel.md) der
Engine; die Mindestanforderungen der Engine stehen in ihren
[Anforderungen](https://github.com/se7enxweb/exponential-velocity/blob/main/docs/requirements.md) und das Release in
ihrem [Changelog](https://github.com/se7enxweb/exponential-velocity/blob/main/CHANGELOG.md).

## Red Hat Enterprise Linux 9

- **Standard-PHP 8.0**: `dnf install php php-fpm php-mysqlnd php-gd php-intl php-mbstring php-xml php-pdo php-opcache`
  installiert PHP 8.0 aus AppStream. Das genügt für Exponential unter Apache, PHP-FPM oder Velocity. Das el9-rpm von
  Velocity (`exponential-velocity-<release>-1.el9.noarch.rpm`) hängt von `php-cli >= 8.0` ab und lässt sich darauf
  installieren, ohne einen Modul-Stream zu aktivieren.
- **Neueres PHP aus AppStream-Modulen**: RHEL 9 bietet außerdem die Modul-Streams `php:8.1`, `php:8.2` und `php:8.3`
  an (`dnf module list php`; wechseln Sie mit `dnf module reset php` und `dnf module enable php:8.3`, dann
  `dnf distro-sync`). Jeder davon führt Exponential und Velocity ebenfalls aus; für Velocity wird keiner gebraucht.
- **RHEL 8**: Das Standard-PHP dort ist 7.2. Aktivieren Sie zuerst den Stream 8.0 (`dnf module enable php:8.0`) und
  installieren Sie Velocity aus dem Phar oder mit Composer; ein el8-rpm gibt es nicht.
- **Remi oder Plesk**: Das Repository von Remi (Modul-Streams `php:remi-8.x`) und die Builds von Plesk unter
  `/opt/plesk/php/8.x` stellen jede Version von 8.0 bis 8.5 nebeneinander bereit.
- Erweiterungen, die die Standardpakete auslassen (`php-pecl-redis`, `php-pecl-apcu`, `oci8`), kommen aus EPEL, von
  Remi oder vom Hersteller; Exponential verwendet sie nur, wenn ihre Funktionen konfiguriert sind.

## Mit Composer unter PHP 8.0 installieren

`composer.json` deklariert seit 6.0.15 `"php": "^8.0 || ^8.1 || ..."`. Die Erweiterungen, die in ihren Metadaten nur
`^8.1` angaben, wurden mit `^8.0` veröffentlicht (explayouts und seine Pakete, die expsite-Pakete,
exp_enhanced_link, expchangeclass, expquery-translator, sevenx-themes-media 1.5.32,
`exponential-legacy-installer` 2.2.4), und syndication 1.3.5, ezstarrating 6.0.9, swark 1.0.5, birthday 1.3.3 und
enhancedezbinaryfile 4.4.5 wurden mit Korrekturen für Code veröffentlicht, den kein PHP 8 laden konnte.

Velocity wird wieder verlangt, in `~0.0.4.57` (0.0.4.54 war das erste Release, das unter PHP 8.0 läuft). Zwischen seiner
Verschiebung nach `"suggest"` (weil es PHP 8.1 brauchte) und diesem Release musste es von Hand hinzugefügt werden;
eine in dieser Zeit erstellte Installation fügt es so hinzu:

```sh
composer require se7enxweb/exponential-velocity:~0.0.4.57
```

| Paket | Deklariert | Blockiert 8.0 |
|---|---|---|
| `google/recaptcha` (über recaptcha) | 2.1.0 braucht `>=8.4` | nein: Die Anforderung ist `*`, daher wählt Composer unter 8.0 1.3.x |

Jedes Paket, das `composer.json` verlangt, erlaubt PHP 8.0, daher funktioniert ein einfaches
`composer install --no-dev` mit dem Standard-PHP von RHEL 9. Der CI-Job `php80` installiert genau so, daher schlägt
dort ein Paket fehl, das sein Minimum wieder anhebt.
