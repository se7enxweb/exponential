---
title: Die EU-Richtlinie über barrierefreie Websites und Exponential
audience: government, project-manager, admin
category: public-sector
order: 40
summary: Für öffentliche Stellen in der EU - was die Richtlinie (EU) 2016/2102 über den barrierefreien Zugang zu den Websites und mobilen Anwendungen öffentlicher Stellen allgemein von einer Website verlangt und welchen Teil jeder Pflicht Exponential abdeckt und welchen Teil Ihre Website und Ihre Organisation abdecken.
keywords: Richtlinie über barrierefreie Websites, Web Accessibility Directive, WAD, öffentliche Stellen, Erklärung zur Barrierefreiheit, Feedback-Mechanismus, Überwachung, unverhältnismäßige Belastung, BITV 2.0, EU, Mitgliedstaat, 2016/2102, public sector bodies, EN 301 549, accessibility statement, feedback mechanism, monitoring, disproportionate burden, member state
type: help
---
# Die EU-Richtlinie über barrierefreie Websites und Exponential

Diese Seite richtet sich an öffentliche Stellen in der Europäischen Union, die eine Website mit Exponential
betreiben. Sie beschreibt die Pflichten der Richtlinie über barrierefreie Websites allgemein und für jede, was die
Software laut [Standards zur Barrierefreiheit](../../../standards/barrierefreiheit.md) bietet. Verbindlich sind die Regeln
der Umsetzung in Ihrem Mitgliedstaat.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Richtlinie in Kürze](#die-richtlinie-in-kürze)
2. [Pflicht für Pflicht](#pflicht-für-pflicht)
3. [Ein Arbeitsplan](#ein-arbeitsplan)
4. [Fallstricke](#fallstricke)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Richtlinie in Kürze

Die Richtlinie (EU) 2016/2102 über den barrierefreien Zugang zu den Websites und mobilen Anwendungen öffentlicher
Stellen (amtlicher Text: <https://eur-lex.europa.eu/eli/dir/2016/2102/oj>) verpflichtet die Mitgliedstaaten,
sicherzustellen, dass öffentliche Stellen ihre Websites und Apps wahrnehmbar, bedienbar, verständlich und robust
gestalten. Die technische Referenz ist die harmonisierte Norm EN 301 549 (v3.2.1, herausgegeben von ETSI:
<https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf>), deren Web-Abschnitt
WCAG 2.1 Stufe AA übernimmt. Die Richtlinie verlangt außerdem eine Erklärung zur Barrierefreiheit, einen
Feedback-Mechanismus sowie Überwachung und Berichterstattung durch die Mitgliedstaaten.

## Pflicht für Pflicht

| Pflicht (allgemein) | Was Exponential bietet | Was Sie tun |
|---|---|---|
| Barrierefreie Inhalte der Website (EN 301 549 / WCAG 2.1 AA) | das Bearbeitungswerkzeug (admin4) wird gegen WCAG 2.2 AA getestet, eine Obermenge von 2.1 AA mit Ausnahme von 4.1.1 Syntaxanalyse; Status **Teilweise erfüllt**; EN 301 549 **Nicht geprüft** | ein barrierefreies öffentliches Design bauen oder einkaufen und prüfen; WCAG 2.2 AA ist ein sicheres Ziel, weil es 2.1 AA enthält |
| Barrierefreie, von Beschäftigten veröffentlichte Inhalte | ein Feld für Alternativtext an Bildern, das eine Klasse verpflichtend machen kann; Überschriften, Tabellenbeschriftungen und Kopfzellen im Rich Text | die Redaktion schulen; Alternativtext an Bildfeldern verlangen; Dokumente vor dem Hochladen prüfen |
| Erklärung zur Barrierefreiheit | **keine Vorlage mitgeliefert** (in der Erklärung des Projekts als Lücke genannt) | nach dem Muster schreiben, das Ihr Mitgliedstaat veröffentlicht; siehe „Eine Erklärung zur Barrierefreiheit schreiben“ in diesem Bereich |
| Feedback-Mechanismus | Kontaktformulare lassen sich mit dem Informationssammler bauen ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)) | einen Kontaktlink in die Erklärung aufnehmen; innerhalb der gesetzlichen Frist antworten |
| Unverhältnismäßige Belastung (wo Ihr Recht sie erlaubt) | nichts in der Software | die Bewertung selbst dokumentieren; das ist eine organisatorische Entscheidung |
| Überwachung durch den Mitgliedstaat | nichts in der Software | Prüfberichte und Korrekturlisten für die Überwachungsstelle bereithalten |

## Ein Arbeitsplan

1. **Legen Sie das Ziel fest.** WCAG 2.2 AA für neue Arbeiten; es deckt ab, was Abschnitt 9 von EN 301 549 verlangt.
2. **Wählen Sie das Administrationsdesign.** Lassen Sie die Redaktion bei admin4 (oder admin4l), dem Design, das das
   Projekt testet ([Das Design admin4](../../../funktionen/6.0/admin4-design.md)).
3. **Prüfen Sie das öffentliche Design** mit einer repräsentativen Stichprobe, wie WCAG-EM es beschreibt
   (<https://www.w3.org/TR/WCAG-EM/>). Halten Sie Befunde mit dem Erfolgskriterium und dem zu korrigierenden Template
   fest.
4. **Beheben Sie zuerst Probleme in Templates**: Eine Korrektur an einem Template repariert jede Seite, die es
   verwendet.
5. **Machen Sie Alternativtext verpflichtend** an den Bildattributen Ihrer Inhaltsklassen (Klasse bearbeiten, Option
   „Alternativtext ist erforderlich“).
6. **Veröffentlichen Sie die Erklärung** als normale Inhaltsseite, verlinkt aus der Fußzeile jeder Seite.
7. **Wiederholen Sie** die Prüfung nach Neugestaltungen und in dem Abstand, den Ihre Überwachungsstelle erwartet.

## Fallstricke

- **Die Website für konform erklären, weil das CMS „barrierefrei“ ist.** Die eigene Erklärung des Projekts bewertet die
  Administration mit „Teilweise erfüllt“ und die öffentlichen Designs als „nicht geprüft“. Ihre Erklärung betrifft Ihre
  Website.
- **Dokumente vergessen.** Von Seiten aus verlinkte PDF- und Office-Dateien sind ebenfalls Inhalte; siehe „Dokumente
  und PDFs“ in diesem Bereich.
- **Die Redaktion ohne Anleitung lassen.** Die meisten Rückschritte nach dem Start kommen aus den Inhalten, nicht aus
  den Templates.
- **Ausgabe des Online-Editors.** Die Integrationen von TinyMCE wurden nicht geprüft (Status **Nicht geprüft**); prüfen Sie
  in Ihrer Stichprobe das HTML, das der Rich Text Ihrer Redaktion erzeugt.

## Verwandte Seiten

- [Standards zur Barrierefreiheit: EN 301 549](../../../standards/barrierefreiheit.md#en-301-549)
- [Die Barrierefreiheit Ihrer Website testen](../../ersteller/website-ersteller/barrierefreiheit-testen.md)
- [Barrierefreiheit für Website-Ersteller](../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)
- W3C WAI, [Eine Erklärung zur Barrierefreiheit entwickeln](https://www.w3.org/WAI/planning/statements/)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
