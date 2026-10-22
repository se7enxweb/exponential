---
title: "Checkliste für das Theme-Design-Review"
audience: theme-designer
category: theme-design
order: 39
summary: Eine Checkliste, um ein Theme-Design zu prüfen, bevor es gebaut wird oder vor einem Release - Konzept, Passung zu den Inhalten, Konsistenz des Systems, Barrierefreiheit, Bereitschaft für mehrere Sprachen und Marken, Erfahrung der Redakteure und die Designregeln des Projekts - für Designer, die ihre eigene Arbeit oder die von Kollegen prüfen.
keywords: Theme-Designer, Design-Review, Checkliste, Kritik, Peer-Review, Review des Designsystems, Konsistenz, Barrierefreiheit, Erfahrung der Redakteure, Designregeln, theme designer, design review, checklist, critique, peer review, design system review, consistency, accessibility, editor experience, design rules
type: help
---
# Checkliste für das Theme-Design-Review

Diese Seite ist für Theme-Designer, die ein Design prüfen: ihr eigenes vor der Übergabe oder das von Kollegen. Sie prüft
das **Design** (Mockups, Tokens, Komponenten und die Entscheidungen dahinter), vor dem Bau oder parallel dazu. Um die
gebauten Seiten zu prüfen, verwenden Sie die [Checkliste zur Design-Qualitätssicherung](checkliste-design-qualitaetssicherung.md); um
Template-Code zu prüfen, die [Checkliste: Code-Review für Templates](../designer/theme-design/checkliste-code-review-fuer-templates.md).

## Inhalt

