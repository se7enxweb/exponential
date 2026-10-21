---
title: "Lektion 6.2: Die Cronjob-Seite"
audience: trainer, admin
category: training
order: 62
summary: Die Teilnehmenden lesen Setup > Cronjobs, führen einen Teil und ein einzelnes Skript aus dem Browser aus, verfolgen die Ausgabe, halten einen Teil an, setzen ihn fort und kopieren die Crontab-Zeile, die die Seite vorschlägt. 45 Minuten.
keywords: website-administration, cronjob-seite, Setup Cronjobs, teil starten, anhalten, fortsetzen, laufenden job anhalten, protokolle leeren, ausgabe, letzte läufe, crontab, managecronjobs, site administration, cronjobs page, Run part, Pause, Resume, Stop running job, Clear logs, Output, Recent runs
type: help
---
# Lektion 6.2: Die Cronjob-Seite

**Setup > Cronjobs** zeigt jeden Cronjob-Teil der Installation auf einer Seite: ob die Crontab ihn ausführt, seinen
Zeitplan in Worten, seine nächste und letzte Ausführung und den genauen Shell-Befehl dahinter. Administratoren können
einen Teil oder ein Skript für eine gewählte Website ausführen, die Ausgabe verfolgen, während er arbeitet, und einen
Teil während eines Release anhalten. Diese Lektion vermittelt die Seite als tägliches Werkzeug und behält die Shell
aus Lektion 6.1 als Ausweichweg.

> **Hinweis**
> Jede Schaltfläche auf dieser Seite wirkt sofort auf die Installation. Üben Sie nur auf Ihrer eigenen
> Schulungsinstallation.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 6: Cronjobs, Workflows und Benachrichtigungen](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Support-Mitarbeiter |
| Voraussetzungen | [Lektion 6.1](cronjob-teile-und-die-crontab.md); ein Administratorkonto auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- einen Teil unter **Setup > Cronjobs** finden und seinen Status, Zeitplan, seine nächste und letzte Ausführung lesen;
- einen Teil und ein Skript für eine gewählte Website ausführen und die **Ausgabe** verfolgen;
- einen Teil mit einer Notiz anhalten und fortsetzen und sagen, was ein angehaltener Teil für die Crontab bedeutet;
- die Crontab-Zeile kopieren, die die Seite für einen Teil vorschlägt, den nichts ausführt;
- die Richtlinie nennen, die eine Rolle braucht, um die Seite zu öffnen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Cronjobs: ausführen, einplanen und verfolgen](../../../../anleitungen/cronjobs.md), Abschnitte 3 bis 12, und
[Cronjob-Teile anhalten](../../../../../eng-us/features/6.0/cronjob-pause.md). Beschrieben wird die Seite des Designs admin4.

1. **Der Weg hinein** (2 Minuten). **Setup** im oberen Menü, dann **Cronjobs** in der linken Spalte
   (`/setup/cronjobs`). Nur Rollen mit der Richtlinie `setup/managecronjobs` sehen sie; eine neue Installation gibt sie
   der Rolle Administrator.
2. **Von oben nach unten** (5 Minuten). Die Seite hat **Jetzt** (untätig oder was läuft, mit
   **Laufenden Job anhalten**, **Protokolle leeren** und **Alle Teile anhalten**), **Übersicht**,
   **Starten und filtern**, **Ausgabe**, **Cronjob-Teile**, **Verfügbar, aber nicht aktiviert**, **Letzte Läufe**,
   **Pausen** und **Crontab**. Die Liste der Teile wechselt zwischen **Kompakt** (eine Zeile pro Teil, der Standard)
   und **Ausführlich** (Karten).
3. **Ausführen** (4 Minuten). Wählen Sie die Website unter **Für Website ausführen** und führen Sie dann einen Teil
   aus. Der Lauf ist ein eigener Prozess und läuft deshalb weiter, wenn die Seite geschlossen wird. Es läuft immer nur
   ein von der Seite gestarteter Job. Zeigen Sie, wie die Ausgabe dem Job live folgt.
