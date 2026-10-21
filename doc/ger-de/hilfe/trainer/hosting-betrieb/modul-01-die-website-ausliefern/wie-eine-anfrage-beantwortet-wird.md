---
title: "Lektion 1.1: Wie eine Anfrage beantwortet wird"
audience: trainer, hosting, admin
category: training
order: 11
summary: Die Teilnehmenden vergleichen die vier Wege, eine Exponential-Website auszuliefern, verfolgen eine Anfrage durch die Routing-Regeln und weisen mit vier curl-Anfragen nach, dass die Regeln aktiv sind. 40 Minuten.
keywords: hosting und betrieb, die website ausliefern, routing von anfragen, htaccess_root, front controller, index.php, Velocity, Apache, nginx, FrankenPHP, curl-prüfung, hosting and operations, serving the site, request routing, curl check
type: help
---
# Lektion 1.1: Wie eine Anfrage beantwortet wird

Ein installiertes Exponential ist ein Verzeichnis voller PHP-Dateien. Etwas muss Verbindungen annehmen, HTTP und HTTPS
sprechen, statische Dateien ausliefern und jede andere Anfrage an einen Front Controller weitergeben. Diese Lektion gibt
den Teilnehmenden die Landkarte, die sie für den ganzen Kurs brauchen: die vier Wege, eine Website auszuliefern, die
eine Liste von Routing-Regeln, die sie alle teilen, und eine schnelle Prüfung, die zeigt, ob ein Server sie wirklich
anwendet.

