# 7. Die Installation über die Konsole: `exp:install`

`exp:install` installiert Exponential mit einem einzigen Befehl, ohne dass Sie vorher eine Datei schreiben müssen. Der
Befehl wandelt seine Optionen in dieselbe Konfiguration um, die eine `kickstart.ini` enthalten würde, führt die
Installationsschritte des Kickstarters im selben Prozess aus und gibt eine Zusammenfassung mit der Adresse der Site,
der Anmeldung an der Administration und dem Passwort des Administrators aus. Jede Option hat einen Standardwert, sodass
`php bin/php/console exp:install` allein das Site-Paket `sevenx_multisite` auf SQLite installiert.

Dieses Kapitel beschreibt jede Option mit ihrem Standardwert und ihren Prüfungen, den Probelauf- und den
Ausgabemodus, den Exit-Status, vollständige Beispiele für jedes Datenbanksystem, wie das Passwort des Administrators
gewählt wird, was der Befehl wohin schreibt, was schiefgehen kann und die anderen Konsolenbefehle, die beim Einrichten
einer Installation wichtig sind.

Verwenden Sie `exp:install`, wenn Sie eine Installation über die Kommandozeile möchten und keine Konfigurationsdatei
aufbewahren müssen: auf einem Laptop, einem Testsystem, in einem Container, in einem CI-Job. Wenn Sie die
Konfiguration als Datei haben möchten, die Sie prüfen, sicher aufbewahren und erneut ausführen können, verwenden Sie den
Kickstarter ([Kapitel 6](06-kickstarter.md)); `exp:install` führt dieselben Schritte aus, das Ergebnis ist also
dasselbe.

[Inhalt](README.md) · Zurück: [6. Der Kickstarter](06-kickstarter.md) · Weiter: [8. Die Site ausliefern](08-die-website-ausliefern.md)

---

## 7.1 Die Konsole im Überblick

`bin/php/console` ist die Befehlskonsole von Exponential. Sie führt keine Liste der Befehle, sondern ermittelt sie zur
Laufzeit aus den vorhandenen Skripten:

| Skript | Befehlsname |
|---|---|
| `bin/php/<name>.php` | `exp:<name>` |
| `bin/shell/<name>.sh` | `shell:<name>` |
| `bin/<name>.sh`, `bin/<name>.php` | `bin:<name>` |
| `extension/<ext>/bin/php/<name>.php` | `ext:<ext>:<name>` |
| `extension/<ext>/bin/shell/<name>.sh` | `ext:<ext>:sh:<name>` |

Ein Skript kann in seinem Kopf einen kurzen Alias angeben (`@alias vc` sorgt dafür, dass `exp:vc` das Skript
`bin/php/velocity.php` ausführt). Ein Name ohne Namensraum wird zuerst als `exp:`, dann als `shell:`, dann als `bin:`
versucht, sodass `php bin/php/console install` ebenfalls `exp:install` ausführt. Ein unbekannter Name gibt „Command ...
was not found“ aus, schlägt den nächstliegenden vorhandenen Befehl vor und endet mit 1.

```bash
php bin/php/console list               # every command
php bin/php/console list exp           # the exp: namespace only
php bin/php/console help exp:install   # describes the command, then runs it with --help
php bin/php/console --version          # Exponential's version, build, copyright and license
```

Bevor die Konsole übergibt, gibt sie auf der Standardfehlerausgabe eine Zeile aus, die den Befehl und das ausgeführte
Skript nennt:

```text
  ▶  running exp:install  →  bin/php/install.php
```

Diese Zeile geht auf die Standardfehlerausgabe und landet daher nie in einer Ausgabe, die Sie in eine Datei oder an ein
anderes Programm weiterleiten.

`exp:install` ist also `bin/php/install.php`, dessen Klasse in `kernel/private/classes/commands/install.php` liegt.
Beide Formen nehmen dieselben Optionen an und verhalten sich gleich; die Konsole fügt nur die obige Zeile hinzu:

```bash
php bin/php/console exp:install --dry-run
php bin/php/install.php --dry-run
```

Viele Installationen legen im Wurzelverzeichnis eine Verknüpfung an, `ln -s bin/php/console console`; danach ist
`./console exp:install` derselbe Befehl. Die Verknüpfung gehört nicht zu den ausgelieferten Dateien; legen Sie sie
selbst an, wenn Sie sie möchten. Führen Sie die Konsole im Wurzelverzeichnis der Installation aus, also in dem
Verzeichnis, das `index.php` enthält. Mehr dazu: [Die Konsole](../bc/6.0/konsole.md).

## 7.2 Was `exp:install` tut

Der Befehl arbeitet in dieser Reihenfolge. Wer sie kennt, versteht jede Meldung, die er ausgeben kann, und weiß, warum
ein fehlgeschlagener Lauf das Verzeichnis so hinterlässt, wie er es vorgefunden hat.

