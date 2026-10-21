---
title: "Lektion 4.4: Fehlersuche im Kurs"
audience: trainer, admin
category: training
order: 44
summary: Beheben Sie die Probleme, die die Teilnehmenden am häufigsten aufhalten, Berechtigungen, eine Wartungsseite, veraltete Caches, ein verlorenes Passwort, mit dem Kapitel zur Fehlerbehebung, den Logs und dem Log-Viewer, ohne den Raum anzuhalten. 50 Minuten.
keywords: fehlersuche, fehlerbehebung, probleme im kurs, berechtigungen, wartungsmodus, caches, fehlerlog, log-viewer, fehlerreferenz, resetuserpassword, debug-ausgabe, troubleshooting, class problems, permissions, maintenance mode, error log, log viewer, error reference, debug output
type: help
---
# Lektion 4.4: Fehlersuche im Kurs

In einem praktischen Kurs geht immer etwas kaputt. Ein Trainer, der die fünf häufigsten Ursachen kennt und weiß, wo die
Logs sind, kann die meisten Probleme in Minuten beheben. Diese Lektion bereitet Trainer mit dem Kapitel zur
Fehlerbehebung des Installationshandbuchs und dem Log-Viewer vor.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 4: Einen Kurs durchführen](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer und ihre Helfer |
| Voraussetzungen | [Modul 2](../modul-02-die-uebungsumgebung-aufbauen/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den fünf Schritten von „Hier beginnen“ im Kapitel zur Fehlerbehebung folgen;
- einen Fehler in `var/log/error.log` über seine Referenz finden, auf der Kommandozeile und unter
  **Setup > Logdateien**;
- die häufigen Probleme der Übungsumgebung beheben: Dateieigentümer, Wartungsmodus, veraltete Caches, ein verlorenes
  Passwort;
- entscheiden, wann eine Teilnehmerin oder ein Teilnehmer stattdessen eine frische Installation bekommt.

## Hinweise für Trainer

### So präsentieren Sie es

1. Die fünf Schritte (5 Minuten, aus [Fehlerbehebung, 12.1](../../../../installation/12-fehlerbehebung.md#121-hier-beginnen)):
   Lesen Sie die Meldung genau; lesen Sie `var/log/error.log` (eine Fehlerseite zeigt eine Referenz wie
   `ERR-1A2B3C4D5E`, und dieselbe Referenz steht im Log); schalten Sie die Debug-Ausgabe nur für sich selbst ein;
   ändern Sie eine Sache und leeren Sie den kleinsten Cache, der sie abdeckt; arbeiten Sie auf der Kommandozeile als
   Eigentümer der Installation.
2. Die häufigen Probleme der Übungsumgebung (10 Minuten):

   | Symptom | Behebung |
   |---|---|
   | Fehler, nachdem ein Befehl als root ausgeführt wurde | `chown -R <web user>:<web group> var`; Skripte als Eigentümer ausführen |
   | Eine Wartungsseite nach einer fehlgeschlagenen Installation | `php bin/php/maintenance.php status`, dann `off` |
   | Eine Änderung wird nicht angezeigt | den kleinsten Cache leeren: `php bin/php/ezcache.php --clear-tag=template`, `--clear-tag=ini` oder `--clear-all` |
   | Verlorenes Administratorpasswort | `php bin/php/resetuserpassword.php -u admin -g --allow-root-user` (als root) |
   | Erzeugtes Passwort unbekannt | `var/log/initial-admin-password` (solange die Datei nicht gelöscht wurde) |

### Häufige Fragen

**Wann gebe ich auf und installiere neu?** Wenn die Behebung länger dauert als eine Neuinstallation (mit SQLite ein
paar Minuten). Behalten Sie die Notizen der Teilnehmenden; installieren Sie neu; lassen Sie sie mit dem Nachbarn
aufholen.

**Können die Teilnehmenden die Logs im Browser lesen?** Administratoren können es: **Setup > Logdateien** zeigt die
Logs mit maskierten privaten Werten; Redakteure können es nicht.

### Stolperfallen

- **Fünf Änderungen auf einmal.** Ändern Sie eine Sache und testen Sie dann.
- **Logs mit personenbezogenen Daten teilen.** Maskieren oder entfernen Sie E-Mail-Adressen und IP-Adressen, bevor Sie
  Logs irgendwo veröffentlichen.

## Praktische Übung

Auf Ihrer Schulungsinstallation:

1. Öffnen Sie **Setup > Logdateien** (`/setup/logs` im Siteaccess der Administration, zum Beispiel
   `/admin/setup/logs`) und öffnen Sie `error.log`. Suchen Sie die Überschriften **Steuerung**,
   **Zeilen von error.log** und **Aktionen**.
2. Führen Sie auf der Kommandozeile `tail -n 20 var/log/error.log` aus.
3. Simulieren Sie ein Wartungsfenster (nur auf Ihrer Schulungsinstallation):
   `php bin/php/maintenance.php on --until=5m`, öffnen Sie die Website und sehen Sie die Wartungsseite, dann
   `php bin/php/maintenance.php off` und `php bin/php/maintenance.php status`.
4. Leeren Sie mit `php bin/php/ezcache.php --clear-tag=template` ein Cache-Tag und lesen Sie die Ausgabe.
5. Listen Sie die Cache-Tags und -IDs auf: `php bin/php/ezcache.php --list-tags` und `--list-ids`.
6. Schreiben Sie für Ihre Helfer eine einseitige Karte „Korrekturen für die Übungsumgebung“ mit der Tabelle aus den
   Hinweisen für Trainer.

## Erwartetes Ergebnis

- Die Teilnehmenden haben `error.log` im Browser und auf der Kommandozeile gefunden.
- Die Wartungsseite ist erschienen und wieder verschwunden.
- Es gibt eine Karte „Korrekturen für die Übungsumgebung“ für die Helfer.

## Quiz

1. Eine Fehlerseite zeigt `ERR-1A2B3C4D5E`. Wo schauen Sie nach?
2. Seiten sind kaputt, nachdem ein Teilnehmer ein Skript als root ausgeführt hat. Was ist die wahrscheinliche Ursache,
   und wie beheben Sie sie?
3. Welcher Befehl leert jeden Cache?
4. Welcher Befehl setzt ein verlorenes Administratorpasswort zurück?

<details>
<summary>Antworten</summary>

1. In `var/log/error.log` (und seinen Rotationen `error.log.1` ...) oder unter **Setup > Logdateien** durch Suchen
   nach der Referenz ([Einen Fehler über seine Referenz finden](../../../../anleitungen/fehler-anhand-der-referenz-finden.md)).
2. Cache- oder Speicherdateien, die root gehören; `chown -R <web user>:<web group> var`.
3. `php bin/php/ezcache.php --clear-all`.
4. `php bin/php/resetuserpassword.php -u admin -g --allow-root-user` (als root), siehe
   [Ein Benutzerpasswort zurücksetzen](../../../../../eng-us/features/6.0/reset-user-password.md).

</details>

## Weiterführende Literatur

- [Fehlerbehebung](../../../../installation/12-fehlerbehebung.md)
- [Der Log-Viewer](../../../../../eng-us/features/6.0/log-viewer.md)
- [Einen Fehler über seine Referenz finden](../../../../anleitungen/fehler-anhand-der-referenz-finden.md)
- [Wenn etwas schiefgeht](../../../wenn-etwas-schiefgeht.md)

## Verwandte Seiten

- [Modul 4: Einen Kurs durchführen](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
