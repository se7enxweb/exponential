---
title: Barrierefreiheit für Website-Ersteller
audience: website-creator, editor
category: website-creation
order: 600
summary: Eine öffentliche Website bauen, die alle nutzen können - was der Template-Autor steuert (Landmarks, Überschriften, Sprache, Fokus, Kontrast, Formulare), was die Redaktion steuert (Alternativtext, Linktext, Struktur) und welche Funktionen von Exponential dabei helfen.
keywords: Barrierefreiheit, Screenreader, Tastatur, Landmarks, Überschriften, Alternativtext, Kontrast, Fokus, Formulare, Sprache, Website-Ersteller, accessibility, a11y, WCAG, screen reader, keyboard, alternative text, lang, RTL, website creator
type: help
---
# Barrierefreiheit für Website-Ersteller

Diese Seite richtet sich an Website-Ersteller, die eine Website wollen, die Menschen mit Behinderungen mit Screenreadern,
Tastatur, Vergrößerung oder Sprachsteuerung nutzen können. Barrierefreiheit entsteht in Templates und Stylesheets und
wird von der Redaktion erhalten. Die Referenz, auf die die meisten Gesetze verweisen, sind die Web Content Accessibility
Guidelines des W3C ([WCAG](https://www.w3.org/WAI/standards-guidelines/wcag/)).

## Inhalt

1. [Was der Template-Autor steuert](#was-der-template-autor-steuert)
2. [Was die Redaktion steuert](#was-die-redaktion-steuert)
3. [Funktionen von Exponential, die helfen](#funktionen-von-exponential-die-helfen)
4. [Formulare](#formulare)
5. [Checkliste](#checkliste)

## Was der Template-Autor steuert

| Bereich | Vorgehen |
|---|---|
| Sprache | `<html lang="...">` aus `$site.http_equiv.Content-language`; `lang` an Inhalten in einer anderen Sprache; `dir="rtl"` für Schreibrichtung von rechts nach links ([Websites mit Schreibrichtung von rechts nach links](websites-von-rechts-nach-links.md)) |
| Landmarks | `header`, `nav` (mit `aria-label`, wenn es mehrere gibt), `main`, `footer` |
| Sprunglink | ein Link „Zum Inhalt springen“ als Erstes auf der Seite, auf `#main` ([Das Pagelayout](das-pagelayout.md)) |
| Überschriften | ein `h1` pro Seite (der Seitenname), Überschriften in der richtigen Reihenfolge |
| Navigation | die aktuelle Seite mit `aria-current="page"` markiert ([Menüs und Navigation](menues-und-navigation.md)) |
| Fokus | ein sichtbarer Fokusstil an jedem Link und Bedienelement; niemals `outline: none` ohne Ersatz |
| Kontrast | Text mindestens 4,5:1 zum Hintergrund, großer Text 3:1 |
| Bilder | `alt` aus dem Alternativtext des Bildes; dekorative Bilder `alt=""` |
| Bewegung | `prefers-reduced-motion` bei Slidern und Animationen beachten |
| Zoom | Layouts, die bei 200 % Zoom und auf schmalen Bildschirmen funktionieren, ohne horizontales Scrollen |

## Was die Redaktion steuert

- **Alternativtexte** für Bilder: machen Sie sie in Bildfeldern zur Pflicht
  ([Das Inhaltsmodell und die Bearbeitung](../../../anleitungen/inhaltsmodell-und-bearbeitung.md)).
- **Linktexte**, die sagen, wohin sie führen.
- **Überschriften** im Rich Text, als Überschriften verwendet, in der richtigen Reihenfolge, nicht wegen der Schriftgröße.
- **Tabellen** mit Kopfzeilen für Daten, nicht für das Layout.
- **Einfache Sprache**, kurze Absätze.

Geben Sie der Redaktion bei der Übergabe eine kurze Liste dieser Punkte mit
([Die Website an die Redaktion übergeben](die-website-an-die-redaktion-uebergeben.md)).

## Funktionen von Exponential, die helfen

- Bildfelder können einen Alternativtext verlangen.
- Der Online-Editor schreibt strukturierten Rich Text (Überschriften, Listen, Tabellen) statt freiem HTML.
- Inhalte mit Schreibrichtung von rechts nach links werden von `parts/exp_content_direction.tpl` mit `lang` und `dir`
  markiert.
- Die Administration selbst folgt einem barrierefreien Design ([admin4-Design](../../../../eng-us/features/6.0/admin4-design.md)),
  sodass auch Redakteure mit Behinderungen damit arbeiten können.
- Inhaltsprüfungen finden Probleme in Inhalten ([Inhaltsprüfungen](../../../../eng-us/features/6.0/content-checks.md)).

## Formulare

- Jedes Eingabefeld hat ein sichtbares `<label>`.
- Pflichtfelder sind im Text gekennzeichnet.
- Fehler werden neben dem Feld angezeigt und oben zusammengefasst, und die Zusammenfassung erhält den Fokus.
- CAPTCHAs haben eine barrierefreie Alternative (beide mitgelieferten CAPTCHA-Dienste bieten eine an).

## Checkliste

- [ ] Landmarks, Sprunglink und ein `h1` in jedem Seitentyp.
- [ ] Nur Tastatur: Jedes Menü, Formular und jeder Dialog ist bedienbar und der Fokus ist immer sichtbar.
- [ ] Kontrast für Text, Links und Schaltflächen geprüft, in jedem Farbschema, das das Design hat.
- [ ] Bilder haben Alternativtexte; dekorative Bilder haben leere.
- [ ] Formulare beschriftet; Fehler werden angesagt.
- [ ] 200 % Zoom und Smartphone-Breite funktionieren.
- [ ] Mit einem automatischen Prüfwerkzeug und von Hand getestet ([Barrierefreiheit testen](barrierefreiheit-testen.md)).

## Verwandte Seiten

- [Barrierefreiheit testen](barrierefreiheit-testen.md)
- [Checklisten für Barrierefreiheit, Sicherheit und Compliance](../../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md)
- [Websites mit Schreibrichtung von rechts nach links](websites-von-rechts-nach-links.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
