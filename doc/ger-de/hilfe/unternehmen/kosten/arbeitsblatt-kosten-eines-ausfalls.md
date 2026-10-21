---
title: "Arbeitsblatt: Kosten eines Ausfalls"
audience: businesses, project-manager, admin, hosting
category: business-solutions
order: 70
summary: Ein Arbeitsblatt, um zu berechnen, was eine Stunde offline Ihre Organisation kostet - entgangene Umsätze, untätiges Personal, Wiederherstellungsarbeit, Vertragsstrafen und verschwendetes Marketing - mit einem durchgerechneten Beispiel aus Beispielannahmen, einem leeren Formular zum Kopieren und den Funktionen von Exponential, die einen Ausfall verkürzen oder verhindern.
keywords: Ausfall, Ausfallzeit, Kosten eines Ausfalls, Verfügbarkeit, Uptime, Arbeitsblatt, entgangener Umsatz, Wiederherstellung, Wartungsmodus, Health-Check, Störung, Business Case, downtime, outage, cost of downtime, availability, uptime, worksheet, lost revenue, recovery, maintenance mode, health check, restore, incident, business case
type: help
---
# Arbeitsblatt: Kosten eines Ausfalls

Diese Seite ist für Inhaber und Führungskräfte, die wissen wollen, was ein Ausfall wirklich kostet, damit sie
entscheiden können, wie viel sie für seine Vermeidung ausgeben. Sie nennt die Kostenposten, ein durchgerechnetes
Beispiel, ein leeres Formular zum Kopieren und die Funktionen von Exponential, die Ihnen helfen, die Zahl klein zu
halten.

> **Hinweis**
> Das durchgerechnete Beispiel verwendet nur **Beispielannahmen**: runde Zahlen in einer neutralen Währungseinheit. Es
> sind keine Branchendurchschnitte. Füllen Sie das leere Formular mit Ihren eigenen Zahlen aus.

## Inhalt

