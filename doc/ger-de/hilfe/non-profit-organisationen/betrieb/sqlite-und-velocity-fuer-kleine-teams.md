---
title: SQLite und Velocity für kleine Teams
audience: non-profit, admin, hosting, businesses
category: non-profits
order: 30
summary: Die beiden Entscheidungen in Exponential, die den Betrieb der Website einer Non-Profit-Organisation günstig machen - SQLite, die voreingestellte Datenbank, die die ganze Website ohne Datenbankserver in einer Datei hält, und Velocity, der mitgelieferte Webserver, der außer PHP nichts braucht - mit dem, was jede spart, ihren Grenzen (immer nur ein Schreibzugriff, meist nicht auf Shared Hosting, kein Pool geforkter Worker unter Windows), Sicherungen und wann Sie etwas anderes wählen.
keywords: SQLite, Velocity, kleines Team, Datenbankserver, eine Datei, ein Schreibzugriff, Sicherung, sqlite3 .backup, Webserver, exp:velocity, Shared Hosting, VPS, PHP 8.0, Grenzen, niedrige Kosten, small team, database server, one file, one writer, backup, web server, shared hosting, limits, low cost
type: help
---
# SQLite und Velocity für kleine Teams

Diese Seite richtet sich an technisch versierte Ehrenamtliche oder den Partner und an die Geschäftsführung, die die
Entscheidung verstehen möchte. Weniger bewegliche Teile bedeuten weniger, wofür man bezahlen, was man patchen,
sichern und reparieren muss. Exponential bietet zwei Entscheidungen, die ganze Dienste aus der Einrichtung einer
kleinen Website entfernen.

## Inhalt

