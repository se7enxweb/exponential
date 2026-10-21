---
title: "Lektion 5.1: Barrierefreiheit: was Exponential leistet und was Ihr Theme leisten muss"
audience: trainer, designer, developer
category: training
order: 51
summary: Die Teilnehmenden lesen den Stand der Barrierefreiheit von Exponential ehrlich (WCAG 2.2 AA teilweise erfüllt für admin4, öffentliche Designs nicht geprüft), trennen den Anteil des Produkts von dem des Themes und der Redakteure und beantworten die Frage einer Kundin oder eines Kunden, ohne zu viel zu versprechen. 45 Minuten.
keywords: theme-design, barrierefreiheit, WCAG 2.2, AA, WAI-ARIA, EN 301 549, Section 508, VPAT, teilweise erfüllt, nicht geprüft, öffentliche designs nicht geprüft, verantwortung des website-bauers, admin4, erklärung zur barrierefreiheit, theme design, accessibility, partly complies, unverified, accessibility statement
type: help
---
# Lektion 5.1: Barrierefreiheit: was Exponential leistet und was Ihr Theme leisten muss

Früher oder später fragt eine Kundin oder ein Kunde: „Ist Exponential barrierefrei?“ Die ehrliche Antwort hat drei
Teile: was das Produkt mitliefert, was das Theme leistet und was die Redakteure veröffentlichen. Diese Lektion gibt den
Teilnehmenden die Fakten aus der Standards-Seite des Projekts selbst, damit sie präzise antworten können und wissen,
welcher Teil ihre Aufgabe ist.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 5: Barrierefreiheit und Rechts-nach-links](README.md) |
| Dauer | 45 Minuten (25 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer, Entwickler und alle, die Fragen von Kunden zur Barrierefreiheit beantworten |
| Voraussetzungen | [Modul 4](../modul-04-die-administration-gestalten/README.md) ist hilfreich, aber nicht erforderlich |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Stand von Exponential für WCAG 2.2 AA, WAI-ARIA 1.2, EN 301 549 und Section 508 so angeben, wie die
  Standards-Seite es tut;
- die getesteten Teile (admin4, admin4l) und die nicht geprüften Teile (öffentliche Designs) nennen;
- erklären, welche Arbeit für die Barrierefreiheit zum Theme gehört und welche zu den Redakteuren;
- einer Kundin oder einem Kunden eine Antwort schreiben, die weder zu viel verspricht noch die Leistung kleinredet.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md) und
[Barrierefreiheit für Website-Bauer](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md).

1. **Die Übersichtstabelle** (8 Minuten). Zeigen Sie sie am Projektor:

   | Standard | Stand |
   |---|---|
   | WCAG 2.2, Stufe AA | Teilweise erfüllt |
   | WAI-ARIA 1.2 und die APG | Teilweise erfüllt |
   | EN 301 549 v3.2.1 | Nicht geprüft |
   | Section 508 (Revised 508 Standards) | Nicht geprüft |

   „Teilweise erfüllt“ bedeutet: Die Administration in admin4 und admin4l ist nach Kriterien der WCAG 2.2 AA gebaut
   und wird mit Regressionstests dagegen geprüft; das ist keine Konformitätsaussage. Es gibt keine vollständige Prüfung
   jeder Seite, kein Testprotokoll mit assistiven Technologien und keinen veröffentlichten Konformitätsbericht (weder
   VPAT noch Accessibility Conformance Report).
2. **Was getestet wird** (5 Minuten). Beispiele von der Seite: zugängliche Namen für jedes Formularelement in admin4
   (`AdminFormLabelsTest`), ein Sprunglink und Landmarks (`AdminLandmarksTest`), der Kontrast der Tokens in beiden
   Modi (`AdminTokenContrastTest`), Tastaturmuster (`AdminKeyboardTest`), Zielflächen von 24 x 24 px. Die Tests lesen
   die Quellen der Templates und Stylesheets; sie rendern keine Seiten im Browser, und axe-Läufe erfolgen von Hand.
3. **Bekannte Lücken** (5 Minuten). Öffentliche Website-Designs (`design/standard`, `design/base`, `design/plain`, die
   als Pakete gelieferten Themes) sind nicht geprüft: Ihre Barrierefreiheit liegt in der Verantwortung der
   Website-Bauer. Der Online-Editor wurde nicht geprüft. Inhalte hängen von den Redakteuren ab.
4. **Drei Verantwortliche** (5 Minuten). Zeichnen Sie drei Spalten: das Produkt (die Administration, die
   Bearbeitungsformulare), das Theme (Sprache, Landmarks, Sprunglink, Überschriften, Fokus, Kontrast, Zoom,
   Bewegung), die Redakteure (Alternativtexte, Linktexte, Überschriften im Rich Text, Tabellen). In diesem Kurs geht es
   um die mittlere Spalte.

### Häufige Fragen