1. [Geplante und ungeplante Ausfallzeit](#geplante-und-ungeplante-ausfallzeit)
2. [Die Kostenposten](#die-kostenposten)
3. [Ein durchgerechnetes Beispiel](#ein-durchgerechnetes-beispiel)
4. [Das leere Arbeitsblatt](#das-leere-arbeitsblatt)
5. [Das Ergebnis verwenden](#das-ergebnis-verwenden)
6. [Was einen Ausfall verkürzt oder verhindert](#was-einen-ausfall-verkürzt-oder-verhindert)

## Geplante und ungeplante Ausfallzeit

- **Geplante Ausfallzeit** ist angekündigte Arbeit: eine Aktualisierung, ein Datenimport. Der Wartungsmodus von
  Exponential nimmt die öffentliche Website mit einer freundlichen Seite und einer Antwort HTTP 503 vom Netz und holt
  sie mit einem Befehl oder einer Schaltfläche zurück ([Wartungsmodus](../../../../eng-us/features/6.0/maintenance-mode.md)).
  Geplante Arbeit in einer ruhigen Stunde kostet wenig.
- **Ungeplante Ausfallzeit** ist ein Ausfall: eine volle Festplatte, ein abgelaufenes Zertifikat, eine
  fehlgeschlagene Aktualisierung, ein Angriff. Sie kostet am meisten, weil sie zu jeder Stunde eintritt und niemand
  vorbereitet ist.

Berechnen Sie die Kosten einer ungeplanten Stunde zu einer geschäftigen Zeit. Diese Zahl rechtfertigt die Vorbeugung.

## Die Kostenposten

| Posten | Zu beantwortende Frage |
|---|---|
| A. Entgangener Rohertrag | Wie viel Rohertrag (Umsatz abzüglich Wareneinsatz und Gebühren) bringt die Website in einer Stunde zu einer geschäftigen Zeit? |
| B. Verlorene Arbeitszeit | Wie viele Mitarbeiter können nicht arbeiten oder beantworten stattdessen Beschwerden, und was kostet ihre Stunde? |
| C. Wiederherstellungsarbeit | Wie viele Stunden von Administratoren oder einem Partner braucht eine typische Wiederherstellung, zu welchem Satz (oft ein Notfallsatz)? |
| D. Vertragsstrafen | Schulden Sie Kunden oder Partnern Gutschriften oder Strafen für einen Ausfall? |
| E. Verschwendetes Marketing | Bezahlen Sie Anzeigen oder einen Newsletter, die Menschen auf eine tote Website schicken? |
| F. Verlorene Kunden | Keine Zahl: Beschreiben Sie es in Worten, zum Beispiel „Erstbesucher, die nie wiederkommen“ |

Kosten einer Stunde = A + B + C/Dauer + D/Dauer + E. Wiederherstellungsarbeit und Strafen fallen oft einmal pro
Vorfall an; teilen Sie sie also durch die Dauer des Ausfalls, oder führen Sie sie als Kosten pro Vorfall.

## Ein durchgerechnetes Beispiel

Ein Unternehmen mit einem Online-Shop und einem Kundenservice-Team. Jeder Eingabewert ist eine **Beispielannahme**.

| Eingabewert | Wert |
|---|---|
| Beispielannahme: Rohertrag der Website in einer geschäftigen Stunde | 200 Währungseinheiten |
| Beispielannahme: betroffene Mitarbeiter | 3 Personen zu 40 Währungseinheiten pro Stunde |
| Beispielannahme: Wiederherstellungsarbeit | 4 Stunden zu 100 Währungseinheiten pro Stunde |
| Beispielannahme: vertragliche Gutschriften | keine |
| Beispielannahme: Werbung, die während des Ausfalls läuft | 20 Währungseinheiten pro Stunde |
| Beispielannahme: Dauer des Ausfalls | 2 Stunden |

Schritt für Schritt:

1. A. Entgangener Rohertrag: 200 x 2 = **400**.
2. B. Verlorene Arbeitszeit: 3 x 40 x 2 = **240**.
3. C. Wiederherstellungsarbeit: 4 x 100 = **400**.
4. D. Vertragliche Gutschriften: **0**.
5. E. Verschwendetes Marketing: 20 x 2 = **40**.
6. Kosten des Vorfalls: 400 + 240 + 400 + 0 + 40 = **1.080**.
7. Kosten pro Stunde dieses Ausfalls: 1.080 / 2 = **540**.

Wenn das Unternehmen ohne Vorbeugung drei solche Vorfälle im Jahr erwartet, betragen die erwarteten jährlichen Kosten
3 x 1.080 = **3.240**, zuzüglich der in Worten beschriebenen verlorenen Kunden. Weniger als das für Vorbeugung
auszugeben, zahlt sich wahrscheinlich aus; [Rendite der Stabilität](rendite-der-stabilitaet.md) zeigt, wie Sie das
darstellen.

## Das leere Arbeitsblatt

Kopieren Sie es in Ihr eigenes Dokument und füllen Sie es aus.

```text
ARBEITSBLATT: KOSTEN EINES AUSFALLS
Organisation: ______________________   Datum: ____________   Erstellt von: ______________

Website oder Anwendung: ______________________
Geschäftigste Stunden und Tage: ______________________

A. Rohertrag der Website pro geschäftiger Stunde:    ______  (Quelle: ______________)
B. Betroffene Mitarbeiter: ___ Personen x ___ Kosten/Stunde = ______  pro Stunde
C. Wiederherstellungsarbeit: ___ Stunden x ___ Satz = ______  pro Vorfall
D. Vertragliche Gutschriften oder Strafen          = ______  pro Vorfall
E. Marketing, das während eines Ausfalls läuft     = ______  pro Stunde
F. Weitere Folgen (in Worten): ________________________________________________

Typische Dauer eines Ausfalls (Stunden): ______
Kosten eines Vorfalls = (A + B + E) x Dauer + C + D = ______
Kosten pro Stunde     = Kosten eines Vorfalls / Dauer = ______
Erwartete Vorfälle pro Jahr ohne Vorbeugung: ______
Erwartete jährliche Ausfallkosten                  = ______

Geprüft von: ______________   Nächste Prüfung: ____________
```

## Das Ergebnis verwenden

- Legen Sie in Ihrem Hosting- oder Supportvertrag ein Verfügbarkeitsziel fest, das zu den Kosten passt
  ([Grundlagen zu Verträgen und SLAs](../auswahl/grundlagen-zu-vertraegen-und-slas.md)).
- Entscheiden Sie, wie viel Sie pro Jahr für Backups, Monitoring und Wiederherstellungstests ausgeben.
- Wählen Sie ein Hosting-Modell, dessen Risiko Sie sich leisten können
  ([Hosting-Modelle im Vergleich](../auswahl/hosting-modelle-im-vergleich.md)).
- Planen Sie Aktualisierungen und andere geplante Arbeit in die Stunden, in denen A am niedrigsten ist.

## Was einen Ausfall verkürzt oder verhindert

| Maßnahme | Was sie bewirkt | Lesen |
|---|---|---|
| Externes Monitoring | Findet einen Ausfall, bevor Kunden es tun | [Health-Checks und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md) |
| Velocity-Watchdog | Startet Velocity nach einem Absturz neu (`exp:velocity watchdog install`) | [SLA-Optionen](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md) |
| Getestete Wiederherstellungen | Macht aus einer Katastrophe ein bekanntes Verfahren | [Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md) |
| Caching | Hält die Website bei Lastspitzen erreichbar | [Caching-Ebenen](../../hosting/leistung/cache-schichten.md) |
| Fehlerreferenzen | Die Fehlerseiten für Besucher tragen eine Referenz, die den Logeintrag findet | [Einen Fehler über seine Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md) |
| Runbooks | Hilfe Schritt für Schritt bei einer ausgefallenen Website, einer vollen Festplatte oder einem abgelaufenen Zertifikat | [Website ausgefallen](../../hosting/runbooks/website-nicht-erreichbar.md), [Festplatte voll](../../hosting/runbooks/festplatte-voll.md), [Zertifikat abgelaufen](../../hosting/runbooks/ablauf-von-zertifikaten.md) |
| Wartungsmodus | Macht aus riskanter Arbeit geplante Ausfallzeit | [Wartungsmodus](../../../../eng-us/features/6.0/maintenance-mode.md) |

Der Bereich [Zuverlässigkeit](../zuverlaessigkeit/README.md) erklärt jede Praxis für Unternehmen, einschließlich der
[Störungsbehandlung für kleine Teams](../../../../eng-us/help/businesses/reliability/incident-response-for-small-teams.md).

## Verwandte Seiten

- [Verfügbarkeit in der Praxis](../../../../eng-us/help/businesses/reliability/uptime-practice.md)
- [Rendite der Stabilität](rendite-der-stabilitaet.md)
- [Monatliche Betriebscheckliste](../vorlagen/monatliche-betriebscheckliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
