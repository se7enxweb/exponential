# Demo-Modus: EXP_DEMO_MODE=1 für öffentliche Demonstrationsseiten (6.0.15)

Eine öffentliche Demo von Exponential veröffentlicht ihre Administrator-Anmeldung, damit Besucher die Administration
ausprobieren können. Ohne Hilfe geht dann zweierlei schief:

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

1. Ein Besucher ändert das Administratorpasswort oder setzt es zurück, und der nächste Besucher kann sich nicht
   anmelden. Bei einer Demo, die alle paar Stunden neu gebaut wird, ist die Demo damit bis zum nächsten Neubau kaputt;
   bei einer, die nicht neu gebaut wird, für immer.
2. Seit 6.0.15 lehnen die Installationsprogramme ein bekanntes Administratorpasswort ab
   ([schwache Passwörter](schwache-passwoerter-und-gueltigkeit-von-reset-links.md)), sodass ein Demo-Installationsprogramm
   das Passwort, das es veröffentlicht, nicht installieren konnte und es nach der Installation zurücksetzen musste.

Der Demo-Modus löst beides. Er wird durch eine **Umgebungsvariable** eingeschaltet, nie durch eine Einstellung:

```sh
EXP_DEMO_MODE=1
```

Er ist auf jeder Installation aus, die sie nicht setzt, also auf jeder Installation, die nicht absichtlich als Demo
eingerichtet wurde. Dort ändert sich nichts.

## Inhalt

