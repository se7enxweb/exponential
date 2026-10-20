---
title: Ein-Klick-Installer für Exponential
audience: hosting
category: hosting
order: 20
summary: Wie ein Hoster Exponential als Installation mit einem Klick anbietet - der eine Installer mit drei Oberflächen (Setup-Assistent, Kickstarter, exp:install), welche zu einem Panel, einem Skript oder einem Kunden passt, was der eine Klick vorher und nachher tun muss, und die Sicherheitsregeln für Passwörter und Datenbanken.
keywords: Ein-Klick-Installer, automatischer Installer, App-Installer, exp:install, Kickstarter, Setup-Assistent, Bereitstellung, Panel, CreateSites, Passwort, Datenbankaktion, Seitenpaket, unbeaufsichtigt, one-click installer, auto installer, app installer, setup wizard, provisioning, password, database action, site package, unattended
featured: true
type: help
---
# Ein-Klick-Installer für Exponential

Diese Seite ist für Hosting-Anbieter, die möchten, dass Kunden mit einem Klick in ihrem Panel oder Bestellformular eine
Exponential-Website bekommen. Sie beantwortet: was Exponential für unbeaufsichtigte Installationen bereits bereitstellt, auf welcher
Oberfläche Sie aufbauen und was Ihr Klick drumherum tun muss.

## Inhalt

