# 6. Der Kickstarter

Der Kickstarter installiert Exponential auf der Kommandozeile aus einer einzigen Datei, `kickstart.ini` im
Installationsverzeichnis. Er führt die Schrittklassen des Setup-Assistenten in einem einzigen PHP-Prozess aus, nimmt
jede Antwort aus der Datei statt aus einem Formular, hält die Site während seiner Arbeit im Wartungsmodus und
protokolliert den Durchlauf in `var/log/kickstart.log` und `var/log/setup.log`. Dieses Kapitel ist die vollständige
Referenz: die beiden Unterbefehle `ini` und `run` mit jeder Option und jedem Exit-Status, jeder Abschnitt und jeder
Schlüssel von `kickstart.ini` mit Typ, Standardwert und Wirkung, so wie der Code sie anwendet, kommentierte
Beispieldateien für SQLite, MySQL und PostgreSQL, die Datenbankaktion und ihre Gefahren, Probeläufe, Fortsetzen und
erneutes Ausführen sowie der Einsatz des Kickstarters in CI und Containern. Die Kurzfassung: Schreiben Sie die Datei
mit `exp:kickstarter ini`, prüfen Sie jeden Wert anhand von [6.4](#64-referenz-zu-kickstartini) (vor allem
`DatabaseAction`), führen Sie `run --dry-run` aus und dann `run --force`.

[Inhalt](README.md) · Zurück: [5. Der Setup-Assistent](05-setup-assistent.md) · Weiter: [7. Die Installation über die Konsole](07-installation-ueber-die-konsole.md)

## 6.1 Wie er funktioniert

```text
php bin/php/console exp:kickstarter run --force
        |
        |  bin/php/kickstarter.php  ->  \Exponential\Command\Kernel\Kickstarter
        |  (kernel/private/classes/commands/kickstarter.php)
        |
        |  1. rotates var/log/kickstart.log -> kickstart.log.1 ... .9
        |  2. starts itself again as a child process (same PHP binary, same memory_limit,
        |     date.timezone and max_execution_time), copies the child's output to the
        |     terminal and to var/log/kickstart.log, masking the passwords of kickstart.ini
        v
expKickstarter (kernel/classes/expkickstarter.php), in the child
        |  - boots a CLI script with the siteaccess "plain", HTTP_HOST "localhost" when unset,
        |    and the time zone Europe/London when PHP's is UTC
        |  - deletes the cached copies var/cache/ini/kickstart-*.php
        |  - switches maintenance mode on (not in a dry run; an open window is kept)
        |  - runs the steps Welcome .. Final from kernel/setup/steps/ezstep_data.php,
        |    each one reading its section of kickstart.ini
        |  - switches maintenance mode off and prints a summary
        v
    a site, or "Setup failed on step: <Step>" and exit status 1
```

Der Befehl hat zwei Namen: `php bin/php/console exp:kickstarter` und `php bin/php/kickstarter.php`. Beide nehmen
dieselben Unterbefehle und Optionen an. Viele Installationen legen im Installationsverzeichnis eine Abkürzung `console`
an (`./console exp:kickstarter`).

## 6.2 Die Befehle

```text
Usage: ./bin/php/console exp:kickstarter <command> [options]
       ./bin/php/kickstarter.php <command> [options]
```

| Befehl | Wirkung |
|---|---|
| `ini` | `kickstart.ini` schreiben oder bearbeiten |
| `run` | aus `kickstart.ini` installieren |
| `help`, `--help`, `-h` oder kein Befehl | die Hilfe anzeigen |

Jedes andere Wort gibt „Unknown command: *Wort*“ und die Hilfe aus, mit Exit-Status 1. Ein Aufruf ohne Befehl zeigt die
Hilfe und endet mit Status 0, sodass ein Skript, das den Unterbefehl vergessen hat, nicht fehlschlägt; prüfen Sie, dass
Ihre Skripte `run` angeben.

### 6.2.1 `ini`: kickstart.ini schreiben

```bash
php bin/php/console exp:kickstarter ini              # interactive editor
php bin/php/console exp:kickstarter ini --yes        # built-in defaults, no questions
php bin/php/console exp:kickstarter ini --defaults   # the values of kickstart.ini-dist
```

| Option | Kurzform | Wirkung |
|---|---|---|
| `--defaults` | `-d` | `kickstart.ini` aus den Werten in `kickstart.ini-dist` schreiben und beenden |
| `--yes` | `-y` | die eingebauten Standardwerte anwenden (Tabelle unten) und `kickstart.ini` ohne Rückfrage schreiben |
| `--from-installed` | | `Server`, `Database` und `User` (niemals `Password`) eines installierten Siteaccess als Standardwerte übernehmen; standardmäßig aus |
| `--help` | `-h` | die Hilfe von `ini` anzeigen |

So wird die Datei aufgebaut (`kernel/classes/expkickstarterini.php`):

1. `kickstart.ini-dist` wird gelesen. Jeder auskommentierte Abschnitt (`#[name]`) und Schlüssel (`#Key=value`) wird zu
   einem Feld; eine Kommentarzeile `## ...` über einem Schlüssel wird zu seiner Beschreibung.
2. Existiert bereits eine `kickstart.ini`, werden ihre Werte darübergelegt, sodass `ini` die vorhandene Datei
   bearbeitet.
3. Mit `--yes` werden die eingebauten Standardwerte über beides gelegt (nur für die Schlüssel, die einen haben), und
   jedes leere `Continue` wird zu `true`.
4. Die Datei wird mit dem Modus `0600` geschrieben (nur für den Eigentümer, auch wenn eine ältere `kickstart.ini`
   weitere Rechte hatte), weil sie Passwörter enthält. Sie beginnt mit einem Kommentarkopf `#`, der die zu prüfenden
   Werte aufführt, und über jedem dieser Schlüssel steht eine Zeile `# REVIEW: ...`: beide `Database`-Schlüssel,
   `DatabaseAction`, `URL` sowie `Email` und `Password` des Administrators. Danach folgt ein Abschnitt je Schritt des
   Assistenten, und die zwischengespeicherten Kopien `var/cache/ini/kickstart-*.php` werden gelöscht.

Ohne `--yes` oder `--defaults` braucht der Befehl ein Terminal; andernfalls hält er mit „No TTY detected. Run with
--defaults or --yes for non-interactive mode.“ und Exit-Status 1 an. Er hält auch an, wenn `kickstart.ini-dist` fehlt
(„kickstart.ini-dist not found in project root.“).

**Die Standardwerte von `--yes`:**

| Abschnitt | Schlüssel | Standardwert |
|---|---|---|
| `database_choice` | `Type` | `sqlite3` |
| `database_init` | `Server`, `Port`, `Database`, `User`, `Password`, `Socket` | `localhost`, leer, `exponential.db` (der Standardwert für den Standard-`Type`), `root`, leer, leer |
| `language_options` | `Primary`, `Languages[]` | `eng-US`, keine |
| `site_types` | `Site_package` | `sevenx_multisite` (das Paket, das `exp:install` installiert) |
| `site_access` | `Access` | `url` |
| `site_details` | `Title` | `My Exponential Site` |
| `site_details` | `URL` | leer |
| `site_details` | `Access`, `AdminAccess`, `EditorAccess` | `sevenx_site_user`, `sevenx_site_admin`, `editor` |
| `site_details` | `AccessPort`, `AdminAccessPort`, `EditorAccessPort` | `8080`, `8081`, `8082` |
| `site_details` | `AccessHostname`, `AdminAccessHostname`, `EditorAccessHostname` | `sevenx-site.test.com`, `sevenx-site-admin.test.com`, `edit.sevenx-site.test.com` |
| `site_details` | `Database`, `DatabaseAction` | `exponential.db`, `ignore` |
| `site_admin` | `FirstName`, `LastName`, `Email`, `Password` | `Admin`, `User`, `admin@example.com`, leer (bei der Installation wird ein Passwort erzeugt) |
| `registration` | `Comments`, `Send` | leer (wird von `run` nicht gelesen), `false` |

