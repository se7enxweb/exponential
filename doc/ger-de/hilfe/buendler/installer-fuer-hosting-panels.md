---
title: Installer für Hosting-Panels
audience: bundler, hosting
category: bundling
order: 300
summary: Wie Sie Exponential als Ein-Klick-Installation in einem Hosting-Control-Panel oder einem App-Installer bündeln - welchen Installer Sie aufrufen, die Schritte drumherum (Konto, Datenbank, Code, Installation als Benutzer des Kontos, Webserver-Regeln, Cron, Passwortübergabe), Sicherheitsregeln und was der Kunde erhalten sollte.
keywords: Panel, Control Panel, Ein-Klick-Installer, App-Installer, Datenbank, Passwort, panel, control panel, one-click installer, app installer, Plesk, cPanel, DirectAdmin, ISPConfig, Webmin, exp:install, setup wizard, database, password, cron, bundle
type: help
---
# Installer für Hosting-Panels

Diese Seite richtet sich an Bündler, die die Schaltfläche "Exponential installieren" eines Hosting-Control-Panels oder
eines Anwendungsinstallers schreiben. Der Hosting-Bereich zeigt die Sicht des Betreibers unter
[Ein-Klick-Installer für Exponential](../hosting/bereitstellung/ein-klick-installer-fuer-exponential.md); diese Seite ist die
Checkliste für die Person, die den Installer baut und ausliefert.

## Inhalt

