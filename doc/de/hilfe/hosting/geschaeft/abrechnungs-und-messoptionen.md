---
title: Abrechnungs- und Messoptionen
audience: hosting
category: hosting
order: 50
summary: Abrechnungsmodelle, die ein Hoster für Exponential-Hosting wählen kann (pauschal, gestuft, nutzungsbasiert, pro Website, pro Dienst), und was der Server und Exponential für jedes tatsächlich messen können - Festplatte, Datenbankgröße, Anfragen, Worker, Backups. Preise werden nicht genannt.
keywords: Abrechnung, Messung, Preismodell, Pauschale, gestuft, nutzungsbasiert, pro Website, Speicher, Festplattennutzung, Datenbankgröße, Anfragen, Zugriffslog, Worker, Bandbreite, Rechnungen, Tarife, Lizenzgebühr, billing, metering, pricing model, flat rate, tiered, usage based, per site, disk usage, database size, requests, access log, invoices, plans, license fee
type: help
---
# Abrechnungs- und Messoptionen

Diese Seite ist für Produktmanager und Mitarbeitende der Finanzabteilung bei einem Hosting-Anbieter, die entscheiden, wie
Exponential-Hosting abgerechnet wird, und für Systemadministratoren, die die Zahlen liefern müssen. Sie beantwortet:
welche Abrechnungsmodelle passen und was sich für jedes zuverlässig messen lässt.

Auf dieser Seite stehen keine Preise. Exponential hat keine Lizenzgebühr, die Sie weitergeben müssten: Die Nutzung von
Exponential unter den Bedingungen der GNU GPL ist kostenlos (`README.md`). Was Sie abrechnen, sind Ihre Infrastruktur und
Ihre Arbeit.

## Inhalt

