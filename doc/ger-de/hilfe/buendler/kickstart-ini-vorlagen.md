---
title: kickstart.ini-Vorlagen für Bundles
audience: bundler, hosting, developer
category: bundling
order: 220
summary: Wie Sie die Installation eines Bundles als geprüfte kickstart.ini-Vorlage mit Platzhaltern führen, sie zur Installationszeit mit Modus 0600 erzeugen, einen Probelauf und die Installation ausführen und sie danach entfernen - mit einer vollständigen SQLite-Vorlage und einer PostgreSQL-Variante.
keywords: kickstart.ini, Vorlage, Platzhalter, Probelauf, Starter-Kit, template, placeholder, envsubst, umask 077, 0600, dry run, run --apply, SQLite, PostgreSQL, DatabaseAction, Site_package, bundle, starter kit
type: help
---
# kickstart.ini-Vorlagen für Bundles

Diese Seite richtet sich an Bündler, die die Installation ihres Bundles als Datei festhalten möchten, die sich prüfen,
vergleichen und versionieren lässt. Das Muster stammt aus
[6.11 In CI und Containern](../../installation/06-kickstarter.md#611-in-ci-und-containern).

## Inhalt

1. [Das Muster](#das-muster)
2. [Eine SQLite-Vorlage](#eine-sqlite-vorlage)
3. [Eine PostgreSQL-Variante](#eine-postgresql-variante)
4. [Erzeugen und ausführen](#erzeugen-und-ausführen)
5. [Eine Vorlage prüfen](#eine-vorlage-prüfen)

## Das Muster

1. "Führen Sie eine Vorlage, z. B. `deploy/kickstart.ini.template`, unter Versionskontrolle, mit Platzhaltern für
   Geheimnisse."
2. "Erzeugen Sie sie bei der Bereitstellung im Wurzelverzeichnis der Installation mit Modus `0600`."
3. "Führen Sie einen Probelauf und dann die Installation aus, und lassen Sie den Job bei einem Status ungleich null
   scheitern."
4. Entfernen Sie die erzeugte Datei.

Die Platzhalter unten haben die Form `${NAME}` für `envsubst`; jedes Vorlagenwerkzeug funktioniert.

## Eine SQLite-Vorlage

Schlüssel aus [6.4.2 Abschnitte und Schlüssel](../../installation/06-kickstarter.md#642-abschnitte-und-schlüssel). Prüfen Sie
jeden gegen die `kickstart.ini-dist` der Version, die Sie ausliefern.

```ini
[database_choice]
Continue=true
Type=sqlite3

[database_init]
Continue=true
Database=${SITE_DB_FILE}

[language_options]
Continue=true
Primary=eng-US

[site_types]
Continue=true
Site_package=${SITE_PACKAGE}

[site_access]
Continue=true
Access=url

[site_details]
Continue=true
Title=${SITE_TITLE}
URL=${SITE_URL}
Access=site
AdminAccess=admin
EditorAccess=editor
Database=${SITE_DB_FILE}
DatabaseAction=remove

[site_admin]
Continue=true
FirstName=Site
LastName=Administrator
Email=${ADMIN_EMAIL}
Password=

[security]
Continue=true

[registration]
Continue=true
Send=false
```

- Für SQLite ist `Database` "ein Dateiname in `var/storage/sqlite3/`"; geben Sie in `[database_init]` und
  `[site_details]` denselben Wert an.
- `Password=` bleibt leer: Der Installer erzeugt eines und schreibt es nach `var/log/initial-admin-password`.
- `DatabaseAction=remove` ist richtig für eine Erstinstallation in eine neue Datei und darf nie auf eine Datenbank mit
  Daten treffen.

## Eine PostgreSQL-Variante

Ändern Sie die Datenbankblöcke; behalten Sie den Rest:

```ini
[database_choice]
Continue=true
Type=pgsql

[database_init]
Continue=true
Server=${DB_HOST}
Port=5432
Database=${DB_NAME}
User=${DB_USER}
Password=${DB_PASSWORD}
```

und `Database=${DB_NAME}` in `[site_details]`. "Keiner der Installer legt die Datenbank oder den Benutzer eines
Datenbankservers an. Legen Sie sie zuerst an"
([Datenbankunterstützung](../datenbankunterstuetzung.md#unterstützung-durch-die-installer)). Kommentierte vollständige Beispiele für
SQLite, MySQL oder MariaDB und PostgreSQL stehen in [6.6](../../installation/06-kickstarter.md#66-kommentierte-beispieldateien).

## Erzeugen und ausführen

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /srv/exponential
umask 077
envsubst < deploy/kickstart.ini.template > kickstart.ini
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --apply --force
rm -f kickstart.ini
```

Das ist das Skript aus [6.11](../../installation/06-kickstarter.md#611-in-ci-und-containern). Punkte aus jenem Abschnitt,
an die Sie denken sollten: "`run` braucht kein Terminal"; "Der Lauf dauert einige Minuten; geben Sie dem Job genug
Zeit"; "Führen Sie die Installation in einem Container einmal aus ... nicht bei jedem Start".

Wenn Sie eine erzeugte `kickstart.ini` von Hand bearbeiten und der Browser-Assistent sie lesen soll, löschen Sie zuerst
`var/cache/ini/kickstart-*.php`: "`ini` und `run` löschen [sie] selbst ... Der Browser-Assistent nicht."

## Eine Vorlage prüfen

- [ ] Jeder Platzhalter wird aus Ihrem Geheimnisspeicher oder aus der Eingabe des Empfängers befüllt, nie aus einem
      Wert in der Vorlage.
- [ ] `DatabaseAction` ist ausdrücklich angegeben.
- [ ] `[site_admin] Password` ist leer oder aus einem Geheimnisspeicher mit einem langen Zufallswert befüllt.
- [ ] `[registration] Send=false`.
- [ ] `URL` und `Email` gehören dem Empfänger.
- [ ] Die erzeugte Datei wird mit Modus `0600` angelegt und nach dem Lauf entfernt.
- [ ] Ein Probelauf der Vorlage läuft für jedes Bundle-Release in Ihrer CI.

## Verwandte Seiten

- [Der Kickstarter für Bundles](der-kickstarter-fuer-bundles.md)
- [Der Kickstarter für Hoster](../hosting/bereitstellung/der-kickstarter-fuer-hoster.md#vorlagen-mit-platzhaltern)
- [Starter-Kits für Agenturen](starter-kits-fuer-agenturen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
