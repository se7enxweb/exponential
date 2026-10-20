---
title: Budget für Aktualisierungen und Wartung
audience: businesses, project-manager, admin
category: business-solutions
order: 90
summary: Wie Sie ein Jahresbudget planen, um eine Exponential-Website aktuell und sicher zu halten - Releases 6.0.x, PHP- und Serveraktualisierungen, Aktualisierungen von Erweiterungen, Prüfungen und Wiederherstellungstests - mit dem, was jede Art von Arbeit umfasst, einem Kalender und einem durchgerechneten Budget aus Beispielannahmen.
keywords: Budget, Aktualisierungen, Wartung, Updates, Release 6.0.x, PHP-Aktualisierung, ezupdate, Upgrade-Prüfung, Wiederherstellungstest, Reserve, Kalender, Migration, Release-Linie, upgrades, maintenance, updates, PHP upgrade, upgrade check, restore test, contingency, calendar, release line
type: help
---
# Budget für Aktualisierungen und Wartung

Diese Seite ist für Führungskräfte, die das Jahresbudget einer Webanwendung festlegen, und für die Administratoren oder
Partner, die die Arbeit machen. Sie erklärt, welche Wartungsarbeiten eine Exponential-Website braucht, was jede Art
umfasst, wie oft sie anfällt und wie Sie daraus ein Budget mit einer Reserve für Überraschungen machen.

> **Hinweis**
> Stunden und Sätze im durchgerechneten Budget sind **Beispielannahmen** in einer neutralen Währungseinheit. Bitten Sie
> die Person, die die Arbeit macht, um ihre eigene Schätzung, und messen Sie im ersten Jahr die tatsächlichen Stunden.

## Inhalt

