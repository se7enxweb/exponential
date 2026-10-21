---
title: Barrierefreiheit pflegen
audience: customer, editor, project-manager
category: customer-success
order: 19
summary: Wie ein Website-Betreiber eine Exponential-Website nach dem Start für Menschen mit Behinderungen nutzbar hält - die Aufteilung zwischen Template-Arbeit und Inhaltsarbeit, die ausgelieferten Funktionen, die helfen, eine vierteljährliche Stichprobe, der Umgang mit Meldungen von Besuchern und was Sie den Partner nach Designänderungen fragen.
keywords: Barrierefreiheit, Pflege, WCAG, Screenreader, Tastatur, Alternativtext, Kontrast, Stichprobe, Barrieren, Website-Betreiber, inklusiv, accessibility, upkeep, screen reader, keyboard, alternative text, contrast, spot check, barriers, site owner, inclusive
type: help
---
# Barrierefreiheit pflegen

Diese Seite ist für den Betreiber, der dafür verantwortlich ist, dass die Website für alle nutzbar ist, auch für
Menschen, die Screenreader, Tastaturen, Vergrößerung oder Sprachsteuerung verwenden. Barrierefreiheit wird vom
Website-Ersteller in Templates und Stylesheets eingebaut und **von der Redaktion gepflegt**
([Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)). Die
Referenz, auf die die meisten Gesetze verweisen, sind die Web Content Accessibility Guidelines des W3C
([WCAG](https://www.w3.org/WAI/standards-guidelines/wcag/)). Diese Seite ist keine Rechtsberatung dazu, welche
Vorschriften für Sie gelten.

## Inhalt

1. [Zwei Arten von Arbeit](#zwei-arten-von-arbeit)
2. [Wobei Exponential hilft](#wobei-exponential-hilft)
3. [Eine vierteljährliche Stichprobe](#eine-vierteljährliche-stichprobe)
4. [Wenn ein Besucher eine Barriere meldet](#wenn-ein-besucher-eine-barriere-meldet)
5. [Nach Designänderungen](#nach-designänderungen)

## Zwei Arten von Arbeit

| Template-Arbeit (einmal, für jede Seite) | Inhaltsarbeit (Redaktion, Seite für Seite) |
|---|---|
| Seitensprache, Landmarks, Sprunglink | Alternativtexte für Bilder |
| eine Hauptüberschrift pro Seite, sichtbarer Fokus | Überschriften als Überschriften verwendet, in der richtigen Reihenfolge |
| Kontrast von Text und Schaltflächen | Linktexte, die sagen, wohin sie führen |
| Zoom auf 200 % und Smartphone-Breiten | Tabellen mit Kopfzeilen, nicht für das Layout |
| barrierefreie Formulare und Fehlermeldungen | einfache Sprache, kurze Absätze |

Quelle: [Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md).
Probleme in der linken Spalte gehen an Ihren Partner; Probleme in der rechten Spalte behebt Ihr Team mithilfe der
[Checkliste zur Barrierefreiheit für Redakteure](checkliste-barrierefreiheit-fuer-redakteure.md).

## Wobei Exponential hilft

- Bildfelder können einen Alternativtext **verlangen**; bitten Sie Ihren Administrator, das für Ihre Bildklassen
  einzuschalten ([Das Inhaltsmodell und das Bearbeiten](../../../anleitungen/inhaltsmodell-und-bearbeitung.md)).
- Der Online-Editor schreibt strukturierten Rich Text (Überschriften, Listen, Tabellen) statt freiem HTML.
- Inhalte in Sprachen, die von rechts nach links geschrieben werden, sind mit ihrer Sprache und Schreibrichtung
  gekennzeichnet ([Websites mit Schreibrichtung von rechts nach links](../../../../eng-us/help/creators/website-creators/right-to-left-sites.md)).
- Die Administration selbst folgt einem barrierefreien Design, sodass auch Redakteure mit Behinderungen arbeiten
  können ([admin4-Design](../../../../eng-us/features/6.0/admin4-design.md)).
- Die Inhaltsprüfungen finden kaputte Beziehungen und fehlende Dateien
  ([Die Inhaltsprüfungen](../../../../eng-us/features/6.0/content-checks.md)).

## Eine vierteljährliche Stichprobe

Testen Sie nicht jede Seite; testen Sie eine Seite jeder Art
([Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)):

1. Wählen Sie die Startseite, eine Landingpage, einen Artikel, eine Liste, die Suchergebnisse und jedes Formular.
2. Lassen Sie auf jeder Seite ein automatisches Prüfwerkzeug laufen (eine Browser-Erweiterung). Notieren Sie Fehler.
3. Legen Sie die Maus weg und verwenden Sie Tab, Enter und Escape: Können Sie alles erreichen und bedienen, und sehen
   Sie immer, wo Sie sind?
4. Zoomen Sie den Browser auf 200 %: Nichts ist abgeschnitten, kein seitliches Scrollen.
5. Lesen Sie fünf aktuelle Artikel auf die Inhaltsregeln hin: Alternativtexte, Überschriften, Linktexte.
6. Sortieren Sie die Funde in Template-Arbeit (an den Partner) und Inhaltsarbeit (an die Redaktion).

Halten Sie die Funde in einer Tabelle fest, etwa der unter
[Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md#recording-findings).

## Wenn ein Besucher eine Barriere meldet

Geben Sie Besuchern eine Möglichkeit, Barrieren zu melden, zum Beispiel ein Kontaktformular, und antworten Sie ihnen
([Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)).

| Schritt | Wer |
|---|---|
| Dem Besucher danken und die Information sofort auf anderem Weg anbieten (Telefon, E-Mail, eine reine Textfassung) | wer das Formular liest |
| Die Seite und das Problem finden; entscheiden, ob Template oder Inhalt | Chefredaktion |
| Inhaltsprobleme in derselben Woche beheben | Redaktion |
| Template-Probleme an den Partner schicken, mit der Seitenadresse, dem, was passiert, und dem Hilfsmittel des Besuchers, falls bekannt | Eigentümer |
| Dem Besucher mitteilen, wenn es behoben ist | wer zuerst geantwortet hat |

## Nach Designänderungen

- Bitten Sie Ihren Partner, nach jeder Designänderung die automatischen Prüfungen als Teil seiner Release-Prüfungen
  laufen zu lassen ([Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)).
- Prüfen Sie die neuen Seiten selbst mit der Tastatur und bei 200 % Zoom, bevor Sie sie abnehmen
  ([Qualitätsschranken und Abnahme](../../fuehrung/qualitaetsschranken-und-abnahme.md)).
- Stellen Sie sicher, dass jedes neue Farbschema und jeder Dunkelmodus genug Kontrast behält.

## Verwandte Seiten

- [Checkliste zur Barrierefreiheit für Redakteure](checkliste-barrierefreiheit-fuer-redakteure.md)
- [Checklisten zu Barrierefreiheit, Sicherheit und Compliance](../../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md)
- [Barrierefreiheit für gemeinnützige Organisationen](../../unternehmen/gemeinnuetzige/barrierefreiheit-fuer-gemeinnuetzige.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
