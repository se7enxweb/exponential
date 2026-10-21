# Exponential mit einem Befehl installieren

Diese Seite richtet sich an alle, die in wenigen Minuten eine funktionierende Exponential-Site möchten, ohne eine
`kickstart.ini` zu schreiben und ohne Datenbankserver.

```bash
./console exp:install
```

Das installiert das Multisite-Paket auf **SQLite**, in `eng-US`, mit den
Siteaccesses `site` und `admin`, die über die URL erreicht werden, und gibt eine Zusammenfassung mit den
Anmeldeadressen von Site und Administrationsoberfläche, dem Benutzernamen, der E-Mail-Adresse, Datenbank,
Paket und Siteaccesses aus, dazu einen kurzen Block, den Sie kopieren und einfügen können. `exp:install`
ist dasselbe wie `php bin/php/install.php`.

## Eine Datenbank wählen

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
> zusätzlichem Aufwand möglich. Einzelheiten: [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md).

| `--db=` | Aliasse | Standardport | Standardname der Datenbank |
|---|---|---|---|
| `sqlite` (Standard) | `sqlite3` | keiner | `exponential.db` |
| `mysql` | `mysqli`, `mariadb` | 3306 | `exponential` (Benutzer `root`) |
| `pgsql` | `postgresql`, `postgres` | 5432 | `exponential` (Benutzer `postgres`) |
| `mongodb` | `mongo` | 27017 | `exponential` |
| `oracle` | `oci8`, `ezoracle` | 1521 | Service `FREEPDB1` |

Für Oracle wird der Connect-String als `host:port/service` zusammengesetzt; ein vollständiger Connect-String
oder ein `@alias` (TNS) wird so verwendet, wie er angegeben ist. Der Oracle-Treiber liegt in der
Extension `ezoracle`; der Installer lädt für den Lauf die Einstellungen dieser Extension,
setzt sie in den `ActiveExtensions` der neuen Site an die erste Stelle und spricht während der Prüfungen
UTF-8 mit Oracle.

```bash
./console exp:install --db=mysql --db-host=127.0.0.1 --db-name=mysite \
                      --db-user=root --db-action=remove
EXP_INSTALL_DB_PASSWORD='...' ./console exp:install --db=pgsql
```

Übergeben Sie das Datenbankpasswort mit `--db-password` oder, besser, mit der
Umgebungsvariablen `EXP_INSTALL_DB_PASSWORD`, die es aus der
Prozessliste und der Shell-Historie heraushält.

## Alle Optionen

| Gruppe | Optionen |
|---|---|
| Datenbank | `--db`, `--db-host`, `--db-port`, `--db-name`, `--db-user`, `--db-password`, `--db-socket`, `--db-action` |
| Site | `--package`, `--language`, `--languages`, `--title`, `--url`, `--access` (`url`, `host` oder `port`), `--site-access`, `--admin-access`, `--host`, `--admin-host`, `--port`, `--admin-port` |
| Administrator | `--email`, `--password`, `--random-password`, `--first-name`, `--last-name` |
| Steuerung | `--force`, `--dry-run`, `--print` |

- Ohne Optionen ist der Administrator `admin` mit einem generierten Passwort und
  der E-Mail-Adresse `nospam@exponential.earth` (aus `./console exp:install --help`).
- Ein generiertes Passwort (der Standard oder `--random-password`) hat 24 Zeichen.
  Es wird in der Zusammenfassung ausgegeben, mit einem Hinweis, dass und warum es generiert wurde, und
  nach `var/log/initial-admin-password` geschrieben (nur für den Eigentümer lesbar).
- `--password=<pass>` wird so übernommen, wie es angegeben ist, wenn es mindestens 10 Zeichen hat
  (`MinPasswordLength`) und kein allgemein bekanntes wie `publish` oder
  `admin` ist. Andernfalls ersetzt es ein generiertes. Die Zusammenfassung zeigt immer das
  Passwort, das die Installation tatsächlich gesetzt hat: Wenn das Setup ein Passwort, das es ablehnt, ersetzt,
  liest die Zusammenfassung dasjenige, das es in `var/log/initial-admin-password`
  festgehalten hat.
