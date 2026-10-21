# Exponential Velocity: Geschichte der Webserver-Engine

Exponential Velocity (`vc`, aus Exponential mit `exp:velocity` gesteuert) ist der PHP-Anwendungsserver, den Exponential für jede Phase von der Entwicklung bis zum Produktivbetrieb empfiehlt. Dieser Abschnitt erzählt Monat für Monat, wie er entstand und wo Sie jede Funktion, jede Einstellung und jeden Upgrade-Hinweis finden, die daraus hervorgingen. Lesen Sie ihn, wenn Sie Exponential unter Velocity betreiben oder wissen wollen, warum es eine Einstellung oder einen Standardwert gibt.

Er ist die Chronik des Repositorys `se7enxweb/exponential-velocity` (früherer Name `se7enxweb/qbix-webserver`; die beiden Änderungsverzeichnisse sind dieselbe Geschichte unter zwei Namen und werden hier einmal dokumentiert) und der kleinen Repositorys für Installer und Pakete daneben.

So nutzen Sie diesen Abschnitt:

- **Sie wollen wissen, was geschah und warum?** Lesen Sie die Monatsseiten der Reihe nach. Jede beginnt mit einem Absatz, einer Tabelle dessen, was ein Anwender danach tun konnte und vorher nicht, und der Geschichte des Monats; die letzte Tabelle führt *jede* Änderung des Zeitraums mit ihrer Einordnung auf.
- **Sie wollen eine Funktion nutzen?** Gehen Sie zu den Funktionsseiten unten; jede hat Einstellungen mit Standardwerten, Befehle, die funktionieren, und Grenzen.
- **Sie machen ein Upgrade?** Lesen Sie zuerst die [Upgrade-Hinweise](../../../eng-us/bc/6.0/velocity-engine-upgrade-notes.md), dann das [Changelog](../../../changelogs/extensions/exponential-velocity.md).
- **Sie suchen einen einzelnen Commit?** Die vollständige, maschinell erzeugte Liste steht im [Änderungsverzeichnis](../aenderungsverzeichnis/exponential-velocity.md) (und unter dem alten Namen im [Änderungsverzeichnis von qbix-webserver](../aenderungsverzeichnis/qbix-webserver.md)).

## Monatsseiten

| Seite | Zeitraum | Releases | Thema |
|---|---|---|---|
| [Juli 2026](2026-07.md) | 20. bis 30. Juli | keine | Das erste Release: ein Webserver in reinem PHP |
| [August 2026](2026-08.md) | 4. bis 31. August | keine | App-Modus, Octane-Worker, Protokollierung, Binärdateien |
| [September, erster Teil](2026-09a.md) | 7. bis 21. September | `v0.0.1` | Kompatibilität mit bestehenden PHP-Anwendungen, der Benchmark |
| [22. September](2026-09b.md) | ein Tag | `v0.0.2.1` bis `v0.0.4.14` | HTTP/2 und der Antwort-Cache |
| [23. September](2026-09c.md) | ein Tag | `v0.0.4.15` bis `v0.0.4.26` | Sicherheitskorrekturen, Plattformen, die Voreinstellung für Exponential |
| [24. September](2026-09d.md) | ein Tag | `v0.0.4.27` | HTTPS, dynamischer Pool, Shell, Konfigurationsbaum, Pakete |
| [25. bis 30. September](2026-09e.md) | sechs Tage | `v0.0.4.28` bis `v0.0.4.42` | Domains, Control Panel, Caches, Worker ohne Root, `sbin/`, uwebserver |
| [Oktober 2026](2026-10.md) | 1. Oktober | schließt `v0.0.4.42` ab | Buchführung zum Release |
| [Installer und Pakete](installer-und-pakete.md) | Dezember 2023 bis Juli 2026 | `exponential-legacy-installer` 2.2.1 bis 2.2.3 | Der Legacy-Installer und die Website-Pakete von `ezwebin` |

## Funktionsseiten