1. [Die Arten von Arbeit](#die-arten-von-arbeit)
2. [Was ein Release der Linie 6.0 umfasst](#was-ein-release-der-linie-60-umfasst)
3. [PHP- und Serveraktualisierungen](#php--und-serveraktualisierungen)
4. [Größere Schritte: von älteren Versionen oder anderen Plattformen](#größere-schritte-von-älteren-versionen-oder-anderen-plattformen)
5. [Ein Wartungskalender](#ein-wartungskalender)
6. [Ein durchgerechnetes Jahresbudget](#ein-durchgerechnetes-jahresbudget)
7. [Das Budget klein halten](#das-budget-klein-halten)

## Die Arten von Arbeit

| Arbeit | Wie oft | Wer | Quelle |
|---|---|---|---|
| Ein neues Release 6.0.x einspielen | wenn eines veröffentlicht wird | Administrator oder Partner | `SECURITY.md`; [Aktualisieren](../../../anleitungen/aktualisieren.md) |
| Erweiterungen aktualisieren | wenn sie Releases veröffentlichen | Administrator | [Automatische Aktualisierungen mit ezupdate](../../hosting/betrieb/automatische-updates-mit-ezupdate.md) |
| PHP aktualisieren | wenn Ihre PHP-Version das Ende ihres Supports erreicht | Hoster oder Administrator | [Runbook PHP-Aktualisierung](../../hosting/runbooks/php-upgrade.md) |
| Betriebssystem und Datenbankserver aktualisieren | wie der Hersteller veröffentlicht | Hoster | [Sicherheitsupdates und Offenlegung](../../hosting/sicherheit/sicherheitsupdates-und-offenlegung.md) |
| Dateien und Datenbank prüfen | vor und nach jeder Aktualisierung | Administrator | [Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md) |
| Eine Wiederherstellung testen | mindestens einige Male im Jahr | Administrator oder Hoster | [Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md) |
| Konten und Zugriffe prüfen | jährlich | Website-Inhaber | [Zugriffsprüfungen](../../../../eng-us/help/businesses/security/access-reviews.md) |

Wie oft Releases kommen: Die Linie 6.0 ging von 6.0.0 am 1. Januar 2024 bis 6.0.14 am 7. Juni 2026, mit 6.0.15 in
Entwicklung ([Wer ist 7x?](../../wer-ist-7x.md#kurz-gesagt)). Planen Sie mehrere Aktualisierungen im Jahr ein, nicht eine
große.

## Was ein Release der Linie 6.0 umfasst

Aus [Aktualisieren, Teil C](../../../anleitungen/aktualisieren.md#teil-c-von-einer-beliebigen-60x-auf-den-aktuellen-stand):

1. Zuerst sichern, immer.
2. Den neuen Code einspielen (ein Checkout des Tags oder `composer update`).
3. Die Klassenzuordnung neu erzeugen und die Caches leeren.
4. PHP-FPM neu laden oder Velocity neu ausrollen.
5. Die Datenbank-Aktualisierungsdatei der Linie anwenden, einmal, vollständig, in der richtigen Reihenfolge.
6. Die Aktualisierung prüfen.

Zwei Tatsachen machen das berechenbar. Sicherheitskorrekturen kommen „with the next 6.0.x release“ (mit dem nächsten
Release 6.0.x), und „a published tag is never changed“ (ein veröffentlichter Tag wird nie geändert, `SECURITY.md`):
Eine Version, die Sie getestet haben, bleibt die Version, die Sie betreiben. Die
[Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md) vergleicht Dateien und Datenbank vor und nach dem Release und
ändert nichts. `ezupdate` kann aus der Administration nach Aktualisierungen suchen und einen Probelauf ausführen
([Automatische Aktualisierungen mit ezupdate](../../hosting/betrieb/automatische-updates-mit-ezupdate.md)).

Verwenden Sie den Wartungsmodus für die Minuten, in denen die Website offline sein muss
([Wartungsmodus](../../../../eng-us/features/6.0/maintenance-mode.md)), und lesen Sie
[Aktualisierungen ohne Überraschungen](../../../../eng-us/help/businesses/reliability/upgrades-without-surprises.md) für die Sicht des Unternehmens.

## PHP- und Serveraktualisierungen

- Exponential unterstützt PHP 8.0 bis 8.5, ebenso Velocity ab seinem Release 0.0.4.54 (`README.md`, „What version of
  PHP is required“).
- Die Testsuite läuft bei jedem Push auf PHP 8.1 bis 8.5 und prüft, dass 8.0 startet
  ([Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung)).
- Die README empfiehlt, „the latest version of PHP available on your OS“ zu verwenden (die neueste auf Ihrem
  Betriebssystem verfügbare PHP-Version).

Planen Sie eine PHP-Aktualisierung als eigenes kleines Projekt mit einem Test auf einer Kopie der Website: Auch Ihre
eigenen Templates und Erweiterungen müssen auf dem neuen PHP laufen.

## Größere Schritte: von älteren Versionen oder anderen Plattformen

Eine Website, die noch auf 4.x oder 5.x (aus den Jahren von eZ Publish) oder auf eZ Platform oder Ibexa läuft, ist eine
Migration, keine Aktualisierung. Planen Sie dafür ein eigenes Budget ein, mit einer Staging-Kopie und einem Plan für
den Rückweg. Das Installationsbuch hat die Kapitel:
[Migration von 4.x](../../../installation/14-migration-von-4x.md),
[Migration von 5.x Legacy](../../../installation/15-migration-von-5x-legacy.md) und
[Migration von eZ Platform und Ibexa](../../../installation/16-migration-von-ez-platform-und-ibexa.md).
[Zu Exponential wechseln](../auswahl/zu-exponential-wechseln.md) erklärt es für Entscheider.

## Ein Wartungskalender

| Wann | Aufgabe |
|---|---|
| Jeden Monat | Nach Releases sehen; Logs und das Audit-Dashboard lesen; der [monatlichen Betriebscheckliste](../../../../eng-us/help/businesses/templates/monthly-operations-checklist.md) folgen |
| Wenn ein Release 6.0.x erscheint | Sein Changelog lesen; auf Staging testen; in einem geplanten Zeitfenster einspielen |
| Jedes Quartal | Eine Wiederherstellung testen; Erweiterungen aktualisieren; Speicherplatz und Aufbewahrung der Backups prüfen |
| Jedes Jahr | PHP- und Betriebssystem-Aktualisierungen planen; Konten und Rollen prüfen; dieses Budget prüfen ([Jahresrückblick](../../../../eng-us/help/businesses/templates/annual-review-checklist.md)) |

## Ein durchgerechnetes Jahresbudget

Jeder Eingabewert ist eine **Beispielannahme**.

| Eingabewert | Wert |
|---|---|
| Beispielannahme: eingespielte Releases 6.0.x pro Jahr | 6 |
| Beispielannahme: Stunden pro Release (Test auf Staging, Aktualisierung, Prüfungen) | 3 |
| Beispielannahme: eine PHP-Aktualisierung mit Tests | 8 Stunden |
| Beispielannahme: monatliche Prüfungen | 1 Stunde im Monat |
| Beispielannahme: Wiederherstellungstests | 4 im Jahr zu je 2 Stunden |
| Beispielannahme: Stundensatz | 50 Währungseinheiten |
| Beispielannahme: Reserve für Überraschungen | 20 % |

Schritt für Schritt:

1. Releases: 6 x 3 = **18** Stunden.
2. PHP-Aktualisierung: **8** Stunden.
3. Monatliche Prüfungen: 1 x 12 = **12** Stunden.
4. Wiederherstellungstests: 4 x 2 = **8** Stunden.
5. Stunden insgesamt: 18 + 8 + 12 + 8 = **46** Stunden.
6. Kosten: 46 x 50 = **2.300**.
7. Reserve: 2.300 x 20 % = **460**.
8. Jährliches Wartungsbudget: 2.300 + 460 = **2.760**.

Wenn ein Partner die Arbeit im Rahmen eines Vertrags macht, ersetzen Sie die Schritte 1 bis 6 durch den Vertragspreis
und behalten Sie die Reserve für Arbeiten außerhalb des Vertrags.

## Das Budget klein halten

- [ ] Halten Sie eigene Änderungen in Erweiterungen und Template-Überschreibungen, nicht in Dateien des Kernels; die
      Upgrade-Prüfung findet dann keine geänderten Kernel-Dateien ([Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
- [ ] Aktualisieren Sie oft in kleinen Schritten, statt Releases auszulassen.
- [ ] Halten Sie eine Staging-Kopie, die der Produktion entspricht.
- [ ] Verfolgen Sie Releases auf GitHub und im Issue-Tracker, der in `README.md`, „Issue tracker“, genannt ist.
- [ ] Schreiben Sie jede Aktualisierung mit Datum und Ergebnis auf; der Audit-Trail zeichnet Deployments und
      Schreibvorgänge in den Einstellungen ebenfalls auf ([Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).

## Verwandte Seiten

- [Release-Linie und Support-Richtlinie](../../../../eng-us/help/businesses/reliability/release-line-and-support-policy.md)
- [Upgrade-Prüfung und Dateikonsistenz](../../../../eng-us/help/businesses/reliability/upgrade-check-and-file-consistency.md)
- [Kunden-Websites aktualisieren](../../hosting/betrieb/kunden-websites-aktualisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
