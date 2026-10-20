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
Planung ([Ein Exponential-Projekt planen](../../../en/us/help/leadership/planning-an-exponential-project.md)).

## Die Phasen

| Phase | Ergebnisse | Hilfe |
|---|---|---|
| 1. Analyse | Ziele, Zielgruppen, Sprachen, Websites, Integrationen, Rahmenbedingungen | [Ein Exponential-Projekt planen](../../../en/us/help/leadership/planning-an-exponential-project.md), [Mehrere Websites und Sprachen planen](../../../en/us/help/leadership/multisite-and-multilingual-planning.md) |
| 2. Inhaltsmodell | Klassen, Attribute, Sektionen, Rollen, Workflows | [Workshops zur Inhaltsmodellierung](../../../en/us/help/leadership/content-modeling-workshops.md) |
| 3. Umgebung | Hosting, PHP, Datenbank, Engine; Git-Repository | [Hosting, Datenbank und Betriebssystem wählen](../../../en/us/help/leadership/choosing-hosting-database-and-os.md), [Mit Git entwickeln](../../../en/us/help/develop-with-git.md) |
| 4. Umsetzung | Designs, Templates, Layouts, Erweiterungen | [Der Layouts-Editor](../../../en/us/help/layouts-editor.md), [Erweiterungen](../../../en/us/guides/extensions.md), [Schätzen und Abgrenzen mit Erweiterungen](../../../en/us/help/leadership/estimating-and-scoping-with-extensions.md) |
| 5. Migration der Inhalte | Importskripte oder manuelle Eingabe, Weiterleitungen | Die Migrationskapitel in `doc/en/us/install/` |
| 6. Qualitätsschranken | Barrierefreiheit, Sicherheit, Leistung, Browser, Sprachen | [Qualitätsschranken und Abnahme](../../../en/us/help/leadership/quality-gates-and-acceptance.md), [Checklisten für Barrierefreiheit, Sicherheit und Compliance](../../../en/us/help/leadership/accessibility-security-and-compliance-checklists.md) |
| 7. Start | DNS, TLS, Caches, Überwachung, Backups | [Sicherheitshärtung](../../../en/us/help/security-hardening.md) |
| 8. Übergabe | Schulung der Redaktion, Dokumentation, sicher übergebene Zugangsdaten | [Erste Schritte](../../../en/us/help/getting-started.md), [Inhalte bearbeiten](../../../en/us/help/editing-content.md) |
| 9. Wartung | Release-Updates, Sicherheitskorrekturen, PHP-Upgrades | [Kunden-Websites aktuell halten](kunden-websites-aktuell-halten.md), [Release- und Upgrade-Planung](../../../en/us/help/leadership/release-and-upgrade-planning.md) |

## Partnerspezifische Prüfungen je Phase

- **Analyse:** Gibt es für jede Anforderung bereits eine Erweiterung? Suchen Sie, bevor Sie schätzen
  ([Erweiterungen](../../../en/us/guides/extensions.md#2-find-an-extension)).
- **Umgebung:** Verwenden Sie ein aktuell unterstütztes PHP (8.0 bis 8.5 für 6.0.15; Velocity braucht 8.1 oder später,
  `doc/en/us/install/02-requirements.md`).
- **Umsetzung:** Halten Sie allgemeine Verbesserungen getrennt vom Kundencode, damit sie an die Upstream-Projekte gehen
  können ([Code zurückgeben](code-zurueckgeben.md)).
- **Qualität:** Prüfen Sie die Website des Kunden im hellen und dunklen Modus und bei starker Vergrößerung, wenn Sie die
  Administration geändert haben.
- **Start:** Entfernen oder schützen Sie die Installer-Dateien und ändern Sie jedes Standardpasswort
  ([Standardpasswort des Administrators](../../../en/us/help/default-administrator-password.md)).
- **Übergabe:** Sagen Sie dem Kunden, dass die Software unter der GPL steht und dass er jederzeit zu einem anderen
  Partner wechseln darf ([Lizenzierung für Partner](lizenzierung-fuer-partner.md)).

## Risiken, die Sie steuern sollten

| Risiko | Gegenmaßnahme | Seite |
|---|---|---|
| Nicht gepflegte Erweiterung im kritischen Pfad | Gepflegte Erweiterungen wählen; Budget für die Pflege der übrigen einplanen | [Risikomanagement](../../../en/us/help/leadership/risk-management.md) |
| Wachsender Umfang | Änderungswünsche schriftlich | [Projektvorlagen](../../../en/us/help/leadership/project-templates.md) |
| Upgrade-Rückstand nach dem Start | Ein Wartungsvertrag mit Release-Updates | [Kunden-Websites aktuell halten](kunden-websites-aktuell-halten.md) |
| Wissen in einem einzigen Kopf | Git-Historie, Dokumentation, Arbeit in Paaren | [Ein Team mit Git führen](../../../en/us/help/leadership/running-a-team-on-git.md) |

## Nach dem Projekt

- Bitten Sie um Erlaubnis, eine Fallstudie zu veröffentlichen und den Kunden zu nennen ([Eine Fallstudie schreiben](eine-fallstudie-schreiben.md)).
- Erfassen Sie das Projekt für Punkte ([Ihre Partneraktivität erfassen](partneraktivitaet-erfassen.md)).
- Fügen Sie das Abzeichen „Powered by Exponential“ hinzu, wenn der Kunde zustimmt ([„Powered by Exponential“ zeigen](powered-by-exponential.md)).

## Verwandte Seiten

- [Implementierungspartner](implementierungspartner.md)
- [Projektverantwortliche: hier beginnen](../../../en/us/help/leadership/project-managers-start-here.md)
- [Releases testen, bevor Ihre Kunden es tun](releases-testen.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
