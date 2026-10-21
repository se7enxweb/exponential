---
title: Eine Stellenausschreibung veröffentlichen
audience: government, editor
category: public-sector
order: 110
summary: Für Redakteure in Personal und Web - eine Stellenausschreibung mit der im Standardpaket mitgelieferten Klasse Job position veröffentlichen (oder mit einem Artikel, wo sie nicht installiert ist), mit Bewerbungsfrist und Bewerbungsweg, und sie nach der Frist herunternehmen.
keywords: Stellenausschreibung, Stellenangebote, Personalgewinnung, Job position, ng_job_position, Bewerbungsfrist, Beschäftigungsart, Bewerbungsweg, nach der Frist verstecken, job vacancy, jobs, recruitment, application deadline, engagement type, how to apply, hide after deadline
type: help
---
# Eine Stellenausschreibung veröffentlichen

Diese Seite ist für Redakteure in Personal oder Kommunikation, die Stellenausschreibungen veröffentlichen. Das
Standard-Sitepaket installiert eine Klasse **Job position** (`ng_job_position`) mit Location, Engagement type,
Application deadline, Intro text, Job overview, Requirements und Responsibilities
([Die Demo-Inhaltspakete](../../../seitenpakete/sevenx-multisite/die-demo-inhaltspakete.md)). Wenn Ihre Site sie
nicht hat, verwenden Sie einen Artikel in einem Ordner für Stellenangebote mit denselben Überschriften.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Nach der Frist](#nach-der-frist)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Der Text der Ausschreibung ist von der Personalabteilung genehmigt.
- Sie wissen, wie man sich bewirbt: über ein E-Recruiting-System (Link) oder ein Postfach.
- Sie kennen Datum und Uhrzeit des Bewerbungsschlusses.

## Schritte

1. Öffnen Sie den Ordner **Jobs**, **Neu erstellen**, wählen Sie **Job position**.
2. **Title**: die Stellenbezeichnung, wie man nach ihr sucht. **Teaser title** für Listen, wenn der Titel lang ist.
3. **Location**, **Engagement type** (Vollzeit, Teilzeit, befristet).
4. **Application deadline**: Datum und Uhrzeit.
5. **Intro text**: zwei Sätze über die Stelle. **Job overview**, **Requirements**, **Responsibilities**: in einfacher
   Sprache, als Listen.
6. Schreiben Sie den Bewerbungsweg in die Übersicht oder die Einleitung, mit dem Link zum Bewerbungssystem oder zum
   Postfach; nennen Sie den Kontakt als Funktion („das Recruiting-Team“).
7. **Vorschau**, dann **Zur Veröffentlichung schicken**.

## Das Ergebnis prüfen

- [ ] Abgemeldet erscheint die Ausschreibung mit ihrer Frist in der Liste der Stellenangebote.
- [ ] Der Bewerbungslink öffnet das richtige Formular.
- [ ] Ein RSS-Feed der Stellenangebote, falls vorhanden, zeigt sie, sobald der Feed das nächste Mal geschrieben wird.

## Nach der Frist

| Möglichkeit | Wie |
|---|---|
| Automatisch | wenn das Web-Team `hide.php` auf das Fristattribut der Klasse eingestellt hat (`HideDateAttributeList[ng_job_position]=application_deadline`), wird die Ausschreibung beim nächsten Lauf nach der Frist versteckt ([Archiv und Ablauf](../../aufbauen/nachrichten-und-warnmeldungen/archiv-und-ablauf.md)) |
| Von Hand | **Verstecken / Zeigen** am Tag der Frist ([Eine Seite vorübergehend verstecken](eine-seite-voruebergehend-verstecken.md)) |

Halten Sie abgeschlossene Ausschreibungen versteckt, statt sie zu entfernen, wenn Ihre Regeln zur Schriftgutverwaltung
das verlangen.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Lebensläufe über ein im Text genanntes offenes Postfach anfordern | Spam; bevorzugen Sie das Bewerbungssystem oder ein geschütztes Formular |
| Bewerbungen mit einem Formular der Website sammeln | Bewerbungen sind personenbezogene Daten, die ins Bewerbungssystem gehören |
| Eine barrierefreie Ausschreibung mit einem nicht barrierefreien Bewerbungsweg | testen Sie auch das Bewerbungssystem |
| Das Herunternehmen vergessen | setzen Sie das Fristattribut und das automatische Verstecken |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine Ausschreibung veröffentlichen](eine-ausschreibung-veroeffentlichen.md)
- [Inhalte erstellen und bearbeiten](../../../inhalte-bearbeiten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
