---
title: Upgrades im Haushalt planen
audience: government, project-manager, admin
category: public-sector
order: 60
summary: Wie eine öffentliche Stelle Upgrades von Exponential über eine Vertragslaufzeit budgetiert - routinemäßige Updates innerhalb von 6.0.x, Upgrades von älteren Linien, Plattform-Upgrades - mit den Schritten, die jedes umfasst, den Werkzeugen, die den Aufwand senken, und einer Budgetvorlage ohne erfundene Zahlen.
keywords: Upgrade-Budget, Upgrade-Planung, Wartungsbudget, Update auf 6.0.x, Migration, Upgrade-Prüfung, Staging, Aufwand, mehrjähriger Haushalt, öffentlicher Sektor, upgrade budget, upgrade planning, maintenance budget, 6.0.x update, migration, upgrade check, staging, effort, multi-year budget, public sector
type: help
---
# Upgrades im Haushalt planen

Diese Seite richtet sich an die Projektleitung oder die Person mit Budgetverantwortung, die Geld und Arbeitszeit dafür
planen muss, eine Exponential-Website aktuell zu halten. Sie erklärt die Arten von Upgrades, was jede umfasst, und gibt
eine Vorlage. Die Schritte stammen aus [Aktualisieren](../../../../anleitungen/aktualisieren.md), [11. Aktualisieren](../../../../installation/11-aktualisieren.md) und
[Mit Git aktualisieren](../../../mit-git-aktualisieren.md).

## Inhalt

1. [Drei Arten von Upgrades](#drei-arten-von-upgrades)
2. [Was ein routinemäßiges Update innerhalb von 6.0.x umfasst](#was-ein-routinemäßiges-update-innerhalb-von-60x-umfasst)
3. [Was den Aufwand treibt](#was-den-aufwand-treibt)
4. [Eine Budgetvorlage](#eine-budgetvorlage)
5. [Fallstricke](#fallstricke)

## Drei Arten von Upgrades

| Art | Wann | Treiber des Aufwands |
|---|---|---|
| Routinemäßiges Update innerhalb von 6.0.x | bei jedem neuen Release und immer nach einer Sicherheitskorrektur | Zahl der eigenen Erweiterungen und Template-Overrides |
| Upgrade von einer älteren Linie (4.x, 5.x, die Symfony-Plattformen) auf 6.0 | einmalig, oft als eigenes Projekt | eigener PHP-Code, das Inhaltsmodell, Datenmenge |
| Plattform-Upgrade (PHP, Datenbank, Betriebssystem) | wenn eine Plattformversion ihre Unterstützung verliert | Kompatibilität Ihres eigenen Codes |

Sicherheitskorrekturen kommen nur als neue Releases ([Unterstützte Versionen](unterstuetzte-versionen.md)), eine Website, die
keine routinemäßigen Updates durchführen kann, kann also keine Korrekturen erhalten. Budgetieren Sie zuerst die Routine.

## Was ein routinemäßiges Update innerhalb von 6.0.x umfasst

Das Verfahren hat sieben Schritte, über die Kommandozeile oder in der Administration
([Mit Git aktualisieren](../../../mit-git-aktualisieren.md#die-sieben-schritte)):

1. sichern und ein Wartungsfenster beginnen ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md));
2. ansehen, was ansteht;
3. den Kernel aktualisieren (Git oder **Git Manager** in der Administration);
4. die Erweiterungen aktualisieren (Composer oder **Setup > Updates and packages**);
5. die Datenbank prüfen und aktualisieren;
6. die Caches leeren und den Code neu laden;
7. die Website prüfen und das Fenster verlassen.

Vorher vergleicht **Setup > Upgrade-Prüfung** die installierten Dateien mit dem Release-Manifest und listet direkt
geänderte Dateien auf, die überschrieben würden ([Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md)). Für jeden
Zeitraum zwischen alter und neuer Version bittet der Upgrade-Leitfaden Sie, seine Checkliste zu lesen und Ihre eigenen
Erweiterungen zu prüfen ([Aktualisieren, Teil C](../../../../anleitungen/aktualisieren.md#teil-c-von-einer-beliebigen-60x-auf-den-aktuellen-stand)).

## Was den Aufwand treibt

| Faktor | Geringerer Aufwand | Höherer Aufwand |
|---|---|---|
| Eigener Code | Website mit Konfiguration gebaut, Overrides in einer Design-Erweiterung | geänderte Kernel-Dateien, viele eigene PHP-Erweiterungen |
| Wie weit zurück | ein Release | viele Releases, mehrere Checklisten abzuarbeiten |
| Testabdeckung | eine Staging-Kopie und ein schriftlicher Testplan | Tests auf der Live-Website |
| Engine | eine Engine, dokumentiert | mehrere, undokumentiert |
| Wissen | Lieferant und hauseigenes Personal geschult | Wissen bei einem früheren Lieferanten geblieben |

Die günstigsten Upgrades sind häufige kleine. Eine Website, die jahrelang Releases überspringt, endet mit einem
Upgrade-Projekt.

## Eine Budgetvorlage

Alle Zahlen sind Ihre eigenen Schätzungen; das Projekt veröffentlicht keine.

```text
Upgrade budget - <site>                                  term <years>

Per routine update
  Supplier or staff hours (steps 1-7 + regression test)   <h> hours   x <rate>
  Number of updates per year (your policy, e.g. "every release" or "quarterly")   <n>
  Staging environment (if not already in the hosting fee) <amount>/year

Security updates outside the routine                      <n per year, estimate> x <h> hours x <rate>

Platform upgrades over the term
  PHP version change                                      <h> hours x <rate>, in year <y>
  Database version change                                 <h> hours x <rate>, in year <y>
  OS change                                               <h> hours x <rate>, in year <y>

One-off migration (if coming from an older line)          <separate project budget>
Contingency                                               <percentage of the above>
```

Bitten Sie die Bieter, ihre Stunden je routinemäßigem Update für Ihre Website einzutragen, damit sich Angebote
vergleichen lassen. Der Teil zu den Gesamtbetriebskosten dieses Abschnitts zur Beschaffung zeigt, wie sich diese Zeile
in das gesamte Modell einfügt.

## Fallstricke

- **Keine Staging-Kopie.** Jedes Upgrade ist dann ein Test auf der Live-Website.
- **Geänderte Kernel-Dateien.** Die Upgrade-Prüfung findet sie; die Lösung ist, die Änderung in eine Erweiterung oder
  einen Override zu verlagern ([Templates und Design](../../../../anleitungen/templates-und-design.md)).
- **Nur das erste Jahr budgetieren.** Upgrades sind jährliche Kosten.
- **Eine Sicherheitskorrektur als optional behandeln.** Eine Korrektur kommt nur mit einem Upgrade.

## Verwandte Seiten

- [Der Lebenszyklus des Produkts](README.md)
- [Sicherheitsupdates und Patch-Rhythmus](sicherheitsupdates-und-patch-rhythmus.md)
- [Aktualisieren](../../../../anleitungen/aktualisieren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
