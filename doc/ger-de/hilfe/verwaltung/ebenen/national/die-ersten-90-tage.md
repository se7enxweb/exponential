---
title: Die ersten 90 Tage für die nationale Regierung
audience: government, project-manager, admin
category: public-sector
order: 60
summary: Ein Plan für 90 Tage in Phasen für ein Ministerium oder zentrales Ressort, das mit Exponential beginnt - Steuerung und Bestandsaufnahme, Modell und Rollen, Bau und Migration, Start - mit konkreten Prüfungen im Produkt am Ende jeder Phase.
keywords: Projektplan, 90 Tage, Projektstart, Bestandsaufnahme, Migration, Start, Ministerium, Steuerung, Prüfungen, Schulung, Go-live, project plan, 90 days, kick-off, inventory, migration, launch, ministry, governance, checks, training, go live
type: help
---
# Die ersten 90 Tage für die nationale Regierung

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren, die den Start der
Website eines Ministeriums oder zentralen Ressorts mit Exponential leiten. Sie ist ein Gerüstplan in drei Phasen von
jeweils etwa 30 Tagen. Passen Sie die Längen an Ihre Größe an; behalten Sie die Prüfungen. Die Installation selbst
behandeln [Erste Schritte](../../../../anleitungen/erste-schritte.md) und das Installationsbuch; dieser Plan beginnt, wenn
Sie eine Installation haben, an der Sie arbeiten können.

## Inhalt