- Der Befehl weigert sich, über eine bestehende Installation zu laufen, es sei denn, Sie fügen `--force` hinzu.
- `--dry-run` prüft die Konfiguration und die Pakete und installiert nichts.
- `--print` zeigt die Konfiguration, die er verwenden würde, mit maskierten Passwörtern.
- Eine bereits vorhandene `kickstart.ini` wird für den Lauf beiseitegelegt und danach
  zurückgelegt. Die verwendete Konfiguration wird mit maskierten Passwörtern in `var/log`
  aufbewahrt.

## Sicherere erste Anmeldung

Eine Kickstart-Installation endet nicht mehr mit einem erratbaren Administratorpasswort.
Wenn die Kickstart-Datei kein Passwort setzt oder eines der allgemein bekannten
Standardpasswörter, generiert das Setup ein zufälliges Passwort mit 20 Zeichen, gibt es einmal auf der
Konsole aus und schreibt es nach `var/log/initial-admin-password` (nur für den Eigentümer
lesbar). Ändern Sie es nach der ersten Anmeldung und löschen Sie die Datei. Das Passwort wird
auch dann gesetzt, wenn keine E-Mail-Adresse des Administrators angegeben ist; die mitgelieferten Beispiel- und
dist-Kickstart-Dateien enthalten kein Passwort mehr.

## Was die Logs Ihnen sagen

| Datei | Inhalt | Einstellungen |
|---|---|---|
| `var/log/setup.log` | Eine Datei pro Lauf des Kickstarters oder des Web-Assistenten: Einleitung, Umgebung, jeder Schritt mit seiner Dauer und den Fehlern und Warnungen, die er verursacht hat (Wiederholungen gruppiert mit Anzahl, erstem und letztem Zeitpunkt, ein Hinweis neben bekannten Problemen), Gesundheitsprüfungen nach der Installation (Datenbank, deklarierte Tabellen, Inhaltsklassen, Objekte, Inhaltswurzel, Namenslisten, Einstellungen, Siteaccesses, Dateieigentum) und eine eingerahmte Zeile RESULT und NEXT. Frühere Läufe rotieren nach `setup.log.1`, `.2` ... | `EXP_SETUP_LOG_DIR` verlegt es an einen anderen Ort (Tests nutzen das, damit ein echtes Log nicht wegrotiert wird). |
| `var/log/kickstart.log` | Alles, was der Kickstarter ausgegeben hat (stdout und stderr), mit maskierten Passwörtern aus `kickstart.ini` und Startzeit, Befehl und Exit-Status drumherum. Neun frühere Läufe werden als `kickstart.log.1` bis `.9` aufbewahrt. | `EXP_KICKSTART_LOG=0` schaltet es ab. |
| `var/log/error.log` | Während ein Lauf andauert, zeigt es Einträge `BEGIN` und `END` mit der Lauf-ID und dem Ergebnis; jeder Eintrag endet mit dem Lauf und Schritt, aus dem er stammt, und ein mehrmals hintereinander geschriebener Eintrag wird einmal mit seiner Anzahl geschrieben. | |

`setup.log` zählt nur, was die Installation selbst protokolliert hat. Ein Fehler, den ein anderer
Prozess in der Zwischenzeit geschrieben hat (zum Beispiel die Anfrage eines Besuchers, die die Site erreichte,
während ihre Datenbank aufgebaut wurde), wird als "(not this run)" aufgeführt, bei den Zählungen ausgelassen
und in einer Zeile NOTE zusammengefasst.

Eine fehlgeschlagene Abfrage wird jetzt einmal protokolliert, mit dem Grund der Datenbank zuerst und der
Anweisung in einer Zeile. Die Nachinstallation eines Site-Pakets meldet den Schritt, bei dem sie
abgebrochen hat (`eZSiteInstaller::abortedStep()`).

## Geänderte Standardwerte

- **SQLite zuerst.** Der Web-Setup-Assistent führt SQLite zuerst auf und wählt es vor,
  und der interaktive Kickstarter bietet es zuerst an und verwendet es als Standard für den
  `Type` von `[database_choice]`. Der Assistent prüft die SQLite-Datenbankdatei und
  fragt nichts ab, was ein Server braucht. Ein Kickstart-Block `database_init` ohne
  Server, Port, Benutzer, Passwort oder Socket löst keine Warnungen aus.