1. **Die Optionen einlesen und prüfen.** Jede Option hat die Form `--name=wert`; Schalter haben keinen Wert. Alles,
   was nicht die Form `--name` oder `--name=wert` hat (ein einzelnes Wort, eine Kurzoption wie `-f`), beendet den
   Befehl mit „Unknown argument: ... (see --help)“. Ein unbekannter Optionsname, eine Option ohne ihren Wert oder ein
   Schalter mit einem Wert beenden ihn mit „Unknown option or missing value: ... (see --help)“. Danach wird jeder Wert
   geprüft ([7.3](#73-optionen)). Bis hierher wurde nichts geschrieben.
2. **Das Passwort des Administrators wählen** ([7.5](#75-das-passwort-des-administrators)).
3. **Die Konfiguration erstellen**: die elf Abschnitte der `kickstart.ini`, jeder mit `Continue=true`, sodass jeder
   Schritt ohne Rückfrage ausgeführt wird ([7.4](#74-so-sieht-die-konfiguration-aus)).
4. Mit `--print`: die Konfiguration mit maskierten Passwörtern anzeigen und anhalten. Das geschieht vor der nächsten
   Prüfung, daher funktioniert `--print` auch in einem Verzeichnis, das bereits eine Installation enthält.
5. **Das Überschreiben verweigern**: Wenn `settings/override/site.ini.append.php` einen Abschnitt `[DatabaseSettings]`
   enthält, liegt in dem Verzeichnis bereits eine Installation; ohne `--force` (oder `--dry-run`) hält der Befehl hier
   an.
6. **Die Konfiguration bereitlegen**: Eine vorhandene `kickstart.ini` wird nach
   `var/log/kickstart.ini.before-exp-install-<YYYYmmdd-HHMMSS>` verschoben, und die erzeugte wird mit dem Modus `0600`
   als `kickstart.ini` geschrieben, weil sie die Passwörter im Klartext enthält. Ein erzeugtes Administratorpasswort
   wird an dieser Stelle in `var/log/initial-admin-password` festgehalten (nicht bei einem Probelauf).
7. **Den Kickstarter ausführen** (`expKickstarter`) mit `--force` oder mit `--dry-run`. Er schaltet den Wartungsmodus
   ein, führt jeden Schritt von `Welcome` bis `Final` aus und schaltet seinen eigenen Wartungsmodus wieder aus
   ([Kapitel 6](06-kickstarter.md)). Ein Wartungsfenster, das schon vor dem Lauf geöffnet war, bleibt unverändert und
   ist auch danach noch geöffnet.
8. **Aufräumen, was auch immer geschehen ist**: Die Konfiguration wird mit maskierten Passwörtern als
   `var/log/exp-install-<YYYYmmdd-HHMMSS>.ini` gespeichert, die erzeugte `kickstart.ini` wird gelöscht und die frühere
   wieder an ihren Platz gelegt. Das geschieht beim Ende des Prozesses, also auch dann, wenn ein Schritt fehlschlägt.
9. **Die Zusammenfassung ausgeben**, aber nur, wenn der Lauf `settings/override/site.ini.append.php` mit einem
   Abschnitt `[DatabaseSettings]` neu geschrieben hat. Diese Datei ist das Letzte, was eine Installation schreibt; ihr
   Vorhandensein mit einer frischen Änderungszeit ist daher der Beweis, dass die Installation durchgelaufen ist.

## 7.3 Optionen

Jeder Wert wird geprüft, bevor etwas geschrieben wird. Die zitierten Texte sind die genauen Meldungen; jede beendet den
Befehl mit dem Exit-Status 1.

### Datenbank

> **Unterstützte Datenbanken (Exponential 6.0.x), nach Präferenz geordnet:** 1. SQLite 3 (Standard) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlichem
> Aufwand möglich. Einzelheiten: [Datenbankunterstützung](../hilfe/datenbankunterstuetzung.md).

| Option | Standardwert | Bedeutung und Prüfungen |
|---|---|---|
| `--db=<type>` | `sqlite` | `sqlite`, `mysql`, `pgsql`, `mongodb` oder `oracle`. Ebenfalls akzeptiert: `sqlite3`; `mysqli`, `mariadb`; `postgresql`, `postgres`; `mongo`; `oci8`, `ezoracle`. Alles andere: „Unknown database type '...': use sqlite, mysql, pgsql, mongodb or oracle“ |
| `--db-host=<host>` | `localhost` | der Datenbankserver |
| `--db-port=<port>` | `3306` (mysql), `5432` (pgsql), `27017` (mongodb), `1521` (oracle), keiner (sqlite) | |
| `--db-name=<name>` | `exponential`; `exponential.db` für sqlite; `FREEPDB1` für oracle | die Datenbank. SQLite: ein Dateiname in `var/storage/sqlite3/`. Oracle: Ein Dienstname wird zum Easy-Connect-String `<db-host>:<db-port>/<service>`; ein Wert mit `/` oder `(` wird unverändert verwendet; `@alias` nennt einen TNS-Alias |
| `--db-user=<user>` | `root` (mysql), `postgres` (pgsql), sonst keiner | |
| `--db-password=<pass>` | keiner | wenn nicht angegeben, wird die Umgebungsvariable `EXP_INSTALL_DB_PASSWORD` verwendet |
| `--db-socket=<path>` | keiner | ein lokaler MySQL-Socket statt Host und Port |
| `--db-action=<action>` | `remove` | `remove`, `ignore` oder `skip`, wie `DatabaseAction` in [Kapitel 6](06-kickstarter.md#65-databaseaction-lesen-sie-dies-bevor-sie-run-ausführen). Alles andere: „--db-action must be remove, ignore or skip“. **`remove` leert die Datenbank zuerst** |

Verwenden Sie lieber `EXP_INSTALL_DB_PASSWORD` als `--db-password`: Eine Option ist, solange die Installation läuft,
für jeden Benutzer des Rechners in der Prozessliste (`ps`) sichtbar, und sie bleibt im Verlauf der Shell stehen. Die
Variable ist an keiner dieser Stellen sichtbar.

Die Standardwerte `root` und `postgres` gibt es nur, damit ein Test auf einem Laptop ohne Optionen funktioniert. Legen
Sie für jede Site, auf die es ankommt, einen eigenen Datenbankbenutzer nur für Exponential an
([Kapitel 9](09-datenbanken.md)) und übergeben Sie ihn mit `--db-user`.

### Site

| Option | Standardwert | Bedeutung und Prüfungen |
|---|---|---|
| `--package=<name>` | `sevenx_multisite` | das Site-Paket |
| `--language=<locale>` | `eng-US` | die Hauptsprache |
| `--languages=<a,b>` | keiner | weitere Sprachen, durch Kommas getrennt, z. B. `ger-DE,fre-FR` |
| `--title=<text>` | `Exponential` | der Name der Site |
| `--organisation-name=<text>` | leer, das heißt der Name der Site | wer die optionalen E-Mails versendet (die Fußzeile von Newslettern und Benachrichtigungen) |
| `--organisation-address=<text>` | leer | die Postanschrift des Absenders; `\n` trennt die Zeilen; die Statusseite der E-Mail-Einstellungen warnt, solange sie leer ist |
| `--url=<url>` | `http://localhost`; bei Host-Zuordnung `http://<--host>` | wo die Site liegt; `http://` wird ergänzt, wenn kein Schema angegeben ist. Setzen Sie diese Option: Die Adresse jedes Siteaccess wird daraus gebildet |
| `--access=<type>` | `url`; `host`, wenn `--host` oder `--admin-host` angegeben ist | wie Siteaccesses zugeordnet werden: `url` (auch `uri`), `host` (auch `hostname`) oder `port`. Alles andere: „--access must be url, host or port“ |
| `--site-access=<name>` | `site` | bei `url`: der Pfad des öffentlichen Siteaccess. Kleinbuchstaben, Ziffern und Unterstriche („--site-access must be lower-case letters, digits and underscores“) |
| `--admin-access=<name>` | `admin` | bei `url`: der Pfad des Admin-Siteaccess; dieselben Zeichen; muss sich von `--site-access` unterscheiden („--site-access and --admin-access must differ“) |
| `--host=<host>` | keiner | bei `host`: der öffentliche Hostname |
| `--admin-host=<host>` | keiner | bei `host`: der Hostname der Administration. `--access=host` benötigt beide („--access=host needs both --host and --admin-host“), und sie müssen sich unterscheiden („--host and --admin-host must differ“) |
| `--port=<port>` | `8080` | bei `port`: der öffentliche Port |
| `--admin-port=<port>` | `8081` | bei `port`: der Port der Administration; muss sich von `--port` unterscheiden („--port and --admin-port must differ“) |
| `--editor-access=<name>` | `editor` (`<--site-access>_editor`, wenn dieser Name vergeben ist) | bei `url`: der Pfad des Editor-Siteaccess; dieselben Zeichen wie bei `--site-access` („--editor-access must be lower-case letters, digits and underscores“) |
| `--editor-host=<host>` | `edit.<--host ohne www.>` (`editor.<...>`, wenn dieser Name vergeben ist) | bei `host`: der Hostname des Editors |
| `--editor-port=<port>` | `--port` + 2 (`8082`) | bei `port`: der Port des Editors, nur Ziffern („--editor-port must be a port number“) |

Der Wert des Editors muss sich vom öffentlichen Wert und vom Wert der Administration der jeweiligen Zuordnungsart
unterscheiden („--editor-port must differ from --port and --admin-port“ usw.); die Standardwerte tun das immer.

Warum Sie `--url` auch für einen Test setzen sollten: Die Installation schreibt die Adresse jedes Siteaccess in die
Einstellungen, und die Adressen in Links, in E-Mails und in der Zusammenfassung werden daraus gebildet. Ohne die Option
zeigen sie alle auf `http://localhost`, was nur dann stimmt, wenn Sie die Site auf demselben Rechner öffnen.

Wie bei jeder Installationsmethode lauten die Siteaccess-Verzeichnisse `settings/siteaccess/site`, `admin` und
`editor`; `--site-access`, `--admin-access` und `--editor-access` wählen die URL-Pfade, die zu ihnen führen; siehe
[7.8](#78-der-editor-siteaccess-mit-expinstall). Wenn `extension/exp_adminui` in der Installation vorhanden ist, wird
außerdem `adminui` aus dem Admin-Siteaccess angelegt und ist unabhängig von `--access` über den Pfad `/adminui`
erreichbar; die Zeile `Siteaccesses` der Zusammenfassung endet dann mit `adminui (by uri)`. Siehe
[Exponential Admin UI](../funktionen/6.0/exp-adminui.md).

### Administrator (Anmeldename `admin`)

| Option | Standardwert | Bedeutung und Prüfungen |
|---|---|---|
| `--email=<address>` | `nospam@exponential.earth` | muss eine gültige Adresse sein („--email is not an e-mail address: ...“). Geben Sie eine echte an: Sie wird zu `[MailSettings] AdminEmail` |
| `--password=<pass>` | erzeugt | siehe [7.5](#75-das-passwort-des-administrators). Ein leerer Wert wird abgelehnt: „--password cannot be empty (leave it out to have one generated)“ |
| `--random-password` | aus | das Passwort immer erzeugen, auch wenn `--password` angegeben ist |
| `--first-name=<name>` | `Administrator` | |
| `--last-name=<name>` | `User` | |

Der Anmeldename ist immer `admin`. Ändern Sie ihn oder legen Sie einen persönlichen Administrator an und deaktivieren Sie
`admin` nach der Installation ([Kapitel 10](10-nach-der-installation.md)).

### Ausführung

| Option | Bedeutung |
|---|---|
| `--force` | über eine vorhandene Installation installieren: Ihre Datenbank (mit `--db-action=remove`) und ihre Einstellungen werden ersetzt |
| `--dry-run` | die Konfiguration und die Pakete prüfen, nichts installieren ([7.6](#76-probelauf-und-ausgabe)) |
| `--print` | die Konfiguration, die verwendet würde, mit maskierten Passwörtern anzeigen und anhalten |
| `--allow-root-user` | wird akzeptiert und an den Installationslauf weitergegeben, der es ebenfalls akzeptiert. `--help` führt diese Option nicht auf. Ein Lauf als root hinterlässt trotzdem Dateien, die root gehören; verwenden Sie lieber den Benutzer des Webservers |
| `--help` | alle Optionen |

Werte dürfen keine Zeilenumbrüche enthalten („A value for *section*/*key* contains a line break“); verwenden Sie `\n`
innerhalb von `--organisation-address`.

Führen Sie den Befehl als der Benutzer aus, unter dem das PHP des Webservers läuft. Die Installation legt Dateien in
`var/` und `settings/` an; Dateien, die `root` angelegt hat, kann der Webserver später nicht ändern, was sich beim
ersten Seitenaufruf als Fehler zeigt ([Kapitel 12](12-fehlerbehebung.md#123-berechtigungen-und-eigentümerschaft)). Zum Beispiel:

```bash
sudo -u www-data php bin/php/console exp:install --url=https://www.example.com
```

## 7.4 So sieht die Konfiguration aus

`--print` zeigt genau das, was der Lauf in die `kickstart.ini` schreiben würde, wobei jedes nicht leere Passwort durch
`***` ersetzt ist. Für die Standardwerte:

```bash
php bin/php/console exp:install --print
```

```ini
; Written by exp:install for one installation run; see kickstart.ini-dist.

[email_settings]
Continue=true
Type=mta
Server=
User=
Password=

[database_choice]
Continue=true
Type=sqlite3

[database_init]
Continue=true
Server=localhost
Port=
Database=exponential.db
User=
Password=
Socket=

[language_options]
Continue=true
Primary=eng-US

[site_types]
Continue=true
Site_package=sevenx_multisite

[site_access]
Continue=true
Access=url

[site_details]
Continue=true
Title=Exponential
URL=http://localhost
Access=site
AdminAccess=admin
AccessPort=8080
AdminAccessPort=8081
AccessHostname=
AdminAccessHostname=
Database=exponential.db
DatabaseAction=remove
OrganisationName=
OrganisationAddress=
EditorAccess=editor

[site_admin]
Continue=true
FirstName=Administrator
LastName=User
Email=nospam@exponential.earth
Password=***

[security]
Continue=true

[registration]
Continue=true
Send=false
```

So lesen Sie sie:

- Die letzte Zeile von `[site_details]` hängt von der Zuordnungsart ab: `EditorAccess` bei `url`, `EditorAccessPort`
  bei `port`, `EditorAccessHostname` bei `host`. Mit `--access=port` lautet sie `EditorAccessPort=8082`.
- `--languages=ger-DE,fre-FR` fügt unter `[language_options]` je Sprache eine Zeile `Languages[]=` hinzu.
- `AccessPort`, `AdminAccessPort` und die Hostnamen-Schlüssel werden immer geschrieben; die Installation verwendet nur
  diejenigen, die zur gewählten Zuordnungsart gehören.

E-Mail wird immer als sendmail/MTA eingerichtet; ändern Sie `[MailSettings]` nach der Installation für SMTP
([Kapitel 10](10-nach-der-installation.md)). Die Registrierungs-E-Mail (`[registration] Send=false`) wird nie versendet.

Die Namen der Abschnitte und Schlüssel sind in [Kapitel 6](06-kickstarter.md#642-abschnitte-und-schlüssel) beschrieben. Um
diese Konfiguration als Datei aufzubewahren und stattdessen mit dem Kickstarter auszuführen, leiten Sie die Ausgabe von
`--print` in eine Datei um und setzen Sie die echten Passwörter an die Stelle von `***`:

```bash
php bin/php/console exp:install --print --db=mysql --db-user=exponential > kickstart.ini
chmod 600 kickstart.ini
```

## 7.5 Das Passwort des Administrators

`exp:install` installiert nie ein Passwort, das leer, zu kurz oder allgemein bekannt ist:

| Ihre Angabe | Ergebnis |
|---|---|
| kein `--password` | ein erzeugtes Passwort |
| `--random-password` | ein erzeugtes Passwort (auch wenn `--password` angegeben ist) |
| `--password=` mit mindestens 10 Zeichen (das ausgelieferte `site.ini [UserSettings] MinPasswordLength`), das nicht allgemein bekannt ist | Ihr Passwort, wie angegeben |
| `--password=`, das kürzer ist oder eines von `publish`, `admin`, `password`, `changeme`, `change-me`, `secret`, `exponential`, `demo`, `123456` (in beliebiger Schreibweise) | ein erzeugtes Passwort; die Zusammenfassung nennt den Grund |
| `--password=` (leer oder nur Leerzeichen) | abgelehnt, Exit-Status 1 |

Warum ersetzen statt ablehnen: Eine unbeaufsichtigte Installation, die bei einem schwachen Passwort anhält, hilft
niemandem, und eine, die `publish` als Passwort des Administrators einer Site im Internet installiert, ist ein offenes
Tor. Ein ersetztes Passwort wird in der Zusammenfassung gemeldet und in der unten genannten Datei festgehalten, es geht
also nie verloren.

Ein erzeugtes Passwort hat 24 Zeichen aus dem Alphabet `./A-Za-z0-9`, gezogen aus der sicheren Zufallsquelle des
Betriebssystems (`random_int()`); das sind etwa 143 Bit. Es wird einmal in der Zusammenfassung angezeigt und, außer bei
einem Probelauf, in `var/log/initial-admin-password` geschrieben (Modus `0600`, nur für den Eigentümer lesbar):

```text
Exponential administrator login: admin
Password: <the password>
Generated 2026-10-05T10:15:00+02:00 by exp:install: no --password given.
Log in, change it, then delete this file.
```

Der Grund in der dritten Zeile ist einer von „no --password given“, „requested with --random-password“ oder „the
password given is ... and cannot be installed“.

Die Zusammenfassung zeigt immer das Passwort, das die Installation tatsächlich gesetzt hat. Der Installationsschritt hat
eine eigene Prüfung; wenn er ein Passwort ersetzt, schreibt er seinen Ersatz in dieselbe Datei, und die Zusammenfassung
liest es von dort und meldet „the password given is well-known and was refused by the installation“.

**Nach der ersten Anmeldung** ändern Sie das Passwort in der Administrationsoberfläche und löschen die Datei:

```bash
rm var/log/initial-admin-password
```

Ein Passwort, das Sie selbst angegeben haben, wird nirgends im Klartext gespeichert: Die Zusammenfassung meldet „The
password is shown here once and stored nowhere else in clear text“, und in der gespeicherten Konfiguration in
`var/log/` ist es maskiert.

### Eine öffentliche Demo: EXP_DEMO_MODE=1

Eine öffentliche Demonstrations-Site veröffentlicht das Passwort ihres Administrators. Mit der Umgebungsvariablen
`EXP_DEMO_MODE=1` (Demo-Modus, seit 6.0.15) installiert `exp:install` ein allgemein bekanntes `--password` unverändert,
statt es zu ersetzen:

```bash
EXP_DEMO_MODE=1 ./console exp:install --db=sqlite --password='publishing' --url=https://demo.example.com
```

Gelockert wird nur das Passwort des Administrators der neuen Installation: Ein allgemein bekanntes und eines, das
kürzer als `MinPasswordLength` ist (auch `--password='publish'`), werden wie angegeben installiert; ein leeres
`--password` wird weiterhin abgelehnt. Behalten Sie die Variable anschließend in der Umgebung des PHP, der Cronjobs und
der Konsole der Demo: Solange sie gesetzt ist, kann niemand ein Administratorpasswort ändern oder zurücksetzen, und die
Administration zeigt ein Banner **Demo-Modus**. Ein Zurücksetzen mit `exp:resetuserpassword` nach der Installation ist
nicht nötig und wird im Demo-Modus abgelehnt. Setzen Sie die Variable nie für eine echte Site. Alles Weitere:
[Demo-Modus](../bc/6.0/demomodus.md).

## 7.6 Probelauf und Ausgabe

| Modus | Prüft die Optionen | Verbindet sich mit der Datenbank | Holt die Pakete | Schreibt |
|---|---|---|---|---|
| `--print` | ja | nein | nein | nichts; gibt die Konfiguration aus und endet mit 0 |
| `--dry-run` | ja | ja | ja, in ein temporäres Repository | `var/log/setup.log`, `var/log/exp-install-<date>.ini`; keine Datenbank, keine Einstellungen, keine Passwortdatei, kein Wartungsmodus |

`--dry-run` führt den Probelauf des Kickstarters aus ([Kapitel 6](06-kickstarter.md#67-probeläufe)): `DatabaseChoice` bis
`SiteDetails` mit der erzeugten Konfiguration, mit Halt vor `SiteAdmin`, sodass keine Passwortdatei geschrieben und
keine E-Mail versendet wird. Das temporäre Paket-Repository (`var/storage/packages/dryrun`) wird am Ende wieder
entfernt. Ein erfolgreicher Probelauf endet mit:

```text
Dry-run completed: database and remote packages verified. Stopped after SiteDetails, nothing written.
```

Er läuft auch in einem Verzeichnis, das bereits eine Installation enthält, ohne `--force`; damit ist er der Weg, eine
Konfiguration vor einer Neuinstallation zu testen. Der Exit-Status ist 0, wenn die Prüfung bestanden wurde.

Wofür sich jeder Modus eignet:

- `--print` beantwortet die Frage „Was würde der Befehl mit diesen Optionen tun?“ in einer Sekunde, ohne Datenbank und
  ohne Netzwerk. Verwenden Sie ihn, um die Schreibweise der Optionen und die abgeleiteten Werte (Editor-Siteaccess, URL,
  Datenbankname) zu prüfen.
- `--dry-run` beantwortet die Frage „Würde es funktionieren?“: Der Datenbankserver akzeptiert Benutzer und Passwort, die
  Datenbank existiert, das Site-Paket lässt sich holen. Verwenden Sie ihn vor jedem echten Lauf auf einem Server und
  immer vor einem `--force`.

## 7.7 Exit-Status

| Status | Wann |
|---|---|
| `0` | installiert; oder `--dry-run` bestanden; oder `--print`; oder `--help` |
| `1` | eine Option war unbekannt, fehlerhaft geschrieben oder hat ihre Prüfung nicht bestanden; ein Wert enthielt einen Zeilenumbruch; das Verzeichnis enthält bereits eine Installation, und weder `--force` noch `--dry-run` wurde angegeben; die vorhandene `kickstart.ini` konnte nicht beiseitegelegt oder die neue nicht geschrieben werden („nothing was changed“); oder der Installationslauf ist fehlgeschlagen |

Ein Skript kann sich daher auf `$?` verlassen:

```bash
php bin/php/console exp:install --dry-run --db=mysql --db-user=exponential \
  && php bin/php/console exp:install --force --db=mysql --db-user=exponential \
  || echo "installation failed, see var/log/setup.log"
```

Bei

```text
This directory already holds an installation (settings/override/site.ini.append.php).
exp:install would replace its database and settings. Re-run with --force to do that,
or with --dry-run to check the configuration only.
```

wurde nichts angetastet.

Wenn der Installationslauf fehlschlägt, gibt der Kickstarter aus, welcher Schritt angehalten hat und warum, und der
Wartungsmodus bleibt eingeschaltet, damit Besucher keine halb installierte Site sehen („The site stays in maintenance
mode. After fixing the cause: run the kickstarter again, or php bin/php/maintenance.php off“). Ein Fenster, das vor dem
Lauf geöffnet war, bleibt in jedem Fall geöffnet. Siehe [Kapitel 6](06-kickstarter.md#68-wartungsmodus-während-eines-durchlaufs).
Die Zusammenfassung wird nur nach einer erfolgreichen Installation ausgegeben.

## 7.8 Der Editor-Siteaccess mit exp:install

Jede Installation erhält den Editor-Siteaccess: eine Administrationsoberfläche nur für die Bearbeitung von Inhalten.
Ohne eine Option `--editor-*` wird sein Wert aus dem öffentlichen abgeleitet und ist nie gleich dem öffentlichen oder
dem Wert der Administration, denn zwei Siteaccesses auf demselben Pfad, Host oder Port würden einen von ihnen
unerreichbar machen:

| `--access` | Editor-Siteaccess erreichbar unter | Option |
|---|---|---|
| `url` | dem Pfad `/editor` (`/<site-access>_editor`, wenn `editor` bereits der öffentliche Pfad oder der Pfad der Administration ist) | `--editor-access` |
| `host` | `edit.<--host ohne www.>`, z. B. `edit.example.com` für `--host=www.example.com`; `editor.<...>`, wenn das der Host der Administration ist | `--editor-host` |
| `port` | `--port` + 2, z. B. `8082` für `8080` (der nächste freie Port, wenn das der Port der Administration ist) | `--editor-port` |

`exp:install` schreibt den Wert in die erzeugte `kickstart.ini` (`EditorAccess`, `EditorAccessHostname` oder
`EditorAccessPort`), und die Zusammenfassung zeigt die Adresse des Editors unter `Editor`. Bei der URL-Zuordnung, dem
häufigsten Fall, ist nichts zu tun. Bei der Host-Zuordnung benötigt der Editor-Host wie die beiden anderen einen
DNS-Eintrag und einen Platz in der Konfiguration des Webservers bzw. der Zertifikate
([Kapitel 8](08-die-website-ausliefern.md)).

## 7.9 Beispiele nach Datenbank

### SQLite (Standard)

```bash
php bin/php/console exp:install
```

Installiert in `var/storage/sqlite3/exponential.db`. Es wird kein Datenbankserver benötigt; das macht diesen Weg zum
schnellsten zu einer funktionierenden Site. Mit einer Adresse und einem Administrator:

```bash
php bin/php/console exp:install \
    --url=https://www.example.com \
    --title="Example" \
    --email=webmaster@example.com \
    --random-password
```

Eine zweite SQLite-Site im selben Verzeichnisbaum ist eine zweite Installation; eine Installation verwendet eine
Datenbankdatei. SQLite im Produktivbetrieb wird in [Kapitel 9](09-datenbanken.md) behandelt.

### MySQL oder MariaDB

Die Datenbank und der Benutzer müssen existieren ([Kapitel 6, MySQL-Beispiel](06-kickstarter.md#662-mysql-oder-mariadb)).

```bash
EXP_INSTALL_DB_PASSWORD='a-long-random-password' \
php bin/php/console exp:install \
    --db=mysql --db-host=127.0.0.1 --db-port=3306 \
    --db-name=exponential --db-user=exponential \
    --url=https://www.example.com --email=webmaster@example.com
```

Über einen lokalen Socket:

```bash
EXP_INSTALL_DB_PASSWORD='...' php bin/php/console exp:install \
    --db=mariadb --db-socket=/var/lib/mysql/mysql.sock --db-name=exponential --db-user=exponential
```

`127.0.0.1` und `localhost` sind für MySQL nicht dasselbe: `localhost` lässt den Client den lokalen Socket verwenden,
`127.0.0.1` eine TCP-Verbindung. Ein als `'exponential'@'localhost'` angelegter Benutzer kann über TCP abgewiesen
werden; verwenden Sie den Hostnamen, für den der Benutzer angelegt wurde.

### PostgreSQL

Die Datenbank muss existieren, und `pgcrypto` muss in ihr verfügbar sein ([Kapitel 6, PostgreSQL-Beispiel](06-kickstarter.md#663-postgresql)).

```bash
EXP_INSTALL_DB_PASSWORD='...' php bin/php/console exp:install \
    --db=pgsql --db-host=db.internal --db-name=exponential --db-user=exponential \
    --url=https://intranet.example.com --email=it@example.com
```

### MongoDB

Benötigt die PHP-Extension `mongodb`, die Bibliothek `mongodb/mongodb` und einen Datenbankbenutzer, der in der
Datenbank der Site angelegt wurde ([Kapitel 9](09-datenbanken.md#95-mongodb)).

```bash
EXP_INSTALL_DB_PASSWORD='...' php bin/php/console exp:install \
    --db=mongodb --db-host=127.0.0.1 --db-port=27017 --db-name=exponential --db-user=exponential \
    --url=https://www.example.com
```

`--db-user` hat für MongoDB keinen Standardwert, und die Option ist nicht optional: Der Adapter schreibt immer einen
Benutzer und ein Passwort in seinen Verbindungsstring, und ohne Benutzer lehnt der MongoDB-Treiber den String ab
(„Failed to parse MongoDB URI: ... 'default' authentication mechanism requires a username“), sodass der Lauf beim
Datenbankschritt anhält. Legen Sie den Benutzer zuerst an, auch auf einem Testserver, der keine Zugriffskontrolle
erzwingt.

### Oracle

Benötigt die PHP-Extension `oci8` und die Extension `ezoracle` in `extension/`, die die Installation für die neue Site
aktiviert.

```bash
EXP_INSTALL_DB_PASSWORD='...' php bin/php/console exp:install \
    --db=oracle --db-host=127.0.0.1 --db-port=1521 --db-name=FREEPDB1 --db-user=EXPONENTIAL
# a TNS alias instead of host, port and service:
EXP_INSTALL_DB_PASSWORD='...' php bin/php/console exp:install --db=oracle --db-name=@EXPDB --db-user=EXPONENTIAL
```

Die erste Form verbindet sich mit `127.0.0.1:1521/FREEPDB1` (Easy Connect); die zweite übergibt `EXPDB` an den
Oracle-Client, der den Namen in seiner `tnsnames.ora` nachschlägt.

### Host- und Port-Zuordnung

```bash
# site on www.example.com, admin on admin.example.com, editor on edit.example.com
php bin/php/console exp:install --access=host --host=www.example.com --admin-host=admin.example.com \
    --url=https://www.example.com

# site on 8080, admin on 8081, editor on 8082 (7.8)
php bin/php/console exp:install --access=port --port=8080 --admin-port=8081
```

### Über eine vorhandene Installation neu installieren

```bash
php bin/php/console exp:install --dry-run --db=mysql --db-name=exponential --db-user=exponential   # check first
php bin/php/console exp:install --force   --db=mysql --db-name=exponential --db-user=exponential   # replaces it
```

**Dies zerstört den Inhalt der Datenbank `exponential`.** Sichern Sie sie vorher
([Kapitel 6](06-kickstarter.md#65-databaseaction-lesen-sie-dies-bevor-sie-run-ausführen) zeigt, wie das für jede Datenbank geht).

### Die Einstellungen neu erzeugen, die Daten behalten

```bash
php bin/php/console exp:install --force --db-action=skip --db=mysql --db-name=exponential --db-user=exponential \
    --url=https://www.example.com
```

`skip` fügt nichts ein und behält den vorhandenen Administrator; die Einstellungen in `settings/override/` und
`settings/siteaccess/` werden aus den Optionen neu geschrieben. Von Hand vorgenommene Änderungen in diesen Dateien werden
ersetzt; kopieren Sie sie daher vorher beiseite und führen Sie sie anschließend wieder zusammen.

## 7.10 Was Sie am Ende sehen

Der Befehl beginnt mit einer Zeile, die nennt, was er gleich tun wird, zum Beispiel:

```text
exp:install: sevenx_multisite on sqlite (exponential.db), http://localhost, access by url, administrator admin / (generated, shown at the end)
```

Bei einem Datenbankserver nennt die Klammer auch den Server, zum Beispiel `(exponential at 127.0.0.1:3306)`.

Danach gibt der Kickstarter seine Schritte aus, und der Befehl endet mit:

```text
================================================================
  Exponential is installed
================================================================
  Installed:      2026-10-05 10:17:31 CEST (151s)
  Site:           http://localhost/site/
  Admin login:    http://localhost/admin/user/login
  Editor:         http://localhost/editor/
  Username:       admin
  Password:       <the password>
  Password note:  generated: no --password given; also in var/log/initial-admin-password (owner only), change it and delete that file
  E-mail:         nospam@exponential.earth
  Database:       sqlite exponential.db
  Package:        sevenx_multisite, eng-US
  Siteaccesses:   site, admin, editor (by url)
  Configuration:  var/log/exp-install-20261005-101500.ini (passwords masked)
================================================================
  Copy and paste:

    Admin:    http://localhost/admin/user/login
    Username: admin
    Password: <the password>
    Site:     http://localhost/site/
================================================================
  The password was generated: it is also in var/log/initial-admin-password (owner only).
  Change it after the first login and delete that file.
```

Bei einem Datenbankserver lautet die Zeile `Database` zum Beispiel `mysql exponential at 127.0.0.1:3306, user
exponential`; zusätzliche Sprachen erscheinen in der Zeile `Package` als `eng-US + ger-DE,fre-FR`. Mit einem Passwort,
das Sie angegeben haben, fehlt die Zeile `Password note`, und die letzte Zeile lautet „The password is shown here once
and stored nowhere else in clear text.“

Die Adressen geben an, wo die Site erreichbar *sein wird*, sobald ein Webserver das Wurzelverzeichnis der Installation
ausliefert; `exp:install` startet keinen. Als Nächstes: die Site ausliefern ([Kapitel 8](08-die-website-ausliefern.md)), dann
[Kapitel 10](10-nach-der-installation.md) durcharbeiten.

## 7.11 Was schiefgehen kann

| Symptom | Ursache | Was zu tun ist |
|---|---|---|
| „This directory already holds an installation ...“ | `settings/override/site.ini.append.php` hat einen Abschnitt `[DatabaseSettings]` | um die Installation zu ersetzen, sichern und `--force` hinzufügen; um nur zu testen, `--dry-run` hinzufügen |
| „Unknown option or missing value: --db (see --help)“ | ein Wert wurde mit einem Leerzeichen (`--db mysql`) statt mit `=` angegeben | `--db=mysql` schreiben; jeder Wert braucht `=` |
| „Unknown argument: -f (see --help)“ | eine Kurzoption oder ein einzelnes Wort | der Befehl kennt nur lange Optionen |
| der Lauf hält in `DatabaseInit` an | falscher Host, Port, Benutzer oder falsches Passwort, oder die Datenbank existiert nicht | `--dry-run` ausführen, bis es besteht; den Benutzer mit dem eigenen Client der Datenbank prüfen |
| der Lauf hält beim Holen der Pakete an | kein Netzwerkzugang zum Paketserver, oder ein Proxy steht im Weg | siehe [Kapitel 12](12-fehlerbehebung.md) |
| die Site zeigt nach einem fehlgeschlagenen Lauf die Wartungsseite | der Wartungsmodus bleibt nach einem Fehler absichtlich eingeschaltet | die Ursache beheben und erneut ausführen, oder `php bin/php/maintenance.php off` |
| die Zusammenfassung erscheint nicht | die Installation hat ihr Ende nicht erreicht (`settings/override/site.ini.append.php` wurde nicht neu geschrieben) | die Ausgabe des Kickstarters weiter oben und `var/log/setup.log` lesen |
| Seiten schlagen nach der Installation mit Berechtigungsfehlern fehl | der Befehl lief als `root` | die Dateien dem Benutzer des Webservers übergeben ([Kapitel 12](12-fehlerbehebung.md#123-berechtigungen-und-eigentümerschaft)) |
| Ihre alte `kickstart.ini` fehlt | der Prozess wurde vor seinem Aufräumen beendet (`kill -9`, Stromausfall) | sie liegt in `var/log/kickstart.ini.before-exp-install-<date>`; verschieben Sie sie zurück |

Das Protokoll des Kickstarters über jeden Lauf, ob erfolgreich oder nicht, ist `var/log/setup.log`
([Kapitel 6](06-kickstarter.md#69-logs)).

## 7.12 Weitere Konsolenbefehle für die Einrichtung

Diese Befehle sind rund um eine Installation nützlich. Das eigene `--help` jedes Befehls führt seine Optionen auf.
Skripte, die die Standard-Skriptoptionen verwenden, verweigern die Ausführung als `root`, sofern nicht
`--allow-root-user` angegeben ist.

| Befehl | Verwendung rund um eine Installation |
|---|---|
| `exp:kickstarter` | `ini` schreibt `kickstart.ini`, `run` installiert daraus ([Kapitel 6](06-kickstarter.md)) |
| `exp:maintenance` | `on`, `off`, `status`: die Wartungsseite; `on` nimmt `--message`, `--until`, `--allow-ip`, `--allow-admin` an |
| `exp:ini` | Einstellungen in jedem Geltungsbereich lesen und ändern: `get`, `set`, `where`, `list` ... ([exp:ini](../bc/6.0/console-exp-ini.md)) |
| `exp:ezcache` | Caches nach Tag oder ID leeren (`--clear-tag=ini`, `--clear-all`) |
| `exp:cache` | alle Caches leeren und untersuchen, einschließlich HTTP, Velocity und OPcache ([Cache-Konsole](../bc/6.0/cache-konsole.md)) |
| `exp:ezpgenerateautoloads` | die Autoload-Arrays der Klassen neu erzeugen (`-e` Extensions, `-k` Kernel, `-o` Overrides); eine unbekannte Option beendet den Befehl mit „The referenced parameter '...' is not registered.“ und dem Exit-Status 1 |
| `exp:velocity` (Alias `exp:vc`) | Exponential Velocity, der empfohlene Anwendungsserver ([Kapitel 8](08-die-website-ausliefern.md)) |
| `exp:webserver` | die Webserver-Engines (`php`, `frankenphp`, `qbix`) mit denselben Verben wie `exp:velocity` |
| `exp:frankenphp` (Alias `exp:fp`) | derselbe Befehl, fest auf die FrankenPHP-Engine eingestellt |
| `exp:resetuserpassword` | das Passwort eines Benutzers zurücksetzen, zum Beispiel das des Administrators |
| `exp:checkmanifest` | die Dateien der Installation gegen `share/filelist.md5` prüfen |
| `exp:checkclasses` | jede deklarierte Klasse laden und diejenigen melden, die PHP ablehnt |
| `exp:ezsqlinsertschema`, `exp:ezsqldumpschema` | ein Datenbankschema aus einer `.dba`-Datei einfügen oder in eine solche ausgeben |
| `exp:updatesearchindex` | den Suchindex neu aufbauen |
| `exp:warm`, `exp:preload` | jede veröffentlichte Seite abrufen, damit die Caches gefüllt sind, bevor Besucher kommen |
| `exp:ezwebininstall` | das Site-Paket `ezwebin` in eine vorhandene Installation installieren |

---

## Quellen

In diesem Repository:

- [Exponential 6.0 installieren](../INSTALL.md), Abschnitt 7
- [Exponential mit einem Befehl installieren](../funktionen/6.0/installation-mit-einem-befehl.md)
- [Die Konsole](../bc/6.0/konsole.md), [exp:ini](../bc/6.0/console-exp-ini.md), [Cache-Konsole](../bc/6.0/cache-konsole.md)
- [Kickstarter-CLI](../bc/6.0/kickstartercli.md)
- [Wartungsmodus](../funktionen/6.0/wartungsmodus.md)
- [Installer-Protokolle und Startdaten](../spezifikationen/6.0/installer-protokolle-und-startdaten.md)
- [Datenbanktreiber: SQLite und Oracle](../spezifikationen/6.0/datenbanktreiber-sqlite-oracle.md),
  [Datenbanktreiber, September 2026](../spezifikationen/6.0/datenbanktreiber-2026-09.md),
  [SQLite-Datenbank](../funktionen/6.0/sqlite-datenbank.md)
- [Abstraktionen für CLI, Cronjobs und Views](../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
- [Erste Schritte](../anleitungen/erste-schritte.md)
- Code: `bin/php/install.php` (Optionen, Prüfungen, Zusammenfassung), `kernel/private/classes/commands/install.php`
  (Passwortregeln, Aufruf des Kickstarters), `kernel/classes/expkickstarter.php`,
  `kernel/setup/steps/ezstep_site_access.php` (Standardwerte des Editors), `kernel/setup/steps/ezstep_site_admin.php`
  (allgemein bekannte Passwörter), `bin/php/console`

Extern:

- PHP `random_int()`: <https://www.php.net/manual/en/function.random-int.php>
- PHP `filter_var()`, verwendet mit `FILTER_VALIDATE_EMAIL`: <https://www.php.net/manual/en/function.filter-var.php>
- PHP OCI8: <https://www.php.net/manual/en/book.oci8.php>
- MongoDB-PHP-Treiber: <https://www.php.net/manual/en/book.mongodb.php>
- Oracle Easy Connect Naming: <https://docs.oracle.com/en/database/oracle/oracle-database/19/netag/configuring-naming-methods.html>
- Symfony Console (das Vorbild für `bin/php/console`): <https://symfony.com/doc/current/console.html>
- Exponential auf GitHub: <https://github.com/se7enxweb/exponential>

[Inhalt](README.md) · Zurück: [6. Der Kickstarter](06-kickstarter.md) · Weiter: [8. Die Site ausliefern](08-die-website-ausliefern.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
