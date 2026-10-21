# 5. Der Setup-Assistent

Der Setup-Assistent installiert Exponential im Browser, eine Seite pro Schritt. Auf einer frischen Installation startet
er von selbst, weil `settings/site.ini` mit `[SiteAccessSettings] CheckValidity=true` ausgeliefert wird; er hält die
Site für alle außer dem Browser, der ihn gestartet hat, im Wartungsmodus; er durchläuft die Schritte aus
`kernel/setup/steps/ezstep_data.php` der Reihe nach; und sein letzter Schritt schreibt die Datenbank, das Site-Paket
und die Einstellungen und schaltet den Assistenten danach ab. Dieses Kapitel beschreibt jede Seite so, wie die
Templates in `design/standard/templates/setup/init/` sie darstellen: wozu sie dient, jedes Feld, die Prüfungen, denen
die Antworten unterzogen werden, und die genauen Meldungen, die erscheinen, wenn eine Antwort abgelehnt wird. Außerdem
behandelt es, wie eine `kickstart.ini` Seiten vorbelegt oder überspringt, was der Assistent schreibt und wie Sie einen
Assistenten neu starten, der auf halbem Weg stehen geblieben ist. Halten Sie es bei Ihrer ersten Installation neben dem
Browser geöffnet: Jede der folgenden Seiten führt auf, was sie abfragt, was sie prüft und was es bedeutet, wenn sie
eine Antwort ablehnt.

[Inhalt](README.md) · Zurück: [4. Eine Installationsmethode wählen](04-installationsweg-waehlen.md) · Weiter: [6. Der Kickstarter](06-kickstarter.md)

## 5.1 Wie der Assistent startet

`settings/site.ini` enthält:

```ini
[SiteAccessSettings]
CheckValidity=true
```

Bei jeder Anfrage prüft der Web-Kernel (`kernel/private/classes/ezpkernelweb.php`) diesen Wert. Solange er die
Zeichenkette `true` ist, wird die Anfrage an das Modul `setup`, View `init`, übergeben (der Code der View ist
`kernel/private/classes/views/setup/ezsetup.php`, der Einstiegspunkt `kernel/setup/ezsetup.php`), und zwar mit:

- dem Pagelayout aus `[SetupSettings] PageLayout` (`setup_pagelayout.tpl`) und dem Design aus `[SetupSettings]
  OverrideSiteDesign` (`standard`);
- keiner Session, keinem Benutzerobjekt und keiner Datenbankverbindung;
- einem vorübergehenden Siteaccess namens `setup`.

`CreateSites`, der letzte arbeitende Schritt, schreibt `CheckValidity=false` in
`settings/override/site.ini.append.php`. Von da an wird die Site normal ausgeliefert, und der Assistent ist nicht mehr
erreichbar.

So starten Sie den Assistenten:

1. Installieren Sie den Code und seine Abhängigkeiten ([Kapitel 3](03-den-code-beschaffen.md)) und machen Sie `settings/`,
   `var/` und `design/` für den Benutzer beschreibbar, unter dem der Webserver läuft.
2. Liefern Sie das Installationsverzeichnis über einen Webserver aus ([Kapitel 8](08-die-website-ausliefern.md)). Exponential
   Velocity ist die empfohlene Engine; für einen schnellen lokalen Start liefert
   `php bin/php/console exp:velocity start --engine=php` die Site mit dem eingebauten Server von PHP aus.
3. Öffnen Sie die Adresse der Site im Browser. Welchen Pfad Sie auch öffnen, es erscheint die erste Seite des
   Assistenten.

Jede Seite hat denselben Rahmen (`design/standard/templates/setup_pagelayout.tpl`): in der Mitte das Formular des
Schritts, ein Bereich **Hilfe** mit dem eigenen Hilfetext des Schritts (`<step>_help.tpl`), ein Bereich
**Zusammenfassung**, der sich im Lauf der Installation füllt (System, Bildverarbeitung, E-Mail, Datenbank, Sprache,
Site), und ein Fortschrittsbalken mit der Anzeige „N% abgeschlossen“.

### Wie sich der Assistent Ihre Antworten merkt

Der Assistent führt keine serverseitige Session. Jede bisher gesammelte Antwort wird als verstecktes Formularfeld mit
dem Namen `P_<group>-<key>` von Seite zu Seite weitergereicht (`design/standard/templates/setup/persistence.tpl`).
Daraus folgt zweierlei:

- **Verwenden Sie für den Assistenten HTTPS auf jedem Rechner, der über ein Netzwerk erreicht wird.** Das
  Datenbankpasswort und das Administratorpasswort werden mit jeder Seite in diesen versteckten Feldern übertragen.
- **Ein Neuladen der ersten Seite beginnt von vorn.** Eine einfache Anfrage (ohne abgeschicktes Formular) ist immer die
  Willkommensseite, ohne gespeicherte Antworten.

### Die Schaltflächen

`design/standard/templates/setup/init/navigation.tpl` zeichnet die Schaltflächen. Nicht jede Seite zeigt alle.

| Schaltfläche | Angezeigt auf | Wirkung |
|---|---|---|
| **Weiter >** | jeder Seite außer der Fehlerseite von „Erstellung der Seiten“ | prüft die Antworten dieser Seite; bei Erfolg wird die nächste Seite angezeigt, sonst noch einmal diese Seite mit dem Problem |
| **< Zurück** | jeder Seite außer der Willkommensseite und der Fehlerseite von „Erstellung der Seiten“ | zeigt die vorige Seite an und überspringt dabei Seiten, die keine Eingabe brauchen |
| **Feineinstellungen** | der Willkommensseite und dem Systemtest, wenn ein optionaler Test fehlgeschlagen ist; der Feineinstellung des Systems | zeigt die optionalen Tests an (oder führt sie erneut aus) |
| **Aktualisieren** | den Seitendetails | zeigt die Seite mit den aktuellen Werten erneut an |
| **Wiederholen** | der Fehlerseite von „Erstellung der Seiten“ | führt den Installationsschritt erneut aus |

## 5.2 Die Browser-Sperre und der Wartungsmodus

Während der Assistent läuft, ist die Site für alle außer dem Browser, der ihn gestartet hat, im **Wartungsmodus**
(Klasse `expMaintenance` in `kernel/classes/expmaintenance.php`):

1. Das Öffnen des Assistenten ändert nichts: Die erste Seite (Willkommen) wird ohne Wartungsmodus angezeigt, gleich
   wer sie anfordert (ein GET, ein Crawler, ein Neuladen). Der erste **abgeschickte** Schritt eines Durchlaufs ruft
   `expMaintenance::beginWizard()` auf. Dieser Aufruf schreibt die Markierungsdatei `var/maintenance.json` mit dem
   Grund `setup`, der Durchlauf-ID des Setup-Logs, dem SHA-256-Hash eines zufälligen Tokens und einer **Frist**
   (Lease), die 15 Minuten später endet (`expMaintenance::WIZARD_LEASE = 900` Sekunden). Der Browser erhält das Token
   als Cookie `exp_setup_wizard` (Pfad `/`, HttpOnly, SameSite=Lax, Secure, wenn die Anfrage über HTTPS kam).
2. Jeder weitere abgeschickte Schritt verlängert die Frist und das Cookie um weitere 15 Minuten, und zwar zu Beginn der
   Anfrage und noch einmal an ihrem Ende (ein langer Schritt wie ein Paketimport hält sie ab seinem Ende). Nichts
   anderes verlängert sie.
3. Allen anderen Besuchern und jeder Anfrage ohne das Cookie antwortet der Front-Controller, bevor irgendeine
   Einstellung oder die Datenbank gelesen wird: mit HTTP **503**, einem `Retry-After`-Header,
   `Cache-Control: no-store`, `X-Robots-Tag: noindex` und der Seite `share/maintenance.html` (oder `errors/maintenance.html`
   der ersten aktiven Extension) mit dem Titel **„The site is being set up“** („Die Site wird eingerichtet“) und dem
   Text „It is being installed right now and will be here in a few minutes.“ („Sie wird gerade installiert und ist in
   wenigen Minuten da.“).
4. Die Seite **Beendet** hebt die Sperre auf: Die Markierungsdatei wird entfernt und das Cookie gelöscht.
5. Ein abgebrochener Assistent hält die Site nicht offline: Sobald die Frist abgelaufen ist, zählt die Markierungsdatei
   nicht mehr, und der nächste Besucher startet einen neuen Assistenten.

