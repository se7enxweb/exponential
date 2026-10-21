---
title: Eine maschinell unterstützte Übersetzung gegenlesen
audience: advocate
category: advocacy
order: 101
summary: Wie eine Muttersprachlerin oder ein Muttersprachler eine der maschinell unterstützten Übersetzungen der Administration von Exponential gegenliest - die Sprache einrichten, die wichtigsten Bildschirme durchgehen, falsche, holprige und uneinheitliche Texte notieren, sie in der Übersetzungsdatei korrigieren, ein Glossar führen - mit einem Prüfbogen.
keywords: Übersetzung gegenlesen, maschinell unterstützt, maschinelle Übersetzung, Muttersprachler, Spanisch, Französisch, Ukrainisch, Hebräisch, Latein, Klingonisch, Glossar, Einheitlichkeit, Qualität, translation review, machine-assisted, machine translation, native speaker, glossary, consistency, quality
type: help
---
# Eine maschinell unterstützte Übersetzung gegenlesen

Diese Seite ist für Muttersprachler einer Sprache, deren Übersetzung der Administration vollständig, aber maschinell
unterstützt ist. Laut [Sprachen in Exponential](../../sprachen.md#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)
ist das Gegenlesen der maschinell unterstützten Übersetzungen ins Spanische, Französische, Ukrainische, Hebräische,
Lateinische und Klingonische „der schnellste Gewinn“. Sie müssen nicht entwickeln können.

## Inhalt

1. [Einrichten](#einrichten)
2. [Die Bildschirme durchgehen](#die-bildschirme-durchgehen)
3. [Worauf Sie achten](#worauf-sie-achten)
4. [Ein Glossar führen](#ein-glossar-führen)
5. [Korrigieren und einreichen](#korrigieren-und-einreichen)
6. [Der Prüfbogen](#der-prüfbogen)

## Einrichten

- Eine Installation, die Sie kontrollieren ([Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)).
- Stellen Sie Ihre eigene Oberflächensprache im Feld **Oberfläche** der rechten Spalte um ([Sprachen](../sprachen.md)).
- Wenn sich Texte nicht ändern, leert ein Administrator den Übersetzungscache
  (`php bin/php/ezcache.php --clear-tag=i18n --allow-root-user`, laut Abschnitt 8).

## Die Bildschirme durchgehen

Prüfen Sie in der Reihenfolge, in der Redakteure ihnen begegnen:

1. Anmeldeseite und Dashboard.
2. Inhaltsbaum, die Vollansicht eines Inhalts, das Formular **Bearbeiten**, **Vorschau**, **Zur Veröffentlichung schicken**.
3. **Versionen verwalten**, der Papierkorb.
4. Hochladen von Medien.
5. Der Layouts-Editor.
6. Seiten unter **Setup**, die ein Administrator nutzt.
7. Fehler- und Bestätigungsmeldungen (versuchen Sie, einen Entwurf zu verwerfen).

## Worauf Sie achten

| Problem | Beispiel |
|---|---|
| Falsche Bedeutung | Eine Schaltfläche, die das Gegenteil dessen sagt, was sie tut |
| Zu wörtlich | Wort-für-Wort-Formulierungen, die niemand verwenden würde |
| Uneinheitlich | Derselbe englische Begriff auf drei Arten übersetzt |
| Falsche Anrede | Förmliche und informelle Anrede gemischt |
| Zu lang | Text, der das Layout sprengt |
| Unübersetzt | Übrig gebliebenes Englisch |
| Defekte Platzhalter | `%1` fehlt oder ist so verschoben, dass der Satz zerbricht |
| Probleme bei der Schreibrichtung von rechts nach links | Satzzeichen oder Zahlen an der falschen Stelle (Hebräisch, Arabisch) |

## Ein Glossar führen

Schreiben Sie auf, wie wichtige Begriffe übersetzt werden, und verwenden Sie sie überall: object, content class, draft,
version, publish, siteaccess, section, role, policy, trash, layout, zone, block. Das [Glossar](../../glossar.md) der
Hilfe erklärt jeden englischen Begriff.

## Korrigieren und einreichen

- Korrigieren Sie Texte in `share/translations/<code>/translation.ts` mit Qt Linguist oder einem XML-Editor und behalten
  Sie Platzhalter, Entitäten und Leerzeichen bei ([Bei Übersetzungen helfen](bei-uebersetzungen-helfen.md#regeln-die-übersetzungen-funktionsfähig-halten)).
- Wenn Sie keine Dateien bearbeiten können, eröffnen Sie ein Issue mit Ihrem Prüfbogen; jemand anderes kann es umsetzen
  ([Welcher Tracker für welche Meldung](welcher-tracker-fuer-welche-meldung.md)).
- Reichen Sie Korrekturen Bildschirm für Bildschirm als Pull Requests ein.

## Der Prüfbogen

```text
TRANSLATION REVIEW                       Language: <code>   Version: Exponential <6.0.x>   Reviewer: <you>

| Screen          | English source            | Current translation       | Proposed              | Why        |
|-----------------|---------------------------|---------------------------|-----------------------|------------|
| Edit form       | Send for publishing       | <...>                     | <...>                 | meaning    |
| Dashboard       | My drafts                 | <...>                     | <...>                 | consistency|

Glossary decisions:
  draft = <...>   publish = <...>   content class = <...>
```

## Verwandte Seiten

- [Bei Übersetzungen helfen](bei-uebersetzungen-helfen.md)
- [Einen Beitrags-Sprint vorschlagen](einen-beitrags-sprint-vorschlagen.md)
- [Sprachen in Exponential](../../sprachen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
