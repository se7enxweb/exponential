# 4. Eine Installationsmethode wählen

Exponential 6.0 lässt sich auf drei Arten installieren, die alle zur gleichen Art von Installation führen: mit dem
**Setup-Assistenten** im Browser, mit dem **Kickstarter** auf der Kommandozeile, gesteuert durch eine Datei
`kickstart.ini`, und mit der **Konsoleninstallation** `exp:install` in einem einzigen Befehl, die diese Datei aus
Optionen erzeugt. Unter allen dreien läuft dieselbe Engine: die Schrittklassen in `kernel/setup/steps/`, in der
Reihenfolge der Schritttabelle in `kernel/setup/steps/ezstep_data.php`, und ein **Site-Paket**, dessen
Installationsskripte die neue Website formen. Dieses Kapitel erklärt diese gemeinsame Engine, vergleicht die drei
Methoden, führt genau auf, was eine Installation auf die Festplatte und in die Datenbank schreibt, und empfiehlt für
jede Situation eine Methode, einschließlich unbeaufsichtigter Installationen in CI-Pipelines und Containern. Wenn Sie
bereits wissen, welche Methode Sie möchten, springen Sie direkt zu deren Kapitel; wenn Sie mehr als einmal
installieren oder die Installation automatisieren werden, lesen Sie zuerst 4.4 und 4.6, denn dort steht, was eine
Neuinstallation zerstört.

[Inhalt](README.md) · Zurück: [3. Den Code beschaffen](03-den-code-beschaffen.md) · Weiter: [5. Der Setup-Assistent](05-setup-assistent.md)

## 4.1 Ein Installer, drei Frontends

Jede Installationsmethode durchläuft dieselbe Folge von Schritten. Jeder Schritt ist eine Klasse `eZStep<Name>` in
`kernel/setup/steps/ezstep_<file>.php`, und die Reihenfolge wird durch `eZStepData::$StepTable` festgelegt:

| # | Schrittklasse | Datei | Fragt nach |
|---|---|---|---|
| 0 | `Welcome` | `ezstep_welcome.php` | der Sprache des Assistenten selbst |
| 1 | `SystemCheck` | `ezstep_system_check.php` | nichts: führt die kritischen Systemtests aus |
| 2 | `SystemFinetune` | `ezstep_system_finetune.php` | nichts: führt auf Anforderung die optionalen Tests aus |
| 3 | `EmailSettings` | `ezstep_email_settings.php` | sendmail/MTA oder SMTP |
| 4 | `DatabaseChoice` | `ezstep_database_choice.php` | dem Datenbanksystem |
| 5 | `DatabaseInit` | `ezstep_database_init.php` | Server, Port, Datenbank, Benutzer, Passwort, Socket |
| 6 | `LanguageOptions` | `ezstep_language_options.php` | der Hauptsprache und den weiteren Sprachen |
| 7 | `SiteTypes` | `ezstep_site_types.php` | dem Site-Paket |
| 8 | `PackageLanguageOptions` | `ezstep_package_language_options.php` | dem Umgang mit Paketsprachen, die Sie nicht gewählt haben |
| 9 | `SiteAccess` | `ezstep_site_access.php` | der Zuordnung der Siteaccesses: über URL, Port oder Hostname |
| 10 | `SiteDetails` | `ezstep_site_details.php` | Titel, URL, Zuordnungswerten, Datenbank und dem Umgang mit vorhandenen Daten |
| 11 | `SiteAdmin` | `ezstep_site_admin.php` | Name, E-Mail-Adresse und Passwort des Administrators |
| 12 | `Security` | `ezstep_security.php` | nichts: gibt Hinweise, wenn die Website nicht im Virtual-Host-Modus läuft |
| 13 | `Registration` | `ezstep_registration.php` | nichts im Browser; eine optionale Registrierungs-E-Mail aus `kickstart.ini` |
| 14 | `CreateSites` | `ezstep_create_sites.php` | nichts: **schreibt die Datenbank, die Pakete und die Einstellungen** |
| 15 | `Final` | `ezstep_final.php` | nichts: zeigt die Adressen der neuen Website |

