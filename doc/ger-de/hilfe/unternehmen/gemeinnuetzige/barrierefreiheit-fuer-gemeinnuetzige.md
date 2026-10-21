---
title: Barrierefreiheit für gemeinnützige Organisationen
audience: businesses, editor, website-creator
category: business-solutions
order: 50
summary: Warum Barrierefreiheit für den Auftrag und die Geldgeber einer gemeinnützigen Organisation wichtig ist, was der Template-Autor und die Redakteure auf einer Exponential-Website jeweils steuern, welche Funktionen helfen, barrierefreie Formulare und Kampagnenseiten, Tests mit kleinem Budget und eine Erklärung zur Barrierefreiheit.
keywords: Barrierefreiheit, barrierefreie Website, Screenreader, Tastatur, Kontrast, Alternativtext, barrierefreie Formulare, Erklärung zur Barrierefreiheit, gemeinnützige Organisation, Inklusion, accessibility, accessible website, WCAG, screen reader, keyboard, contrast, alternative text, accessible forms, accessibility statement, nonprofit, inclusion
type: help
---
# Barrierefreiheit für gemeinnützige Organisationen

Diese Seite richtet sich an Vorstände, Mitarbeiter in der Kommunikation und ehrenamtliche Redakteure gemeinnütziger
Organisationen. Viele gemeinnützige Organisationen arbeiten für Menschen mit Behinderungen, ältere Menschen oder
Menschen mit wenig Zeit oder Bandbreite; eine Website, die sie nicht nutzen können, arbeitet gegen den Auftrag. Diese
Seite erklärt, wer auf einer Exponential-Website wofür verantwortlich ist, welche Funktionen helfen und wie ein kleines
Team testen kann.

> **Hinweis**
> Gesetze zur Barrierefreiheit und Bedingungen von Geldgebern unterscheiden sich nach Land und Programm. Diese Seite ist
> keine Rechtsberatung; klären Sie mit Ihrem Berater oder Geldgeber, welcher Standard für Sie gilt (oft die Web Content
> Accessibility Guidelines).

## Inhalt