4. **Anhalten** (2 Minuten). Ein angehaltener Teil wird von jedem Lauf ausgelassen, auch von dem der Crontab, bis er
   fortgesetzt wird. Eine in **Notiz zum Anhalten oder Fortsetzen** eingegebene Notiz geht mit der nächsten gedrückten
   Schaltfläche mit und erscheint unter **Pausen**.
5. **Crontab** (2 Minuten). Die Seite liest die Crontab des Benutzers, unter dem der Webserver läuft. Für Teile, die
   nichts ausführt, schlägt sie eine Zeile vor; **Crontab** ganz unten listet sie zum Einfügen bereit auf.

### Häufige Fragen

**Warum sind auf einer neuen Installation zwei Teile als „braucht Aufmerksamkeit“ markiert?** `unlock` und
`cluster_maintenance` stehen in `[AdminSettings] ForbiddenParts[]`: Sie sind destruktiv und gehören in die Hände
eines Administrators in einer Shell. Ihre Karten zeigen *Gesperrt*.

**Ich habe auf Laufenden Job anhalten geklickt, und das Skript lief noch einen Moment weiter.** Die Seite sendet
`SIGTERM`, nicht `SIGKILL`, damit das Skript seine Sperre freigeben und sauber enden kann.

**Löscht Protokolle leeren den Verlauf?** Nein. Es leert die gemeinsamen Ausgabe- und Fehlerlogs; die Logs der
einzelnen Teile und **Letzte Läufe** bleiben erhalten.

**Die Crontab führt einen Teil aus, aber die Seite zeigt Nicht geplant.** Die Zeile verwendet vermutlich einen Pfad
über einen symbolischen Link, oder sie steht in der Crontab eines anderen Benutzers als dem des Webservers. Siehe
Abschnitt 17 der Anleitung.

### Stolperfallen

- **Kein PHP-Programm für die Kommandozeile.** Meldet die Seite in Rot, dass kein Programm gefunden wurde, ist jedes
  **Starten** deaktiviert. Setzen Sie `cronjob.ini [AdminSettings] PhpCliPath` auf das CLI-Programm der PHP-Version
  der Website.
- **Apache und Velocity sind uneins.** Jeder Webserver liest die Crontab des Benutzers, unter dem er läuft, deshalb
  können die Zahlen bei *Geplant* abweichen. Das ist zu erwarten; sagen Sie es, bevor jemand fragt.
- **Ein Lauf, der scheinbar nichts tut.** Führt die Crontab in diesem Moment dasselbe Skript aus, überspringt die
  Sperre es. Suchen Sie in der Ausgabe nach *Cronjob part locked by other process*.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, als Administrator in der Administration angemeldet.

1. Öffnen Sie **Setup** im oberen Menü und wählen Sie **Cronjobs** in der linken Spalte.
2. Notieren Sie unter **Übersicht**, wie viele Teile es gibt, wie viele geplant sind und wie viele Aufmerksamkeit
   brauchen.
3. Schalten Sie die Liste auf **Ausführlich**. Suchen Sie die Karte des Teils **Frequent**. Lesen Sie seine
   Abzeichen, **Zeitplan** oder **Vorgeschlagener Zeitplan**, **Nächste Ausführung** und
   **Zuletzt von hier gestartet**.
4. Öffnen Sie die Liste der Skripte auf der Karte und lesen Sie, was jedes Skript tut.
5. Wählen Sie unter **Für Website ausführen** Ihren öffentlichen Siteaccess.
6. Klicken Sie auf der Karte **Frequent** auf **Teil starten**. Beobachten Sie, wie **Jetzt** auf *Läuft* wechselt und
   die **Ausgabe** dem Job folgt.
7. Öffnen Sie nach dem Ende **Letzte Läufe** und suchen Sie Ihren Lauf mit Dauer und Ergebnis.
8. Öffnen Sie wieder die Skripte von **Frequent** und klicken Sie nur neben `notification.php` auf **Starten**.
   Vergleichen Sie die Ausgabe mit Schritt 6.