`CreateSites` ist mit `count_step => false` markiert: Dieser Schritt wird nie als Seite angezeigt und zählt nicht im
Fortschrittsbalken des Assistenten. Er ist der einzige Schritt, der die Datenbank, das Site-Paket und die
Siteaccess-Einstellungen installiert; die Schritte davor sammeln und prüfen Antworten (`DatabaseInit` öffnet die
Datenbank zum Test, wodurch eine leere SQLite-Datei angelegt wird, falls keine existiert, und im Browser schreibt
`LanguageOptions` den Zeichensatz nach `settings/override/i18n.ini.append.php`) und halten ihre Ergebnisse für die
nachfolgenden Schritte im Speicher. Deshalb lässt sich eine Installation nicht auf halbem Weg in einem neuen Prozess
fortsetzen: Der Kickstarter verweigert einen `--start-step`, der diese Ergebnisse bräuchte
([Kapitel 6](06-kickstarter.md#610-erneut-ausführen-und-fortsetzen)).

Die drei Frontends unterscheiden sich nur darin, **woher die Antworten kommen** und **wie die Schritte gesteuert
werden**:

```text
                 browser forms             kickstart.ini               command-line options
                 (one page per step)       (one section per step)      (exp:install --db=... --url=...)
                        |                          |                                 |
                        |                          |                  bin/php/install.php writes a
                        |                          |                  temporary kickstart.ini
                        v                          v                                 v
   kernel/setup/ezsetup.php           expKickstarter (kernel/classes/expkickstarter.php)
   (module setup, view init)          runs the same step classes in one CLI process
                        \                          |                                 /
                         \_________________________|________________________________/
                                                   v
                       eZStep* classes in kernel/setup/steps/  ->  CreateSites
                                                   v
                         site package install scripts (eZSitePreInstall, eZSitePostInstall, ...)
```

Daraus ergeben sich zwei Folgen:

- **Eine Korrektur oder ein Standardwert in einem Schritt gilt überall.** Die Prüfungen der SQLite-Datei, die
  Validierung der Sprachen und das erzeugte Administratorpasswort verhalten sich im Browser und auf der Kommandozeile
  gleich (mit den Unterschieden, auf die dieses Handbuch dort hinweist, wo es sie gibt).
- **`kickstart.ini` wird auch vom Setup-Assistenten im Browser gelesen.** Der Konstruktor jedes Schritts
  (`eZStepInstaller`) liest `kickstart.ini` im Installationsverzeichnis. Findet der Assistent einen Abschnitt für
  einen Schritt, füllt er diese Seite vorab aus oder überspringt sie, wenn der Abschnitt `Continue=true` enthält.
  Siehe [Kapitel 5](05-setup-assistent.md#56-wie-kickstartini-seiten-vorbelegt-und-überspringt).

## 4.2 Die drei Methoden im Überblick

| | Setup-Assistent | Kickstarter | Konsoleninstallation |
|---|---|---|---|
| Einstiegspunkt | jede Seite der Website, solange `[SiteAccessSettings] CheckValidity=true` gilt | `php bin/php/console exp:kickstarter run --force` (auch `php bin/php/kickstarter.php run --force`) | `php bin/php/console exp:install` (auch `php bin/php/install.php`) |
| Antworten kommen aus | den Formularen, eine Seite pro Schritt | `kickstart.ini` im Installationsverzeichnis, ein Abschnitt pro Schritt | Kommandozeilenoptionen, jede mit einem Standardwert |
| Braucht während der Installation einen Webserver | ja | nein | nein |
| Braucht vorab eine geschriebene Datei | nein (`kickstart.ini` optional) | ja: `kickstart.ini` (`exp:kickstarter ini` schreibt sie) | nein |
| Interaktiv | ja | nur `exp:kickstarter ini` stellt Fragen; `run` nicht | nein |
| Probelauf | nein | `run --dry-run` | `--dry-run`, und `--print`, um die Konfiguration anzuzeigen |
| Teilläufe | „Zurück“ und „Weiter“ im Browser | `run --start-step=<Step>` und `--stop-step=<Step>`, zur Diagnose; eine Installation läuft ab `Welcome`, und ein späterer Start, der die Ergebnisse früherer Schritte bräuchte, wird verweigert | nein; erneut mit denselben Optionen ausführen |
| Schutz vor Überschreiben | startet nur, solange `CheckValidity=true` gilt | `--force` erforderlich, sobald die Schritte `CreateSites` einschließen | verweigert die Ausführung, wenn `settings/override/site.ini.append.php` einen Abschnitt `[DatabaseSettings]` enthält, außer mit `--force` |
| Wartungsseite während des Laufs | ja, für jeden Besucher außer dem Browser des Assistenten | ja | ja (sie führt den Kickstarter aus) |
| Laufprotokoll | `var/log/setup.log` | `var/log/setup.log` und `var/log/kickstart.log` | `var/log/setup.log` und `var/log/exp-install-<date>.ini` (sie gibt nur auf dem Terminal aus; es gibt kein `kickstart.log`) |
| Administratorpasswort | im Formular eingegeben | aus `kickstart.ini`; erzeugt, wenn leer oder allgemein bekannt | erzeugt, sofern nicht `--password` angegeben und annehmbar ist |
| Am besten geeignet für | eine erste Installation, das Ausprobieren von Exponential, Hosts ohne Shell-Zugang | wiederholbare Installationen aus einer geprüften Datei, Staging-Kopien, Offline-Paketsätze | die schnellste Installation; Skripte, CI, Container, Demos |
| Kapitel | [5](05-setup-assistent.md) | [6](06-kickstarter.md) | [7](07-installation-ueber-die-konsole.md) |

### Der Setup-Assistent

Der Assistent startet von selbst. `settings/site.ini` wird ausgeliefert mit:

```ini
[SiteAccessSettings]
CheckValidity=true
```

Solange dieser Wert `true` ist, leitet der Kernel (`ezpKernelWeb`) jede Anfrage an das Modul `setup`, View `init`,
mit dem Pagelayout `[SetupSettings] PageLayout=setup_pagelayout.tpl` und ohne Session oder Datenbank. `CreateSites`
schreibt `CheckValidity=false` nach `settings/override/site.ini.append.php`, und ab der nächsten Anfrage wird die
Website ausgeliefert. Der Assistent braucht zuerst einen funktionierenden Webserver ([Kapitel 8](08-die-website-ausliefern.md)),
und er ist die einzige Methode, die jede Entscheidung auf einer Seite mit einer Erklärung zeigt. Er wird in
[Kapitel 5](05-setup-assistent.md) Seite für Seite beschrieben.

### Der Kickstarter

Der Kickstarter (`bin/php/kickstarter.php`, Konsolenname `exp:kickstarter`) führt die Schrittklassen in einem
einzigen PHP-Prozess auf der Kommandozeile aus und liest jede Antwort aus `kickstart.ini`. Er hat zwei
Unterbefehle: `ini` schreibt die Datei (interaktiv oder mit Standardwerten über `--yes` oder `--defaults`), und `run`
installiert daraus. Er ist die Methode der Wahl, wenn dieselbe Installation mehr als einmal angelegt werden muss,
wenn die Antworten als Datei geprüft werden sollen, bevor sie angewendet werden, oder wenn das Site-Paket aus einem
lokalen Paket-Repository stammt. Er wird in [Kapitel 6](06-kickstarter.md) beschrieben.

### Die Konsoleninstallation

`exp:install` (`bin/php/install.php`) ist ein Frontend für den Kickstarter. Es wandelt seine Optionen in eine
temporäre `kickstart.ini` um, führt `expKickstarter` im selben Prozess aus, entfernt die temporäre Datei und stellt
eine zuvor vorhandene `kickstart.ini` wieder her. Jede Option hat einen Standardwert, sodass

```bash
php bin/php/console exp:install
```

allein das Site-Paket `sevenx_multisite` auf SQLite (`var/storage/sqlite3/exponential.db`) installiert, mit dem
Administrator `admin` und einem erzeugten Passwort, das am Ende einmal angezeigt wird. Sie wird in
[Kapitel 7](07-installation-ueber-die-konsole.md) beschrieben.

## 4.3 Site-Pakete: was installiert wird

Die Schritte installieren ein **Site-Paket**: ein Exponential-Paket vom Typ `site` (`package.xml` mit
`<type value="site" />`) sowie die Pakete, die es verlangt. Pakete liegen im Paket-Repository unterhalb von
`var/storage/packages/`; das Anbieterverzeichnis wird durch `settings/package.ini [RepositorySettings] Vendor` (`7x`)
festgelegt, sodass ein importiertes Paket in `var/storage/packages/7x/<name>/` landet.

Der Paketindex dieser Version, `[RepositorySettings] RemotePackagesIndexURL`
(`https://exponential.packages.exponential.earth/exponential/6.0/6.0.15`, dessen `index.xml` die Pakete auflistet),
bietet zwölf Site-Pakete an. Diejenigen, die Sie am wahrscheinlichsten wählen:

| Site-Paket | Zusammenfassung im Index | Hinweise |
|---|---|---|
| `sevenx_multisite` | 7x MultiSite Default Installation | der Standard von `exp:install --package` und von `exp:kickstarter ini --yes`; verlangt `sevenx_classes` (die Inhaltsklassen) und `sevenx_multisite_democontent` (die Demo-Inhalte), jeweils `min-version 5.1` |
| `sevenx_multisite_clean` | 7x MultiSite Default Installation (without demo content) | dieselbe Website, leer |
| `cjw_multisite` | CJW Multisite | die Starter-Website des Designs CJW Themes Jumper: eine englische und eine deutsche Website mit den Starter-Inhalten und ihren Layouts |
| `sevenx_site`, `sevenx_site_clean` | 7x Simple Website Interface | eine einfachere Website, mit oder ohne Demo-Inhalte |
| `ezwebin_site`, `ezflow_site`, `ezdemo_site` (jeweils auch als `..._clean`), `plain_site` | Website Interface, eZ Flow, Exponential Demo Site, Plain site | die älteren Site-Pakete, beibehalten für Websites, die darauf aufbauen |

Ein `_clean`-Paket installiert dieselbe Struktur ohne die Demo-Artikel und -Bilder: Wählen Sie es für eine echte
Website, die Sie selbst füllen werden, und das vollständige Paket, um Exponential auszuprobieren.

Woher die Pakete kommen:

- **Der entfernte Paketindex.** `SiteTypes` lädt den Index von `[RepositorySettings] RemotePackagesIndexURL` in
  `settings/package.ini` herunter (wenn dieser Wert leer ist: `RemotePackagesIndexURLBase` plus `x.y/x.y.z`, etwa
  `6.0/6.0.15`; vor 6.0.15 wurde das Wort `stable` angehängt, das es auf dem Paketserver nicht gibt) und bietet die
  dort aufgeführten Site-Pakete neben den bereits importierten an. Ein Paket, das entfernt aufgeführt, aber noch nicht
  importiert ist, wird heruntergeladen und importiert, gefolgt von den Paketen, die es verlangt.
- **Das lokale Repository.** Ein Paket, das bereits unter `var/storage/packages/` importiert ist, wird unverändert
  verwendet. Lässt sich der Index nicht herunterladen, sucht `SiteTypes` stattdessen im System-Repository nach einer
  `index.xml`; so funktioniert eine Offline-Installation.
- **Ein Upload.** Die Seite „Site Paket“ des Assistenten hat ein Feld **Paket hochladen** für eine `.ezpkg`-Datei.

### Installationsskripte der Pakete: der „Multi-Site-Installer“

Ein Site-Paket kann PHP-Installationsskripte als `<settings-file>`-Einträge in seiner `package.xml` mitbringen.
`CreateSites` ruft die darin definierten Funktionen, sofern vorhanden, an festen Punkten auf:

| Funktion | Aufgerufen von `CreateSites` | Zweck |
|---|---|---|
| `eZSitePreInstall( $siteType )` | bevor das Paket installiert wird | Vorbereitung, zum Beispiel Klassen anlegen |
| `eZSiteINISettings( $parameters )` | während die Einstellungen erzeugt werden | Einstellungen des öffentlichen Siteaccess |
| `eZSiteAdminINISettings( $parameters )` | während die Einstellungen erzeugt werden | Einstellungen des Administrations-Siteaccess |
| `eZSiteCommonINISettings( $parameters )` | während die Einstellungen erzeugt werden | Einstellungen, die nach `settings/override/` geschrieben werden |
| `eZSiteRoles( $parameters )` | nachdem die Inhalte eingespielt sind | Rollen und Richtlinien |
| `eZSitePreferences( $parameters )` | nach den Rollen | Benutzereinstellungen |
| `eZSitePostInstall( $parameters )` | am Ende | alles Übrige |
| `eZSiteFinalText( $parameters )` | für die letzte Seite | Text, der auf der Seite „Beendet“ des Assistenten angezeigt wird |

`sevenx_multisite` führt `ini-site.php`, `ini-admin.php`, `ini-common.php`, `sevenx-multi-site-installer.php`,
`install-scripts.php`, `roles.php` und `preferences.php` auf. Seine `install-scripts.php` definiert
`eZSitePreInstall()` und `eZSitePostInstall()`, die jeweils einen `sevenxMultiSiteInstaller` (eine Unterklasse von
`eZSiteInstaller` des Kernels in `kernel/classes/ezsiteinstaller.php`) erzeugen und `preInstall()` bzw.
`postInstall()` aufrufen. Dieser „Multi-Site-Installer“ ist daher keine vierte Installationsmethode: Er ist der Teil
des Site-Pakets an jeder Installation, gleich welches Frontend sie gestartet hat. Bricht seine Nachinstallation
mittendrin ab, meldet `CreateSites` den Fehler `EZSW-080` („The post-install of site package '...' stopped at step N
of M (...): the steps after it did not run“).

### Weitere Installer-Skripte, denen Sie begegnen können

| Skript | Was es tut | Zum Installieren einer neuen Website verwenden? |
|---|---|---|
| `exp:ezwebininstall` (`bin/php/ezwebininstall.php`) | installiert das Site-Paket `ezwebin` in eine konfigurierte Installation; Optionen `--repository`, `--package`, `--package-dir`, `--url`, `--admin-siteaccess`, `--user-siteaccess`, `--auto-mode` | nein: Es setzt eine bereits vorhandene Installation voraus |
| `extension/expsite_installer/bin/php/install.php` (Extension `expsite_installer`, sofern vorhanden) | aktiviert die Extensions `explayouts*`/`expsite*` in `settings/override/site.ini.append.php` und führt `expSiteInstaller::runFullInstall()` mit den Daten in `extension/expsite_data_media/data` aus | nein: Es legt Layout-Daten über eine bestehende Installation, und dieser Datensatz ist veraltet; installieren Sie stattdessen neu aus den Site-Paketen |

## 4.4 Was eine Installation schreibt

Alle drei Methoden enden in `CreateSites` und schreiben daher dasselbe. Wenn Sie es kennen, wird klar, was eine
Neuinstallation ersetzt und was gesichert werden muss.

### Einstellungen

| Datei | Geschrieben von | Inhalt |
|---|---|---|
| `settings/override/site.ini.append.php` | `CreateSites` | `[SiteAccessSettings] CheckValidity=false`, `MatchOrder` (`uri`, `host` oder `port`), `HostMatchMapItems[]`, `AvailableSiteAccessList[]`; `[PortAccessSettings]` für die Zuordnung über den Port; `[SiteSettings] SiteList[]` und `DefaultAccess`; `[MailSettings] AdminEmail`, `Transport` (`sendmail` oder `SMTP`) mit `TransportServer`, `TransportUser`, `TransportPassword` für SMTP; `[DesignSettings] DesignLocationCache=enabled`; sowie die globalen `site.ini`-Einstellungen, die das Site-Paket zurückgibt (zum Beispiel `[ExtensionSettings] ActiveExtensions[]`) |
| `settings/override/i18n.ini.append.php` | `LanguageOptions` (Browser) und `CreateSites` | `[CharacterSettings] Charset` (bei einer neuen Website immer `utf-8`) |
| `settings/override/image.ini.append.php` | `CreateSites` | `[ImageMagick] IsEnabled` sowie `ExecutablePath`/`Executable`, wenn die Systemprüfung ImageMagick gefunden hat |
| `settings/override/mailpreferences.ini.append.php` | `CreateSites` | das Website-Geheimnis der E-Mail-Einstellungen sowie den Namen und die Postanschrift der Organisation aus „Seitendetails“ |
| `settings/override/<file>.ini.append.php` | `CreateSites` | jede weitere INI-Datei, die `eZSiteCommonINISettings()` des Pakets zurückgibt |
| `settings/siteaccess/site/` | `CreateSites` | der öffentliche Siteaccess: `site.ini.append.php` mit `[DatabaseSettings]` (`DatabaseImplementation`, `Server`, `Port`, `Database`, `User`, `Password`, `Socket`, `Charset`), `[SiteSettings] SiteName` und `SiteURL`, `[RegionalSettings]`, `[FileSettings] VarDir`, `[DesignSettings] SiteDesign`, dazu die Siteaccess-Einstellungen des Pakets |
| `settings/siteaccess/admin/` | `CreateSites` | der Administrations-Siteaccess, mit denselben `[DatabaseSettings]` |
| `settings/siteaccess/editor/` | `CreateSites` (`createEditorSiteAccess()`) | eine Kopie der `*.ini.append.php`-Dateien des Administrations-Siteaccess mit `SiteDesign=editor`, `AdditionalSiteDesignList[]` `admin4l`, `admin4`, `admin3`, `admin2`, `admin`, `ExtensionSettingsSiteAccess=admin`, `SiteName=Editor`, einer eigenen `SiteURL` und `[SiteAccessRules]`, die die Module `setup`, `visual`, `explayouts_ui`, `explayouts_ui_api`, `git_manager`, `xrowextract` und `bccie` abschalten; dazu Appends für `menu.ini`, `toolbar.ini` und `admininterface.ini` |
| `settings/siteaccess/adminui/` | `CreateSites` (`createAdminUISiteAccess()`) oder die Nachinstallation des Multisite-Pakets mit derselben Funktion; nur wenn `extension/exp_adminui` vorhanden ist | eine Kopie der `*.ini.append.php`-Dateien des Administrations-Siteaccess mit `SiteDesign=adminui`, `AdditionalSiteDesignList[]` `admin4l`, `admin4`, `admin3`, `admin2`, `admin`, `ActiveAccessExtensions[]=exp_adminui`, `SiteName=Admin UI`, als `SiteURL` die Adresse der Administration mit dem Pfad `/adminui`, sowie eine `icon.ini` und eine `ezoe.ini` ohne Einstellungen. Aufgeführt in `AvailableSiteAccessList[]` und `SiteList[]`, zugeordnet nur über die URI (keine Host-Zuordnungszeile, kein Port; `uri` wird zu `MatchOrder` hinzugefügt). Siehe [Exponential Admin UI](../../en/us/features/6.0/exp-adminui.md) |

Die **Verzeichnisnamen der Siteaccesses lauten immer `site`, `admin` und `editor`**, dazu `adminui`, wenn die
Extension `exp_adminui` in der Installation vorhanden ist. Was Sie im Assistenten, in `kickstart.ini` oder mit
`exp:install --site-access=` wählen, ist der **Zuordnungswert**: der URL-Pfad, der Port oder der Hostname, der zu
jedem Siteaccess führt. `Access=www` im URL-Modus bedeutet daher „der Pfad `/www` liefert den Siteaccess `site` aus“,
nicht einen Siteaccess namens `www`.

Das Datenbankpasswort landet in `settings/siteaccess/site/site.ini.append.php` und
`settings/siteaccess/admin/site.ini.append.php` (sowie in den Kopien für editor und adminui), ein SMTP-Passwort in
`settings/override/site.ini.append.php`. Halten Sie `settings/override/` und `settings/siteaccess/` aus der
Versionsverwaltung heraus und nur für den Benutzer des Webservers lesbar; [Kapitel 13](13-sicherheitshaertung.md)
behandelt das.

### Datenbank

Was mit der Datenbank geschieht, entscheidet die **Datenbankaktion**: der Schlüssel `DatabaseAction` in
`kickstart.ini`, `--db-action` bei `exp:install` oder die Auswahlliste **Aktion**, die der Assistent anzeigt, wenn die
Datenbank bereits Tabellen enthält.

| Aktion | Beschriftung im Assistenten | Wirkung in `CreateSites` |
|---|---|---|
| `remove` | Bestehende Daten entfernen | `eZDBTool::cleanup()` löscht die Tabellen des Kernels; bei SQLite wird die ganze Datei geleert. Danach werden das Schema (`share/db_schema.dba`), die Basisdaten (`share/db_data.dba`) und die Pakete installiert. **Alles, was in der Datenbank war, geht verloren.** |
| `ignore` | Die Daten belassen und neue hinzufügen | Schema, Daten und Pakete werden hinzugefügt, ohne vorher aufzuräumen |
| `skip` | Die Daten belassen und nichts weiteres tun | Es werden weder Schema noch Daten eingefügt; die Einstellungen werden trotzdem geschrieben. `SiteAdmin` wird ebenfalls übersprungen, sodass der bestehende Administrator erhalten bleibt. |

Das Laden des Schemas legt jede Tabelle an, die der Kernel deklariert, importiert dann die eigenen `.dba`-Daten jedes
Datentyps (auf anderen Engines als SQLite) und, sofern die Datenbank es unterstützt, den Volltextteil des
Audit-Index. Danach werden das Site-Paket und die von ihm verlangten Pakete in die Datenbank installiert. Die
Datenbank-Engines selbst sind Gegenstand von [Kapitel 9](09-datenbanken.md).

### Dateien und Protokolle

| Pfad | Geschrieben von | Hinweise |
|---|---|---|
| `var/storage/sqlite3/<file>` | `DatabaseInit` / `CreateSites` | die SQLite-Datenbank, mit den Dateien `-wal` und `-shm` daneben |
| `var/storage/packages/` | `SiteTypes` | heruntergeladene und importierte Pakete |
| `design/<package>/override/templates/` | `CreateSites` | leere Override-Verzeichnisse für das nach dem Paket benannte Website-Design |
| `var/<package>/` oder `var/site/` | die Website | `CreateSites` schreibt `[FileSettings] VarDir=var/<package identifier>` in die Siteaccess-Einstellungen, und ein Site-Paket kann ein eigenes festlegen: `sevenx_multisite` setzt `var/site` für den öffentlichen Siteaccess. Hochgeladene Dateien und die Inhalts-Caches liegen darunter |
| `var/maintenance.json` | alle Methoden | existiert nur, solange die Installation läuft (siehe unten) |
| `var/log/setup.log` | alle Methoden | ein lesbarer Eintrag pro Lauf; frühere Läufe rotieren nach `setup.log.1`, `.2` ... |
| `var/log/setup-run.state` | der Assistent | erlaubt jeder Anfrage des Assistenten, denselben Lauf fortzusetzen |
| `var/log/kickstart.log` | `exp:kickstarter run` | alles, was der Lauf ausgegeben hat, Passwörter maskiert; neun frühere Läufe bleiben als `kickstart.log.1` bis `.9` erhalten |
| `var/log/exp-install-<date>.ini` | `exp:install` | die verwendete Konfiguration, Passwörter maskiert |
| `var/log/initial-admin-password` | Kickstarter, `exp:install` | nur wenn ein Passwort erzeugt wurde; Modus `0600`. Lesen Sie die Datei, melden Sie sich an, ändern Sie das Passwort und löschen Sie die Datei |

### Wartungsmodus während der Installation

Jede Methode schaltet während ihrer Arbeit den **Wartungsmodus** ein, damit kein Besucher auf eine halb aufgebaute
Datenbank trifft: `var/maintenance.json` existiert, und die Front-Controller (`index.php`, `index_rest.php`,
`index_treemenu.php`) beantworten jede Anfrage mit `share/maintenance.html` (oder `errors/maintenance.html` des
aktiven Themes), HTTP 503 und einem `Retry-After`-Header, bevor die Einstellungen oder die Datenbank berührt werden.

- Der **Kickstarter** (und damit `exp:install`) schaltet ihn zu Beginn des Laufs ein und aus, wenn die Installation
  fertig ist. Nach einem fehlgeschlagenen Schritt bleibt er eingeschaltet: Der Lauf meldet das, und
  `php bin/php/maintenance.php off` beendet ihn.
- Der **Assistent** schaltet ihn bei seiner ersten Anfrage ein, für jeden Browser außer seinem eigenen (das Cookie
  `exp_setup_wizard`), und auf seiner Seite „Beendet“ wieder aus; ein verlassener Assistent gibt die Website nach
  30 Minuten frei ([Kapitel 5](05-setup-assistent.md#52-die-browser-sperre-und-der-wartungsmodus)).
- Ein Probelauf schaltet ihn nicht ein.

## 4.5 Welche Methode für welche Situation

| Situation | Empfohlen | Warum |
|---|---|---|
| Ein erster Blick auf Exponential auf einem Laptop | `exp:install` | ein Befehl, SQLite, nichts zu beantworten |
| Erste Produktivinstallation, von Hand durchgeführt | der Assistent oder `exp:install` mit ausdrücklichen Optionen | der Assistent erklärt jede Entscheidung; `exp:install` hinterlässt einen maskierten Eintrag dessen, was verwendet wurde |
| Shared Hosting ohne Shell-Zugang | der Assistent | er braucht nur einen Browser und FTP-Zugang |
| Dieselbe Website wird immer wieder neu aufgebaut (Staging, Test, Schulung) | der Kickstarter | die geprüfte `kickstart.ini` ist die Spezifikation der Installation |
| CI-Pipeline, Container-Image, automatisierte Testumgebung | `exp:install` | Optionen oder Umgebungsvariablen, Exit-Status, keine Datei als Vorlage nötig |
| Mehrere Umgebungen, die sich nur in Hostnamen und Zugangsdaten unterscheiden | der Kickstarter mit einer `kickstart.ini` pro Umgebung oder `exp:install` mit Optionen pro Umgebung | beide sind deterministisch |
| Kein Internetzugang auf dem Server | der Kickstarter oder der Assistent mit den Paketen bereits in `var/storage/packages/` | das Site-Paket wird aus dem lokalen Repository genommen, wenn der Index nicht erreichbar ist |
| Die Einstellungen für eine bestehende Datenbank neu erzeugen | der Kickstarter mit `DatabaseAction=skip` oder `exp:install --db-action=skip --force` | `skip` schreibt die Einstellungen und lässt die Daten unberührt |
| Neuinstallation über eine bestehende Installation | `exp:kickstarter run --force` oder `exp:install --force`, nach einer Sicherung | beide sagen klar, dass sie die Datenbank ersetzen |

### Entscheidungshilfe

1. **Haben Sie Shell-Zugang?** Nein: Verwenden Sie den Assistenten. Ja: Weiter.
2. **Muss eine Person jede Antwort prüfen, bevor irgendetwas geschieht?** Ja: Schreiben Sie `kickstart.ini` mit
   `exp:kickstarter ini`, prüfen Sie die Datei, dann `exp:kickstarter run --dry-run` und `run --force`.
3. **Ist die Installation Teil eines Skripts, einer Pipeline oder eines Container-Starts?** Ja: Verwenden Sie
   `exp:install` mit ausdrücklichen Optionen, das Passwort in `EXP_INSTALL_DB_PASSWORD`, und prüfen Sie den
   Exit-Status.
4. **Andernfalls** ist `exp:install` der kürzeste Weg und der Assistent der am besten erklärte.

## 4.6 Unbeaufsichtigte Installationen: CI und Container

Die Kommandozeilenmethoden wurden für den unbeaufsichtigten Einsatz gebaut. Diese Regeln halten solche
Installationen sicher und wiederholbar.

**Bevorzugen Sie in der Automatisierung `exp:install`.** Es braucht keine Datei, prüft seine Optionen, bevor
irgendetwas geschrieben wird, und gibt bei jedem Fehler einen Exit-Status ungleich null zurück
([Kapitel 7](07-installation-ueber-die-konsole.md#77-exit-status)). Der Kickstarter lässt sich genauso gut in Skripten verwenden,
wenn die Konfiguration besser als geprüfte Datei aufbewahrt wird.

**Halten Sie Geheimnisse aus der Kommandozeile heraus.** `exp:install` liest das Datenbankpasswort aus der
Umgebungsvariablen `EXP_INSTALL_DB_PASSWORD`, wenn `--db-password` nicht angegeben ist; so erscheint es weder in der
Prozessliste noch in der Shell-History. `kickstart.ini` enthält Passwörter im Klartext: Legen Sie die Datei mit dem
Modus `0600` an, committen Sie sie nie, und entfernen Sie sie nach der Installation (der Assistent würde sie bei
einem späteren Lauf lesen).

**Wählen Sie das Administratorpasswort bewusst.** Ohne `--password` erzeugt `exp:install` ein Passwort mit 24 Zeichen
und schreibt es nach `var/log/initial-admin-password` (Modus `0600`). Übergeben Sie in einer Pipeline entweder ein
Passwort aus Ihrem Secret Store mit `--password=` (mindestens `[UserSettings] MinPasswordLength` Zeichen, also 10, und
kein allgemein bekanntes), oder lesen Sie das erzeugte Passwort aus dieser Datei und ändern Sie es anschließend.

**Führen Sie die Installation als Benutzer des Webservers aus.** Der Installer legt Dateien in `settings/`, `var/`
und `design/` an. Führen Sie ihn als der Benutzer aus, unter dem der Webserver oder Velocity läuft, oder korrigieren
Sie die Eigentümerschaft danach; die Systemprüfung testet die Verzeichnisrechte als der Benutzer, unter dem sie
läuft. Weder der Kickstarter noch `exp:install` verweigern die Ausführung als `root` (beide akzeptieren
`--allow-root-user` und ignorieren es), aber von `root` angelegte Dateien können später für den Webserver nicht
beschreibbar sein. Die meisten anderen Befehle, darunter `exp:velocity`, verweigern `root`, sofern nicht
`--allow-root-user` angegeben ist.

**Machen Sie die Datenbankaktion ausdrücklich.** Sowohl `exp:install` (als Standard) als auch die
Kickstarter-Dateien, die die meisten Leute schreiben, verwenden `remove`. Genau das will ein frischer Container, und
genau das darf niemals auf eine Datenbank zeigen, die Daten enthält, die Sie brauchen.

**Rechnen Sie nur mit `remove` mit einem idempotenten Lauf.** Wird dieselbe Konfiguration erneut mit
`DatabaseAction=remove` ausgeführt, entsteht wieder dieselbe Installation. Mit `ignore` fügt ein zweiter Lauf eine
zweite Kopie der Daten hinzu.

**Ein minimaler Container-Einstiegspunkt** (installiert einmal, startet dann den Server und bleibt im Vordergrund,
solange dieser läuft):

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /srv/exponential
if ! grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php 2>/dev/null; then
    php bin/php/console exp:install \
        --db=mysql --db-host="${DB_HOST}" --db-name="${DB_NAME}" --db-user="${DB_USER}" \
        --url="${SITE_URL}" --email="${ADMIN_EMAIL}" --password="${ADMIN_PASSWORD}"
fi
php bin/php/console exp:velocity start
# exp:velocity start runs the server in the background and returns; status exits 0 while it runs
while php bin/php/console exp:velocity status >/dev/null 2>&1; do sleep 30; done
echo "Velocity stopped" >&2
exit 1
```

Der Test `grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php` ist derselbe Test, mit dem
`exp:install` selbst entscheidet, ob ein Verzeichnis bereits eine Installation enthält. Übergeben Sie
`EXP_INSTALL_DB_PASSWORD` in der Umgebung des Containers. Die Schleife am Ende ist wichtig: `exp:velocity start`
löst den Server ab und kehrt zurück, sobald er antwortet; ein Einstiegspunkt, der damit endete, würde daher den
Hauptprozess des Containers beenden und mit ihm den Container. Die Schleife hält den Einstiegspunkt am Leben, solange
`exp:velocity status` den Server als laufend meldet (Exit-Status 0), und beendet den Container, wenn der Server
stoppt, sodass die Container-Laufzeitumgebung ihn neu starten kann. Läuft der Einstiegspunkt als `root`, fügen Sie
den beiden `exp:velocity`-Befehlen `--allow-root-user` hinzu. Das Starten und Konfigurieren von Velocity ist
Gegenstand von [Kapitel 8](08-die-website-ausliefern.md).

**Ein CI-Smoke-Test**, der auf SQLite installiert und den Job bei jedem Fehler scheitern lässt:

```bash
php bin/php/console exp:install --dry-run          # configuration and packages, nothing written
php bin/php/console exp:install --force --title="CI build ${CI_PIPELINE_ID:-local}"
test -f settings/siteaccess/site/site.ini.append.php
tail -n 20 var/log/setup.log
```

## 4.7 Bevor Sie beginnen, gleich mit welcher Methode

- Der Code ist vorhanden und die Abhängigkeiten sind installiert ([Kapitel 3](03-den-code-beschaffen.md)), und PHP 8.0
  oder neuer mit den Erweiterungen aus [Kapitel 2](02-voraussetzungen.md) steht zur Verfügung.
- Bei einem Datenbankserver: Die Datenbank und ihr Benutzer existieren, und der Benutzer darf Tabellen anlegen
  ([Kapitel 9](09-datenbanken.md)). SQLite braucht nur ein beschreibbares `var/storage/sqlite3/`.
- `settings/`, `var/` und `design/` sind für den Benutzer beschreibbar, unter dem die Installation läuft.
- Für den Assistenten: Die Website wird ausgeliefert ([Kapitel 8](08-die-website-ausliefern.md)); Exponential Velocity ist
  die empfohlene Engine für jede Phase.
- Sie haben eine Sicherung jeder Datenbank, die die Installation berühren könnte.

---

**Eine öffentliche Demo installieren.** Jede Methode eignet sich für eine öffentliche Demo, deren Administratorpasswort veröffentlicht ist: Führen Sie sie mit der Umgebungsvariablen `EXP_DEMO_MODE=1` aus und geben Sie das veröffentlichte Passwort an; die Installer installieren es dann wie angegeben, auch `publish` oder `publishing`. Für eine Demo, die nach Zeitplan neu aufgebaut wird, ist `exp:install` auf SQLite die übliche Wahl ([Rezept: eine öffentliche Demo auf LiteSpeed und Plesk betreiben](../../en/us/help/hosting/servers/run-a-public-demo-on-litespeed-and-plesk.md), [Demo-Modus](../../en/us/bc/6.0/demo-mode.md)).

## Referenzen

In diesem Repository:

- [Exponential 6.0 installieren](../../en/us/INSTALL.md): die einseitige Installationsanleitung, die dieses Handbuch erweitert
- [Kickstarter CLI](../../en/us/bc/6.0/kickstartercli.md) und [Kickstarter: eine ganze Website aus einer Datei installieren](../../en/us/features/6.0/kickstarter-cli.md)
- [Exponential mit einem Befehl installieren](../../en/us/features/6.0/install-in-one-command.md)
- [Das neue Aussehen des Setup-Assistenten und der Editor-Siteaccess](../../en/us/features/6.0/setup-wizard-and-editor-siteaccess.md)
- [Standardwerte einer sauberen Installation](../../en/us/features/6.0/clean-install-defaults.md)
- [Wartungsmodus](../../en/us/features/6.0/maintenance-mode.md)
- [Installer-Protokolle und Ausgangsdaten](../../en/us/specifications/6.0/installer-logs-and-seed-data.md)
- [Stapelverarbeitung im Paket-Installer](../../en/us/features/6.0/package-installer-batching.md)
- [INI-Overrides für mehrere Websites](../../en/us/features/6.0/multi-site-ini-overrides.md)
- [Exponential-Konsole](../../en/us/bc/6.0/console.md)
- [Velocity-Engines](../../en/us/bc/6.0/velocity-engines.md)
- [Audit-Protokoll](../../en/us/bc/6.0/audit.md) (das Ereignis `system.install.run` protokolliert jede Installation)
- [Erste Schritte](../anleitungen/erste-schritte.md) und [Bereitstellung](../anleitungen/bereitstellung.md)
- Code: `kernel/setup/steps/ezstep_data.php`, `kernel/setup/steps/ezstep_create_sites.php`,
  `kernel/classes/expkickstarter.php`, `bin/php/install.php`, `kernel/classes/expmaintenance.php`,
  `kernel/classes/ezsiteinstaller.php`

Extern:

- Exponential auf GitHub: <https://github.com/se7enxweb/exponential>
- Exponential Velocity: <https://github.com/se7enxweb/exponential-velocity>
- Composer: <https://getcomposer.org/doc/>
- PHP auf der Kommandozeile: <https://www.php.net/manual/en/features.commandline.php>
- PHP `proc_open()` (vom Laufprotokoll des Kickstarters verwendet): <https://www.php.net/manual/en/function.proc-open.php>
- Paketindex dieser Version: <https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml>

[Inhalt](README.md) · Zurück: [3. Den Code beschaffen](03-den-code-beschaffen.md) · Weiter: [5. Der Setup-Assistent](05-setup-assistent.md)