| Seite | Was sie behandelt |
|---|---|
| [Webserver Velocity](../../../eng-us/features/6.0/velocity-web-server.md) | Was er ist, wie Sie ihn starten, Voreinstellungen, WebSockets, was er ausliefert |
| [Control Panel und Dashboard](../../../eng-us/features/6.0/velocity-control-panel.md) | Anmeldung, Reiter, Domains, SSL, Cache, Protokolle, Passwörter, Zwei-Faktor-Authentifizierung |
| [Antwort-Cache](../../../eng-us/features/6.0/velocity-response-cache.md) | Einschalten, Lebensdauern, stale-while-revalidate, die Generationsmarke, der eigene Cache der Anwendung |
| [HTTPS und Zertifikate](../../../eng-us/features/6.0/velocity-https-certificates.md) | Selbst signiert, eigene Dateien, Let's Encrypt und andere ACME-Zertifizierungsstellen |
| [Q-Shell](../../../eng-us/features/6.0/velocity-q-shell.md) | Die ausklappbare Konsole in jeder Ansicht des Servers |
| [Pakete und Binärdateien](../../../eng-us/features/6.0/velocity-packages-and-binaries.md) | deb, rpm, Docker, statische Binärdateien und das Phar |
| [uwebserver](../../../eng-us/features/6.0/velocity-uwebserver.md) | Der kleine Server für statische Dateien in C |
| [Scheduler](../../../eng-us/features/6.0/velocity-scheduler.md) | Aufgaben wie bei Cron innerhalb des Servers |
| [Statische Dateien und Bilder](../../../eng-us/features/6.0/velocity-static-files-and-images.md) | Cache-Lebensdauern, Kompression, Verzeichnislisten, verkleinerte und umgewandelte Bilder |
| [WebSockets, Ereignisse und Räume](../../../eng-us/features/6.0/velocity-websockets-and-events.md) | socket.io, Räume, Server-Sent Events |
| [Discovery, Föderation und Bereitstellung](../../../eng-us/features/6.0/velocity-discovery-federation-and-deploy.md) | Dokumente unter `/.well-known/`, Weiterleiten von Ereignissen, `--deploy`, das experimentelle Mesh |

## Referenzseiten

| Seite | Was sie behandelt |
|---|---|
| [Worker-Pool](../../spezifikationen/6.0/velocity-worker-pool.md) | Worker, die Zygote, Speicher, Zurücksetzen zwischen Anfragen, die Kompatibilitätsschicht |
| [HTTP/2 und Sicherheit](../../spezifikationen/6.0/velocity-http2-und-sicherheit.md) | Was der Server ablehnt und warum, Grenzen für Anfragen, Header |
| [Einstellungen der Engine](../../spezifikationen/6.0/velocity-engine-einstellungen.md) | Jede Einstellung nach Bereich, mit Standardwert und Geltungsbereich: Protokollierung, Marke, Event-Loop, Cache zum Vorwärmen beim Start |
| [Engines von Velocity](../../../eng-us/bc/6.0/velocity-engines.md) | Eine Engine aus Exponential wählen und betreiben |
| [Velocity auf der Platte](../../../eng-us/bc/6.0/velocity-ondisk-layout.md) | Der Baum `/etc/vc` |
| [Das Engine-Archiv](../../../eng-us/bc/6.0/phar.md) | Exponential aus einem Phar betreiben |

## Ein Hinweis zu den Namen

Die Engine begann als Qbix-Webserver. Im Code sehen Sie weiterhin `Q_WebServer`, `qbixserver.php`, `qbixctl`, das Präfix `Q.` der Einstellungen und das URL-Präfix `/Q/`: Das sind Bezeichner, und sie bleiben, wie sie sind. Das Produkt heißt Velocity; das Composer-Paket ist `se7enxweb/exponential-velocity`.

## Verwandte Seiten

- [Geschichte von Exponential](../README.md): die Monatschronik der Hauptinstallation
- [Engines von Velocity](../../../eng-us/bc/6.0/velocity-engines.md): eine Engine aus Exponential wählen und betreiben
- [Upgrade-Hinweise zur Engine von Velocity](../../../eng-us/bc/6.0/velocity-engine-upgrade-notes.md) und das [Changelog](../../../changelogs/extensions/exponential-velocity.md)
- [Eine Website betreiben](../../anleitungen/betrieb-einer-website.md)
- [Glossar](../../../eng-us/glossary.md)
