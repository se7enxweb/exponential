---
title: Der Kickstarter für Hoster
audience: hosting, admin
category: hosting
order: 40
summary: Wie ein Hoster den Kickstarter für wiederholbare Exponential-Installationen aus einer geprüften kickstart.ini nutzt - die Befehle ini und run, DatabaseAction und warum es Daten zerstören kann, Probeläufe, Vorlagen mit Platzhaltern, Logs und das Aufräumen der Datei danach.
keywords: Kickstarter, kickstart.ini, exp:kickstarter, kickstarter.php, ini, run, --force, --dry-run, --list-steps, DatabaseAction, remove, ignore, skip, Vorlage, envsubst, Staging, wiederholbare Installation, kickstart.log, kickstarter, template, staging, repeatable install
type: help
---
# Der Kickstarter für Hoster

Diese Seite ist für Systemadministratoren, die dieselbe Art von Exponential-Website immer wieder installieren: die Standardvorlagen eines
Hosters, Staging-Kopien, Schulungs-Websites. Sie beantwortet: wie der Kickstarter funktioniert, wie Sie seine Konfiguration als geprüfte
Vorlage halten und wie Sie damit keine Datenbank zerstören. Die Referenz ist [Kapitel 6](../../../installation/06-kickstarter.md).

## Inhalt

1. [Was er ist](#was-er-ist)
2. [Die Befehle](#die-befehle)
3. [DatabaseAction](#databaseaction)
4. [Vorlagen mit Platzhaltern](#vorlagen-mit-platzhaltern)
5. [Logs](#logs)
6. [Aufräumen](#aufräumen)

## Was er ist

Der Kickstarter (`bin/php/kickstarter.php`, Konsolenname `exp:kickstarter`) führt die Schrittklassen in einem PHP-Prozess auf der
Kommandozeile aus und liest jede Antwort aus `kickstart.ini` ([4.2](../../../installation/04-installationsweg-waehlen.md#der-kickstarter)).
Er ist die Methode für den Fall, dass dieselbe Installation mehr als einmal gemacht werden muss, die Antworten vor der Anwendung als
Datei geprüft werden sollen oder das Seitenpaket aus einem lokalen Paket-Repository kommt.

## Die Befehle

```bash
php bin/php/console exp:kickstarter ini --yes          # write kickstart.ini from the built-in defaults
php bin/php/console exp:kickstarter run --list-steps   # show the steps, change nothing
php bin/php/console exp:kickstarter run --dry-run      # check, stop after SiteDetails
php bin/php/console exp:kickstarter run --apply --force        # install
```

- `ini` schreibt die Datei: interaktiv, mit `--yes` (eingebaute Standardwerte) oder `--defaults` (die Werte aus `kickstart.ini-dist`).
- `run` installiert daraus. `--force` ist immer erforderlich, wenn der Bereich der Schritte `CreateSites` enthält.
- Ein Aufruf ohne Befehl zeigt die Hilfe und endet mit Status 0, sodass ein Skript, das den Unterbefehl vergessen hat, nicht scheitert;
  prüfen Sie, dass Ihre Skripte `run` nennen ([6.2](../../../installation/06-kickstarter.md#62-die-befehle)).

## DatabaseAction

> **Warnung**
> `DatabaseAction=remove` zerstört Daten. Es leert die in `[site_details] Database` genannte Datenbank und installiert hinein. Alles in
> dieser Datenbank geht verloren, und nichts fragt nochmals nach: `--force` ist die einzige Bestätigung
> ([6.5](../../../installation/06-kickstarter.md#65-databaseaction-lesen-sie-dies-bevor-sie-run-ausführen)).

| Wert | Datenbank | Nutzen Sie ihn für |
|---|---|---|
| `remove` | Tabellen gelöscht (SQLite: Datei geleert), dann installiert | eine saubere Installation, eine Neuinstallation |
| `ignore` | zu dem hinzugefügt, was da ist, nichts gelöscht | eine neue, leere Datenbank |
| `skip` | nichts eingefügt | `settings/siteaccess/` und `settings/override/` gegen eine vollständige Datenbank neu erzeugen |

Ein Wert, der fehlt oder falsch geschrieben ist, verhält sich wie `ignore`: Schreiben Sie ihn ausdrücklich. Sichern Sie jede Datenbank,
die ein Lauf berühren könnte, zuerst in ein Verzeichnis außerhalb des Wurzelverzeichnisses der Installation.

## Vorlagen mit Platzhaltern

Das Muster aus [6.11](../../../installation/06-kickstarter.md#611-in-ci-und-containern), für die Standard-Websites eines Hosters:

1. Halten Sie eine Vorlage wie `deploy/kickstart.ini.template` unter Versionsverwaltung, mit Platzhaltern für die Werte des Kunden und
   Geheimnisse.
2. Erzeugen Sie daraus bei der Bereitstellung die Datei im Wurzelverzeichnis der Installation, mit dem Modus `0600`.
3. Probelauf, dann installieren, und bei einem Status ungleich null scheitern:

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /var/www/vhosts/<domain>/<installation>
umask 077
envsubst < deploy/kickstart.ini.template > kickstart.ini
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --apply --force
rm -f kickstart.ini
```

`exp:kickstarter ini --from-installed` füllt `Database` aus den Einstellungen einer bestehenden Installation, was hilft, wenn Sie eine
Vorlage aus einer Website bauen, die Sie bereits betreiben ([6.2.1](../../../installation/06-kickstarter.md#621-ini-kickstartini-schreiben)).

> **Tipp**
> Der Assistent im Browser liest `kickstart.ini` ebenfalls und füllt Seiten daraus vor oder überspringt sie
> ([5.6](../../../installation/05-setup-assistent.md#56-wie-kickstartini-seiten-vorbelegt-und-überspringt)). Ein Hoster kann die Datenbankseite für
> einen Kunden vorfüllen, der die Installation dann im Browser abschließt
> ([Der Setup-Assistent auf Shared Hosting](der-setup-assistent-auf-shared-hosting.md)).

## Logs

| Datei | Enthält |
|---|---|
| `var/log/setup.log` | jeden Schritt des Laufs |
| `var/log/kickstart.log` (`.1` bis `.9` behalten frühere Läufe) | jeden Lauf des Kickstarters, Passwörter maskiert; `EXP_KICKSTART_LOG=0` schaltet es aus |

`var/log/kickstart.log` und `var/log/setup.log` sind die Artefakte, die Sie von einem gescheiterten Job aufbewahren.

## Aufräumen

- Entfernen Sie `kickstart.ini` nach der Installation: Sie enthält Passwörter im Klartext, und der Assistent würde sie bei einem
  späteren Lauf lesen ([4.6](../../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)).
- Übergeben und entfernen Sie `var/log/initial-admin-password`, wenn ein Passwort erzeugt wurde.

**Eine öffentliche Demo.** Mit `EXP_DEMO_MODE=1` in der Umgebung von `exp:kickstarter run` wird ein bekanntes oder kurzes `[site_admin] Password` (`publish`, `publishing`) unverändert installiert, statt ersetzt zu werden; ein leeres wird weiterhin ersetzt. Der Kindprozess des Laufs erbt die Variable. [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md).

## Verwandte Seiten

- [exp:install für Hoster](exp-install-fuer-hoster.md)
- [Bereitstellung automatisieren](bereitstellung-automatisieren.md)
- [Ein-Klick-Installer](ein-klick-installer-fuer-exponential.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
