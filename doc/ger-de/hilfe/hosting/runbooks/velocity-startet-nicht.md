---
title: "Runbook: Velocity startet nicht"
audience: hosting, admin
category: hosting
order: 90
summary: Was zu tun ist, wenn die Engine Exponential Velocity nicht hochkommt oder nicht antwortet - ihren Status und ihr Konsolen-Log lesen, dann die üblichen Ursachen (Port belegt, fehlendes Paket oder fehlende PHP-Erweiterung, Engine-Archiv, Zertifikat, Rechte, ein hängender Neustart) mit der Abhilfe für jede.
keywords: Runbook, Velocity, startet nicht, qbix, exp:velocity status, console.log, Port belegt, läuft bereits, Engine-Archiv, EnginePhar, sockets, pcntl, Zertifikat, Ports 80 443, root, Watchdog, Neustart, kill, runbook, will not start, port in use, already running, engine archive, certificate, watchdog, restart
type: help
---
# Runbook: Velocity startet nicht

Für die Bereitschaft, wenn die Engine Exponential Velocity einer Website nicht startet oder startet und nicht antwortet. Die Meldungen und
Abhilfen stammen aus [12.11 Velocity und der Webserver](../../../installation/12-fehlerbehebung.md#1211-velocity-und-der-webserver).

## Inhalt

1. [Erster Blick](#erster-blick)
2. [Ursachen und Abhilfen](#ursachen-und-abhilfen)
3. [Ein Neustart, der hängt](#ein-neustart-der-hängt)
4. [Nach dem Vorfall](#nach-dem-vorfall)

## Erster Blick

```bash
php bin/php/console exp:velocity status --allow-root-user
```

`status` meldet nur dann „running“, wenn der Server eine Anfrage beantwortet; sonst „not answering“, mit den gefundenen Problemen, und
Exit-Status 1. Lesen Sie dann, was der Serverprozess ausgegeben hat:

```bash
tail -n 50 var/vc/qbix/run/console.log
```

`var/vc/qbix/run/console.log` (`[ServerSettings] LogFile`) enthält die Startzeilen und den Grund, wenn ein Start von `qbix` nicht hochkommt.
Das Fehlerlog des Servers liegt in `var/vc/qbix/log/` (`<siteaccess>-error.log`).

Prüfen Sie außerdem, auf welche Engine die Website gesetzt ist: `[ServerSettings] Engine` in `settings/velocity.ini` (überschrieben in
`settings/override/velocity.ini.append.php`). Der mitgelieferte Standard ist `php`, der eingebaute Server von PHP für die Entwicklung; die
Produktion nutzt `qbix`:

```bash
php bin/php/console exp:velocity config get ServerSettings Engine --allow-root-user
```

## Ursachen und Abhilfen

| Meldung oder Symptom | Ursache | Abhilfe |
|---|---|---|
| `velocity: no server script at ...` | die Engine `qbix` braucht das Velocity-Paket, das nicht installiert ist | führen Sie `composer install` aus (das Paket ist erforderlich), oder installieren Sie `se7enxweb/exponential-velocity` `~0.0.4.54` (PHP 8.0 oder später), wie [12.11](../../../installation/12-fehlerbehebung.md#1211-velocity-und-der-webserver) beschreibt |
| `velocity: already running` | ein Server dieser Engine läuft bereits | `exp:velocity status`; `restart`, um Änderungen anzuwenden |
| `velocity: port ... is already in use by another process` | ein anderes Programm oder eine andere Engine hält den Port | halten Sie die andere Engine an (`exp:velocity stop --engine=...`) oder ändern Sie `Port`/`HTTPSPort` mit `exp:velocity config set` |
| `velocity: EnginePhar names an archive that does not exist` | `[ServerSettings] EnginePhar` zeigt auf ein Archiv, das nie gebaut oder entfernt wurde | `php bin/php/console exp:phar build` oder `exp:velocity config set ServerSettings EnginePhar disabled` |
| Die Ports 80 und 443 lassen sich nicht binden | Ports unter 1024 brauchen root | starten Sie Velocity als root (die Worker wechseln zu `[ServerSettings] User`), oder nutzen Sie Ports über 1024 hinter einem Proxy |
| eine fehlende PHP-Erweiterung in `console.log` | dem PHP der Kommandozeile fehlt `sockets`, `pcntl` oder `openssl` | `php bin/php/console exp:velocity ext check --allow-root-user` zeigt, was fehlt, `ext install-hint <ext>` den Installationsbefehl |
| das PHP der Maschine ist 8.0, und Composer lehnt die Engine ab | ein Engine-Release älter als 0.0.4.54, das PHP 8.1 brauchte | verlangen Sie `~0.0.4.54` oder später, das auf 8.0 läuft ([Einen Webserver wählen](../server/einen-webserver-waehlen.md)) |
| HTTPS zeigt ein selbstsigniertes Zertifikat | das konfigurierte Zertifikat fehlt, ist abgelaufen oder passt nicht zu seinem Schlüssel | `exp:velocity ssl show`; korrigieren Sie `[HTTPSSettings] Certificate` und `Key` ([Ablauf von Zertifikaten](ablauf-von-zertifikaten.md)) |
| Worker können nicht schreiben | Worker laufen als `[ServerSettings] User` und `Group` | setzen Sie sie oder korrigieren Sie das Eigentum; Worker laufen nie als root, sofern nicht `AllowRootWorkers=enabled` ([12.3](../../../installation/12-fehlerbehebung.md#123-berechtigungen-und-eigentümerschaft)) |
| es hat angehalten, und der Watchdog startet es nicht | `exp:velocity stop` oder `kill` hinterlässt `stopped.json`; der Watchdog lässt es dann absichtlich in Ruhe | `exp:velocity start`; `exp:velocity watchdog status` und `var/log/velocity-watchdog.log` sagen, was der Watchdog getan hat |

Um die genaue Kommandozeile zu sehen, die der Server ausführen würde, ohne ihn zu starten:

```bash
php bin/php/console exp:velocity command --allow-root-user
```

## Ein Neustart, der hängt

`exp:velocity restart` gelingt nur, wenn ein neuer Elternprozess antwortet, und läuft losgelöst, damit ein auf halbem Weg beendeter Aufrufer
den Server nicht unten lässt. Wenn ein Neustart nicht fertig wird:

1. `exp:velocity status` (antwortet ein neuer Elternprozess?).
2. Wenn ein Worker festhängt: `php bin/php/console exp:velocity kill --allow-root-user` (anhalten ohne Rückfrage, für einen festhängenden
   Worker), dann `exp:velocity start`.
3. Prüfen Sie erneut mit `status` und mit einer Anfrage an die Website.

> **Tipp**
> Während die Engine nicht läuft, kann dieselbe Installation von Apache oder nginx mit PHP-FPM ausgeliefert werden, wenn das auf der
> Maschine eingerichtet ist; siehe [Velocity neben Apache oder nginx](../server/velocity-neben-apache-oder-nginx.md).

## Nach dem Vorfall

- [ ] Leeren Sie nach dem Start den Antwort-Cache von Velocity: `php bin/php/console exp:velocity cache clear --allow-root-user`.
- [ ] Wenn die Ursache ein Absturz war, erwägen Sie den Watchdog (`exp:velocity watchdog install`, als root)
      ([Velocity für Hoster](../server/velocity-fuer-hoster.md)).
- [ ] Wenn die Ursache eine Änderung war (ein Ausrollen, eine geänderte Einstellung), halten Sie sie fest und korrigieren Sie das Verfahren
      ([Änderungen ausrollen](../betrieb/aenderungen-ausrollen.md)).

## Verwandte Seiten

- [Velocity für Hoster](../server/velocity-fuer-hoster.md)
- [Velocity-Worker und Pools](../server/velocity-worker-und-pools.md)
- [Website nicht erreichbar](website-nicht-erreichbar.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
