---
title: "Lektion 5.5: Die Caches vor PHP"
audience: trainer, admin
category: training
order: 55
summary: Die Teilnehmenden vergleichen den Antwort-Cache von Velocity, den rollenbewussten HTTP-Cache und den statischen Cache, erklären, warum der statische Cache ausgeschaltet ausgeliefert wird, und lesen die Header, die zeigen, welcher Cache geantwortet hat. 35 Minuten.
keywords: website-administration, antwort-cache von velocity, http-cache, httpcache.ini, statischer cache, StaticCache, makestaticcache, cache-ebenen, X-Exp-Cache, header, opcache, apcu, site administration, velocity response cache, http cache, static cache, caching layers, headers
type: help
---
# Lektion 5.5: Die Caches vor PHP

Manche Caches beantworten eine Anfrage, bevor Exponential überhaupt etwas rendert: der Antwort-Cache von Velocity, der
rollenbewusste HTTP-Cache und der statische Cache. Sie machen eine Website sehr schnell und sind auch der Grund, warum
ein Besucher eine alte Seite sehen kann, nachdem alles andere geleert wurde. Diese Lektion vergleicht sie, erklärt,
warum der statische Cache ausgeschaltet ausgeliefert wird, und zeigt, wie man sieht, welcher Cache geantwortet hat.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 5: Caches](README.md) |
| Dauer | 35 Minuten (15 Präsentation, 20 Übung) |
| Niveau | Experten |
| Zielgruppe | Website-Administratoren und Support-Mitarbeiter |
| Voraussetzungen | [Lektion 5.4](caches-vorladen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Caches vor PHP nennen, ob jeder ein- oder ausgeschaltet ausgeliefert wird und wen jeder bedient;
- erklären, warum der statische Cache ausgeschaltet ausgeliefert wird und was ihn ersetzt;
- den Status dieser Caches mit `exp:cache status` lesen;
- an den Antwort-Headern erkennen, welcher Cache geantwortet hat;
- jeden von ihnen leeren, nachdem der neue Code live ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Cache-Ebenen](../../../hosting/leistung/cache-schichten.md),
[Statischer Cache](../../../hosting/leistung/statischer-cache.md) und
[Eine Website betreiben, die Caches vor PHP](../../../../anleitungen/betrieb-einer-website.md#die-caches-vor-php).

1. **Die drei** (6 Minuten):

   | | Antwort-Cache von Velocity | Rollenbewusster HTTP-Cache | Statischer Cache |
   |---|---|---|---|
   | Ausgeliefert | eingeschaltet unter Velocity | ausgeschaltet | ausgeschaltet |
   | Bedient | anonyme Besucher | anonyme und angemeldete, pro Berechtigungskontext | anonyme Besucher, als Dateien |
   | Geleert durch | `exp:velocity cache clear`; Einträge laufen ab | bei Inhaltsänderungen bereinigt; `--clear-id=exphttpcache` | beim Veröffentlichen neu erzeugt |

2. **Warum der statische Cache ausgeschaltet ausgeliefert wird** (3 Minuten). War er eingeschaltet, erzeugte jedes
   Veröffentlichen die betroffenen Seiten auf jedem öffentlichen Siteaccess über HTTP innerhalb der Anfrage des
   Redakteurs neu, sodass ein Veröffentlichen Sekunden dauerte. Der Antwort-Cache von Velocity bietet Besuchern
   denselben Vorteil ohne diese Kosten. `site.ini [ContentSettings] StaticCache=disabled` ist der ausgelieferte Wert;
   lassen Sie ihn so, sofern kein Hoster anders entscheidet.
3. **Sehen, welcher Cache geantwortet hat** (3 Minuten). Der HTTP-Cache sendet `X-Exp-Cache: HIT`, `STALE`,
   `MISS (reason)` oder `BYPASS (reason)`. Prüfen Sie die Header mit den Entwicklerwerkzeugen des Browsers oder mit
   `curl -I`.
4. **Die Reihenfolge des Leerens** (3 Minuten). Leeren Sie diese Caches **nachdem** PHP-FPM oder Velocity den neuen
   Code ausführt; eine Seite, die zwischendurch vom alten Code gerendert wird, wird sonst wieder gespeichert.

### Häufige Fragen

**Ein Besucher sieht eine alte Seite, dabei habe ich alles geleert.** Prüfen Sie in dieser Reihenfolge den
Antwort-Cache von Velocity, den HTTP-Cache, eine statische Datei, einen vorgeschalteten Proxy oder ein CDN und den
Browser-Cache.

**Kann ich den statischen Cache für eine Übung einschalten?** Das ist nicht nötig: Lesen Sie stattdessen die Hilfe des
Generators. Auf einer Live-Website entscheidet das der Hoster (siehe [Hosting und Betrieb](../../../../../eng-us/help/trainers/hosting-operations/README.md)).

**Und OPcache und APCu?** Sie speichern PHP-Code und Daten pro PHP-Prozess. Die Seite Caches hat
**OPcache zurücksetzen** und **APCu leeren** für den Prozess, der geantwortet hat; der andere Server behält seine
eigenen.

### Stolperfallen

- **Im angemeldeten Zustand testen.** Der Antwort-Cache von Velocity und der statische Cache bedienen nie angemeldete
  Benutzer. Testen Sie in einem privaten Fenster.
- **In einer Bereitstellung zu früh leeren.** Alter Code füllt die Caches wieder. Vermitteln Sie die Reihenfolge
  jetzt; der Hosting-Kurs automatisiert sie.

## Praktische Übung

Auf Ihrer Schulungsinstallation, mit einem Terminal im Wurzelverzeichnis und einem privaten Browserfenster:

1. Lesen Sie den Status der Caches vor PHP:

   ```bash
   php bin/php/cache.php status
   ```

   Notieren Sie für jede Zeile, ob sie ein- oder ausgeschaltet ist.
2. Lesen Sie die Einstellung des statischen Caches:

   ```bash
   php bin/php/console exp:ini where site.ini/ContentSettings/StaticCache
   ```

3. Lesen Sie die Optionen des Generators für den statischen Cache, ohne ihn auszuführen:

   ```bash
   php bin/php/makestaticcache.php --help
   ```

4. Lesen Sie den Status des Antwort-Caches von Velocity:

   ```bash
   php bin/php/cache.php velocity status
   ```

5. Öffnen Sie im privaten Fenster zweimal die öffentliche Startseite. Lesen Sie dann die Header der Seite:

   ```bash
   curl -sI http://localhost:8088/
   ```

6. Bearbeiten Sie in der Administration den Titel der Startseite (oder eines beliebigen sichtbaren Artikels) und
   veröffentlichen Sie ihn; laden Sie dann das private Fenster sofort und noch einmal nach einer halben Minute neu.
7. Leeren Sie den Antwort-Cache und laden Sie neu:

   ```bash
   php bin/php/console exp:velocity cache clear
   ```

8. Öffnen Sie **Setup > Cache-Verwaltung**, Gruppe **Seiten, Abfragen und der Server**, und lesen Sie die Zeilen für
   den HTTP-Cache, den Cache der Abfragen, das SQL-Profil, den Antwort-Cache von Velocity, OPcache und APCu. Drücken
   Sie deren Schaltflächen nicht.

## Erwartetes Ergebnis

- Schritt 1 endet mit `PASS  status read`; der statische Cache und der HTTP-Cache sind ausgeschaltet, der Antwort-Cache
  von Velocity ist eingeschaltet.
- Schritt 2 zeigt, dass `StaticCache=disabled` wirksam ist.
- Schritt 6: Die Änderung erscheint unter Umständen erst, wenn der Eintrag im Antwort-Cache abläuft oder geleert wird
  (Schritt 7).
- Der Trainer prüft das, indem er alle Teilnehmenden die ersten drei Caches nennen lässt, die zu prüfen sind, wenn ein
  Besucher eine alte Seite sieht.

## Quiz

1. Welcher der drei Caches wird eingeschaltet ausgeliefert?
   A. Der statische Cache B. Der HTTP-Cache C. Der Antwort-Cache von Velocity D. Keiner
2. Warum wird der statische Cache ausgeschaltet ausgeliefert?
3. Welcher Header sagt, was der rollenbewusste HTTP-Cache getan hat?
4. Warum leert man die Caches vor PHP erst, nachdem der neue Code läuft?
5. Nennen Sie zwei Caches, die nie einen angemeldeten Besucher bedienen.

<details>
<summary>Antworten</summary>

1. **C.** Unter Velocity ([Cache-Ebenen](../../../hosting/leistung/cache-schichten.md#die-schichten-auf-einen-blick)).
2. Jedes Veröffentlichen erzeugte die betroffenen Seiten über HTTP innerhalb der Anfrage des Redakteurs neu, was das
   Veröffentlichen langsam machte ([Statischer Cache](../../../hosting/leistung/statischer-cache.md#warum-er-ausgeschaltet-ausgeliefert-wird)).
3. `X-Exp-Cache`.
4. Sonst wird eine Seite, die zwischendurch vom alten Code gerendert wurde, wieder gespeichert.
5. Der Antwort-Cache von Velocity und der statische Cache.

</details>

## Weiterführende Literatur

- [Cache-Ebenen](../../../hosting/leistung/cache-schichten.md): alle zwölf Ebenen.
- [Statischer Cache](../../../hosting/leistung/statischer-cache.md) und
  [Generator für den statischen Cache](../../../../../eng-us/features/6.0/static-cache-generator.md).
- [Antwort-Cache von Velocity](../../../../../eng-us/features/6.0/velocity-response-cache.md).
- [HTTP-Caching](../../../../bc/6.0/http-caching.md).

## Verwandte Seiten

- [Modul 5: Caches](README.md)
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