1. [Zwei Personen machen eine Website barrierefrei](#zwei-personen-machen-eine-website-barrierefrei)
2. [Was der Template-Autor steuert](#was-der-template-autor-steuert)
3. [Was Redakteure steuern](#was-redakteure-steuern)
4. [Funktionen, die helfen](#funktionen-die-helfen)
5. [Barrierefreie Formulare](#barrierefreie-formulare)
6. [Kampagnenseiten und Downloads](#kampagnenseiten-und-downloads)
7. [Testen mit kleinem Budget](#testen-mit-kleinem-budget)
8. [Eine Erklärung zur Barrierefreiheit](#eine-erklärung-zur-barrierefreiheit)
9. [Checkliste Barrierefreiheit](#checkliste-barrierefreiheit)

## Zwei Personen machen eine Website barrierefrei

Der Website-Ersteller baut die Templates einmal; Redakteure fügen jede Woche Inhalte hinzu. Beides zählt, und ein
barrierefreies Design wird durch nicht barrierefreie Inhalte zunichtegemacht. Der Leitfaden für Website-Ersteller teilt
die Arbeit auf dieselbe Weise auf
([Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)).

## Was der Template-Autor steuert

Aus [Barrierefreiheit für Website-Ersteller, Was der Template-Autor steuert](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md#what-the-template-author-controls):

| Bereich | Verlangen Sie von Ihrem Website-Ersteller |
|---|---|
| Sprache | Die Sprache der Seite gesetzt, und `dir="rtl"` für Inhalte von rechts nach links |
| Landmarks | Kopfbereich, Navigation, Hauptinhalt und Fußzeile ausgezeichnet |
| Sprunglink | „Zum Inhalt springen“ als Erstes auf der Seite |
| Überschriften | Ein `h1` pro Seite, Überschriften in der richtigen Reihenfolge |
| Fokus | Ein sichtbarer Fokusstil an jedem Link und jedem Bedienelement |
| Kontrast | Text mindestens 4,5:1 gegenüber seinem Hintergrund, großer Text 3:1 |
| Bewegung | Slider und Animationen respektieren reduzierte Bewegung |
| Zoom | Layouts, die bei 200 % Zoom und auf schmalen Bildschirmen funktionieren, ohne horizontales Scrollen |

Nehmen Sie diese Punkte in Ihren Vertrag oder Ihr Briefing mit dem Partner auf, der die Website baut
([Checkliste: Anforderungen](../vorlagen/checkliste-anforderungen.md)).

## Was Redakteure steuern

- **Alternativtexte** für Bilder, die eine Bedeutung tragen; dekorative Bilder bekommen einen leeren.
- **Linktexte**, die sagen, wohin sie führen („Den Jahresbericht lesen“, nicht „hier klicken“).
- **Überschriften** im Rich Text, als Überschriften verwendet und in der richtigen Reihenfolge, nicht um Text größer zu
  machen.
- **Tabellen** mit Kopfzeilen, nur für Daten.
- **Einfache Sprache**, kurze Absätze.

Quelle: [Was Redakteure steuern](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md#what-editors-control).
Geben Sie Ehrenamtlichen diese Liste auf einer Seite, wenn sie anfangen; sie gehört zur Übergabe
([Die Website an Redakteure übergeben](../../../../eng-us/help/creators/website-creators/handing-over-to-editors.md)).

## Funktionen, die helfen

- Bildfelder können einen Alternativtext verlangen, sodass ein Bild nicht ohne einen veröffentlicht werden kann.
- Der Online-Editor schreibt strukturierten Rich Text (Überschriften, Listen, Tabellen) statt freiem HTML.
- Inhalte von rechts nach links werden mit `lang` und `dir` ausgezeichnet.
- Die Administration selbst folgt einem barrierefreien Design, „damit auch Redakteure mit Behinderungen arbeiten
  können“ ([admin4-Design](../../../../eng-us/features/6.0/admin4-design.md)).
- Inhaltsprüfungen finden Probleme in Inhalten ([Inhaltsprüfungen](../../../../eng-us/features/6.0/content-checks.md)).

Quelle: [Exponential-Funktionen, die helfen](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md#exponential-features-that-help).
Ehrenamtliche mit Behinderungen können die Website daher ebenso bearbeiten wie lesen, was den Kreis derer erweitert,
die Ihnen helfen können.

## Barrierefreie Formulare

Spenden-, Ehrenamts- und Kontaktformulare sind die Stellen, an denen eine nicht barrierefreie Website eine gemeinnützige
Organisation am unmittelbarsten etwas kostet:

- [ ] Jedes Eingabefeld hat eine sichtbare Beschriftung.
- [ ] Pflichtfelder sind im Text gekennzeichnet.
- [ ] Fehler erscheinen neben dem Feld und werden oben zusammengefasst, und die Zusammenfassung erhält den Fokus.
- [ ] CAPTCHAs haben eine barrierefreie Alternative; „beide mitgelieferten CAPTCHA-Dienste bieten eine“.

Quelle: [Barrierefreiheit für Website-Ersteller, Formulare](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md#forms).
Zu den Formularen selbst siehe [Ehrenamtliche und Anmeldeformulare](../../../../eng-us/help/businesses/nonprofits/volunteers-and-sign-up-forms.md).

## Kampagnenseiten und Downloads

- Hero-Blöcke mit Text über Bildern brauchen in jedem Zustand genug Kontrast; prüfen Sie sie wie jeden anderen Text.
- Ein `h1` pro Kampagnenseite; Blöcke sollten nicht jeweils ihre eigene oberste Überschrift mitbringen.
- Schaltflächen sagen, was sie tun („Für den Winteraufruf spenden“).
- Downloads zeigen Format und Größe vor dem Klick, „eine Anforderung der Barrierefreiheit für viele Websites“
  ([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#download-links-in-templates)).
- Jahresberichte als PDF: Veröffentlichen Sie die wichtigsten Inhalte auch als Webseite, die sich mit assistiven
  Technologien leichter lesen lässt.

## Testen mit kleinem Budget

Die Routine in [Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)
braucht für den Anfang keine kostenpflichtigen Werkzeuge:

1. Wählen Sie die Seiten, auf die es ankommt: Startseite, Spenden, Formular für Ehrenamtliche, eine Kampagnenseite, ein
   Nachrichtenartikel.
2. Lassen Sie auf jeder einen automatischen Prüfer laufen.
3. Bedienen Sie jede Seite nur mit der Tastatur: jedes Menü, jedes Formular und jeden Dialog, wobei der Fokus immer
   sichtbar ist.
4. Zoomen Sie auf 200 % und verkleinern Sie das Fenster auf Telefonbreite.
5. Hören Sie sich die Seiten zum Spenden und für Ehrenamtliche mit einem Screenreader an.
6. Schreiben Sie auf, was Sie gefunden haben, und beheben Sie es; testen Sie nach jeder Änderung der Templates erneut.

Wenn Sie Menschen kennen, die assistive Technologien nutzen, bitten Sie sie, die Seiten zum Spenden und für
Ehrenamtliche auszuprobieren; das findet, was Werkzeuge übersehen.

## Eine Erklärung zur Barrierefreiheit

Viele Organisationen veröffentlichen eine kurze Erklärung: den Standard, den Sie anstreben, was nach Ihrem Wissen noch
nicht funktioniert und wie man ein Problem meldet. Nennen Sie darin das Kontaktformular oder die Adresse und beantworten
Sie Meldungen. Ob eine Erklärung vorgeschrieben ist und in welcher Form, hängt von Ihrem Recht ab; Exponential erzeugt
keine.

## Checkliste Barrierefreiheit

- [ ] Die Anforderungen an die Templates stehen im Briefing oder Vertrag.
- [ ] Bildfelder verlangen Alternativtexte.
- [ ] Redakteure haben die einseitige Liste dessen, was sie steuern.
- [ ] Spenden-, Ehrenamts- und Kontaktformulare sind beschriftet, mit angesagten Fehlern und einem barrierefreien
      CAPTCHA.
- [ ] Kampagnenseiten sind auf Kontrast und Reihenfolge der Überschriften geprüft.
- [ ] Die wichtigsten Seiten sind mit der Tastatur, bei 200 % Zoom und mit einem Screenreader getestet.
- [ ] Eine Erklärung zur Barrierefreiheit mit einer Möglichkeit, Probleme zu melden.
- [ ] Erneuter Test nach jeder Änderung des Designs.

## Verwandte Seiten

- [Gemeinnützige Organisationen](README.md)
- [Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
- [Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)
- [Checklisten zu Barrierefreiheit, Sicherheit und Compliance](../../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
