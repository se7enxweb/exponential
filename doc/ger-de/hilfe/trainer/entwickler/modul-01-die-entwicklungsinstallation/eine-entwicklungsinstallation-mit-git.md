---
title: "Lektion 1.2: Eine Entwicklungsinstallation mit Git"
audience: trainer, developer
category: training
order: 12
summary: Die Teilnehmenden holen den Kernel und seine Erweiterungen als Git-Klone, installieren mit exp:install eine Website auf SQLite, starten mit Velocity einen Webserver und melden sich an. 60 Minuten.
keywords: entwickler, git clone, composer install, preferred-install source, exp:install, sqlite, velocity, entwicklungsinstallation, branch my-dev, developers course, development installation, my-dev branch
type: help
---
# Lektion 1.2: Eine Entwicklungsinstallation mit Git

Entwickler arbeiten auf einer eigenen Installation, in der sie etwas kaputt machen dürfen. In dieser Lektion baut sich
jede Person eine solche Installation: den Kernel und jede Erweiterung als Git-Klone, eine Datenbank in einer
einzigen SQLite-Datei und einen Webserver, der mit einem Befehl startet. Alles Weitere im Kurs läuft auf dieser
Installation.

> **Hinweis**
> Der Klon und `composer install` laden mehrere hundert Megabyte herunter. In einem Schulungsraum mit langsamer
> Verbindung bereiten Sie vorher auf jedem Rechner eine Kopie eines fertigen Klons vor und beginnen bei Schritt 5.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 1: Die Entwicklungsinstallation](README.md) |
| Dauer | 60 Minuten (15 Präsentation, 45 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | PHP-Entwickler, die neu bei Exponential sind |
| Voraussetzungen | [Lektion 1.1](die-landkarte-fuer-entwickler.md); PHP, Composer 2 und Git 2.28 oder neuer auf dem eigenen Rechner |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Composer so einstellen, dass die Pakete `se7enxweb/*` als Git-Klone installiert werden;
- den Kernel klonen, einen eigenen Branch beginnen und `composer install` ausführen;
- eine Website mit `php bin/php/console exp:install` installieren und das erzeugte Administratorpasswort finden;
- den eingebauten Webserver mit `exp:velocity start` starten und sich an der Administration anmelden;
- erklären, warum die Entwicklungsinstallation nie Einstellungen oder Daten mit einer Live-Website teilt.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Mit Git entwickeln](../../../mit-git-entwickeln.md),
[Eine Entwicklungsinstallation einrichten](../../../../../eng-us/help/extension-development/part-1-from-user-to-developer/setting-up-a-development-installation.md)
und [7. Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md).