Die Sperre gibt es, damit ein zweiter Besucher keinen zweiten Assistenten starten kann, der den ersten abbrechen oder
über ihn hinweg installieren würde, und damit niemand auf eine halb aufgebaute Datenbank trifft.

### Der Assistent auf einer installierten Site

Ein Administrator kann `setup/init` auf einer installierten Site weiterhin öffnen (die View braucht die Richtlinie
`setup/install`). Dort läuft der Assistent nicht: Die Seite meldet **„Diese Website ist bereits installiert“** und dass
eine Neuinstallation mit dem Kickstarter (`kickstart.ini`, danach `php bin/php/kickstarter.php`) oder mit einer frischen
Installation erfolgt. Es wird nichts geschrieben: keine Wartungsmarkierung, kein Durchlauf in `var/log/setup.log`.
Eine Site gilt als installiert, wenn:

- `var/site_installed.json` existiert (die letzte Seite des Assistenten schreibt diese Datei), oder
- die Anfrage nicht über die Gültigkeitsprüfung kam (`CheckValidity` ist aus, die Site wird also normal ausgeliefert),
  oder
- die Anfrage über die Gültigkeitsprüfung kam und die Datenbank aus den Einstellungen Inhaltsobjekte enthält.

Für die Entwicklung lässt `settings/site.ini [SetupSettings] AllowWizardOnInstalledSite=enabled` (Standard ist
`disabled`) den Assistenten auf einer installierten Site laufen, und zwar erst nach einer Bestätigungsseite: Der
Wartungsmodus beginnt, wenn das Formular dieser Seite mit seinem Einmal-Token (das in der Session liegt)
zurückgeschickt wird, niemals durch ein GET. Ein abgeschickter Schritt, dessen Frist abgelaufen ist, kehrt zur
Bestätigungsseite zurück.

Vor 6.0.15 startete jedes GET auf `setup/init` den Wartungsmodus, sodass ein Crawler eines angemeldeten Administrators
eine installierte Site mit „The site is being set up“ offline nehmen konnte, bis die Frist abgelaufen war oder
`exp:maintenance off` ausgeführt wurde.

## 5.3 Die Seiten, Schritt für Schritt

Der Assistent führt die Schritte aus `eZStepData::$StepTable` der Reihe nach aus. Ein Schritt, dessen `init()`
entscheidet, dass nichts abgefragt werden muss, wird ohne Seite übergangen; die folgende Tabelle nennt, wann das
geschieht.

| # | Seitentitel | Schrittklasse | Angezeigt, wenn |
|---|---|---|---|
| 1 | Willkommen in Exponential | `Welcome` | immer (außer `kickstart.ini` hat einen Abschnitt `[welcome]` mit `Continue=true`) |
| 2 | Systemtest | `SystemCheck` | nur wenn ein kritischer Test fehlschlägt |
| 3 | Feineinstellung des Systems | `SystemFinetune` | nur wenn Sie **Feineinstellungen** gedrückt haben |
| 4 | Postausgang | `EmailSettings` | immer |
| 5 | Datenbanksystem auswählen | `DatabaseChoice` | wenn mehr als eine Datenbank-Extension geladen ist |
| 6 | Initialisierung der Datenbank | `DatabaseInit` | immer |
| 7 | Sprachunterstützung | `LanguageOptions` | immer |
| 8 | Site Paket | `SiteTypes` | immer |
| 9 | Sprachoptionen | `PackageLanguageOptions` | wenn die Pakete Sprachen enthalten, die Sie nicht gewählt haben |
| 10 | Konfiguration der Siteaccesses | `SiteAccess` | immer |
| 11 | Seitendetails | `SiteDetails` | immer |
| 12 | Administrator der Seite | `SiteAdmin` | außer die Datenbankaktion lautet „Die Daten belassen und nichts weiteres tun“ |
| 13 | Seitensicherheit | `Security` | nur wenn die Site nicht im Virtual-Host-Modus läuft und es keine `.htaccess` gibt |
| 14 | Registrierung | `Registration` | immer |
| — | (Erstellung der Seiten) | `CreateSites` | nur wenn die Installation fehlschlägt |
| 15 | Beendet | `Final` | immer |

### 5.3.1 Willkommen

**Zweck.** Begrüßt Sie, bietet die Sprache des Assistenten selbst zur Auswahl an und führt im Hintergrund die
optionalen Tests aus.

**Bildschirm.** Die Überschrift „Willkommen in Exponential *Version*“. Wenn alle optionalen Tests bestanden wurden,
lautet der Text: „Willkommen im Exponential Content Management System and Development Framework. Dieser Assistent wird
Ihnen helfen Exponential zu installieren. Klicken Sie auf *Weiter*, um fortzufahren.“ Wenn ein optionaler Test
fehlgeschlagen ist, ergänzt der Text: „Ihr System ist nicht optimal vorbereitet. Sie können auf *Feineinstellungen*
klicken, um Hinweise zu erhalten, wie Sie Ihre Umgebung optimieren können. Sie können auch einfach auf *Weiter*
klicken, um dies auszulassen.“, und es erscheint eine Schaltfläche **Feineinstellungen**.

| Feld | Formularname | Werte |
|---|---|---|
| Installationsprache auswählen | `eZSetupWizardLanguage` | die Sprachen, für die die Installation Locales hat; die bevorzugte Sprache des Browsers ist vorausgewählt |

**Was die Seite tut.** Legt die Übersetzung fest, in der der Rest des Assistenten angezeigt wird. Es wird nichts
geschrieben.

### 5.3.2 Systemtest

**Zweck.** Führt die kritischen Tests aus. Wenn alle bestehen, wird die Seite nicht angezeigt, und der Assistent geht
weiter.

Die kritischen Tests stehen in `settings/site.ini [SetupSettings] CriticalTests`, ihre Parameter in
`settings/setup.ini`:

| Test | Was er prüft | Parameter in `setup.ini` |
|---|---|---|
| `directory_permissions` | der Webserver kann die aufgeführten Verzeichnisse beschreiben | `[directory_permissions] CheckList`: `design`, `extension`, `settings`, `settings/override`, `settings/siteaccess`, `settings/siteaccess/admin`, `var`, `var/cache` und seine Unterverzeichnisse, `var/log`, `var/storage`, `var/storage/original`, `var/storage/reference`, `var/storage/variations`, `var/autoload` |
| `phpversion` | die PHP-Version | `[phpversion] MinimumVersion` |
| `database_extensions` | mindestens eine Datenbank-Extension | `[database_extensions] Extensions=sqlite3;mysqli;pgsql;mongodb;oci8`, `Require=one` |
| `image_conversion` | GD oder ImageMagick | `[image_conversion] TestList=imagegd_extension;imagemagick_program`, `Require=one` |
| `safe_mode`, `magic_quotes_runtime` | veraltete PHP-Einstellungen, die ausgeschaltet sein müssen | |
| `memory_limit` | `memory_limit` | `[memory_limit] MinMemoryLimit=64M` (`-1` besteht) |
| `execution_time` | `max_execution_time` | `[execution_time] MinExecutionTime=30` (`0` besteht) |
| `allow_url_fopen` | `allow_url_fopen` ist eingeschaltet | |
| `php_session` | die Extension `session` | |
| `file_upload` | `file_uploads` ist eingeschaltet und das Upload-Verzeichnis beschreibbar | |
| `zlib_extension`, `dom_extension`, `iconv_extension`, `mbstring_extension`, `intl_extension`, `xsl_extension` | die PHP-Extensions | |
| `timezone` | `date.timezone` ist in `php.ini` gesetzt (dort gewähltes UTC besteht; UTC nur als Rückfallwert schlägt fehl) | |
| `ezcversion` | die Zeta Components | `[ezcversion] TestClass=ezcBaseFile`, `MinimumVersion=2008.2` |

