---
title: Shownotes schreiben
audience: podcaster
category: podcasting-streaming
order: 14
summary: Wie Sie Shownotes im Editor für formatierten Text von Exponential so schreiben, dass sie Hörern auf der Website und in Podcast-Apps dienen - Aufbau, Links, Kapitelzeiten, Mitwirkende, Hinweis auf Sponsoren und wie die Notizen in die Beschreibung des Feeds gelangen.
keywords: Shownotes, Episodenbeschreibung, formatierter Text, Online-Editor, Links, Kapitel, Zeitmarken, Mitwirkende, Hinweis auf Sponsoren, Feed-Beschreibung, SEO, Barrierefreiheit, show notes, episode description, rich text, online editor, chapters, timestamps, credits, sponsor disclosure, feed description, accessibility
type: help
---
# Shownotes schreiben

Diese Seite ist für Moderatoren und Produzenten, die den Text zu einer Episode schreiben. Shownotes haben zwei Leser:
den Besucher auf der Episodenseite und die Podcast-App, die die Beschreibung aus dem Feed zeigt. Einen fertigen Aufbau
finden Sie in der [Vorlage für Shownotes](vorlage-fuer-shownotes.md).

## Inhalt

1. [Wohin die Notizen gehören](#wohin-die-notizen-gehören)
2. [Was gute Notizen enthalten](#was-gute-notizen-enthalten)
3. [Links](#links)
4. [Kapitelzeiten](#kapitelzeiten)
5. [Mitwirkende und Offenlegung](#mitwirkende-und-offenlegung)
6. [Wie die Notizen in den Feed gelangen](#wie-die-notizen-in-den-feed-gelangen)

## Wohin die Notizen gehören

Schreiben Sie die Notizen in das Feld mit formatiertem Text der Episode (*Description*, *Caption*, *Show notes*, je
nach Klasse). Der Editor für formatierten Text bietet Absatzformate, Fett, Kursiv, Listen, Links, Tabellen und
**Objekt einbetten** ([Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md#rich-text)). Verwenden Sie Überschriften
aus den Absatzformaten statt fetter Zeilen, damit Screenreader und Suchmaschinen die Struktur erkennen.

## Was gute Notizen enthalten

1. **Ein oder zwei Sätze** dazu, worum es in der Episode geht, ganz am Anfang, weil Apps den Rest oft abschneiden.
2. **Gäste** mit ihrer Rolle und jeweils einem Link.
3. **Themen oder Kapitel** mit Zeiten.
4. **Links** zu allem, was erwähnt wird.
5. **Mitwirkende**: Moderation, Produktion, Schnitt, Musik.
6. **Offenlegung** von Sponsoren und bezahlten Erwähnungen.
7. **Wo das Transkript zu finden ist**, wenn es auf einer anderen Seite steht ([Transkripte und Untertitel](transkripte-und-untertitel.md)).

## Links

- Verlinken Sie mit aussagekräftigem Text: "die Versionshinweise zu 6.0", nicht "hier".
- Verlinken Sie andere Episoden mit dem Link-Dialog des Editors, der ein Objekt der Website auswählen kann; solche
  Links folgen der Episode, wenn sie verschoben wird.
- Prüfen Sie externe Links vor dem Veröffentlichen. Die Linkprüfung der Website kann defekte Links später melden
  ([Link- und Inhaltsprüfungen](../ersteller/website-ersteller/link-und-inhaltspruefungen.md)).

## Kapitelzeiten

Exponential schreibt keine Kapitelmarken in das Audio oder den Feed. Schreiben Sie Kapitelzeiten als einfachen Text in
die Notizen:

```text
00:00 Introduction
04:30 Interview with the guest
38:10 Listener questions
```

Verwenden Sie `MM:SS` oder `HH:MM:SS` einheitlich. Manche Apps erkennen solche Zeiten in der Beschreibung; auf der
Website sind sie einfacher Text.

## Mitwirkende und Offenlegung

- Nennen Sie alle, die an der Episode mitgearbeitet haben und genannt werden möchten. Fragen Sie Gäste, wie sie genannt
  werden möchten ([Gäste und Mitwirkende](gaeste-und-mitwirkende.md)).
- Nennen Sie Sponsoring am Anfang der Notizen und ebenso im Audio. Der Pressebereich erklärt die ethischen Fragen
  ([Ethik von Offenlegung und Sponsoring](../presse/offenlegung-und-ethik-beim-sponsoring.md)).
- Nennen Sie Musik und anderes Material Dritter mit seiner Lizenz ([Rechte und Lizenzen](rechte-und-lizenzen.md)).

## Wie die Notizen in den Feed gelangen

Im Podcast-Export stammt die `description` jeder Episode aus dem Feld, das für ihre Quelle als **Beschreibung**
zugeordnet ist ([Referenz der Episodenzuordnung](referenz-der-episodenzuordnung.md)). Bei einem Feld mit formatiertem Text
erhält der Feed die gerenderte Ausgabe des Felds als Text (`podcastText()` in `kernel/classes/ezrssexport.php`); das
Markup wird in das XML maskiert, wie RSS es verlangt. Viele Apps zeigen es mit Links; manche zeigen einfachen Text.

Wenn der Feed nur eine kurze Zusammenfassung zeigen soll, geben Sie der Klasse ein eigenes Feld für eine kurze
Beschreibung, ordnen dieses als **Beschreibung** zu und behalten die vollständigen Notizen auf der Episodenseite.

## Vor dem Veröffentlichen

- [ ] Der erste Satz funktioniert für sich allein.
- [ ] Jeder Gast ist wie vereinbart genannt und verlinkt.
- [ ] Sponsoren sind offengelegt.
- [ ] Die Links sind getestet.
- [ ] Keine privaten Daten: keine Telefonnummern, Privatadressen oder E-Mail-Adressen von Gästen, außer sie haben darum
  gebeten ([Privatsphäre in Ihrer Berichterstattung](../presse/privatsphaere-in-ihrer-berichterstattung.md)).

## Verwandte Seiten

- [Vorlage für Shownotes](vorlage-fuer-shownotes.md)
- [Gäste in Podcast und Radio](../presse/gaeste-in-podcast-und-radio.md)
- [SEO-Grundlagen](../ersteller/website-ersteller/seo-grundlagen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
