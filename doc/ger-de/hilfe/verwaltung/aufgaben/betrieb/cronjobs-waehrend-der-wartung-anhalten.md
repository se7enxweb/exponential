---
title: Cronjobs während der Wartung anhalten
audience: government, admin, hosting
category: public-sector
order: 120
summary: Schritt für Schritt - einen oder alle Teile der Cronjobs während eines Release oder einer Migration unter Setup > Cronjobs anhalten, mit einer Notiz zum Grund, damit kein Ausführender sie startet, und sie danach fortsetzen, ohne es zu vergessen.
keywords: Cronjobs anhalten, Alle Teile anhalten, fortsetzen, Wartung, Release, Migration, cronpart_deactivated, PauseSettings, Setup Cronjobs, mehrere Server, pause cronjobs, Pause all parts, resume, maintenance, release, migration, several servers
type: help
---
# Cronjobs während der Wartung anhalten

Diese Aufgabe ist für Administratoren, die ein Wartungsfenster, eine Datenmigration oder einen Neuaufbau eines Index
eröffnen. Ein angehaltener Teil wird von `runcronjobs.php` ausgelassen, wer ihn auch startet (die Crontab, die Konsole,
Velocity), ohne die Crontab zu bearbeiten ([Teile der Cronjobs anhalten](../../../../../eng-us/features/6.0/cronjob-pause.md)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Anhalten](#anhalten)
3. [Fortsetzen](#fortsetzen)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- `setup/managecronjobs`.
- Wissen Sie, auf welche Teile es ankommt: Das Anhalten von `frequent` stoppt für diese Zeit Freigaben,
  Benachrichtigungen sowie die Warnungen und die Indizierung des Audits.

## Anhalten

1. **Setup > Cronjobs**.
2. Klicken Sie auf der Karte eines Teils auf **Anhalten** (oder **Alle Teile anhalten**) und geben Sie eine Notiz an wie
   „Upgrade-Fenster, Änderung <Referenz>“.
3. Ein Teil, der angehalten wird, während er läuft, beendet das laufende Skript und lässt die Skripte danach aus.

Die Karte zeigt dann **Angehalten**, seit wann, von wem und die Notiz; die Übersicht zählt angehaltene Teile.

## Fortsetzen

Gleich nach dem Fenster setzen Sie die Teile auf derselben Seite fort; führen Sie dann den häufigen Teil einmal aus
([Einen Teil der Cronjobs von Hand ausführen](einen-teil-der-cronjobs-von-hand-ausfuehren.md)), damit Freigaben und Warnungen aufholen.

## Prüfen, ob es funktioniert hat

- Während des Anhaltens: `php runcronjobs.php --siteaccess=site frequent --allow-root-user` gibt „Notice: The cronjob
  part 'frequent' is paused (Setup > Cronjobs). Paused since ... by ...: <note>“ aus und endet mit 0.
- Nach dem Fortsetzen: Die Karten zeigen kein Kennzeichen **Angehalten**; die Kachel der Setup-Übersicht zählt keine
  angehaltenen Teile mehr.

## Fallstricke

- Mehrere Webserver: Die Dateien zum Anhalten sind auf jedem Server lokal, sofern `cronjob.ini [PauseSettings] Directory`
  nicht auf ein gemeinsames Verzeichnis zeigt.
- Ein vergessenes Anhalten stoppt Freigaben und Warnungen stillschweigend; setzen Sie „Cronjobs fortsetzen“ auf die
  Checkliste zum Abschluss des Fensters.

## Verwandte Seiten
- [Die Site für ein Upgrade in den Wartungsmodus versetzen](die-site-fuer-ein-upgrade-in-den-wartungsmodus-versetzen.md)
- [Die Site wieder online bringen](die-site-wieder-online-bringen.md)
- [Überwachung der Cronjobs](../../../../../eng-us/help/government/operations/monitoring/cronjob-monitoring.md)
- [Anleitung zu den Cronjobs](../../../../anleitungen/cronjobs.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
