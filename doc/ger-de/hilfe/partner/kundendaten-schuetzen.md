---
title: Kundendaten schützen
audience: partner
category: partnership
order: 710
summary: Wie Partner personenbezogene Daten ihrer Kunden und der Benutzer ihrer Kunden in jedem Teil der Partnerschaft schützen - Support-Tickets, Fehlerberichte, Aktivitätsprotokolle, Leads, Fallstudien, Staging-Kopien und Schulungen - mit Regeln, Platzhaltern und einer Checkliste.
keywords: Datenschutz, personenbezogene Daten, Kundendaten, DSGVO, anonymisieren, Platzhalter, Staging, Tickets, Leads, Einwilligung, privacy, data protection, personal data, customer data, GDPR, anonymize, placeholders, consent
type: help
---
# Kundendaten schützen

Diese Seite ist für alle in einem Partnerunternehmen. Partner verarbeiten personenbezogene Daten von Kunden und von den
Benutzern ihrer Kunden. Die Partnerschaft darf nie zu einem Weg werden, über den Daten abfließen. Diese Seite legt
praktische Regeln fest; über die Einzelheiten entscheidet das Recht, das für Sie gilt (zum Beispiel das
Datenschutzrecht Ihres Landes oder des Landes Ihres Kunden).

## Die Regeln

1. **Teilen Sie nur, was nötig ist.** Ein Ticket braucht den Fehler, nicht die Benutzerliste des Kunden.
2. **Platzhalter in der Öffentlichkeit.** Ersetzen Sie in Issues, Foren, Chats und Screenshots Namen, E-Mail-Adressen,
   private URLs und Hostnamen durch Platzhalter wie `customer-site.example`.
3. **Einwilligung für Leads und Referenzen.** Geben Sie die Kontaktdaten einer Person nur mit deren Einwilligung an
   Dritte weiter ([Empfehlungen und Leads](empfehlungen-und-leads.md)).
4. **Keine Produktivdaten in Tests und Schulungen.** Staging-Kopien werden anonymisiert; Schulungen nutzen erfundene
   Testkonten.
5. **Keine personenbezogenen Daten in Aktivitätsprotokollen.** Nachweis für Punkte ist eine öffentliche URL und eine
   Beschreibung ([Ihre Partneraktivität erfassen](partneraktivitaet-erfassen.md)).
6. **Löschen, wenn erledigt.** Exporte, Dumps und Screenshots werden nach der Nutzung gelöscht.

## Wo Daten in der Praxis abfließen

| Ort | Risiko | Vorbeugung |
|---|---|---|
| Support-Tickets und Fehler-Issues | Logs mit E-Mail-Adressen, Sitzungs-IDs | Logs vor dem Einfügen bereinigen ([Ein Support-Ticket als Partner eröffnen](support-ticket-eroeffnen.md)) |
| Screenshots | Namen in Listen, Benutzermenüs | Ein Testkonto verwenden; unkenntlich machen oder zuschneiden |
| Datenbank-Dumps zur Fehlersuche | Ganze Benutzertabellen | Eine Nachstellung teilen, keinen Dump |
| Staging-Kopien | Daten echter Benutzer auf einem schwächer geschützten Server | Anonymisieren; Staging wie den Produktivbetrieb schützen |
| Fallstudien | Namen und Zitate ohne Freigabe | Schriftliche Freigabe ([Eine Fallstudie schreiben](eine-fallstudie-schreiben.md)) |
| Geteilte Leads | Kontaktdaten ohne Einwilligung weitergegeben | Zuerst die Einwilligung festhalten |

## Verträge

- Vereinbaren Sie mit jedem Kunden, wer welche Daten zu welchem Zweck verarbeitet.
- Wenn 7x oder ein anderer Partner für Sie an der Website arbeitet (White Label, Unterauftrag), gehört er zu dieser
  Kette; nennen Sie ihn, wie es Ihr Recht verlangt ([Dienstleistungen von 7x als White Label](white-label-dienstleistungen.md)).

## Checkliste

- [ ] Mitarbeitende in diesen Regeln geschult
- [ ] Platzhalter in jedem öffentlichen Beitrag verwendet
- [ ] Staging anonymisiert
- [ ] Nachweise der Einwilligung für Leads und Referenzen
- [ ] Löschroutine für Exporte und Dumps

## Verwandte Seiten

- [Sicherheitspflichten von Partnern](sicherheitspflichten.md)
- [Verhaltenskodex für Partner](verhaltenskodex.md)
- [Sicherheitshärtung](../sicherheitshaertung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
