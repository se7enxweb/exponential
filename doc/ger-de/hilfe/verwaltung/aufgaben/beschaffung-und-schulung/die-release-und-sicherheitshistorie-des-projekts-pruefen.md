---
title: Die Release- und Sicherheitshistorie des Projekts prüfen
audience: government, project-manager, admin
category: public-sector
order: 100
summary: Schritt für Schritt selbst prüfen, wie oft Exponential veröffentlicht wird, wie Sicherheitsprobleme behandelt und wie Änderungen dokumentiert werden - anhand der Tags, der Changelogs, von SECURITY.md und der Nachweise zur Härtung im Repository - statt sich auf die Beschreibung eines Anbieters zu verlassen.
keywords: Release-Historie, Sicherheitshistorie, Tags, Changelog, SECURITY.md, Härtung, Zustand des Projekts, Due Diligence, Bewertung, Aufgabe zur Beschaffung, release history, security history, tags, changelog, hardening, project health, due diligence, evaluation, procurement task
type: help
---
# Die Release- und Sicherheitshistorie des Projekts prüfen

**Wer:** eine IT-Fachkraft oder der Sicherheitsbeauftragte im Bewertungsteam. **Wann:** vor der Ausschreibung oder
während der Bewertung. Die Methode steht in
[Den Zustand eines Open-Source-Projekts bewerten](../../beschaffung/bewertung/den-zustand-eines-open-source-projekts-bewerten.md).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Was festzuhalten ist](#was-festzuhalten-ist)
4. [Ergebnis](#ergebnis)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

Sie brauchen einen Klon des Repositorys oder einen Browser auf GitHub
([3. Den Code beschaffen](../../../../installation/03-den-code-beschaffen.md)).

## Schritte

1. **Listen Sie die Releases auf**, nach Version sortiert, nicht nach Name (nach Name sortiert steht `v6.0.10` vor
   `v6.0.9`):

   ```bash
   git fetch --tags
   git tag -l 'v6.*' --sort=version:refname | tail -10
   ```

   Die Seite der Standards verzeichnet die Tags `v6.0.0` bis `v6.0.14` und GitHub-Releases ab `v6.0.10`
   ([Versionsverwaltung und Releases](../../../../standards/versionsverwaltung-und-releases.md#die-versionen-des-kernels)).
2. **Notieren Sie die Daten** der jüngsten Releases; die Seite der Roadmap verzeichnet `v6.0.11` bis `v6.0.14` zwischen
   Dezember 2025 und Juni 2026 ([Die Roadmap](../../../projekt/roadmap.md#was-bereits-geliefert-wurde)).
3. **Lesen Sie das Changelog** der aktuellen Linie und überfliegen Sie, was sich geändert hat
   ([Changelog 6.0.15](../../../../../changelogs/6.0/6.0.15.md); [Ein Changelog lesen](../../../presse/ein-changelog-lesen.md)).
4. **Lesen Sie `SECURITY.md`**: unterstützte Versionen, vertrauliche Meldung, erste Antwort innerhalb eines Werktags,
   Korrekturen als nächstes Release.
5. **Lesen Sie die Nachweise zur Härtung**: Die Seite der Sicherheitsstandards verlinkt drei dokumentierte Durchgänge
   der Härtung ([Sicherheitsstandards](../../../../standards/sicherheit.md#status)).
6. **Prüfen Sie die Lücken in den Richtlinien**, die das Projekt selbst nennt: keine signierten Commits oder Tags, kein
   Schutz der Branches, keine Prüfung der Abhängigkeiten in der CI
   ([Versionsverwaltung und Releases](../../../../standards/versionsverwaltung-und-releases.md#bekannte-lücken),
   [Sicherheitsstandards](../../../../standards/sicherheit.md#bekannte-lücken)).
7. **Prüfen Sie die Erweiterungen, die Sie verwenden werden**, auf dieselbe Weise: ihre Tags und
   `doc/changelogs/extensions/<name>.md`.

## Was festzuhalten ist

```text
Project history check - <date>, by <name>
Latest release: <tag>, <date>       Releases in the last 12 months: <n>
Changelog current: yes/no           Supported line per SECURITY.md: <...>
Security reporting channel: <...>   Documented hardening passes: <list>
Known gaps stated by the project: <list>
Extensions checked: <name: latest tag, date>
Conclusion: <...>
```

## Ergebnis

Ein datierter Nachweis, von Ihren eigenen Mitarbeitern aus Primärquellen erstellt, für die Bewertungsakte.

## Fallstricke

- Tags nach Name sortieren und das falsche „neueste“ wählen.
- Die Roadmap als geleistete Arbeit nehmen: Das Changelog ist der Nachweis
  ([Die Roadmap als Behörde lesen](../../beschaffung/lebenszyklus/die-roadmap-lesen.md)).

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Release-Richtlinie und Versionsnummern](../../beschaffung/lebenszyklus/release-richtlinie-und-versionsnummern.md)
- [Nachweise zur Sicherheit bewerten](../../beschaffung/bewertung/sicherheitsnachweise-bewerten.md)
