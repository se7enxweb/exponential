---
title: "Lektion 2.3: Eine Installation pro Teilnehmer mit exp:install"
audience: trainer, admin
category: training
order: 23
summary: Installieren Sie eine vollständige Schulungs-Website mit einem Befehl, prüfen Sie sie vorher mit --print und --dry-run, finden Sie das erzeugte Administratorpasswort und wiederholen Sie die Installation für alle Teilnehmenden auf dieselbe Weise. 45 Minuten.
keywords: exp:install, installation über die konsole, kickstarter, SQLite, probelauf, dry run, print, initial-admin-password, übungsumgebung, sevenx_multisite, ein befehl, console install, training lab, one command
type: help
---
# Lektion 2.3: Eine Installation pro Teilnehmer mit exp:install

Die sicherste Übungsumgebung gibt allen Teilnehmenden ihre eigene Installation: Niemand kann die Übung des Nachbarn
kaputt machen, und alle können Administrator sein. Exponential macht das günstig: `exp:install` installiert eine
vollständige Website auf SQLite mit einem Befehl, ohne dass vorher eine Datei geschrieben werden muss. Diese Lektion
bringt Trainern bei, den Befehl auszuführen, zu prüfen und seine Ausgabe zu lesen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 2: Die Übungsumgebung aufbauen](README.md) |
| Dauer | 45 Minuten (10 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer, die Übungsumgebungen vorbereiten |
| Voraussetzungen | [Lektion 2.2](den-code-fuer-einen-kurs-beschaffen.md): ein Verzeichnis mit dem Code |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Exponential mit `php bin/php/console exp:install` und seinen nützlichsten Optionen installieren;
- vor der Installation die Konfiguration mit `--print` und den ganzen Ablauf mit `--dry-run` prüfen;
- den Administrator-Login und das erzeugte Passwort finden und die Passwortdatei nach der ersten Anmeldung entfernen;
- erklären, warum der Befehl ohne `--force` nicht über eine vorhandene Installation installiert.

## Hinweise für Trainer

### So präsentieren Sie es

1. Zeigen Sie den Befehl in seiner einfachsten Form (3 Minuten): `php bin/php/console exp:install` installiert das
   Seitenpaket `sevenx_multisite` auf SQLite (`var/storage/sqlite3/exponential.db`) mit dem Administrator `admin`
   und einem erzeugten Passwort, das am Ende einmal angezeigt wird.
2. Zeigen Sie `--print` (2 Minuten): Es gibt die Konfiguration aus, Passwörter maskiert, und schreibt nichts.
3. Zeigen Sie `--dry-run` (2 Minuten): Es prüft die Datenbank und holt die Pakete in ein temporäres Repository und hört
   dann auf, bevor etwas geschrieben wird. Bei Erfolg endet es mit „Dry-run completed: database and remote packages
   verified. Stopped after SiteDetails, nothing written.“
4. Erklären Sie das Passwort (3 Minuten): Ohne `--password` wird ein Passwort mit 24 Zeichen erzeugt, in der
   Zusammenfassung angezeigt und nach `var/log/initial-admin-password` geschrieben (Modus 0600). Ändern Sie es nach der
   ersten Anmeldung und löschen Sie die Datei. Bekannte Passwörter wie `publish` oder `admin` werden nie installiert.

### Häufige Fragen

**Kann ich allen Teilnehmenden dasselbe Passwort geben?** Sie können mit `--password=` eines mit mindestens 10 Zeichen
übergeben, das nicht allgemein bekannt ist. Für einen Kurs ist es besser, jede Installation ihr eigenes erzeugen zu
lassen und die Teilnehmenden es als erste Übung ändern zu lassen.

**Was tut `--url`?** Es ist die Adresse, aus der die Links jedes Siteaccess gebildet werden. Für eine Übungsumgebung,
die auf demselben Rechner bereitgestellt wird, passt `--url=http://127.0.0.1:8087` zum Standardport des eingebauten
Servers (Lektion 2.4).

**Was, wenn die Installation fehlschlägt?** Sie gibt aus, welcher Schritt angehalten hat und warum, und lässt die
Website im Wartungsmodus. `var/log/setup.log` enthält die Einzelheiten; `php bin/php/maintenance.php off` beendet die
Wartung, nachdem die Ursache behoben ist.

### Stolperfallen

- **Als root ausführen.** Führen Sie den Befehl als der Benutzer aus, der die Website bereitstellen wird.
- **Im selben Verzeichnis erneut ausführen.** Der Befehl verweigert es: „This directory already holds an
  installation“. Das ist ein Sicherheitsnetz; Lektion 2.6 behandelt das Zurücksetzen.
- **Ein Passwort in der Shell-Historie.** Für Datenbankpasswörter ist die Umgebungsvariable `EXP_INSTALL_DB_PASSWORD`
  besser als eine Option (für SQLite nicht nötig).

## Praktische Übung

Im Verzeichnis `editor-course` aus Lektion 2.2 (oder in einem beliebigen frischen Code-Verzeichnis):

1. `php bin/php/console exp:install --print --url=http://127.0.0.1:8087 --title="Training site"` und lesen Sie die
   ausgegebene Konfiguration: Suchen Sie `Site_package`, `Type=sqlite3` und `EditorAccess`.
2. `php bin/php/console exp:install --dry-run --url=http://127.0.0.1:8087 --title="Training site"`. Warten Sie auf die
   Zeile „Dry-run completed“.
3. Installieren Sie: `php bin/php/console exp:install --url=http://127.0.0.1:8087 --title="Training site"`.
4. Lesen Sie die Zusammenfassung: die Adresse der Website, den Login der Administration und das Passwort. Kopieren Sie
   das Passwort an einen sicheren Ort.
5. `ls -l var/log/initial-admin-password` und `ls -l var/storage/sqlite3/`: Die Passwortdatei (nur für den Eigentümer
   lesbar) und die Datenbankdatei existieren.
6. Führen Sie den Installationsbefehl ohne `--force` noch einmal aus und lesen Sie die Ablehnungsmeldung.
7. Notieren Sie, wie lange die Installation auf diesem Rechner gedauert hat; das gehört in Ihre Kursplanung.

## Erwartetes Ergebnis

- Die Zusammenfassung der Installation zeigte die Adresse der Website, `admin` als Login und ein erzeugtes Passwort.
- `var/storage/sqlite3/exponential.db` existiert; `var/log/initial-admin-password` existiert mit dem Modus
  `-rw-------`.
- Der zweite Lauf ohne `--force` hat mit „This directory already holds an installation“ angehalten und nichts
  verändert.

## Quiz

1. Was installiert `exp:install`, wenn keine Option angegeben ist?
2. Was ist der Unterschied zwischen `--print` und `--dry-run`?
3. Wohin wird ein erzeugtes Administratorpasswort geschrieben, und was tun Sie nach der ersten Anmeldung mit dieser
   Datei?
4. Warum hält ein zweiter Lauf im selben Verzeichnis an?
5. Wird `publish` als Administratorpasswort akzeptiert?

<details>
<summary>Antworten</summary>

1. Das Seitenpaket `sevenx_multisite` auf SQLite, den Administrator `admin`, ein erzeugtes Passwort.
2. `--print` zeigt die Konfiguration ohne Prüfung der Datenbank oder des Netzes; `--dry-run` verbindet sich mit der
   Datenbank und holt die Pakete und hält dann vor dem Schreiben an
   ([Die Installation über die Konsole, 7.6](../../../../installation/07-installation-ueber-die-konsole.md#76-probelauf-und-ausgabe)).
3. In `var/log/initial-admin-password`; ändern Sie das Passwort und löschen Sie dann die Datei.
4. `settings/override/site.ini.append.php` enthält bereits `[DatabaseSettings]`; ohne `--force` verweigert der Befehl,
   eine Installation zu ersetzen.
5. Nein. Bekannte Passwörter werden durch ein erzeugtes ersetzt, und die Zusammenfassung sagt, warum.

</details>

## Weiterführende Literatur

- [Die Installation über die Konsole: exp:install](../../../../installation/07-installation-ueber-die-konsole.md): jede Option.
- [Installation mit einem Befehl](../../../../funktionen/6.0/installation-mit-einem-befehl.md): die Funktionsseite.
- [Der Kickstarter](../../../../installation/06-kickstarter.md): dieselbe Installation aus einer geprüften `kickstart.ini`.
- [Nach der Installation](../../../../installation/10-nach-der-installation.md): was als Nächstes zu tun ist.

## Verwandte Seiten

- [Modul 2: Die Übungsumgebung aufbauen](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
