---
title: Geplante Änderungen mit OpenSpec
audience: developer
category: standards
order: 95
summary: Wie eine größere Änderung an Exponential geplant wird, bevor sie gebaut wird - das Verzeichnis OpenSpec, das Schema plan-driven, die Reihenfolge Vorschlag, Specs, Entwurf, Aufgaben, Plan, Umsetzung und Prüfung, und wohin fertige Änderungen und ihre Specs gehen.
keywords: OpenSpec, Spezifikation, Vorschlag, Entwurf, Aufgaben, Plan, Prüfung, Änderung, Archiv, plan-driven, Schema, Übergabe, Workflow-Designer, Suche in der Administration, specification, proposal, design, tasks, verify, change, archive, handoff
type: reference
---
# Geplante Änderungen mit OpenSpec

Diese Seite richtet sich an Entwickler, die eine größere Änderung an Exponential planen oder eine Änderung bauen, die
jemand anderes geplant hat. Sie erklärt das Verzeichnis `openspec/` im Wurzelverzeichnis des Repositorys und wie eine
Änderung es durchläuft. Kleine Korrekturen brauchen es nicht; eine Änderung, die eine Funktion, ein Modul oder eine
Erweiterung hinzufügt oder mehrere Designs berührt, sollte dort beginnen.

OpenSpec ([github.com/Fission-AI/OpenSpec](https://github.com/Fission-AI/OpenSpec)) hält den Plan einer Änderung als
einfaches Markdown neben dem Code, sodass der Plan im selben Pull Request wie der Code geprüft wird und in der
Historie bleibt. Exponential lädt zur Laufzeit nichts aus `openspec/`.

## Das Verzeichnis

| Pfad | Inhalt |
|---|---|
| `openspec/config.yaml` | Der Projektkontext und die Regeln, denen jedes Artefakt folgt: Architekturregeln, die Prüfungen, die eine Änderung nennt, die Plattformmatrix aus Datenbanken und Suchmaschinen |
| `openspec/schemas/plan-driven/` | Das Schema: welche Artefakte eine Änderung hat, in welcher Reihenfolge, und die Vorlage jedes Artefakts. `SOURCE` sagt, wo das gemeinsame Schema gepflegt wird |
| `openspec/changes/<name>/` | Eine laufende Änderung: `proposal.md`, `specs/`, `design.md`, `tasks.md`, `plan.md`, `verify.md` |
| `openspec/changes/archive/<datum>-<name>/` | Eine fertige und gemergte Änderung, so aufbewahrt, wie sie war |
| `openspec/specs/` | Das aktuelle Verhalten, eine Spec je Fähigkeit; eine archivierte Änderung führt ihre Specs hier zusammen |
| `openspec/handoff/` | Eine längere Spezifikation eines Tickets, auf die Änderungen als Entwurfsquelle verweisen, mit ihren Mockups |

## Die Schritte einer Änderung

Das Schema `plan-driven` ordnet die Artefakte als **Vorschlag, Specs, Entwurf, Aufgaben, Plan, Umsetzung, Prüfung**:

1. **Vorschlag** (`proposal.md`): warum die Änderung nötig ist, was sich ändert, welches Ticket sie umsetzt und was
   nicht dazugehört.
2. **Specs**: das beobachtbare Verhalten als Anforderungen mit Szenarien (*WHEN* ... *THEN* ...); keine Klassennamen
   oder Dateipfade. Wo sich das Verhalten je Siteaccess, Rolle oder Suchmaschine unterscheidet, sagt es ein Szenario.
3. **Entwurf** (`design.md`): die Erweiterung, die die Änderung trägt (und warum eine Änderung in `lib/` oder
   `kernel/` nötig ist), die INI-Dateien und Schlüssel, die Richtlinien und Einschränkungen, die Datenbanktabellen
   und wie eine bestehende Installation sie erhält.
4. **Aufgaben** (`tasks.md`): Gruppen von Arbeit; jede Gruppe bringt ihre eigenen Tests und ihre Dokumentation mit.
5. **Plan** (`plan.md`): die Aufgaben als ausführbare Schritte, Test zuerst.
6. **Umsetzung**: die Arbeit, in einem eigenen Worktree, nie im Haupt-Checkout.
7. **Prüfung** (`verify.md`): die genaue phpunit-Aufrufzeile und das Ergebnis jeder betroffenen Suite und die
   übrigen Projektprüfungen.

Ist die Änderung gemergt, wandert sie nach `openspec/changes/archive/`, und ihre Specs aktualisieren `openspec/specs/`.

## Geplante und fertige Änderungen

| Änderung | Stand |
|---|---|
| `add-workflow-definitions`, `add-workflow-runtime`, `add-workflow-jobs-and-timers`, `add-workflow-admin-ui`, `add-workflow-review-options`, `add-workflow-integrations`, `add-workflow-webhooks-and-importers`, `add-workflow-reference-cases` | Geplant: die grafischen redaktionellen Workflows der Erweiterung `expworkflow`; Entwurfsquelle `openspec/handoff/expworkflow-16702-spec.md` |
| `archive/2026-11-02-add-admin-search` | Fertig: Suchvorschläge beim Tippen und die Suchseite mit ihrer Filterleiste; Specs in `openspec/specs/admin/` |

Die Artefakte von OpenSpec selbst sind auf Englisch geschrieben.

## Verwandte Seiten

- [Tests und Continuous Integration](tests-und-ci.md)
- [Versionsverwaltung und Releases](versionsverwaltung-und-releases.md)
- [Dokumentation und Veröffentlichung](dokumentation-und-veroeffentlichung.md)
- [Programmierstandards](programmierstandards.md)