> **Hinweis**
> Jede Übung dieses Kurses läuft auf der eigenen Schulungsinstallation oder virtuellen Maschine der Person, nie auf
> einer Live-Website eines Kunden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 1: Die Website ausliefern](README.md) |
| Dauer | 40 Minuten (20 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende im Hosting |
| Voraussetzungen | Eine Schulungsinstallation pro Person, ausgeliefert auf ihrem Rechner (siehe [Eine Installation pro Person](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/one-install-per-learner.md)); ein Terminal mit `curl` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die vier Wege nennen, eine Website auszuliefern (Velocity mit der Engine `qbix`, FrankenPHP über Velocity, Apache mit
  PHP-FPM, nginx mit PHP-FPM), und sagen, welchen das Projekt empfiehlt;
- erklären, warum das Document Root das Wurzelverzeichnis der Installation ist;
- beschreiben, was mit einer Anfrage nach einer Punktdatei, einer PHP-Datei unterhalb des Wurzelverzeichnisses, einer
  Ressource und jedem anderen Pfad geschieht;
- mit vier `curl`-Anfragen prüfen, dass die Routing-Regeln auf einem Server aktiv sind.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Kapitel 8: Die Website ausliefern](../../../../installation/08-die-website-ausliefern.md), Abschnitte
[8.1](../../../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle) und
[8.2](../../../../installation/08-die-website-ausliefern.md#82-wie-eine-anfrage-beantwortet-wird).

1. **Die Wahl in einer Tabelle** (6 Minuten). Zeigen Sie die Tabelle aus Abschnitt 8.1 am Beamer. Weisen Sie auf drei
   Zeilen hin: *Separate web server needed* (nein bei Velocity und FrankenPHP), *Application kept in memory between
   requests* (nur Velocity) und *Suits shared hosting* (meist nur Apache mit PHP-FPM). Die Engine `qbix` von Velocity ist
   in jeder Phase der empfohlene Weg; FrankenPHP ist eine produktionsreife Alternative; Apache und nginx mit PHP-FPM
   sind die traditionellen Einrichtungen.
2. **Wann man einen traditionellen Server wählt** (4 Minuten). Aus Abschnitt 8.1: Hosting, das lang laufende Prozesse
   verbietet, ein abgestimmter Apache oder nginx, den der Kunde behalten möchte, oder Kunden, die auf Ebene des
   Betriebssystems voneinander isoliert sein müssen. (Ein Rechner mit nur PHP 8.0 steht nicht mehr auf der Liste:
   Velocity läuft seit seinem Release 0.0.4.54 auf 8.0.)
3. **Eine Routing-Liste** (6 Minuten). Zeichnen Sie das Diagramm aus Abschnitt 8.2. Die Regeln sind einmal in
   `.htaccess_root` aufgeschrieben; die Engines von Velocity und die erzeugte FrankenPHP-Konfiguration setzen dieselbe
   Liste um. Betonen Sie die zwei Sicherheitsfolgen: Keine PHP-Datei läuft, weil ihr Pfad angefragt wurde (außer den
   Front Controllern im Wurzelverzeichnis), und eine Datei außerhalb der Ressourcenliste wird nie ausgeliefert.
4. **Die Prüfung** (4 Minuten). Zeigen Sie die vier `curl`-Anfragen aus Abschnitt 8.2 gegen Ihre eigene
   Schulungs-Website und erklären Sie jede erwartete Antwort.

### Häufige Fragen

**Warum ist das Document Root kein Verzeichnis `public/`?** Das Wurzelverzeichnis der Installation ist unter jedem
Server das Document Root; die Routing-Regeln entscheiden, was erreichbar ist. Ein `404` für jede Ressource unter
`design/` ist das Zeichen, dass das Document Root falsch gesetzt ist.

**Kann ich den eingebauten Server von PHP verwenden?** Er ist die dritte Engine von Velocity (`php`) und nur deshalb
der ausgelieferte Standard, weil er keine Installation braucht. Er hat kein TLS und ist für die Entwicklung gedacht,
nicht für Besucher.

**Können zwei Server dieselben Dateien ausliefern?** Ja. Velocity neben Apache während einer Migration ist eine häufige
Form; dann sind die Regeln zum Dateieigentum aus [Abschnitt 8.7.2](../../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)
wichtig (Lektion 1.2).

**Wo sind die Regeln für nginx?** Das Projekt liefert keine mit; Kapitel 8 gibt einen abgeleiteten Server-Block an,
Regel für Regel aus `.htaccess_root` übersetzt (Lektion 1.2).

### Stolperfallen

- **Ein `200` für `/.git/config`.** Die Regeln sind nicht aktiv: Apache ohne `.htaccess` oder `AllowOverride`, oder ein
  nginx-Block, der nicht der aus dem Kapitel ist. Halten Sie an und beheben Sie das vor allem anderen; das ist das
  wichtigste Ergebnis der Lektion.
- **Falscher Port.** Die Teilnehmenden kopieren `127.0.0.1:8088` aus dem Buch, während ihre Website woanders läuft.
  Lassen Sie sie zuerst `php bin/php/console exp:velocity status` ausführen; es listet jede Adresse als vollständige
  URL auf.
- **Die Engine qbix antwortet bei Punktpfaden mit 403, nicht mit 404.** Beides bedeutet, dass die Datei verweigert
  wurde.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, ausgeliefert auf Ihrem Rechner (zum Beispiel mit
`php bin/php/console exp:velocity start` nach `exp:install`, siehe
[Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md)).

1. Wechseln Sie in einem Terminal in das Wurzelverzeichnis der Installation (das Verzeichnis, das `index.php` enthält).
2. Führen Sie `php bin/php/console exp:velocity status` aus und notieren Sie die Adresse der Website.
3. Öffnen Sie `.htaccess_root` in einem Textbetrachter (`less .htaccess_root`). Suchen Sie die Regel für Punktdateien
   und die letzte Regel, die alles andere an `index.php` schickt.
4. Fragen Sie eine Punktdatei an und ersetzen Sie dabei die Adresse durch Ihre eigene:

   ```bash
   curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8088/.git/config
   ```

5. Fragen Sie eine Quelldatei des Kernels an:

   ```bash
   curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8088/kernel/classes/ezcontentobject.php
   ```

6. Fragen Sie auf dieselbe Weise `robots.txt` an und notieren Sie den Code.
7. Fragen Sie nach der Einstellungsdatei und zählen Sie, ob ihr Inhalt zurückkam:

   ```bash
   curl -s http://127.0.0.1:8088/settings/site.ini | grep -c '^\[DatabaseSettings\]'
   ```

8. Fragen Sie einen Stylesheet-Pfad von der Startseite der Website an (kopieren Sie einen aus dem Seitenquelltext) und
   prüfen Sie, dass er mit `200` antwortet.
9. Notieren Sie jeweils in einem Satz, was mit den Anfragen der Schritte 4 bis 8 geschehen ist und warum.

## Erwartetes Ergebnis

- Schritt 4: `404` (oder `403` von der Engine `qbix`).
- Schritt 5: `404`; die PHP-Datei wurde weder ausgeführt noch geschickt.
- Schritt 6: `200`, wenn die Datei existiert.
- Schritt 7: `0`: Die Anfrage hat `index.php` erreicht, das mit einer Seite geantwortet hat, nicht mit der Datei.
- Schritt 8: `200` für einen aufgeführten Ressourcenpfad.
- Der Trainer prüft das, indem er zwei Teilnehmende bittet, ihre Codes vorzulesen und das Ergebnis von Schritt 7 zu
  erklären.

## Quiz

1. Welchen Weg, eine Website auszuliefern, empfiehlt das Projekt für jede Phase?
   A. Apache mit PHP-FPM B. nginx mit PHP-FPM C. Velocity mit der Engine `qbix` D. Den eingebauten Server von PHP
2. Was ist das Document Root einer Exponential-Website?
3. Eine Anfrage nach `/settings/site.ini` liefert den Inhalt der Datei. Was sagt Ihnen das?
4. Nennen Sie zwei Gründe, Apache oder nginx mit PHP-FPM statt Velocity zu wählen.
5. Richtig oder falsch: Eine `.php`-Datei im Design-Verzeichnis einer Erweiterung läuft, wenn ihr Pfad angefragt wird.

<details>
<summary>Antworten</summary>

1. **C.** Kapitel 8 nennt die Engine `qbix` von Velocity in jeder Phase den empfohlenen Weg.
2. Das Wurzelverzeichnis der Installation, das Verzeichnis, das `index.php` enthält (Abschnitt 8.2).
3. Die Routing-Regeln sind nicht aktiv; beheben Sie das am Server, bevor die Website von außen erreichbar ist.
4. Zwei beliebige aus: Hosting, das lang laufende Prozesse verbietet, ein vorhandener abgestimmter Server, Isolation
   zwischen Kunden auf Ebene des Betriebssystems (Abschnitt 8.1).
5. **Falsch.** Skripte und Punktpfade werden unterhalb des Wurzelverzeichnisses nie ausgeliefert oder ausgeführt, auch
   nicht in einem aufgeführten Ressourcenverzeichnis.

</details>

## Weiterführende Informationen

- [Kapitel 8: Die Website ausliefern](../../../../installation/08-die-website-ausliefern.md): die Referenz für dieses Modul.
- [Einen Webserver wählen](../../../hosting/server/einen-webserver-waehlen.md): die Entscheidungshilfe für Hoster.
- [Was das Hosting von Exponential umfasst](../../../hosting/grundlagen/was-das-hosting-von-exponential-umfasst.md): das größere
  Bild für Hosting-Mitarbeitende.
- [Anforderungen auf einen Blick](../../../hosting/grundlagen/anforderungen-auf-einen-blick.md): Anforderungen an PHP und
  Erweiterungen.

## Verwandte Seiten

- [Modul 1: Die Website ausliefern](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
