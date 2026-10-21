---
title: Checkliste Barrierefreiheit
audience: government, admin, editor, project-manager
category: public-sector
order: 30
summary: Eine Checkliste zur Barrierefreiheit für eine öffentliche Stelle, die Exponential betreibt - die Administration, das öffentliche Design, die Inhalte der Redaktion, Tests und die Erklärung zur Barrierefreiheit - mit dem, was Exponential mitbringt, und dem, was in Ihrer Verantwortung bleibt.
keywords: Barrierefreiheit, Checkliste, WCAG 2.2, AA, EN 301 549, Section 508, BITV, Erklärung zur Barrierefreiheit, Alternativtext, Tastatur, Kontrast, admin4, öffentliches Design, Tests, öffentlicher Sektor, accessibility, checklist, accessibility statement, alternative text, keyboard, contrast, public design, testing, public sector
type: help
---
# Checkliste Barrierefreiheit

Diese Checkliste richtet sich an die Verantwortlichen für Barrierefreiheit, die Website-Administration und die leitende
Redaktion einer öffentlichen Stelle. Sie trennt, was Exponential mitbringt, von dem, was Ihre Organisation tun muss. Die
Statusangaben stammen aus den [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md): WCAG 2.2 AA
**Teilweise erfüllt** für die Administration admin4 und admin4l; EN 301 549 und Section 508
**Nicht geprüft**; öffentliche Designs nicht geprüft; kein VPAT.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung. Amtliche Texte: [WCAG 2.2](https://www.w3.org/TR/WCAG22/),
> [EN 301 549](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf),
> [Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj), [Section 508](https://www.section508.gov/).

## Inhalt

1. [Die Administration](#die-administration)
2. [Das öffentliche Design](#das-öffentliche-design)
3. [Inhalte](#inhalte)
4. [Tests](#tests)
5. [Erklärung und Rückmeldungen](#erklärung-und-rückmeldungen)

## Die Administration

- [ ] Mitarbeitende, die es brauchen, verwenden das Design **admin4**, das gegen die Kriterien von WCAG 2.2 AA gebaut und getestet ist ([Das Design admin4](../../../../eng-us/features/6.0/admin4-design.md)).
- [ ] Mitarbeitende kennen die [Tastenkürzel](../../tastenkuerzel.md) und den hellen, dunklen und systemabhängigen Modus.
- [ ] Sie kennen die Lücken: Die klassische Administration, der Online-Editor (TinyMCE) und einige Erweiterungen werden nicht von denselben Tests abgedeckt ([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#known-gaps)).
- [ ] Die Oberflächensprache ist bei Bedarf pro Benutzer eingestellt ([Einstellungen zum Umschalten der Oberfläche](../../../anleitungen/ui-umschalter-einstellungen.md)).

## Das öffentliche Design

- [ ] Das Design wurde für WCAG 2.2 AA gebaut oder geprüft; die öffentlichen Designs des Projekts sind nicht geprüft und liegen in der Verantwortung der Websiteersteller.
- [ ] Sprunglink, Landmarks, eine `h1` pro Seite, sichtbarer Fokus, `lang` an `<html>` ([Barrierefreiheit für Websiteersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)).
- [ ] Farbkontrast von Text 4,5:1 und von Bedienelementen 3:1; keine Information allein über Farbe.
- [ ] Das Layout bricht bei 320 CSS-Pixeln und bei 200 % Zoom sauber um.
- [ ] Formulare haben Beschriftungen, Fehlermeldungen und Anleitungen ([Ein Kontaktformular bauen](../../../../eng-us/help/creators/website-creators/building-a-contact-form.md)).
- [ ] Sprachen mit Schreibrichtung von rechts nach links setzen die Richtung ([Websites von rechts nach links](../../../../eng-us/help/creators/website-creators/right-to-left-sites.md)).
- [ ] Ein CAPTCHA, falls vorhanden, hat eine barrierefreie Alternative ([Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)).

## Inhalte

- [ ] Die Redaktion ist in barrierefreien Inhalten geschult ([Redaktion 201, Modul 8](../../../../eng-us/help/trainers/editors-201/module-08-quality-and-accessibility/README.md)).
- [ ] Jedes informative Bild hat einen Alternativtext; nichts in der Software zwingt die Redaktion, ihn auszufüllen ([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#known-gaps)).
- [ ] Überschriften sind in der richtigen Reihenfolge; Tabellen haben Kopfzeilen; Linktexte beschreiben das Ziel.
- [ ] Dokumente (PDF, Office-Dateien) sind barrierefrei oder haben eine barrierefreie Alternative.
- [ ] Videos haben Untertitel; Audio hat Transkripte.
- [ ] Einfache Sprache ([Checkliste einfache Sprache](checkliste-einfache-sprache.md)).

## Tests

- [ ] Testen Sie die wichtigsten Abläufe allein mit der Tastatur und mit einem Screenreader ([Barrierefreiheit testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)).
- [ ] Lassen Sie eine automatische Prüfung über die Haupttemplates laufen; sie findet nur einen Teil der Probleme.
- [ ] Folgen Sie für eine formale Bewertung einer Methode wie [WCAG-EM](https://www.w3.org/TR/WCAG-EM/).
- [ ] Testen Sie nach Designänderungen und nach Aktualisierungen erneut.
- [ ] Wenn ein Prüfbericht verlangt wird, beauftragen Sie einen für Ihre Website; das Projekt veröffentlicht keinen.

## Erklärung und Rückmeldungen

- [ ] Erklärung zur Barrierefreiheit veröffentlicht: Stand der Konformität, bekannte Probleme, Kontakt für Rückmeldungen, Datum ([Leitfaden des W3C](https://www.w3.org/WAI/planning/statements/)).
- [ ] Ein Weg für Rückmeldungen, der ohne die nicht barrierefreien Inhalte funktioniert (E-Mail, Telefon).
- [ ] Rückmeldungen werden innerhalb Ihrer eigenen Frist beantwortet, und Korrekturen werden verfolgt.
- [ ] Die Erklärung wird mindestens einmal im Jahr überprüft ([Jährliche Überprüfung](jaehrliche-ueberpruefung.md)).

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- Der Bereich Barrierefreiheit dieser Hilfe für Verwaltungen (von einem anderen Autor geschrieben)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