**Lesen Sie die Datei vor `run`.** `--yes` schreibt eine Datei, die so, wie sie ist, eine SQLite-Site in eine leere
Datenbank installiert, aber die mit `# REVIEW` markierten Werte sind Vermutungen:

- Mit `--yes` sind `[database_init] Database` und `[site_details] Database` gleich `exponential.db`, eine Datei in
  `var/storage/sqlite3/` (der Name, den `exp:install` für SQLite verwendet). Wählen Sie im interaktiven Editor einen
  anderen `Type`, folgt ihm ein `Database`, das noch einen Standardwert enthält: `exponential` für MySQL, PostgreSQL
  und MongoDB, `localhost:1521/FREEPDB1` für Oracle. Setzen Sie bei einem Datenbankserver beide auf die Datenbank, die
  Sie angelegt haben.
- Mit `--yes` ist `DatabaseAction` gleich `ignore`: Schema, Basisdaten und Paket werden in die Datenbank installiert,
  so wie sie ist, und nichts wird gelöscht. Das ist für eine neue, leere Datenbank richtig (eine SQLite-Datei, die noch
  nicht existiert, wird angelegt). Auf einer Datenbank, die bereits eine Site enthält, überspringt `ignore` die
  vorhandenen Tabellen und setzt das Paket obendrauf; lassen Sie `Database` deshalb auf eine neue zeigen. `remove`,
  das die Datenbank zuerst leert, wird nur geschrieben, wenn Sie es wählen (lesen Sie zuerst
  [6.5](#65-databaseaction-lesen-sie-dies-bevor-sie-run-ausführen)).
- Mit `--yes` ist `[site_details] URL` leer, sodass die Adresse der Site `http://localhost` wird. Setzen Sie die
  echte Adresse.
- Mit `--defaults` ist jeder Wert das Beispiel aus `kickstart.ini-dist`: das Paket `news_site`, die Datenbanken
  `ezp35test` und `ezp39test`, `DatabaseAction=remove`, der Administrator „God Like“. Das ist ein Ausgangspunkt zum
  Bearbeiten, nicht mehr.

Ein durchgespieltes Beispiel für eine SQLite-Testsite:

```bash
php bin/php/console exp:kickstarter ini --yes
# edit kickstart.ini: URL=http://localhost:8087, Email= your address
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --force
```

`ini` schreibt die Datei mit dem Modus `0600`, weil sie Passwörter enthält; belassen Sie es dabei.

Die Standardwerte übernehmen nichts aus einer bereits vorhandenen Installation: Deren Siteaccess-Einstellungen nennen
eine Live-Datenbank, und eine daraus geschriebene Datei würde über sie hinweg installieren. Mit `--from-installed`
durchsucht der Generator `settings/siteaccess/*/site.ini` und übernimmt `Server`, `Database` und `User` des ersten
Siteaccess, der eine Datenbank und einen Benutzer nennt, für `[database_init]` und `[site_details] Database`; das
`Password` wird nie kopiert, geben Sie es selbst ein. **Eine mit `--from-installed` erzeugte Datei zeigt auf die
Datenbank dieser Installation**: Lesen Sie sie, bevor Sie sie ausführen, und prüfen Sie `DatabaseAction`.

**Der interaktive Editor.** `ini` ohne Optionen zeigt ein Menü der Abschnitte mit einer Zusammenfassung jedes
Abschnitts:

| Taste | Im Hauptmenü | In einem Abschnitt |
|---|---|---|
| eine Zahl | diesen Abschnitt bearbeiten | dieses Feld bearbeiten |
| `n` / `p` | nächster / vorheriger Abschnitt | nächster / vorheriger Abschnitt |
| `m` | | zurück zum Hauptmenü |
| `w` | Assistent: jedes Feld jedes Abschnitts durchgehen, dann speichern | |
| `v` | Vorschau der Datei (dabei wird sie geschrieben) | |
| `s` | speichern und bleiben | speichern und bleiben |
| `q` | speichern und beenden | speichern und beenden |
| `?` | Hilfe | Hilfe |

Felder werden entsprechend ihrem Typ bearbeitet: `Continue` und `Send` als Ja/Nein, `Type`, `Access` und
`DatabaseAction` als nummerierte Auswahl, Ports als Zahlen, `Password` ohne Bildschirmausgabe, `Languages[]` als
kommagetrennte Liste (`+value` hängt an). Ein „Beenden ohne Speichern“ gibt es nicht: Brechen Sie mit Strg-C ab, um
die Datei unverändert zu lassen.

### 6.2.2 `run`: aus kickstart.ini installieren

```bash
php bin/php/console exp:kickstarter run --list-steps    # show the steps, change nothing
php bin/php/console exp:kickstarter run --dry-run       # check, stop after SiteDetails
php bin/php/console exp:kickstarter run --force         # install
```

| Option | Standard | Wirkung |
|---|---|---|
| `--force` | aus | erforderlich, sobald der Bereich der Schritte `CreateSites` einschließt. Ohne diese Option hält `run` mit „The CreateSites step will modify the database and site settings.“ und „Re-run with --force to confirm you want to install the site package.“ an, Exit-Status 1 |
| `--dry-run` | aus | listet die gefundenen Abschnitte und die Schritte auf, führt dann `DatabaseChoice` bis `SiteDetails` aus (Start- und Endschritt setzt er selbst), importiert das Site-Paket in ein temporäres Repository und hält vor `SiteAdmin` an. Kein `--force` nötig. Siehe [6.7](#67-probeläufe) |
| `--list-steps` | aus | gibt die Schritttabelle aus und endet mit Status 0. Wie jedes `run` braucht es eine `kickstart.ini` im Installationsverzeichnis; ohne sie hält es mit der „not found“-Meldung aus [6.2.3](#623-exit-status) an |
| `--start-step=<Step>` | `welcome` | erster auszuführender Schritt. Wird abgelehnt, wenn ein Schritt im Bereich Ergebnisse von Schritten davor braucht, siehe [6.10](#610-erneut-ausführen-und-fortsetzen) |
| `--stop-step=<Step>` | `final` | letzter auszuführender Schritt |
| `--help`, `-h` | | die Hilfe von `run` |

Die üblichen Skriptoptionen (`--allow-root-user`, `--no-colors`, `--quiet`, `--debug`, `--verbose`, `--siteaccess`,
`--logfiles`) werden angenommen und ignoriert; der Kickstarter läuft immer auf dem Siteaccess plain. `exp:install`
reicht `--allow-root-user` auf diesem Weg weiter.

Schrittnamen sind die Klassennamen aus der Schritttabelle und werden ohne Rücksicht auf Groß- und Kleinschreibung
verglichen (`SiteDetails`, `sitedetails`). Ein unbekannter Name hält den Durchlauf mit „Unknown start step: ...“ oder
„Unknown stop step: ...“ an.

`--list-steps` gibt aus:

```text
Setup steps:
  [ 0] Welcome                  (counted)
  [ 1] SystemCheck              (counted)
  [ 2] SystemFinetune           (counted)
  [ 3] EmailSettings            (counted)
  [ 4] DatabaseChoice           (counted)
  [ 5] DatabaseInit             (counted)
  [ 6] LanguageOptions          (counted)
  [ 7] SiteTypes                (counted)
  [ 8] PackageLanguageOptions   (counted)
  [ 9] SiteAccess               (counted)
  [10] SiteDetails              (counted)
  [11] SiteAdmin                (counted)
  [12] Security                 (counted)
  [13] Registration             (counted)
  [14] CreateSites              (hidden)
  [15] Final                    (counted)
```

Die Umgebungsvariablen von `run`:

| Variable | Wirkung |
|---|---|
| `EXP_KICKSTART_LOG=0` | kein `var/log/kickstart.log` und kein Kindprozess |
| `EXP_SETUP_LOG_DIR=<dir>` | `setup.log` in ein anderes Verzeichnis schreiben (von Tests verwendet, damit kein echtes Log rotiert wird) |

### 6.2.3 Exit-Status

| Status | Wann |
|---|---|
| `0` | die Installation wurde abgeschlossen; oder `--list-steps`; oder ein Probelauf, der durchgelaufen ist; oder `ini` hat die Datei geschrieben; oder die Hilfe |
| `1` | `kickstart.ini` nicht gefunden („kickstart.ini not found. Generate it first with: ./bin/php/console exp:kickstarter ini“); ein unbekannter Befehl, eine unbekannte Option oder ein unbekannter Schrittname; ein `--start-step`, der nicht funktionieren kann; `--force` fehlt; `kickstart.ini` hat keine Abschnitte (Probelauf); ein Schritt ist fehlgeschlagen; `ini` konnte nicht ausgeführt werden oder nicht schreiben |

Ist das Durchlauf-Log eingeschaltet, endet der Elternprozess mit dem Status des Kindprozesses, sodass Skripte dieselben
Werte sehen.

### 6.2.4 Was ein Durchlauf ausgibt

Ein erfolgreicher Durchlauf gibt den Fortschritt jedes Schritts aus und dann:

```text
Maintenance mode off: the site answers again.

Setup summary
--------------------------------------------------
Site package: sevenx_multisite
Title:        My Exponential site
Database:     exponential
Access type:  url
Site URL:     https://www.example.com/site
Admin URL:    https://www.example.com/admin
--------------------------------------------------
Finished:     2026-10-05 10:15:42 CEST
Elapsed:      00:02:31 (151.3 seconds)
```

Ein fehlgeschlagener Schritt gibt `Step <Step> failed:` aus, gefolgt von einer Zeile je Problem, zum Beispiel einem
fehlgeschlagenen Systemtest mit seiner Meldung, der Datenbankmeldung aus
[Kapitel 5](05-setup-assistent.md#536-initialisierung-der-datenbank) oder den codierten Fehlern von `CreateSites`
([Abschnitt 5.3.15](05-setup-assistent.md#5315-erstellung-der-seiten)), und danach `Setup failed on step: <Step>`. Musste ein
Passwort erzeugt werden, gibt der Durchlauf es einmal aus (siehe `[site_admin]` unten).

Die Zeit bei `Finished` steht in der Zeitzone von PHP, mit einer Ausnahme: Ist diese Zone UTC (kein `date.timezone` in
der `php.ini` der Kommandozeile), wechselt der Kickstarter vor dem Start auf `Europe/London`, sodass die angezeigte Zeit
im Sommer eine Stunde von UTC abweichen kann. Setzen Sie `date.timezone` für das PHP der Kommandozeile, dann sind die
Zeiten Ihre eigenen.

## 6.3 Wie sich jeder Schritt auf der Kommandozeile verhält

Auf der Kommandozeile gibt es niemanden, dem eine Seite angezeigt werden könnte; ein Schritt, der eine Seite anzeigen
würde, schlägt deshalb fehl, mit diesen Ausnahmen:

| Schritt | Abschnitt | Ohne Abschnitt oder mit `Continue=false` |
|---|---|---|
| `Welcome` | keiner nötig | setzt die Sprache des Assistenten auf `eng-GB` und fährt fort |
| `SystemCheck` | keiner | führt die kritischen Tests aus; ein fehlgeschlagener Test lässt den Durchlauf fehlschlagen und wird mit seiner Meldung aufgeführt |
| `SystemFinetune` | keiner | fährt fort, gleich was die optionalen Tests ergeben |
| `EmailSettings` | `[email_settings]` | **schlägt fehl** |
| `DatabaseChoice` | `[database_choice]` | schlägt fehl, außer es ist genau eine Datenbank-Extension geladen |
| `DatabaseInit` | `[database_init]` | **schlägt fehl** |
| `LanguageOptions` | `[language_options]` | **schlägt fehl** |
| `SiteTypes` | `[site_types]` | **schlägt fehl** |
| `PackageLanguageOptions` | keiner | legt jede Paketsprache, die Sie nicht gewählt haben, als sie selbst an (die Voreinstellung des Assistenten) und fährt fort |
| `SiteAccess` | `[site_access]` | **schlägt fehl** |
| `SiteDetails` | `[site_details]` | **schlägt fehl** |
| `SiteAdmin` | `[site_admin]` | **schlägt fehl** (übersprungen bei `DatabaseAction=skip`) |
| `Security` | `[security]` | fährt fort |
| `Registration` | `[registration]` | fährt fort, ohne etwas zu senden |
| `CreateSites` | keiner | installiert |
| `Final` | keiner | beendet den Durchlauf |

Ein Schritt, der fehlschlägt, weil sein Abschnitt fehlt oder nicht `Continue=true` enthält, nennt den Grund, zum
Beispiel „kickstart.ini has no [site_admin] section: the step needs one to run without the web wizard“ oder
„kickstart.ini [site_access] has Continue=false: the step stops there for the web wizard. Set Continue=true to run it
from the command line.“ „Unknown failure“ bleibt nur für einen Schritt, der aus einem Grund angehalten hat, den der
Kickstarter nicht erkennen kann.

Für die Kommandozeile gilt also: **Geben Sie jeden Abschnitt von `[email_settings]` bis `[site_admin]` an, jeden mit
`Continue=true`.** Dieselbe Datei steuert auch den Browser-Assistenten ([Kapitel 5](05-setup-assistent.md#56-wie-kickstartini-seiten-vorbelegt-und-überspringt)).

Der Systemtest läuft als der Benutzer, der den Kickstarter ausführt. Führen Sie ihn als den Benutzer aus, unter dem der
Webserver oder Velocity läuft, oder korrigieren Sie danach die Eigentümer von `settings/`, `var/` und `design/`; die
Gesundheitsprüfungen am Ende von `var/log/setup.log` umfassen auch die Dateieigentümer.

## 6.4 Referenz zu kickstart.ini

### 6.4.1 Syntax

`kickstart.ini` wird von `eZINI` gelesen wie jede Einstellungsdatei von Exponential:

- ein `[section]` je Schritt, benannt nach dem Bezeichner des Schritts;
- `Key=value`, eines pro Zeile; Zeilen `Key[]=value` bilden ein Array;
- **kein Leerraum vor einem Abschnitt oder Schlüssel**: Eine eingerückte Zeile wird nicht gelesen;
- eine Zeile, die mit `#` oder `;` beginnt, ist ein Kommentar; ein Kommentar hinter einem Wert ist es nicht:
  `Key=value ; note` ergibt den Wert `value ; note`, schreiben Sie also jeden Kommentar in eine eigene Zeile;
- Werte sind Text; boolesche Werte sind die Wörter `true` und `false`.

Belassen Sie die Datei im Modus `0600`: Sie enthält Passwörter im Klartext. Committen Sie sie niemals.

### 6.4.2 Abschnitte und Schlüssel

Typen: *bool* ist `true`/`false`, *Auswahl* eines der aufgeführten Wörter, *int* eine Zahl, *Text* freier Text,
*geheim* Text, der in den Logs maskiert wird.

**`[welcome]`** (nicht in `kickstart.ini-dist`)

| Schlüssel | Typ | Standard | Wirkung |
|---|---|---|---|
| `Continue` | bool | | mit `true` überspringt der Browser-Assistent seine Willkommensseite. Die Kommandozeile überspringt sie ohnehin |

**`[email_settings]`**

| Schlüssel | Typ | Standard | Wirkung |
|---|---|---|---|
| `Continue` | bool | `false` | `true`: die Werte verwenden, nicht nachfragen |
| `Type` | Auswahl: `mta`, `smtp` | `mta` | `smtp` schreibt `Transport=SMTP`; jeder andere Wert bedeutet Sendmail/MTA. Unter Windows wird `smtp` erzwungen |
| `Server` | Text | | SMTP-Host (`TransportServer`), verwendet mit `smtp` |
| `User` | Text | | SMTP-Benutzer (`TransportUser`) |
| `Password` | geheim | | SMTP-Passwort (`TransportPassword`) |

**`[database_choice]`**

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Empfehlung:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
> zusätzlichem Aufwand möglich. Einzelheiten: [Datenbankunterstützung](../../eng-us/help/database-support.md).

| Schlüssel | Typ | Standard | Wirkung |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Type` | Auswahl | `setup.ini [DatabaseSettings] DefaultType` (`sqlite3`) | `sqlite3` (Alias `sqlite`), `mysqli` (Alias `mysql`), `pgsql` (Alias `postgresql`), `mongodb`, `oci8` (Aliase `oracle`, `ezoracle`; braucht `extension/ezoracle`). Ein unbekannter Wert wird ignoriert, und der Schritt fällt auf Nachfragen zurück |

**`[database_init]`**

| Schlüssel | Typ | Standard | Wirkung |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Server` | Text | leer | Datenbank-Host |
| `Port` | int | leer (der Standard des Treibers) | |
| `Database` | Text | **erforderlich** | die Datenbank. SQLite: ein Dateiname in `var/storage/sqlite3/`, der `^[A-Za-z0-9][A-Za-z0-9_.-]{0,99}\.(db\|db3\|sqlite\|sqlite3)$` entspricht, oder ein absoluter Pfad ohne `/../` (nur aus `kickstart.ini` erlaubt). Oracle: der Connect-String, z. B. `127.0.0.1:1521/FREEPDB1`, oder ein TNS-Alias |
| `User` | Text | leer | |
| `Password` | geheim | leer | |
| `Socket` | Text | leer | Pfad eines lokalen MySQL-Sockets; leer schreibt `Socket=disabled` |

Dieser Schritt verbindet sich mit der Datenbank, um sie zu prüfen. Prüfungen und Meldungen sind die des Assistenten
([Abschnitt 5.3.6](05-setup-assistent.md#536-initialisierung-der-datenbank)).

**`[language_options]`**

| Schlüssel | Typ | Standard | Wirkung |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Primary` | Text (Locale) | **erforderlich** | die Hauptsprache, z. B. `eng-US` |
| `Languages[]` | Textliste | keine | zusätzliche Sprachen. Die Hauptsprache darf hier ebenfalls stehen; sie wird aus der Liste entfernt |

Jede Sprache muss ein Locale in `share/locale` haben. Andernfalls hält der Schritt den Durchlauf mit „Step
LanguageOptions failed:“ und dem Grund an, der auch in `var/log/setup.log` steht („kickstart.ini [language_options]:
The primary language ... is not a language this installation has a locale for (share/locale).“). Die Site wird
immer als UTF-8 installiert.

**`[site_types]`**

| Schlüssel | Typ | Standard | Wirkung |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Site_package` | Text | **erforderlich** | der Bezeichner des Site-Pakets, z. B. `sevenx_multisite` |

So wird das Paket gefunden: Der entfernte Index aus `settings/package.ini [RepositorySettings] RemotePackagesIndexURL`
wird gelesen, und sein Eintrag für das Paket hat Vorrang. Ein Paket, das unter `var/storage/packages/` **bereits
importiert** ist, wird so verwendet, wie es ist; eines, das es nicht ist, wird heruntergeladen und importiert, gefolgt
von den benötigten Paketen, die fehlen oder älter als ihre `min-version` sind. Lässt sich der Index nicht lesen, wird
ein Paket, das bereits im lokalen Repository liegt, verwendet, sofern alle seine Voraussetzungen importiert sind (eine
Offline-Installation). Um eine frische Kopie eines Pakets zu erzwingen, entfernen Sie zuerst sein importiertes
Verzeichnis.

**`[site_access]`**

| Schlüssel | Typ | Standard | Wirkung |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Access` | Auswahl: `url`, `port`, `hostname` | | wie Anfragen den Siteaccesses zugeordnet werden; geschrieben als `MatchOrder=uri`, `port` oder `host` |

**`[site_details]`**

| Schlüssel | Typ | Standard, wenn er fehlt | Wirkung |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Title` | Text | die Zusammenfassung des Site-Pakets | `[SiteSettings] SiteName` |
| `URL` | Text | `http://<host><path>`; auf der Kommandozeile `http://localhost` | die Adresse der Site; `SiteURL` wird ohne Schema geschrieben. Setzen Sie ihn immer |
| `OrganisationName` | Text | leer (der Name der Site wird verwendet) | Absender der optionalen E-Mails (`mailpreferences.ini [FooterSettings]`) |
| `OrganisationAddress` | Text | leer | die Postanschrift des Absenders; `\n` trennt Zeilen |
| `Access` | Text | der Bezeichner des Pakets | mit `Access=url`: der URL-Pfad des Siteaccess `site` |
| `AdminAccess` | Text | *Bezeichner*`_admin` | mit `url`: der Pfad von `admin` |
| `EditorAccess` | Text | `editor` (`<Access>_editor`, wenn `editor` belegt ist) | mit `url`: der Pfad von `editor` |
| `AccessPort` | int | `8080` | mit `port`: der Port von `site` |
| `AdminAccessPort` | int | der nächste freie aus `8080`, `8081`, ... | mit `port`: der Port von `admin` |
| `EditorAccessPort` | int | der Port der Site + 2 (`8082` bei `8080`), der nächste freie Port, wenn dieser der des Admins ist | mit `port`: der Port von `editor`; niemals der der Site oder des Admins |
| `AccessHostname` | Text | *Bezeichner*`.`*Host* | mit `hostname`: der Host von `site` |
| `AdminAccessHostname` | Text | *Bezeichner*`-admin.`*Host* | mit `hostname`: der Host von `admin` |
| `EditorAccessHostname` | Text | `edit.`*der Host der Site ohne* `www.` (`edit.example.com` bei `www.example.com`) | mit `hostname`: der Host von `editor` |
| `Database` | Text | **erforderlich** | die Datenbank, die die Site verwendet (SQLite: die Datei). Geben Sie denselben Wert wie bei `[database_init] Database` an |
| `DatabaseAction` | Auswahl: `remove`, `ignore`, `skip` | Verhalten von `ignore` | was mit vorhandenen Daten geschehen soll, [6.5](#65-databaseaction-lesen-sie-dies-bevor-sie-run-ausführen) |

Nur die Schlüssel der gewählten Zugriffsart werden verwendet. Der Standardport des Admins zählt nur dann ab 8080
hoch, wenn Sie ihn weglassen: Mit `AccessPort=9000` und sonst nichts erhält der Admin 8080 und die Redaktion 9002. Geben
Sie alle drei an, um sicherzugehen.

Die **Verzeichnisse** der Siteaccesses sind immer `site`, `admin` und `editor`, dazu `adminui`, wenn die Extension
`exp_adminui` in der Installation vorhanden ist (sie wird nur über den Pfad `/adminui` erreicht und hat daher hier
keinen Schlüssel; siehe [Exponential Admin UI](../../eng-us/features/6.0/exp-adminui.md)). `Access`, `AdminAccess` und
`EditorAccess` sind die Werte, die sie auswählen, nicht ihre Namen. Anders als der Browser-Assistent lehnt die
Kommandozeile `admin` als Pfad nicht ab, sodass `AdminAccess=admin` das gewohnte `/admin` ergibt.

**`[site_admin]`**

| Schlüssel | Typ | Standard | Wirkung |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `FirstName` | Text | `Administrator` | |
| `LastName` | Text | `User` | |
| `Email` | Text | keiner | die E-Mail-Adresse des Administrators und `[MailSettings] AdminEmail` |
| `Password` | geheim | erzeugt | das Passwort des Benutzers `admin` |

**Ein leeres, bekanntes oder kurzes Passwort wird nie installiert.** Ist `Password` leer oder fehlt, ist es kürzer als
`site.ini [UserSettings] MinPasswordLength` (10 im Auslieferungszustand) oder ein bekanntes oder häufiges Passwort (die
gemeinsame Liste von `expWeakPasswordList`: `publish`, `admin`, `password`, `changeme`, `secret`, `exponential`,
`demo`, `qwerty`, `123456` und viele mehr, mit ihren einfachen Varianten wie anderer Groß- und Kleinschreibung, Ziffern
oder `!` am Ende, Buchstabenersetzungen wie `p@ssw0rd` und Endungen wie `-ing` oder `-er`; erweiterbar mit
`[UserSettings] WeakPasswordList[]` und `WeakPasswordFile`), erzeugt der Schritt ein zufälliges Passwort aus 20
Zeichen, gibt es einmal aus:

```text
The administrator password was not set, is a well-known one or is shorter than 10 characters; generated a random one.
  admin password: <the password>
  (also written to var/log/initial-admin-password - change the password after the first login and delete that file)
```

und schreibt `var/log/initial-admin-password` mit dem Modus `0600`. Melden Sie sich an, ändern Sie das Passwort und
löschen Sie die Datei. Es gelten dieselben Regeln wie bei `exp:install` ([Kapitel 7](07-installation-ueber-die-konsole.md)); vor
6.0.15 wurde ein kurzes Passwort aus `kickstart.ini` so installiert, wie es war.

**Nur für öffentliche Demos:** Mit der Umgebungsvariablen `EXP_DEMO_MODE=1` (`EXP_DEMO_MODE=1 ./console exp:kickstarter
run`) wird ein bekanntes oder kurzes Passwort (`publish`) so installiert, wie es ist, damit eine öffentliche Demo das
Passwort erhält, das sie veröffentlicht; ein leeres wird weiterhin ersetzt. Der Kindprozess des Durchlaufs erbt die
Variable. Die Site muss die Variable danach für ihr PHP behalten, sonst kann ihr Administratorpasswort wieder geändert
werden: [Demo-Modus](../../eng-us/bc/6.0/demo-mode.md). Setzen Sie sie niemals für eine echte Site.

**`[security]`**

| Schlüssel | Typ | Standard | Wirkung |
|---|---|---|---|
| `Continue` | bool | `false` | der Schritt hat keine Werte; `true` überspringt seine Hinweisseite im Browser-Assistenten |

**`[registration]`**

| Schlüssel | Typ | Standard | Wirkung |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Send` | bool | `false` | `true` sendet über den konfigurierten Mail-Transport einen Registrierungsbericht über die Installation (System-, PHP- und Datenbankangaben), aber nur an `settings/setup.ini [RegistrationSettings] Receiver`, das standardmäßig leer ist: Ohne Empfänger wird nichts gesendet. Die alte Upstream-Adresse für die Registrierung wird nie verwendet |
| `UserData[...]` | Text | leer | die Angaben, die mit diesem Bericht gesendet werden |
| `Comments` | Text | | in `kickstart.ini-dist` aufgeführt, von dieser Version nicht gelesen |

### 6.4.3 Die Vorlage: kickstart.ini-dist

`kickstart.ini-dist` im Installationsverzeichnis dokumentiert jeden Abschnitt mit auskommentierten Beispielen
(`#[section]`, `#Key=value`) und einer Beschreibung `## ...` über jedem Schlüssel; diese Datei liest `ini`.
`kickstart.ini--example` ist eine vollständige, erzeugte Datei für eine Installation mit Zuordnung über den Host,
nützlich zum Vergleich mit Ihrer eigenen. Keine der beiden wird von `run` gelesen.

## 6.5 DatabaseAction: Lesen Sie dies, bevor Sie run ausführen

> **Warnung: `DatabaseAction=remove` vernichtet Daten.** Die Aktion leert die in `[site_details] Database` genannte
> Datenbank und installiert in sie hinein. Bei MySQL, PostgreSQL und den anderen Servern wird zuerst jede
> Kernel-Tabelle gelöscht; bei SQLite wird die ganze Datei geleert. **Alles in dieser Datenbank geht verloren, und
> nichts fragt Sie noch einmal**: `--force` ist die einzige Bestätigung. Lassen Sie `Database` auf eine Datenbank
> zeigen, die nichts enthält, was Sie brauchen, prüfen Sie das zweimal und legen Sie vorher eine Sicherung an.
> `exp:kickstarter ini --from-installed` füllt `Database` aus den Einstellungen einer vorhandenen Installation
> ([6.2.1](#621-ini-kickstartini-schreiben)).

| Wert | Wirkung auf die Datenbank | Wirkung auf die Einstellungen | Verwenden für |
|---|---|---|---|
| `remove` | vorhandene Tabellen werden gelöscht (SQLite: Datei geleert), dann werden Schema, Basisdaten und Pakete installiert | geschrieben | eine saubere Installation, eine Neuinstallation |
| `ignore` | Schema, Daten und Pakete werden zum Vorhandenen hinzugefügt; vorhandene Tabellen werden übersprungen, nichts wird gelöscht | geschrieben | eine neue, leere Datenbank (der Standard von `ini --yes`) |
| `skip` | nichts wird eingefügt; der Administrator bleibt unberührt | geschrieben | das Neuerzeugen von `settings/siteaccess/` und `settings/override/` für eine Datenbank, die bereits vollständig ist |

Ein fehlender oder falsch geschriebener Wert verhält sich wie `ignore`: Die Daten werden zum Vorhandenen hinzugefügt,
was auf einer nicht leeren Datenbank mit doppelten Tabellen oder Zeilen fehlschlägt. Schreiben Sie den Wert
ausdrücklich hin.

Sichern Sie vor einem `remove`, und zwar in ein Verzeichnis außerhalb des Installationsverzeichnisses (ein Dump im
Document-Root kann von jedem heruntergeladen werden, der seinen Namen errät):

```bash
mkdir -p /var/backups/exponential
# MySQL / MariaDB (MariaDB also calls the program mariadb-dump)
mysqldump --single-transaction --routines exponential > /var/backups/exponential/db-$(date +%Y%m%d-%H%M).sql
# PostgreSQL
pg_dump --format=custom --file=/var/backups/exponential/db-$(date +%Y%m%d-%H%M).dump exponential
# SQLite (the online backup gives a consistent copy even while the site writes)
sqlite3 var/storage/sqlite3/exponential.db ".backup '/var/backups/exponential/db.sqlite'"
```

Prüfen Sie, dass die Sicherung nicht leer ist (`ls -l /var/backups/exponential/`), bevor Sie die Installation
ausführen; ein Dump, der mangels Rechten fehlgeschlagen ist, hinterlässt eine Datei von wenigen Bytes.

## 6.6 Kommentierte Beispieldateien

Jedes Beispiel ist eine vollständige `kickstart.ini` für `run --force`. Zeilen, die mit `;` beginnen, sind Kommentare
und können in der Datei bleiben; jeder steht in einer eigenen Zeile, weil ein Kommentar hinter einem Wert Teil des
Werts würde.

### 6.6.1 SQLite

```ini
; kickstart.ini - SQLite, URL matching (/site, /admin, /editor)

[email_settings]
Continue=true
; local sendmail/MTA
Type=mta

[database_choice]
Continue=true
Type=sqlite3

[database_init]
Continue=true
; a file needs no server, port, user or password
Server=
Port=
; var/storage/sqlite3/exponential.db
Database=exponential.db
User=
Password=
Socket=

[language_options]
Continue=true
; the language of the bundled content
Primary=eng-US
; optional additional language
Languages[]=ger-DE

[site_types]
Continue=true
Site_package=sevenx_multisite

[site_access]
Continue=true
Access=url

[site_details]
Continue=true
Title=My Exponential site
URL=https://www.example.com
; https://www.example.com/site   -> siteaccess site
Access=site
; https://www.example.com/admin  -> siteaccess admin
AdminAccess=admin
; https://www.example.com/editor -> siteaccess editor
EditorAccess=editor
; same as [database_init] Database
Database=exponential.db
; empties the file if it exists
DatabaseAction=remove

[site_admin]
Continue=true
FirstName=Site
LastName=Administrator
Email=webmaster@example.com
; empty: generated, printed once, var/log/initial-admin-password
Password=

[security]
Continue=true

[registration]
Continue=true
; always
Send=false
```

Das Verzeichnis `var/storage/sqlite3/` muss für den Benutzer, der den Kickstarter ausführt, und für den Webserver
beschreibbar sein; die Datenbank führt daneben `-wal`- und `-shm`-Dateien. SQLite im Produktivbetrieb wird in
[Kapitel 9](09-datenbanken.md) behandelt.

### 6.6.2 MySQL oder MariaDB

Legen Sie zuerst die Datenbank und ihren Benutzer an, mit UTF-8:

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'exponential'@'localhost' IDENTIFIED BY 'a-long-random-password';
GRANT ALL PRIVILEGES ON exponential.* TO 'exponential'@'localhost';
```

```ini
; kickstart.ini - MySQL/MariaDB, host name matching

[email_settings]
Continue=true
Type=smtp
Server=smtp.example.com
User=mailer@example.com
; masked in kickstart.log
Password=CHANGE_ME_SMTP

[database_choice]
Continue=true
; "mysql" is accepted as well
Type=mysqli

[database_init]
Continue=true
Server=127.0.0.1
Port=3306
; must exist; no SHOW DATABASES privilege needed
Database=exponential
User=exponential
Password=CHANGE_ME_DB
; or /var/lib/mysql/mysql.sock instead of host and port
Socket=

[language_options]
Continue=true
Primary=eng-US

[site_types]
Continue=true
Site_package=sevenx_multisite

[site_access]
Continue=true
Access=hostname

[site_details]
Continue=true
Title=Example
URL=https://www.example.com
OrganisationName=Example Ltd
OrganisationAddress=1 Example Street\n12345 Example City
AccessHostname=www.example.com
AdminAccessHostname=admin.example.com
; also the default: edit.<AccessHostname without www.>
EditorAccessHostname=edit.example.com
Database=exponential
; DROPS every kernel table in `exponential`
DatabaseAction=remove

[site_admin]
Continue=true
FirstName=Site
LastName=Administrator
Email=webmaster@example.com
Password=

[security]
Continue=true

[registration]
Continue=true
Send=false
```

Unterstützt der Server InnoDB, werden die Tabellen als InnoDB angelegt. Der Verbindungszeichensatz, der in die
Siteaccess-Einstellungen geschrieben wird, ist `utf-8`.

### 6.6.3 PostgreSQL

Die Datenbank muss existieren, und die Extension `pgcrypto` muss darin verfügbar sein (sie stellt `digest()` bereit):

```sql
CREATE ROLE exponential LOGIN PASSWORD 'a-long-random-password';
CREATE DATABASE exponential OWNER exponential ENCODING 'UTF8';
\c exponential
CREATE EXTENSION IF NOT EXISTS pgcrypto;
```

```ini
; kickstart.ini - PostgreSQL, port matching (8080 site, 8081 admin, 8082 editor)

[email_settings]
Continue=true
Type=mta

[database_choice]
Continue=true
; "postgresql" is accepted as well
Type=pgsql

[database_init]
Continue=true
Server=db.internal
Port=5432
; the login is tested against this database
Database=exponential
User=exponential
Password=CHANGE_ME_DB
Socket=

[language_options]
Continue=true
Primary=eng-GB
; dropped if it is the primary; eng-US content is kept anyway
Languages[]=eng-US

[site_types]
Continue=true
Site_package=sevenx_multisite

[site_access]
Continue=true
Access=port

[site_details]
Continue=true
Title=Intranet
URL=http://intranet.example.com
AccessPort=8080
AdminAccessPort=8081
; also the default: AccessPort + 2
EditorAccessPort=8082
Database=exponential
DatabaseAction=remove

[site_admin]
Continue=true
FirstName=Site
LastName=Administrator
Email=it@example.com
Password=

[security]
Continue=true

[registration]
Continue=true
Send=false
```

Kann der Installer `pgcrypto` nicht selbst anlegen, schlägt `DatabaseInit` mit der Meldung über die Funktion `digest`
fehl, die in [Abschnitt 5.3.6](05-setup-assistent.md#536-initialisierung-der-datenbank) zitiert ist. MongoDB und Oracle
verwenden dieselben Abschnitte mit `Type=mongodb` oder `Type=oci8`; ihre Besonderheiten stehen in
[Kapitel 9](09-datenbanken.md).

## 6.7 Probeläufe

```bash
php bin/php/console exp:kickstarter run --dry-run
```

Ein Probelauf:

1. gibt „Dry-run: validating kickstart.ini“, die gefundenen Abschnitte und die Schritttabelle aus; eine Datei ohne
   Abschnitte hält hier mit „No groups found in kickstart.ini“ und Status 1 an;
2. führt `DatabaseChoice` bis `SiteDetails` mit den Werten der Datei aus, die Datenbank muss also erreichbar sein;
3. lädt in `SiteTypes` das Site-Paket und seine Voraussetzungen in ein temporäres Repository
   `var/storage/packages/dryrun/` herunter, das entfernt wird, sobald der Schritt fertig ist (und zu Beginn jedes
   Durchlaufs);
4. hält nach `SiteDetails` an und gibt „Dry-run completed: database and remote packages verified. Stopped after
   SiteDetails, nothing written.“ aus.

Er schaltet den Wartungsmodus nicht ein und schreibt weder die Datenbank noch die Einstellungen. `SiteAdmin` und
`Registration` laufen nicht, also wird kein `var/log/initial-admin-password` geschrieben und keine E-Mail gesendet. Der
Durchlauf wird in `var/log/setup.log` und `var/log/kickstart.log` protokolliert.

## 6.8 Wartungsmodus während eines Durchlaufs

Während ein echter Durchlauf arbeitet, beantwortet die Site jede Webanfrage mit der Wartungsseite (HTTP 503, Titel
„The site is being set up“), sodass kein Besucher auf die halb aufgebaute Datenbank trifft ([Kapitel 4](04-installationsweg-waehlen.md#44-was-eine-installation-schreibt)).
Der Durchlauf gibt zu Beginn „Maintenance mode on: the site shows the maintenance page until the installation is
done.“ und am Ende „Maintenance mode off: the site answers again.“ aus.

Nach einem fehlgeschlagenen Schritt **bleibt** die Site im Wartungsmodus, und der Durchlauf meldet: „The site stays in
maintenance mode. After fixing the cause: run the kickstarter again, or php bin/php/maintenance.php off“.

Ein bereits geöffnetes Wartungsfenster (`exp:maintenance on` oder eine Markierung eines anderen Durchlaufs) bleibt
genau so, wie es ist: Der Durchlauf ersetzt `var/maintenance.json` nicht, meldet „Maintenance mode was already on: kept
as it is, and left on after the installation (php bin/php/maintenance.php off ends it).“ und lässt das Fenster am Ende
offen. Beenden Sie es selbst, wenn Sie fertig sind.

```bash
php bin/php/maintenance.php status       # or: php bin/php/console exp:maintenance status
php bin/php/maintenance.php off
```

## 6.9 Logs

| Datei | Inhalt |
|---|---|
| `var/log/kickstart.log` | alles, was der Durchlauf auf stdout und stderr ausgegeben hat, mit den Passwörtern aus `kickstart.ini` als `********` maskiert, einem Kopf mit Datum, Verzeichnis und Befehl und dem Exit-Status am Ende. Frühere Durchläufe: `kickstart.log.1` (der letzte) bis `kickstart.log.9`. `EXP_KICKSTART_LOG=0` schaltet es ab |
| `var/log/setup.log` | das lesbare Protokoll des Durchlaufs: Umgebung, jeder Schritt mit seiner Dauer, Fehler und Warnungen (mit einem Hinweis neben bekannten Problemen), die Gesundheitsprüfungen nach einer Installation und eine Zeile RESULT/NEXT. Frühere Durchläufe rotieren nach `setup.log.1` ... |
| `var/log/error.log` | Einträge `BEGIN` und `END` rund um den Durchlauf; während des Durchlaufs geschriebene Einträge enden mit dem Durchlauf und dem Schritt, aus dem sie stammen |
| `var/log/initial-admin-password` | nur wenn ein Passwort erzeugt wurde |

```bash
tail -n 40 var/log/setup.log
grep -n "FAIL\|ERROR" var/log/setup.log
```

## 6.10 Erneut ausführen und fortsetzen

**Erneut ausführen für eine identische Installation.** Dieselbe `kickstart.ini` mit `DatabaseAction=remove` und
`run --force` ersetzt die Datenbank und die Einstellungen erneut. Verwenden Sie das, um eine Test- oder Schulungssite
zurückzusetzen.

**Nach einem Fehler die ganze Abfolge erneut ausführen.** Beheben Sie die Ursache (der fehlgeschlagene Schritt und
seine Meldung stehen in der Ausgabe und in `var/log/setup.log`), dann:

```bash
php bin/php/console exp:kickstarter run --dry-run     # optional: check up to SiteDetails
php bin/php/console exp:kickstarter run --force
```

Die Schritte reichen ihre Ergebnisse innerhalb eines Prozesses an die folgenden weiter: `DatabaseChoice` setzt den
Datenbanktyp, den `DatabaseInit` und `SiteDetails` verwenden, `SiteTypes` das Paket, mit dem `SiteAccess` und
`SiteDetails` arbeiten, und `SystemCheck` das, was `CreateSites` über ImageMagick erfährt. Ein Durchlauf, der mit
`--start-step` bei einem späteren Schritt beginnt, hätte diese Ergebnisse nicht; deshalb lehnt der Kickstarter einen
solchen Start ab, bevor irgendetwas läuft, und nennt den frühesten Schritt, der funktioniert, zum Beispiel
„--start-step=SiteDetails cannot work: the steps SiteDetails..Final need what Welcome, ... found out earlier in the
same run (...). Start at Welcome or earlier: --start-step=Welcome.“ Jeder Bereich, der installiert, beginnt bei
`Welcome`, dem Standard. `--start-step` bleibt der Diagnose der frühen Schritte vorbehalten (zum Beispiel
`--start-step=DatabaseChoice --stop-step=DatabaseInit`, um nur die Datenbankverbindung zu testen). Mit
`DatabaseAction=remove` ersetzt ein vollständiger zweiter Durchlauf alles, was der fehlgeschlagene hinterlassen hat.

**Früh anhalten.** `--stop-step=<Step>` endet nach diesem Schritt. Mit einem Endschritt vor `CreateSites` (zum
Beispiel `--stop-step=SiteDetails`) wird nichts installiert, und `--force` ist nicht nötig: Der Durchlauf prüft die
Konfiguration bis zu diesem Punkt, und die Zusammenfassung im Setup-Log meldet „stopped after SiteDetails (nothing
installed)“. Anders als ein Probelauf schaltet ein solcher Durchlauf den Wartungsmodus ein, solange er arbeitet.

**Nur die Einstellungen neu erzeugen.** `DatabaseAction=skip` und `run --force` schreiben `settings/siteaccess/` und
`settings/override/` aus der Datei neu und lassen die Datenbank unberührt.

**Datei bearbeitet und alte Werte gesehen?** `ini` und `run` löschen `var/cache/ini/kickstart-*.php` selbst, weil
`config.php` die Prüfung der Änderungszeit von INI-Dateien abschaltet. Der Browser-Assistent tut das nicht; löschen Sie
diese Dateien, nachdem Sie `kickstart.ini` von Hand bearbeitet haben, wenn der Assistent sie lesen soll.

**Wechsel der Datenbank-Engine.** Die Einstellungen in `settings/override/` haben Vorrang vor denen eines Siteaccess.
Wenn Sie auf eine andere Engine installieren als die, die das Verzeichnis vorher enthielt, setzen Sie zuerst
`[DatabaseSettings] DatabaseImplementation` in `settings/override/site.ini.append.php` auf den neuen Treiber (oder
entfernen Sie den alten Wert), sonst spricht die Site weiter das Protokoll der alten Engine.

**Nach der Installation.** Verschieben Sie `kickstart.ini` aus dem Installationsverzeichnis heraus oder löschen Sie
sie: Sie enthält Passwörter, und ein später gestarteter Browser-Assistent würde sie lesen.

## 6.11 In CI und Containern

Der Kickstarter eignet sich gut für die Automatisierung, wenn die Konfiguration am besten als geprüfte Datei
aufbewahrt wird:

1. Halten Sie eine Vorlage, z. B. `deploy/kickstart.ini.template`, mit Platzhaltern für Geheimnisse unter
   Versionskontrolle.
2. Erzeugen Sie daraus bei der Bereitstellung die Datei im Installationsverzeichnis, mit dem Modus `0600`.
3. Führen Sie einen Probelauf aus, dann die Installation, und lassen Sie den Job bei einem Status ungleich null
   fehlschlagen:

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /srv/exponential
umask 077
envsubst < deploy/kickstart.ini.template > kickstart.ini
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --force
rm -f kickstart.ini
```

Was Sie beachten sollten:

- `run` braucht kein Terminal; `ini` braucht eines, sofern nicht `--yes` oder `--defaults` angegeben ist.
- Der Durchlauf dauert einige Minuten; geben Sie dem Job genug Zeit. Der Kindprozess behält `memory_limit` und
  `max_execution_time` des Elternprozesses.
- `var/log/kickstart.log` und `var/log/setup.log` sind die Artefakte, die Sie von einem fehlgeschlagenen Job aufbewahren
  sollten.
- Führen Sie die Installation in einem Container einmal aus (zum Beispiel, wenn
  `settings/override/site.ini.append.php` keinen Abschnitt `[DatabaseSettings]` hat), nicht bei jedem Start.
- Für Installationen ohne Datei baut `exp:install` ([Kapitel 7](07-installation-ueber-die-konsole.md)) dieselbe Konfiguration aus
  Optionen auf.

## 6.12 Hinweise zu den anderen Dokumenten über den Kickstarter

Die früheren Dokumente [Kickstarter CLI](../../eng-us/bc/6.0/kickstartercli.md) und
[Kickstarter: eine ganze Site aus einer Datei installieren](../../eng-us/features/6.0/kickstarter-cli.md) bleiben nützlicher
Hintergrund. Wo sie von diesem Kapitel abweichen, folgt dieses Kapitel dem Code dieser Version:

- **Paket-Download.** Der Kickstarter bevorzugt den Eintrag des entfernten Index, verwendet aber ein bereits
  importiertes Paket und lädt nur herunter, was fehlt ([6.4.2](#642-abschnitte-und-schlüssel), `[site_types]`). Ein
  Probelauf lädt immer herunter, und zwar in das temporäre Repository `dryrun`.
  [Kickstarter CLI](../../eng-us/bc/6.0/kickstartercli.md) sagt inzwischen dasselbe.
- **`--stop-step=CreateSites`.** Hält nicht an, bevor die Datenbank geschrieben wird: `CreateSites` ist der Schritt,
  der sie schreibt. Halten Sie bei `Registration` oder früher an, um die Datenbank unberührt zu lassen.
- **`[registration] Comments`.** Wird nicht gelesen; `Send` ist standardmäßig `false`.
- **Fortsetzen mit `--start-step=SiteDetails`.** Ältere Dokumente schlugen vor, einen fehlgeschlagenen Durchlauf dort
  fortzusetzen. Ein solcher Start wird abgelehnt, weil die Schritte davor Werte setzen, die `SiteDetails` und
  `CreateSites` im selben Prozess lesen; führen Sie die ganze Abfolge erneut aus
  ([6.10](#610-erneut-ausführen-und-fortsetzen)). [Kickstarter CLI](../../eng-us/bc/6.0/kickstartercli.md) und
  [Exponential 6.0 installieren](../../eng-us/INSTALL.md) sagen inzwischen dasselbe.
- **`ini --yes`.** Bis zum 5. Oktober 2026 schrieb es `Database=ezp`, was SQLite als Dateinamen ablehnt, und
  `DatabaseAction=skip`, was nichts installiert, und zwar mit dem normalen Dateimodus des Prozesses. Jetzt schreibt es
  `exponential.db`, `ignore` und den Modus `0600` und markiert die zu prüfenden Werte mit `# REVIEW`
  ([6.2.1](#621-ini-kickstartini-schreiben)).

## Quellen

In diesem Repository:

- [Exponential 6.0 installieren](../../eng-us/INSTALL.md), Abschnitt 6
- [Kickstarter CLI](../../eng-us/bc/6.0/kickstartercli.md) und [Kickstarter: eine ganze Site aus einer Datei installieren](../../eng-us/features/6.0/kickstarter-cli.md)
- [Exponential mit einem Befehl installieren](../../eng-us/features/6.0/install-in-one-command.md)
- [Wartungsmodus](../../eng-us/features/6.0/maintenance-mode.md)
- [Installer-Logs und Startdaten](../spezifikationen/6.0/installer-protokolle-und-startdaten.md)
- [Standardwerte einer sauberen Installation](../../eng-us/features/6.0/clean-install-defaults.md)
- [SQLite-Datenbank](../../eng-us/features/6.0/sqlite-database.md), [SQLite-Installer](../spezifikationen/6.0/platform-sqlite-installer.md),
  [Datenbanktreiber, September 2026](../spezifikationen/6.0/datenbanktreiber-2026-09.md)
- [Die Konsole](../../eng-us/bc/6.0/console.md) und [Abstraktionen für CLI, Cronjobs und Views](../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)
  (warum `bin/php/kickstarter.php` ein einziger Aufruf von `kernel/private/classes/commands/kickstarter.php` ist)
- [Bereitstellung](../anleitungen/bereitstellung.md)
- Code: `kernel/private/classes/commands/kickstarter.php`, `kernel/classes/expkickstarter.php`,
  `kernel/classes/expkickstarterini.php`, `kernel/setup/steps/`, `kickstart.ini-dist`, `kickstart.ini--example`

Extern:

- PHP `proc_open()`: <https://www.php.net/manual/en/function.proc-open.php>
- PHP `posix_isatty()`: <https://www.php.net/manual/en/function.posix-isatty.php>
- MySQL `mysqldump`: <https://dev.mysql.com/doc/refman/8.4/en/mysqldump.html>
- MariaDB `mariadb-dump`: <https://mariadb.com/docs/server/clients-and-utilities/backup-restore-and-import-clients/mariadb-dump>
- PostgreSQL `pg_dump`: <https://www.postgresql.org/docs/current/app-pgdump.html>
- PostgreSQL pgcrypto: <https://www.postgresql.org/docs/current/pgcrypto.html>
- SQLite-Online-Backup: <https://www.sqlite.org/backup.html> und der Befehl `.backup` der Shell:
  <https://www.sqlite.org/cli.html>
- GNU `envsubst`: <https://www.gnu.org/software/gettext/manual/html_node/envsubst-Invocation.html>
- Exponential auf GitHub: <https://github.com/se7enxweb/exponential>

[Inhalt](README.md) · Zurück: [5. Der Setup-Assistent](05-setup-assistent.md) · Weiter: [7. Die Installation über die Konsole](07-installation-ueber-die-konsole.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