1. [Wie Sie das Review durchführen](#wie-sie-das-review-durchführen)
2. [Konzept und Umfang](#konzept-und-umfang)
3. [Passung zu den Inhalten](#passung-zu-den-inhalten)
4. [Konsistenz des Systems](#konsistenz-des-systems)
5. [Barrierefreiheit im Design](#barrierefreiheit-im-design)
6. [Sprachen und Marken](#sprachen-und-marken)
7. [Erfahrung der Redakteure](#erfahrung-der-redakteure)
8. [Regeln des Projekts](#regeln-des-projekts)
9. [Ergebnis](#ergebnis)

## Wie Sie das Review durchführen

Prüfen Sie in einer Sitzung von ein bis zwei Stunden, mit den Mockups, der Token-Tabelle, dem Komponenteninventar und
dem Zuordnungsblatt geöffnet. Schreiben Sie die Befunde gleich auf, jeweils mit dem Frame und dem Punkt unten, an dem er
scheitert. Eine zweite prüfende Person, die das Design nicht gemacht hat, findet mehr.

## Konzept und Umfang

- [ ] Das Design erfüllt die Ziele des Briefings, in einem Satz, den Sie sagen können.
- [ ] Jeder Punkt der Bestandsaufnahme ist gestaltet: Rahmen, Klassenansichten, besondere Seiten, Blöcke
      ([Vom Mockup zum Theme](vom-mockup-zum-theme.md#etappe-1-bestandsaufnahme)).
- [ ] Nichts ist gestaltet, hinter dem keine Inhalte stehen.
- [ ] Die Zahl der Layouttypen, Ansichtsmodi und Ansichtstypen von Blöcken ist so klein, wie die Website es zulässt
      ([Ansichtsmodi gestalten](ansichtsmodi-gestalten.md#wie-viele-modi-ein-design-braucht)).

## Passung zu den Inhalten

- [ ] Jedes sichtbare Element ist einem Feld zugeordnet ([Ein Design auf Inhaltsklassen abbilden](ein-design-auf-inhaltsklassen-abbilden.md)).
- [ ] Kürzeste und längste Inhalte für Schlagzeilen, Einleitungen, Namen und Beschriftungen gezeigt.
- [ ] Jedes optionale Feld hat ein Design für leer ([Für Redakteure gestalten](fuer-redakteure-gestalten.md#regel-2-jedes-optionale-feld-hat-ein-design-für-leer)).
- [ ] Alle Elemente des formatierten Texts gestaltet.
- [ ] Listen für null, einen, einige und viele Einträge.
- [ ] Bilder in jeder Form berücksichtigt ([Bildzuschnitte und Art Direction gestalten](bildzuschnitte-und-art-direction-gestalten.md)).

## Konsistenz des Systems

- [ ] Alle Werte kommen aus der Token-Tabelle; keine einmaligen Farben, Größen oder Abstände.
- [ ] Ein Akzent und ein Radius für Komponenten.
- [ ] Jeder Farb-Token hat einen dunklen Wert, und jeder wichtige Frame ist dunkel gezeichnet.
- [ ] Jede Komponente sieht überall gleich aus, wo sie verwendet wird; fast gleiche zusammengeführt
      ([Komponenteninventar](komponenteninventar.md)).
- [ ] Die Namen stimmen zwischen Designtool, Inventar und geplanten Klassennamen überein.

## Barrierefreiheit im Design

- [ ] Kontrastverhältnisse für beide Themes gemessen und notiert ([Farbkontrast](../designer/theme-design/farbkontrast.md)).
- [ ] Fokuszustand für jedes interaktive Element gestaltet ([Interaktive Zustände gestalten](interaktive-zustaende-gestalten.md)).
- [ ] Keine Bedeutung allein durch Farbe (Links, Fehler, aktuelle Seite, Pflichtfelder).
- [ ] Die Überschriftenstruktur ergibt in der Reihenfolge gelesen Sinn ([Landmarks und Überschriften](../designer/theme-design/landmarks-und-ueberschriften.md)).
- [ ] Zielflächen groß genug zum Antippen; Layouts funktionieren bei 200 % Zoom.
- [ ] Bewegung hat eine reduzierte Fassung ([Bewegung und Interaktion](bewegung-und-interaktion.md#reduzierte-bewegung)).

## Sprachen und Marken

- [ ] Längste Sprache in Navigation, Schaltflächen und Überschriften ausprobiert ([Für viele Sprachen gestalten](fuer-viele-sprachen-gestalten.md)).
- [ ] Gespiegeltes Layout für Sprachen, die von rechts nach links laufen, bedacht.
- [ ] Kein Text in Bildern.
- [ ] Bei mehreren Marken: Nur Punkte der Matrix variieren ([Für mehrere Marken im Multisite-Betrieb gestalten](fuer-mehrere-marken-in-multisite-gestalten.md)).

## Erfahrung der Redakteure

- [ ] Redakteure erreichen jedes gestaltete Aussehen, ohne sich besondere Regeln merken zu müssen
      ([Für Redakteure gestalten](fuer-redakteure-gestalten.md#regel-6-kein-design-hängt-davon-ab-dass-redakteure-sich-erinnern)).
- [ ] Ansichtstypen von Blöcken haben klare Namen ([Layoutblöcke entwerfen](layoutbloecke-gestalten.md#ansichtstypen-für-redakteure-benennen)).
- [ ] Anleitung an den Feldern geschrieben, wo Längen oder Bildformen wichtig sind.

## Regeln des Projekts

- [ ] Nichts im Design braucht zum Bauen Inline-Stile oder Inline-Skripte ([CSP-sicheres Design](../designer/theme-design/csp-sicheres-design.md)).
- [ ] Schriften selbst gehostet, Lizenzen geprüft; Einbettungen Dritter mit Platzhaltern aufgeführt
      ([Für Performance-Budgets gestalten](fuer-performance-budgets-gestalten.md#drittanbieter)).
- [ ] Das Design überschreibt mitgelieferte Designs in seiner eigenen Erweiterung, nie durch Bearbeiten.
- [ ] Gestaltung der Administration, falls berührt, verwendet die gemeinsamen Admin-Komponenten und Tokens
      ([admin4-Komponenten](../designer/theme-design/admin4-komponenten.md)).

## Ergebnis

Schließen Sie das Review mit einem von: bereit zur Übergabe, bereit nach den aufgeführten Korrekturen oder braucht eine
weitere Runde. Halten Sie das Ergebnis und die Befunde neben dem Design im Repository des Themes fest.

## Verwandte Seiten

- [Checkliste zur Übergabe](checkliste-zur-uebergabe.md)
- [Checkliste zur Design-Qualitätssicherung](checkliste-design-qualitaetssicherung.md)
- [Eine Prüfung der Barrierefreiheit für jedes Release](../designer/lebenszyklus/barrierefreiheitspruefung-pro-release.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
