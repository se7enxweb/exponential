# 12. Fehlerbehebung

Dieses Kapitel ist ein Nachschlagewerk für Probleme während und nach einer Installation. Es beginnt mit der
allgemeinen Vorgehensweise (das Log lesen, die Debug-Ausgabe einschalten, immer nur eine Sache ändern), erklärt, wo
sich jedes Log befindet und wie Sie die Debug-Ausgabe gefahrlos einschalten, behandelt Berechtigungen und
Eigentümerschaft und listet dann Symptome nach Bereichen auf: Beschaffen des Codes, die Systemprüfung, der
Setup-Assistent, der Kickstarter und die Installation über die Konsole, Seiten zur Laufzeit, Datenbanken, Velocity und
der Webserver, Caches sowie das Anmelden. Jede hier zitierte Meldung ist aus dem Code übernommen, der sie ausgibt, und
die zugehörige Datei ist genannt, sodass Sie nach genau dem Text suchen können, den Sie sehen. Jede Zeile nennt die
Ursache und die Abhilfe.

[Inhalt](README.md) · Zurück: [11. Upgrade](11-aktualisieren.md) · Weiter: [13. Sicherheitshärtung für den Produktivbetrieb](13-sicherheitshaertung.md)

## 12.1 Hier beginnen

1. **Lesen Sie die Meldung genau.** Die meisten Fehler in diesem Kapitel sind wörtlich zitiert; suchen Sie auf dieser
   Seite nach ein paar Wörtern Ihrer Meldung.
