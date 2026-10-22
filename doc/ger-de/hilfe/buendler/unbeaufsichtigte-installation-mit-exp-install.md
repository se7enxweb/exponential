---
title: Unbeaufsichtigte Installation mit exp:install
audience: bundler, hosting, developer
category: bundling
order: 200
summary: Wie ein Bundle Exponential mit einem Befehl installiert - exp:install und seine Optionen für Datenbank, Website, Administrator und Steuerung, Geheimnisse in EXP_INSTALL_DB_PASSWORD, die Datenbankaktion, die erzeugte Passwortdatei, Exit-Status, Protokolle und sichere Muster für Images und Installer.
keywords: unbeaufsichtigte Installation, Geheimnisse, Datenbankaktion, Exit-Status, Protokolle, exp:install, install.php, unattended, --db, --db-action, --package, --url, --access, --host, --admin-host, --email, --password, --random-password, --force, --dry-run, --print, EXP_INSTALL_DB_PASSWORD, initial-admin-password, exit status
type: help
---
# Unbeaufsichtigte Installation mit exp:install

Diese Seite richtet sich an Bündler, die die Website aus einem Skript installieren: beim ersten Start eines Images, aus
einem Panel-Installer, in einem CI-Job. `exp:install` (auch `php bin/php/install.php`) "schreibt aus seinen Optionen
eine `kickstart.ini` und führt den Kickstarter aus"
([Das Standard-Administratorpasswort](../standard-administratorpasswort.md#die-konsoleninstallation-expinstall)). Die
vollständige Referenz ist [Kapitel 7: Die Konsoleninstallation](../../installation/07-installation-ueber-die-konsole.md).

## Inhalt

1. [Die kürzeste Installation](#die-kürzeste-installation)
2. [Die Optionen](#die-optionen)
3. [Geheimnisse](#geheimnisse)
4. [Die Datenbankaktion](#die-datenbankaktion)
5. [Exit-Status und Protokolle](#exit-status-und-protokolle)
6. [Muster für Bundles](#muster-für-bundles)

## Die kürzeste Installation

```bash
php bin/php/console exp:install
```

"Das installiert das Multisite-Paket auf **SQLite**, in `eng-US`, mit den Siteaccesses `site` und `admin`, erreichbar
per URL, und gibt eine Zusammenfassung mit den Anmeldeadressen der Website und der Administration aus"
([Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)).

## Die Optionen

Aus `php bin/php/install.php --help` (6.0.15):

| Gruppe | Optionen |
|---|---|
| Datenbank | `--db` (Standard `sqlite`, `mysql`/`mariadb`, `pgsql`, `mongodb`, `oracle`), `--db-host`, `--db-port`, `--db-name`, `--db-user`, `--db-password`, `--db-socket`, `--db-action` (Standard `remove`, `ignore`, `skip`) |
| Website | `--package` (Standard `sevenx_multisite`), `--language` (Standard `eng-US`), `--languages`, `--title`, `--url`, `--access` (`url`, `host`, `port`), `--site-access`, `--admin-access`, `--host`, `--admin-host`, `--port`, `--admin-port`, `--editor-access`, `--editor-host`, `--editor-port` |
| Administrator (Login `admin`) | `--email` (Standard `nospam@exponential.earth`), `--password`, `--random-password`, `--first-name`, `--last-name` |
| Ablauf | `--force`, `--dry-run`, `--print`, `--help` |

Einige Regeln aus dem Hilfetext:

- `--host` oder `--admin-host` bedeuten `--access=host`; "--access=host needs both --host and --admin-host" (beide
  sind nötig), und sie müssen sich unterscheiden.
- `--password` "wird übernommen, wenn es mindestens 10 Zeichen hat (site.ini MinPasswordLength) und nicht allgemein
  bekannt ist"; sonst ersetzt es ein erzeugtes, und die Zusammenfassung sagt das.
- `--random-password` erzeugt 24 Zeichen, "einmal am Ende angezeigt und nach var/log/initial-admin-password
  geschrieben".
- Setzen Sie für eine echte Website immer `--url`; der Standard ist `http://localhost`.
- Setzen Sie `--email` immer auf eine Adresse des Empfängers; der Standard ist ein Platzhalter.

## Geheimnisse

- **Datenbankpasswort:** "Übergeben Sie das Datenbankpasswort mit `--db-password` oder, besser, mit der
  Umgebungsvariable `EXP_INSTALL_DB_PASSWORD`, die es aus der Prozessliste und der Shell-Historie heraushält."
- **Administratorpasswort:** Lassen Sie es erzeugen und übergeben Sie es aus `var/log/initial-admin-password`, oder
  übergeben Sie eines aus Ihrem Geheimnisspeicher. "Schreiben Sie es nie in ein Skript oder ein Image"
  ([Das Standard-Administratorpasswort](../standard-administratorpasswort.md#die-konsoleninstallation-expinstall)).
- `--print` zeigt die Konfiguration "mit maskierten Passwörtern"; auch `var/log/exp-install-<date>.ini` hält sie
  maskiert.

## Die Datenbankaktion

`--db-action=remove` ist der Standard und "leert zuerst die Datenbank". Das ist "das, was ein frischer Container will,
und genau das, was nie auf eine Datenbank zeigen darf, die Daten enthält, die Sie brauchen"
([4.6](../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)).

| Wert | Verwendung in einem Bundle |
|---|---|
| `remove` | die erste Installation in eine neue, leere Datenbank oder eine neue SQLite-Datei |
| `ignore` | Hinzufügen in eine leere Datenbank; ein zweiter Lauf fügt eine zweite Kopie der Daten hinzu |
| `skip` | die Einstellungen gegen eine bestehende, vollständige Datenbank neu schreiben (`--db-action=skip --force`) |

## Exit-Status und Protokolle

| Status | Bedeutung |
|---|---|
| `0` | installiert; oder `--dry-run` bestanden; oder `--print`; oder `--help` |
| `1` | eine Option hat ihre Prüfung nicht bestanden, das Verzeichnis enthält bereits eine Installation ohne `--force`, oder der Lauf ist fehlgeschlagen |

Bei einem Fehler bleibt der Wartungsmodus aktiv, und `var/log/setup.log` sagt, warum
([7.7 Exit-Status](../../installation/07-installation-ueber-die-konsole.md#77-exit-status)). Bewahren Sie `var/log/setup.log` und
`var/log/kickstart.log` jeder fehlgeschlagenen Bundle-Installation auf.

## Muster für Bundles

Ein Probelauf, dann die Installation, mit Abbruch bei jedem Fehler:

```bash
set -euo pipefail
export EXP_INSTALL_DB_PASSWORD="$(cat /run/secrets/db_password)"
php bin/php/console exp:install --dry-run --db=pgsql --db-host=db --db-name=site --db-user=site \
    --url=https://www.example.com --email=webmaster@example.com
php bin/php/console exp:install --db=pgsql --db-host=db --db-name=site --db-user=site \
    --url=https://www.example.com --email=webmaster@example.com
```

Getrennte Hosts für die Website und die Administration:

```bash
php bin/php/console exp:install --access=host --host=www.example.com --admin-host=admin.example.com
```

Führen Sie es als Benutzer der Website aus, nie als root: "Von `root` angelegte Dateien kann der Webserver später nicht
ändern" ([Ein-Klick-Installer](../hosting/bereitstellung/ein-klick-installer-fuer-exponential.md#sicherheitsregeln)). Installieren Sie
einmal, danach nie bei jedem Start ([Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md)).

## Verwandte Seiten

- [exp:install für Hoster](../hosting/bereitstellung/exp-install-fuer-hoster.md)
- [Die drei Installer](die-drei-installer.md)
- [Standardzugangsdaten und erste Anmeldung](standardzugangsdaten-und-erste-anmeldung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
