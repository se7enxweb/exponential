---
title: Vorlage für eine Wartungsvereinbarung
audience: customer, partner, hosting
category: customer-success
order: 8
summary: Eine Vorlage für die Vereinbarung, die eine Exponential-Website aktuell hält - Release-Updates, Sicherheitskorrekturen, PHP-Upgrades, Pflege der Erweiterungen, Prüfungen nach jedem Update, Berichte und die jährliche Überprüfung - mit auszufüllenden Feldern. Keine Rechtsberatung.
keywords: Wartungsvereinbarung, Vorlage, Updates, Upgrades, Sicherheits-Releases, PHP-Upgrade, Erweiterungen, Upgrade-Prüfung, Bericht, keine Rechtsberatung, maintenance agreement, template, updates, upgrades, security releases, PHP upgrade, extensions, upgrade check, report
type: help
---
# Vorlage für eine Wartungsvereinbarung

**Vorlage. Keine Rechtsberatung.** Korrekturen erreichen eine Exponential-Website nur als nächstes Release
(`SECURITY.md`), die Pflege der Website ist also eine Aufgabe, die nie endet. Diese Vorlage hält diese Aufgabe
schriftlich fest. Sie folgt der Upgrade-Praxis, die unter [Upgrades mit Ihrem Anbieter](../gesund-halten/upgrades-mit-ihrem-anbieter.md),
[Kunden-Websites aktualisieren](../../../../en/us/help/hosting/operations/upgrading-customer-sites.md) und
[Kunden aktuell halten](../../../../en/us/help/partners/keeping-customers-current.md) beschrieben ist. Werte in eckigen Klammern sind
Felder, die zu vereinbaren sind. Lassen Sie das Ergebnis von Ihrer Rechtsberatung prüfen.

## Parteien und Umfang

| Feld | Wert |
|---|---|
| Kunde | [organization name] |
| Anbieter | [host, partner or developer] |
| Websites und Umgebungen | [addresses; live, staging] |
| Ausgangsversion | [Exponential version], PHP [version], Datenbank [engine and version], Engine [Velocity / Apache or nginx with PHP-FPM / other] |
| Gültig ab | [date] |

## Updates

| Aufgabe | Vereinbart |
|---|---|
| Der gepflegten Linie folgen | Upgrade auf das neueste Release 6.0.x innerhalb von [agreed time] nach seinem Erscheinen |
| Releases mit Sicherheitskorrekturen | Eingespielt innerhalb von [agreed time] nach dem Release |
| Erweiterungen | Mit jedem Upgrade aktualisiert; Erweiterungen ohne gepflegtes Release werden dem Kunden gemeldet |
| PHP-Version | Auf einem PHP-Zweig mit Sicherheitsunterstützung gehalten; Umstellungen [time] im Voraus geplant ([PHP-Upgrade](../../../../en/us/help/hosting/runbooks/php-upgrade.md)) |
| Betriebssystem und Datenbankserver | [who, how often] |
| Zuerst auf einer Kopie ausprobiert | [always / for releases that change the database / never] |
| Backup direkt vor jedem Update | Datenbank und Dateien ([Backups](../gesund-halten/backups.md)) |
| Zeitfenster | [day and time, time zone], angekündigt [days ahead] |

## Nach jedem Update erhält der Kunde

| Nachweis | Quelle |
|---|---|
| Alte und neue Version | **Setup > Systeminformationen** |
| Ergebnis der Upgrade-Prüfung, mit Erklärung bewusst geänderter Dateien | **Setup > Upgrade-Prüfung** ([Die Upgrade-Prüfung](../../../../en/us/guides/upgrade-check.md)) |
| Zusammenfassung dessen, was sich für Redaktion und Besucher ändert | Das Changelog und die Hinweise zu Verhaltensänderungen, in einfachen Worten |
| Durchgeführte Prüfungen | Startseite, Anmeldung, Veröffentlichen, Formulare, Shop, jede Website und Sprache |
| Offene Punkte | Zu prüfende Einstellungen, Erweiterungen, die auf ein Release warten |

## Regelmäßige Prüfungen

| Prüfung | Häufigkeit | Berichtet |
|---|---|---|
| Statusprüfungen der Systeminformationen | [interval] | [yes / no] |
| Fehlerübersicht und Logs | [interval] | Zusammenfassung [interval] |
| Verifizierung des Prüfprotokolls | [interval] | [yes / no] |
| Überprüfung der Konten und der Rolle Administrator | [interval] | Änderungen aufgelistet |
| Wiederherstellungstest | [interval] | Das Testprotokoll ([Wiederherstellungstest](../gesund-halten/wiederherstellungstest.md)) |

## Eigener Code

| Feld | Vereinbart |
|---|---|
| Wo der Code der Website aufbewahrt wird | [repository, owned by the customer or copyable on request] |
| Änderungen an den eigenen Dateien von Exponential | [none / kept in version control with a reason] |
| Allgemeine Korrekturen an das Projekt zurückgegeben | [yes / no] ([Code zurückgeben](../../../../en/us/help/partners/contributing-code-back.md)) |
| Arbeit, um eigenen Code bei einem Upgrade anzupassen | [included up to [hours] / quoted separately] |

## Berichte und Überprüfung

| Feld | Wert |
|---|---|
| Wartungsbericht | [monthly / quarterly]: Versionen, eingespielte Updates, Prüfungen, offene Punkte |
| Jährliche Überprüfung | [date], nach [Ihren Anbieter jedes Jahr überprüfen](anbieter-jedes-jahr-ueberpruefen.md) |
| Verzeichnis | Der Anbieter führt ein Verzeichnis der Versionen, Engine, Datenbank und Erweiterungen der Website |

## Laufzeit und Ausstieg

| Feld | Wert |
|---|---|
| Laufzeit und Kündigungsfrist | [term], [notice period] |
| Am Ende | Übergabe wie in der [Checkliste für die Übergabe](checkliste-uebergabe.md) |

## Unterschriften

| | Kunde | Anbieter |
|---|---|---|
| Name und Rolle | [name, role] | [name, role] |
| Datum | [date] | [date] |

## Verwandte Seiten

- [Wie Updates funktionieren](../gesund-halten/wie-updates-funktionieren.md)
- [SLA-Vorlage](sla-vorlage.md)
- [Budget für Upgrades und Wartung](../../../../en/us/help/businesses/costs/budgeting-for-upgrades-and-maintenance.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
