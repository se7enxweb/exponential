---
title: "Lektion 4.2: Entwickler, praktische Aufgabe"
audience: trainer, developer
category: training
order: 42
summary: Eine praktische Aufgabe von 75 Minuten für das Ende des Kurses Entwickler, eine kleine Erweiterung mit einer geschützten Modul-View und einem Template bauen, aktivieren, die Zugriffskontrolle mit einem Testbenutzer nachweisen, ein Template-Override hinzufügen, die Syntax prüfen und die Arbeit mit übereinstimmenden Versionsfeldern in ein lokales Git-Repository committen, bewertet mit einem Bewertungsraster aus sechs Kriterien. 105 Minuten mit Bewertung.
keywords: prüfungen und praktische übungen, entwickler, praktische aufgabe, bewertungsraster, erweiterung, modul, view, richtlinie, template, override, syntaxprüfung, assessments and practical exercises, developers, practical task, rubric, extension, module.php, FunctionList, policy, wash, eztemplatecheck, php -l, ezinfo.php, extension.xml, git commit
type: help
---
# Lektion 4.2: Entwickler, praktische Aufgabe

Die Aufgabe ist eine kleine, vollständige Erweiterung: eine Seite, die Besucher begrüßt, durch eine Richtlinie
geschützt, mit einem eigenen Template und mit korrekten Metadaten in Git verwaltet. Sie deckt den täglichen Ablauf von
Entwicklern ab: schreiben, prüfen, aktivieren, Cache leeren, testen.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Alle Teilnehmenden arbeiten auf ihrer
> eigenen Schulungsinstallation (einem Git-Klon, wie im Kurs Entwickler). Nichts wird irgendwohin gepusht und es wird
> kein Tag angelegt: Ein gepushter Tag ist endgültig.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 4: Die technischen Kurse prüfen](README.md) |
| Dauer | 105 Minuten (5 Einweisung, 75 Aufgabe, 25 Bewertung) |
| Niveau | Fortgeschrittene bis Experten |
| Zielgruppe | Teilnehmende am Ende des Kurses Entwickler |
| Voraussetzungen | [Lektion 4.1](entwickler-schriftlicher-test.md); eine Shell, ein Editor und Git auf der eigenen Schulungsinstallation |

## Lernziele

Wer diese Aufgabe besteht, hat gezeigt, dass er Folgendes kann:

- eine Erweiterung mit Metadaten, Einstellungen, einem Modul, einer View und einem Template schreiben;
- eine View mit einer Funktion schützen und sie über eine Rolle freigeben;
- die Erweiterung aktivieren und dafür sorgen, dass das System sie sieht (Autoloads, Caches);
- ein Template-Override für ein einzelnes Objekt schreiben und die Template-Syntax prüfen;
- die Arbeit mit konsistenten Versionsfeldern in Git verwalten.

## Hinweise für Trainer

### So führen Sie sie durch