**Bildschirm.** „Systemtest“, dann „Es gibt einige Probleme, die gelöst werden müssen. Eine Liste finden Sie unten.
Jeder Abschnitt enthält eine Beschreibung des Problems und eine vorgeschlagene / empfohlene Lösung.“ und „Wenn die
Probleme beseitigt sind, können Sie auf den Knopf *Weiter* klicken um fortzusetzen. Der Systemtest wird dann erneut
ausgeführt. Sollte alles in Ordnung sein, wird das Setup auf der nächsten Ebene fortsetzen. Sollten noch Probleme
bestehen, wird diese Seite erneut aufgerufen.“ Unter **Probleme** hat jeder fehlgeschlagene Test seine eigene
Erläuterung (`design/standard/templates/setup/tests/
<test>_error.tpl`). Das Problem mit den Verzeichnisberechtigungen zum Beispiel trägt den Titel „Unzureichende
Verzeichnisberechtigungen“, sagt „Exponential kann einige wichtige Verzeichnisse nicht anlegen. Ohne diese Einstellung
kann die Installation nicht abgeschlossen werden.“, listet „Die betroffenen Verzeichnisse sind: ...“ auf und gibt
fertige **Shell-Befehle** an (sowie **Alternative Shell-Kommandos** für den Fall, dass Sie den Eigentümer nicht ändern
können).

| Feld | Formularname | Wirkung |
|---|---|---|
| Diesen Test ignorieren (eines je fehlgeschlagenem Test) | `<test>_Ignore` | der Test wird beim nächsten Durchlauf ausgelassen. „Einige Probleme können durch Markierung des Auswahlfelds *Diesen Test ignorieren* ignoriert werden. Dies sollten Sie aber nach Möglichkeit vermeiden.“ |

Beheben Sie die Ursachen auf dem Server und drücken Sie **Weiter**; die Tests laufen erneut.

> **Hinweis.** Der Test der PHP-Version vergleicht mit `setup.ini [phpversion] MinimumVersion`, das `8.0.0` lautet:
> dasselbe Minimum, das Composer erzwingt (`composer.json` verlangt `^8.0`); siehe [Kapitel 2](02-voraussetzungen.md).

### 5.3.3 Feineinstellung des Systems

**Zweck.** Zeigt die fehlgeschlagenen optionalen Tests mit Ratschlägen an. Die Seite erscheint nur, nachdem
**Feineinstellungen** gedrückt wurde.

Die optionalen Tests sind `[SetupSettings] OptionalTests`: `variables_order`, `php_magicquotes`, `curl_extension`,
`imagegd_extension`, `imagemagick_program` (sucht nach `convert` in `/bin`, `/sbin`, `/usr/bin`, `/usr/sbin`,
`/usr/local/bin`, `/usr/local/sbin`), `database_all_extensions` (alle von `sqlite3`, `mysqli`, `pgsql`, `mongodb`),
`php_register_globals`, `texttoimage_functions` (`imagettftext`, `imagettfbbox`) und `open_basedir`.

**Bildschirm.** „Feineinstellung des Systems“, „Es gibt einige Dinge, die untersucht werden sollten, um die maximale
Leistung und alle Funktionen zu erhalten. ...“ und „Falls Sie diese Dinge nicht beheben wollen, klicken Sie einfach auf
*Weiter*.“ Drücken Sie **Feineinstellungen**, um die Tests nach einer Korrektur erneut auszuführen, oder **Weiter**, um
fortzufahren. Nichts auf dieser Seite blockiert die Installation.

### 5.3.4 Postausgang

**Zweck.** Wie die Site E-Mails versendet.

**Bildschirm.** „Postausgang“, „Hier wird konfiguriert, mit welchen Einstellungen Exponential E-Mails versendet.“, die
beiden Optionen („Direkter Versand durch einen Transferagenten (muss auf dem Server installiert sein).“ und
„Indirekter Versand unter Verwendung eines SMTP Relay-Servers.“) und der Hinweis „SMTP wird für MS Windows Benutzer
empfohlen.“

| Feld | Formularname | Werte |
|---|---|---|
| E-Mail Versand | `eZSetupEmailTransport` | `1` **Sendmail/MTA** (vorausgewählt), `2` **SMTP** (unter Windows ist SMTP die einzige Wahl) |
| Servername | `eZSetupSMTPServer` | SMTP-Host |
| Benutzername (optional) | `eZSetupSMTPUser` | |
| Passwort (optional) | `eZSetupSMTPPassword` | |

**Prüfung.** Keine: Die Werte werden so übernommen, wie sie eingegeben wurden, und nicht getestet.

**Geschrieben von „Erstellung der Seiten“** in `settings/override/site.ini.append.php`: `[MailSettings] Transport=sendmail`
oder `Transport=SMTP` mit `TransportServer`, `TransportUser` und `TransportPassword`.

### 5.3.5 Datenbanksystem auswählen

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Empfehlung:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
> zusätzlichem Aufwand möglich. Einzelheiten: [Datenbankunterstützung](../../eng-us/help/database-support.md).

**Zweck.** Welches Datenbanksystem die Site verwendet. Die Liste enthält die Systeme, deren PHP-Extension der
Systemtest gefunden hat, beschränkt auf diejenigen, die der Installer kennt (`eZSetupDatabaseMap()` in
`kernel/setup/ezsetupcommon.php`):

| Typ | Angezeigt als | Treiber | Mindestversion |
|---|---|---|---|
| `sqlite3` | SQLite (empfohlen) | `sqlite3` | 3.0.1 |
| `mysqli` | MySQL Improved | `ezmysqli` | 4.1.1 |
| `pgsql` | PostgreSQL | `ezpostgresql` | 8.0 |
| `mongodb` | MongoDB | `mongodb` | 4.0 |
| `oci8` | Oracle | `ezoracle` (braucht die Extension `ezoracle` in `extension/`) | 19.0 |

**Bildschirm.** „Datenbanksystem auswählen“, „Auf Ihrem System wurde Unterstützung für die folgenden Datenbanksysteme
erkannt:“, „Bitte wählen Sie das Datenbanksystem, das Sie verwenden möchten.“ und eine Liste mit Optionsfeldern. Das
empfohlene System ist `setup.ini [DatabaseSettings] DefaultType` (`sqlite3`); es steht an erster Stelle, ist
vorausgewählt und mit „(empfohlen)“ gekennzeichnet, mit dem Text: „SQLite wird empfohlen: Es braucht keinen
Datenbankserver, und Exponential hält die gesamte Datenbank in einer einzigen Datei innerhalb der Installation. Die
übrigen unten aufgeführten Datenbanksysteme bleiben für Sites verfügbar, die einen Datenbankserver verwenden.“

Fehlt die Extension `sqlite3`, wird stattdessen das erste verfügbare System vorausgewählt, und die Seite meldet:
„SQLite ist das empfohlene Datenbanksystem für Exponential, kann hier aber nicht verwendet werden: Die PHP-Extension
sqlite3 ist nicht geladen. Stattdessen wurde *X* ausgewählt. Um SQLite zu verwenden, schalten Sie die Extension sqlite3
in PHP ein und starten Sie den Setup-Assistenten erneut.“

| Feld | Formularname |
|---|---|
| Datenbank | `eZSetupDatabaseType` |

**Wann die Seite übersprungen wird.** Wenn genau eine Datenbank-Extension geladen ist, wird dieses System ohne Seite
gewählt. Es wird nichts geschrieben.

### 5.3.6 Initialisierung der Datenbank

**Zweck.** Wo die Datenbank liegt und wie die Anmeldung erfolgt. Die Felder hängen vom gewählten System ab.

**Bildschirm.** „Initialisierung der Datenbank“ und „Bitte geben Sie die Zugriffsdaten für die Datenbank in die Felder
unten ein.“

Für einen Datenbankserver:

| Feld | Formularname | Standardwert (`setup.ini [DatabaseSettings]`) |
|---|---|---|
| Servername | `eZSetupDatabaseServer` | `DefaultServer=localhost` |
| Port | `eZSetupDatabasePort` | `DefaultPort` (leer), `DefaultPort_pgsql=5432` |
| Benutzername | `eZSetupDatabaseUser` | `DefaultUser=root`, `DefaultUser_pgsql=postgres` |
| Passwort | `eZSetupDatabasePassword` | `DefaultPassword` (leer) |
| Name der Datenbank | `eZSetupDatabaseName` | `DefaultName=exponential` |
| Socket (optional) | `eZSetupDatabaseSocket` | leer; „Falls Sie MySQL nutzen und nicht wissen, was Sie in das Socket-Feld eintragen sollen, lassen Sie es leer“ |

