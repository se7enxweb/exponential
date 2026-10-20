---
title: Gemessene Schritte und Zeiten
audience: designer, website-creator, admin, developer, editor
category: build-two-ways
order: 30
summary: Wie die auf diesen Seiten genannten Schritte und Zeiten gemessen wurden (auf einer Wegwerfkopie einer echten Installation von Exponential 6.0.15+), und die Rohdaten, damit jeder sie auf seiner eigenen Site wiederholen kann.
keywords: gemessen, Geschwindigkeit, Schritte, Zeitmessung, Benchmark, exp:ini, exp:cache, Renderzeit, Seiten-Cache, Methode, wiederholbar, measured, speed, steps, timing, benchmark, page render time, page cache, method, repeatable
type: help
---
# Gemessene Schritte und Zeiten

Die Zahlen auf diesen Seiten stammen aus einem einzigen Test, der hier beschrieben ist, damit er wiederholt werden kann.
Sie sind kein Benchmark der Software: Sie zeigen, wie viele Schritte eine Aufgabe braucht und wie lange die Wartezeiten
auf einer gewöhnlichen Installation dauern.

## Der Aufbau

- Eine Kopie einer Live-Installation von Exponential 6.0.15+ mit der Demo-Site Fit & Healthy (SQLite-Datenbank), auf
  demselben geteilten Server, ausgeliefert vom eingebauten Webserver von PHP mit sechs Workern (`PHP_CLI_SERVER_WORKERS=6`).
- Der Server war mit anderer Arbeit beschäftigt: eine Load Average von etwa 29 während der Messungen.
- Ein Wegwerf-Administratorkonto, das danach mit der Kopie entfernt wurde.
- Zeiten mit der Uhr der Shell um jeden Befehl gemessen (`date +%s%N` davor und danach), jeweils drei Durchläufe.

## Schritte

Eine **Aktion** ist ein Klick, eine Auswahl in einer Liste oder eine Eingabe.

| Aufgabe | Weg | Aktionen oder Befehle |
|---|---|---|
| Eine Seite zum Kopfmenü hinzufügen | Administration: Setup, INI-Einstellungen, `menu.ini` wählen, `site` wählen, Einstellungen anzeigen, Bearbeiten, `=192` tippen, Speichern, Template & Inhalt wählen, Leeren | 10 Aktionen |
| Eine Seite zum Kopfmenü hinzufügen | Konsole: `exp:ini add ...`, `exp:cache clear --tag=template,content` | 2 Befehle |
| Einen Link zur zweiten Zeile der Fußzeile hinzufügen | Inhalt: Media, Configuration, Footer links, Neu erstellen, Menu item, OK, Titel tippen, URL tippen, Zur Veröffentlichung schicken | 9 Aktionen |

## Zeiten

| Gemessen | Lauf 1 | Lauf 2 | Lauf 3 |
|---|---|---|---|
| `./console exp:ini add menu.ini/SiteInfo/FooterMenuID[] 164 siteaccess:site --no-backup` | 1512 ms | 1258 ms | 2127 ms |
| `./console exp:cache clear --tag=template,content` | 1983 ms | 2177 ms | 2470 ms |
| Erste Anfrage an `/fitness` nach diesem Leeren (Templates neu kompiliert) | 3413 ms | 3300 ms | 2921 ms |
| Zweite Anfrage an `/fitness` (aus dem Seiten-Cache) | 28 ms | 22 ms | 102 ms |

## Wiederholen

Auf einer Kopie Ihrer eigenen Site (für Schreibvorgänge nie auf der Live-Site):

```bash
a=$(date +%s%N); ./console exp:ini add menu.ini/SiteInfo/FooterMenuID[] <id> siteaccess:<sa> --dry-run; b=$(date +%s%N)
echo $(( (b - a) / 1000000 )) ms
a=$(date +%s%N); ./console exp:cache clear --tag=template,content --dry-run; b=$(date +%s%N)
echo $(( (b - a) / 1000000 )) ms
curl -s -o /dev/null -w '%{time_total}\n' https://www.example.com/
```

Mit `--dry-run` lassen sich beide Befehle auf jeder Installation gefahrlos messen; sie schreiben und leeren dann nichts.

## Verwandte Seiten

- [Zwei Wege zum Bauen](../../../eng-us/help/build-two-ways/two-ways-to-build.md)
- [Caches nach einer Änderung](caches-nach-einer-aenderung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