2. **Lesen Sie das Log.** Zuerst `var/log/error.log`; die Dateien sind in [12.2](#122-wo-die-logs-liegen) beschrieben.
   Die Fehlerseite, die ein Besucher sieht, zeigt eine Referenz wie `ERR-1A2B3C4D5E`; dieselbe Referenz steht in
   `error.log` neben den Details.
3. **Schalten Sie die Debug-Ausgabe nur für sich selbst ein** ([12.4](#124-debug-ausgabe)) und laden Sie die Seite neu.
4. **Ändern Sie eine Sache und leeren Sie dann den kleinsten Cache, der diese Änderung abdeckt**
   ([12.12](#1212-caches-und-änderungen-die-nicht-sichtbar-werden)). Unter Velocity braucht eine geänderte Klasse oder Einstellung
   außerdem `exp:velocity restart` oder `exp:velocity deploy`.
5. **Arbeiten Sie auf der Kommandozeile als Eigentümer der Installation.** Ein Skript, das als `root` läuft, legt
   Dateien an, die der Webserver später nicht ändern kann ([12.3](#123-berechtigungen-und-eigentümerschaft)).

## 12.2 Wo die Logs liegen

Alle Pfade sind relativ zum Wurzelverzeichnis der Installation. Eine Logdatei wird rotiert, wenn sie 200 KB
überschreitet, und drei ältere Kopien werden aufbewahrt (`.1` bis `.3`). Zwei Konstantenpaare in `config.php` ändern
das (auskommentierte Beispiele in `config.php-RECOMMENDED`): `EZPUBLISH_LOG_MAX_FILE_SIZE` und
`EZPUBLISH_LOG_ROTATE_FILES` für die Dateien, die `eZDebug` schreibt (`error.log`, `warning.log`, `notice.log`,
`debug.log`, `strict.log`; `eZDebug::MAX_LOGFILE_SIZE = 204800`, `MAX_LOGROTATE_FILES = 3`), sowie
`CUSTOM_LOG_MAX_FILE_SIZE` und `CUSTOM_LOG_ROTATE_FILES` für die Dateien, die über `eZLog` geschrieben werden
(`storage.log`, `async.log`, `requestrules.log` und die Zeilen „Unexpected error“). Der Wert `0` für die Anzahl der
Rotationsdateien schaltet die eingebaute Rotation ab. Warum das bei der Suche wichtig ist: Ein Fehler von gestern kann
bereits in `error.log.1` oder `.2` stehen, durchsuchen Sie deshalb alle Dateien
(`grep -h ERR-1A2B3C4D5E var/log/error.log*`).

| Datei | Geschrieben von | Inhalt |
|---|---|---|
| `var/log/error.log` | `eZDebug` (Fehler werden immer protokolliert: `[DebugSettings] AlwaysLog[]=error`), der Handler für nicht abgefangene Fehler | Fehler; „Unexpected error ERR-..., the message was : ...“ mit Datei und Zeile für jede Fehlerseite, die ein Besucher gesehen hat |
| `var/log/warning.log`, `notice.log`, `debug.log`, `strict.log` | `eZDebug` | Warnungen, Hinweise, Debug- und Strict-Meldungen, wenn ihr Typ in `AlwaysLog[]` steht oder die Debug-Ausgabe eingeschaltet ist |
| `var/log/setup.log` | der Setup-Assistent, der Kickstarter, `exp:install` | jeder Schritt einer Installation, einschließlich fehlgeschlagener Datenbankverbindungen |
| `var/log/kickstart.log` | der Kickstarter (`kickstart.log.1` bis `.9` bewahren frühere Läufe auf) | jeder Kickstarter-Lauf, Passwörter maskiert; `EXP_KICKSTART_LOG=0` schaltet es ab |
| `var/log/exp-install-<date>.ini` | `exp:install` | die Konfiguration, die eine Installation über die Konsole verwendet hat, Passwörter maskiert |
| `var/log/initial-admin-password` | die Installationsprogramme | das erzeugte Administratorpasswort, nur für den Eigentümer lesbar; löschen Sie die Datei nach der ersten Anmeldung |
| `var/log/storage.log` | die Dateiablage | Dateien, die nach `var/storage` geschrieben wurden (unter `[FileSettings] VarDir`/`LogDir`) |
| `var/log/async.log` | asynchrones Veröffentlichen | die Veröffentlichungswarteschlange |
| `var/log/requestrules.log` | Anfrageregeln | abgewiesene und protokollierte Anfragen |
| `var/log/audit/` | das Audit-Protokoll (`settings/audit.ini`, `[AuditSettings] LogDir=log/audit`, relativ zu `[FileSettings] VarDir`) | hashverkettete Audit-Dateien; siehe [Audit](../bc/6.0/audit.md) |
| `var/vc/qbix/log/` | Velocity (`settings/velocity.ini`, `[LogSettings] Dir`) | `<siteaccess>-access.log` und `<siteaccess>-error.log`, benannt nach `[SiteSettings] DefaultAccess`; beim Rotieren mit Datum versehen |
| `var/vc/qbix/run/console.log` | Velocity (`[ServerSettings] LogFile`) | was der Serverprozess ausgibt: Start, Aufwärmphase und Fehler beim Starten |
| `var/vc/frankenphp/log/` | die FrankenPHP-Engine | ihre `access.log` und `error.log` |
| die eigenen Logs des Webservers und von PHP-FPM | Apache, nginx, PHP-FPM | fatale PHP-Fehler, die auftreten, bevor Exponential sie protokollieren kann |

Vor September 2026 protokollierte Velocity nach `var/log/qbix/`; eine Installation, deren `[LogSettings] Dir` diesen
Ort nennt, behält ihn bei.

Die Fehler eines Shell-Skripts erscheinen im Terminal. Kommandozeilenskripte nehmen `-d` (oder `--debug`) an, um die
Debug-Ausgabe am Ende des Laufs auszugeben (`-d all` oder einer der Werte `accumulator`, `include`, `timing`, `error`,
`warning`, `debug`, `notice`, `strict`), und `-v` für zusätzliche Informationen.

## 12.3 Berechtigungen und Eigentümerschaft

Der Benutzer, unter dem der Webserver oder die Worker von Velocity laufen, muss `design`, `extension`, `settings`,
`settings/override`, `settings/siteaccess` und `var` mit allem darunter beschreiben können. Der Setup-Assistent prüft
die Liste in `settings/setup.ini`, `[directory_permissions] CheckList`, und zeigt, wenn die Prüfung fehlschlägt,
„Unzureichende Verzeichnisberechtigungen“ mit Befehlen für Ihren Rechner an, eingeleitet mit „Diese Shell-Kommandos
werden dem Webserver die nötigen Rechte geben.“ Sie haben diese Form:

```bash
sudo chmod -R ug+rwx design extension settings var
sudo chown -R <web user>:<web group> design extension settings var
```

| Symptom | Ursache | Abhilfe |
|---|---|---|
| Die Systemprüfung schlägt bei den Verzeichnisberechtigungen fehl | Der Benutzer des Webservers kann eines der Verzeichnisse nicht beschreiben | Führen Sie die Befehle aus, die die Seite ausgibt, und klicken Sie dann auf **Weiter**. |
| Die Seiten funktionierten, dann treten nach einem Cronjob oder einem Konsolenbefehl Fehler auf | Ein als `root` (mit `--allow-root-user`) ausgeführtes Skript hat Cache- oder Ablagedateien angelegt, die root gehören | `chown -R <web user>:<web group> var`; führen Sie Skripte als Eigentümer der Installation aus. |
| Apache/PHP-FPM, die Velocity-Worker und die Cronjobs laufen unter verschiedenen Benutzern, und jeder beschädigt die Dateien der anderen | Dateien erhalten die umask des Prozesses, der sie anlegt | Geben Sie ihnen eine gemeinsame Gruppe, die `var/` (und `settings/override/`, wenn Einstellungen in der Administration geändert werden) lesen und schreiben kann. |
| Das Skript bricht mit „Running scripts as root may be dangerous.“ ab | `eZScript` verweigert `root` (`kernel/classes/ezscript.php`) | Führen Sie es als Eigentümer aus, oder hängen Sie `--allow-root-user` an. |
| SQLite: „Das Verzeichnis %directory kann vom Webserver (Benutzer %user) nicht beschrieben werden. SQLite muss dort die Datenbankdatei anlegen sowie die -wal- und -shm-Dateien, die es daneben führt.“ | Das Datenbankverzeichnis ist nicht beschreibbar | Geben Sie diesem Benutzer Schreibrechte für `var/storage/sqlite3/` (legen Sie es bei Bedarf zuerst an). |
| SQLite: „Die Datenbankdatei %file existiert, kann aber vom Webserver (Benutzer %user) nicht beschrieben werden.“ | Die Datei gehört einem anderen Benutzer | Korrigieren Sie Eigentümer oder Modus der Datei, oder wählen Sie einen anderen Dateinamen. |
| Velocity-Worker können nicht schreiben | Die Worker laufen als `[ServerSettings] User` und `Group` aus `settings/velocity.ini`; wenn diese leer sind, als `VC_RUN_USER`/`VC_RUN_GROUP` oder als Eigentümer des Document Root | Setzen Sie `User`/`Group`, oder korrigieren Sie die Eigentümerschaft. Worker laufen nie als root, außer bei `AllowRootWorkers=enabled`. |

Die Platzhalter `%directory`, `%file` und `%user` werden auf der Seite ausgefüllt
(`kernel/setup/steps/ezstep_installer.php`).

## 12.4 Debug-Ausgabe

Die Debug-Ausgabe zeigt Warnungen, Fehler, Zeitmessungen, SQL und die verwendeten Templates am Ende jeder Seite an (seit
dem 2. Oktober 2026 als angeheftete [Exp Debug bar](../funktionen/6.0/exp-debug-leiste.md)). Sie ist standardmäßig
ausgeschaltet (`settings/site.ini`, `[DebugSettings] DebugOutput=disabled`). Schalten Sie sie **nur für Ihre eigene
Adresse** ein, in `settings/override/site.ini.append.php`:

```ini
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]=203.0.113.10
```

Leeren Sie dann den INI-Cache und laden Sie die Seite neu:

```bash
php bin/php/ezcache.php --clear-tag=ini
```

Unter Velocity starten Sie ihn zusätzlich neu (`php bin/php/console exp:velocity restart`), weil die Worker die
Einstellungen im Speicher halten. Dasselbe lässt sich mit [exp:ini](../funktionen/6.0/exp-ini-befehl.md) erreichen, das
den Override schreibt und protokolliert:

```bash
php bin/php/console exp:ini set site.ini/DebugSettings/DebugOutput enabled global
```

Die Schalter (`settings/site.ini`, `[DebugSettings]`, sofern nicht anders angegeben):

| Einstellung | Standard | Wirkung |
|---|---|---|
| `DebugOutput` | `disabled` | der Hauptschalter |
| `DebugByIP`, `DebugIPList[]` | `disabled` | Debug nur diesen Adressen oder Netzen anzeigen (`192.0.0.0/27`) |
| `DebugByUser`, `DebugUserIDList[]` | `disabled` | Debug nur diesen Benutzer-IDs anzeigen |
| `Debug` | `inline` | `inline` in der Seite, oder `popup` |
| `DebugRedirection` | `disabled` | bei Weiterleitungen mit einer Seite anhalten, die die Debug-Ausgabe zeigt |
| `DisplayDebugWarnings` | `disabled` | Warnungen und Fehler in der Seite anzeigen, nicht nur in der Debug-Ausgabe |
| `AlwaysLog[]` | `error` | welche Typen auch bei ausgeschalteter Debug-Ausgabe nach `var/log/` geschrieben werden |
| `ScriptDebugOutput` | `disabled` | Debug-Ausgabe für Kommandozeilenskripte (mit `DebugOutput=enabled`) |
| `DebugToolbar` | `enabled` | die Entwickler-Werkzeugleiste (Caches leeren, Schnelleinstellungen) für Administratoren |
| `[TemplateSettings] Debug`, `ShowUsedTemplates`, `ShowXHTMLCode` | | Template-Debug, die Liste der verwendeten Templates, Template-Namen inline im HTML |
| `[DatabaseSettings] SQLOutput` | `disabled` | jede SQL-Abfrage in der Debug-Ausgabe |

**Lassen Sie die Debug-Ausgabe auf einer öffentlichen Site nie für alle eingeschaltet.** Sie zeigt Pfade, Abfragen und
Einstellungen. Bei mehreren Rechnern legen Sie die Debug-Einstellungen nur in die Einstellungen des
Entwicklungsrechners; siehe [Einstellungen je Umgebung (EXP_ENV)](../funktionen/6.0/umgebungseinstellungen.md).

## 12.5 Den Code beschaffen und Composer

| Symptom | Ursache | Abhilfe |
|---|---|---|
| Composer: Die PHP-Version erfüllt `phpunit/phpunit` nicht | Die Tags `v6.0.13` und `v6.0.14` legen in `require-dev` PHPUnit `13.0.0` fest, das PHP 8.4.1 oder neuer braucht. Ohne Lock-Datei löst Composer `require-dev` auch mit `--no-dev` auf | Das aktuelle `composer.json` (Linie 6.0.15, `dev-main`) verlangt `^9.6 \|\| ^10.5 \|\| ^11.5 \|\| ^12.0 \|\| ^13.4`, sodass Composer das PHPUnit wählt, das zum verwendeten PHP passt (9.6 auf PHP 8.0, 13.4 auf 8.4 und 8.5), und ein einfaches `composer install --no-dev` unter PHP 8.0 bis 8.5 funktioniert; nichts muss entfernt werden. Nur für `v6.0.13` oder `v6.0.14` unter PHP 8.1 bis 8.3: zuerst `composer remove --dev --no-update --no-interaction phpunit/phpunit zetacomponents/php-generator`. Die Testsuite selbst braucht PHPUnit 10, also PHP 8.1. |
| Composer: Das Root-Paket verlangt unter PHP 8.0 PHP 8.1 | Die Releases `v6.0.8` bis `v6.0.14` verlangen PHP 8.1 | Verwenden Sie PHP 8.1 oder neuer, oder die Linie 6.0.15 (`dev-main`), die 8.0 akzeptiert. |
| Composer: `se7enxweb/exponential-velocity` verlangt PHP 8.1 | Ein Velocity-Release älter als 0.0.4.54 wird verlangt; diese brauchten PHP 8.1 | Verlangen Sie `~0.0.4.54` oder später (`composer require se7enxweb/exponential-velocity:~0.0.4.54`), das unter PHP 8.0 läuft. |
| Composer bricht wegen einer fehlenden `ext-mongodb` ab | Ein Paket, das die PHP-Erweiterung `mongodb` braucht, wurde hinzugefügt, meist `mongodb/mongodb` für den MongoDB-Datenbank-Handler (Exponential schlägt es nur vor; kein erforderliches Paket braucht die Erweiterung) | Installieren Sie die Erweiterung für das PHP, mit dem Composer läuft, und für das PHP des Webservers. Wenn Sie MongoDB nicht verwenden, entfernen Sie das Paket wieder (`composer remove mongodb/mongodb`). |
| Composer fragt, ob es `se7enxweb/exponential-legacy-installer` vertrauen soll, oder weigert sich, es auszuführen | Composer 2.2+ führt nur erlaubte Plugins aus; Ihr eigenes `composer.json` (Weg C aus Kapitel 3) führt es nicht auf | Antworten Sie mit Ja, oder `composer config allow-plugins.se7enxweb/exponential-legacy-installer true`. |
| Nach `composer require se7enxweb/exponential` liegen die Kernel-Dateien im aktuellen Verzeichnis | So installiert das Installer-Plugin ein Paket vom Typ `ezpublish-legacy`: in das Wurzelverzeichnis des Projekts | Erwartet; siehe [Kapitel 3](03-den-code-beschaffen.md#32-drei-wege-zum-code). |
| `Class ... not found`, nachdem Composer eine Extension hinzugefügt hat | Die Klassenverzeichnisse wurden nicht neu erzeugt (Composer führt nur die Skripte des Root-Pakets aus) | `php bin/php/ezpgenerateautoloads.php -e`, dann die Caches leeren. |
| Eine Extension liegt in `extension/`, hat aber keine Wirkung | Dass sie auf der Festplatte liegt, aktiviert sie nicht | Tragen Sie sie in `[ExtensionSettings] ActiveExtensions[]` ein (oder unter **Setup > Extensions**), dann `php bin/php/ezcache.php --clear-tag=ini`. |

## 12.6 Die Systemprüfung des Setup-Assistenten

Die Überschriften unten sind die von `design/standard/templates/setup/tests/<test>_error.tpl`; die Prüfungen sind in
[Kapitel 2](02-voraussetzungen.md#24-der-systemtest-des-setup-assistenten) beschrieben.

| Meldung | Ursache | Abhilfe |
|---|---|---|
| „Unzureichende Verzeichnisberechtigungen“ | der Webserver kann ein Verzeichnis aus `CheckList` nicht beschreiben | die Befehle auf der Seite ([12.3](#123-berechtigungen-und-eigentümerschaft)) |
| „Fehlender Datenbank-Handler“ („No supported database handlers were found. ...“) | keine der Erweiterungen `sqlite3`, `mysqli`, `pgsql`, `mongodb`, `oci8` ist geladen | installieren Sie eine; `sqlite3` braucht keinen Server |
| „Fehlende Unterstützung für die Bildkonvertierung“ | weder `imagegd2()` von GD noch `convert` von ImageMagick | installieren Sie `gd` oder ImageMagick |
| „Es steht nicht genug Arbeitsspeicher für die Installation von Exponential zur Verfügung“ („Exponential wird mit einem Speicherlimit von %1 nicht korrekt arbeiten.“) | `memory_limit` unter `64M` | erhöhen Sie es in der `php.ini`, die der Webserver liest, und laden Sie PHP dann neu |
| „Die erlaubte Ausführungszeit (Execution Time) ist zu kurz, um Exponential zu installieren“ | `max_execution_time` unter 30 | erhöhen Sie den Wert und laden Sie PHP dann neu |
| „allow_url_fopen INI Einstellung wurde deaktiviert“ | `allow_url_fopen=Off` | schalten Sie es ein |
| „Datei Upload ist deaktiviert“ | `file_uploads=Off`, oder das Upload-Verzeichnis fehlt oder ist nicht beschreibbar | schalten Sie Uploads ein; prüfen Sie `upload_tmp_dir` (leer bedeutet `TMPDIR` oder `/tmp`) |
| „Session-Extension fehlt“ | kein `session` | installieren Sie es |
| „Fehlende zlib Extension“, „Fehlende DOM Extension“, „Fehlende iconv Extension“, „Die MBString Extension fehlt“, „Die intl-Erweiterung fehlt“, „Die xsl-Erweiterung fehlt“ | die Erweiterung ist im PHP des Webservers nicht geladen | installieren Sie sie für dieses PHP (siehe [Kapitel 2](02-voraussetzungen.md#29-hinweise-zu-betriebssystemen)) und laden Sie PHP neu |
| „Zeitzonen Konfiguration“ („You are using the default time zone, UTC. ...“) | `php.ini` hat kein `date.timezone`, deshalb ist `index.php` auf UTC ausgewichen | setzen Sie `date.timezone`; um UTC bewusst beizubehalten, setzen Sie es auf `UTC` oder kreuzen Sie **Diesen Test ignorieren** an |
| „Falsche eZ Components Version entdeckt“ oder „Fehlende eZ Components Abhängigkeit“ | die Zeta Components fehlen (`ezcBaseFile` nicht gefunden) | `composer install` |
| „Unstabile PHP Version“ | ein PHP, das in `[phpversion] UnstableVersions` aufgeführt ist | verwenden Sie ein anderes PHP |
| „cURL Extension fehlt“, „Die imagegd2-Extension fehlt“, „Fehlendes ImageMagick-Programm“, „Funktionen zur Texterstellung nicht vorhanden“, „Fehlende Datenbank-Handler“ | optionale Prüfungen unter **Feineinstellung des Systems** | installieren Sie sie, oder klicken Sie auf **Weiter** |
| „PHP registriert keine Umgebungs-Variablen“ | in `variables_order` fehlt `E` | setzen Sie es auf `EGPCS` |
| die Warnung zu `open_basedir` | `open_basedir` ist gesetzt | lassen Sie es leer, wenn Probleme auftreten |

Der Assistent läuft im PHP des **Webservers**. Schlägt eine Prüfung im Browser fehl, während `php -m` auf der
Kommandozeile unauffällig aussieht, unterscheiden sich die beiden PHP-Installationen: Installieren Sie die Erweiterung
für das Web-PHP (unter Plesk die PHP-Version der Domain) und laden Sie es neu.

## 12.7 Der Setup-Assistent

| Symptom oder Meldung | Ursache | Abhilfe |
|---|---|---|
| Jede Adresse zeigt den Setup-Assistenten | `[SiteAccessSettings] CheckValidity` ist noch `true`: Keine Installation wurde abgeschlossen | Schließen Sie den Assistenten ab, oder installieren Sie mit dem Kickstarter oder `exp:install`. Der letzte Schritt schreibt `CheckValidity=false` in `settings/override/site.ini.append.php`. |
| „The site is being set up“ (HTTP 503) | In einem anderen Browser läuft ein Assistent; nur der Browser mit dem Cookie `exp_setup_wizard` kommt durch. Oder ein Kickstarter-Lauf baut die Datenbank neu auf | Warten Sie: Die Sperre endet mit der letzten Seite des Assistenten oder 30 Minuten nach seiner letzten Anfrage (`expMaintenance::WIZARD_LEASE = 1800`). Wenn nichts läuft: `php bin/php/maintenance.php status`, dann `php bin/php/maintenance.php off`. |
| Der Assistent überspringt Seiten oder zeigt Werte, die Sie nicht eingegeben haben | Im Wurzelverzeichnis der Installation liegt eine `kickstart.ini`; Abschnitte mit `Continue=true` werden übersprungen, die anderen vorausgefüllt | Verschieben Sie `kickstart.ini`, um jede Seite selbst auszufüllen. |
| „Die Datenbank hat die Verbindung nicht akzeptiert. Bitte überprüfen Sie Ihre Einstellungen und probieren es erneut.“ | MySQL/MariaDB hat abgelehnt: falscher Host, Port, Benutzer, falsches Passwort, oder der Server läuft nicht | Prüfen Sie die Werte; testen Sie sie mit dem `mysql`-Client; siehe `var/log/setup.log`. |
| „Die Verbindung zur PostgreSQL-Datenbank ist fehlgeschlagen. ...“ | PostgreSQL hat die Verbindung abgelehnt | Prüfen Sie Server, Port, Benutzer, Passwort und ob die Datenbank existiert; erlauben Sie den Host in `listen_addresses` (`postgresql.conf`) und in `pg_hba.conf`. |
| „Die Funktion 'digest' ist in Ihrer Datenbank nicht verfügbar, und Exponential kann ohne sie nicht laufen. ...“ | PostgreSQL fehlt `pgcrypto`, und der Assistent konnte es nicht anlegen | Installieren Sie bei Bedarf das contrib-Paket des Servers; der Eigentümer der Datenbank oder ein Superuser führt `CREATE EXTENSION pgcrypto;` aus; klicken Sie auf **Weiter**. |
| „Die Version %version Ihrer Datenbank passt nicht zur Mindestanforderung. Benötigt wird mindestens Version %req_version.“ | Der Server ist älter als das Minimum in `kernel/setup/ezsetupcommon.php` | Verwenden Sie einen neueren Datenbankserver. |
| „Die Datenbank [...] kann nicht verwendet werden. Der Setup Wizard wollte die Site mit dem [...] Zeichensatz erstellen, aber die Datenbank wurde mit dem Zeichensatz [...] erstellt.“ | Die MySQL-Datenbank ist nicht UTF-8 | Legen Sie die Datenbank mit einem UTF-8-Zeichensatz an, oder konvertieren Sie sie. |
| „Die ausgewählte Datenbank ist nicht leer. Bitte wählen Sie eine der Alternativen unten.“ | Die Datenbank enthält Tabellen | Entscheiden Sie, ob die Daten entfernt oder behalten werden, oder wählen Sie eine andere Datenbank. Entfernen löscht alles darin. |
| „Der ausgewählte Benutzer hat keinen Zugriff auf irgendeine Datenbank. Bitte wechseln Sie den Benutzer oder erstellen einen Datenbank für ihn.“ | Der Benutzer darf keine Datenbank sehen | Legen Sie die Datenbank an und gewähren Sie dem Benutzer Zugriff. |
| „Der Name der Datenbankdatei ist ungültig. Geben Sie einen einfachen Dateinamen mit der Endung .db, .db3, .sqlite oder .sqlite3 an, ...“ | SQLite-Dateiname mit Pfad oder falscher Endung | Ein einfacher Name wie `exponential.db`; die Datei liegt in `var/storage/sqlite3/`. |
| „Die Datei %file existiert und ist keine SQLite-Datenbank. Wählen Sie einen anderen Dateinamen; die Installation überschreibt sie nicht.“ | Eine andere Datei trägt diesen Namen | Wählen Sie einen anderen Namen. |
| „Die SQLite-Datenbankdatei %file konnte nicht geöffnet werden. Den Grund finden Sie in var/log/setup.log und var/log/error.log.“ | Berechtigungen, eine beschädigte Datei oder eine fehlende `sqlite3`-Erweiterung | Lesen Sie die beiden Logs; siehe [12.3](#123-berechtigungen-und-eigentümerschaft). |
| „Die Passwörter stimmen nicht überein.“ | Die beiden Passwortfelder unterscheiden sich | Geben Sie sie erneut ein. |
| „Für die Hauptsprache %1 hat diese Installation kein Locale (share/locale).“ | Ein Locale-Code ohne Datei in `share/locale/` | Wählen Sie eine aufgeführte Sprache. |
| „%1 ist die Hauptsprache und kann nicht zugleich eine zusätzliche Sprache sein. ...“ | Dieselbe Sprache zweimal | Entfernen Sie das Häkchen bei den zusätzlichen Sprachen. |
| „Could not fetch site package: '...'“ oder „Failed to initialize site package '...'“ | Das Site-Paket konnte nicht heruntergeladen oder gelesen werden | Prüfen Sie den Netzwerkzugang zum Paket-Repository, das in `settings/package.ini` konfiguriert ist; lesen Sie `var/log/setup.log`. |
| „The editor siteaccess could not be made from settings/siteaccess/<admin>“ | Die Dateien des Administrations-Siteaccess wurden nicht geschrieben | Prüfen Sie die Schreibrechte für `settings/siteaccess/`; siehe `var/log/setup.log`. |
| „Failed loading database schema file share/db_schema.dba“ oder „Failed loading database data file share/db_data.dba“ | Das Verzeichnis `share/` der Installation ist unvollständig | Beschaffen Sie den Code erneut (Kapitel 3). |
| Die Seiten werden dargestellt, aber nach der Installation fehlen Bilder oder Unterelemente | Das Site-Paket verwendet ein Locale, das nicht unter den installierten Sprachen ist | Fügen Sie es den Sprachen hinzu und installieren Sie erneut. |

Alle Meldungen dieser Tabelle stehen in `kernel/setup/steps/ezstep_installer.php`, `ezstep_create_sites.php` und
`kernel/setup/ezsetupcommon.php`.

## 12.8 Der Kickstarter und die Installation über die Konsole

| Symptom oder Meldung | Ursache | Abhilfe |
|---|---|---|
| „The CreateSites step will modify the database and site settings.“ / „Re-run with --force to confirm you want to install the site package.“ | Der Kickstarter schreibt die Datenbank nur mit `--force` (`kernel/classes/expkickstarter.php`) | `php bin/php/console exp:kickstarter run --apply --force`, nachdem Sie `DatabaseAction` geprüft haben. |
| „Unknown start step: ...“ oder „Unknown stop step: ...“ | Ein Schrittname, den es nicht gibt | `php bin/php/console exp:kickstarter run --list-steps` listet sie auf. |
| „This directory already holds an installation (settings/override/site.ini.append.php).“ | `exp:install` hat eine konfigurierte Datenbank gefunden (`bin/php/install.php`) | Fügen Sie `--force` nur hinzu, um diese Installation zu ersetzen; `--dry-run` prüft, ohne etwas zu ändern. |
| Werte aus `kickstart.ini` werden ignoriert | Führende Leerzeichen vor einem Abschnitt oder Schlüssel (`kickstart.ini-dist`: „Remove all leading whitespaces“), ein fehlendes `Continue=true` oder veraltete zwischengespeicherte Werte | Entfernen Sie die Leerzeichen; setzen Sie `Continue=true`; löschen Sie `var/cache/ini/kickstart-*.php` (die Unterbefehle `ini` und `run` tun das selbst). |
| Die Installation landete in der falschen Datenbank-Engine | Ein `DatabaseImplementation`, das eine frühere Installation in `settings/override/site.ini.append.php` hinterlassen hat: Das Override-Verzeichnis hat Vorrang vor den Einstellungen jedes Siteaccess | Setzen Sie es vor der erneuten Installation auf die neue Engine (oder entfernen Sie es); prüfen Sie es mit `php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation`. |
| Nach einer Installation ist alles in der Datenbank verschwunden | `DatabaseAction=remove` (der Standard von `exp:install`, `--db-action=remove`) leert die angegebene Datenbank zuerst | Stellen Sie die Sicherung wieder her. Richten Sie Installationen nur auf Datenbanken, die nichts enthalten, was Sie brauchen. |
| Das von Ihnen angegebene Passwort wurde ersetzt | Passwörter, die kürzer als `[UserSettings] MinPasswordLength` oder allgemein bekannt sind, werden durch ein erzeugtes ersetzt | Lesen Sie es aus der Zusammenfassung oder aus `var/log/initial-admin-password`. |

## 12.9 Seiten und Fehler zur Laufzeit

| Was der Besucher sieht | Ursache | Abhilfe |
|---|---|---|
| „The site is missing the software libraries it needs“ (HTTP 503) | `vendor/` fehlt, wurde verschoben oder umbenannt (`lib/ezutils/classes/ezexecution.php`, `errorCase()`) | Stellen Sie `vendor/` wieder her, oder führen Sie `composer install` aus (`--no-dev` im Produktivbetrieb); dann `php bin/php/ezcache.php --clear-all` und, unter Velocity, ein Neustart. Siehe [Eine Installation reparieren](../bc/6.0/reparatur.md). |
| „Something went wrong on our side“ (HTTP 500) mit einer Referenz `ERR-...` | Ein nicht abgefangener Fehler | Suchen Sie die Referenz in `var/log/error.log`; die Zeile nennt Meldung, Datei und Zeile. Bei eingeschalteter Debug-Ausgabe zeigt die Seite die Details selbst. |
| „We'll be right back“ (HTTP 503, `Retry-After: 30`) | Die Datenbank war nicht erreichbar: abgelehnt, unbekannter Host, falsches Passwort, zu viele Verbindungen, Verbindung abgebrochen (`eZExecution::isDatabaseUnavailable()`) | Prüfen Sie, ob der Datenbankserver läuft, und die Zugangsdaten in `settings/override/site.ini.append.php`; die Logzeile enthält die Meldung des Treibers, bei PostgreSQL „Unable to connect to the database server '<host>'“. |
| „Down for maintenance“ (HTTP 503) | Der Wartungsmodus ist eingeschaltet | `php bin/php/maintenance.php status`; `php bin/php/maintenance.php off`, wenn die Arbeiten erledigt sind. |
| „Dieses Formular ist abgelaufen“ (HTTP 403) | Das Token des Formulars fehlte oder war falsch: Die Seite war lange geöffnet, die Sitzung ist abgelaufen, oder das Formular kam von einer anderen Seite | Laden Sie das Formular neu und senden Sie es erneut ab. Es wurde nichts gespeichert. Siehe [Seite „Formular abgelaufen“](../funktionen/6.0/seite-abgelaufenes-formular.md). |
| Eine leere Seite, oder 500 ohne Eintrag in `error.log` | PHP ist gescheitert, bevor Exponential protokollieren konnte: ein Syntaxfehler, eine fehlende PHP-Erweiterung, erschöpfter Speicher | Lesen Sie das Fehlerlog des Webservers oder von PHP-FPM; führen Sie `php -l` für eine geänderte Datei aus; prüfen Sie `php -v` und `vendor/`. |
| 404 für jede Seite unter Apache | `.htaccess` wird nicht gelesen (`AllowOverride All` fehlt), oder `mod_rewrite` ist ausgeschaltet | `cp .htaccess_root .htaccess`; Overrides erlauben; `mod_rewrite` aktivieren. |
| Alle paar Neuladevorgänge erscheint eine alte Seite | Der Service Worker des Browsers spielt eine Seite ab, die er zwischengespeichert hat (der Worker der Site ist `/index.js`, geladen von `/sw.js`) | Lassen Sie `/sw.js` an seinem Platz; ein Neuladen, nachdem der neue Worker aktiv geworden ist (Cache-Version `exp-nav-v5`), behebt es, oder löschen Sie die Website-Daten im Browser. Siehe [Verhaltensänderungen vom 16. bis 30. September 2026](../bc/6.0/verhaltensaenderungen-2026-09b.md). |
| Die Kommandozeile gibt „An unexpected error has occurred. Please contact the webmaster.“ aus | Ein nicht abgefangener Fehler in einem Skript | Führen Sie es erneut mit `-d all` aus; lesen Sie `var/log/error.log`. |

## 12.10 Datenbanken

| Symptom oder Meldung | Ursache | Abhilfe |
|---|---|---|
| SQLite: „database is busy: the transaction could not start within N s, another write held the lock all that time; nothing was written“ | Ein anderer Schreibvorgang hielt die Schreibsperre während der gesamten `[DatabaseSettings] SQLiteTransactionWait` (ausgeliefert 25 s, unter dem Anfrage-Timeout von Velocity von 30 s) | Es wurde nichts geschrieben; versuchen Sie es erneut. Erhöhen Sie die Einstellung zusammen mit dem Anfrage-Timeout (bei Velocity `Q.webserver.requestTimeout`, bei PHP-FPM `request_terminate_timeout`) und halten Sie sie darunter, oder verlegen Sie eine stark genutzte Redaktions-Site auf MySQL, MariaDB oder PostgreSQL. |
| SQLite: Die Site ist nach einer Installation oder einem großen Import langsam | Der Query-Planer hat keine Statistiken | Einmal `sqlite3 var/storage/sqlite3/exponential.db ANALYZE`. |
| SQLite: Die Datenbankdatei lässt sich herunterladen | Der Webserver liefert `var/` aus | Prüfen Sie es mit `curl -sI https://www.example.com/var/storage/sqlite3/exponential.db` (alles außer 200 ist richtig); verwenden Sie die mitgelieferten `.htaccess`-Regeln oder Velocity. |
| Nach dem Wechsel der Datenbank-Engine lädt keine Seite | `DatabaseImplementation` nennt noch die alte Engine | Setzen Sie es in `settings/override/site.ini.append.php`, dann `php bin/php/ezcache.php --clear-tag=ini`. |
| MySQL: `SET storage_engine=InnoDB;` schlägt in einer Update-Datei fehl | Eine Update-Datei von vor Oktober 2026: Diese Schreibweise wurde in MySQL 5.7.5 und MariaDB 12.0 entfernt | Nehmen Sie die aktuelle Datei, in der `SET default_storage_engine=InnoDB;` steht; siehe [Kapitel 11](11-aktualisieren.md#113-die-update-dateien). |
| PostgreSQL: Fehler zu `digest` | `pgcrypto` fehlt | `CREATE EXTENSION pgcrypto;` als Eigentümer oder Superuser. |
| MongoDB: Abfragen auf Teilbäume und URL-Aliase sind langsam | Die Indizes wurden nicht angelegt | `mongosh "mongodb://<user>:<password>@localhost:27017/<database>" --file bin/mongodb/create_indexes.js`; siehe [MongoDB](../funktionen/6.0/mongodb-datenbankunterstuetzung.md). |
| MongoDB: „Failed to parse MongoDB URI: 'mongodb://:@...'. 'default' authentication mechanism requires a username.“ | Es wurde kein Datenbankbenutzer angegeben: Der Adapter (`lib/ezdb/classes/expmongodb.php`) schreibt immer `<user>:<password>@` in seine Verbindungszeichenkette | Legen Sie in der Datenbank der Site einen Benutzer an und geben Sie ihn als `User` und `Password` an (`--db-user` bei `exp:install`); siehe [Kapitel 9](09-datenbanken.md#95-mongodb). |
| MongoDB: `Class "MongoDB\Client" not found` | Die PHP-Bibliothek `mongodb/mongodb` ist nicht installiert | `composer require mongodb/mongodb`; außerdem muss die PHP-Erweiterung `mongodb` geladen sein. |
| Oracle: Der Treiber wird nicht gefunden | Die Extension `ezoracle` ist nicht in `extension/` oder nicht aktiv | Sie wird von `composer.json` verlangt; prüfen Sie `extension/ezoracle` und ob `oci8` geladen ist. |

## 12.11 Velocity und der Webserver

| Symptom oder Meldung | Ursache | Abhilfe |
|---|---|---|
| `velocity: no server script at ...` | Die Engine `qbix` braucht das Velocity-Paket, das nicht installiert ist | `composer install` (das Paket ist erforderlich), oder `composer require se7enxweb/exponential-velocity:~0.0.4.57` (PHP 8.0+); oder verwenden Sie für die Entwicklung `--engine=php`. |
| `velocity: already running` | Ein Server dieser Engine läuft bereits | `php bin/php/console exp:velocity status`; `restart`, um Änderungen zu übernehmen. |
| `velocity: port ... is already in use by another process (another engine? exp:velocity stop --engine=qbix\|frankenphp)` | Ein anderes Programm oder eine andere Engine belegt den Port (Engines php und frankenphp) | Halten Sie die andere Engine an, oder ändern Sie `Port`/`HTTPSPort` mit `exp:velocity config set`. |
| `velocity: did not start; see <log>` (die Engines `php` und `frankenphp`) | Der Server ist beim Start gescheitert | Lesen Sie das genannte Log. |
| Ein Start von `qbix`, der nicht hochkommt | Die Engine ist nach dem Start gescheitert: ein belegter Port, ein Zertifikat, das sie nicht lesen kann, eine fehlende PHP-Erweiterung | Lesen Sie `var/vc/qbix/run/console.log` (`[ServerSettings] LogFile`); dort stehen die Startzeilen und der Grund. |
| `velocity: EnginePhar names an archive that does not exist: ... -- build it first, or set EnginePhar=disabled` | `[ServerSettings] EnginePhar` zeigt auf ein Engine-Archiv, das nie gebaut oder das entfernt wurde | `php bin/php/console exp:phar build`, oder `exp:velocity config set ServerSettings EnginePhar disabled`; siehe [Kapitel 8](08-die-website-ausliefern.md#8315-das-engine-archiv-enginephar). |
| `velocity deploy: FAIL after Ns; the steps after the failed one were not run` | Ein Schritt von `exp:velocity deploy` ist fehlgeschlagen | Die Ausgabe nennt den Schritt; beheben Sie das Problem und führen Sie `deploy` erneut aus (`--dry-run` listet die Schritte auf). |
| Eine Änderung an PHP, Templates oder Einstellungen wird unter Velocity nicht sichtbar | Die Worker halten die Anwendung im Speicher | `exp:velocity restart` (oder `deploy`), dann `exp:velocity cache clear`. |
| Die Ports 80 und 443 lassen sich nicht binden | Ports unter 1024 erfordern root | Starten Sie Velocity als root (die Worker wechseln zu `[ServerSettings] User`), oder verwenden Sie Ports über 1024 hinter einem Proxy. |
| HTTPS zeigt ein selbstsigniertes Zertifikat | Das konfigurierte Zertifikat fehlt, ist abgelaufen oder passt nicht zu seinem Schlüssel; Velocity weicht auf ein selbstsigniertes aus | `php bin/php/console exp:velocity ssl show`; korrigieren Sie `[HTTPSSettings] Certificate` und `Key`. Siehe [Velocity: HTTPS und Zertifikate](../funktionen/6.0/velocity-https-zertifikate.md). |
| Eine PHP-Änderung wird unter Apache oder nginx nicht sichtbar | Das PHP-FPM, das die Site ausliefert, wurde nicht neu geladen; auf Servern mit mehreren PHP-Versionen ist es nicht immer `php-fpm` (Plesk: `plesk-php<XY>-fpm`) | Laden Sie den richtigen Dienst neu; `exp:velocity deploy` findet ihn mit `[DeploySettings] PhpFpmService=auto`. |
| Nur mit Velocity versucht `deploy`, PHP-FPM neu zu laden | `PhpFpmService=auto` | Setzen Sie `[DeploySettings] PhpFpmService=disabled`. |

## 12.12 Caches und Änderungen, die nicht sichtbar werden

Leeren Sie den kleinsten Cache, der die Änderung abdeckt (`php bin/php/ezcache.php --list-tags` und `--list-ids`
listen sie auf):

| Sie haben geändert | Befehl |
|---|---|
| eine Einstellung (`.ini`) | `php bin/php/ezcache.php --clear-tag=ini` (nicht `--clear-id=ini`, das den globalen INI-Cache stehen lässt) |
| ein Template (`.tpl`) | `php bin/php/ezcache.php --clear-tag=template` |
| eine Template-Datei, einen Override oder eine Extension hinzugefügt | `php bin/php/ezcache.php --clear-id=template-override` |
| ein Stylesheet oder Skript, das von `ezjscore` gepackt wird | `php bin/php/ezcache.php --clear-id=ezjscore-packer` und `--clear-id=template-block` |
| Inhalte wirken veraltet | `php bin/php/ezcache.php --clear-tag=content` |
| eine PHP-Klasse, unter Velocity | `php bin/php/console exp:velocity deploy` (oder `restart`) |
| unsicher | `php bin/php/ezcache.php --clear-all` |

Leeren Sie unter Velocity außerdem dessen Antwort-Cache, und zwar **nach** dem Neustart:
`php bin/php/console exp:velocity cache clear --apply`. Eine Seite, die zwischen dem Leeren eines Caches und dem Neustart noch
vom alten Code dargestellt wird, landet erneut im Cache und wird ausgeliefert, als hätte die Änderung nicht
funktioniert. Mehr dazu in [Cache-Konsole](../bc/6.0/cache-konsole.md) und [Eine Site betreiben](../anleitungen/betrieb-einer-website.md).

| Symptom | Ursache | Abhilfe |
|---|---|---|
| Eine neue Template-Datei stellt einen leeren Bereich dar (HTTP 200) | Der Template-Override-Cache ist älter als die Datei | `--clear-id=template-override`. |
| `Class ... not found` nach dem Hinzufügen oder Umbenennen einer Klasse | Die Klassenverzeichnisse sind veraltet | `php bin/php/ezpgenerateautoloads.php -e` (`-k` für den Kernel), dann die Caches leeren; unter Velocity neu starten. |
| „The referenced parameter '...' is not registered.“ von `ezpgenerateautoloads.php`, Exit-Status 1 | Eine Option, die der Generator nicht kennt; es wurde nichts erzeugt | Korrigieren Sie die Option (`--help` listet sie auf). Ältere Releases gaben bei einer unbekannten Option, auch bei `--allow-root-user`, ihre Verwendungshinweise aus und endeten mit 0; ein Deployment-Skript, das sie ausgeführt hat, hat daher womöglich nie etwas neu erzeugt. |
| „The audit index classes are not in the autoload array: run bin/php/ezpgenerateautoloads.php -k first.“ | `createaudittables.php` lief, bevor das Klassenverzeichnis des Kernels neu erzeugt wurde | Führen Sie `php bin/php/ezpgenerateautoloads.php -k` aus, dann das Skript erneut. |

## 12.13 Anmelden

| Symptom | Ursache | Abhilfe |
|---|---|---|
| Das Administratorpasswort ist verloren | | `php bin/php/resetuserpassword.php -u admin -g --allow-root-user` als root (die root-Umgehung), oder mit der Anmeldung eines Administrators (`-a <login> -ap <password>`); `-p <password>` setzt ein vorgegebenes Passwort. Siehe [Ein Benutzerpasswort zurücksetzen](../funktionen/6.0/benutzerpasswort-zuruecksetzen.md). |
| Das erzeugte Passwort ist unbekannt | Es wurde einmal am Ende der Installation ausgegeben | `var/log/initial-admin-password` (nur für den Eigentümer lesbar); löschen Sie die Datei nach der ersten Anmeldung. |
| Ein per SQL oder Migration angelegtes Konto kann sich nicht anmelden | Seit dem 16. August 2026 gilt ein Benutzer ohne Zeile in `ezuser_setting` als deaktiviert | Legen Sie die Zeile an; die Abfragen stehen in der [Sicherheitsspezifikation vom August 2026](../spezifikationen/6.0/sicherheitshaertung-2026-08.md#f-06-konten-finden-die-sich-nicht-anmelden-können). |
| Hinter einem Proxy oder Load Balancer: HTTPS-Seiten verlinken oder leiten auf `http://` weiter, eine Weiterleitungsschleife, oder jeder Besucher hat die Adresse des Proxys | Weitergeleitete Header (`X-Forwarded-Proto`, `X-Forwarded-Host`, `X-Forwarded-For`) zählen nur von Adressen in `site.ini [HTTPHeaderSettings] TrustedProxies[]`, ausgeliefert nur mit `127.0.0.1` und `::1` | Tragen Sie die Adresse des Proxys in `TrustedProxies[]` ein; für die Adresse des Besuchers setzen Sie außerdem `ClientIpByCustomHTTPHeader=X-Forwarded-For`. Siehe [Kapitel 8](08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy) und [Vertrauenswürdige Proxys](../bc/6.0/vertrauenswuerdige-proxys.md). |
| Angemeldet, aber auf der nächsten Seite abgemeldet (hinter einem Proxy oder bei gemischtem HTTP und HTTPS) | Attribute des Sitzungscookies: `[Session] CookieSecure=auto` markiert das Cookie als `Secure`, wenn die Anfrage über HTTPS kam; `CookieSameSite=Lax` | Liefern Sie die gesamte Site über ein einziges Schema aus; wenn die Administration von einer anderen Site in einen Frame eingebettet oder Formulare von dort an sie gesendet werden, setzen Sie `CookieSameSite=None` zusammen mit `CookieSecure=true`. Siehe [Sicherheitsvorgaben](../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md). |

## 12.14 Hilfe erhalten

- Durchsuchen Sie diese Dokumentation: die [Anleitungen](../anleitungen/README.md), die [Funktionsseiten](../../eng-us/features/6.0/),
  die [Hinweise zu Verhaltensänderungen](../../eng-us/bc/6.0/) und das [Glossar](../glossar.md).
- Das Online-Handbuch: [https://exponential.doc.exponential.earth/](https://exponential.doc.exponential.earth/).
- Einen Fehler melden: im [Issue-Tracker von Exponential](https://github.com/se7enxweb/exponential/issues) oder im
  [Issue-Tracker der Community](https://github.com/se7enxweb/exponential-community/issues).
- Melden Sie ein Sicherheitsproblem vertraulich, wie in [SECURITY.md](../../../SECURITY.md) beschrieben, nie in einem
  öffentlichen Issue.
- Foren: [share.exponential.earth/forums](https://share.exponential.earth/forums).

Wenn Sie ein Problem melden, geben Sie die Version (`php bin/php/console --version`), `php -v`, die Datenbank und ihre
Version, die Engine, die die Site ausliefert, die genaue Meldung und die passenden Zeilen aus `var/log/error.log` an.
Entfernen Sie Passwörter und personenbezogene Daten aus Logs, bevor Sie sie weitergeben.

Manches, was wie ein Fehler aussieht, ist beabsichtigtes Verhalten oder Auslegungssache, auf das sich andere Sites
verlassen. Eine Änderung daran braucht eine klare Begründung und kann mehrere Runden der Prüfung brauchen; eine
Option ist besser als ein neuer Standard
([Negative Funktionen](../hilfe/projekt/unterstuetzung-erhalten.md#negative-funktionen)).

**Demo-Modus.** Ein Banner **Demo-Modus**, ein Administratorpasswort, das sich nicht ändern lässt („Diese Installation ist eine öffentliche Demo: Die Passwörter von Administratoren können nicht geändert oder zurückgesetzt werden.“), oder ein Installationsprogramm, das ein allgemein bekanntes Passwort beibehält: Der Prozess hat die Umgebungsvariable `EXP_DEMO_MODE=1`. Bei einer Demo ist das so gewollt; auf einer echten Site entfernen Sie die Variable. Eine Demo, deren Banner fehlt oder deren Build fehlschlägt, weil das Installationsprogramm ein Passwort erzeugt hat: [Probleme mit dem Demo-Modus](../hilfe/hosting/runbooks/probleme-mit-dem-demomodus.md).

## Referenzen

In diesem Repository:

- [Installationsübersicht](../INSTALL.md), Abschnitt 12.
- [Eine Installation reparieren](../bc/6.0/reparatur.md) und [Reparatur aus dem Browser](../funktionen/6.0/reparatur-im-browser.md).
- [Wartungsmodus](../funktionen/6.0/wartungsmodus.md), [Seite „Formular abgelaufen“](../funktionen/6.0/seite-abgelaufenes-formular.md),
  [Ein Benutzerpasswort zurücksetzen](../funktionen/6.0/benutzerpasswort-zuruecksetzen.md).
- [Die Exp Debug bar](../funktionen/6.0/exp-debug-leiste.md), [Referenz der Debug-Leiste](../bc/6.0/debug-leiste.md),
  [Einstellungen je Umgebung](../funktionen/6.0/umgebungseinstellungen.md), [exp:ini](../funktionen/6.0/exp-ini-befehl.md).
- [Cache-Konsole](../bc/6.0/cache-konsole.md), [Eine Site betreiben](../anleitungen/betrieb-einer-website.md),
  [Bereitstellung](../anleitungen/bereitstellung.md).
- [SQLite-Datenbankunterstützung](../funktionen/6.0/sqlite-datenbank.md), [SQLite-Transaktionen](../bc/6.0/sqlite-transaktionen.md),
  [MongoDB](../funktionen/6.0/mongodb-datenbankunterstuetzung.md).
- [Velocity-Engines](../bc/6.0/velocity-engines.md), [Velocity: HTTPS und Zertifikate](../funktionen/6.0/velocity-https-zertifikate.md),
  [Velocity: Verzeichnisstruktur auf der Festplatte](../bc/6.0/velocity-verzeichnisstruktur.md).
- [Audit](../bc/6.0/audit.md), [Sicherheitsvorgaben](../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md),
  [Anleitung zu Sicherheit und Audit](../anleitungen/sicherheit-und-audit.md).
- [Installer-Logs und Startdaten](../spezifikationen/6.0/installer-protokolle-und-startdaten.md).
- Code: `kernel/setup/ezsetuptests.php`, `kernel/setup/steps/ezstep_installer.php`,
  `kernel/setup/steps/ezstep_create_sites.php`, `kernel/classes/expkickstarter.php`, `bin/php/install.php`,
  `lib/ezutils/classes/ezexecution.php`, `lib/ezutils/classes/ezdebug.php`, `lib/ezfile/classes/ezlog.php`,
  `kernel/classes/expmaintenance.php`, `kernel/classes/expvelocity.php`, `design/standard/templates/setup/tests/`.

Extern:

- PHP: [Fehlerbehandlung und Protokollierung](https://www.php.net/manual/en/errorfunc.configuration.php),
  [php.ini-Direktiven](https://www.php.net/manual/en/ini.list.php),
  [Liste der unterstützten Zeitzonen](https://www.php.net/manual/en/timezones.php),
  [PHP-FPM-Konfiguration](https://www.php.net/manual/en/install.fpm.configuration.php).
- Composer: [Fehlerbehebung](https://getcomposer.org/doc/articles/troubleshooting.md),
  [Plattformanforderungen](https://getcomposer.org/doc/03-cli.md#check-platform-reqs).
- Apache: [mod_rewrite](https://httpd.apache.org/docs/current/mod/mod_rewrite.html),
  [AllowOverride](https://httpd.apache.org/docs/current/mod/core.html#allowoverride).
- Datenbanken: [Client-Authentifizierung in PostgreSQL](https://www.postgresql.org/docs/current/client-authentication.html),
  [pgcrypto](https://www.postgresql.org/docs/current/pgcrypto.html),
  [SQLite-Kommandozeilen-Shell](https://sqlite.org/cli.html), [SQLite WAL](https://sqlite.org/wal.html).
- [Issue-Tracker von Exponential](https://github.com/se7enxweb/exponential/issues),
  [Issues der Community](https://github.com/se7enxweb/exponential-community/issues).

[Inhalt](README.md) · Zurück: [11. Upgrade](11-aktualisieren.md) · Weiter: [13. Sicherheitshärtung für den Produktivbetrieb](13-sicherheitshaertung.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