1. [Was der Demo-Modus tut](#was-der-demo-modus-tut)
2. [Wer als Administrator gilt](#wer-als-administrator-gilt)
3. [Jeder geschlossene Weg](#jeder-geschlossene-weg)
4. [Die Installationsprogramme akzeptieren das Demo-Passwort](#die-installationsprogramme-akzeptieren-das-demo-passwort)
5. [Warum er sich nicht versehentlich einschalten lässt](#warum-er-sich-nicht-versehentlich-einschalten-lässt)
6. [Einschalten](#einschalten)
7. [Banner und Protokoll](#banner-und-protokoll)
8. [Risiken](#risiken)
9. [Das Demo-Installationsprogramm auf dem Demo-Host](#das-demo-installationsprogramm-auf-dem-demo-host)
10. [Für Entwickler](#für-entwickler)
11. [Kompatibilität](#kompatibilität)
12. [Tests](#tests)
13. [Wo der Demo-Modus sonst dokumentiert ist](#wo-der-demo-modus-sonst-dokumentiert-ist)

## Was der Demo-Modus tut

Solange `EXP_DEMO_MODE=1` gilt:

- **Das Passwort eines Administrators kann nicht geändert oder zurückgesetzt werden**, auf keinem Weg: die Seite
  **Passwort ändern**, das Formular **Passwort vergessen?** und seine Links, das Benutzerkonto im Bearbeitungsformular,
  `exp:resetuserpassword` (auch mit `--allow-root-user`), die Webdienste und jeder andere Code, der einen Benutzer
  speichert (siehe [Jeder geschlossene Weg](#jeder-geschlossene-weg)). Jede Ablehnung sagt, übersetzt: „Diese
  Installation ist eine öffentliche Demo: Die Passwörter von Administratoren können nicht geändert oder zurückgesetzt
  werden.“
- **Jedes andere Konto behält jede Regel**: Ein Redakteur oder ein registrierter Besucher ändert sein eigenes Passwort
  wie gewohnt und setzt es zurück, und neue Passwörter werden weiterhin gegen die Liste bekannter Passwörter geprüft.
- **Die Installationsprogramme akzeptieren das Demo-Passwort**: ein bekanntes Administratorpasswort beliebiger Länge,
  etwa `publish` oder `publishing`. Betroffen ist nur das Administratorpasswort einer neuen Installation; ein leeres
  wird weiterhin abgelehnt ([Die Installationsprogramme akzeptieren das Demo-Passwort](#die-installationsprogramme-akzeptieren-das-demo-passwort)).
- **Die Administration zeigt auf jeder Seite ein Banner** (admin4, admin4l und die klassische Administration):
  „Demo-Modus. Diese Installation ist eine öffentliche Demo: Jeder kann ihr Administratorpasswort kennen, und
  Administratorpasswörter können nicht geändert oder zurückgesetzt werden. Geben Sie hier nichts Privates ein.“
- **Jeder Prozess verzeichnet einmal, dass der Demo-Modus eingeschaltet ist**: eine Zeile in `var/log/security.log`
  und das Audit-Ereignis `system.demo.mode` ([Banner und Protokoll](#banner-und-protokoll)).

Der Demo-Modus macht eine Installation nicht schreibgeschützt, setzt keine Inhalte zurück und schränkt nicht ein, was
ein Administrator außer dem Passwort tun darf. Eine Demo, die rückgängig machen muss, was Besucher tun, wird nach
Zeitplan neu gebaut (siehe [Das Demo-Installationsprogramm auf dem Demo-Host](#das-demo-installationsprogramm-auf-dem-demo-host)).

## Wer als Administrator gilt

Ein Konto gilt als Administrator, wenn seine Rollen ihm **uneingeschränkten Zugriff auf jede Funktion jedes Moduls**
geben: eine Richtlinie für alle Module und alle Funktionen ohne Einschränkung, wie sie die ausgelieferte Rolle
**Administrator** hat. Das umfasst das Konto `admin`, jedes Konto in **Administrator users** und jedes Konto, dem eine
solche Rolle direkt gegeben wurde.

Ein Konto, dessen Rollen sich nicht lesen lassen (ein Datenbankfehler), gilt bei eingeschaltetem Demo-Modus als
Administrator: Ein Fehler öffnet nie das Passwort eines Administrators. Die Prüfung ist
`expDemoMode::isAdministrator()`.

Ein Konto mit weitem, aber eingeschränktem Zugriff (ein Redakteur, ein Administrator einer Sektion) ist nicht
geschützt. Veröffentlicht eine Demo auch die Anmeldung eines solchen Kontos, geben Sie ihm eine Rolle, deren Übernahme
sich nicht lohnt, oder bauen Sie die Demo oft neu.

## Jeder geschlossene Weg

| Weg | Was im Demo-Modus geschieht | Code |
|---|---|---|
| **Passwort ändern** (`user/password`), jedes Design | das Formular sagt, warum, und ändert nichts; in admin und admin4 zeigt die Seite den Grund, bevor etwas eingegeben wird | `kernel/private/classes/views/user/password.php` |
| **Passwort vergessen?** (`user/forgotpassword`), die Anforderung | kein Schlüssel und keine E-Mail für einen Administrator; die Antwort ist dieselbe, die jede Adresse erhält, und die Seite sagt jedem Besucher, dass Administratorpasswörter nicht zurückgesetzt werden können, sodass das Formular nicht verrät, welche Adressen einem Administrator gehören | `kernel/private/classes/views/user/forgotpassword.php` |
| **Passwort vergessen?**, der Link | ein Link eines Administrators (verschickt, bevor der Modus eingeschaltet wurde) wird abgelehnt und entfernt | ebenda |
| Das Benutzerkonto in **content/edit** (auch das eigene Profil) | ein neues Passwort für einen Administrator ist ein Validierungsfehler mit dem Grund; die anderen Felder lassen sich weiterhin ändern | `kernel/classes/datatypes/ezuser/ezusertype.php` |
| `exp:resetuserpassword` / `bin/php/resetuserpassword.php` | mit dem Grund und Exit-Code 1 abgelehnt, auch mit `--allow-root-user` | `kernel/private/classes/commands/resetuserpassword.php` |
| Webdienste: `expaccount.reset`, `expaccount.changePassword`, `expuser.setPassword` | HTTP 403 mit dem Grund | `extension/expservices/classes/users/expusersbase.php` |
| Webdienste: `expaccount.forgotRequest` | kein Schlüssel und keine E-Mail für einen Administrator; dieselbe Antwort wie für jede Adresse | `extension/expservices/classes/users/expaccountservices.php` |
| Die Operationen `user/password` und `user/forgotpassword` (Workflows, Erweiterungen) | Status false mit dem Grund `demo_mode` | `kernel/user/ezuseroperationcollection.php` |
| **Alles andere, was einen Benutzer speichert**: ein Import aus CSV oder einem Paket, eine Erweiterung, ein Skript | `eZUser::store()` behält das gespeicherte Passwort eines Administrators und speichert alles andere; die Ablehnung wird verzeichnet | `kernel/classes/datatypes/ezuser/ezuser.php`, `expDemoMode::guardStore()` |

Jede Ablehnung wird im Audit-Protokoll als `access.user.password.change.failed` oder
`access.user.password.reset.failed` mit dem Grund `demo_mode` und dem Weg verzeichnet
([audit.md](audit.md)), und als Warnung in der Debug-Ausgabe.

Nicht betroffen: die Anmeldung (ein vorhandenes Passwort wird nie abgelehnt) und das Hash-Upgrade bei der Anmeldung
(`[UserSettings] UpdateHash`), das dasselbe Passwort mit einem neueren Verfahren speichert.

## Die Installationsprogramme akzeptieren das Demo-Passwort

Jedes Installationsprogramm fragt dieselben Funktionen, ob ein Administratorpasswort installiert werden darf:
`eZStepSiteAdmin::isWeakDefaultPassword()` (die Liste bekannter Passwörter) und
`eZStepSiteAdmin::isTooShortAdminPassword()` (`[UserSettings] MinPasswordLength`, im Auslieferungszustand 10). Das sind
der Setup-Assistent, der Kickstarter (`kickstart.ini`), `exp:install` (`bin/php/install.php`) und jedes darauf
aufgebaute Demo-Installationsprogramm. Im Demo-Modus akzeptieren beide das Demo-Passwort. Genau:

- **Gelockert:** die Prüfung gegen die Liste bekannter Passwörter (`expWeakPasswordList`, mit ihren Varianten) und
  die Mindestlänge, nur für das Administratorpasswort einer neuen Installation. Die Installation speichert es innerhalb
  von `expDemoMode::installing()` und `expDemoMode::settingInstallationAdministratorPassword()`; nur dort lässt
  `eZUser::validatePassword()` ein Passwort durch, das kürzer als `MinPasswordLength` ist.
- **Nicht gelockert:** Ein leeres Passwort wird weiterhin abgelehnt (der Kickstarter und `exp:install` erzeugen eines,
  der Assistent fragt erneut); jedes andere Konto, das die Installation anlegt, jedes nach der Installation gesetzte
  Passwort und jedes Formular (`user/password`, Registrierung, das Formular zur Benutzerbearbeitung, die Konsole, die
  Webdienste) behalten `MinPasswordLength`, die Regeln aus `[PasswordSettings]` und die Liste bekannter Passwörter.

Eine Demo kann also `publish` (sieben Zeichen, der Klassiker) oder `publishing` unverändert installieren, mit den
ausgelieferten Einstellungen.

Der Setup-Assistent sagt auf seiner Seite für den Administrator, dass der Demo-Modus eingeschaltet ist und was das
bedeutet. Das Speichern des Administratorpassworts durch die Installation geht durch den Speicherschutz, weil es
innerhalb von `expDemoMode::installing()` läuft.

## Warum er sich nicht versehentlich einschalten lässt

- **Nur über die Umgebung.** Es gibt dafür keine INI-Einstellung, keinen Anfrageparameter, kein Cookie und keine Seite
  in der Administration. Eine von einer Demo kopierte Einstellungsdatei, eine daraus wiederhergestellte Datenbank, ein
  Paket oder eine präparierte Anfrage können ihn nicht einschalten. Ein HTTP-Header kommt als `HTTP_EXP_DEMO_MODE` an
  und wird nie gelesen.
- **Nur der exakte Wert `1`.** `0`, `true`, `yes`, `on`, ein leerer Wert und alles andere lassen ihn aus.
- **Bei jedem Aufruf gelesen.** Nichts wird gecacht, sodass ein persistenter Worker (Exponential Velocity, FrankenPHP)
  für die Umgebung antwortet, die er gerade hat, und ein kompiliertes Template bei jeder Ausführung erneut fragt.
- **Sichtbar.** Das Banner auf jeder Seite der Administration, der Hinweis im Setup-Assistenten, die Warnung auf der
  Konsole (`Demo mode is on (EXP_DEMO_MODE=1) ...` auf der Standardfehlerausgabe) und die Log-Zeile jedes Prozesses.

Der Wert wird zuerst mit `getenv()` gelesen (die Umgebung des Prozesses; unter PHP-FPM auch die FastCGI-Parameter),
dann aus `$_SERVER` (Apache `SetEnv` mit mod_php und ähnlichen SAPIs). Ein Wert in der Umgebung des Prozesses gewinnt:
`EXP_DEMO_MODE=0` dort hält den Modus aus, egal was `$_SERVER` sagt.

## Einschalten

Setzen Sie die Variable für **jeden** Prozess der Demo: das PHP des Webservers, Exponential Velocity, wenn er die
Website ausliefert, die Cronjobs und jeden Konsolenbefehl. Ein Prozess ohne sie läuft mit ausgeschaltetem Demo-Modus.

| Wo PHP läuft | Wie |
|---|---|
| PHP-FPM | im Pool der Demo: `env[EXP_DEMO_MODE] = 1` (ein Pool mit `clear_env = no` übergibt auch die Umgebung des Masters) |
| Apache mit mod_php oder mit Weiterleitung an PHP-FPM | `SetEnv EXP_DEMO_MODE 1` im virtuellen Host der Demo (mit PHP-FPM über `mod_proxy_fcgi` erreicht es PHP als FastCGI-Parameter) |
| Exponential Velocity | `export EXP_DEMO_MODE=1` in den eigenen **Site-envvars** der Demo, `/etc/vc/sites-available/<site>.envvars` (`<site>` ist der Name, den `./console exp:velocity layout` ausgibt). `exp:velocity start` liest die Datei nur für den Server dieser Installation, sodass Neustarts und Deploys aus jeder Shell sie behalten. Die Umgebung des Prozesses, der den Server dieser Installation startet, funktioniert ebenfalls (`Environment=EXP_DEMO_MODE=1` in seiner eigenen systemd-Unit oder einem Drop-in). **Nie** `/etc/vc/envvars`: Der Server jeder Installation auf dem Host liest diese Datei, und ein Velocity-Worker lehnt einen Wert für den Demo-Modus ab, der von dort kommt ([Velocity: nur eine Installation](#velocity-nur-eine-installation)) |
| FrankenPHP, RoadRunner | in der Umgebung des Serverprozesses |
| Cronjobs und Konsolenbefehle | in der Crontab (`EXP_DEMO_MODE=1` auf einer eigenen Zeile oben) und in der Shell dessen, der Befehle ausführt |
| Ein Container | `ENV EXP_DEMO_MODE=1` im Demo-Image oder `-e EXP_DEMO_MODE=1` / `environment:` nur für den Demo-Dienst |

Prüfen Sie es: Das Banner erscheint in der Administration, und `grep 'demo/mode' var/log/security.log` führt die
Prozesse auf, die damit gestartet sind.

**Velocity und Neustarts.** Setzen Sie die Variable in die Site-envvars der Demo, neben ihre Site-Datei:

    # /etc/vc/sites-available/<site>.envvars   (<site>: siehe ./console exp:velocity layout)
    export EXP_DEMO_MODE=1

`exp:velocity start` liest diese Datei bei jedem Start des Servers dieser Installation und übergibt, was sie setzt, nur
diesem Server, sodass `exp:velocity restart` und `exp:velocity deploy` den Demo-Modus eingeschaltet lassen, gleich
welche Shell sie ausführt. `./console exp:velocity layout` führt die gesetzten Namen in der Zeile `site envvars` auf.
Eine Variable, die bereits in der Umgebung gesetzt ist, die den Server startet, wird von der Site-Datei überschrieben.
Um den Demo-Modus zu beenden, entfernen Sie die Zeile und starten Velocity neu. Die Datei wird gelesen, nie
ausgeführt, nach denselben Regeln wie envvars: ein `NAME=value` pro Zeile, und sie kann `PATH`, `LD_*`, `DYLD_*`,
`MALLOC_*`, `HTTP_*`, `PHPRC`, `PHP_INI_SCAN_DIR`, `GLIBC_TUNABLES`, `HOME`, `USER` und die anderen Variablen von Loader
und Shell nicht setzen (`expVelocityConfigLayout::ENV_DENIED`, dieselbe Liste wie die der Engine). Ein Neustart mit dem
eigenen `qbixctl` der Engine statt mit `exp:velocity` liest die Site-Datei nicht: Der Demo-Modus ist dann bis zum
nächsten `exp:velocity restart` ausgeschaltet (aus ist die sichere Richtung: Die Website funktioniert, nur könnten
Besucher das Passwort ändern).

### Velocity: nur eine Installation

Die gemeinsame Datei `/etc/vc/envvars` (oder `/etc/qbix/envvars`) liest der Velocity-Server **jeder** Installation auf
dem Host, so wie jeder virtuelle Host `/etc/apache2/envvars` liest. Ein dort gesetzter Demo-Modus würde ihn auch für
Produktivsites einschalten, deren Administratoren dann kein Passwort mehr ändern könnten. Deshalb gilt seit diesem
Release:

- `exp:velocity start` übergibt `EXP_DEMO_MODE` nie aus den gemeinsamen envvars
  (`expVelocityConfigLayout::SITE_ONLY_ENV`);
- ein Velocity-Worker lehnt einen Wert ab, den die Engine selbst aus einer envvars-Datei eines Konfigurationsverzeichnisses
  außerhalb der Installation exportiert hat (`expDemoMode::isSharedEnvvarsValue()`, das den eigenen Eintrag
  `Q_WebServer_Layout::$exported` der Engine und die von ihr gelesenen Verzeichnisse auswertet). Der Demo-Modus bleibt
  ausgeschaltet, und der Worker verzeichnet einmal pro Prozess in `var/log/security.log` und als Debug-Warnung:
  `demo/refused pid=... EXP_DEMO_MODE=1 came from an envvars file other installations share ...`;
- ein Konfigurationsbaum innerhalb der Installation (`var/vc/qbix/etc`, von einem Konto ohne root verwendet) gehört
  allein dieser Installation, und seine envvars werden wie bisher übernommen.

Eine Installation, die mit einem früheren Release über `/etc/vc/envvars` mit eingeschaltetem Demo-Modus gestartet
wurde, hat ihn nach dem Neustart des Updates **ausgeschaltet**, bis die Zeile in die Site-envvars verschoben ist.
Verschieben Sie sie vor dem Neustart: `grep EXP_DEMO_MODE /etc/vc/envvars`, dann tragen Sie sie nur in
`/etc/vc/sites-available/<site>.envvars` der Demo ein und entfernen sie aus der gemeinsamen Datei.

PHP-FPM, Apache, LiteSpeed, FrankenPHP und die Konsole sind nicht betroffen: Jeder Pool, jeder virtuelle Host und jeder
Prozess hat bereits seine eigene Umgebung.

**Nie auf einer Website, die keine Demo ist.** Eine Site-envvars-Datei gehört zu einer Installation; kopieren Sie die
einer Demo nie auf eine andere Website.

**Setzen Sie sie nie** in einem gemeinsam genutzten PHP-FPM-Pool, einem serverweiten `SetEnv`, einem globalen
`/etc/environment`, einem Basis-Image für Container oder einer Deploy-Vorlage, die für andere Websites verwendet wird.

## Banner und Protokoll

- **Banner:** `design/admin4/templates/parts/exp_demo_mode_banner.tpl` (admin4, und admin4l über seinen Rückfall) und
  dieselbe Datei in `design/admin` (die klassische Administration, und admin3 über seinen Rückfall). admin4 zeichnet es
  oben im Hauptbereich (`page_mainarea.tpl`, unter dem festen Kopf), admin4l außerdem vor der Hauptzone eines
  Admin-Layouts, admin3 als eigenes Design oben in seinem eigenen Hauptbereich
  (`design/admin3/templates/page_mainarea.tpl`), die klassische Administration unter ihrem Kopf; immer außerhalb der Cache-Blöcke, sodass es der Umgebung jeder Anfrage folgt. Es
  verwendet die gemeinsame Komponente `.a4-notice.is-warn` aus `components.css` (Tokens mit hellen und dunklen Werten;
  kein eigenes CSS). Der Template-Operator `exp_demo_mode()` gibt zurück, ob der Modus eingeschaltet ist; ein eigenes
  Design kann das Banner mit `{include uri='design:parts/exp_demo_mode_banner.tpl'}` anzeigen.
- **Protokoll:** Zu Beginn der ersten Webanfrage (`ezpKernelWeb::requestInit()`) und jedes Skripts
  (`eZScript::initialize()`) schreibt jeder Prozess eine Zeile in `var/log/security.log`, im Format der Zeilen des
  Anfrage-Klassifizierers, sodass **Setup > Logdateien** sie als `demo/mode` gruppiert:

  ```
  [ Oct 19 2026 18:00:00 ] [203.0.x.x] demo/mode on pid=4711 sapi=fpm-fcgi EXP_DEMO_MODE=1: administrator passwords cannot be changed or reset, the installers accept a well-known administrator password; never use it in production
  ```

  und verzeichnet das Audit-Ereignis `system.demo.mode` (Schweregrad warning, Kanal system, immer eingeschaltet). Ein
  geforkter Worker hat seine eigene PID und verzeichnet es erneut; ein persistenter Worker verzeichnet es einmal. Ein
  Webprozess, der jede Anfrage neu beginnt (PHP-FPM, mod_php, LiteSpeed, der eingebaute Server von PHP), legt je Prozess
  eine leere Markierungsdatei im Cache-Verzeichnis an (`<cache>/demo-mode/<pid>-<start time>`) und verzeichnet es so
  ebenfalls einmal statt bei jeder Anfrage; nach dem Leeren der Caches verzeichnen die laufenden Prozesse es erneut. Ein
  Konsolenbefehl meldet außerdem `Demo mode is on (EXP_DEMO_MODE=1) ...` auf der Standardfehlerausgabe.

Auf einer Produktivsite bedeutet ein Ereignis `system.demo.mode` oder eine Zeile `demo/mode`, dass die Variable einen
Prozess erreicht hat, den sie nicht hätte erreichen dürfen: Entfernen Sie sie sofort und prüfen Sie, wer die Umgebung
des Servers geändert hat.

## Risiken

- **Der Demo-Modus sagt allen, dass das Administratorpasswort bekannt ist.** Betreiben Sie ihn nur auf einer
  Installation, deren Inhalte, Benutzer und Einstellungen alle wegwerfbar sind: keine echten Kundendaten, keine echten
  E-Mail-Adressen, keine Geheimnisse in Einstellungen, keine Verbindung zu anderen Systemen.
- **Administratoren behalten jede andere Befugnis.** Ein als Administrator angemeldeter Besucher kann Konten anlegen,
  Rollen ändern, Einstellungen in der Administration bearbeiten, Pakete installieren und Dateien hochladen. Der
  Demo-Modus schränkt das nicht ein. Bauen Sie die Demo nach Zeitplan neu, halten Sie sie auf einem eigenen Host oder
  Konto und geben Sie ihr keine Zugangsdaten, die anderswo funktionieren.
- **Ein bekanntes Passwort wird installiert.** Das ist der Zweck und der Grund, warum der Modus nie eine echte Website
  erreichen darf.
- **E-Mail.** Besucher können E-Mails auslösen (Registrierung, Benachrichtigungen). Senden Sie die E-Mails der Demo in
  eine Datei oder eine Senke, nie an echte Adressen.
- **Eine verlorene Variable schaltet ihn stillschweigend aus.** Ein Neustart ohne die Variable betreibt die Demo mit
  ausgeschaltetem Demo-Modus: Besucher können das Administratorpasswort dann wieder ändern. Setzen Sie sie dort, wo sie
  Neustarts überdauert, und achten Sie auf das Banner.
- **Nur Administratoren sind geschützt.** Andere veröffentlichte Anmeldungen (Redakteur, Mitglied) kann weiterhin
  ändern, wer sie verwendet.
- **Eine gemeinsame Umgebung erreicht jede Website darin.** Ein serverweites `SetEnv`, ein gemeinsam genutzter
  PHP-FPM-Pool, `/etc/environment`, ein Basis-Image für Container oder die Shell eines Betreibers, der die Variable
  exportiert hat, schaltet den Demo-Modus für jede Installation ein, die diese Umgebung erreicht; deren Administratoren
  können dann ein kompromittiertes Passwort nicht mehr ändern. Für Velocity lehnt der Kernel die gemeinsame
  `/etc/vc/envvars` selbst ab ([Velocity: nur eine Installation](#velocity-nur-eine-installation)); für die anderen
  setzen Sie sie nur pro Installation. Führen Sie `exp:velocity start`, `exp:install` oder den Kickstarter für eine
  andere Website nie aus einer Shell aus, die die Variable hat.

## Das Demo-Installationsprogramm auf dem Demo-Host

Die öffentliche Demo wird alle zwei Stunden aus dem aktuellen Code neu gebaut, von einem Skript, das `exp:install` mit
SQLite ausführt und die neue Website einwechselt. Mit dem Demo-Modus sollte es die Variable zweimal setzen:

1. **Für die Installation,** sodass `exp:install` das veröffentlichte Demo-Passwort unverändert installiert, statt es
   durch ein zufälliges zu ersetzen und danach zurückzusetzen:

   ```sh
   EXP_DEMO_MODE=1 php bin/php/install.php --apply --db=sqlite --password='<the published demo password>' ...
   ```

   Der Schritt zum Zurücksetzen nach der Installation (`exp:resetuserpassword`) wird dann nicht mehr gebraucht und
   im Demo-Modus ohnehin abgelehnt. Jedes nicht leere Passwort funktioniert, auch das siebenstellige `publish`.
2. **Für die laufende Website,** in der Umgebung des PHP der Demo (ihr PHP-FPM-Pool, die Umgebung von `lsphp` unter
   LiteSpeed oder das `SetEnv` des virtuellen Hosts) und in der Crontab des Benutzers der Demo, sodass das Banner
   erscheint und das Administratorpasswort zwischen zwei Neubauten nicht geändert werden kann. Der Preloader, der nach
   dem Einwechseln läuft (`bin/php/preload.php`), braucht keine Variable.

Die Variable gehört nur zum eigenen Benutzer und virtuellen Host der Demo. Das Skript darf sie nicht in `config.php`,
`settings/` oder irgendetwas anderes schreiben, das auf andere Websites kopiert wird.

So läuft die immer aktuelle Demo des Projekts seit dem 19. Oktober 2026, unter LiteSpeed mit `lsphp`: Ihr Build-Skript
exportiert `EXP_DEMO_MODE=1` für jeden Schritt, `exp:install` übernimmt das veröffentlichte Passwort wie angegeben, ein
generiertes Passwort lässt den Build fehlschlagen (der alte Schritt zum Zurücksetzen ist entfernt), und die
`.htaccess` jedes Builds erhält `SetEnv EXP_DEMO_MODE 1`; `security.log` zeigt `demo/mode on ... sapi=litespeed`, und
die Seiten zur Anmeldung und für vergessene Passwörter zeigen den Hinweis. Die allgemeine Form:
[Rezept: eine öffentliche Demo unter LiteSpeed und Plesk betreiben](../../hilfe/hosting/server/rezept-eine-oeffentliche-demo-auf-litespeed-und-plesk-betreiben.md)
und [`install-demo-sqlite.sh`](../../../eng-us/examples/demo-mode/install-demo-sqlite.sh).

## Für Entwickler

- `expDemoMode::enabled()`: Der Modus ist eingeschaltet (`EXP_DEMO_MODE` ist genau `1`); bei jedem Aufruf gelesen.
- `expDemoMode::protectsPassword( $user )`: Der Modus ist eingeschaltet, und das Konto (ein `eZUser` oder eine ID
  eines Inhaltsobjekts) ist ein Administrator. Fragen Sie es, bevor Sie ein Passwort ändern, und zeigen Sie
  `expDemoMode::refusalText()`.
- `expDemoMode::refused( $user, $path, $event )`: verzeichnet eine Ablehnung (Audit-Protokoll und Debug-Ausgabe).
- `expDemoMode::installing( $callable )`: führt die Arbeit eines Installationsprogramms aus, die das
  Administratorpasswort setzt; der Speicherschutz lässt sie durch. Der Geltungsbereich endet mit dem Aufruf, auch bei
  einer Ausnahme. Ein eigenes Installationsprogramm, das das Administratorpasswort nach den Schritten des Kernels setzt,
  muss es verwenden, sonst wird seine Änderung im Demo-Modus abgelehnt.
- `expDemoMode::guardStore( $user )`: von `eZUser::store()` aufgerufen; behält das gespeicherte Passwort eines
  Administrators, solange der Modus eingeschaltet ist. Code, der `ezuser.password_hash` mit eigenem SQL schreibt,
  umgeht das: Tun Sie das nicht.
- Der Template-Operator `exp_demo_mode()` (`expDemoModeOperator`).
- Prüfen Sie den Modus mit `class_exists( 'expDemoMode' ) && expDemoMode::enabled()` in Code, der in einem persistenten
  Worker laufen kann, der gestartet wurde, bevor die Klasse existierte.

## Kompatibilität

- Ohne `EXP_DEMO_MODE=1` ändert sich nichts: keine Einstellung, keine Datenbankänderung, kein neuer Standard.
- Neue Klassen: `expDemoMode` (`kernel/classes/expdemomode.php`), `expDemoModeOperator`
  (`kernel/common/expdemomodeoperator.php`); erzeugen Sie die Autoloads des Kernels neu, wenn Sie durch Kopieren von
  Dateien aktualisieren. Exponential Velocity lädt Klassen beim Start: Führen Sie nach dem Update
  `exp:velocity deploy --kernel` aus (oder starten Sie es neu).
- Neues Audit-Ereignis `system.demo.mode`; die vorhandenen Ereignisse `access.user.password.change.failed` und
  `access.user.password.reset.failed` können den Grund `demo_mode` haben.
- Neue Template-Variablen (alle optional, sodass ein älteres Design weiterhin rendert): `demo_mode_locked` und
  `demo_mode_text` in `user/password.tpl`, `demo_mode_text` und `demo_mode_refused` in `user/forgotpassword.tpl`,
  `demo_mode` in `setup/init/site_admin.tpl`.
- Die Operationen `user/password` und `user/forgotpassword` können `status => false` mit `reason => demo_mode`
  zurückgeben.
- Die Webdienste können für eine Änderung oder ein Zurücksetzen des Passworts eines Administrators mit HTTP 403
  antworten.
- Exponential Velocity: `EXP_DEMO_MODE` in der gemeinsamen `/etc/vc/envvars` schaltet den Demo-Modus nicht mehr ein
  (früher galt das für jede Installation auf dem Host); verwenden Sie die Site-envvars
  `/etc/vc/sites-available/<site>.envvars`, die `exp:velocity start` liest, oder die Umgebung, die den Server dieser
  Installation startet. `exp:velocity layout` zeigt die Zeile `site envvars`.
- Neues Template `design/admin3/templates/page_mainarea.tpl` (das Banner für admin3 als eigenes Design): Leeren Sie
  nach einem Update durch Kopieren von Dateien den Cache der Template-Overrides.

## Tests

- `tests/tests/kernel/classes/user/expDemoModeTest.php` (ohne Datenbank): die Regeln für den Wert, getenv vor
  `$_SERVER` und nie die Anfrage, kein Caching, wer Administrator ist, die Entscheidung des Speicherschutzes, der
  Geltungsbereich von `installing()`, die Lockerung der Installationsprogramme und ihre Grenzen, die Aufzeichnung einmal
  pro Prozess, der Template-Operator, das Banner (ein Teil-Template, gemeinsame Komponente, kein Inline-CSS, außerhalb
  jedes Cache-Blocks der drei Pagelayouts), jeder Weg zum Passwort, der Audit-Katalog und die Kataloge.
- `expAuditRecordTest` zählt das neue Audit-Ereignis.

## Wo der Demo-Modus sonst dokumentiert ist

Jede Seite unten sagt, was sie über den Demo-Modus sagen muss, und verlinkt hierher zurück.

- **Für jede Zielgruppe:** [Demo-Modus](../../hilfe/demomodus.md) (Administratoren), [FAQ zum Demo-Modus](../../hilfe/demomodus-faq.md),
  [Demo-Modus für Hoster](../../hilfe/hosting/sicherheit/demomodus-fuer-hoster.md),
  [Demo-Modus für Entwickler](../../hilfe/erweiterungsentwicklung/teil-8-referenz/demomodus-fuer-entwickler.md).
- **Rezepte und Fehlerbehebung:** [Rezept: eine öffentliche Demo unter LiteSpeed und Plesk betreiben](../../hilfe/hosting/server/rezept-eine-oeffentliche-demo-auf-litespeed-und-plesk-betreiben.md),
  [Runbook: Probleme mit dem Demo-Modus](../../hilfe/hosting/runbooks/probleme-mit-dem-demomodus.md),
  [Beispiele zum Demo-Modus](../../beispiele/demomodus/README.md) (`.htaccess`, virtueller Host, PHP-FPM-Pool, nginx,
  systemd-Drop-in, Crontab, Container-Umgebung, ein Skript für den Neubau), [Umgebungsvariablen](../../anleitungen/umgebungsvariablen.md).
- **Installation:** [der Überblick](../../INSTALL.md), [der Index der Kapitel](../../installation/README.md),
  [2 Anforderungen](../../installation/02-voraussetzungen.md), [4 Eine Installationsmethode wählen](../../installation/04-installationsweg-waehlen.md),
  [5 Setup-Assistent](../../installation/05-setup-assistent.md), [6 Kickstarter](../../installation/06-kickstarter.md),
  [7 exp:install](../../installation/07-installation-ueber-die-konsole.md), [8 Die Website ausliefern](../../installation/08-die-website-ausliefern.md),
  [9 Datenbanken](../../installation/09-datenbanken.md), [10 Nach der Installation](../../installation/10-nach-der-installation.md),
  [11 Aktualisieren](../../installation/11-aktualisieren.md), [12 Fehlerbehebung](../../installation/12-fehlerbehebung.md),
  [13 Sicherheitshärtung](../../installation/13-sicherheitshaertung.md), [Mit einem Befehl installieren](../../funktionen/6.0/installation-mit-einem-befehl.md).
- **Upgrade und Einstellungen:** [Aktualisieren](../../anleitungen/aktualisieren.md), [Umgebungseinstellungen](../../funktionen/6.0/umgebungseinstellungen.md),
  `config.php-RECOMMENDED` und `config.env.php.example` (der Demo-Modus ist keine Konstante),
  [Bekannte Passwörter werden abgelehnt](schwache-passwoerter-und-gueltigkeit-von-reset-links.md).
- **Benutzer, Passwörter und Rollen:** [Ihr Passwort ändern](../../funktionen/6.0/moderne-passwortaenderung.md),
  [Ein Benutzerpasswort zurücksetzen](../../funktionen/6.0/benutzerpasswort-zuruecksetzen.md), [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md),
  [Das Administratorpasswort ändern](../../hilfe/administratorpasswort-aendern.md),
  [Das Standard-Administratorpasswort](../../hilfe/standard-administratorpasswort.md),
  [Konten und Anmeldung](../../hilfe/konten-und-anmeldung.md), [In der Administration anmelden](../../hilfe/anmelden-in-der-administration.md).
- **Sicherheit, Audit und Logs:** [Audit](audit.md), [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md),
  [Log-Viewer](../../funktionen/6.0/log-betrachter.md), [Sicherheitshärtung](../../hilfe/sicherheitshaertung.md),
  [Härtung für Hoster](../../hilfe/hosting/sicherheit/haertung-fuer-hoster.md),
  [Audit-Protokoll für Hoster](../../hilfe/hosting/sicherheit/pruefprotokoll.md), [security.log](../../hilfe/hosting/betrieb/sicherheitslog.md),
  [Erste Anmeldung und Standardpasswörter](../../hilfe/hosting/sicherheit/erste-anmeldung-und-standardpasswoerter.md).
- **Server, Panels und Cron:** [Velocity-Engines](velocity-engines.md), [Velocity für Hoster](../../hilfe/hosting/server/velocity-fuer-hoster.md),
  [LiteSpeed](../../hilfe/hosting/server/litespeed-und-openlitespeed.md), [Apache mit PHP-FPM](../../hilfe/hosting/server/apache-mit-php-fpm.md),
  [nginx mit PHP-FPM](../../hilfe/hosting/server/nginx-mit-php-fpm.md), [FrankenPHP](../../hilfe/hosting/server/frankenphp.md),
  [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md),
  [PHP-Auswahl und FPM-Pools](../../hilfe/hosting/control-panels/php-auswahl-und-fpm-pools.md),
  [Cron und Shell-Zugang](../../hilfe/hosting/control-panels/cron-und-shell-zugang.md), [Cronjobs](../../hilfe/hosting/betrieb/cronjobs.md),
  [Cronjobs](../../anleitungen/cronjobs.md), [Container](../../hilfe/hosting/modelle/container-docker-und-podman.md),
  [SQLite für Hoster](../../hilfe/hosting/grundlagen/sqlite-fuer-hoster.md), die Checklisten für Hoster
  ([neuer Server](../../hilfe/hosting/checklisten/checkliste-fuer-einen-neuen-server.md), [Go-live](../../hilfe/hosting/checklisten/checkliste-fuer-den-start-einer-kunden-website.md),
  [Sicherheitsprüfung](../../hilfe/hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)) und die Bereitstellung mit
  [exp:install](../../hilfe/hosting/bereitstellung/exp-install-fuer-hoster.md), [dem Kickstarter](../../hilfe/hosting/bereitstellung/der-kickstarter-fuer-hoster.md)
  und [dem Setup-Assistenten](../../hilfe/hosting/bereitstellung/der-setup-assistent-auf-shared-hosting.md).
- **Webdienste und Entwickler:** [Dienste](backend-dienste-ueber-ezjscore.md), [REST-API](../../anleitungen/rest-api-v1-und-v2.md),
  [expservices](../../hilfe/erweiterungsentwicklung/teil-8-referenz/expservices.md),
  [eZUser und Zugriff](../../hilfe/erweiterungsentwicklung/teil-8-referenz/ezuser-und-zugriff.md),
  [Konsolenbefehle](../../hilfe/erweiterungsentwicklung/teil-8-referenz/konsolenbefehle-fuer-entwickler.md).
- **Glossare:** [Glossar](../../glossar.md), [für Hoster](../../hilfe/hosting/glossar-fuer-hoster.md),
  [für Erweiterungsentwickler](../../hilfe/erweiterungsentwicklung/teil-8-referenz/glossar-fuer-erweiterungsentwickler.md),
  [für Fürsprecher](../../hilfe/fuersprecher/glossar-fuer-fuersprecher.md), [für Partner](../../hilfe/partner/glossar-fuer-partner.md).
- **Presse, Fürsprecher, Trainer, Partner und Vertrieb:** [Eine Demo-Installation einrichten](../../hilfe/presse/eine-demo-installation-einrichten.md),
  [Presse: hier beginnen](../../hilfe/presse/presse-hier-beginnen.md), [Sicherheit bei Live-Demos](../../hilfe/fuersprecher/sicherheit-bei-live-demos.md),
  [Fürsprecher: hier beginnen](../../hilfe/fuersprecher/fuersprecher-hier-beginnen.md), [FAQ für Neulinge](../../hilfe/fuersprecher/faq-fuer-neue.md),
  [Trainer: hier beginnen](../../hilfe/trainer/trainer-hier-beginnen.md), [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md),
  [Vertrieb und Marketing: hier beginnen](../../hilfe/geschaeft/vertrieb-und-marketing-hier-beginnen.md), [Demo-Skripte](../../hilfe/geschaeft/demo-drehbuecher.md),
  [Demo-Websites für Designer](../../hilfe/designer/veroeffentlichung/demo-websites.md).

## Verwandte Seiten

- [Demo-Modus](../../hilfe/demomodus.md) (Administratoren), [Demo-Modus für Hoster](../../hilfe/hosting/sicherheit/demomodus-fuer-hoster.md),
  [Demo-Modus für Entwickler](../../hilfe/erweiterungsentwicklung/teil-8-referenz/demomodus-fuer-entwickler.md),
  [FAQ zum Demo-Modus](../../hilfe/demomodus-faq.md), [Probleme mit dem Demo-Modus](../../hilfe/hosting/runbooks/probleme-mit-dem-demomodus.md)
- [Bekannte Passwörter werden abgelehnt](schwache-passwoerter-und-gueltigkeit-von-reset-links.md)
- [Kapitel 13: Sicherheitshärtung für die Produktion](../../installation/13-sicherheitshaertung.md)
- [Eine Demo-Installation einrichten](../../hilfe/presse/eine-demo-installation-einrichten.md)
