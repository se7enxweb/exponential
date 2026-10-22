---
title: Monatliche Checkliste für Wiederverkäufer
audience: reseller, partner, hosting
category: reselling
order: 480
summary: Die monatliche Routine eines Wiederverkäufers von Angeboten auf Basis von Exponential - Releases und das Inventar der Websites, Datensicherungen und Wiederherstellungstests, Logs und Sicherheit, Support-Statistiken, Partneraktivität, Pipeline und Rechnungsstellung -, mit einer Vorlage für den monatlichen Kundenbericht.
keywords: monatliche Checkliste, Routine, Releases, Inventar, Datensicherungen, Wiederherstellungstest, Logs, Sicherheit, Support-Statistiken, Partner-Tickets, Pipeline, Rechnungsstellung, Kundenbericht, monthly checklist, routine, inventory, backups, restore test, logs, security, support statistics, partner tickets, invoicing, customer report
type: help
---
# Monatliche Checkliste für Wiederverkäufer

Diese Seite ist für Betriebs- und Kundenbetreuungspersonal bei Wiederverkäufern. Arbeiten Sie sie einmal im Monat
für alle Kunden durch; sie hält die Websites aktuell und liefert Ihnen das Material für die Kundenberichte, die Kunden
zur Verlängerung bewegen. Der Hosting-Bereich hat eine tiefergehende technische Routine ([Monatlicher Betrieb](../../hosting/checklisten/checkliste-fuer-den-monatlichen-betrieb.md)).

## Inhalt

1. [Websites](#websites)
2. [Support](#support)
3. [Geschäft](#geschäft)
4. [Vorlage für den Kundenbericht](#vorlage-für-den-kundenbericht)

## Websites

- [ ] Prüfen Sie, ob es neue Releases von Exponential 6.0.x und von Erweiterungen gibt; lesen Sie das Changelog
      ([Kunden aktuell halten](../kunden/kunden-aktuell-halten.md)).
- [ ] Aktualisieren Sie das Inventar der Websites: Version, PHP, Erweiterungen je Website.
- [ ] Spielen Sie ausstehende Releases im vereinbarten Zeitfenster ein, zuerst auf Staging; halten Sie die drei
      Ergebnisse fest (Version, Upgrade-Prüfung, Testveröffentlichung).
- [ ] Datensicherungen liefen jeden Tag; testen Sie die Wiederherstellung der Websites, die diesen Monat an der Reihe
      sind ([Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)).
- [ ] Prüfen Sie **Setup > Logdateien** und das Sicherheitslog jeder Website ([Sicherheitslog](../../hosting/betrieb/sicherheitslog.md)).
- [ ] Prüfen Sie das Audit-Protokoll auf unerwartete Aktionen von Administratoren ([Audit-Protokoll für Hoster](../../hosting/sicherheit/pruefprotokoll.md)).
- [ ] Zertifikate: keines läuft innerhalb von 30 Tagen ab ([Ablauf von Zertifikaten](../../hosting/runbooks/ablauf-von-zertifikaten.md)).
- [ ] Speicherplatz und Wachstum von `var/storage` ([Festplatte voll](../../hosting/runbooks/festplatte-voll.md)).
- [ ] Konten ausgeschiedener Mitarbeiter aus der Administration jedes Kunden entfernt.

## Support

- [ ] Tickets je Kunde nach Schweregrad; gemessene Erstreaktion im Vergleich zu Ihren Zusagen
      ([Service-Levels gestalten](../angebote/service-levels-gestalten.md)).
- [ ] In diesem Monat genutzte Partner-Support-Tickets im Vergleich zum Kontingent Ihres Tarifs
      ([Partnertarife und Preise](../../partner/partnertarife-und-preise.md#die-tarife)).
- [ ] Wiederkehrende Fragen in Kundendokumentation umgewandelt ([Dokumentation, die Sie Ihren Kunden geben können](../kunden/dokumentation-fuer-ihre-kunden.md)).
- [ ] An das Projekt gemeldete Fehler und ihr Status ([Eskalation für Wiederverkäufer](../kunden/eskalation.md)).

## Geschäft

- [ ] Rechnungen versendet; zusätzliche Stunden abgerechnet ([Rechnungsstellung und Steuern](../verantwortlichkeiten/rechnungsstellung-und-steuern.md)).
- [ ] Pipeline überprüft; veraltete Leads geschlossen ([Vertriebsprozess](../verkauf/vertriebsprozess.md#pipeline-aufzeichnung)).
- [ ] Empfohlene Leads nachverfolgt; Provisionsaufzeichnungen aktualisiert ([Leads übergeben](../verkauf/leads-uebergeben.md#provisionsaufzeichnungen)).
- [ ] Partneraktivität für Punkte erfasst ([Partneraktivität erfassen](../../partner/partneraktivitaet-erfassen.md)).
- [ ] Verlängerungen, die in den nächsten 90 Tagen fällig sind, angestoßen ([Verlängerungen und Abwanderung](../kunden/verlaengerungen-und-abwanderung.md#der-verlängerungszyklus)).

## Vorlage für den Kundenbericht

```text
Monthly report for <Customer>, <month>

Your site: <example.com>, Exponential 6.0.__, PHP 8.__
Updates applied: <release, date> (or: none due)
Backups: daily, all successful / <exceptions>; last restore test: <date>
Availability: <measured>, incidents: <none / short description>
Support: <n> requests; average first response <time>
Security: <nothing to report / what was done>
Next month: <planned update window, PHP change, training, open wishes>
```

Halten Sie ihn kurz; sein Zweck ist, die Arbeit zu zeigen, für die der Kunde bezahlt.

## Verwandte Seiten

- [Monatlicher Betrieb](../../hosting/checklisten/checkliste-fuer-den-monatlichen-betrieb.md)
- [Jährliche Checkliste für Wiederverkäufer](jaehrliche-checkliste.md)
- [Wiederverkäufer verwalteter Dienste](../modelle/wiederverkaeufer-verwalteter-dienste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