Eine Einstellung `Default<Name>_<type>` in `setup.ini` überschreibt `Default<Name>` für diesen Datenbanktyp.

Für SQLite gibt es ein einziges Feld, **Name der Datenbankdatei** (`eZSetupDatabaseName`, Standard `sqlite.db`), und den
Text „SQLite hält die gesamte Datenbank in einer Datei im Verzeichnis var/storage/sqlite3 dieser Installation. Es
benötigt keinen Server, Benutzer oder Passwort. Die Datei wird angelegt, falls sie nicht existiert; enthält sie bereits
Tabellen, fragt die Seite mit den Website-Details, was damit geschehen soll.“

**Prüfung.** Ein Druck auf **Weiter** verbindet sich mit den angegebenen Werten mit der Datenbank:

- Bei MySQL, PostgreSQL und Oracle mit einem Datenbanknamen wird diese Datenbank direkt verwendet (das Recht
  `SHOW DATABASES` ist nicht nötig). PostgreSQL prüft die Anmeldung gegen die genannte Datenbank, die existieren muss.
  Ohne Namen listet der Installer die Datenbanken auf, die der Benutzer erreichen darf.
- Bei SQLite wird der Dateiname geprüft, bevor irgendetwas die Datei öffnet: Er muss
  `^[A-Za-z0-9][A-Za-z0-9_.-]{0,99}\.(db|db3|sqlite|sqlite3)$` entsprechen, das Verzeichnis `var/storage/sqlite3` muss
  beschreibbar sein (SQLite schreibt dort auch `-wal`- und `-shm`-Dateien), und eine vorhandene Datei muss beschreibbar
  sein und mit dem SQLite-Header beginnen.
- PostgreSQL braucht außerdem die Funktion `digest()` aus der Extension `pgcrypto`; der Installer versucht, sie
  anzulegen.

Die Meldungen, zitiert aus `eZStepInstaller::databaseErrorInfo()`:

| Problem | Meldung |
|---|---|
| SQLite-Name ungültig | „Der Name der Datenbankdatei ist ungültig. Geben Sie einen einfachen Dateinamen mit der Endung .db, .db3, .sqlite oder .sqlite3 an, der aus Buchstaben, Ziffern, Punkten, Bindestrichen und Unterstrichen besteht, zum Beispiel sqlite.db. Die Datei wird in var/storage/sqlite3 abgelegt.“ |
| SQLite-Verzeichnis nicht beschreibbar | „Das Verzeichnis *Verzeichnis* kann vom Webserver (Benutzer *Benutzer*) nicht beschrieben werden. SQLite muss dort die Datenbankdatei anlegen sowie die -wal- und -shm-Dateien, die es daneben führt. Geben Sie diesem Benutzer Schreibrechte für das Verzeichnis (legen Sie es zuerst an, falls es nicht existiert), und versuchen Sie es dann erneut.“ |
| SQLite-Datei nicht beschreibbar | „Die Datenbankdatei *Datei* existiert, kann aber vom Webserver (Benutzer *Benutzer*) nicht beschrieben werden. Geben Sie diesem Benutzer Schreibrechte dafür, oder wählen Sie einen anderen Dateinamen.“ |
| Keine SQLite-Datei | „Die Datei *Datei* existiert und ist keine SQLite-Datenbank. Wählen Sie einen anderen Dateinamen; die Installation überschreibt sie nicht.“ |
| SQLite lässt sich nicht öffnen | „Die SQLite-Datenbankdatei *Datei* konnte nicht geöffnet werden. Den Grund finden Sie in var/log/setup.log und var/log/error.log.“ |
| PostgreSQL-Verbindung fehlgeschlagen | „Die Verbindung zur PostgreSQL-Datenbank ist fehlgeschlagen. Bitte stellen Sie sicher, dass Servername, Port, Benutzername und Passwort richtig sind, dass die auf dieser Seite angegebene Datenbank besteht und dass sich dieser Benutzer mit ihr verbinden darf. Der Server muss Verbindungen von diesem Rechner annehmen (listen_addresses in postgresql.conf und pg_hba.conf).“ mit einem Link zur PostgreSQL-Dokumentation über die Client-Authentifizierung |
| Andere Verbindungsfehler | „Die Datenbank hat die Verbindung nicht akzeptiert. Bitte überprüfen Sie Ihre Einstellungen und probieren es erneut.“ |
| Keine Datenbank erreichbar | „Der ausgewählte Benutzer hat keinen Zugriff auf irgendeine Datenbank. Bitte wechseln Sie den Benutzer oder erstellen eine Datenbank für ihn.“ |
| `pgcrypto` fehlt | „Die Funktion 'digest' ist in Ihrer Datenbank nicht verfügbar, und Exponential kann ohne sie nicht laufen. Sie stammt aus der PostgreSQL-Extension pgcrypto, die das Setup nicht anlegen konnte. Installieren Sie das contrib-Paket des Servers, falls pgcrypto fehlt, lassen Sie dann den Eigentümer der Datenbank oder einen Superuser darin CREATE EXTENSION pgcrypto; ausführen und klicken Sie erneut auf Weiter.“ |
| Server zu alt | „Ihre Datenbankversion *Version* erfüllt nicht die Mindestanforderung *erforderlich*. Weitere Informationen finden Sie auf der Seite mit den Anforderungen.“ |

Jede abgelehnte Verbindung wird außerdem mit Server, Benutzer, Datenbank und Fehlercode (niemals mit dem Passwort) in
`var/log/setup.log` geschrieben. Die Datenbanksysteme werden ausführlich in [Kapitel 9](09-datenbanken.md) behandelt.

### 5.3.7 Sprachunterstützung

**Zweck.** Die Hauptsprache der Site und eventuelle zusätzliche Sprachen.

**Bildschirm.** „Sprachunterstützung“, „Bitte wählen Sie mit den Optionsfeldern die Standardsprache aus. Mit den
Kontrollkästchen können Sie weitere Sprachen auswählen. ...“ und „Die mit der Website gelieferten Inhalte sind in
Englisch (USA) geschrieben. Sie bleiben in Englisch (USA), was immer Sie hier wählen, und die Website zeigt sie
überall dort an, wo noch keine Übersetzung in Ihre Sprachen existiert.“ Darauf folgt eine Liste aller Sprachen, die
ein Locale in `share/locale` haben, jede mit einem Optionsfeld (Hauptsprache) und einem Kontrollkästchen (zusätzliche
Sprache); die Sprache der mitgelieferten Inhalte ist mit „Sprache der mitgelieferten Inhalte“ gekennzeichnet.

| Feld | Formularname |
|---|---|
| Standard (Optionsfeld) | `eZSetupDefaultLanguage` |
| Zusätzlich (Kontrollkästchen) | `eZSetupLanguages[]` |

Beim ersten Aufruf ist die am meisten bevorzugte Sprache des Browsers, für die es ein Locale gibt, die Hauptsprache,
und seine übrigen akzeptierten Sprachen sind angekreuzt.

**Prüfung** (`eZSetupValidateLanguageChoice()`), angezeigt unter „Die Sprachauswahl kann nicht verwendet werden“:

| Problem | Meldung |
|---|---|
| keine Hauptsprache | „Wählen Sie eine Hauptsprache.“ |
| unbekannte Hauptsprache | „Für die Hauptsprache *X* hat diese Installation kein Locale (share/locale).“ |
| unbekannte zusätzliche Sprache | „Für die zusätzliche Sprache *X* hat diese Installation kein Locale (share/locale).“ |
| Hauptsprache auch als zusätzliche Sprache angekreuzt | „*X* ist die Hauptsprache und kann nicht zugleich eine zusätzliche Sprache sein. Entfernen Sie das Häkchen, oder wählen Sie eine andere Hauptsprache.“ |

Lässt sich kein gemeinsamer Zeichensatz finden (nur ohne Unicode-Unterstützung möglich), zeigt die Seite „Keine
Unicode-Unterstützung“ mit dem Rat, Sprachen mit ähnlichen Zeichen zu wählen oder die Datenbank für Unicode zu
konfigurieren.

**Geschrieben.** Eine neue Site verwendet immer UTF-8: `settings/override/i18n.ini.append.php` erhält
`[CharacterSettings] Charset=utf-8`, sobald die Seite akzeptiert ist.

