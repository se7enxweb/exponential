---
title: Ein Leitfaden für Implementierungsprojekte
audience: partner
category: partnership
order: 360
summary: Ein Leitfaden Phase für Phase für Partner, die ein Exponential-Projekt liefern - Analyse, Inhaltsmodell, Umgebung, Umsetzung, Migration der Inhalte, Qualitätsschranken, Start, Übergabe und Wartung - mit Ergebnissen, Checklisten und den Hilfeseiten hinter jeder Phase.
keywords: Leitfaden, Implementierung, Projekt, Phasen, Analyse, Inhaltsmodell, Umsetzung, Migration, Start, Übergabe, Wartung, Qualitätsschranken, Ergebnisse, playbook, implementation, project, phases, discovery, content model, build, migration, launch, handover, maintenance, quality gates, deliverables
type: help
---
# Ein Leitfaden für Implementierungsprojekte

Diese Seite ist für Projektleitungen bei Implementierungspartnern. Sie ist ein Leitfaden, keine Methode, der Sie folgen
müssen: Passen Sie ihn an Ihre Verträge an. Der Bereich der Hilfe für Projektverantwortliche enthält die Details zur
Planung ([Ein Exponential-Projekt planen](../fuehrung/ein-exponential-projekt-planen.md)).

## Die Phasen

| Phase | Ergebnisse | Hilfe |
|---|---|---|
| 1. Analyse | Ziele, Zielgruppen, Sprachen, Websites, Integrationen, Rahmenbedingungen | [Ein Exponential-Projekt planen](../fuehrung/ein-exponential-projekt-planen.md), [Mehrere Websites und Sprachen planen](../fuehrung/multisite-und-mehrsprachigkeit-planen.md) |
| 2. Inhaltsmodell | Klassen, Attribute, Sektionen, Rollen, Workflows | [Workshops zur Inhaltsmodellierung](../fuehrung/workshops-zur-inhaltsmodellierung.md) |
| 3. Umgebung | Hosting, PHP, Datenbank, Engine; Git-Repository | [Hosting, Datenbank und Betriebssystem wählen](../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md), [Mit Git entwickeln](../mit-git-entwickeln.md) |
| 4. Umsetzung | Designs, Templates, Layouts, Erweiterungen | [Der Layouts-Editor](../layout-editor.md), [Erweiterungen](../../anleitungen/extensions.md), [Schätzen und Abgrenzen mit Erweiterungen](../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md) |
| 5. Migration der Inhalte | Importskripte oder manuelle Eingabe, Weiterleitungen | Die Migrationskapitel in `doc/eng-us/install/` |
| 6. Qualitätsschranken | Barrierefreiheit, Sicherheit, Leistung, Browser, Sprachen | [Qualitätsschranken und Abnahme](../fuehrung/qualitaetsschranken-und-abnahme.md), [Checklisten für Barrierefreiheit, Sicherheit und Compliance](../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md) |
| 7. Start | DNS, TLS, Caches, Überwachung, Backups | [Sicherheitshärtung](../sicherheitshaertung.md) |
| 8. Übergabe | Schulung der Redaktion, Dokumentation, sicher übergebene Zugangsdaten | [Erste Schritte](../erste-schritte.md), [Inhalte bearbeiten](../inhalte-bearbeiten.md) |
| 9. Wartung | Release-Updates, Sicherheitskorrekturen, PHP-Upgrades | [Kunden-Websites aktuell halten](kunden-websites-aktuell-halten.md), [Release- und Upgrade-Planung](../fuehrung/release-und-upgrade-planung.md) |

## Partnerspezifische Prüfungen je Phase

- **Analyse:** Gibt es für jede Anforderung bereits eine Erweiterung? Suchen Sie, bevor Sie schätzen
  ([Erweiterungen](../../anleitungen/extensions.md#2-eine-extension-finden)).
- **Umgebung:** Verwenden Sie ein aktuell unterstütztes PHP (8.0 bis 8.5 für 6.0.15; auch für Velocity ab seinem Release 0.0.4.54,
  `doc/eng-us/install/02-requirements.md`).
- **Umsetzung:** Halten Sie allgemeine Verbesserungen getrennt vom Kundencode, damit sie an die Upstream-Projekte gehen
  können ([Code zurückgeben](code-zurueckgeben.md)).
- **Qualität:** Prüfen Sie die Website des Kunden im hellen und dunklen Modus und bei starker Vergrößerung, wenn Sie die
  Administration geändert haben.
- **Start:** Entfernen oder schützen Sie die Installer-Dateien und ändern Sie jedes Standardpasswort
  ([Standardpasswort des Administrators](../standard-administratorpasswort.md)).
- **Übergabe:** Sagen Sie dem Kunden, dass die Software unter der GPL steht und dass er jederzeit zu einem anderen
  Partner wechseln darf ([Lizenzierung für Partner](lizenzierung-fuer-partner.md)).

## Risiken, die Sie steuern sollten

| Risiko | Gegenmaßnahme | Seite |
|---|---|---|
| Nicht gepflegte Erweiterung im kritischen Pfad | Gepflegte Erweiterungen wählen; Budget für die Pflege der übrigen einplanen | [Risikomanagement](../fuehrung/risikomanagement.md) |
| Wachsender Umfang | Änderungswünsche schriftlich | [Projektvorlagen](../fuehrung/projektvorlagen.md) |
| Upgrade-Rückstand nach dem Start | Ein Wartungsvertrag mit Release-Updates | [Kunden-Websites aktuell halten](kunden-websites-aktuell-halten.md) |
| Wissen in einem einzigen Kopf | Git-Historie, Dokumentation, Arbeit in Paaren | [Ein Team mit Git führen](../fuehrung/ein-team-mit-git-fuehren.md) |

## Nach dem Projekt

- Bitten Sie um Erlaubnis, eine Fallstudie zu veröffentlichen und den Kunden zu nennen ([Eine Fallstudie schreiben](eine-fallstudie-schreiben.md)).
- Erfassen Sie das Projekt für Punkte ([Ihre Partneraktivität erfassen](partneraktivitaet-erfassen.md)).
- Fügen Sie das Abzeichen „Powered by Exponential“ hinzu, wenn der Kunde zustimmt ([„Powered by Exponential“ zeigen](powered-by-exponential.md)).

## Verwandte Seiten

- [Implementierungspartner](implementierungspartner.md)
- [Projektverantwortliche: hier beginnen](../fuehrung/projektleiter-hier-beginnen.md)
- [Releases testen, bevor Ihre Kunden es tun](releases-testen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
