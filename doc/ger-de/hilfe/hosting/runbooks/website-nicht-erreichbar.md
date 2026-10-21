---
title: "Runbook: Website nicht erreichbar"
audience: hosting, admin
category: hosting
order: 20
summary: Was zu tun ist, wenn eine Exponential-Website Fehler oder gar nichts liefert - die Fehlerseite erkennen, die Engine, die Datenbank, den Wartungsmodus, vendor und Berechtigungen prüfen, das richtige Log lesen, beheben und prüfen.
keywords: Runbook, Website nicht erreichbar, Ausfall, 500, 503, Fehlerseite, leere Seite, Datenbank nicht erreichbar, Wartungsmodus, vendor fehlt, Velocity, PHP-FPM, error.log, Berechtigungen, runbook, site down, outage, error page, blank page, database unavailable, maintenance mode, vendor missing, permissions
type: help
---
# Runbook: Website nicht erreichbar

Für die Bereitschaft, wenn die Exponential-Website eines Kunden keine Seiten ausliefert. Arbeiten Sie von oben nach unten; hören Sie auf,
wenn die Ursache gefunden ist. Die Fehlerseiten und ihre Ursachen stammen aus
[12.9 Seiten und Fehler zur Laufzeit](../../../installation/12-fehlerbehebung.md#129-seiten-und-fehler-zur-laufzeit).

## Inhalt

1. [Symptome](#symptome)
2. [Die ersten fünf Minuten](#die-ersten-fünf-minuten)
3. [Welche Seite sieht der Besucher?](#welche-seite-sieht-der-besucher)
4. [Überhaupt nichts antwortet](#überhaupt-nichts-antwortet)
5. [Nach dem Vorfall](#nach-dem-vorfall)

## Symptome

- Die externe Prüfung löst einen Alarm aus.
- Kunden melden eine Fehlerseite (`Something went wrong`, `We'll be right back`), eine leere Seite oder einen Verbindungsfehler des
  Browsers.

## Die ersten fünf Minuten

1. Öffnen Sie die Website selbst und notieren Sie genau, was sie zeigt, mit dem HTTP-Status:

   ```bash
   curl -sI https://www.example.com/ | head -5
   ```

2. Öffnen Sie auch die Administration: Scheitert sie auf dieselbe Weise?
3. Lesen Sie die neuesten Fehler:

   ```bash
   ./console exp:logs --tail=var/log/error.log --lines=50
   ```

4. Prüfen Sie die Maschine: Speicherplatz (`df -h`), Arbeitsspeicher, Last. Eine volle Festplatte verursacht die meisten Symptome unten;
   siehe [Festplatte voll](festplatte-voll.md).

## Welche Seite sieht der Besucher?

| Der Besucher sieht | Ursache | Tun Sie |
|---|---|---|
| `We'll be right back` (503, `Retry-After: 30`) | die Datenbank war nicht erreichbar | prüfen Sie, dass der Datenbankserver läuft; prüfen Sie die Zugangsdaten in `settings/override/site.ini.append.php`; die Log-Zeile enthält die Meldung des Treibers |
| `Down for maintenance` (503) | der Wartungsmodus ist an | `php bin/php/maintenance.php status --allow-root-user`; wenn die Arbeit erledigt ist: `php bin/php/maintenance.php off --allow-root-user`; das Löschen von `var/maintenance.json` beendet ihn, was auch immer sonst falsch ist |
| `The site is missing the software libraries it needs` (503) | `vendor/` fehlt, wurde verschoben oder umbenannt | legen Sie `vendor/` zurück oder führen Sie `composer install --no-dev` aus; dann `php bin/php/ezcache.php --clear-all`, und starten Sie Velocity neu, wenn es die Website ausliefert |
| `Something went wrong on our side` (500) mit `ERR-...` | ein nicht abgefangener Fehler | `./console exp:logs --reference=ERR-...`; die Zeile nennt Meldung, Datei und Zeile ([Einen Fehler über seine Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)) |
| Eine leere Seite, oder 500 ohne etwas in `error.log` | PHP scheiterte, bevor Exponential protokollieren konnte: Syntaxfehler, fehlende Erweiterung, Speicher erschöpft | lesen Sie das Fehlerlog des Webservers oder von PHP-FPM; `php -l` auf einer kürzlich geänderten Datei |
| 404 für jede Seite unter Apache | `.htaccess` wird nicht gelesen oder `mod_rewrite` ist aus | erlauben Sie Overrides, aktivieren Sie `mod_rewrite` ([Apache mit PHP-FPM](../server/apache-mit-php-fpm.md)) |

> **Tipp**
> Fehler, die direkt nach einem Ausrollen oder einem Update begonnen haben: Die Ursache liegt fast immer in dieser Änderung. Unter Velocity
> braucht eine geänderte Klasse oder Einstellung `exp:velocity restart` oder `exp:velocity deploy`; siehe
> [Änderungen ausrollen](../betrieb/aenderungen-ausrollen.md).

## Überhaupt nichts antwortet

Die Verbindung wird abgelehnt oder läuft in eine Zeitüberschreitung.

**Unter Velocity:**

```bash
php bin/php/console exp:velocity status --allow-root-user
```

`status` meldet nur dann „running“, wenn der Server eine Anfrage beantwortet; sonst gibt es die gefundenen Probleme aus und endet mit 1.
Wenn er nicht läuft, starten Sie ihn (`exp:velocity start`) und lesen Sie `var/vc/qbix/run/console.log`, wenn er nicht hochkommt; siehe
[Velocity startet nicht](velocity-startet-nicht.md). Ein Server, der läuft, aber nicht antwortet: `exp:velocity restart`, und wenn ein
Worker festhängt, `exp:velocity kill` und dann `start`.

**Unter Apache oder nginx mit PHP-FPM:**

- Läuft der Webserver? Läuft der PHP-FPM-Pool der Website? (`systemctl status <service>`; auf Plesk ist der Dienst `plesk-php<XY>-fpm`.)
- Sind alle PHP-FPM-Kinder beschäftigt? Die Statusseite oder das Log des Pools sagt es; siehe [Langsame Website](langsame-website.md).

**Davor:** Ein Load Balancer, Reverse Proxy, CDN oder eine Firewall-Regel könnte der Teil sein, der scheitert. Testen Sie den Origin direkt.

## Nach dem Vorfall

- [ ] Leeren Sie die Caches, die die Abhilfe braucht, und mit Velocity seinen Antwort-Cache **nach** jedem Neustart
      (`php bin/php/console exp:velocity cache clear --allow-root-user`).
- [ ] Prüfen Sie: Startseite, Administration, eine Anmeldung, eine Veröffentlichung.
- [ ] Schreiben Sie die Notiz zum Vorfall: Beginn, Erkennung, Ursache, Abhilfe, Ende, was eine Wiederholung verhindert.
- [ ] Informieren Sie den Kunden.
- [ ] Wenn die Ursache ein Fehler in Exponential ist, melden Sie ihn ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)).

## Verwandte Seiten

- [Langsame Website](langsame-website.md)
- [Velocity startet nicht](velocity-startet-nicht.md)
- [Kapitel 12: Fehlersuche](../../../installation/12-fehlerbehebung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
