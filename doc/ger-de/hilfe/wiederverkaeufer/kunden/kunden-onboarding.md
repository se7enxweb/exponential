---
title: Kunden-Onboarding
audience: reseller, partner, hosting
category: reselling
order: 360
summary: Wie ein Wiederverkäufer einen neuen Kunden für ein Angebot auf Basis von Exponential aufnimmt - das Kickoff, das Sammeln der Fakten, Einrichtung oder Übernahme der Website, Konten und Rollen, erste Datensicherung und Wiederherstellungstest, Schulung der Redakteure und die ersten 30 Tage -, mit einer Checkliste für das Onboarding und einer Vorlage für das Willkommensschreiben.
keywords: Onboarding, neuer Kunde, Kickoff, Willkommen, übernehmen, Einrichtung, Konten, Rollen, Datensicherung, Wiederherstellungstest, Schulung, erste 30 Tage, Inventar, Checkliste, onboarding, new customer, kickoff, welcome, take over, setup, accounts, roles, backup, restore test, training, first 30 days, inventory, checklist
type: help
---
# Kunden-Onboarding

Diese Seite ist für Kundenbetreuer und technisches Personal bei Wiederverkäufern. Das Onboarding gibt den Ton der
ganzen Beziehung vor: Ein Kunde, der weiß, wen er anrufen kann, was gesichert wird und was als Nächstes geschieht,
eröffnet weniger besorgte Tickets.

## Inhalt

1. [Zwei Ausgangspunkte](#zwei-ausgangspunkte)
2. [Die Schritte des Onboardings](#die-schritte-des-onboardings)
3. [Die ersten 30 Tage](#die-ersten-30-tage)
4. [Vorlage für das Willkommensschreiben](#vorlage-für-das-willkommensschreiben)
5. [Checkliste für das Onboarding](#checkliste-für-das-onboarding)

## Zwei Ausgangspunkte

| Ausgangspunkt | Was geschieht | Seite |
|---|---|---|
| Eine neue Website | Aus einem Seitenpaket installieren (`sevenx_multisite_clean` oder `sevenx_site_clean` für eine echte Website), konfigurieren, gestalten, befüllen | [Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md) |
| Eine bestehende Website, die Sie übernehmen | Den Export entgegennehmen, prüfen, umziehen, verifizieren | [Eine Website übernehmen](../../kunden/partner-und-hoster/eine-website-uebernehmen.md), [Eine Website zwischen Hostern umziehen](../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md) |

## Die Schritte des Onboardings

1. **Kickoff-Gespräch.** Stellen Sie die Kontakte auf beiden Seiten, den Support-Kanal und die Supportzeiten sowie
   den Update-Rhythmus vor.
2. **Sammeln Sie die Fakten** in Ihrem Inventar der Websites: Exponential-Version, PHP, Datenbank, Engine,
   Erweiterungen mit Versionen, Siteaccesses, Hostnamen ([Kunden aktuell halten](kunden-aktuell-halten.md)).
3. **Richten Sie die Website ein oder übernehmen Sie sie.** Führen Sie bei einer Übernahme **Setup > Upgrade-Prüfung**
   aus; sie vergleicht die Dateien mit dem Release und die Datenbank mit ihrem Schema und ändert nichts ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
4. **Konten.** Der Kunde erhält sein eigenes Administratorkonto; Ihr Personal erhält persönliche Konten, nie ein
   gemeinsames ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)). Ändern Sie jedes Standardpasswort
   sofort ([Erste Anmeldung und Standardpasswörter](../../hosting/sicherheit/erste-anmeldung-und-standardpasswoerter.md)).
5. **Härtung.** Wenden Sie Ihre Standardhärtung an ([Sicherheitshärtung](../../sicherheitshaertung.md)).
6. **Erste Datensicherung und ein Wiederherstellungstest.** Eine Datensicherung umfasst die Datenbank und
   `var/storage`, `settings/override` und `settings/siteaccess`; stellen Sie sie einmal wieder her, um zu beweisen,
   dass sie funktioniert ([Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)).
7. **Überwachung** der Startseite und der Administration ([Health-Checks und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)).
8. **Schulung der Redakteure** ([Schulungs-Wiederverkäufer](../modelle/schulungs-wiederverkaeufer.md)).
9. **Übergeben Sie die Kundendokumentation** ([Dokumentation, die Sie Ihren Kunden geben können](dokumentation-fuer-ihre-kunden.md)).

## Die ersten 30 Tage

| Woche | Tun Sie dies |
|---|---|
| 1 | Kickoff, Fakten, Konten, Härtung, erste Datensicherung |
| 2 | Wiederherstellungstest, Überwachung, Schulung der Redakteure |
| 3 | Prüfen Sie die Logs (**Setup > Logdateien**) und beheben Sie, was Sie finden ([Log-Viewer](../../../funktionen/6.0/log-betrachter.md)) |
| 4 | Erster Bericht an den Kunden: was getan wurde, was geplant ist, offene Fragen |

Die Seite des Kunden für denselben Monat steht unter [Die ersten 30 Tage als Betreiber](../../kunden/ihre-ersten-30-tage.md);
schicken Sie ihm den Link.

## Vorlage für das Willkommensschreiben

```text
Subject: Welcome to <Company>: your Exponential site <example.com>

Your contacts: <name, role>, <name, role>
Support: <channel>, <days and hours, time zone>; urgent (site down): <phone, role>
What we do for you: <tier summary>
Updates: we apply Exponential releases <rhythm>; we announce windows <n> days ahead.
Backups: <frequency>, kept <days>; last restore test: <date>
Your accounts: you have your own administrator account; please do not share it.
Your documentation: <link to the customer manual>
Next steps: <training date>, <first report date>
```

## Checkliste für das Onboarding

- [ ] Vertrag unterschrieben, erste Rechnung versendet.
- [ ] Kontakte und Support-Kanal vereinbart.
- [ ] Inventar der Website vollständig.
- [ ] Upgrade-Prüfung ausgeführt (Übernahme) oder Website aus einem sauberen Paket installiert (neu).
- [ ] Persönliche Konten; Standardpasswörter geändert; der Kunde hat ein eigenes Administratorkonto.
- [ ] Härtung angewendet.
- [ ] Datensicherung läuft; Wiederherstellung getestet.
- [ ] Überwachung läuft.
- [ ] Redakteure geschult; Dokumentation übergeben.
- [ ] Erster Bericht nach 30 Tagen versendet.

## Verwandte Seiten

- [Go-live einer neuen Kunden-Website](../../hosting/checklisten/checkliste-fuer-den-start-einer-kunden-website.md)
- [Checkliste für das Onboarding von Mitarbeitern](../../kunden/alltag/checkliste-einarbeitung.md)
- [Checkliste für den Start für Wiederverkäufer](../checklisten/checkliste-fuer-den-start.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