1. **Vorher**: Alle Teilnehmenden haben eine Schulungsinstallation aus einem Git-Klon, über einen lokalen Port
   ausgeliefert, sind als Administrator angemeldet und haben ein Terminal. Der Leitfaden
   [Erweiterungen, Abschnitt 6.1](../../../../anleitungen/extensions.md#61-die-kleinste-funktionierende-extension-von-hand) darf
   geöffnet sein: Er zeigt eine ähnliche, aber einfachere Erweiterung.
2. **Einweisung**: Teilen Sie die Schritte und das Bewertungsraster aus; 75 Minuten. Die Teilnehmenden nennen die
   Erweiterung `<account>greet` (Kleinbuchstaben, Buchstaben, Ziffern und Unterstriche).
3. **Bewertung**: Lesen Sie die Dateien, führen Sie `php -l` darauf aus, öffnen Sie die Seite in einem privaten
   Fenster und als Testbenutzer und lesen Sie `git log` in der Erweiterung.

### Häufige Fragen

**Dürfen die Teilnehmenden den Code des Leitfadens kopieren?** Ja, und ihn anpassen: Die Aufgabe ist anders (eine
Richtlinie, ein Override, Git). Wer kopiert, ohne anzupassen, scheitert an den Schritten 4 und 6.

**Die Seite ist nach dem Aktivieren leer.** Ein neues Template braucht geleerte Caches; siehe
[Erweiterungen, Abschnitt 8](../../../../anleitungen/extensions.md#8-wenn-es-nicht-funktioniert). Das gehört zur Aufgabe.

### Stolperfallen

- **Velocity-Worker.** Läuft die Website auf Velocity, brauchen neue Klassen und Einstellungen einen Neustart oder ein
  Deployment; sagen Sie den Teilnehmenden, welchen Server ihre Übungsumgebung verwendet.
- **Eine gemeinsame Installation.** Zwei Teilnehmende, die auf einer Installation Erweiterungen aktivieren, kommen
  sich gegenseitig in die Quere. Eine Installation pro Teilnehmer.

## Praktische Übung

Die Aufgabe, wie sie an die Teilnehmenden ausgeteilt wird. Arbeiten Sie im Stammverzeichnis Ihrer eigenen
Schulungsinstallation.

1. Legen Sie `extension/<account>greet/` mit `ezinfo.php` und `extension.xml` an, beide mit der Version `0.1.0`, Ihrem
   Namen in der Copyright-Zeile und der Lizenz "GNU General Public License v2.0 (or any later version)".
2. Fügen Sie `settings/module.ini.append.php` und `settings/design.ini.append.php` hinzu, damit der Kernel Ihr Modul
   und Ihr Design findet.
3. Schreiben Sie ein Modul `greet` mit einer View `hello`, die einen Parameter `Name` annimmt und ihn über das Template
   `design/standard/templates/greet/hello.tpl` anzeigt. Geben Sie den Namen mit `|wash` aus.
4. Schützen Sie die View: Deklarieren Sie in `module.php` eine Funktion `read` und verlangen Sie sie für die View.
5. Prüfen Sie jede PHP-Datei mit `php -l` und das Template mit
   `php bin/php/eztemplatecheck.php -s<public siteaccess> <path to hello.tpl> --allow-root-user`.
6. Aktivieren Sie die Erweiterung (im Browser oder mit `exp:ini add`), erzeugen Sie die Autoloads neu und leeren Sie
   die Caches. Laden Sie den Serverprozess neu, wenn Ihre Übungsumgebung das braucht.
7. Öffnen Sie `/greet/hello/Ada` in einem privaten Fenster als anonymer Besucher. Notieren Sie, was Sie sehen.
8. Erstellen Sie eine Rolle `Greet readers` mit der Richtlinie `greet` `read`, weisen Sie sie der Gruppe Anonymous
   Users zu und öffnen Sie die Seite erneut. Notieren Sie, was Sie sehen. Probieren Sie dann
   `/greet/hello/<script>alert(1)</script>` und notieren Sie, was die Seite ausgibt.
9. Schreiben Sie im Design Ihrer Erweiterung ein Override von `node/view/full.tpl` für einen Artikel der Website (über
   die Objekt-ID), das über dem Inhalt eine Zeile `Override from <account>greet` einfügt. Tragen Sie die Regel in die
   Datei `settings/override.ini.append.php` Ihrer Erweiterung ein, leeren Sie die Override- und Template-Caches und
   prüfen Sie den Artikel.
10. Führen Sie in `extension/<account>greet/` den Befehl `git init` aus, fügen Sie Ihre Dateien hinzu und committen
    Sie mit der Nachricht `Added: Greeting page with access control`.
11. Erhöhen Sie die Version in `ezinfo.php` und `extension.xml` auf `0.1.1`, prüfen Sie beide Dateien und committen
    Sie mit `Updated: Version 0.1.1`. Legen Sie keinen Tag an und pushen Sie nicht.

## Erwartetes Ergebnis

- Schritt 7: Der Zugriff wird dem anonymen Besucher verweigert. Schritt 8: Die Seite zeigt **Hello, Ada**; der
  Skripttext wird als Text angezeigt, nicht ausgeführt.
- Der Artikel zeigt die Override-Zeile; die anderen Artikel nicht.
- `php -l` meldet keine Syntaxfehler; `eztemplatecheck.php` meldet das Template als gültig.
- **Setup > Extensions** listet die Erweiterung nach dem Leeren des Caches mit der Version 0.1.1 auf.
- `git log` zeigt zwei Commits mit den beiden Nachrichten; beide Versionsfelder lauten `0.1.1`; es gibt keinen Tag.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Aufbau und Metadaten der Erweiterung | Dateien fehlen | Dateien vorhanden, Metadaten widersprüchlich | Konsistent, Lizenz oder Copyright fehlt | Beide Dateien konsistent und vollständig | Die Dateien |
| Modul, View und Template | Seite funktioniert nicht | Seite funktioniert ohne den Parameter | Funktioniert, Name nicht mit wash ausgegeben | Funktioniert, Name mit wash ausgegeben | `/greet/hello/Ada` und der Skripttest |
| Zugriffskontrolle (Pflicht) | View ungeschützt | Funktion deklariert, nicht verlangt | Verlangt, Rolle nicht erstellt oder falsch zugewiesen | Vorher verweigert, nach der Rolle erlaubt | Schritte 7 und 8, die Seite der Rolle |
| Aktivierung und Prüfungen | Nicht aktiviert | Aktiviert, Caches oder Autoloads ausgelassen | Funktioniert, Prüfungen nicht ausgeführt | Aktiviert, `php -l` und Template-Prüfung ohne Fehler | Terminal-Verlauf, **Setup > Extensions** |
| Override | Keins | Datei ohne Regel oder Regel ohne Datei | Funktioniert, aber für jeden Artikel | Funktioniert nur für das eine Objekt | Der Artikel und ein anderer Artikel |
| Git und Versionen | Kein Repository | Ein Commit, Nachricht ohne Präfix | Zwei Commits, Versionen widersprechen sich | Zwei Commits mit Präfixen, beide Felder 0.1.1, kein Tag | `git log`, die Dateien |

**Bestanden**: 12 von 18 Punkten oder mehr und **Zugriffskontrolle** mit 2 oder 3.

## Quiz

Fragen für die Nachbesprechung nach der Bewertung.

1. Warum hat Schritt 7 den Zugriff verweigert, und was wäre ohne Schritt 4 geschehen?
2. Was hat `|wash` beim Skripttest bewirkt?
3. Warum sollen Sie in dieser Aufgabe keinen Tag anlegen?
4. Ihr Override erscheint bei jedem Artikel. Was ist an der Regel falsch?

<details>
<summary>Antworten</summary>

1. Die View verlangt die Funktion `read`, und keine Rolle hat sie gewährt; ohne Schritt 4 hätte jeder die View öffnen
   können ([Erweiterungen](../../../../anleitungen/extensions.md#61-die-kleinste-funktionierende-extension-von-hand)).
2. Es hat das HTML maskiert, sodass das Skript als Text ausgegeben wurde
   ([Templates und Design](../../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten)).
3. Ein gepushter Tag ist endgültig; Releases werden anhand des bereits Veröffentlichten gewählt und nach dem
   Versions-Commit getaggt ([Erweiterungen](../../../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).
4. Die Bedingung `Match[object]` fehlt oder ist falsch
   ([Templates und Design](../../../../anleitungen/templates-und-design.md#4-eine-änderung-mit-einem-override-vornehmen)).

</details>

## Weiterführende Literatur

- [Erweiterungen](../../../../anleitungen/extensions.md): die kleinste Erweiterung, Aktivierung, Release.
- [Templates und Design](../../../../anleitungen/templates-und-design.md): Overrides und Template-Prüfungen.
- [Mit Git entwickeln](../../../mit-git-entwickeln.md): die tägliche Arbeit mit Klonen.

## Verwandte Seiten

- [Lektion 4.1: Entwickler, schriftlicher Test](entwickler-schriftlicher-test.md)
- [Modul 4: Die technischen Kurse prüfen](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
