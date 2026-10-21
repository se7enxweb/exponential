---
title: Überwachung und Rückmeldungen
audience: government, project-manager, editor, admin
category: public-sector
order: 290
summary: Für die Koordination Barrierefreiheit - wie Sie eine Live-Website barrierefrei halten, mit einer monatlichen Stichprobe der Inhalte, einem Rückmeldekanal aus den eigenen Werkzeugen von Exponential, einer Routine für Antworten und den Nachweisen, die eine Überwachungsstelle oder Prüfende verlangen werden.
keywords: Überwachung, Rückmeldung, Feedback-Mechanismus, Meldung einer Barriere, Kontaktformular, Informationssammler, Stichprobe, Inhaltsprüfung, Antwortzeit, Nachweise, Erklärung zur Barrierefreiheit, kontinuierliche Verbesserung, öffentlicher Sektor, monitoring, feedback, feedback mechanism, barrier report, contact form, information collector, spot check, content review, response time, records, accessibility statement, continuous improvement, public sector
type: help
---
# Überwachung und Rückmeldungen

Diese Seite richtet sich an die Koordination Barrierefreiheit und an Projektverantwortliche, die eine Website nach dem
Start barrierefrei halten, und an Redaktion und Administration, die ihnen helfen. Gesetze für öffentliche Stellen
verlangen meist einen Weg, auf dem Menschen Barrieren melden und Informationen in anderer Form anfordern können, sowie
regelmäßige Prüfungen. Exponential gibt Ihnen die Bausteine; die Routine ist Ihre Sache.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Ein Rückmeldekanal](#ein-rückmeldekanal)
2. [Meldungen beantworten](#meldungen-beantworten)
3. [Eine monatliche Stichprobe der Inhalte](#eine-monatliche-stichprobe-der-inhalte)
4. [Auf Rückschritte achten](#auf-rückschritte-achten)
5. [Aufzubewahrende Nachweise](#aufzubewahrende-nachweise)
6. [Verwandte Seiten](#verwandte-seiten)

## Ein Rückmeldekanal

- Bieten Sie mindestens zwei Wege an: eine E-Mail-Adresse und ein Formular (und eine Telefonnummer, wo Sie eine haben).
- Bauen Sie das Formular mit dem Informationssammler: die mitgelieferte Klasse Feedback form (`feedback_form`) oder
  eine eigene Klasse, sodass jede Meldung als Sammlung gespeichert und an ein Team gemailt werden kann
  ([Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md)).
- Felder: was die Person tun wollte, die Seitenadresse (wo möglich vorausgefüllt), was geschehen ist, wie geantwortet
  werden soll. Machen Sie jedes Feld außer der Beschreibung optional.
- Verlinken Sie den Kanal aus der Erklärung zur Barrierefreiheit und der Fußzeile.
- Das Formular muss selbst barrierefrei sein (siehe „Barrierefreie Formulare“).

## Meldungen beantworten

1. Bestätigen Sie jede Meldung innerhalb einer festen Frist und geben Sie ein Aktenzeichen.
2. Stellen Sie die Information sofort in barrierefreier Form bereit, wenn es um Inhalte geht (schicken Sie den Text
   eines PDF, beschreiben Sie ein Bild).
3. Ordnen Sie die Ursache ein: Inhalt (der Redaktion zuweisen), Template (der Entwicklung), Erweiterung, Dritter.
4. Beheben Sie, antworten Sie und nehmen Sie den Fall in Ihre Befundtabelle auf.
5. Lesen Sie Meldungen unter **Setup > Gesammelte Informationen**; der Zugriff erfordert `infocollector/read`, geben Sie
   diese Richtlinie also den Personen, die Meldungen bearbeiten
   ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)).

## Eine monatliche Stichprobe der Inhalte

- Nehmen Sie zehn Seiten, die im Monat veröffentlicht oder geändert wurden (das Dashboard und die Suche können aktuelle
  Inhalte auflisten).
- Für jede: Alternativtext, Überschriften, Linktexte, Tabellen, verlinkte Dokumente, Sprache.
- Wo die Erweiterung Exponential Intelligence aktiv ist, listet der Bericht **Missing alt text** (Fehlender
  Alternativtext; `/expai/alttext`) Bilder ohne Alternativtext unterhalb eines Knotens auf
  ([Exponential Intelligence](../../../funktionen/6.0/erweiterungen/expai.md)).
- Geben Sie Probleme mit einem Beispiel für die bessere Fassung an die Redaktion zurück.

## Auf Rückschritte achten

- Nach jedem Release, das Templates ändert: eine automatische Prüfung und ein Tastaturdurchgang auf den geänderten
  Templates (siehe „Mit axe und der Tastatur testen“).
- Nach einem Upgrade: Lesen Sie das Changelog des Release auf Einträge zur Barrierefreiheit (das Projekt hält
  Korrekturen dort fest, zum Beispiel im [Changelog von 6.0.15](../../../../changelogs/6.0/6.0.15.md)).
- Prüfen Sie eingebettete Inhalte Dritter (Karten, Video, Formulare anderer Dienste) erneut, wenn der Anbieter sie
  ändert.

## Aufzubewahrende Nachweise

| Nachweis | Warum |
|---|---|
| Prüfberichte mit Datum | der Abschnitt zur Bewertung in der Erklärung; Überwachungsstellen |
| Befundtabelle mit Korrekturen | der Plan in der Erklärung; Entwicklung über die Zeit |
| Erhaltene Rückmeldungen und Antworten | Beleg, dass der Mechanismus funktioniert |
| Fassungen der Erklärung | was wann erklärt wurde (als Versionen des Objekts der Erklärung aufbewahrt, im Rahmen der Grenze des Versionsverlaufs) |
| Schulung der Redaktion | Nachweis ergriffener Maßnahmen |

## Verwandte Seiten

- [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)
- [Formulareingaben](../../ersteller/website-ersteller/formulareinsendungen.md)
- [Die Barrierefreiheit Ihrer Website testen](../../ersteller/website-ersteller/barrierefreiheit-testen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
