---
title: Was Exponential für die Barrierefreiheit nicht abdeckt
audience: government, project-manager, admin, developer
category: public-sector
order: 100
summary: Die bekannten Lücken bei der Barrierefreiheit, die das Projekt selbst nennt - öffentliche Designs nicht geprüft, klassische Administration und admin3 teilweise korrigiert, Online-Editor nicht verifiziert, Erweiterungen ohne Gesamterklärung, keine automatische Engine in der CI, kein Konformitätsbericht, Inhalte der Redaktion überlassen - und wie eine öffentliche Stelle um jede herum plant.
keywords: Lücken bei der Barrierefreiheit, bekannte Lücken, nicht geprüft, öffentliche Designs, klassische Administration, admin3, Online-Editor, Erweiterungen, Konformitätsbericht, Risikoregister, Plan, accessibility gaps, known gaps, not audited, public designs, classic admin, online editor, TinyMCE, extensions, axe, CI, VPAT, ACR, conformance report, risk register, plan
type: help
---
# Was Exponential für die Barrierefreiheit nicht abdeckt

Diese Seite richtet sich an Projektverantwortliche und die Koordination Barrierefreiheit, die ein Risikoregister oder
einen Plan zur Barrierefreiheit führen. Sie listet die Lücken auf, die die eigene Erklärung des Projekts nennt
([Standards zur Barrierefreiheit: bekannte Lücken](../../../../eng-us/standards/accessibility.md#known-gaps)), und für jede, was
eine öffentliche Stelle dagegen tun kann. Zu sagen, wo die Grenzen liegen, gehört zu einer ehrlichen Erklärung zur
Barrierefreiheit.

## Inhalt

1. [Die Lücken auf einen Blick](#die-lücken-auf-einen-blick)
2. [Um jede Lücke herum planen](#um-jede-lücke-herum-planen)
3. [Ein Eintrag im Risikoregister als Beispiel](#ein-eintrag-im-risikoregister-als-beispiel)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Lücken auf einen Blick

| Lücke (aus der Erklärung des Projekts) | Wirkung auf eine öffentliche Stelle |
|---|---|
| Designs öffentlicher Websites (`design/standard`, `design/base`, `design/plain`, `design/mysite`, die paketierten Website-Designs) sind nicht geprüft | für die Seiten, die die Öffentlichkeit sieht, gibt es keine Nachweise des Projekts; Sie bewerten sie |
| Klassische Administration (`design/admin`) und admin3 sind nicht von den Tests für Kontrast, Landmarks und Formularfehler abgedeckt | Beschäftigte, die sie nutzen, arbeiten in einer Umgebung mit weniger Nachweisen |
| Der Online-Editor (TinyMCE 8 und TinyMCE 3 in `extension/ezoe`) wurde nicht geprüft; Status **Unverified** | das Bearbeiten von Rich Text und seine Ausgabe brauchen Ihre eigene Prüfung |
| Als eigene Pakete ausgelieferte Erweiterungen (Layout-Editor, Newsletter, Shop) werden einzeln überprüft; keine Gesamterklärung | jede Erweiterung, die Sie aktivieren, braucht einen Blick |
| Keine automatische Engine für Barrierefreiheit in der CI; die Tests lesen Quellen und stellen keine Seiten dar; axe läuft manuell | Rückschritte zwischen Releases fallen nur durch die Quelltests und manuelle Reviews auf |
| Kein veröffentlichter Konformitätsbericht (Bewertung nach WCAG-EM, VPAT oder ACR) | Sie können keinen in einer Ausschreibung oder Erklärung zitieren |
| Alternativtext, Reihenfolge der Überschriften und Linktext hängen von der Redaktion ab; standardmäßig zwingt nichts die Redaktion, das Feld für den Alternativtext auszufüllen | die Qualität der Inhalte ist eine organisatorische Aufgabe |
| Keine Vorlage für eine Erklärung zur Barrierefreiheit von Websites | Sie schreiben Ihre eigene |
| EN 301 549 Abschnitt 11.8 (Unterstützung durch Autorenwerkzeuge) nicht bewertet | Sie bewerten die Unterstützung beim Erstellen selbst, wenn Sie sie brauchen |

## Um jede Lücke herum planen

**Öffentliche Designs.** Planen Sie eine Prüfung Ihres Designs vor dem Start und nach jeder Neugestaltung ein. Bauen Sie
bevorzugt auf einem Design auf, dessen Templates Sie steuern, und geben Sie Ihrem Design die Checkliste aus
[Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md).

**Klassische Administration und admin3.** Setzen Sie `SiteDesign=admin4` im Administrations-Siteaccess und beschränken
Sie den Designwechsel, wo möglich, auf admin4 und admin4l
([Der Designwechsel der Administration](../../../anleitungen/ui-umschalter-design.md)).

**Online-Editor.** Nehmen Sie den Editor in Ihre Bewertung auf: Fügen Sie nur mit der Tastatur eine Überschrift, eine
Liste, eine Tabelle mit Kopfzeile und Beschriftung, einen Link und ein Bild mit Alternativtext ein; untersuchen Sie dann
das veröffentlichte HTML. TinyMCE 3 ist die Standard-Engine; TinyMCE 8 ist je Benutzer oder je Siteaccess wählbar
([Der Online-Editor auf TinyMCE 8](../../../../eng-us/features/6.0/online-editor-tinymce8.md)). Bewerten Sie die Engine, die Sie
einsetzen.

**Erweiterungen.** Führen Sie eine Liste der aktivierten Erweiterungen (Setup > Extensions) und testen Sie die
Bildschirme, die jede hinzufügt. Suchen Sie die Einträge der Erweiterung im
[Changelog von 6.0.15](../../../../changelogs/6.0/6.0.15.md), bevor Sie sich auf sie verlassen.

**Keine Engine in der CI.** Nehmen Sie eine automatische Prüfung Ihrer eigenen öffentlichen Seiten in Ihre
Release-Routine auf (ein Werkzeug auf Basis von axe auf einer festen Liste von URLs) und einen Tastaturdurchgang für
jedes Release; siehe „Mit axe und der Tastatur testen“ in diesem Bereich.

**Kein Konformitätsbericht.** Beauftragen oder schreiben Sie Ihren eigenen, abgegrenzt auf Ihre Konfiguration; siehe
„Einen Accessibility Conformance Report für Ihre Website vorbereiten“ in diesem Bereich.

**Inhalte.** Machen Sie Alternativtext an Bildattributen verpflichtend (Klasse bearbeiten, „Alternativtext ist
erforderlich“), schulen Sie die Redaktion und prüfen Sie die Inhalte jeden Monat stichprobenartig.

## Ein Eintrag im Risikoregister als Beispiel

| Feld | Beispieleintrag |
|---|---|
| Risiko | Öffentliche Seiten, die auf unserem eigenen Design aufbauen, erfüllen WCAG 2.2 AA möglicherweise nicht |
| Quelle | Standards zur Barrierefreiheit, bekannte Lücken: öffentliche Designs nicht geprüft |
| Wahrscheinlichkeit / Auswirkung | von Ihrer Organisation zu bewerten |
| Maßnahmen | Prüfung vor dem Start (Stichprobe nach WCAG-EM), Korrekturen in Templates, erneute Prüfung nach Neugestaltungen, Schulung der Redaktion, monatliche Stichproben der Inhalte |
| Verantwortlich | `<accessibility coordinator>` |
| Aufbewahrte Nachweise | Prüfbericht, Korrekturliste mit Daten, Verlauf der Erklärung |

## Verwandte Seiten

- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- [Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)
- [Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