### 5.3.8 Site-Paket

**Zweck.** Das zu installierende Site-Paket ([Kapitel 4](04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)).

**Bildschirm.** „Site Paket“, „Bitte wählen Sie ein Webseitenpaket das Sie ausprobieren möchten oder auf dem Ihre
Webseite basieren soll.“, die verwendete „URL des entfernten Repositorys:“ und ein Eintrag je Site-Paket: ein
Optionsfeld, ein Vorschaubild, sofern das Paket eines hat, „*Zusammenfassung* (Vers. *Version*)“, „Importiert“ oder
„Nicht importiert“ und seine **Abhängigkeiten**, jede „Importiert“, „Nicht importiert“ oder „Unbekannt“. Wird nur ein
Site-Paket angeboten, ist es vorausgewählt. Unter der Liste: **Weitere Pakete** mit einem Link zum vollständigen
Paketarchiv und **Paket hochladen** mit einem Dateifeld und einer Schaltfläche **Upload**.

| Feld | Formularname | Anmerkungen |
|---|---|---|
| Site-Paket (Optionsfeld) | `eZSetup_site_type` | `name` für ein importiertes Paket, `name|url` für eines, das heruntergeladen werden soll |
| Paket hochladen | `PackageBinaryFile` mit **Upload** (`UploadPackageButton`) | importiert eine `.ezpkg`-Datei und zeigt die Seite erneut an |

**Prüfung und Meldungen.**

| Situation | Meldung |
|---|---|
| Weiter gedrückt, ohne ein Paket zu wählen | „Kein Site Paket ausgewählt.“ |
| Der Paketindex lässt sich nicht herunterladen, und es gibt keine lokale `index.xml` | „Das Empfangen der Remote Site Paketliste ist fehlgeschlagen. Sie können die Pakete manuell hochladen.“ |
| Ein Download ist fehlgeschlagen | „Der Download des Pakets '*Name*' ist gescheitert. Sie können das Paket manuell uploaden.“ |
| Das Paket ist ungültig | „Ungültiges Paket“ |
| Ein benötigtes Paket konnte nicht geholt werden | ein Fehler, der das Paket nennt |
| Upload ohne Datei | „Kein Paket zum Upload ausgewählt.“ |
| Upload von etwas anderem | „Die hochgeladene Datei ist kein Exponential Paket“ |
| Ein entferntes Paket wurde heruntergeladen | „Paket '*Name*' und seine Abhängigkeiten wurden erfolgreich heruntergeladen. Drücken Sie 'Weiter', um fortzufahren.“ |

Die Liste bietet den entfernten Eintrag eines Pakets an, wenn es noch nicht importiert ist oder die entfernte Version
neuer ist, andernfalls die importierte Kopie. Die Wahl eines entfernten Eintrags lädt das Paket neu herunter (eine
ältere importierte Kopie wird ersetzt), gefolgt von jedem Paket, das es benötigt und das fehlt oder älter als seine
`min-version` ist. Der entfernte Index ist `settings/package.ini [RepositorySettings] RemotePackagesIndexURL`.

### 5.3.9 Sprachoptionen der Pakete

**Zweck.** Wird nur angezeigt, wenn die gewählten Pakete Inhalte in Sprachen enthalten, die Sie nicht gewählt haben
(die eigene Sprache der mitgelieferten Inhalte, `eng-US`, zählt nie).

**Bildschirm.** „Sprachoptionen“, „Die Sprachen, die Sie für die Seite ausgewählt haben, sind nicht in den gewählten
Paketen verfügbar. Um diesen Konflikt zu lösen, benutzen Sie bitte die Sprachzuordnung:“ und eine Tabelle **Sprache** /
**Aktion** mit einer Zeile je Paketsprache. Die Aktionen sind:

| Aktion | Wirkung |
|---|---|
| **Neue Sprache** (vorausgewählt) | die Sprache wird angelegt, und die Inhalte bleiben in ihr, neben Ihren Sprachen |
| **Zuordnen zu** *einer Sprache der Site* | die Inhalte werden ohne Übersetzung als diese Sprache umgekennzeichnet |
| **Den Inhalt in dieser Sprache überspringen** | die Inhalte in dieser Sprache werden nicht installiert |

Der Hinweis auf der Seite: „Hinweis: Wird die Sprache angelegt, bleiben die Paketinhalte in der Sprache, in der sie
geschrieben wurden, neben den von Ihnen gewählten Sprachen. Eine Zuordnung kennzeichnet diese Inhalte als eine andere
Sprache um, ohne sie zu übersetzen.“

### 5.3.10 Konfiguration der Siteaccesses

**Zweck.** Wie eine Anfrage einem Siteaccess zugeordnet wird. Jede Installation erhält drei Siteaccesses, `site`,
`admin` und `editor`; diese Seite entscheidet, woran sie unterschieden werden. Ein vierter, `adminui` (die
[Exponential Admin UI](../../eng-us/features/6.0/exp-adminui.md)), kommt hinzu, wenn die Extension `exp_adminui` in der
Installation vorhanden ist; er wird immer über seinen Pfad `/adminui` erreicht, und diese Seite ändert daran nichts.

**Bildschirm.** „Konfiguration der Siteaccesses“, „Bitte wählen Sie eine Zugriffmethode, die Sie für Ihre Seite
verwenden möchten. Die Zugriffsmethode gibt an, wie der Zugang von einem Webbrowser erfolgen soll. Falls Sie sich nicht
sicher sind, wählen Sie URL.“, die Erläuterung der drei Siteaccesses und die Konvention für Hostnamen
(`yourdomain.com`, `admin.yourdomain.com`, `edit.yourdomain.com`).

| Feld | Formularname | Werte |
|---|---|---|
| Zugriffs Methode | `eZSetup_site_access` | `url` **URL (empfohlen)**, `port` **Port**, `hostname` **Hostname** |

**Was die Wahl auf der nächsten Seite vorbelegt:**

| Methode | Site | Admin | Redaktion |
|---|---|---|---|
| URL | der Bezeichner des Pakets, z. B. `sevenx_multisite` | *Bezeichner*`_admin` | `editor` |
| Port | `8080` | `8081` | `8082` |
| Hostname | *Bezeichner*`.`*dieser Host* | *Bezeichner*`-admin.`*dieser Host* | `edit.`*dieser Host* |

Das sind Vorschläge; die nächste Seite zeigt sie in bearbeitbaren Feldern an. Zwei Dinge sollten Sie wissen, bevor Sie
sie übernehmen:

