---
title: Der Kickstarter für Bundles
audience: bundler, hosting, developer
category: bundling
order: 210
summary: Wie ein Bundle den Kickstarter verwendet - kickstart.ini im Wurzelverzeichnis der Installation, die Befehle ini und run mit ihren tatsächlichen Optionen, die Abschnitte der Datei, DatabaseAction, der Registrierungsbericht, den ein Bundle ausgeschaltet lassen sollte, das erzeugte Passwort, erneute Läufe und Aufräumen.
keywords: Kickstarter, Registrierungsbericht, erneuter Lauf, aufräumen, kickstarter, kickstart.ini, kickstart.ini-dist, kickstart.ini--example, exp:kickstarter, kickstarter.php, ini, run, --yes, --defaults, --from-installed, --dry-run, --list-steps, --start-step, --stop-step, --force, DatabaseAction, registration, Send
type: help
---
# Der Kickstarter für Bundles

Diese Seite richtet sich an Bündler, die aus einer geprüften Konfigurationsdatei installieren. Der Kickstarter "führt
die Schrittklassen in einem PHP-Prozess auf der Kommandozeile aus und liest jede Antwort aus `kickstart.ini`"
([Der Kickstarter für Hoster](../hosting/bereitstellung/der-kickstarter-fuer-hoster.md#was-er-ist)). Die vollständige Referenz ist
[Kapitel 6: Der Kickstarter](../../installation/06-kickstarter.md).

## Inhalt

1. [Wo die Datei liegt](#wo-die-datei-liegt)
2. [Befehle und Optionen](#befehle-und-optionen)
3. [Die Abschnitte von kickstart.ini](#die-abschnitte-von-kickstartini)
4. [DatabaseAction](#databaseaction)
5. [Einstellungen, die ein Bundle bewusst setzen sollte](#einstellungen-die-ein-bundle-bewusst-setzen-sollte)
6. [Erneute Läufe und Aufräumen](#erneute-läufe-und-aufräumen)

## Wo die Datei liegt

`kickstart.ini` gehört in das **Wurzelverzeichnis der Installation**, neben `index.php`. Das Repository liefert dort
zwei Vorlagen aus, keine aktive Datei:

| Datei | Rolle |
|---|---|
| `kickstart.ini-dist` | die Vorlage, die `exp:kickstarter ini --defaults` kopiert; "Copy this file as kickstart.ini to the root of your Exponential installation" (ihr eigener Kopf) |
| `kickstart.ini--example` | ein Beispiel, im Juli 2026 umbenannt, damit es nicht für jede Web-Einrichtung gilt ([Kickstarter](../../funktionen/6.0/kickstarter-cli.md#gut-zu-wissen)) |

Liefern Sie nie eine befüllte `kickstart.ini` in einem Bundle aus: "Sie enthält das Datenbankpasswort", und "ein
später gestarteter Browser-Assistent würde sie lesen"
([6.10](../../installation/06-kickstarter.md#610-erneut-ausführen-und-fortsetzen)).

## Befehle und Optionen

Geprüft mit der Hilfe des Befehls unter 6.0.15 (`kernel/private/classes/commands/kickstarter.php`):

| Befehl | Optionen |
|---|---|
| `php bin/php/console exp:kickstarter ini` | `--yes` (eingebaute Standardwerte), `--defaults` (kopiert `kickstart.ini-dist`), `--from-installed` (übernimmt Datenbankserver, Name und Benutzer, nicht das Passwort, eines installierten Siteaccess) |
| `php bin/php/console exp:kickstarter run` | `--dry-run`, `--list-steps`, `--start-step`, `--stop-step`, `--force` |

`bin/php/kickstarter.php` ist dasselbe Programm ohne die Konsole. `--force` "ist immer dann erforderlich, wenn der
Bereich der Schritte `CreateSites` enthält". Ein Aufruf ohne Befehl zeigt die Hilfe und endet mit `0`, "damit ein
Skript, das den Unterbefehl vergessen hat, nicht scheitert; prüfen Sie, dass Ihre Skripte `run` nennen"
([Der Kickstarter für Hoster](../hosting/bereitstellung/der-kickstarter-fuer-hoster.md#die-befehle)).

## Die Abschnitte von kickstart.ini

Die in `kickstart.ini-dist` aufgeführten Blöcke: `email_settings`, `database_choice`, `database_init`,
`language_options`, `site_types`, `site_access`, `site_details`, `site_admin`, `security`, `registration`. Jeder hat
`Continue=true`, um die Werte ohne Rückfrage zu verwenden. Die Schlüssel stehen unter
[6.4.2 Abschnitte und Schlüssel](../../installation/06-kickstarter.md#642-abschnitte-und-schlüssel).

| Abschnitt | Schlüssel, die ein Bundle meist setzt |
|---|---|
| `[database_choice]` | `Type` (Standard `sqlite3`, `mysqli`, `pgsql`, ...) |
| `[database_init]` | `Server`, `Port`, `Database`, `User`, `Password`, `Socket` |
| `[language_options]` | `Primary` (zum Beispiel `eng-US`), `Languages[]` |
| `[site_types]` | `Site_package` |
| `[site_access]` | `Access` (`url`, `port`, `hostname`) |
| `[site_details]` | `Title`, `URL`, `Database`, `DatabaseAction`, die Zugangswerte |
| `[site_admin]` | `FirstName`, `LastName`, `Email`, `Password` (leer lassen, um es erzeugen zu lassen) |

## DatabaseAction

> **Warnung**
> "`DatabaseAction=remove` zerstört Daten. Es leert die in `[site_details] Database` genannte Datenbank und installiert
> hinein ... `--force` ist die einzige Bestätigung"
> ([6.5](../../installation/06-kickstarter.md#65-databaseaction-lesen-sie-dies-bevor-sie-run-ausführen)).

"Ein fehlender oder falsch geschriebener Wert verhält sich wie `ignore`": Schreiben Sie ihn in jeder Vorlage, die Sie
ausliefern, ausdrücklich hinein
([Der Kickstarter für Hoster](../hosting/bereitstellung/der-kickstarter-fuer-hoster.md#databaseaction)).

## Einstellungen, die ein Bundle bewusst setzen sollte

| Einstellung | Warum |
|---|---|
| `[site_admin] Password=` leer | der Installer erzeugt ein zufälliges Passwort mit 20 Zeichen und schreibt es nach `var/log/initial-admin-password` ([Das Standard-Administratorpasswort](../standard-administratorpasswort.md#der-kickstarter-kickstartini)) |
| `[site_admin] Email` | eine echte Adresse des Empfängers, damit Passwort-Zurücksetzungen jemanden erreichen |
| `[site_details] URL` | "Setzen Sie sie immer"; der Standard auf der Kommandozeile ist `http://localhost` |
| `[registration] Send=false` | `true` "sendet einen Registrierungsbericht über die Installation (System-, PHP- und Datenbankangaben) über den konfigurierten Mailtransport"; ein Bundle darf das nicht ohne Zustimmung des Empfängers tun |
| `[email_settings]` | der Transport, den die Website des Empfängers verwenden wird; lassen Sie Mail lieber unkonfiguriert, als auf Ihren eigenen Server zu zeigen |

## Erneute Läufe und Aufräumen

- "Dieselbe `kickstart.ini` mit `DatabaseAction=remove` und `run --force` ersetzt die Datenbank und die Einstellungen
  erneut. Verwenden Sie das, um eine Test- oder Schulungswebsite zurückzusetzen." So setzt sich ein Schulungs-Bundle
  zurück.
- `DatabaseAction=skip` mit `run --force` "schreibt `settings/siteaccess/` und `settings/override/` aus der Datei neu
  und lässt die Datenbank in Ruhe".
- Nach der Installation: "Verschieben Sie `kickstart.ini` aus dem Wurzelverzeichnis der Installation oder löschen Sie
  sie", und übergeben und entfernen Sie `var/log/initial-admin-password`.
- Protokolle, die Sie von einem fehlgeschlagenen Lauf aufbewahren: `var/log/kickstart.log` (Passwörter maskiert;
  `EXP_KICKSTART_LOG=0` schaltet es ab) und `var/log/setup.log`.

## Verwandte Seiten

- [kickstart.ini-Vorlagen](kickstart-ini-vorlagen.md)
- [Der Kickstarter für Hoster](../hosting/bereitstellung/der-kickstarter-fuer-hoster.md)
- [Kickstarter: eine ganze Website aus einer Datei installieren](../../funktionen/6.0/kickstarter-cli.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