1. [Welchen Installer Sie aufrufen](#welchen-installer-sie-aufrufen)
2. [Was das Installer-Paket enthält](#was-das-installer-paket-enthält)
3. [Der Installationsablauf](#der-installationsablauf)
4. [Sicherheitsregeln](#sicherheitsregeln)
5. [Was der Kunde erhält](#was-der-kunde-erhält)
6. [Hinweise zu Panels](#hinweise-zu-panels)

## Welchen Installer Sie aufrufen

| Situation im Panel | Aufruf |
|---|---|
| Das Panel kann einen Befehl als Benutzer des Kontos ausführen | `exp:install`: "Verwenden Sie in der Automatisierung bevorzugt `exp:install`" ([4.6](../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)) |
| Das Konto hat keine Shell, nur Datei-Upload und einen Browser | legen Sie den Code ab und lassen Sie den Kunden im Einrichtungsassistenten abschließen, wahlweise vorbelegt durch eine `kickstart.ini` ([Der Einrichtungsassistent auf Shared Hosting](../hosting/bereitstellung/der-setup-assistent-auf-shared-hosting.md)) |
| Sie pflegen feste Vorlagen je Tarif | den Kickstarter mit einer Vorlage je Tarif ([kickstart.ini-Vorlagen](kickstart-ini-vorlagen.md)) |

## Was das Installer-Paket enthält

- Den Code: ein vorbereitetes Archiv eines festgelegten Releases, gebaut aus Ihrer Lock-Datei, oder die Befehle zum
  Abrufen ([Den Code beschaffen](den-code-fuer-ein-bundle-beschaffen.md)).
- Die Zuordnung der Formularfelder des Panels zu den Optionen von `exp:install`: Website-URL zu `--url`, E-Mail zu
  `--email`, die vom Panel angelegte Datenbank zu `--db`, `--db-host`, `--db-name`, `--db-user`, das Passwort über
  `EXP_INSTALL_DB_PASSWORD`, das Seitenpaket zu `--package`.
- Die Webserver-Regeln für den Server des Panels ([Webserver- und Engine-Wahl](webserver-und-engine-wahl.md)).
- Eine Beschreibung der Deinstallation: welche Dateien, welche Datenbank, welche Cron-Einträge.
- Ihre Hinweise und das Quellcode-Angebot ([Lizenztexte und Hinweise](mitzuliefernde-lizenztexte-und-hinweise.md)).

## Der Installationsablauf

Aus [Was der eine Klick tun muss](../hosting/bereitstellung/ein-klick-installer-fuer-exponential.md#was-der-eine-klick-tun-muss):

1. Bereiten Sie das Konto vor: Systembenutzer, Home-Verzeichnis, PHP-FPM-Pool oder Velocity-Instanz.
2. Bereiten Sie die Datenbank vor: nichts für SQLite 3; für eine Server-Engine eine leere Datenbank und einen
   Benutzer, der Tabellen anlegen darf.
3. Legen Sie den Code an seinen Platz.
4. Führen Sie den Installer als Benutzer der Website aus, nie als root, zuerst mit einem Probelauf:

   ```bash
   sudo -u <site user> env EXP_INSTALL_DB_PASSWORD='<from your secret store>' \
     php bin/php/console exp:install --dry-run --db=pgsql --db-user=<db user> --url=https://www.example.com
   sudo -u <site user> env EXP_INSTALL_DB_PASSWORD='<from your secret store>' \
     php bin/php/console exp:install --apply --db=pgsql --db-user=<db user> --url=https://www.example.com \
     --email=<customer contact>
   ```

5. Liefern Sie die Website aus.
6. Installieren Sie die Crontab für `runcronjobs.php`.
7. Übergeben Sie das Passwort und löschen Sie `var/log/initial-admin-password`.
8. Tragen Sie die Website in Backups und Überwachung ein.

## Sicherheitsregeln

- "Richten Sie `--db-action=remove` nie auf eine Datenbank, die Daten enthält." Ein Ein-Klick-Installer legt jedes
  Mal eine neue, leere Datenbank an.
- Halten Sie Geheimnisse von der Kommandozeile fern: Eine Option "ist für jeden Benutzer der Maschine in der
  Prozessliste (`ps`) sichtbar, solange die Installation läuft".
- "Prüfen Sie den Exit-Status. `0` installiert, `1` etwas ist fehlgeschlagen; bei einem Fehler bleibt der
  Wartungsmodus aktiv, und `var/log/setup.log` sagt, warum."
- "Lassen Sie keine Installationsdateien zurück": Eine `kickstart.ini` enthält Passwörter im Klartext.

Quelle: [Sicherheitsregeln](../hosting/bereitstellung/ein-klick-installer-fuer-exponential.md#sicherheitsregeln).

## Was der Kunde erhält

- Die Adresse der Website und die Adresse der Administration.
- Den Login `admin` und sein Passwort, einmal, über einen sicheren Kanal, mit dem Rat, es zu ändern und persönliche
  Konten anzulegen ([Standardzugangsdaten und erste Anmeldung](standardzugangsdaten-und-erste-anmeldung.md)).
- Das gewählte Seitenpaket, mit oder ohne Demo-Inhalte.
- Einen Link zur Hilfe in seiner Administration und zu Ihrer eigenen Support-Seite
  ([Grenzen des Supports](grenzen-des-supports.md)).

## Hinweise zu Panels

Der Hosting-Bereich hat eine Seite je Panel: [Plesk](../hosting/control-panels/plesk.md), [cPanel](../hosting/control-panels/cpanel.md),
[DirectAdmin](../hosting/control-panels/directadmin.md), [ISPConfig](../hosting/control-panels/ispconfig.md),
[Webmin](../hosting/control-panels/webmin.md), dazu [PHP-Auswahl und FPM-Pools](../hosting/control-panels/php-auswahl-und-fpm-pools.md)
und [Cron und Shell-Zugang](../hosting/control-panels/cron-und-shell-zugang.md). Ob ein bestimmter App-Installer-Katalog
Exponential führt, ist hier nicht dokumentiert; fragen Sie dessen Anbieter.

## Verwandte Seiten

- [Ein-Klick-Installer für Exponential](../hosting/bereitstellung/ein-klick-installer-fuer-exponential.md)
- [Bereitstellung automatisieren](../hosting/bereitstellung/bereitstellung-automatisieren.md)
- [Unbeaufsichtigte Installation mit exp:install](unbeaufsichtigte-installation-mit-exp-install.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
