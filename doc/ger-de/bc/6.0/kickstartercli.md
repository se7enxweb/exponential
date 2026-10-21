# Kickstarter-CLI

Lesen Sie diese Seite, wenn Sie Exponential über die Kommandozeile installieren, Installationen per Skript ausführen
oder Testwebsites neu installieren. `bin/php/kickstarter.php` (Konsole: `php bin/php/console exp:kickstarter`, neu in
Exponential 6.0.15) führt den Setup-Assistenten ohne Browser aus: Er liest jede Antwort des Assistenten aus
`kickstart.ini`.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neuer Befehl mit zwei Unterbefehlen: `ini` schreibt `kickstart.ini` aus `kickstart.ini-dist`, `run` führt die Schritte des Assistenten aus. `run` verweigert den zerstörenden Schritt `CreateSites` ohne `--force`; `run --dry-run` testet die Datenbankverbindung und den Download der Pakete, ohne etwas zu schreiben. Im Kickstart-Modus wird ein Site-Paket, das bereits in `var/storage/packages/` liegt, so verwendet, wie es ist; nur ein fehlendes wird aus dem entfernten Repository heruntergeladen. `eZPackage::import()` erhält ein optionales fünftes Argument `$skipExisting`. |
| Wer betroffen ist | Alle, die Websites installieren. Bestehende Aufrufer von `eZPackage::import()` sind unverändert (das neue Argument ist standardmäßig `false`). Eine im Projektwurzelverzeichnis gelassene `kickstart.ini` wird auch vom Setup-Assistenten im Web gelesen. |
| Wie Sie es prüfen | `php bin/php/kickstarter.php run --list-steps` (ändert nichts) |
| Wie Sie es beheben | Nichts zu beheben. Entfernen oder verschieben Sie `kickstart.ini` nach einer Installation über die CLI, wenn der Web-Assistent sie nicht verwenden soll. Setzen Sie vor einer Installation auf eine andere Datenbank-Engine `DatabaseImplementation` in `settings/override/site.ini.append.php` (siehe [`database_choice`](#database_choice)). |

## Voraussetzungen

Führen Sie alle Befehle im Projektwurzelverzeichnis aus (dem Verzeichnis, das `index.php`, `autoload.php`, `bin/`,
`kickstart.ini` usw. enthält).

```bash
# Confirm you are in the project root
ls index.php autoload.php kickstart.ini-dist
```

Sie brauchen:

- **PHP 8.1 oder neuer** in Ihrem `$PATH`.
- Einen bereits laufenden und erreichbaren Datenbankserver (MySQL, PostgreSQL, SQLite 3 oder MongoDB).
- Das Verzeichnis `var/cache/ini/` muss beschreibbar sein, weil `kickstart.ini` dort gecacht wird.


## Schnellstart

```bash
# Generate an interactive kickstart.ini
php bin/php/console exp:kickstarter ini

# Test the remote packages and configuration (safe, no --force)
php bin/php/console exp:kickstarter run --dry-run

# Install the site (requires --force)
php bin/php/console exp:kickstarter run --force
```

Für eine unbeaufsichtigte Installation mit sinnvollen Standardwerten:

```bash
php bin/php/console exp:kickstarter ini --yes
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --force
```


## Befehlsreferenz

### `exp:kickstarter ini` — `kickstart.ini` erzeugen

Baut die Datei `kickstart.ini`, die `exp:kickstarter run` verarbeitet. Er liest `kickstart.ini-dist` (lauter
auskommentierte Beispiele) und schreibt eine ausgefüllte `kickstart.ini` in das Projektwurzelverzeichnis.

```bash
php bin/php/console exp:kickstarter ini
php bin/php/kickstarter.php ini
```

#### Optionen

| Option | Kurz | Beschreibung |
|--------|-------|-------------|
| `--defaults` | `-d` | Die Werte aus `kickstart.ini-dist` wörtlich in `kickstart.ini` kopieren und beenden. |
| `--yes` | `-y` | Die eingebauten sinnvollen Standardwerte übernehmen und `kickstart.ini` ohne Rückfragen schreiben. |
| `--from-installed` | | Server, Name und Benutzer der Datenbank (nie das Passwort) eines installierten Siteaccess als Standardwerte nehmen. Standardmäßig aus. |
| `--help` | `-h` | Hilfe anzeigen. |

Die Standardwerte von `--yes` werden aus `kernel/classes/expkickstarterini.php` geladen. Sie übernehmen nichts aus den
Einstellungen einer installierten Website: Diese nennen eine laufende Datenbank, und eine daraus geschriebene
`kickstart.ini` würde darüber installieren. Mit `--from-installed` werden Server, Name und Benutzer der Datenbank des
ersten installierten Siteaccess, der eine Datenbank nennt, zu den Standardwerten; sein Passwort wird nie kopiert. Das
Standard-`Site_package` ist `sevenx_multisite`, das Paket, das `exp:install` installiert.


### `exp:kickstarter run` — den Setup-Assistenten ausführen

Führt die Schritte des Setup-Assistenten von `welcome` bis `final` mit den Werten aus `kickstart.ini` aus. Ein
konfiguriertes Site-Paket, das bereits in `var/storage/packages/` liegt, wird so verwendet, wie es ist; nur ein
fehlendes Paket (oder eine fehlende Abhängigkeit) wird aus dem entfernten Repository heruntergeladen. Löschen Sie zuerst
die lokale Kopie, wenn Sie einen frischen Download möchten.

```bash
php bin/php/console exp:kickstarter run
php bin/php/kickstarter.php run
```

#### Optionen

| Option | Beschreibung |
|--------|-------------|
| `--start-step=<step>` | Erster auszuführender Schritt (Standard: `welcome`). Ein späterer Start wird abgelehnt, wenn ein Schritt im Lauf Ergebnisse von Schritten davor braucht (siehe [Nach einem Fehlschlag fortsetzen](#nach-einem-fehlschlag-fortsetzen)). |
| `--stop-step=<step>` | Letzter auszuführender Schritt (Standard: `final`). |
| `--dry-run` | `kickstart.ini` validieren, `DatabaseChoice` bis `SiteDetails` ausführen (Datenbankverbindung, Download der Pakete), dann anhalten. Schreibt kein Passwort, sendet keine E-Mail, installiert nichts. |
| `--list-steps` | Die Setup-Schritte auflisten und beenden. |
| `--force` | Erforderlich, wenn der Bereich der Schritte `CreateSites` einschließt, weil `CreateSites` die Datenbank und die Einstellungen der Website ändert. |
| `--help` | Hilfe anzeigen. |

Die üblichen Skriptoptionen (`--allow-root-user`, `--no-colors`, `--quiet`, `--debug`, `--verbose`, `--siteaccess`,
`--logfiles`) werden angenommen und ignoriert; der Kickstarter läuft immer auf dem einfachen Siteaccess.

#### `CreateSites` und `--force`

`CreateSites` ist der Schritt, der das Datenbankschema löscht / einfügt, das Site-Paket und die benötigten Pakete
installiert und die Dateien in `settings/siteaccess/` schreibt. Weil er zerstörend ist, bricht `exp:kickstarter run`
ab, wenn dieser Schritt eingeschlossen ist und `--force` nicht angegeben wird:

```
The CreateSites step will modify the database and site settings.
Re-run with --force to confirm you want to install the site package.
```

Um die Schritte für Konfiguration und Pakete auszuführen und anzuhalten, bevor etwas installiert wird, halten Sie beim
Schritt vor `CreateSites` an (kein `--force` nötig):

```bash
php bin/php/console exp:kickstarter run --stop-step=Registration
```

`--stop-step=CreateSites` tut das Gegenteil: Es schließt `CreateSites` ein, installiert also (und braucht `--force`) und
lässt nur `Final` aus.

#### Nach einem Fehlschlag fortsetzen

Ein Lauf kann nicht dort weitermachen, wo ein früherer angehalten hat. Die Schritte halten, was sie herausgefunden haben
(Typ und Verbindung der Datenbank, das gewählte Paket und seine Sprachen, die Werte der Siteaccesses, das Ergebnis der
Systemprüfung für ImageMagick, den Administrator), nur im laufenden Prozess; `kickstart.ini` enthält die Antworten, nicht
diese Ergebnisse. Daher wird ein späteres `--start-step` abgelehnt, bevor irgendetwas läuft, wenn ein Schritt im Bereich
einen früheren braucht, und die Meldung nennt den frühesten Schritt, der funktioniert:

```
--start-step=SiteDetails cannot work: the steps SiteDetails..Final need what Welcome, SystemCheck, EmailSettings, DatabaseChoice, DatabaseInit, LanguageOptions, SiteTypes, PackageLanguageOptions, SiteAccess found out earlier in the same run (...). Start at Welcome or earlier: --start-step=Welcome.
```

Jeder Bereich, der `CreateSites` einschließt, muss bei `Welcome` beginnen. Beheben Sie nach einem Fehlschlag die
Ursache in `kickstart.ini` und führen Sie die ganze Installation erneut aus:

```bash
php bin/php/console exp:kickstarter run --force
```

### Umgang mit entfernten Paketen

`exp:kickstarter run` ist dafür gedacht, das Site-Paket aus dem entfernten Paket-Repository zu installieren, das in
`package.ini` konfiguriert ist (z. B. `https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml`).
Die entfernte `index.xml` führt Pakete mit einem Attribut `version` auf. Bis 2026-10-19 passte dieses Attribut **nicht**
zu der Version, die die eigene `package.xml` des Pakets meldet; seitdem führt der Index für 6.0.15 die Site-Pakete von 7x
und `cjw_multisite` mit genau dem Text auf, den `eZPackage::getVersion()` zurückgibt (`6.0-15-stable`).

Zum Beispiel hatte `sevenx_site` im entfernten Index `version="1.0.0-0"`, während das heruntergeladene `.ezpkg`
enthielt:

```xml
<version>6.0.10-stable</version>
<version>
  <number>6.0</number>
  <release>10-stable</release>
</version>
```

Daher gab `eZPackage::getVersion()` `6.0-10-stable` zurück. Ein naives `version_compare('1.0.0-0', '6.0-10-stable')`
würde entscheiden, dass die lokale Kopie neuer ist, und den Download überspringen. Der Kickstart-Ablauf verlässt sich in
keiner Richtung auf die Version im Index.

Hat `kickstart.ini` einen Abschnitt `[site_types]`, sieht `eZStepSiteTypes::createSitePackagesList()`, dass
`hasKickstartData()` wahr ist, und behält den entfernten Eintrag (mit seiner URL), auch wenn es eine lokale Kopie gibt,
sodass der obige Versionsvergleich das Paket nicht verstecken kann. `eZStepSiteTypes::init()` ruft dann
`downloadAndImportPackage()` mit `forceDownload=false` auf: Ein Site-Paket, das bereits in `var/storage/packages/` liegt,
wird so verwendet, wie es ist, und nur ein fehlendes wird heruntergeladen. `downloadDependantPackages()` prüft jede
benötigte Abhängigkeit gegen ihre `min-version` und lädt nur fehlende oder ältere Pakete herunter, wobei es die URLs aus
der entfernten `index.xml` bezieht. Um eine neuere Kopie eines bereits vorhandenen Pakets zu installieren, entfernen Sie
zuerst die lokale Kopie.

### Probelauf

`--dry-run` ist nicht mehr nur ein statischer Validator. Er:

1. Validiert `kickstart.ini` und listet die Schritte des Assistenten auf.
2. Führt die Schritte des Assistenten von `DatabaseChoice` bis `SiteDetails` aus.
3. Importiert während `SiteTypes` das Site-Paket und seine Abhängigkeiten in ein temporäres Repository
   `var/storage/packages/dryrun/`.
4. Räumt das Repository `dryrun/` auf, sobald der Schritt `SiteTypes` fertig ist.
5. Hält nach `SiteDetails` an (Index 10): vor `SiteAdmin`, das ein erzeugtes Passwort nach
   `var/log/initial-admin-password` schreiben kann, vor `Registration`, das eine E-Mail senden kann, und vor
   `CreateSites` (Index 14).

Der Pfad des Probelaufs verwendet dieselben Helfer `downloadAndImportPackage()` und `downloadDependantPackages()` wie
eine echte Installation, aber mit `repositoryID='dryrun'` und `skipExisting=true`. `eZPackage::import()` unterstützt das
Flag `skipExisting`, sodass es in das temporäre Verzeichnis importieren kann, ohne `STATUS_ALREADY_EXISTS`
zurückzugeben, auch wenn `7x/sevenx_site` lokal bereits existiert. So wird der Probelauf zu einem echten Test des
entfernten Downloads und der Auflösung der Abhängigkeiten, ohne den lokalen Paket-Cache oder die Dateien in
`settings/siteaccess/` zu überschreiben.

```bash
php bin/php/console exp:kickstarter run --dry-run
```

Weil der Schritt zum Anhalten `SiteDetails` ist, wird die Sperre `--force` nie ausgelöst und `CreateSites` nie
erreicht, daher bleiben Datenbank und Dateien der Website unberührt, keine Passwortdatei wird geschrieben und keine
E-Mail gesendet.


## Setup-Schritte

Die Schritte des Assistenten sind in `kernel/setup/steps/ezstep_data.php` definiert.

| # | Schritt | Hinweise |
|---|------|-------|
| 0 | `Welcome` | Setzt `eZSetupWizardLanguage` immer auf `eng-GB` und fährt fort. |
| 1 | `SystemCheck` | Prüft PHP-Erweiterungen, Rechte und Dateisystem. |
| 2 | `SystemFinetune` | Wendet die Feinabstimmung des Systems an. |
| 3 | `EmailSettings` | Konfiguriert den E-Mail-Handler. |
| 4 | `DatabaseChoice` | Wählt den Datenbanktyp. |
| 5 | `DatabaseInit` | Testet die Datenbankverbindung und speichert den DSN. |
| 6 | `LanguageOptions` | Setzt die Hauptsprache und zusätzliche Sprachen. |
| 7 | `SiteTypes` | Wählt das Site-Paket und lädt es im aktiven Kickstart-Modus aus dem entfernten Repository herunter. |
| 8 | `PackageLanguageOptions` | Ordnet Paketsprachen, die nicht zur Sprachliste der Website gehören, der Hauptsprache zu. |
| 9 | `SiteAccess` | Wählt die Methode der Zuordnung von Zugängen (`url`, `port`, `hostname`). |
| 10 | `SiteDetails` | Setzt Titel, URL, Namen der Zugänge und `DatabaseAction` der Website. |
| 11 | `SiteAdmin` | Legt den Admin-Benutzer an. |
| 12 | `Security` | Sicherheitsoptionen. |
| 13 | `Registration` | Registrierungsbericht. Sendet nichts, außer mit `Send=true` und gesetztem setup.ini `[RegistrationSettings] Receiver`. |
| 14 | `CreateSites` | Zerstörender Schritt: installiert Schema, Daten, Pakete und Dateien der Siteaccesses. Wird im Fortschritt nicht gezählt. |
| 15 | `Final` | Letzter Schritt. |


## Referenz für `kickstart.ini`

`kickstart.ini` ist eine gewöhnliche INI-Datei im Projektwurzelverzeichnis. Jeder Abschnitt entspricht einem Schritt
des Assistenten. Das Flag `Continue=true` in einem Abschnitt bedeutet, dass der Assistent die Oberfläche für diesen
Schritt überspringen und die Werte aus der Datei verwenden soll.

### `email_settings`

```ini
[email_settings]
Continue=true
Type=mta
Server=
User=
Password=
```

| Feld | Beschreibung |
|-------|-------------|
| `Continue` | `true` überspringt die Seite des Assistenten; `false` füllt sie vor, zeigt sie aber trotzdem. |
| `Type` | `mta` für Sendmail/MTA, `smtp` für einen SMTP-Server. |
| `Server` | Hostname des SMTP-Servers. |
| `User` | SMTP-Benutzername. |
| `Password` | SMTP-Passwort. |

### `database_choice`

```ini
[database_choice]
Continue=true
Type=sqlite3
```

| Feld | Beschreibung |
|-------|-------------|
| `Type` | Datenbanktreiber: `sqlite3` (der Standard, braucht keinen Datenbankserver), `mysqli`, `pgsql`, `mongodb`. |

**Wichtig:** `Type` wählt den Treiber, mit dem sich das *Installationsprogramm* verbindet. Er entscheidet nicht über die
`DatabaseImplementation`, die für die fertige Website in `settings/override/site.ini.append.php` geschrieben wird.
Dieser Wert wird aus der laufenden `site.ini` genommen, und er bleibt bewusst dem Betreiber überlassen.

Der Grund ist, dass ein Treiber mehr als einen Namen hat. `mysql`, `mysqli`, `ezmysql` und `ezmysqli` lösen sich alle
über den Block `ImplementationAlias` in `settings/site.ini` zu `eZMySQLiDB` auf, und eine Website kann dort einen eigenen
Alias für einen eigenen Treiber registrieren. Den Namen von dem Treiber abzuleiten, der sich gerade verbunden hat, würde
diese Wahl bei jeder Installation überschreiben, daher behält das Installationsprogramm sie.

Die Folge sollte man vor einer Installation kennen, die die Engine wechselt. Die laufende `site.ini` enthält noch den
Namen der Engine, die Sie verlassen, daher wird die neue Installation mit der Implementierung der bisherigen Engine
neben dem neuen Host, der neuen Datenbank und den neuen Zugangsdaten geschrieben. `settings/override/` hat Vorrang vor
jedem Siteaccess, daher versucht dann die ganze Website, mit dem neuen Server das Protokoll der alten Engine zu sprechen,
und keine Seite lädt.

Setzen Sie den Wert vor dem Lauf passend zum Ziel, zum Beispiel beim Wechsel zu MySQL:

```bash
sed -i '/^\[DatabaseSettings\]/,/^\[/ s/^DatabaseImplementation=.*/DatabaseImplementation=mysqli/' \
  settings/override/site.ini.append.php
```

Führen Sie dann das Installationsprogramm wie üblich aus. Danach sollten der Wert in
`settings/override/site.ini.append.php` und der in jedem Siteaccess beide die Engine nennen, auf die Sie gerade
installiert haben; stimmen sie nicht überein, ist das zu korrigieren.

### `database_init`

```ini
[database_init]
Continue=true
Server=localhost
Port=
Database=ezp
User=root
Password=
Socket=
```

| Feld | Beschreibung |
|-------|-------------|
| `Server` | Datenbank-Host. |
| `Port` | Datenbank-Port (für den Standard leer lassen). |
| `Database` | Datenbankname. |
| `User` | Datenbankbenutzer. |
| `Password` | Datenbankpasswort. |
| `Socket` | Pfad zu einem Unix-Socket oder leer. |

### `language_options`

```ini
[language_options]
Continue=true
Primary=eng-GB
Languages[]=eng-US
```

| Feld | Beschreibung |
|-------|-------------|
| `Primary` | Locale der Hauptsprache der Website. |
| `Languages[]` | Zusätzliche Sprachen. Die Hauptsprache wird immer automatisch hinzugefügt, wenn sie nicht aufgeführt ist. |

**Wichtig:** Jede Locale, die das gewählte Site-Paket verwendet (zum Beispiel `eng-US` in `sevenx_site`), muss in
`Languages[]` stehen oder der Hauptsprache zugeordnet sein. Sonst werden Inhaltsobjekte mit einer Sprache importiert, die
nicht als priorisierte Sprache der Website gesetzt ist, und das Nachschlagen von Baumknoten für diese Objekte schlägt
fehl.

Bleibt `Languages[]` leer, behandelt der Schritt `eZStepPackageLanguageOptions` es als leeres Array und ordnet
Locales, die nur das Paket hat, der Hauptsprache zu, sodass die Installation ohne einen fatalen Fehler von
`array_diff()` weiterlaufen kann.

### `site_types`

```ini
[site_types]
Continue=true
Site_package=sevenx_site
```

| Feld | Beschreibung |
|-------|-------------|
| `Continue` | `true`, um die Werte dieses Abschnitts zu verwenden und die Oberfläche zu überspringen. |
| `Site_package` | Bezeichner des zu installierenden Site-Pakets. Wird `exp:kickstarter run` verwendet, wird die entfernte Paketliste aus dem konfigurierten Repository der lokalen Kopie in `var/storage/packages/` vorgezogen. Der Paketindex verwendet ein anderes Versionsformat als die Paketdateien, daher wird immer der entfernte Eintrag gewählt, wenn ein Abschnitt `[site_types]` in `kickstart.ini` aktiv ist. |

### `site_access`

```ini
[site_access]
Continue=true
Access=url
```

| Feld | Beschreibung |
|-------|-------------|
| `Access` | `url`, `port` oder `hostname`. |

### `site_details`

```ini
[site_details]
Continue=true
Title=My Exponential Site
URL=
Access=sevenx_site_user
AdminAccess=sevenx_site_admin
AccessPort=8080
AdminAccessPort=8081
AccessHostname=sevenx-site.test.com
AdminAccessHostname=sevenx-site-admin.test.com
EditorAccess=editor
EditorAccessPort=8082
EditorAccessHostname=edit.sevenx-site.test.com
Database=ezp
DatabaseAction=skip
```

| Feld | Beschreibung |
|-------|-------------|
| `Title` | Titel der Website. |
| `URL` | URL der Website; wenn leer, wird sie aus der aktuellen Anfrage abgeleitet. |
| `Access` | Name des Siteaccess für Benutzer. |
| `AdminAccess` | Name des Siteaccess für die Administration. |
| `AccessPort` | Port für den Benutzerzugang bei `Access=port`. |
| `AdminAccessPort` | Port für den Administrationszugang bei `Access=port`. |
| `AccessHostname` | Hostname für den Benutzerzugang bei `Access=hostname`. |
| `AdminAccessHostname` | Hostname für den Administrationszugang bei `Access=hostname`. |
| `EditorAccess` | Name des Siteaccess für Redakteure (Standard `editor`): die Administrationsoberfläche nur zum Bearbeiten von Inhalten, ohne Setup, Design und Entwicklerwerkzeuge. Jede Installation hat einen. |
| `EditorAccessPort` | Port für den Redakteurszugang bei `Access=port` (Standard 8082). |
| `EditorAccessHostname` | Hostname für den Redakteurszugang bei `Access=hostname`; nach Konvention `edit.<the site's domain>`. |
| `Database` | Datenbankname für diese Website. |
| `DatabaseAction` | Was zu tun ist, wenn die Datenbank bereits Daten enthält. Siehe unten. |

#### Werte von `DatabaseAction`

| Wert | Konstante | Verhalten |
|-------|----------|-----------|
| `ignore` | `DB_DATA_APPEND` (1) | Versuchen, Einträge hinzuzufügen, ohne aufzuräumen. |
| `remove` | `DB_DATA_REMOVE` (2) | Vorhandene Einträge aufräumen und Schema + Daten + Pakete installieren. |
| `skip` | `DB_DATA_KEEP` (3) | Schema und Daten nicht einfügen. Verwenden Sie das, um nur die Dateien der Siteaccesses gegen eine vorhandene Datenbank neu zu erzeugen. |

### `site_admin`

```ini
[site_admin]
Continue=true
FirstName=Admin
LastName=User
Email=<e-mail address of the first administrator>
Password=<choose a password>
```

| Feld | Beschreibung |
|-------|-------------|
| `FirstName` | Vorname des Administrators. |
| `LastName` | Nachname des Administrators. |
| `Email` | E-Mail-Adresse des Administrators. |
| `Password` | Passwort des Administrators. |

### `security`

```ini
[security]
Continue=true
```

### `registration`

```ini
[registration]
Continue=true
Send=false
```

| Feld | Beschreibung |
|-------|-------------|
| `Send` | `true`, um den Registrierungsbericht zu senden; alles andere oder gar kein `Send=` sendet nichts (der Standard). Der Bericht geht nur an `settings/setup.ini` `[RegistrationSettings] Receiver`, das standardmäßig leer ist, daher sendet selbst `Send=true` nichts, bis ein Empfänger gesetzt ist. Die alte Registrierungsadresse des Upstreams wird nie verwendet. |
| `UserData[...]` | Die Felder des Registrierungsformulars (`UserData[first_name]`, `UserData[last_name]`, `UserData[email]`, `UserData[country]`, `UserData[company]`), im Bericht verwendet, wenn er gesendet wird. |

Ein Schlüssel `Comments=`, den ältere Versionen von `exp:kickstarter ini` schrieben, wird nicht gelesen.


## Ablauf der Installation

1. **Erzeugen Sie die Konfigurationsdatei**:

   ```bash
   php bin/php/console exp:kickstarter ini --yes
   ```

2. **Prüfen und bearbeiten Sie `kickstart.ini`**. Bestätigen Sie mindestens:

   - die Zugangsdaten in `database_init`.
   - `Site_package` in `site_types`.
   - dass `Primary` und `Languages[]` in `language_options` jede Locale enthalten, die das Site-Paket verwendet.
   - dass `DatabaseAction` in `site_details` für eine saubere Installation auf `remove` gesetzt ist oder auf `skip`, um
     Siteaccesses neu zu erzeugen.

3. **Prüfen Sie, dass sich die entfernten Pakete herunterladen lassen**, ohne die Website zu ändern:

   ```bash
   php bin/php/console exp:kickstarter run --dry-run
   ```

   Das führt `DatabaseChoice` bis `SiteDetails` aus, importiert das Site-Paket und seine Abhängigkeiten nach
   `var/storage/packages/dryrun/` (das nach dem Schritt `SiteTypes` entfernt wird) und hält vor `SiteAdmin` an: Nichts
   wird installiert, keine Passwortdatei geschrieben und keine E-Mail gesendet.

4. **Führen Sie das Installationsprogramm aus**:

   ```bash
   php bin/php/console exp:kickstarter run --force
   ```

5. **Prüfen Sie, dass die Website** unter der in der Zusammenfassung geschriebenen URL erreichbar ist.


## Beispiele

### Saubere Installation

```bash
php bin/php/console exp:kickstarter ini --yes
sed -i 's/^DatabaseAction=skip$/DatabaseAction=remove/' kickstart.ini
php bin/php/console exp:kickstarter run --force
```

### Nur die Dateien der Siteaccesses und die INI-Dateien neu erzeugen

```bash
# Adjust the relevant fields and keep DatabaseAction=skip
php bin/php/console exp:kickstarter run --force --stop-step=CreateSites
```

### Nach einem fehlgeschlagenen Lauf

Ein Lauf kann nicht ab einem späteren Schritt fortgesetzt werden (siehe
[Nach einem Fehlschlag fortsetzen](#nach-einem-fehlschlag-fortsetzen)); `--start-step=SiteDetails` wird mit dem
Schritt abgelehnt, bei dem zu beginnen ist. Beheben Sie `kickstart.ini` und führen Sie die Installation erneut aus:

```bash
php bin/php/console exp:kickstarter run --force
```

### Probelauf, um `kickstart.ini` und die entfernten Pakete zu validieren

```bash
php bin/php/console exp:kickstarter run --dry-run
```

Das validiert die INI-Abschnitte, listet die Schritte des Assistenten auf und führt dann die Schritte von
`DatabaseChoice` bis `SiteDetails` aus, um die Datenbankverbindung zu prüfen und dass sich das konfigurierte Site-Paket
und alle seine entfernten Abhängigkeiten herunterladen lassen. Es hält vor `SiteAdmin` an, daher werden weder Datenbank
noch Dateien der Siteaccesses geändert, keine Passwortdatei geschrieben und keine E-Mail gesendet. Die
heruntergeladenen Pakete werden in ein temporäres Repository `var/storage/packages/dryrun/` importiert, das entfernt
wird, wenn der Schritt `SiteTypes` fertig ist.

### Die Schritte des Assistenten auflisten

```bash
php bin/php/console exp:kickstarter run --list-steps
```


## Fragen und Antworten: kurze Antworten für häufige Anliegen

### Wie fange ich am einfachsten an?

```bash
php bin/php/console exp:kickstarter ini --yes
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --force
```

Die erste Zeile erzeugt die Konfiguration. Die zweite Zeile testet die entfernten Pakete, ohne etwas zu ändern. Die
dritte Zeile installiert.

### Brauche ich für die normale Verwendung `--start-step` oder `--stop-step`?

Nein. Sie sind für Teilläufe, und ein späteres `--start-step` kann einen früheren Lauf nicht fortsetzen: Es wird
abgelehnt, wenn die Schritte davor gebraucht werden, und das ist bei einem Bereich, der installiert, immer der Fall. Die
normalen Befehle sind `run --dry-run` und `run --force`.

### Warum braucht `run` `--force`?

`CreateSites` installiert Datenbankschema, Daten und Dateien der Siteaccesses. `--force` bestätigt, dass Sie das
möchten. Ohne die Option hält der Befehl sicher an.

### Was tut `--dry-run` tatsächlich?

Es validiert `kickstart.ini`, listet die Schritte auf und führt dann `DatabaseChoice` bis `SiteDetails` aus, um die
Datenbankverbindung und den Download der Pakete zu testen. Es hält vor `SiteAdmin` an und räumt das temporäre
Repository `dryrun/` auf.

### Was ändert `--dry-run` auf der Festplatte?

Nichts Dauerhaftes. Es lädt Pakete nach `var/storage/packages/dryrun/` herunter und entfernt dieses Verzeichnis, wenn
`SiteTypes` fertig ist. Es berührt weder die Datenbank noch `settings/siteaccess/`, schreibt kein
`var/log/initial-admin-password` und sendet keine E-Mail.

### Was ändert `run --force`?

Es installiert das Site-Paket und die Abhängigkeiten nach `var/storage/packages/`, schreibt `settings/siteaccess/`,
legt das Datenbankschema an und fügt die Daten der Website ein. Verwenden Sie zuerst `--dry-run` zum Testen.

### Kann ich `run --dry-run` ohne funktionierende Datenbank ausführen?

Nein. `DatabaseChoice` und `DatabaseInit` testen weiterhin die Verbindung. Es werden aber keine Daten geschrieben. Die
Datenbank muss nur erreichbar sein.

### Warum verwendet der entfernte Paketindex eine andere Version?

Für die Site-Pakete von 7x und `cjw_multisite` tut er das nicht mehr: Seit dem 2026-10-19 führt der Index für 6.0.15 sie
als `6.0-15-stable` auf, den Text, den `eZPackage::getVersion()` für ihre `package.xml` zurückgibt (vorher `1.0.0-0` und
`6.0.15-0`). Andere Einträge tragen weiterhin eigene Versionen, daher behält der Kickstart-Modus den entfernten Eintrag,
was immer der Vergleich sagt.

### Macht das den normalen Setup-Assistenten im Web kaputt?

Nein. Der Setup-Assistent im Web verwendet `eZStepSiteTypes::display()` mit der alten Logik des Versionsvergleichs. Das
Bevorzugen des entfernten Eintrags geschieht nur innerhalb von `eZStepSiteTypes::init()`, ausgelöst durch
`kickstart.ini` `[site_types]` mit `Continue=true`. Lassen Sie `kickstart.ini` nicht im Web-Wurzelverzeichnis, bleibt der
Web-Assistent unverändert.

### Macht die Änderung an `eZPackage::import()` andere Werkzeuge kaputt?

Nein. Die Signatur ist
`eZPackage::import( $archiveName, &$packageName, $dbAvailable = true, $repositoryID = false, $skipExisting = false )`.
Das neue fünfte Argument ist optional und standardmäßig `false`. Alle vorhandenen Aufrufer (`ezpm.php`, `upload.php`,
`bin/php/ezwebincommon.php`, Tests) funktionieren weiter.

### Ist es sicher, `kickstart.ini` im Projektwurzelverzeichnis zu lassen?

`kickstart.ini` wird vom CLI-Werkzeug gelesen und kann, wenn vorhanden, auch vom Setup-Assistenten im Web gelesen
werden. Soll der Web-Assistent sie nicht verwenden, entfernen oder verschieben Sie sie nach der Installation über die
CLI.

### Was, wenn `Languages[]` in `language_options` leer ist?

Das Installationsprogramm wandelt die Liste der Sprachen jetzt in ein Array um, sodass ein leeres `Languages[]` keinen
fatalen Fehler von `array_diff()` mehr verursacht. Die Paketsprachen werden der Hauptsprache zugeordnet.

### Was ist das Verzeichnis `dryrun/`?

Ein temporäres Paket-Repository, das nur während `--dry-run` angelegt wird. `eZPackage::import()` unterstützt ein Flag
`skipExisting`, sodass der Probelauf das entfernte Paket dort importieren kann, ohne mit der lokalen Kopie
`7x/sevenx_site` zu kollidieren. `expKickstarter` entfernt `dryrun/` nach `SiteTypes` und entfernt außerdem beim Start
der CLI ein übrig gebliebenes `dryrun/`.

### Was, wenn der entfernte Paketserver nicht erreichbar ist?

`eZStepSiteTypes` fällt auf eine lokale `index.xml` zurück, wenn sich die entfernte `index.xml` nicht herunterladen
lässt. Ist keine von beiden verfügbar, bleibt der Schritt stehen und zeigt die Paketliste, damit Sie ein Paket von Hand
hochladen können. Der normale Web-Assistent ist nicht betroffen, weil er den Kickstart-Modus nur verwendet, wenn
`kickstart.ini` existiert.

### Was, wenn der Probelauf unterbrochen wird und `dryrun/` zurückbleibt?

`expKickstarter` entfernt `dryrun/` nach `SiteTypes` und außerdem zu Beginn jedes CLI-Laufs ein übrig gebliebenes
`dryrun/`. Das Verzeichnis ist ein temporärer Cache und enthält weder Datenbank noch Einstellungen der Website.

### Ist es für den langfristigen Einsatz sicher?

Ja. Die Änderungen sind additiv. Der Pfad von `--dry-run` ist vom Installationspfad getrennt. Der Installationspfad
(`run --force`) verlangt weiterhin die Sperre `--force`. Die APIs von `eZPackage` und `eZStepSiteTypes` behalten ihr
altes Verhalten, sofern die neuen optionalen Flags nicht verwendet werden.

## Fehlerbehebung

### `kickstart.ini not found`

Erzeugen Sie sie zuerst:

```bash
php bin/php/console exp:kickstarter ini
```

### `The CreateSites step will modify the database and site settings`

Fügen Sie `--force` hinzu, um zu bestätigen, dass die Datenbank geändert wird:

```bash
php bin/php/console exp:kickstarter run --force
```

### `eZINI` cacht veraltete Werte

Sowohl `exp:kickstarter ini` als auch `exp:kickstarter run` löschen `var/cache/ini/kickstart-*.php` automatisch.
Bearbeiten Sie `kickstart.ini` von Hand, leeren Sie dieses Verzeichnis:

```bash
rm -f var/cache/ini/kickstart-*.php
```

### Inhaltsseiten werden gerendert, aber Unterelemente (Bilder, Galerien) fehlen

Das bedeutet meist, dass die Paketsprachen nicht mit den Sprachen der Website abgeglichen wurden. Stellen Sie sicher,
dass `Languages[]` in `language_options` von `kickstart.ini` jede Locale aufführt, die das Site-Paket verwendet.
Verwendet das Paket `eng-US`, fügen Sie hinzu:

```ini
[language_options]
Primary=eng-GB
Languages[]=eng-US
```

Setzen Sie dann `DatabaseAction=remove` und führen Sie das Installationsprogramm erneut aus:

```bash
php bin/php/console exp:kickstarter run --force
```

### Felder von `kickstart.ini` werden nicht gelesen

- Stellen Sie sicher, dass vor Abschnittsköpfen oder Schlüsseln kein führender Leerraum steht.
- Stellen Sie sicher, dass `Continue=true` für den Abschnitt gesetzt ist, den Sie überspringen möchten.
- Stellen Sie sicher, dass die Datei als `kickstart.ini` im Projektwurzelverzeichnis gespeichert ist.

### Das Site-Paket wird nicht vom entfernten Server heruntergeladen

- Bestätigen Sie, dass `package.ini` auf eine erreichbare URL einer `index.xml` zeigt.
- Bestätigen Sie, dass `Site_package` in `site_types` zum Attribut `name` in der entfernten `index.xml` passt.
- Ein Paket, das bereits in `var/storage/packages/` liegt, wird nicht erneut heruntergeladen; entfernen Sie die lokale
  Kopie, um einen Download zu erzwingen.
- Denken Sie daran, dass `hasKickstartData()` wahr sein muss, damit der entfernte Eintrag bevorzugt wird. Der Abschnitt
  `[site_types]` muss `Continue=true` haben und `Site_package` gesetzt sein.
- Führen Sie `--dry-run` aus, um genau zu sehen, wo der Download fehlschlägt.


## Dateien

| Datei | Zweck |
|------|---------|
| `bin/php/kickstarter.php` | Einstiegspunkt für die Unterbefehle `ini` und `run`. |
| `kernel/classes/expkickstarter.php` | Klasse `expKickstarter`, die die Schritte des Setup-Assistenten steuert, einschließlich Ablauf und Aufräumen von `--dry-run`. |
| `kernel/classes/expkickstarterini.php` | Klasse `expKickstarterIni`, die `kickstart.ini` aus `kickstart.ini-dist` erzeugt. |
| `kernel/classes/ezpackage.php` | `eZPackage::import()` unterstützt jetzt `skipExisting` und ein Ziel-Repository für Importe im Probelauf. |
| `kernel/setup/steps/ezstep_site_types.php` | `eZStepSiteTypes` wählt im Kickstart-Modus das entfernte Site-Paket und lädt es herunter, einschließlich der Auflösung von Abhängigkeiten. |
| `kernel/setup/steps/ezstep_package_language_options.php` | `eZStepPackageLanguageOptions` behandelt ein leeres `Languages[]`, indem es in ein Array umgewandelt wird. |
| `kernel/setup/steps/ezstep_data.php` | Tabelle der Schritte des Assistenten. |
| `kickstart.ini-dist` | Kommentierte Vorlage, die jeden Abschnitt und jedes Feld beschreibt. |
| `kickstart.ini` | Erzeugte/aktive Konfiguration. |

## Hinweise zur Implementierung

### Änderungen an `eZPackage::import()`

`eZPackage::import( $archiveName, &$packageName, $dbAvailable = true, $repositoryID = false, $skipExisting = false )`
nimmt jetzt ein fünftes Argument an. Ist `$skipExisting` `true`, überspringt die Funktion die Prüfung mit
`eZPackage::fetch()`, die normalerweise `STATUS_ALREADY_EXISTS` zurückgeben würde. Das erlaubt dem Pfad des Probelaufs,
ein Paket in ein anderes als das Standard-Repository zu importieren (z. B. `var/storage/packages/dryrun/`), während eine
Kopie mit demselben Namen noch in `var/storage/packages/7x/` existiert.

### Änderungen an `eZStepSiteTypes`

- `downloadAndImportPackage( $packageName, $packageUrl, $forceDownload = false, $repositoryID = false, $skipExisting = false )`
  leitet jetzt die optionalen Flags für Repository und Überspringen vorhandener Pakete an `eZPackage::import()` weiter.
- `downloadDependantPackages( $sitePackage, $repositoryID = false, $skipExisting = false )` leitet jetzt dieselben Flags
  an `downloadAndImportPackage()` weiter.
- Ist in `init()` die POST-Variable `eZSetupKickstartDryRun` gesetzt, wird das Site-Paket mit `skipExisting=true` in das
  Repository `dryrun` importiert. Im normalen Modus wird es in das eigene Herstellerverzeichnis des Pakets (`7x`) mit
  `forceDownload=false` importiert: Ein bereits vorhandenes Paket wird so verwendet, wie es ist.
- `createSitePackagesList()` ruft jetzt vor dem Versionsvergleich `$this->hasKickstartData()` auf. Ist eine
  Kickstart-Konfiguration aktiv, wird der lokale Paketeintrag übersprungen und der entfernte behalten, sodass die
  entfernte URL erhalten bleibt; das Paket wird nur heruntergeladen, wenn es nicht im lokalen Repository liegt.

### Änderungen an `eZStepPackageLanguageOptions`

`siteLanguageLocaleList` wird jetzt sowohl in `processPostData()` als auch in `init()` mit `(array)` umgewandelt, sodass
ein leerer Wert für `Languages[]` in `kickstart.ini` keinen fatalen Fehler von `array_diff()` in
`eZStepPackageLanguageOptions::init()` mehr auslöst.


## Die Zusammenfassung endet mit einer Endzeit und einer Dauer

Nach der Zusammenfassung gibt der Kickstarter eine Linie aus, dann `Finished:` mit Datum, Uhrzeit und Zeitzone und
`Elapsed:` als `hh:mm:ss (n seconds)`. Zwei Läufe derselben `kickstart.ini` geben sonst identischen Text aus, daher
unterscheidet die Endzeit einen frischen Lauf vom Scrollback, und die Dauer lässt Sie sie vergleichen (`showFinishTime()`
in `kernel/classes/expkickstarter.php`).

Jeder Lauf wird außerdem mit maskierten Passwörtern nach `var/log/kickstart.log` protokolliert; frühere Läufe werden als
`kickstart.log.1` (der letzte) bis `kickstart.log.9` behalten, und `EXP_KICKSTART_LOG=0` schaltet das Log aus (siehe
`php bin/php/kickstarter.php --help`). Die Schritte auflisten, ohne etwas zu ändern:
`php bin/php/kickstarter.php run --list-steps`.


## Verwandte Seiten

- [Kickstarter-CLI (Funktionsseite)](../../funktionen/6.0/kickstarter-cli.md) und [Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)
- [Anleitung für den Einstieg](../../anleitungen/erste-schritte.md)
- [Logs des Installationsprogramms und Startdaten](../../spezifikationen/6.0/installer-protokolle-und-startdaten.md)
- [Exponential-Konsole](konsole.md)
- [September 2026, erste Hälfte: 13. September](../../geschichte/2026/2026-09a.md#13-september-caches-die-sie-sehen-cronjobs-die-sie-ausführen-können)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- `kickstart.ini-dist` im Projektwurzelverzeichnis: jeder Abschnitt und jedes Feld, kommentiert.