1. [SQLite: die Website in einer Datei](#sqlite-die-website-in-einer-datei)
2. [Grenzen von SQLite und bewährte Praxis](#grenzen-von-sqlite-und-bewährte-praxis)
3. [Velocity: ein Webserver, der PHP ist](#velocity-ein-webserver-der-php-ist)
4. [Grenzen von Velocity](#grenzen-von-velocity)
5. [Welche Kombination für welche Non-Profit-Organisation](#welche-kombination-für-welche-non-profit-organisation)
6. [Checkliste](#checkliste)

## SQLite: die Website in einer Datei

SQLite 3 ist die erste Datenbank in der offiziellen Reihenfolge und die Voreinstellung. „With SQLite, a complete
Exponential site runs from one file on disk, with no database server to install, configure or secure“: Mit SQLite
läuft eine vollständige Exponential-Website aus einer Datei auf der Festplatte, ohne Datenbankserver, der installiert,
konfiguriert oder abgesichert werden müsste ([SQLite-Datenbankunterstützung](../../../funktionen/6.0/sqlite-datenbank.md)).

Was es einer Non-Profit-Organisation erspart:

| Ohne SQLite | Mit SQLite |
|---|---|
| Ein Datenbankserver, der installiert, gepatcht und überwacht werden muss | Nichts zusätzlich |
| Zugangsdaten zur Datenbank, die verwaltet werden müssen | Keine, die durchsickern könnten |
| Eine separate Datenbanksicherung | Eine Datei (plus das Speicherverzeichnis) |
| Ein größerer Hosting-Tarif | Oft der kleinste |

## Grenzen von SQLite und bewährte Praxis

Aus [SQLite-Datenbankunterstützung, Grenzen und bewährte Praxis](../../../funktionen/6.0/sqlite-datenbank.md#grenzen-und-gute-praxis):

- „SQLite allows one writer at a time. Many editors publishing at the same moment queue behind each other ... for a
  busy editorial team choose MySQL, PostgreSQL or MongoDB.“ SQLite erlaubt also immer nur einen Schreibzugriff; viele
  Redakteure, die gleichzeitig veröffentlichen, warten hintereinander, und für ein vielbeschäftigtes Redaktionsteam
  wählen Sie MySQL, PostgreSQL oder MongoDB. Eine Non-Profit-Organisation mit einer Handvoll Redakteuren liegt meist
  deutlich darunter.
- Legen Sie die Datenbankdatei nicht in ein Verzeichnis, das der Webserver direkt ausliefert; prüfen Sie, dass sie
  sich nicht herunterladen lässt.
- Sichern Sie mit `sqlite3 <file> ".backup <target>"`, „not by copying the file while the site is busy“, also nicht
  durch Kopieren der Datei, während die Website ausgelastet ist.
- Führen Sie nach einem großen Import einmal `ANALYZE` aus.

Ein späterer Wechsel zu einer anderen Datenbank ist möglich ([Datenbankunterstützung](../../datenbankunterstuetzung.md),
[SQLite für Hoster](../../hosting/grundlagen/sqlite-fuer-hoster.md)).

## Velocity: ein Webserver, der PHP ist

Exponential Velocity ist ein in PHP geschriebener Webserver, der die Installation aus einem Pool langlebiger
PHP-Prozesse betreibt; „you do not need Apache, nginx or PHP-FPM in front of it, though it works behind them“: Sie
brauchen weder Apache noch nginx oder PHP-FPM davor, er funktioniert aber auch dahinter
([Der Webserver Velocity](../../../funktionen/6.0/velocity-webserver.md)). Seit Exponential 6.0.15 wird er von der
`composer.json` im Hauptverzeichnis verlangt, sodass jede Installation ihn hat ([Velocity ist Teil von Exponential 6.0.15](../../velocity-ist-teil-von-exponential-6-0-15.md)).

Was er einem kleinen Team bietet:

- **Eine einzige Sache zum Betreiben** statt eines Webservers plus PHP-FPM: `./console exp:velocity start`, `restart`,
  `deploy` und `cache clear` sind die täglichen Befehle (dieselbe Seite).
- **HTTPS, einen Response-Cache und ein Kontrollzentrum** im selben Server.
- **Geschwindigkeit**, mit der ein kleiner Server auch Lastspitzen wie einen Aktionstag für einen Spendenaufruf
  bewältigt. Die vom Projekt gemessenen Zahlen stehen auf der Funktionsseite; messen Sie lieber Ihre eigene Website,
  statt sie zu zitieren.

## Grenzen von Velocity

- Auf Shared Hosting läuft er meist **nicht** (Vergleichstabelle in `README.md`: „Shared hosting ... usually not“). Er
  passt zu einem kleinen virtuellen Server.
- PHP 7.4 oder neuer.
- Windows kennt kein `fork()`, dort gibt es daher keinen Pool geforkter Worker.
- Jemand muss ihn beim Hochfahren starten und nach Änderungen neu starten ([Der Webserver Velocity, Grenzen](../../../funktionen/6.0/velocity-webserver.md#grenzen)).

Velocity ist nicht zwingend: Apache, Nginx, LiteSpeed und andere Server funktionieren weiterhin (`README.md`,
„Requirements“).

## Welche Kombination für welche Non-Profit-Organisation

| Situation | Datenbank | Webserver |
|---|---|---|
| Sehr kleine Website auf Shared Hosting | SQLite | Apache oder LiteSpeed des Hosters |
| Kleine Website auf einem virtuellen Server mit technisch versierten Ehrenamtlichen | SQLite | Velocity |
| Viele Redakteure, die gleichzeitig veröffentlichen, oder ein großer Shop | PostgreSQL, MariaDB oder MySQL | Velocity, oder Apache/Nginx |
| Managed Hosting | Was der Hoster unterstützt | Was der Hoster betreibt |

Hosting-Modelle: [Hosting mit kleinem Budget](hosting-mit-kleinem-budget.md).

## Checkliste

- [ ] Datenbank nach der Zahl der gleichzeitig veröffentlichenden Redakteure gewählt.
- [ ] SQLite-Datei nicht aus dem Web herunterladbar; Sicherung mit `.backup`, Wiederherstellung getestet.
- [ ] Velocity nur bei einem Hoster, der einen dauerhaft laufenden Prozess erlaubt; startet beim Hochfahren.
- [ ] Die täglichen Befehle für die Nachfolge aufgeschrieben.

## Verwandte Seiten

- [Betrieb für Non-Profit-Organisationen](README.md)
- [Betrieb mit kleinem Budget](../../unternehmen/gemeinnuetzige/betrieb-mit-kleinem-budget.md)
- [Velocity für Unternehmen](../../unternehmen/zuverlaessigkeit/velocity-fuer-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
