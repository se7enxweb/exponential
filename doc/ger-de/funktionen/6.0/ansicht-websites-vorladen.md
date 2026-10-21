# Seiten vorladen (Administrationsoberfläche)

> **Seit dem 6. Oktober 2026** ist die Seite neu gestaltet: ein Lauf zur Zeit, Fortschritt live, die letzten Läufe mit
> ihren Fehlern, ein Probelauf und eine Bildprüfung. Die aktuelle Anleitung ist
> [Caches vorwärmen](../../anleitungen/caches-vorwaermen.md).

Diese Seite ist für Administratoren, die die Caches einer Website nach einem Deployment oder dem Leeren eines Caches warm
haben möchten und als Zugabe eine Liste defekter Links wollen. **Setup > Seiten vorladen** durchläuft jede Seite einer
Website aus der Administrationsoberfläche und zeigt den Fortschritt live. Es ist das Gegenstück im Browser zum
Vorlader auf der Kommandozeile, beschrieben in [Vorlader für den Website-Cache](../../../eng-us/bc/6.0/preload.md), und verwendet
denselben Crawler.

## Verwenden

1. Öffnen Sie **Setup > Seiten vorladen** (`/setup/preload`).
2. Wählen Sie die **Vorzuwärmende Website**.
3. Setzen Sie **Seitenlimit** und **Link-Tiefe**.
4. Drücken Sie **Vorladen starten**. Der Fortschritt wird live in den Ausgabebereich geschrieben. **Anhalten** beendet
   den Lauf.
5. Lesen Sie am Ende die Zusammenfassung und den Bericht über defekte Links.

| Feld | Standard | Bedeutung |
|---|---|---|
| Vorzuwärmende Website | der aktuelle Siteaccess | Welcher Siteaccess durchlaufen wird |
| Seitenlimit | 250 | Die meisten Seiten, die ein Lauf holt. Ein Lauf, der an die Grenze stößt, sagt es und wie viele Seiten noch in der Warteschlange waren. |
| Link-Tiefe | 3 | Wie weit von der Wurzel der Website aus Links gefolgt wird |

Die Entsprechung auf der Kommandozeile:

```bash
php bin/php/preload.php --help --allow-root-user
```

Um einen Lauf aus der Shell zu prüfen, während er läuft:

```bash
ls -t var/*/preload/ | head
```

## Zugriff

Geben Sie einer Rolle die Richtlinie **setup / preload** (die Funktion `preload` des Moduls `setup`). Die Ansichten sind
`preload` (die Seite), `preloadjob` (startet und hält den Lauf im Hintergrund an) und `preloadstream` (der ältere
Fortschritts-Stream). Alle sind Ansichten desselben Moduls und durchlaufen daher dieselben Siteaccess- und
Richtlinienprüfungen wie die Seite. Der Menüeintrag ist `settings/menu.ini` `Links[preload]=setup/preload` (Beschriftung
`Preload Sites`, `PolicyList_preload[]=setup/preload`).

## Wissenswertes Verhalten

- **Es wärmt die gewählte Website vor und bleibt in ihr.** Ein Host kann mehrere Siteaccesses bedienen. Der Crawler
  ermittelt das URL-Präfix, das den gewählten Siteaccess auswählt (aus `site.ini [SiteAccessSettings] MatchOrder` mit
  `HostMatchMapItems` oder `HostUriMatchMapItems` oder dem ersten URL-Segment), und folgt nur Seiten darunter. Vorher
  wärmte die Wahl einer zweiten Website die Standard-Website vor und meldete Erfolg.
- **Defekte Links nennen ihre Seiten.** Ein defektes Ziel wird einmal gemeldet, mit den Seiten, die darauf verlinken. Ein
  Link im Website-Menü oder in einem Template steht auf jeder Seite, daher behält der Bericht eine begrenzte Zahl
  verweisender Seiten und sagt, wie viele es noch gab. Ein Verweis auf etwas, das keine Seite ist (ein Anker, ein
  Fragment), wird nicht mehr als defekt gemeldet.
- **Ein Aufrufer kann die geholten Seiten behalten.** Der Generator für den statischen Cache verwendet das, um sie zu
  speichern (siehe [Generator für den statischen Cache](generator-fuer-den-statischen-cache.md)).

## Es läuft im Hintergrund

Seit dem 28. September 2026 startet **Vorladen starten** den Lauf als Hintergrundprozess, statt eine Webanfrage für den
ganzen Durchlauf offen zu halten. Der alte Weg las den Fortschritt als Stream. Ein Anwendungsserver mit Pool wie
Velocity übergibt eine Antwort erst, wenn sie vollständig ist, und beendet eine Anfrage nach ihrem Zeitlimit, sodass der
Stream nichts brachte und nach 30 Sekunden mit 504 endete; die Schaltfläche tat sichtbar nichts. Ein Proxy, der
gestreamte Antworten puffert, verhielt sich genauso. Der neue Weg funktioniert hinter jedem Webserver und Proxy.

- `setup/preloadjob` startet `bin/php/preloadjob.php` abgekoppelt (`setsid`) mit den gewählten Seiten, der Tiefe und dem
  Siteaccess, beantwortet seine Ereignisse nach einer angegebenen Zeile als JSON und hält einen Lauf auf Anforderung an
  (er endet nach der Seite, die er gerade holt). Starten und Anhalten brauchen das Formular-Token, wie jede Änderung;
  ein Start ohne es wird abgelehnt.
- Jeder Lauf hält seine Ereignisse in `var/<var dir>/preload/<id>.jsonl`; die neuesten 20 Läufe werden aufbewahrt. Dem
  Lauf werden nur Pipes übergeben, und die Seite sagt es, wenn ein Lauf aufhörte, ohne fertig zu werden.
- Die Konsole fragt einmal pro Sekunde ab und zeigt die Ereignisse genau wie bisher. **Anhalten** beendet den Lauf
  selbst, nicht nur die Sicht der Seite darauf.
- Der Server braucht `proc_open`, `setsid` (`/usr/bin/setsid` oder `/bin/setsid`) und die PHP-Kommandozeile. Ohne sie
  sagt es die Konsole (siehe `kernel/classes/exppreloadjob.php`).
- Die Konsole zeigt jetzt am Ende eines Laufs den Bericht über defekte Links und die Zusammenfassung. (Eine
  JavaScript-Variable namens `tr` verdeckte den Übersetzungshelfer und hielt die Konsole kurz davor an.) Ein Ereignis,
  das die Seite nicht anzeigen kann, wird als Fehler geschrieben, und die Abfrage geht weiter.
- `setup/preloadstream` bleibt für alles, was es verwendet.

## Verwandte Seiten

- [Vorlader für den Website-Cache](../../../eng-us/bc/6.0/preload.md) (die Ausgabe lesen, Einsatz mit cron, Fehlersuche)
- [HTTP/2 und Vorwärmen des Caches](../../../eng-us/bc/6.0/http2-and-cache-warming.md), [Generator für den statischen Cache](generator-fuer-den-statischen-cache.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [September 2026, erste Hälfte: Seiten vorladen](../../../eng-us/history/2026/2026-09a.md#13-september-caches-you-can-see-cronjobs-you-can-run), [die Korrekturen vom 15. September](../../../eng-us/history/2026/2026-09a.md#15-september-paging-everywhere)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
