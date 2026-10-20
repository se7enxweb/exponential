---
title: "Kurzkarte für die Übungsumgebung"
audience: trainer, admin
category: training
order: 6
summary: Alle Befehle, die ein Trainer braucht, um eine Schulungsinstallation aufzubauen, bereitzustellen, zu prüfen, zurückzusetzen und zu entfernen, auf einer druckbaren Karte, mit der Seite, die jeden Befehl erklärt.
keywords: übungsumgebung, kurzkarte, spickzettel, befehle, exp:install, exp:velocity, ezcache, wartung, resetuserpassword, schulungsinstallation, druckbar, lab, quick card, cheat sheet, commands
type: help
---
# Kurzkarte für die Übungsumgebung

Eine Karte mit den Befehlen einer Schulungsumgebung, in der Reihenfolge, in der ein Kurs sie verwendet. Führen Sie sie
im Wurzelverzeichnis der Installation aus, als der Benutzer, dem die Dateien gehören. Jede Zeile nennt die Seite, die
sie erklärt. Drucken Sie die Karte für sich und Ihre Helfer aus.

## Die Karte

```text
EXPONENTIAL TRAINING LAB                                   run in the installation root, not as root

CHECK THE MACHINE
  php -v ; php -m                        PHP 8.0+ (Velocity included); intl, xsl, sqlite3, mbstring ...
  composer --version ; git --version

GET THE CODE (a fixed release)
  git tag -l 'v6.*' --sort=version:refname | tail -3
  composer create-project se7enxweb/exponential:<tag> <dir>          editor and admin courses
  git clone https://github.com/se7enxweb/exponential.git <dir>        developer courses
    git switch -c training <tag> ; composer install

INSTALL (SQLite, one command)
  php bin/php/console exp:install --print   --url=http://127.0.0.1:8087
  php bin/php/console exp:install --dry-run --url=http://127.0.0.1:8087
  php bin/php/console exp:install           --url=http://127.0.0.1:8087 [--package=sevenx_multisite_clean]
  cat var/log/initial-admin-password        then sign in, change it, and: rm var/log/initial-admin-password

SERVE
  php bin/php/console exp:velocity start | status | stop
  php bin/php/console exp:velocity config set PHPServerSettings Port 8091     a second site on one machine

WHEN SOMETHING BREAKS
  tail -n 50 var/log/error.log           the reference ERR-... of an error page is in here
  php bin/php/maintenance.php status | off
  php bin/php/ezcache.php --clear-tag=template | --clear-tag=ini | --clear-all
  php bin/php/resetuserpassword.php -u admin -g --allow-root-user            (as root)

RESET FOR THE NEXT GROUP
  exp:velocity stop ; cp -a <golden>/storage/. var/storage/ ; php bin/php/ezcache.php --clear-all
  or: pwd (check!) ; exp:install --dry-run ... ; exp:install --force ...

AFTER THE COURSE
  stop servers; remove learner accounts on shared sites; delete lab directories you created
```

## Wo jeder Befehl erklärt wird

| Befehle | Seite |
|---|---|
| Einen Rechner prüfen | [Lektion 2.1: Anforderungen an einen Schulungsrechner](../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/requirements-for-a-training-machine.md), [Anforderungen](../../installation/02-voraussetzungen.md) |
| Den Code beschaffen | [Lektion 2.2: Den Code für einen Kurs beschaffen](../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/getting-the-code-for-a-class.md), [Den Code beschaffen](../../installation/03-den-code-beschaffen.md) |
| Installieren | [Lektion 2.3: Eine Installation pro Teilnehmer](../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/one-install-per-learner.md), [Die Installation über die Konsole](../../installation/07-installation-ueber-die-konsole.md) |
| Bereitstellen | [Lektion 2.4: Die Websites der Übungsumgebung bereitstellen](../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md), [Die Website bereitstellen](../../installation/08-die-website-ausliefern.md) |
| Fehler beheben | [Lektion 4.4: Fehlersuche im Kurs](../../../eng-us/help/trainers/train-the-trainer/module-04-delivering/troubleshooting-in-class.md), [Fehlerbehebung](../../installation/12-fehlerbehebung.md) |
| Zurücksetzen | [Lektion 2.6: Die Übungsumgebung zurücksetzen](../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/resetting-the-lab.md) |
| Aufräumen | [Lektion 4.5: Sicherheit und Datenschutz im Kurs](../../../eng-us/help/trainers/train-the-trainer/module-04-delivering/security-and-privacy-in-class.md) |

> **Warnung**
> `exp:install --force` ersetzt die Datenbank und die Einstellungen der Installation, in der es läuft. Führen Sie es
> nur in einem Verzeichnis aus, das Sie für die Schulung angelegt haben, und prüfen Sie vorher mit `pwd`, wo Sie sind.

## Verwandte Seiten

- [Trainer: hier beginnen](trainer-hier-beginnen.md)
- [Trainer-Werkzeugkasten](trainer-werkzeugkasten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