1. [Ein Installer, drei Oberflächen](#ein-installer-drei-oberflächen)
2. [Welche Oberfläche für welches Angebot](#welche-oberfläche-für-welches-angebot)
3. [Was der eine Klick tun muss](#was-der-eine-klick-tun-muss)
4. [Sicherheitsregeln](#sicherheitsregeln)
5. [Was der Kunde erhält](#was-der-kunde-erhält)

## Ein Installer, drei Oberflächen

Jede Installationsmethode führt dieselbe Folge von Schrittklassen in `kernel/setup/steps/` aus, von `Welcome` bis `Final`; `CreateSites`
ist der Schritt, der die Datenbank, die Pakete und die Einstellungen schreibt. Die Oberflächen unterscheiden sich nur darin, woher die
Antworten kommen ([4.1](../../../../en/us/install/04-choosing-an-install-method.md#41-one-installer-three-front-ends)):

| | Setup-Assistent | Kickstarter | `exp:install` |
|---|---|---|---|
| Antworten aus | Formularen im Browser | `kickstart.ini` | Kommandozeilenoptionen, jede mit einem Standard |
| Braucht während der Installation einen Webserver | ja | nein | nein |
| Probelauf | nein | `run --dry-run` | `--dry-run`, `--print` |
| Schutz gegen Überschreiben | startet nur, solange `CheckValidity=true` | `--force` erforderlich | verweigert eine bestehende Installation ohne `--force` |
| Log | `var/log/setup.log` | `setup.log`, `kickstart.log` | `setup.log`, `var/log/exp-install-<date>.ini` |
| Administratorpasswort | im Formular eingegeben | aus der Datei; erzeugt, wenn leer oder bekannt | erzeugt, sofern kein annehmbares `--password` angegeben ist |

Quelle: [4.2](../../../../en/us/install/04-choosing-an-install-method.md#42-the-three-methods-at-a-glance).

## Welche Oberfläche für welches Angebot

| Angebot | Oberfläche | Warum |
|---|---|---|
| Eine Schaltfläche im Panel, die die Website anlegt | `exp:install` | Bevorzugen Sie `exp:install` in der Automatisierung: Es braucht keine Datei, prüft seine Optionen, bevor etwas geschrieben wird, und gibt bei jedem Fehler einen Exit-Status ungleich null zurück ([4.6](../../../../en/us/install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)) |
| Vorlagen, die Sie pflegen („Starter-Website für Agenturen“) | der Kickstarter | die geprüfte `kickstart.ini` ist die Spezifikation der Installation |
| Kunden auf Shared Hosting ohne Shell | der Setup-Assistent | braucht nur einen Browser; kann durch eine `kickstart.ini` vorgefüllt werden |
| Container-Images | `exp:install` im Entry Point, einmal ausgeführt | siehe [Container: Docker und Podman](../modelle/container-docker-und-podman.md) |

## Was der eine Klick tun muss

Exponential installiert die Website. Ihr Installer erledigt alles drumherum:

1. **Das Konto vorbereiten**: ein Systembenutzer, ein Home-Verzeichnis, ein PHP-FPM-Pool oder eine Velocity-Instanz für die Website
   ([Benutzer und Pools pro Kunde](../sicherheit/benutzer-und-pools-pro-kunde.md)).
2. **Die Datenbank vorbereiten**: nichts für SQLite 3 (der Standard; ein beschreibbares `var/storage/sqlite3/` genügt); für eine
   Server-Engine eine leere Datenbank und einen Benutzer, der Tabellen anlegen darf
   ([Datenbanken und Benutzer in Panels](../control-panels/datenbanken-und-benutzer.md)).
3. **Den Code an seinen Platz bringen**: einen Git-Checkout eines Release-Tags oder `composer create-project`/`composer install` aus
   einer getesteten Lock-Datei ([Composer und Git auf Servern](../grundlagen/composer-und-git-auf-servern.md)).
4. **Den Installer als Benutzer der Website ausführen**, nie als root:

   ```bash
   sudo -u <site user> env EXP_INSTALL_DB_PASSWORD='<from your secret store>' \
     php bin/php/console exp:install --dry-run --db=pgsql --db-user=<db user> --url=https://www.example.com
   sudo -u <site user> env EXP_INSTALL_DB_PASSWORD='<from your secret store>' \
     php bin/php/console exp:install --db=pgsql --db-user=<db user> --url=https://www.example.com \
     --email=<customer contact>
   ```

5. **Die Website ausliefern**: den Webserver darauf richten oder Velocity starten ([Einen Webserver wählen](../server/einen-webserver-waehlen.md)).
6. **Die Crontab einrichten** für `runcronjobs.php` ([Cronjobs](../betrieb/cronjobs.md)).
7. **Das Passwort übergeben**: `var/log/initial-admin-password` einmal lesen, dem Kunden über einen sicheren Kanal geben und die Datei
   löschen; oder das Passwort mit `--password=` aus Ihrem Geheimnisspeicher setzen.
8. **Die Website registrieren** in Backups und Überwachung.

Jeder Schritt ist in [Bereitstellung automatisieren](bereitstellung-automatisieren.md) beschrieben.

## Sicherheitsregeln

- **Richten Sie `--db-action=remove` nie auf eine Datenbank, die Daten enthält.** Es ist der Standard von `exp:install` und leert die
  Datenbank zuerst ([7.3](../../../../en/us/install/07-console-install.md#73-options)). Ein Ein-Klick-Installer sollte jedes Mal eine neue, leere
  Datenbank anlegen.
- **Halten Sie Geheimnisse von der Kommandozeile fern.** Nutzen Sie `EXP_INSTALL_DB_PASSWORD` statt `--db-password`; eine Option ist für
  jeden Benutzer der Maschine in der Prozessliste (`ps`) sichtbar, solange die Installation läuft.
- **Als Benutzer der Website ausführen.** Dateien, die `root` anlegt, kann der Webserver später nicht ändern.
- **Prüfen Sie den Exit-Status.** `0` installiert, `1` etwas ist gescheitert; bei einem Fehler bleibt der Wartungsmodus an, und
  `var/log/setup.log` sagt, warum ([7.7](../../../../en/us/install/07-console-install.md#77-exit-status)).
- **Lassen Sie keine Installationsdateien zurück.** Eine `kickstart.ini` enthält Passwörter im Klartext; entfernen Sie sie nach der
  Installation, da der Assistent sie bei einem späteren Lauf lesen würde.

## Was der Kunde erhält

- Die Adresse der Website und die Adresse der Administration (über die URL `/admin`, über den Host oder über den Port, je nach `--access`).
- Die Anmeldung `admin` des Administrators und sein Passwort, einmal; den Rat, es zu ändern und persönliche Konten anzulegen
  ([Erste Anmeldung und Standardpasswörter](../sicherheit/erste-anmeldung-und-standardpasswoerter.md)).
- Das Seitenpaket, das Sie gewählt haben, mit oder ohne Demo-Inhalte ([Seitenpakete](seitenpakete-fuer-hoster.md)).
- Einen Link zur Hilfe: das Hilfe-Center in seiner Administration.

## Verwandte Seiten

- [exp:install](exp-install-fuer-hoster.md)
- [Der Kickstarter](der-kickstarter-fuer-hoster.md)
- [Lizenzierung für Hoster](../geschaeft/lizenzierung-fuer-hoster.md) (Images und Installer, die Sie weitergeben)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
