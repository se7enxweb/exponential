---
title: Hosting-Tarife gestalten
audience: hosting
category: hosting
order: 20
summary: Wie Sie Tarife für Exponential-Hosting aus technischen Bausteinen bauen - Engine, Datenbank, Speicher, Backups, Caching, Support und Dienste; Tarifvorlagen für Shared-, VPS-, dedizierte und verwaltete Angebote, mit Grenzen aus Ihren eigenen Messungen statt erfundener Zahlen.
keywords: Hosting-Tarife, Pakete, Tarifgestaltung, Shared, VPS, dediziert, verwaltet, Grenzen, Kontingente, Worker, Datenbank, Speicher, Backups, Staging, Zusatzoptionen, Produktgestaltung, hosting plans, packages, plan design, dedicated, managed, limits, quotas, add-ons, product design
type: help
---
# Hosting-Tarife gestalten

Diese Seite ist für Produktmanager und Systemadministratoren, die gemeinsam die Tarife eines Angebots für
Exponential-Hosting gestalten. Sie beantwortet: was die Bausteine eines Tarifs sind, welche technischen Entscheidungen
hinter jedem Baustein stehen, wie Sie die Grenzen eines Tarifs festlegen und wie typische Tariffamilien aussehen.

Die Seite gibt Struktur, keine Preise. Ihre Kosten, Ihre Hardware und Ihr Markt bestimmen die Zahlen; die Quellen des
Projekts veröffentlichen keine, und diese Hilfe erfindet keine.

## Inhalt

