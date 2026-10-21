---
title: Das erste Upgrade im Budget einplanen
audience: government, project-manager, admin
category: public-sector
order: 210
summary: Schritt für Schritt das erste routinemäßige Upgrade einer neuen Exponential-Site in einer Behörde planen und budgetieren - die Ausgangsversion, die Upgrade-Prüfung, Staging, Stunden des Lieferanten, ein Wartungsfenster und der Nachweis, der das nächste Upgrade günstiger macht.
keywords: erstes Upgrade, Budget für Upgrades, Planung, Upgrade-Prüfung, Staging, Wartungsfenster, Stunden des Lieferanten, routinemäßiges Update, Aufgabe zur Beschaffung, first upgrade, upgrade budget, planning, upgrade check, staging, maintenance window, supplier hours, routine update, procurement task
type: help
---
# Das erste Upgrade im Budget einplanen

**Wer:** der Projektleiter mit dem Site-Administrator. **Wann:** beim Go-live, für die ersten Monate des Betriebs. Das
erste Upgrade zeigt, ob die Site so gebaut wurde, dass sie sich aktualisieren lässt; planen Sie es früh. Hintergrund:
[Upgrades im Budget einplanen](../../beschaffung/lebenszyklus/upgrades-im-haushalt-planen.md).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Ergebnis](#ergebnis)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Die bei der Abnahme gelieferte Version, aus **Setup > Systeminformationen**
  ([Systeminformationen](../../../../anleitungen/systeminformationen.md)).
- Die Stunden des Lieferanten je routinemäßigem Update, aus dem Angebot.

## Schritte

1. **Halten Sie den Ausgangspunkt fest**: Version des Kernels, Versionen der Erweiterungen, Engine, PHP, Datenbank.
2. **Führen Sie Setup > Upgrade-Prüfung** direkt nach dem Go-live aus: Jede an Ort und Stelle geänderte Datei sind
   künftige Kosten eines Upgrades; bitten Sie den Lieferanten, solche Änderungen jetzt in Erweiterungen oder Overrides
   zu verlegen ([Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md)).
3. **Prüfen Sie, ob Staging existiert** und der Produktion entspricht.
4. **Wählen Sie den Auslöser**: das nächste Release 6.0.x oder das erste Sicherheitsrelease, je nachdem, was zuerst
   kommt ([Unterstützte Versionen](../../beschaffung/lebenszyklus/unterstuetzte-versionen.md)).
5. **Budgetieren Sie es**: die Stunden des Lieferanten für die sieben Schritte plus einen Regressionstest, die Zeit
   Ihrer Mitarbeiter für Tests und eine Reserve ([Mit Git aktualisieren](../../../mit-git-aktualisieren.md#die-sieben-schritte)).
6. **Vereinbaren Sie ein Wartungsfenster** und wie es angekündigt wird
   ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md)).
7. **Schreiben Sie die Testliste**, die Sie nach jedem Upgrade verwenden (Startseite, Suche, ein Formular, Anmeldung,
   Veröffentlichen).
8. **Halten Sie nach dem Upgrade** Stunden, Probleme und Korrekturen **fest**; verwenden Sie sie, um die folgenden
   Upgrades zu budgetieren ([Bereitschaft für Upgrades](../../checklisten/bereitschaft-fuer-aktualisierungen.md)).

## Ergebnis

Ein erstes Upgrade, pünktlich und im Budget erledigt, und echte Zahlen für die jährliche Position der Upgrades.

## Fallstricke

- Keine Budgetposition für Upgrades im ersten Jahr.
- Eine Site mit geänderten Dateien des Kernels abnehmen.
- Das erste Upgrade aufschieben, bis sich mehrere Releases angesammelt haben.

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Kosten für Upgrades und Wartung](../../beschaffung/gesamtbetriebskosten/kosten-fuer-upgrades-und-wartung.md)
- [Aktualisieren](../../../../anleitungen/aktualisieren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
