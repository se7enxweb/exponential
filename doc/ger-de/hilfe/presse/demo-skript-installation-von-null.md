---
title: "Demo-Skript: Installation von null"
audience: press
category: press-media
order: 160
summary: Ein zehnminütiges Videoskript von einem leeren Ordner bis zu einer laufenden Exponential-Website - Klonen, Composer, ein Probelauf, die Installation mit einem Befehl auf SQLite, die Website ausliefern, die erste Anmeldung und die Passwortänderung - mit den Befehlen aus den Installationskapiteln und den Momenten, die Sie herausschneiden oder unkenntlich machen müssen.
keywords: Demo-Skript, installieren, Installation, von null, exp:install, SQLite, Composer, git clone, Probelauf, erste Anmeldung, Passwort, Velocity, Video, Creator, demo script, install, installation, from zero, dry run, first sign-in, password
type: help
---
# Demo-Skript: Installation von null

Diese Seite ist für Creator, die eine Installation von Exponential aus dem Nichts filmen. Die Befehle stammen aus
[Mit Git entwickeln](../mit-git-entwickeln.md#eine-neue-site), [Exponential mit einem Befehl installieren](../../../eng-us/features/6.0/install-in-one-command.md)
und [Die Installation über die Konsole](../../installation/07-installation-ueber-die-konsole.md). `exp:install` gehört zur Linie 6.0.15, die in
Entwicklung ist; beim Release 6.0.14 würden Sie stattdessen den Einrichtungsassistenten oder den Kickstarter verwenden
([Eine Installationsmethode wählen](../../installation/04-installationsweg-waehlen.md)). Sagen Sie vor der Kamera, welche
Sie zeigen.

Das Skript dauert etwa zehn Minuten geschnittenes Video. Downloads dauern länger: Die Dokumentation nennt mehr als
27.000 Commits und etwa 370 MB für den Klon des Kernels und einige Minuten für die Erweiterungen; schneiden Sie das
Warten heraus.

## Inhalt

1. [Vor der Aufnahme](#vor-der-aufnahme)
2. [Das Skript](#das-skript)
3. [Momente zum Herausschneiden oder Unkenntlichmachen](#momente-zum-herausschneiden-oder-unkenntlichmachen)
4. [Wenn es vor der Kamera scheitert](#wenn-es-vor-der-kamera-scheitert)
5. [Beschreibungsblock](#beschreibungsblock)

## Vor der Aufnahme

```text
[ ] Ein Demo-Rechner oder eine virtuelle Maschine, die Sie löschen dürfen; PHP, Git 2.28+ und Composer 2 installiert
[ ] Voraussetzungen einmal geprüft (PHP-Version und -Erweiterungen), damit die Aufnahme nicht daran scheitert
[ ] Terminal: große Schrift, schlichter Prompt ohne echten Host- oder Benutzernamen, leerer Verlauf
[ ] Ein Webserver oder Velocity bereit, um den Ordner auszuliefern, in den Sie installieren
[ ] Sie haben das ganze Skript einmal außerhalb der Kamera durchgespielt und die Zeit gemessen
```

Voraussetzungen: [Voraussetzungen](../../installation/02-voraussetzungen.md). Die vollständige Einrichtung für Creator steht unter
[Eine Demo-Installation einrichten](eine-demo-installation-einrichten.md).

## Das Skript

| Schritt | Befehl oder Klick | Sagen Sie |
|---|---|---|
| 1 | Titelkarte | Offenlegung. „Von einem leeren Ordner zu einer laufenden Exponential-Website, mit <Version>.“ |
| 2 | `git clone https://github.com/se7enxweb/exponential.git exponential` | „Der Kernel, mit seinem ganzen Verlauf.“ |
| 3 | `cd exponential` und `git switch -c my-demo` | „Mein eigener Branch auf main, der Entwicklungslinie.“ (Für das Release: `git switch -c my-demo v6.0.14`.) |
| 4 | `composer install` | „Composer holt die Erweiterungen und Bibliotheken. Exponential braucht diesen Schritt, bevor es laufen kann.“ |
| 5 | `php bin/php/console exp:install --dry-run` | „Ein Probelauf prüft die Konfiguration und die Pakete und installiert nichts.“ |
| 6 | `php bin/php/console exp:install` | „Ohne Optionen: das Multisite-Paket, auf SQLite, in amerikanischem Englisch, ohne Datenbankserver.“ |
| 7 | Nur die erste Zeile der Ausgabe zeigen | „Es sagt, was es gleich tun wird.“ Dann zum Ende schneiden |
| 8 | Die Zusammenfassung mit unkenntlich gemachten Passwortzeilen zeigen | „Adresse der Website, Anmeldung zur Administration, Benutzername, Datenbank, Paket und Siteaccesses. Das erzeugte Passwort wird einmal ausgegeben und in eine Datei geschrieben, die nur der Eigentümer lesen kann.“ |
| 9 | Die Website ausliefern: Ihr Webserver oder `php bin/php/console exp:velocity start` | „Das Installationsprogramm startet keinen Webserver; ich liefere den Ordner aus.“ |
| 10 | Browser: die Anmeldeadresse der Administration aus der Zusammenfassung | „Die Anmeldung zur Administration.“ Halten Sie die Aufnahme an, während Sie das Passwort tippen |
| 11 | Benutzermenü, **Passwort ändern** | „Als Erstes: das erzeugte Passwort ändern und dann `var/log/initial-admin-password` löschen.“ |
| 12 | Das Dashboard, dann die Website | „Und hier ist die Website.“ |

Die Wahl der Datenbank, in der dokumentierten Reihenfolge der Präferenz: SQLite (der Standard), PostgreSQL, MongoDB,
MariaDB, Oracle, MySQL ([Exponential mit einem Befehl installieren](../../../eng-us/features/6.0/install-in-one-command.md#choose-a-database)).
Wenn Sie eine andere Datenbank zeigen, übergeben Sie das Passwort mit der Umgebungsvariablen `EXP_INSTALL_DB_PASSWORD`,
die es aus der Prozessliste und dem Shell-Verlauf heraushält, und tippen Sie es nie vor der Kamera.

## Momente zum Herausschneiden oder Unkenntlichmachen

| Moment | Warum |
|---|---|
| Die Zeilen `Password:` und der Block „Copy and paste“ der Zusammenfassung | Sie geben das Administratorpasswort aus |
| Jedes getippte `--password=` oder `--db-password=` | Passwörter in der Befehlszeile und im Verlauf |
| `cat var/log/initial-admin-password` | Führen Sie es nie vor der Kamera aus |
| Das Anmeldeformular, während Sie tippen | Halten Sie die Aufnahme an |
| Fehlerausgaben mit vollständigen Pfaden Ihres Rechners | Zuerst lesen; unkenntlich machen, was Sie nicht zeigen wollen |

Auch nachdem Sie es unkenntlich gemacht haben: Betrachten Sie ein Passwort, das während einer Aufnahme auf Ihrem
Bildschirm war, als verbrannt und ändern Sie es ([Geheimnisse vom Bildschirm fernhalten](geheimnisse-vom-bildschirm-fernhalten.md)).

## Wenn es vor der Kamera scheitert

| Symptom | Ursache und was zu tun ist |
|---|---|
| "This directory already holds an installation ..." | Eine frühere Aufnahme: Ergänzen Sie `--force`, nur auf diesem Demo-Rechner |
| "Unknown option or missing value" | Jeder Wert braucht `=`: `--db=mysql`, nicht `--db mysql` |
| Der Lauf bricht beim Holen der Pakete ab | Kein Netzwerkzugang zum Paketserver |
| Die Website zeigt nach einem gescheiterten Lauf die Wartungsseite | Beheben Sie die Ursache und starten Sie neu, oder `php bin/php/maintenance.php off` |
| Rechtefehler nach der Installation als root | Übergeben Sie die Dateien an den Benutzer des Webservers |

Vollständige Tabelle: [Was schiefgehen kann](../../installation/07-installation-ueber-die-konsole.md#711-was-schiefgehen-kann). Logs: `var/log/setup.log`.

## Beschreibungsblock

```text
Installiert Exponential <Version> von Grund auf mit einem Befehl auf SQLite, auf meinem eigenen Testrechner.
<Zeile zur Offenlegung>

Gezeigte Befehle:
  git clone https://github.com/se7enxweb/exponential.git exponential
  composer install
  php bin/php/console exp:install --dry-run
  php bin/php/console exp:install
Installation mit einem Befehl: <Link zu „Exponential mit einem Befehl installieren“>
Das erzeugte Passwort nach der ersten Anmeldung ändern: <Link zu „Das Standard-Administratorpasswort“>
```

**Vor der Kamera für eine öffentliche Demo installieren.** Wenn sich Zuschauer beim Ergebnis anmelden sollen, installieren Sie mit `EXP_DEMO_MODE=1` vor dem Befehl: Das veröffentlichte Passwort bleibt wie angegeben, und die Administration zeigt danach ein Banner **Demo-Modus**. Stellen Sie die Variable nicht als etwas dar, das jede Website setzen sollte. [FAQ zum Demo-Modus](../demomodus-faq.md).

## Verwandte Seiten

- [Eine Demo-Installation einrichten](eine-demo-installation-einrichten.md)
- [Das Standard-Administratorpasswort](../standard-administratorpasswort.md)
- [Demo-Skript: Velocity](demo-skript-velocity.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