1. [Die Bausteine](#die-bausteine)
2. [Technische Entscheidungen hinter jedem Baustein](#technische-entscheidungen-hinter-jedem-baustein)
3. [Grenzen aus Messungen festlegen](#grenzen-aus-messungen-festlegen)
4. [Tariffamilien](#tariffamilien)
5. [Zusatzoptionen](#zusatzoptionen)
6. [Ein Tarifblatt zum Kopieren](#ein-tarifblatt-zum-kopieren)
7. [Häufige Fehler](#häufige-fehler)

## Die Bausteine

Jeder Tarif für Exponential-Hosting ist eine Kombination dieser Bausteine. Schreiben Sie für jeden Tarif auf, was er
enthält.

| Baustein | Die Frage, die er beantwortet | Optionen |
|---|---|---|
| Engine | Was liefert die Website aus? | Exponential Velocity, FrankenPHP über Velocity, Apache oder nginx mit PHP-FPM |
| PHP | Welche Versionen, wer wählt? | der unterstützte Bereich 8.0 bis 8.5 (einschließlich Velocity) |
| Datenbank | Welche Engine, geteilt oder dediziert? | in der offiziellen Reihenfolge: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL |
| Speicher | Wie viel Platz für `var/storage` (Uploads und Bildvarianten), Caches und Logs? | ein Kontingent pro Tarif |
| Abschottung | Was trennt diesen Kunden von anderen? | ein Systembenutzer und PHP-FPM-Pool pro Website, ein VPS, ein Container, eine dedizierte Maschine |
| Backups | Was wird gesichert, wie oft, wie lange aufbewahrt, wie wiederhergestellt? | Datenbank plus `var/storage`, `settings/override`, `settings/siteaccess` |
| Caching | Welche Schichten sind eingeschaltet und abgestimmt? | Antwort-Cache von Velocity, HTTP-Cache-Header, Reverse Proxy, CDN, Valkey |
| Umgebungen | Gibt es eine Staging-Kopie? | nur Produktion; Produktion plus Staging; mehrere |
| Updates | Wer spielt Exponential-Updates ein, und wann? | der Kunde; der Hoster auf Anfrage; der Hoster nach Zeitplan |
| Support | Wer beantwortet was, wie schnell? | siehe [Support-Stufen](support-stufen-fuer-exponential-hosting.md) |
| Dienste | Welche Arbeit wird über das Hosting hinaus angeboten? | Installation, Migration, Entwicklung, Schulung |

## Technische Entscheidungen hinter jedem Baustein

### Engine

[Kapitel 8](../../../installation/08-die-website-ausliefern.md) empfiehlt Exponential Velocity für jede Phase und nennt FrankenPHP
eine produktionsreife Alternative. Velocity ist ein lang laufender Prozess; auf Shared Hosting, das lang laufende
Prozesse verbietet, nutzen Tarife stattdessen Apache oder nginx mit PHP-FPM. Eine Tariffamilie kann sich daher an dieser
Linie teilen:

- **Shared-Tarife** auf Apache oder nginx mit einem PHP-FPM-Pool pro Kunde;
- **VPS-, dedizierte und verwaltete Tarife** auf Velocity, mit einem eigenen Worker-Pool pro Website.

Details: [Einen Webserver wählen](../server/einen-webserver-waehlen.md), [Velocity für Hoster](../server/velocity-fuer-hoster.md).

### Datenbank

SQLite 3 ist der Standard und mit dem Treiber, den Exponential mitliefert, eine vollwertige Produktionsdatenbank für eine
Website, die von einer Maschine ausgeliefert wird ([Datenbankunterstützung](../../datenbankunterstuetzung.md#1-sqlite-3-der-standard)).
Es braucht keinen Datenbankserver und kostet einen Hoster daher nichts zusätzlich. Ein Tarif, der mehrere Web-Knoten
verspricht, braucht einen Datenbankserver, zuerst PostgreSQL
([Datenbanken in der offiziellen Reihenfolge](../grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)).

### Speicher und Backups

Ein vollständiges Backup besteht aus der Datenbank und den Dateien: `var/storage`, `settings/override` und
`settings/siteaccess` ([Datenbankunterstützung](../../datenbankunterstuetzung.md#backup-und-wiederherstellung)). Bemessen Sie den
Backup-Speicher eines Tarifs nach diesen Verzeichnissen, nicht nach der ganzen Installation; den Code selbst können Sie
erneut aus Git oder Composer holen. Siehe [Was gesichert werden muss](../betrieb/backups-was-gesichert-werden-muss.md).

### Abschottung

Tarife, die sich eine Maschine teilen, brauchen eine Abschottung zwischen Kunden auf Ebene des Betriebssystems. Kapitel 8
nennt das als Grund, einen klassischen Webserver zu behalten: wenn mehrere Kunden auf einer Maschine voneinander auf
Ebene des Betriebssystems abgeschottet sein müssen. Siehe [Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md).

## Grenzen aus Messungen festlegen

Das Installationsbuch nennt wenige harte Zahlen und sagt das auch: Die Dokumentation nennt wenige harte Zahlen, und das
Buch erfindet keine weiteren ([2.10](../../../installation/02-voraussetzungen.md#210-arbeitsspeicher-cpu-und-festplatte)). Was es nennt:

| Element | Wert | Quelle |
|---|---|---|
| PHP `memory_limit` | mindestens 64M | Kapitel 2 |
| Ein Velocity-Worker | etwa 10 MB privater Speicher (gemessen) | Kapitel 2, 2.10 |
| Velocity-Pool | `Workers=4` mitgeliefert | `settings/velocity.ini` |

Alles andere kommt aus Ihren eigenen Messungen. Eine Methode:

1. Installieren Sie eine Referenz-Website auf der Hardware, die der Tarif nutzen wird (ein Seitenpaket mit Demo-Inhalten
   ist ein guter Anfang, siehe [Seitenpakete](../bereitstellung/seitenpakete-fuer-hoster.md)).
2. Messen Sie sie mit dem Benchmark-Befehl ([Benchmark einer gehosteten Website](../leistung/benchmark-einer-gehosteten-website.md))
   mit kalten und warmen Caches.
3. Beobachten Sie über einen repräsentativen Zeitraum den Speicher pro Worker oder pro PHP-FPM-Kind, die Größe der
   Datenbank und das Wachstum von `var/`.
4. Legen Sie jede Grenze des Tarifs mit Reserve über dem Gemessenen fest, und schreiben Sie auf, wie Sie gemessen haben.
5. Messen Sie nach jedem Minor-Release von Exponential und jedem PHP-Upgrade erneut.

> **Tipp**
> Schreiben Sie die Messmethode in das Tarifblatt. Wenn ein Kunde fragt, warum ein Tarif seine Grenze hat, ist die
> Antwort die Messung, keine Schätzung.

## Tariffamilien

Vorlagen, keine Empfehlungen. Ersetzen Sie jedes `<...>` durch Ihre eigene Zahl.

| | Shared | VPS | Dediziert | Verwaltet |
|---|---|---|---|---|
| Engine | Apache oder nginx + PHP-FPM | Velocity (oder klassisch) | Velocity | Velocity, vom Hoster abgestimmt |
| Datenbank | SQLite 3 oder eine Datenbank auf einem geteilten Server | SQLite 3 oder ein Server auf dem VPS | jede der sechs | mit dem Kunden gewählt |
| Abschottung | Systembenutzer + PHP-FPM-Pool | virtuelle Maschine | Maschine | laut Vertrag |
| Shell-Zugang | `<yes/no>` | ja | ja | für den Hoster; Kunde `<yes/no>` |
| Speicherkontingent | `<GB>` | `<GB>` | `<disk>` | `<GB>` |
| Backups | `<daily>`, `<n>` Tage aufbewahrt | `<...>` | `<...>` | `<...>`, Wiederherstellung getestet `<monthly>` |
| Staging-Kopie | `<no>` | `<optional>` | `<optional>` | `<included>` |
| Updates | Kunde | Kunde, oder Hoster auf Anfrage | Kunde | Hoster, nach Zeitplan |
| Support | `<tier>` | `<tier>` | `<tier>` | `<tier>` |

## Zusatzoptionen

Optionen, die ein Hoster getrennt verkaufen kann, jede durch eine Funktion in den Quellen gestützt:

| Zusatzoption | Worauf sie beruht |
|---|---|
| Staging-Umgebung | eine zweite Installation aus demselben Git-Checkout und eine Kopie der Daten; siehe [Änderungen ausrollen](../betrieb/aenderungen-ausrollen.md) |
| Verwaltete Updates | die Erweiterung ezupdate und das Kapitel zum Upgrade, siehe [Automatische Updates mit ezupdate](../betrieb/automatische-updates-mit-ezupdate.md) |
| Längere Aufbewahrung von Backups | Ihr Backup-System; siehe [Backup-Zeitpläne und Aufbewahrung](../betrieb/backup-zeitplaene-und-aufbewahrung.md) |
| CDN | siehe [CDN](../leistung/cdn.md) |
| Suchserver | siehe [Such-Backends](../grundlagen/such-backends.md) |
| Migrationsdienst | siehe [Kunden zu Ihrem Hosting migrieren](../migrationen/kunden-zu-ihrem-hosting-migrieren.md) |
| Sicherheitsprüfung | siehe [Checkliste für die Sicherheitsprüfung](../checklisten/checkliste-fuer-die-sicherheitspruefung.md) |

## Ein Tarifblatt zum Kopieren

```text
Plan name:            <name>
Audience:             <who it is for>
Engine:               <Velocity | FrankenPHP | Apache + PHP-FPM | nginx + PHP-FPM>
PHP versions:         <list>; who chooses: <host | customer>
Database:             <engine, version>; dedicated or shared: <...>
Isolation:            <system user + pool | VM | container | machine>
Limits:               storage <...>, workers or FPM children <...>, database size <...>
  measured on:        <hardware>, <date>, method: <benchmark command, content set>
Backups:              <what>, <how often>, kept <how long>, stored <where>, restore tested <how often>
Environments:         <production | + staging>
Updates:              <who>, <when>, announced <how>
Support tier:         <tier name>  (see the support tiers sheet)
Add-ons available:    <list>
Not included:         <list, for example custom development>
```

## Häufige Fehler

- **Eine Kapazität versprechen, die Sie nicht gemessen haben.** Messen Sie zuerst; siehe oben.
- **Die Wiederherstellung aus dem Tarif weglassen.** Ein Backup-Plan ohne Wiederherstellungstest ist unvollständig.
- **Velocity auf einer Plattform anbieten, die lang laufende Prozesse beendet.** Prüfen Sie es, bevor Sie es verkaufen.
- **Kunden in einem PHP-FPM-Pool mischen.** Geben Sie jeder Website einen eigenen Systembenutzer und Pool.
- **Einen Tarif nach dem alten Produktnamen benennen.** Das Produkt heißt Exponential; siehe
  [Marken- und Namensregeln](../../geschaeft/marken-und-namensregeln.md).

## Verwandte Seiten

- [SLA-Optionen](sla-optionen-fuer-exponential-hosting.md)
- [Support-Stufen](support-stufen-fuer-exponential-hosting.md)
- [Abrechnungs- und Messoptionen](abrechnungs-und-messoptionen.md)
- [Ein Hosting-Modell wählen](../modelle/ein-hosting-modell-waehlen.md)
- [Speicher, CPU und Festplatte dimensionieren](../grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
