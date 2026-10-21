---
title: Eine PHP-Änderung ausrollen
audience: government, admin, developer, hosting
category: public-sector
order: 260
summary: Schritt für Schritt - eine Änderung an einer PHP-Klasse, einem Operator oder an Einstellungen mit einem Befehl ausliefern, exp:velocity deploy, nach einem Probelauf für den Änderungsnachweis - Autoloads, INI-Caches, das richtige PHP-FPM, Velocity, dann die Seiten-Caches in der richtigen Reihenfolge.
keywords: Deployment, ausrollen, exp:velocity deploy, Probelauf, PHP-Änderung, Autoloads, PHP-FPM neu laden, Velocity neu starten, Reihenfolge der Caches, Änderungsnachweis, Kernel, Packer, deploy, dry run, PHP change, PHP-FPM reload, Velocity restart, cache order, change record, kernel, packer
type: help
---
# Eine PHP-Änderung ausrollen

Diese Aufgabe ist für Administratoren und Entwickler, die eine genehmigte Änderung an Code oder Einstellungen live
bringen. Ein Befehl führt jeden Schritt in der richtigen Reihenfolge aus und hält beim ersten Fehler an
([Bereitstellung, Abschnitt 7](../../../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Optionen](#optionen)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Änderung ist genehmigt und auf Staging getestet
  ([Staging und Genehmigung](../../betrieb/aktualisierungen-und-aenderungen/staging-und-freigabe.md)).
- Der neue Code liegt auf dem Server (Ihr Deployment des genehmigten Commits).
- Shell-Zugang im Installationsverzeichnis.

## Schritte

1. Probelauf für den Änderungsnachweis (gibt die vierzehn Schritte aus, jeden mit `DRY` markiert, und ändert nichts):

   ```bash
   ./console exp:velocity deploy --dry-run --allow-root-user
   ```

2. Ausführen:

   ```bash
   ./console exp:velocity deploy --allow-root-user
   ```

   Jeder Schritt gibt PASS, FAIL oder SKIP mit seiner Dauer aus; der Lauf hält beim ersten Fehler mit dem Exit-Status 1
   an.

Zu den Schritten gehören die Autoloads der Erweiterungen, die INI-Caches, die Template-Caches, der Schritt für PHP-FPM
(er wartet darauf, dass OPcache neu validiert, statt neu zu laden, sofern nicht ausdrücklich verlangt, weil ein
Neuladen jeden Pool dieser PHP-Version anhält), der Neustart von Velocity, der Inhalts-Cache, zuletzt der Antwort-Cache
von Velocity und Anfragen zum Vorwärmen für `[DeploySettings] WarmUpURL[]`.

## Optionen

| Option | Wann |
|---|---|
| `--kernel` | eine Klasse des Kernels wurde hinzugefügt oder umbenannt |
| `--fpm-reload` | `php.ini` oder der Pool hat sich geändert, oder Dateien wurden umbenannt |
| `--no-fpm` | nur Velocity |
| `--no-velocity` | nur Apache mit PHP-FPM |
| `--packer` | eine Serverfunktion von ezjscore oder gepackte Skripte und Stile haben sich geändert |

Welches PHP-FPM neu geladen wird, ergibt sich aus `[DeploySettings] PhpFpmService` (`auto` sucht den Pool, der nach dem
Hostnamen der Installation benannt ist): `./console exp:velocity config get DeploySettings PhpFpmService --allow-root-user`.

## Prüfen, ob es funktioniert hat

- Jeder Schritt PASS; bewahren Sie die Ausgabe mit dem Änderungsnachweis auf.
- Das geänderte Verhalten ist sichtbar; `./console exp:logs --tail=var/log/error.log --lines=20` zeigt nichts Neues.
- Wenn etwas kaputtgegangen ist: [Ein fehlerhaftes Deployment zurückrollen](../../betrieb/vorfaelle/fehlerhafte-bereitstellung-zuruecknehmen.md).

## Verwandte Seiten
- [Die Caches nach einem Deployment vorwärmen](die-caches-nach-einem-deployment-vorwaermen.md)
- [Caches nach einer Änderung an Einstellungen leeren](caches-nach-einer-aenderung-an-einstellungen-leeren.md)
- [Änderungen ausrollen (Hosting)](../../../hosting/betrieb/aenderungen-ausrollen.md)
- [Engines von Velocity: eine PHP-Änderung ausrollen](../../../../bc/6.0/velocity-engines.md#welches-php-fpm)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