- **Der Assistent lehnt `admin` und `user` als Zuordnungswerte ab** ([5.3.11](#5311-seitendetails)). Deshalb lautet der
  URL-Vorschlag für die Administration *Bezeichner*`_admin` (mit dem Standardpaket
  `https://example.com/sevenx_multisite_admin/`) und nicht `/admin`. Wenn Sie `/site` und `/admin` möchten, verwenden
  Sie den Kickstarter oder `exp:install`, die diese Werte akzeptieren.
- **„Dieser Host“ ist der Name, den der Browser verwendet hat.** Öffnen Sie den Assistenten als `http://192.0.2.10/`
  oder `localhost`, werden die Vorschläge für die Hostnamen daraus gebildet; tragen Sie die echten Namen ein, bevor Sie
  fortfahren.

Die Zuordnung über Port und Hostname braucht eine passende Konfiguration des Webservers
([Kapitel 8](08-die-website-ausliefern.md)): Der Server muss auf allen drei Ports lauschen oder alle drei Hostnamen
empfangen. Beachten Sie bei Velocity, dass sein eigener HTTPS-Port standardmäßig 8080 ist (`settings/velocity.ini`,
`[ServerSettings] HTTPSPort`), dieselbe Nummer, die der Assistent für die öffentliche Site vorschlägt.

Ein durchgespieltes Beispiel: Für `www.example.com` (öffentlich), `admin.example.com` und `edit.example.com` wählen Sie
hier **Hostname** und tragen auf der Seite „Seitendetails“ `www.example.com`, `admin.example.com` und
`edit.example.com` als Benutzer-, Admin- und Redaktions-Hostname ein sowie `https://www.example.com` als „Url der
Seite“.

### 5.3.11 Seitendetails

**Zweck.** Titel, Adresse und Zuordnungswerte der Site, der Absender der optionalen E-Mails der Site und die Datenbank.

**Bildschirm.** „Seitendetails“, „Hier können Sie die Informationen Ihrer Seite ändern, die Sie installieren möchten.
Zusätzlich dazu können Sie eine Datenbank für sie wählen.“, dann die Feldgruppe „Details für Site“:

| Feld | Formularname | Anmerkungen |
|---|---|---|
| Titel | `eZSetup_site_templates_title` | der Name der Site (`[SiteSettings] SiteName`) |
| Url der Seite | `eZSetup_site_templates_url` | die Adresse der Site (`SiteURL`, ohne Schema geschrieben) |
| Benutzer Pfad / Benutzer Port / Benutzer Hostname | `eZSetup_site_templates_value` | Zuordnungswert von `site` |
| Admin Pfad / Admin Port / Admin Hostname | `eZSetup_site_templates_admin_value` | Zuordnungswert von `admin` |
| Pfad der Redaktion / Port der Redaktion / Hostname der Redaktion | `eZSetup_site_templates_editor_value` | Zuordnungswert von `editor`; die Seite erklärt, was der Redaktions-Siteaccess ist |
| Name der Organisation | `eZSetup_site_templates_organisation_name` | wer die optionalen E-Mails versendet; mit dem Titel vorbelegt |
| Postanschrift | `eZSetup_site_templates_organisation_address` | darf leer bleiben; die Statusseite der E-Mail-Einstellungen warnt dann |
| Datenbank | `eZSetup_site_templates_database` | die Datenbank (Server) oder der Dateiname (SQLite: „Eine Datei in var/storage/sqlite3, die angelegt wird, falls sie nicht existiert.“, wobei die vorhandenen Dateien in der Liste des Felds angeboten werden) |
| Aktion | `eZSetup_site_templates_existing_database` | wird nur angezeigt, wenn die Datenbank bereits Tabellen enthält |

**Prüfung der Zuordnungswerte.** Pfadwerte müssen `^[a-zA-Z0-9_]*$` entsprechen, Ports `^[0-9]*$`, Hostnamen
`^[a-zA-Z0-9.\-:]*$`. Alle drei müssen verschieden sein, keiner darf leer sein, und **`admin` und `user` sind
reserviert und können im Assistenten nicht als Zuordnungswert verwendet werden**. Ein abgelehnter Wert wird mit `*`
markiert, und eine dieser Warnungen erscheint:

- „'User path' und 'Admin path' dürfen nur Buchstaben ('a-zA-Z'), Zahlen (0-9) und Unterstriche ('_') enthalten. Die
  Werte dürfen nicht 'admin' oder 'user' lauten und dürfen nur einmal verwendet werden. Bitte ändern Sie ungültige
  Werte, die mit * gekennzeichnet sind“
- „'User port' und 'Admin port' soll nur Zahlen (0-9) enthalten, bitte korrigieren Sie Werte, die mit * gekennzeichnet
  sind“
- „'User hostname' und 'Admin hostname' dürfen nur Buchstaben ('a-zA-Z'), Zahlen ('0-9'), Bindestrich ('-'), Punkte
  ('.') und Doppelpunkte (':') enthalten. Bitte ändern Sie die ungültigen Angaben, die mit einem * gekennzeichnet sind“

Im Hostnamen-Modus wird ein Unterstrich in dem Wert, der Ihnen wieder angezeigt wird, durch einen Bindestrich ersetzt.

**Prüfung der Datenbank.** Es wird erneut eine Verbindung zur Datenbank aufgebaut. Schlägt deren Anforderungsprüfung
fehl, wird die Datenbankmeldung aus [Abschnitt 5.3.6](#536-initialisierung-der-datenbank) auf dieser Seite angezeigt; kann
überhaupt keine Verbindung hergestellt werden, kehrt der Assistent zur Initialisierung der Datenbank zurück. Enthält
sie bereits Tabellen, zeigt die Seite die Warnung „Die ausgewählte Datenbank ist nicht leer. Bitte wählen Sie eine der
Alternativen unten.“ (bei SQLite: „Die Datenbankdatei *Datei* enthält bereits *N* Tabellen.“) und die Auswahlliste
**Aktion**:

| Option | Wert | Entspricht `DatabaseAction=` |
|---|---|---|
| Die Daten belassen und neue hinzufügen | `1` | `ignore` |
| Bestehende Daten entfernen | `2` | `remove` |
| Die Daten belassen und nichts weiteres tun | `3` | `skip` |
| Ich habe eine neue Datenbank gewählt | `4` | (zeigt die Seite mit dem neuen Datenbanknamen erneut an) |

> **Warnung.** „Bestehende Daten entfernen“ löscht die vorhandenen Tabellen, wenn die Installation läuft; bei SQLite
> wird die Datei geleert. Wählen Sie diese Option nur für eine Datenbank, die nichts enthält, was Sie brauchen, und
> legen Sie vorher eine Sicherung an.

### 5.3.12 Administrator der Seite

**Zweck.** Das Administratorkonto. Sein Login ist immer **admin**. Die Seite wird übersprungen, wenn die
Datenbankaktion „Die Daten belassen und nichts weiteres tun“ lautet, weil dann der vorhandene Administrator erhalten
bleibt.

**Bildschirm.** „Administrator der Seite“, „Auf dieser Seite können Sie den Administrator Ihrer Installation
festlegen. Dies stellt sicher, dass Ihre Installation sicher ist und ein gültiger Name und E-Mail Adresse verwendet
wird.“, dann „Administrator Einstellungen“:

| Feld | Formularname | Vorbelegt |
|---|---|---|
| Login | (angezeigt, nicht bearbeitbar) | `admin` |
| Vorname | `eZSetup_site_templates_first_name` | Administrator |
| Nachname | `eZSetup_site_templates_last_name` | User |
| E-Mail Adresse | `eZSetup_site_templates_email` | |
| Passwort | `eZSetup_site_templates_password1` | |
| Passwort bestätigen | `eZSetup_site_templates_password2` | |

**Prüfung und Meldungen** (unter „Warnung“):

| Problem | Meldung |
|---|---|
| Vorname leer | „Sie müssen den Vornamen angeben.“ |
| Nachname leer | „Sie müssen den Nachnamen angeben.“ |
| E-Mail-Adresse leer | „Sie müssen eine E-Mail Adresse angeben.“ |
| E-Mail-Adresse ungültig (`eZMail::validate()`) | „Sie müssen eine gültige E-Mail Adresse angeben.“ |
| Passwort leer | „Sie müssen ein Passwort angeben.“ |
| die beiden Passwörter unterscheiden sich | „Die Passwörter stimmen nicht überein.“ |
| kürzer als `site.ini [UserSettings] MinPasswordLength` (10) | „Das Passwort ist zu kurz.“ |
| ein bekanntes oder häufiges Passwort, auch wenn es lang genug ist (`exponential`, `administrator`, `qwertyuiop`, ein Wort mit einer Jahreszahl oder `!` am Ende, Buchstabenersetzungen wie `p@ssw0rd`; die Liste von `expWeakPasswordList`, seit 6.0.15) | „Dieses Passwort ist bekannt oder zu häufig, jeder könnte es erraten. Bitte wählen Sie ein anderes.“ |

Im Browser-Assistenten wählen Sie das Passwort selbst, und ein leeres wird abgelehnt. (Das automatische Ersetzen eines
leeren oder bekannten Passworts gilt für `kickstart.ini`, [Kapitel 6](06-kickstarter.md#64-referenz-zu-kickstartini).)

**Nur für öffentliche Demos:** Wenn das PHP des Webservers die Umgebungsvariable `EXP_DEMO_MODE=1` hat (Demo-Modus, seit
6.0.15), weist die Seite in einem Hinweis darauf hin, und ein bekanntes oder kurzes Passwort wird akzeptiert (`publish`, `publishing`), damit eine öffentliche Demo das Passwort installieren kann,
das sie veröffentlicht; ein leeres wird weiterhin abgelehnt. Nach der Installation kann niemand ein
Administratorpasswort ändern oder zurücksetzen, solange die Variable gesetzt ist. Setzen Sie sie niemals für eine echte
Site: [Demo-Modus](../../eng-us/bc/6.0/demo-mode.md).

**Geschrieben.** Die E-Mail-Adresse des Administrators wird zu `[MailSettings] AdminEmail`.

### 5.3.13 Seitensicherheit

**Zweck.** Ratschläge, wenn die Site nicht im Virtual-Host-Modus läuft. Die Seite wird übersprungen, wenn das
Installationsverzeichnis eine `.htaccess`-Datei enthält oder wenn die Site ohne `index.php` in ihren Adressen läuft
(Virtual-Host-Modus).

**Bildschirm.** „Seitensicherheit“, „Ihre Seite wird nicht im Virtual-Host Modus betrieben, was unsicher ist. Es wird
empfohlen Exponential im Virtual-Host Modus laufen zu lassen. Falls Sie diese Möglichkeit nicht haben, sollten Sie den
Anweisungen unten folgen, wie Sie eine .htaccess-Datei verwenden. Diese Datei informiert den Webserver darüber, dass
der Zugang zu bestimmten Dateien eingeschränkt wird.“, gefolgt von den auszuführenden Befehlen:

```bash
cd /path/to/the/installation
cp .htaccess_root .htaccess
```

und „Falls Sie keinen Zugriff auf eine Shell haben, müssen Sie die Datei per FTP kopieren oder Ihren Webhoster fragen,
dies für Sie zu tun.“ Diese Seite schreibt nichts. Die Konfiguration des Webservers steht in
[Kapitel 8](08-die-website-ausliefern.md), die Härtung in [Kapitel 13](13-sicherheitshaertung.md).

### 5.3.14 Registrierung

**Zweck.** Eine Seite über die Community. Die Überschrift lautet „Open-Source-Software ist nichts ohne eine lebendige
Community!“, gefolgt von Links zu share.exponential.earth (dessen Blogs, Artikeln und Foren). Die Seite hat kein
Formular: Ein Druck auf **Weiter** sendet nichts.

### 5.3.15 Erstellung der Seiten

Dieser Schritt hat keine eigene Seite. Ein Druck auf **Weiter** auf der Seite „Registrierung“ führt ihn aus: Das kann
einige Minuten dauern, bei einem großen Site-Paket erheblich länger. Er:

1. verbindet sich mit der Datenbank und leert sie je nach Aktion, installiert dann `share/db_schema.dba` und
   `share/db_data.dba`;
2. installiert das Site-Paket und die Pakete, die es benötigt, und führt die Installationsskripte des Pakets aus;
3. richtet das Administratorkonto ein;
4. schreibt `settings/siteaccess/site/`, `settings/siteaccess/admin/` und `settings/siteaccess/editor/` (und
   `settings/siteaccess/adminui/`, wenn `extension/exp_adminui` vorhanden ist) sowie die Dateien in `settings/override/` ([Kapitel 4](04-installationsweg-waehlen.md#44-was-eine-installation-schreibt)),
   einschließlich `CheckValidity=false`.

Schlägt etwas fehl, erscheint die Seite „Erstellung der Seiten“: „Der Setup-Assistent war nicht in der Lage die
Erstellung der ausgewählten Seiten abzuschließen.“, „Die folgenden Fehler wurden erkannt:“ mit einer Liste
codierter Fehler und „Falls Sie der Meinung sind alle Fehler behoben zu haben, klicken Sie auf den Knopf
‚Wiederholen‘.“ Angeboten wird nur **Wiederholen**.

| Code | Bedeutung |
|---|---|
| `EZSW-001` | „Laden der Datenbank-Schemadatei share/db_schema.dba fehlgeschlagen“ |
| `EZSW-002` | „Laden der Datenbank-Datendatei share/db_data.dba fehlgeschlagen“, oder die eigenen Daten eines Datentyps konnten nicht importiert werden |
| `EZSW-003` | „Laden des Schema-Handlers für *Datenbank* fehlgeschlagen“ |
| `EZSW-004` | „Einfügen der Daten in *Datenbank* fehlgeschlagen“ mit der Meldung der Datenbank |
| `EZSW-005` | „Verbindung zur Datenbank *Name* fehlgeschlagen“ |
| `EZSW-020` bis `EZSW-025` | der Administrator-Benutzer konnte nicht geholt, mit einer neuen Version versehen oder veröffentlicht werden |
| `EZSW-040` | „Initialisierung des Site-Pakets '*Name*' fehlgeschlagen“ |
| `EZSW-041` | „Site-Paket konnte nicht geholt werden: '*Name*'“ |
| `EZSW-050`, `EZSW-051` | ein benötigtes Paket konnte nicht geholt oder installiert werden |
| `EZSW-070` | eine Benutzereinstellung konnte nicht angelegt werden |
| `EZSW-080` | „Die Nachinstallation des Site-Pakets '*Name*' hat bei Schritt *N* von *M* (*Funktion*) angehalten: Die Schritte danach wurden nicht ausgeführt“ |
| `EZSW-081` | „Der Siteaccess editor konnte nicht aus settings/siteaccess/admin erstellt werden“ |
| `EZSW-082` | „Der Siteaccess adminui konnte nicht aus settings/siteaccess/admin erstellt werden“ |

`var/log/setup.log` enthält die Einzelheiten des Fehlers, mit einem Hinweis neben bekannten Problemen.

### 5.3.16 Beendet

**Zweck.** Zeigt, wo die neue Site zu finden ist. Das Erreichen dieser Seite beendet den Setup-Durchlauf in
`var/log/setup.log` und die Wartungssperre.

**Bildschirm.** „Beendet“, „Exponential wurde mit dem ausgewählten Seitentyp installiert. Sie finden den Benutzernamen
in den unten aufgeführten Details.“, ein Hinweis, dass der erste Aufruf der Benutzer- oder Administrationsoberfläche
einige Zeit dauert (30 bis 60 Sekunden), während Exponential die Site vorbereitet, eventueller Text, den das Site-Paket
hinzufügt, und **Seitendetails**: Titel, Benutzerseite (Frontend), Administrationsoberfläche (Backend), Redaktions-Site
(jeweils ein Link) und Benutzername `admin`.

## 5.4 Einen Durchlauf beobachten: das Setup-Log

Der Assistent schreibt je Durchlauf einen Eintrag nach `var/log/setup.log` (`kernel/classes/expsetuplog.php`). Weil der
Assistent aus einer Anfrage pro Seite besteht, startet die erste Anfrage den Durchlauf, und die späteren setzen ihn aus
`var/log/setup-run.state` fort. Für jeden Schritt hält das Log Zeilen `>> Step` und `<< Step` mit dem Ergebnis fest
(zum Beispiel „accepted“, „rejected (asked again)“, „shown“), die Fehler und Warnungen, die der Schritt verursacht hat,
einen Hinweis neben bekannten Problemen und nach der Installation eine Reihe von Gesundheitsprüfungen mit einer
abschließenden Zeile RESULT und NEXT. Passwörter werden maskiert. Frühere Durchläufe werden als `setup.log.1`,
`setup.log.2` und so weiter aufbewahrt.

```bash
tail -f var/log/setup.log          # follow the wizard from a shell
```

## 5.5 Sprache des Assistenten, Sprachen der Site

Im Assistenten gibt es zwei verschiedene Sprachauswahlen:

- **Die eigene Sprache des Assistenten** (Willkommensseite) ändert nur die Übersetzung, in der die Seiten angezeigt
  werden.
- **Die Sprachen der Site** (Seite „Sprachunterstützung“) bestimmen die Inhaltssprachen und das Locale der
  Siteaccesses: `[RegionalSettings] Locale`, `ContentObjectLocale` und `SiteLanguageList[]` sowie `[ContentSettings]
  TranslationList` für die zusätzlichen Sprachen. Die mitgelieferten Inhalte bleiben in `eng-US` und werden zur
  Rückfallsprache am Ende von `SiteLanguageList`.

Mehr dazu: [Übersetzungen und Sprachen](../../eng-us/features/6.0/translations-and-languages.md).

## 5.6 Wie kickstart.ini Seiten vorbelegt und überspringt

Der Browser-Assistent liest `kickstart.ini` im Installationsverzeichnis, und zwar über
`eZINI::instance( 'kickstart.ini', '.' )` im Konstruktor jedes Schritts. Für jeden Schritt gilt:

- **Kein Abschnitt für den Schritt:** Die Seite wird wie oben beschrieben angezeigt.
- **Ein Abschnitt mit `Continue=false` oder ohne `Continue`:** Die Seite wird angezeigt, vorbelegt mit den Werten des
  Abschnitts.
- **Ein Abschnitt mit `Continue=true`:** Der Schritt übernimmt die Werte, und der Assistent geht weiter, ohne die Seite
  anzuzeigen.

Die Abschnitte sind nach den Bezeichnern der Schritte benannt: `welcome`, `email_settings`, `database_choice`,
`database_init`, `language_options`, `site_types`, `site_access`, `site_details`, `site_admin`, `security`,
`registration`. Die Schlüssel sind Thema von [Kapitel 6](06-kickstarter.md#64-referenz-zu-kickstartini). Die Schritte
Systemtest, Feineinstellung des Systems, Sprachoptionen der Pakete, Erstellung der Seiten und Beendet haben keinen
Abschnitt.

Die Werte aus `kickstart.ini` werden verwendet, wenn ein Schritt zum ersten Mal erreicht wird. Gehen Sie mit **Zurück**
zu einem Schritt, zeigt die Seite stattdessen, was Sie eingegeben haben, sodass Sie einen Wert aus der Datei
korrigieren können.

Zwei Vorsichtshinweise:

- `[registration]` sendet nichts, sofern dort nicht `Send=true` steht, und selbst dann geht der Bericht nur an
  `settings/setup.ini [RegistrationSettings] Receiver`, das standardmäßig leer ist. Die alte Upstream-Adresse für die
  Registrierung wird nie verwendet.
- Der Einstellungs-Cache hält eine kompilierte Kopie von `kickstart.ini` in `var/cache/ini/kickstart-*.php`. Wenn
  `config.php` die Prüfung der Änderungszeit von INI-Dateien abschaltet (`define( 'EZP_INI_FILEMTIME_CHECK', false );`,
  aus Geschwindigkeitsgründen in `config.php-RECOMMENDED` angeboten), wird eine bearbeitete `kickstart.ini` nicht
  bemerkt. Löschen Sie diese Dateien, nachdem Sie `kickstart.ini` für den Browser-Assistenten von Hand bearbeitet haben.
  (Der Kickstarter löscht sie selbst.)

Wenn Sie jede Seite selbst beantworten möchten, verschieben Sie `kickstart.ini`, bevor Sie den Assistenten starten.

## 5.7 Einen stehen gebliebenen Assistenten neu starten

Ein Assistent kann auf halbem Weg stehen bleiben: ein geschlossener Browser, ein neu gestarteter Server, ein Schritt,
der immer wieder fehlschlägt. Was zu tun ist, hängt davon ab, wie weit er gekommen ist.

**Die Installation ist noch nicht gelaufen** (Sie haben auf der Seite „Registrierung“ nie **Weiter** gedrückt, oder
„Erstellung der Seiten“ ist fehlgeschlagen):

1. Öffnen Sie die Adresse der Site in dem Browser, der den Assistenten gestartet hat. Eine einfache Anfrage zeigt immer
   die Willkommensseite; die Antworten des früheren Versuchs sind verloren, weil sie in der Seite gespeichert waren.
2. Erhält ein anderer Browser (oder derselbe Browser ohne sein Cookie) „The site is being set up“, warten Sie entweder,
   bis 30 Minuten nach der letzten Anfrage an den Assistenten vergangen sind, oder heben Sie die Sperre in einer Shell
   auf:

   ```bash
   php bin/php/maintenance.php status
   php bin/php/maintenance.php off          # or: php bin/php/console exp:maintenance off
   ```

3. Ist „Erstellung der Seiten“ fehlgeschlagen, lesen Sie `var/log/setup.log`, beheben Sie die Ursache und drücken Sie
   **Wiederholen**, oder beginnen Sie bei „Willkommen“ von vorn. Wählen Sie bei einem Datenbankserver auf der Seite
   „Seitendetails“ **Bestehende Daten entfernen**, damit die halb geschriebenen Tabellen ersetzt werden.

**Die Installation ist gelaufen, aber Sie möchten den Assistenten noch einmal ausführen** (`CheckValidity` ist bereits
`false`):

1. Legen Sie eine Sicherung der Datenbank sowie von `settings/override/` und `settings/siteaccess/` an.
2. Setzen Sie `CheckValidity=true` in `settings/override/site.ini.append.php`:

   ```bash
   php bin/php/console exp:ini where site.ini/SiteAccessSettings/CheckValidity
   ```

   zeigt, woher der Wert kommt; bearbeiten Sie diese Datei und setzen Sie `CheckValidity=true`.
3. Leeren Sie den INI-Cache, damit die Änderung gelesen wird: `php bin/php/ezcache.php --clear-tag=ini`. Wenn Velocity
   die Site ausliefert, starten Sie es neu ([Kapitel 8](08-die-website-ausliefern.md)).
4. Öffnen Sie die Site. Der Assistent beginnt bei „Willkommen“. Wählen Sie auf der Seite „Seitendetails“, was mit den
   vorhandenen Daten geschehen soll.

**Die Site zeigt nach einer fehlgeschlagenen Installation über die Kommandozeile nur noch die Wartungsseite**: Der
Kickstarter lässt den Wartungsmodus nach einem Fehler eingeschaltet. Beheben Sie die Ursache und führen Sie ihn erneut
aus, oder führen Sie `php bin/php/maintenance.php off` aus.

---

## Quellen

In diesem Repository:

- [Exponential 6.0 installieren](../../eng-us/INSTALL.md), Abschnitt 5
- [Das neue Aussehen des Setup-Assistenten und der Redaktions-Siteaccess](../../eng-us/features/6.0/setup-wizard-and-editor-siteaccess.md)
- [Standardwerte einer sauberen Installation](../../eng-us/features/6.0/clean-install-defaults.md)
- [Wartungsmodus](../../eng-us/features/6.0/maintenance-mode.md)
- [Installer-Logs und Startdaten](../spezifikationen/6.0/installer-protokolle-und-startdaten.md)
- [SQLite-Datenbank](../../eng-us/features/6.0/sqlite-database.md), [SQLite-Installation](../../eng-us/features/6.0/platform-sqlite-install.md),
  [Datenbanktreiber](../spezifikationen/6.0/datenbanktreiber-sqlite-oracle.md)
- [Übersetzungen und Sprachen](../../eng-us/features/6.0/translations-and-languages.md)
- [Die Konsole: `exp:ini`](../../eng-us/bc/6.0/console-exp-ini.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../eng-us/bc/6.0/behaviour-changes-2026-09b.md)
- Code: `kernel/setup/steps/`, `kernel/setup/ezsetupcommon.php`, `kernel/setup/ezsetuptests.php`,
  `kernel/private/classes/views/setup/ezsetup.php`, `kernel/classes/expmaintenance.php`,
  `kernel/classes/expsetuplog.php`; Templates in `design/standard/templates/setup/init/` und
  `design/standard/templates/setup/tests/`; Einstellungen `settings/setup.ini`, `settings/site.ini [SetupSettings]`,
  `settings/package.ini [RepositorySettings]`

Extern:

- PHP-Laufzeitkonfiguration (`memory_limit`, `max_execution_time`, `file_uploads`, `date.timezone`):
  <https://www.php.net/manual/en/ini.list.php>
- PHP SQLite3: <https://www.php.net/manual/en/book.sqlite3.php>
- PostgreSQL-Client-Authentifizierung: <https://www.postgresql.org/docs/current/client-authentication.html>
- PostgreSQL pgcrypto: <https://www.postgresql.org/docs/current/pgcrypto.html>
- HTTP 503 und `Retry-After` (RFC 9110): <https://www.rfc-editor.org/rfc/rfc9110.html#name-503-service-unavailable>
  und <https://www.rfc-editor.org/rfc/rfc9110.html#name-retry-after>
- HTTP-Cookies, `HttpOnly`, `Secure` und `SameSite` (das Cookie `exp_setup_wizard` des Assistenten):
  <https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/Cookies>
- Exponential auf GitHub: <https://github.com/se7enxweb/exponential>

[Inhalt](README.md) · Zurück: [4. Eine Installationsmethode wählen](04-installationsweg-waehlen.md) · Weiter: [6. Der Kickstarter](06-kickstarter.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