**Dürfen wir sagen „Exponential ist WCAG-konform“?** Nein. Sagen Sie, was die Standards-Seite sagt: Die Administration
(admin4) ist nach Kriterien der WCAG 2.2 AA gestaltet und getestet und erfüllt sie teilweise; die Barrierefreiheit der
öffentlichen Website hängt von ihrem Theme und ihren Inhalten ab.

**Hat das Projekt ein VPAT?** Nein; die Standards-Seite sagt das ausdrücklich.

**Hilft die Administration Redakteuren mit Behinderungen?** admin4 ist für die Bedienung per Tastatur, Screenreader,
Dunkelmodus, erzwungene Farben und reduzierte Bewegung gebaut; die klassischen Designs haben weniger Korrekturen
erhalten.

### Stolperfallen

- **Zu viel versprechen im Verkaufsgespräch.** Bleiben Sie bei der Formulierung der Standards-Seite; sie ist die
  eigene Aussage des Projekts.
- **Kleinreden.** Die Arbeit an der Administration ist echt und getestet; sagen Sie, was getestet wird.
- **Die Inhalte vergessen.** Ein perfektes Theme mit Bildern ohne Alternativtext ist nicht barrierefrei.

## Praktische Übung

Zuerst einzeln, dann in Paaren. Verwenden Sie [Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md).

1. Lesen Sie die Tabelle „Zusammenfassung“ und schreiben Sie die vier Einstufungen auf eine Karte.
2. Wählen Sie im Abschnitt zu WCAG drei Zeilen der Tabelle „Wie Exponential die Kriterien erfüllt“ und notieren Sie zu
   jeder den Test, der sie absichert.
3. Lesen Sie „Bekannte Lücken“ und listen Sie die Lücken auf, die ein öffentliches Theme betreffen.
4. Öffnen Sie [Barrierefreiheit für Website-Bauer](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
   und übertragen Sie die Tabelle „Was die Template-Autorin oder der Template-Autor bestimmt“ auf Ihr Blatt mit drei
   Spalten (Produkt, Theme, Redakteure).
5. Ergänzen Sie die Punkte aus „Was Redakteure bestimmen“ in der Spalte der Redakteure.
6. Eine Kundin schreibt: „Unsere Behörde muss die Vorschriften zur Barrierefreiheit erfüllen. Ist Ihr CMS konform?“
   Schreiben Sie eine Antwort von fünf bis acht Sätzen, die sich nur auf die beiden Seiten stützt.
7. Tauschen Sie die Antworten mit Ihrer Partnerin oder Ihrem Partner und markieren Sie jeden Satz, der mehr behauptet,
   als die Seiten sagen.

## Erwartetes Ergebnis

- Die Karte zeigt: WCAG 2.2 AA teilweise erfüllt, WAI-ARIA 1.2 teilweise erfüllt, EN 301 549 nicht geprüft,
  Section 508 nicht geprüft.
- Das Blatt mit drei Spalten ordnet die Punkte der Template-Autoren der Spalte des Themes zu.
- Die Antworten sagen, wogegen die Administration getestet wird, dass die öffentliche Website von Theme und Inhalten
  abhängt, dass es keinen Konformitätsbericht gibt und was das Theme leisten wird. Keine Antwort behauptet volle
  Konformität.
- Der Trainer liest zwei Antworten laut vor und bespricht die Formulierungen.

## Quiz

1. Welchen Stand hat Exponential laut seiner Standards-Seite für WCAG 2.2 AA?
   A. Vollständig erfüllt B. Teilweise erfüllt C. Nicht erfüllt D. Zertifiziert
2. Welcher Teil des Produkts ist nach Kriterien der WCAG 2.2 AA gebaut und wird mit Regressionstests dagegen geprüft?
3. Wer ist für die Barrierefreiheit eines öffentlichen Website-Designs verantwortlich?
4. Nennen Sie zwei Dinge auf einer Seite, die Redakteure bestimmen.
5. Gibt es ein VPAT oder einen Accessibility Conformance Report für Exponential?

<details>
<summary>Antworten</summary>

1. **B.** ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md#summary))
2. Die Administrationsdesigns admin4 und admin4l.
3. Die Website-Bauer: Öffentliche Designs sind nicht geprüft, und ihre Barrierefreiheit hängt vom Theme und von den
   Inhalten ab.
4. Zwei beliebige: Alternativtexte, Linktexte, Überschriften im Rich Text, Datentabellen mit Kopfzeilen, einfache
   Sprache.
5. Nein.

</details>

## Weiterführende Literatur

- [Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md): die Aussage des Projekts, mit Belegen und
  Lücken.
- [Barrierefreiheit für Website-Bauer](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md): der
  Anteil des Themes und der Redakteure.
- [Prüfung der Barrierefreiheit bei jedem Release](../../../../../eng-us/help/designers/lifecycle/accessibility-audit-per-release.md): ein
  Theme über seine ganze Lebensdauer barrierefrei halten.

## Verwandte Seiten

- [Modul 5: Barrierefreiheit und Rechts-nach-links](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