9. Geben Sie in **Notiz zum Anhalten oder Fortsetzen** `Training: pause test` ein und klicken Sie beim Teil
   **Frequent** auf **Anhalten**. Lesen Sie die Karte und den Abschnitt **Pausen**.
10. Führen Sie in einem Terminal im Wurzelverzeichnis der Installation `php runcronjobs.php -s <site> frequent` aus und
    lesen Sie den Hinweis, den es ausgibt.
11. Klicken Sie zurück auf der Seite beim Teil **Frequent** auf **Fortsetzen**.
12. Öffnen Sie ganz unten **Crontab** und kopieren Sie die für **Frequent** vorgeschlagene Zeile in Ihre Notizen.
    Installieren Sie sie nur, wenn Ihr Trainer Sie darum bittet.

## Erwartetes Ergebnis

- Schritt 6: eine Meldung, dass der Teil für die gewählte Website als Prozess gestartet wurde, und die Ausgabe zeigt
  eine Kopfzeile mit dem Teil und dem Siteaccess, dann die Skripte.
- Schritt 7: Der Lauf steht unter **Letzte Läufe** mit *OK* oder einer Zahl von Problemen.
- Schritt 10: `runcronjobs.php` gab einen Hinweis aus, dass der Teil angehalten ist, mit der Notiz aus Schritt 9, und
  führte nichts aus.
- Nach Schritt 11 zeigt die Karte nicht mehr *Angehalten*.
- Der Trainer prüft das, indem er den Abschnitt **Pausen** einer Teilnehmerin oder eines Teilnehmers am Beamer
  öffnet: Er zeigt das Anhalten und das Fortsetzen mit der Notiz.

![Die Cronjob-Seite: jeder Teil mit seinen Skripten, dem letzten Lauf und Jetzt starten oder Anhalten](../../../../../eng-us/help/images/trainers/sa-cronjobs.webp)

## Quiz

1. Welche Richtlinie braucht eine Rolle, um **Setup > Cronjobs** zu öffnen?
2. Richtig oder falsch: Ein auf der Seite angehaltener Teil wird von der Crontab weiterhin ausgeführt.
3. Was sendet **Laufenden Job anhalten** an den Job?
   A. `SIGKILL` B. `SIGTERM` C. `SIGHUP` D. Nichts; es blendet den Job nur aus
4. Warum können Sie den Teil `unlock` nicht über die Seite ausführen?
5. Sie ändern **Für Website ausführen**. Was ändert sich sonst noch auf der Seite?

<details>
<summary>Antworten</summary>

1. `setup/managecronjobs` ([Cronjobs, Abschnitt 2](../../../../anleitungen/cronjobs.md#2-bevor-sie-beginnen)).
2. **Falsch.** Ein angehaltener Teil wird von jedem Lauf ausgelassen, egal wer ihn startet, bis er fortgesetzt wird
   ([Cronjob-Teile anhalten](../../../../../eng-us/features/6.0/cronjob-pause.md)).
3. **B.** Damit das Skript seine Sperre freigeben und sauber enden kann.
4. Er steht in `[AdminSettings] ForbiddenParts[]`, weil er destruktiv ist; führen Sie ihn, wenn es sein muss, in einer
   Shell aus.
5. Der Siteaccess in jedem Shell-Befehl, den die Seite zeigt, sodass ein kopierter Befehl dem entspricht, was
   **Starten** tun würde.

</details>

## Weiterführende Literatur

- [Cronjobs: ausführen, einplanen und verfolgen](../../../../anleitungen/cronjobs.md): jeder Bereich der Seite und eine
  Tabelle zur Fehlersuche.
- [Cronjob-Teile anhalten](../../../../../eng-us/features/6.0/cronjob-pause.md): wie Pausen gespeichert und gemeldet werden.
- [Die Cronjob-Konsole](../../../../../eng-us/features/6.0/cronjobs-console.md): die Referenz zur Funktion.
- [Die Cronjob-Konsole für Hoster](../../../hosting/betrieb/die-cronjob-konsole.md): dieselbe Seite aus Sicht eines
  Hosters.

## Verwandte Seiten

- [Modul 6: Cronjobs, Workflows und Benachrichtigungen](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