- **Die mitgelieferte Site ist vorausgewählt**, sodass ein Klick auf Weiter bei jedem Schritt die
  Standard-Site installiert. Früher erhielt, wer jeden Standard akzeptierte, "No site
  package chosen".
- **Hauptsprache `eng-US`** sowohl im Assistenten als auch im Kickstart-Editor,
  die Sprache der mitgelieferten Daten. Andere Hauptsprachen installieren sauber:
  Der Schritt für die Paketsprache bietet die eigene Sprache der sauberen Daten nicht mehr zur
  Zuordnung an, das Anlegen der Sprache ist der Standard für andere Paketsprachen
  (eine Zuordnung beschriftet Inhalte um, ohne sie zu übersetzen), und das Site-Paket
  erhält `primary_language`, `extra_language_codes` und `language_map` über den
  Namen. Siteaccesses, die für zwei Locales einer Sprache angelegt werden, erhalten unterschiedliche Namen
  (`eng` und `eng_gb`). Siehe [Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md).
- **`DatabaseImplementation` wird nicht aus `Type` abgeleitet.** Der Kickstart-`Type`
  wählt den Treiber, mit dem sich der Installer verbindet; die nach
  `settings/override/site.ini.append.php` geschriebene `DatabaseImplementation`
  stammt aus der aktiven `site.ini`, weil ein Treiber mehrere Namen hat und eine Site
  einen eigenen Alias registrieren kann. Wenn Sie auf eine andere Engine installieren, setzen Sie zuerst die
  Implementierung, sonst spricht die neue Site das Protokoll der alten Engine mit dem
  neuen Server.
- **GD zuerst für Bilder.** `settings/image.ini [ImageConverterSettings] ImageConverters[]` führt GD vor
  ImageMagick auf; ImageMagick bleibt der Rückfall für Formate und Filter, die GD fehlen
  (PSD, TIFF, PDF, WebP, flatten, swirl, noise), und wenn GD fehlt.
- **Keine Links auf die Website des früheren Produkts** in der Linkliste einer neuen Installation,
  und `ActiveExtensions` führt jede Extension einmal auf, wobei die `SiteURL` des Site-Pakets
  erhalten bleibt.
- **Der Wartungsmodus** umschließt jeden Lauf: siehe [Wartungsmodus](wartungsmodus.md).

**Öffentliche Demos.** `EXP_DEMO_MODE=1 ./console exp:install --password=publishing` installiert das veröffentlichte Demo-Passwort so, wie es angegeben ist; im Demo-Modus wird ein allgemein bekanntes oder kurzes Administratorpasswort (auch `publish`) beibehalten, ein leeres wird weiterhin abgelehnt. Behalten Sie die Variable für die laufende Site bei. [Demo-Modus](../../bc/6.0/demomodus.md), [Rezept: eine öffentliche Demo auf LiteSpeed und Plesk betreiben](../../hilfe/hosting/server/rezept-eine-oeffentliche-demo-auf-litespeed-und-plesk-betreiben.md).

## Verwandte Seiten

- [Kickstarter: eine ganze Site aus einer Datei installieren](kickstarter-cli.md) (die dateigesteuerte Variante desselben Installers), [Kickstarter auf der Kommandozeile](../../bc/6.0/kickstartercli.md)
- [Das neue Aussehen des Setup-Assistenten und der Siteaccess für Redakteure](einrichtungsassistent-und-editor-siteaccess.md), [Wartungsmodus](wartungsmodus.md)
- [SQLite-Datenbank](sqlite-datenbank.md), [Datenbanktreiber](../../spezifikationen/6.0/datenbanktreiber-sqlite-oracle.md), [Datenbanktreiber und Installer, September 2026](../../spezifikationen/6.0/datenbanktreiber-2026-09.md)
- [Installer-Logs und Startdaten](../../spezifikationen/6.0/installer-protokolle-und-startdaten.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Historie: [16. bis 30. September 2026](../../geschichte/2026/2026-09b.md), [Januar 2024, zweite Hälfte](../../geschichte/2024/2024-01b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
