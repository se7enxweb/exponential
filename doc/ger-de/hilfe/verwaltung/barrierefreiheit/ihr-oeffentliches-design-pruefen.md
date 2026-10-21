---
title: Ihr öffentliches Design mit WCAG-EM prüfen
audience: government, project-manager, designer, developer
category: public-sector
order: 120
summary: Für öffentliche Stellen, die ihre eigene Website bewerten müssen - wie Sie eine mit Exponential gebaute Website nach der Methodik WCAG-EM des W3C bewerten, eine Stichprobe aus den Templates, Klassen und Siteaccesses von Exponential wählen und das Ergebnis festhalten.
keywords: WCAG-EM, Bewertungsmethodik, Prüfung der Barrierefreiheit, Stichprobe, Templates, Inhaltsklassen, Siteaccess, öffentliches Design, Konformitätsziel, Befunde, Bericht, erneute Prüfung, evaluation methodology, accessibility audit, sample, templates, content classes, siteaccess, public design, conformance target, findings, report, re-audit
type: help
---
# Ihr öffentliches Design mit WCAG-EM prüfen

Diese Seite richtet sich an die Koordination Barrierefreiheit, an Designerinnen und Designer und an Prüferinnen und
Prüfer, die eine mit Exponential gebaute öffentliche Website bewerten. Das Projekt hat die öffentlichen Designs
**nicht** geprüft; diese Bewertung ist Ihre Aufgabe
([Standards zur Barrierefreiheit: bekannte Lücken](../../../../eng-us/standards/accessibility.md#known-gaps)). Das folgende
Vorgehen folgt der Website Accessibility Conformance Evaluation Methodology des W3C, WCAG-EM
(<https://www.w3.org/TR/WCAG-EM/>), und überträgt ihre Schritte auf die Art, wie Exponential Seiten aufbaut.

## Inhalt

1. [Warum die Struktur von Exponential hilft](#warum-die-struktur-von-exponential-hilft)
2. [Schritt 1: Den Umfang festlegen](#schritt-1-den-umfang-festlegen)
3. [Schritt 2: Die Website erkunden](#schritt-2-die-website-erkunden)
4. [Schritt 3: Eine Stichprobe wählen](#schritt-3-eine-stichprobe-wählen)
5. [Schritt 4: Bewerten](#schritt-4-bewerten)
6. [Schritt 5: Berichten](#schritt-5-berichten)
7. [Verwandte Seiten](#verwandte-seiten)

## Warum die Struktur von Exponential hilft

Jede Seite einer Exponential-Website wird von einer kleinen Zahl von Templates gezeichnet: dem Pagelayout, einer
Vollansicht je Inhaltsklasse, Zeilen- und Einbettungsansichten und den Templates der Datentypen und Rich-Text-Tags
([Designs und die Fallback-Kette](../../../../eng-us/help/creators/website-creators/designs-and-the-fallback-chain.md),
[Template-Overrides](../../../../eng-us/help/creators/website-creators/template-overrides.md)). Ein Befund in einem Template betrifft
meist jede Seite, die es verwendet, und eine Korrektur dort behebt ihn überall. Wählen Sie die Stichprobe nach
Templates und ergänzen Sie dann Inhalte, die die Redaktion erstellt hat.

## Schritt 1: Den Umfang festlegen

- **Ziel**: WCAG 2.2 Konformitätsstufe AA (enthält 2.1 AA, das Abschnitt 9 von EN 301 549 übernimmt).
- **Siteaccesses**: Listen Sie jeden öffentlichen Siteaccess auf (jede Sprache oder Website ist einer); nehmen Sie
  solche von rechts nach links auf.
- **Eingeschlossen**: Formulare, Suche, Anmeldung und Registrierung, Shop, Newsletter-Abonnement, von Seiten aus
  verlinkte Dokumente.
- **Ausdrücklich ausgeschlossen**: Inhalte Dritter, die Sie nicht steuern (zum Beispiel ein eingebetteter
  Kartendienst), im Bericht genannt.
- **Grundlage der Barrierefreiheitsunterstützung**: die Browser und assistiven Technologien, mit denen Sie testen.

## Schritt 2: Die Website erkunden

Sammeln Sie die Bausteine:

| Ansehen | Wo |
|---|---|
| Verwendete Inhaltsklassen | Setup > Klassen (`/class/grouplist`) |
| Templates und Overrides | die `override.ini` und die Ordner `templates/` des Designs; Setup > Extensions für aktivierte Designs |
| Layouts und Blöcke (falls der Layout-Editor verwendet wird) | [Landingpages mit Layouts](../../../../eng-us/help/creators/website-creators/landing-pages-with-layouts.md) |
| Formulare | Objekte von Klassen mit Informationssammler-Attributen, Setup > Gesammelte Informationen |
| Allgemeine Funktionen | Suche, Anmeldung, Fehlerseiten (nicht gefunden, Zugriff verweigert) |

## Schritt 3: Eine Stichprobe wählen

Eine praktische Stichprobe für eine Exponential-Website:

1. die Startseite und jedes Layout einer Landingpage;
2. eine Vollansicht jeder Inhaltsklasse, die Besucher sehen;
3. eine Ordnerliste mit Blätterfunktion;
4. Suchergebnisse mit und ohne Treffer;
5. jede Art von Formular;
6. die Fehlerseiten;
7. eine Seite je Sprach-Siteaccess;
8. eine zufällige Auswahl von etwa zehn von der Redaktion geschriebenen Inhaltsseiten, für die Qualität der Inhalte.

Das entspricht der Liste unter
[Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md#choose-the-pages).

## Schritt 4: Bewerten

Für jede Seite der Stichprobe:

- eine automatische Prüfung (zum Beispiel mit einem Werkzeug auf Basis von axe), festgehalten mit der Version des
  Werkzeugs;
- ein Durchgang mit der Tastatur und ein Durchgang mit einem Screenreader;
- Zoom auf 200 % und eine Breite von 320 px;
- eine manuelle Prüfung jedes Kriteriums von WCAG 2.2 AA, das Werkzeuge nicht entscheiden können (bedeutungstragende
  Reihenfolge, Zweck von Links, Überschriften, Korrekturvorschläge bei Fehlern).

Halten Sie jeden Befund fest mit: Seite, Kriterium, was geschieht, Schweregrad und **woher er stammt**: aus einem
Template (nennen Sie es), aus Inhalten (nennen Sie das Objekt) oder aus einer Erweiterung.

## Schritt 5: Berichten

| Teil | Inhalt |
|---|---|
| Umfang und Ziel | wie in Schritt 1 |
| Stichprobe | die URLs und warum jede gewählt wurde |
| Ergebnisse je Kriterium | erfüllt, nicht erfüllt, nicht vorhanden |
| Befunde | mit Quelle (Template, Inhalt, Erweiterung) |
| Schlussfolgerung | in Ihren eigenen Worten; vermeiden Sie „konform“, sofern nicht jede Seite der Stichprobe jedes Kriterium erfüllt |
| Datum und Prüfende | wer wann mit welchen Werkzeugen geprüft hat |

Übernehmen Sie die Befunde in die Erklärung zur Barrierefreiheit (bekannte Lücken und Plan) und in Ihre Liste der
Korrekturen. Prüfen Sie nach einer Neugestaltung, einem Upgrade, das Templates ändert, oder einer neuen Inhaltsklasse
erneut.

## Verwandte Seiten

- [Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)
- [Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- W3C, [WCAG-EM](https://www.w3.org/TR/WCAG-EM/)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
