---
title: Vierteljährliche Checkliste
audience: customer, admin, businesses
category: customer-success
order: 40
summary: Die vierteljährliche Checkliste für den Betreiber einer Exponential-Website - ein Wiederherstellungstest, eine Zugriffsüberprüfung, Updates der Erweiterungen, ein Inhalts-Audit, Stichproben zur Barrierefreiheit, Kosten gegenüber dem Budget und die Nachverfolgung der Jahresentscheidungen.
keywords: vierteljährliche Checkliste, Wiederherstellungstest, Zugriffsüberprüfung, Rollen, Updates der Erweiterungen, Inhalts-Audit, Barrierefreiheit, Budget, Nachverfolgung, Website-Betreiber, quarterly checklist, restore test, access review, roles, extension updates, content audit, accessibility, budget, follow-up, site owner
type: help
---
# Vierteljährliche Checkliste

Diese Seite ist die Prüfung des Betreibers alle drei Monate. Sie enthält die Aufgaben, die für einen Monat zu groß und
für ein Jahr zu wichtig sind: beweisen, dass sich Backups wiederherstellen lassen, überprüfen, wer Zugriff hat, und
prüfen, dass der Jahresplan im Zeitplan liegt. Planen Sie dafür einen halben Tag ein, mit dem Hoster oder Partner für
die technischen Teile.

## Die Checkliste

```text
QUARTERLY CHECKLIST - quarter: ________     Owner: ________

RECOVERY (with the host)
[ ] Restore test done on another machine; date ______, time it took ______
[ ] The restored copy opened and showed recent content and orders
[ ] Backup retention still matches the retention schedule

ACCESS
[ ] Every administrator and shop account belongs to a current person
[ ] Roles still match what people do (editors, approvers, store staff)
[ ] Two-step sign-in in place for administrators, as decided

SOFTWARE (with the partner)
[ ] Extensions updated, or the reason they are not
[ ] Unused extensions listed for removal
[ ] PHP version still supported; next upgrade planned

CONTENT
[ ] Content audit of one section: outdated pages updated, hidden or removed
[ ] Accessibility spot check of the main pages and one form
[ ] Menus and landing pages still lead where visitors go

MONEY
[ ] Costs of the quarter against the budget (cost register)
[ ] Figures for the ROI worksheet collected for the quarter

PLAN
[ ] Decisions of the annual value review: on track? Owners reminded
[ ] Campaigns of the next quarter: dates and preparation started
```

## Wo jeder Punkt erledigt wird

| Punkt | Lesen Sie |
|---|---|
| Wiederherstellungstest | [Wiederherstellungsübungen](../../../../../en/us/help/businesses/reliability/restore-drills.md), [Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md) |
| Zugriffsüberprüfung | [Zugriffsüberprüfungen](../../../../../en/us/help/businesses/security/access-reviews.md), [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) |
| Zweistufige Anmeldung | [Zwei-Faktor-Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) |
| Updates der Erweiterungen | [Budget für Upgrades und Wartung](../../../../../en/us/help/businesses/costs/budgeting-for-upgrades-and-maintenance.md) |
| PHP-Unterstützung | `README.md`: PHP 8.0 bis 8.5 unterstützt; Ihr Hoster plant das Upgrade |
| Inhalts-Audit | [Die Inhaltsprüfungen](../../../../../en/us/features/6.0/content-checks.md), [Veröffentlichen und Versionen](../../../veroeffentlichen-und-versionen.md) |
| Barrierefreiheit | [Barrierefreiheit testen](../../../../../en/us/help/creators/website-creators/testing-accessibility.md) |
| Kosten und ROI-Zahlen | [Betriebskosten für Betreiber](../betriebskosten-fuer-betreiber.md), [Den ROI messen](../den-roi-messen.md) |
| Nachverfolgung des Plans | [Jährliche Überprüfung des Nutzens](../jaehrliche-ueberpruefung-des-nutzens.md) |

## Warum der Wiederherstellungstest am wichtigsten ist

Ein Backup ist erst durch eine Wiederherstellung bewiesen. Der Wartungskalender im Bereich für Unternehmen sieht in
jedem Quartal einen Wiederherstellungstest vor ([Budget für Upgrades und Wartung](../../../../../en/us/help/businesses/costs/budgeting-for-upgrades-and-maintenance.md)).
Bitten Sie jedes Mal um eine kurze schriftliche Notiz: das Datum, welches Backup, wie lange es gedauert hat und was auf
der wiederhergestellten Kopie geprüft wurde. Bewahren Sie die Notizen auf; sie sind der Nachweis, dass Ihre
Wiederherstellung funktioniert.

> **Warnung**
> Ein Wiederherstellungstest läuft auf einem anderen Rechner oder einer Kopie, nie über der Live-Website. Stellen Sie
> sicher, dass Ihr Hoster das verstanden hat, bevor er beginnt.

## Verwandte Seiten

- [Monatliche Checkliste](monatlich.md)
- [Jährliche Checkliste](jaehrlich.md)
- [Wiederherstellungsübungen](../../../../../en/us/help/businesses/reliability/restore-drills.md)
- [Zugriffsüberprüfungen](../../../../../en/us/help/businesses/security/access-reviews.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