1. [Tage 1 bis 30: Steuerung und Bestandsaufnahme](#tage-1-bis-30-steuerung-und-bestandsaufnahme)
2. [Tage 31 bis 60: Modell, Rollen und Bau](#tage-31-bis-60-modell-rollen-und-bau)
3. [Tage 61 bis 90: Migration, Schulung und Start](#tage-61-bis-90-migration-schulung-und-start)
4. [Zu beobachtende Risiken](#zu-beobachtende-risiken)

## Tage 1 bis 30: Steuerung und Bestandsaufnahme

| Schritt | Wer | Ergebnis |
|---|---|---|
| Product Owner, Webteam und je veröffentlichender Einheit einen Kontakt benennen | Leitung | ein Organigramm der Steuerung |
| Vereinbaren, wer was veröffentlichen darf und wer freigibt | Einheiten, Webteam | eine Freigabematrix |
| Bestandsaufnahme der aktuellen Website: Seiten, Dokumente, Verantwortliche, letzte Änderung | Webteam | eine Tabelle ([Sitemap und Inhaltsinventar](../../../../../eng-us/help/creators/website-creators/site-map-and-content-inventory.md)) |
| Entscheiden, was nicht migriert wird | Einheiten | eine Liste aufzugebender Seiten, mit Umleitungen |
| Anforderungen mit den Beauftragten für Barrierefreiheit, Sicherheit, Datenschutz und Schriftgut sammeln | Projektverantwortliche | die Anforderungstabelle ([Schwerpunkte der Anforderungen](schwerpunkte-der-anforderungen.md)) |
| Installationen für Entwicklung, Test und Produktion einrichten | Betrieb | drei Installationen, dieselbe Version ([Bereitstellen](../../../../anleitungen/bereitstellung.md)) |

**Prüfungen an Tag 30**

- [ ] **Setup > Systeminformationen** zeigt auf jeder Installation dieselbe Version von Exponential.
- [ ] Die Freigabematrix nennt für jede freigebende Person eine Vertretung.
- [ ] Jede Seite der Bestandsaufnahme hat eine verantwortliche Person oder eine Entscheidung „aufgeben“.

## Tage 31 bis 60: Modell, Rollen und Bau

| Schritt | Ergebnis | Lesen |
|---|---|---|
| Die Inhaltsklassen (Publikation, Konsultation, Pressemitteilung ...) auf der Entwicklung anlegen | Klassen unter **Setup > Klassen** | [Inhaltsmodell](inhaltsmodell.md) |
| Baum, Sektionen und Rollen bauen | die Struktur | [Struktur, Sektionen und Rollen](struktur-sektionen-und-rollen.md) |
| Den Freigabe-Workflow und seinen Trigger sowie die Crontab einrichten | eine ausstehende Version, die nach der Freigabe veröffentlicht wird | [Workflows](../../../../anleitungen/workflows.md), [Cronjobs](../../../../anleitungen/cronjobs.md) |
| Das öffentliche Design bauen oder anpassen; auf Barrierefreiheit testen | Templates | [Barrierefreiheit für Website-Ersteller](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md), [Die Barrierefreiheit testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md) |
| Sprachen und Siteaccesses konfigurieren | ein Siteaccess je Sprache | [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) |
| Die zweistufige Anmeldung für Beschäftigte einschalten | über `Enforce2FA` entschieden | [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) |
| Klassen als Paket von der Entwicklung in den Test übertragen | ein getestetes Paket | [Pakete](../../../../anleitungen/pakete.md) |

**Prüfungen an Tag 60**

- [ ] Eine Testredaktion einer Einheit kann die Seite einer anderen Einheit nicht bearbeiten (probieren Sie es aus).
- [ ] Eine von einer Testredaktion veröffentlichte Seite wartet unter **Setup > Workflow-Prozesse**, bis sie freigegeben
  ist.
- [ ] **Setup > Cronjobs** zeigt den häufigen Teil als geplant.
- [ ] Ihr Test der Barrierefreiheit der Design-Templates hat keinen offenen schwerwiegenden Befund.

## Tage 61 bis 90: Migration, Schulung und Start

| Schritt | Ergebnis | Lesen |
|---|---|---|
| Inhalte Einheit für Einheit in die Sektion Staging migrieren | Inhalte vorhanden, für Besucher unsichtbar | [Sektionen 13.3](../../../../anleitungen/sektionen.md#133-inhalte-vorbereiten-bevor-sie-öffentlich-werden) |
| Redaktion und Freigebende schulen | alle haben eine Testseite veröffentlicht | [Editors 101](../../../../../eng-us/help/trainers/editors-101/README.md) |
| Umleitungen für alte Adressen einrichten | Platzhalter und Aliase | [Umleitungen und verschobene Inhalte](../../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md) |
| Lasttest mit eingeschalteten Caches | ein gemessenes Ergebnis | [Leistung und Caching](../../../../../eng-us/help/creators/website-creators/performance-and-caching.md) |
| Wartungsmodus und eine Wiederherstellung aus der Sicherung proben | ein Probelauf mit Zeitmessung | [Wartungsmodus](../../../../anleitungen/wartungsmodus.md), [Sicherungen und Wiederherstellungen](../../../../../eng-us/help/creators/website-creators/backups-and-restores.md) |
| Start: die Inhalte aus Staging Standard zuweisen, DNS umstellen | die Website ist live | [Start-Checkliste](../../../../../eng-us/help/creators/website-creators/launch-checklist.md) |

**Prüfungen an Tag 90**

- [ ] Abgemeldet öffnet sich eine Stichprobe von 20 migrierten Seiten; alte Adressen antworten mit einer Umleitung.
- [ ] Das Audit-Protokoll listet die Änderungen des Starttags (**Setup > Audit**).
- [ ] Jede Redakteurin und jeder Redakteur hat sich mindestens einmal angemeldet (**Benutzer > Benutzerkonten prüfen**,
  Filter *Nie angemeldet*).
- [ ] Die Erklärung zur Barrierefreiheit ist veröffentlicht.

## Zu beobachtende Risiken

| Risiko | Anzeichen | Antwort |
|---|---|---|
| Einheiten migrieren alles | die Seitenzahl wächst, statt zu schrumpfen | an der Liste „aufgeben“ festhalten |
| Die Freigabe wird zum Engpass | **Setup > Workflow-Prozesse** zeigt den ältesten wartenden Prozess in Gelb (über einen Tag) | Vertretungen ergänzen, **Betroffene Versionen** eingrenzen |
| Dokumente sind nicht barrierefrei | PDF-Uploads ohne Zusammenfassungen | ein Feld für die Zusammenfassung verpflichtend machen; schulen |
| Cronjobs laufen nach einem Serverumzug nicht | freigegebene Inhalte erscheinen nie | nach jedem Umzug die Seite der Cronjobs prüfen |

## Verwandte Seiten

- [Nationale Regierung](README.md)
- [Viele Ministerien auf einer Plattform](viele-ministerien-auf-einer-plattform.md)
- [Ihre Website planen](../../../../../eng-us/help/creators/website-creators/planning-your-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