1. **Warum Klone** (4 Minuten). Ein Release-Archiv enthält die Dateien einer Version; ein Klon enthält die
   Geschichte. `git log`, `git blame` und `git bisect` beantworten „Warum steht diese Zeile hier?“ und „Welche
   Änderung hat es kaputt gemacht?“, und ein Branch hält Ihre eigenen Änderungen fest. Zeigen Sie die Tabelle in
   [Mit Git entwickeln](../../../mit-git-entwickeln.md#warum-git-klone-und-keine-archive).
2. **Die eine Composer-Einstellung** (2 Minuten). `composer config --global 'preferred-install.se7enxweb/*' source`
   macht jedes Paket `se7enxweb/*` zu einem Klon und alles andere zu einem schnellen Archiv.
3. **`exp:install`** (5 Minuten). Ohne Optionen installiert es das Seitenpaket `sevenx_multisite` auf SQLite in
   `eng-US` und endet mit einer Zusammenfassung: Adresse der Website, Anmeldung an der Administration, Benutzer
   `admin` und ein erzeugtes Passwort, das auch in `var/log/initial-admin-password` geschrieben wird (nur für den
   Eigentümer lesbar).
4. **Ausliefern** (4 Minuten). `settings/velocity.ini` wird mit `[ServerSettings] Engine=php` ausgeliefert, dem
   eingebauten Server von PHP mit dem Router `bin/php/velocity-router.php`; `[PHPServerSettings] Port=8087`.

### Häufige Fragen

**„HEAD detached“ in einer Erweiterung: Ist etwas kaputt?** Nein. Composer checkt genau den Release-Tag aus, den
`composer.json` verlangt. Beginnen Sie einen Branch, bevor Sie etwas ändern.

**Brauche ich ein GitHub-Konto?** Nur um Änderungen zurückzuschicken (Lektion 13.3). Für die Installation braucht man
keins.

**Kann ich MySQL statt SQLite verwenden?** Ja: `exp:install` nimmt Datenbankoptionen an, aufgeführt in
[7.3 Optionen](../../../../installation/07-installation-ueber-die-konsole.md#73-optionen). SQLite braucht keinen Datenbankserver, was einen
Schulungsraum einfach hält.

**Warum `./console`?** Viele Entwickler legen einen Link an, `ln -s bin/php/console console`. Er gehört nicht zu den
ausgelieferten Dateien; dieser Kurs schreibt `php bin/php/console` vollständig aus.

### Stolperfallen

- **Alles als root ausführen.** Befehle verweigern die Ausführung als root, solange `--allow-root-user` nicht
  angegeben ist. Lassen Sie die Teilnehmenden als eigener Benutzer arbeiten.
- **`exp:install` über eine bestehende Installation.** Ohne `--force` verweigert es die Arbeit, und seine
  Voreinstellung `--db-action=remove` leert die angegebene Datenbank. Richten Sie es nie auf eine Datenbank, auf die
  es ankommt.
- **Ein belegter Port.** Ist Port 8087 belegt, schlägt der Start fehl; der Trainer ändert `Port` in einem
  Einstellungs-Override oder hält den anderen Server an.

## Praktische Übung

Alle Teilnehmenden arbeiten auf ihrem eigenen Rechner (oder in ihrem eigenen Ordner auf einem gemeinsamen
Schulungsserver).

1. Weisen Sie Composer an, für die Pakete des Projekts Klone zu bevorzugen:

   ```bash
   composer config --global 'preferred-install.se7enxweb/*' source
   composer config --global preferred-install
   ```

2. Klonen Sie den Kernel und beginnen Sie Ihren Branch:

   ```bash
   git clone https://github.com/se7enxweb/exponential.git exponential
   cd exponential
   git switch -c my-dev
   ```

3. Installieren Sie die Pakete: `composer install`.
4. Prüfen Sie, dass die Erweiterungen Klone sind: `ls -d extension/*/.git | wc -l`.
5. Sehen Sie sich die Konfiguration an, ohne etwas zu schreiben:

   ```bash
   php bin/php/console exp:install --print --url=http://127.0.0.1:8087 --title="Developer course"
   ```

6. Installieren Sie: `php bin/php/console exp:install --url=http://127.0.0.1:8087 --title="Developer course"`.
7. Kopieren Sie das Passwort aus der Zusammenfassung an einen sicheren Ort.
8. Starten Sie den Webserver: `php bin/php/console exp:velocity start`.
9. Öffnen Sie die Anmeldeadresse der Administration aus der Zusammenfassung und melden Sie sich als `admin` an.
10. Ändern Sie das Administratorpasswort in der Administration und entfernen Sie dann
    `var/log/initial-admin-password`.
11. Führen Sie `git status` im Wurzelverzeichnis der Installation aus und lesen Sie, welche Dateien die Installation
    angelegt hat.

## Erwartetes Ergebnis

- Schritt 4 gibt eine Zahl größer als null aus.
- Schritt 6 endet mit dem Kasten „Exponential is installed“, der die Adressen der Website, der Administration und
  des Editors auflistet.
- Schritt 9 zeigt das Dashboard der Administration.
- Schritt 11 listet Dateien wie `settings/override/` als nicht versioniert: die eigenen Einstellungen der
  Installation, die nie in ein öffentliches Repository gepusht werden dürfen, weil sie hostspezifische Werte enthalten.
- Der Trainer prüft das, indem er alle Teilnehmenden bittet, das Dashboard und die Ausgabe von `git branch`
  (`* my-dev`) zu zeigen.

## Quiz

1. Was ändert `composer config --global 'preferred-install.se7enxweb/*' source`?
2. Welche Datenbank verwendet `exp:install`, wenn keine Option etwas anderes angibt?
   A. MySQL B. PostgreSQL C. SQLite D. MongoDB
3. Wohin wird das erzeugte Administratorpasswort außer in die Zusammenfassung geschrieben?
4. Richtig oder falsch: `exp:install` installiert ohne Rückfrage über eine bestehende Installation.
5. Warum darf `settings/override/` nie von der Live-Website in die Entwicklungsinstallation kopiert werden?

<details>
<summary>Antworten</summary>

1. Composer installiert jedes Paket `se7enxweb/*` als Git-Klon und alles andere als Archiv.
2. **C.** SQLite, in einer einzigen Datei unterhalb von `var/storage/sqlite3/`.
3. In `var/log/initial-admin-password`, nur für den Eigentümer lesbar; löschen Sie die Datei, nachdem Sie das
   Passwort geändert haben.
4. **Falsch.** Ohne `--force` verweigert es die Arbeit.
5. Es enthält Datenbankpasswörter und hostspezifische Werte; jeder Rechner behält seine eigenen.

</details>

## Weiterführende Literatur

- [Mit Git entwickeln](../../../mit-git-entwickeln.md): Klone, Branches, auf dem neuesten Stand bleiben.
- [Eine Entwicklungsinstallation einrichten](../../../../../eng-us/help/extension-development/part-1-from-user-to-developer/setting-up-a-development-installation.md):
  dieselben Schritte mit Debug-Einstellungen.
- [7. Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md): jede Option von `exp:install`.
- [8. Die Website ausliefern](../../../../installation/08-die-website-ausliefern.md): Velocity, Apache, nginx und FrankenPHP.

## Verwandte Seiten

- [Modul 1: Die Entwicklungsinstallation](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