1. [Abrechnungsmodelle](#abrechnungsmodelle)
2. [Was Sie messen können](#was-sie-messen-können)
3. [Die Festplattennutzung messen](#die-festplattennutzung-messen)
4. [Anfragen messen](#anfragen-messen)
5. [Die Datenbank messen](#die-datenbank-messen)
6. [Dienste abrechnen](#dienste-abrechnen)
7. [Fragen, die Sie vor dem Start klären](#fragen-die-sie-vor-dem-start-klären)

## Abrechnungsmodelle

| Modell | Wie es funktioniert | Passt zu | Achten Sie auf |
|---|---|---|---|
| Pauschal pro Tarif | ein fester Preis pro Zeitraum und Tarif | Shared- und VPS-Tarifen | Grenzen müssen durchgesetzt werden, sonst subventionieren leichte Websites die schweren |
| Gestuft | Tarife steigen nach Speicher, Workern oder Umgebungen | den meisten Angeboten | legen Sie fest, was an der Grenze geschieht: Upgrade, Drosselung oder Benachrichtigung |
| Nutzungsbasiert | eine Grundgebühr plus gemessene Nutzung (Speicher, Anfragen, Backups) | großen oder schwankenden Websites | Kunden müssen den Zähler sehen können; Streitfälle brauchen Logs |
| Pro Website | ein Preis pro Exponential-Installation, unabhängig von der Größe | Agenturen mit vielen kleinen Websites | eine Installation mit mehreren Websites liefert mehrere Websites aus einer Installation aus; entscheiden Sie, wie Sie das zählen |
| Pro Dienst | Hosting plus getrennt abgerechnete Dienste (Updates, Migrationen, Support-Stunden) | verwalteten Tarifen | schreiben Sie den Umfang jedes Dienstes auf |

> **Hinweis**
> Eine Exponential-Installation kann über Siteaccesses mehrere Websites ausliefern
> ([Mehrere Websites in einer Installation](../bereitstellung/mehrere-websites-in-einer-installation.md)). Wenn Sie „pro Website“
> abrechnen, sagen Sie, ob Sie pro Installation, pro Domain oder pro Siteaccess meinen.

## Was Sie messen können

| Größe | Quelle | Zuverlässig für die Abrechnung? |
|---|---|---|
| Festplattennutzung von Uploads und Bildvarianten | die Größe von `var/storage` (oder des Cluster-Speichers) | ja |
| Festplattennutzung von Caches und Logs | `var/cache`, `var/log` und die Verzeichnisse pro Website unter `var/` | ja, aber Caches werden neu aufgebaut und schrumpfen; rechnen Sie den Speicher nach `var/storage` ab |
| Datenbankgröße | die eigenen Werkzeuge der Datenbank-Engine; bei SQLite 3 die Größe der Datenbankdatei | ja |
| Anfragen | das Zugriffslog des Webservers oder von Velocity | ja, wenn das Log vollständig ist und lückenlos rotiert wird |
| Worker der Engine | `Workers` in `settings/velocity.ini` (`[ServerSettings]`, mitgeliefert `Workers=4`) oder `pm.max_children` des PHP-FPM-Pools | ja, als Eigenschaft des Tarifs |
| Backup-Volumen | Ihr Backup-System | ja |
| CPU-Zeit | Abrechnung des Betriebssystems pro Systembenutzer oder Container | hängt von Ihrer Plattform ab |

## Die Festplattennutzung messen

Messen Sie die Verzeichnisse, die Kundendaten enthalten, zu einer festen Zeit in jedem Zeitraum:

```bash
du -s --block-size=1M /var/www/vhosts/<domain>/<installation>/var/storage
```

Wo eine Website geclustert ist, liegen die Dateien stattdessen im Cluster-Speicher; messen Sie dort
([Clustering und gemeinsamer Speicher](../betrieb/clustering-und-gemeinsamer-speicher.md)).

## Anfragen messen

- **Apache oder nginx**: das Zugriffslog des virtuellen Hosts.
- **Velocity**: sein eigenes Zugriffslog, eine Zeile pro Anfrage. `[LogSettings]` in `settings/velocity.ini` schaltet es
  ein (`Enabled=enabled`, der Standard), hält es unter `Dir=var/vc/qbix/log`, benennt es nach dem
  Standard-Siteaccess (`site-access.log`) und schreibt standardmäßig das Format `combined`, also das, was Apache und nginx
  schreiben, für Werkzeuge, die diese lesen. Dieselben Werkzeuge, die Sie für Apache-Logs nutzen, können es zählen.

> **Warnung**
> Ein Zugriffslog nennt die Menschen, die die Website besucht haben. Velocity schreibt es deshalb mit `FileMode=0640`.
> Nutzen Sie die Zählungen für die Abrechnung; geben Sie rohe Zugriffslogs nicht an Dritte weiter. Siehe
> [Compliance und DSGVO](../sicherheit/compliance-und-dsgvo.md).

Die eigenen Anfrage-Logs von Exponential (`404.log`, `security.log`) dienen der Diagnose, nicht der Abrechnung: Sie halten
nur Antworten „nicht gefunden“ und verdächtige Anfragen fest ([Anfrage-Logs](../betrieb/anfrage-logs.md)).

## Die Datenbank messen

| Datenbank (offizielle Reihenfolge) | Wie Sie ihre Größe lesen |
|---|---|
| 1. SQLite 3 | die Größe der Datenbankdatei |
| 2. PostgreSQL | die Funktionen des Servers für die Datenbankgröße |
| 3. MongoDB | die Datenbankstatistik des Servers |
| 4. MariaDB | den Tabellenstatus / das Information Schema des Servers |
| 5. Oracle | die Segmentgrößen des Servers für das Schema |
| 6. MySQL | den Tabellenstatus / das Information Schema des Servers |

Wo die Datenbank auf einem geteilten Server liegt, messen Sie pro Datenbank (oder Schema) und bewahren die Messung mit der
Rechnung auf. Mehr zu jeder Engine: [Datenbankunterstützung](../../datenbankunterstuetzung.md).

## Dienste abrechnen

Dienste, die eine natürliche Einheit haben:

| Dienst | Einheit |
|---|---|
| Installation einer neuen Website | pro Installation, siehe [Ein-Klick-Installer](../bereitstellung/ein-klick-installer-fuer-exponential.md) |
| Migration einer Kunden-Website | pro Projekt, nach einer Bewertung, siehe [Kunden migrieren](../migrationen/kunden-zu-ihrem-hosting-migrieren.md) |
| Verwaltete Updates | pro Zeitraum, siehe [Automatische Updates mit ezupdate](../betrieb/automatische-updates-mit-ezupdate.md) |
| Wiederherstellung auf Anfrage | pro Wiederherstellung, oder bis zu einer Anzahl pro Zeitraum enthalten |
| Support über die Stufe hinaus | pro Stunde, siehe [Support-Stufen](support-stufen-fuer-exponential-hosting.md) |

## Fragen, die Sie vor dem Start klären

1. Was geschieht, wenn eine Website eine Grenze erreicht: Benachrichtigung, automatisches Upgrade oder Drosselung?
2. Welche Messung gilt, wenn der Kunde widerspricht, und wie lange bewahren Sie sie auf?
3. Zählen Backups zum Speicherkontingent?
4. Wird eine Staging-Installation als zweite Website abgerechnet?
5. Zählt eine Installation mit mehreren Websites als eine Website oder als mehrere?
6. Werden Caches zum Speicher gezählt? (Empfehlung: nein; sie können geleert und neu aufgebaut werden.)

## Verwandte Seiten

- [Hosting-Tarife gestalten](hosting-tarife-gestalten.md)
- [Aufbau des Speichers](../betrieb/aufbau-des-speichers.md)
- [Speicher, CPU und Festplatte dimensionieren](../grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
