---
title: Upgrades mit Ihrem Anbieter
audience: customer, businesses, hosting
category: customer-success
order: 3
summary: Wie ein Website-Betreiber mit einem Hoster oder Partner zusammenarbeitet, der die Upgrades durchführt - was zu vereinbaren ist, was vor und nach jedem Upgrade zu fragen ist, welche Nachweise zu erwarten sind und welche Entscheidungen bei Ihnen bleiben.
keywords: Upgrade, Anbieter, Hoster, Partner, Wartungsvertrag, Nachweise, Upgrade-Prüfung, Changelog, Staging, Zurücksetzen, upgrade, provider, host, partner, maintenance contract, evidence, upgrade check, changelog, staging, rollback
type: help
---
# Upgrades mit Ihrem Anbieter

Die meisten Website-Betreiber führen Upgrades nicht selbst durch: Ein Hoster, ein Implementierungspartner oder ein
freiberuflicher Entwickler erledigt das. Diese Seite behandelt Ihre Seite dieser Vereinbarung: was einmal zu vereinbaren
ist, was jedes Mal zu fragen ist und welche Nachweise zu erwarten sind. Das eigene Verfahren des Anbieters steht unter
[Kunden-Websites aktualisieren](../../hosting/betrieb/kunden-websites-aktualisieren.md) und, für Partner, unter
[Kunden aktuell halten](../../partner/kunden-websites-aktuell-halten.md).

## Einmal vereinbaren

Schreiben Sie diese Punkte in den Vertrag oder die Support-Vereinbarung (siehe
[Vorlage für eine Wartungsvereinbarung](../partner-und-hoster/vorlage-wartungsvereinbarung.md)):

| Punkt | Was zu klären ist |
|---|---|
| Wer aktualisiert | Ihr Anbieter, Ihr eigener Entwickler oder beide, mit einer klaren Grenze |
| Wie oft | Mindestens so oft, wie neue Releases 6.0.x erscheinen ([Release-Linie und Support](release-linie-und-support.md)) |
| Sicherheits-Releases | Innerhalb welcher Zeit nach dem Release eine Sicherheitskorrektur eingespielt wird; fordern Sie, dass „eine veröffentlichte Korrektur innerhalb einer bestimmten Zeit eingespielt wird“ ([Grundlagen zu Verträgen und SLAs](../../../../en/us/help/businesses/choosing/contracts-and-slas-basics.md)) |
| PHP-Updates | Wer die Website auf eine neuere PHP-Version umstellt, und wann ([Runbook für PHP-Upgrades](../../hosting/runbooks/php-upgrade.md)) |
| Staging | Ob jedes Upgrade zuerst auf einer Kopie ausprobiert wird |
| Zeitfenster | Wann geplante Arbeiten stattfinden dürfen und wie weit im Voraus Sie informiert werden |
| Nachweise | Was Sie nach jedem Upgrade erhalten (siehe unten) |

## Vor jedem Upgrade fragen Sie

- [ ] Welche Version betreibt die Website jetzt, und welche wird sie danach betreiben?
- [ ] Was ändert sich für die Redaktion, für Besucher und für das Team? (Der Anbieter liest das Changelog und die
      Hinweise zu Verhaltensänderungen; bitten Sie um eine Zusammenfassung in einfachen Worten.)
- [ ] Wurde es zuerst auf einer Kopie der Website ausprobiert?
- [ ] Wann ist das Zeitfenster, und werden Besucher eine Wartungsseite sehen?
- [ ] Wird direkt davor ein Backup der Datenbank **und** der Dateien erstellt? „Zuerst sichern, jedes Mal“
      ([Backup-Pläne und Aufbewahrung](../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md)).
- [ ] Wie ist der Weg zurück, wenn etwas fehlschlägt?
- [ ] Welche unserer eigenen Erweiterungen oder angepassten Templates könnten betroffen sein?

## Nach jedem Upgrade erwarten Sie

| Nachweis | Woher er kommt |
|---|---|
| Die neue Versionsnummer | **Setup > Systeminformationen** ([Systeminformationen](../../../../en/us/guides/system-information.md)) |
| Eine saubere Upgrade-Prüfung, oder eine Liste bewusst geänderter Dateien | **Setup > Upgrade-Prüfung** ([Die Upgrade-Prüfung](../../../../en/us/guides/upgrade-check.md)) |
| Das Deployment festgehalten | Das Prüfprotokoll hält Deployments und das Leeren von Caches fest ([Das Prüfprotokoll](../../../../en/us/features/6.0/audit-trail.md)) |
| Ihre wichtigsten Seiten geprüft | Startseite, Anmeldung, eine Veröffentlichung, ein Formular, der Shop, falls Sie einen haben |
| Alles, was noch zu tun ist | Zu prüfende Einstellungen, Erweiterungen, die auf ein Release warten |

Dateien, die der Anbieter bewusst geändert hat, sind in der Upgrade-Prüfung zu erwarten; fragen Sie, welche das sind und
warum ([Upgrade-Prüfung und Dateikonsistenz](../../../../en/us/help/businesses/reliability/upgrade-check-and-file-consistency.md)).

## Der Teil Ihres Teams

- **Testen Sie, was nur Sie wissen.** Der Anbieter prüft, dass die Website funktioniert; Ihre Redakteure wissen, ob die
  richtigen Dinge funktionieren. Führen Sie eine kurze Liste mit fünf bis zehn Seiten und Aktionen und prüfen Sie sie
  nach jedem Upgrade ([Checkliste für den Upgrade-Tag](checkliste-upgrade-tag.md)).
- **Melden Sie Unterschiede schnell**, mit Seitenadresse, Uhrzeit und der Fehlerreferenz, falls eine angezeigt wird
  ([Probleme melden](probleme-melden.md)).
- **Führen Sie Buch.** Notieren Sie das Datum, die alte und die neue Version und wer es durchgeführt hat. Das
  Prüfprotokoll hält den technischen Nachweis fest; Ihre Notiz den geschäftlichen.

## Warnzeichen

- Upgrades sind lange Zeit „nicht nötig“, während neue Releases erscheinen. Korrekturen erreichen Sie nur durch Upgrades.
- Der Anbieter kann Ihnen nicht sagen, welche Version die Website betreibt.
- Änderungen am Code von Exponential selbst werden auf der Live-Website vorgenommen und nirgends aufbewahrt. Die
  Upgrade-Prüfung listet sie auf; ein Anbieter, der solche Änderungen pflegt, sollte sie in einer Versionsverwaltung
  halten und allgemeine Korrekturen zurückgeben ([Code zurückgeben](../../partner/code-zurueckgeben.md)).

## Verwandte Seiten

- [Wie Updates funktionieren](wie-updates-funktionieren.md)
- [Was Sie Ihren Hoster fragen sollten](was-sie-ihren-hoster-fragen-sollten.md)
- [Was Sie Ihren Partner fragen sollten](was-sie-ihren-partner-fragen-sollten.md)
- [Budget für Upgrades und Wartung](../../../../en/us/help/businesses/costs/budgeting-for-upgrades-and-maintenance.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
