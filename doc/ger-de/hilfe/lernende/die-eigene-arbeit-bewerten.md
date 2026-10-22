---
title: Die eigene Arbeit bewerten
audience: student
category: learning
order: 24
summary: Wie Lernende, Lerngruppen und Lehrkräfte die Lernprojekte bewerten - die vier gemeinsamen Kriterien mit 0 bis 3 Punkten, projektspezifische Prüfungen, Notenstufen, Selbst- und gegenseitige Begutachtung, Rückmeldungen, die helfen, und eine ehrliche Aussage darüber, was eine Note aus diesen Projekten bedeutet und was nicht.
keywords: bewertung, bewertungsraster, selbsteinschätzung, gegenseitige begutachtung, punktzahl, rückmeldung, feedback, kriterien, lernprojekte, noten, notenstufen, ehrlich, lernende, grading, rubric, self assessment, peer review, score, criteria, study projects, marks, grade bands, honest, learner
type: help
---
# Die eigene Arbeit bewerten

Jedes Lernprojekt endet mit einem Bewertungsraster. Diese Seite erklärt, wie Sie es verwenden, allein, mit anderen
Lernenden oder als Lehrkraft, damit eine Note etwas Echtes über die Arbeit aussagt.

## Inhalt

1. [Die vier Kriterien](#die-vier-kriterien)
2. [Projektprüfungen](#projektprüfungen)
3. [Von Punkten zur Note](#von-punkten-zur-note)
4. [Selbstbegutachtung](#selbstbegutachtung)
5. [Gegenseitige Begutachtung](#gegenseitige-begutachtung)
6. [Rückmeldungen, die helfen](#rückmeldungen-die-helfen)
7. [Was eine Note bedeutet](#was-eine-note-bedeutet)

## Die vier Kriterien

Bewerten Sie jedes Kriterium mit 0 bis 3 Punkten.

| Punkte | Es funktioniert | Es ist auf dem dokumentierten Weg gebaut | Es ist sicher | Es ist erklärt |
|---|---|---|---|---|
| **0** | Läuft nicht oder wurde nicht abgegeben | Kerndateien bearbeitet, Einstellungen am falschen Ort | Geheimnisse in Dateien oder nicht maskierte Benutzereingaben | Kein Bericht |
| **1** | Funktioniert teilweise; das Hauptziel ist nicht erreicht | Funktioniert, weicht aber stellenweise vom dokumentierten Weg ab | Eine Sicherheitslücke | Ein Bericht, dem Lesende nicht folgen können |
| **2** | Erreicht das Ziel | Folgt dem dokumentierten Weg, mit kleinen Abweichungen | Keine Lücke gefunden | Lesende können folgen und es wiederholen |
| **3** | Erreicht das Ziel und behandelt Grenzfälle | Folgt durchgehend dem dokumentierten Weg und nennt die Seiten | Sicher, und der Bericht sagt, was geprüft wurde | Klar, kurz, mit Quellen und dem Release |

„Der dokumentierte Weg“ ist das, was die Hilfeseiten sagen, zum Beispiel: Overrides statt bearbeiteter Dateien in
`design/standard/` ([Templates und Design](../../anleitungen/templates-und-design.md#1-wie-ein-template-gefunden-wird)),
Dateien `.ini.append.php` statt Änderungen an `settings/*.ini`
([Wie INI-Einstellungen funktionieren](../erweiterungsentwicklung/teil-2-builder/wie-ini-einstellungen-funktionieren.md)) und `wash`
bei der Ausgabe ([Templates und Design](../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten)).

## Projektprüfungen

Jede Projektseite fügt eine kurze Liste von Ja-Nein-Prüfungen hinzu, zum Beispiel „die Klasse hat ein Muster für den
Objektnamen“ oder „der API-Schlüssel ist nicht im Repository“. Jede Prüfung, die Sie mit Ja beantworten können, mit
einem Nachweis (einem Screenshot, einem Befehl und seiner Ausgabe), zählt. Jedes Projekt sagt, wie viele Prüfungen es
hat.

## Von Punkten zur Note

| Summe der vier Kriterien (0 bis 12) und Prüfungen | Notenstufe |
|---|---|
| 11 bis 12, und jede Prüfung erfüllt | Ausgezeichnet |
| 8 bis 10, und mindestens drei Viertel der Prüfungen erfüllt | Gut |
| 6 bis 7, und mindestens die Hälfte der Prüfungen erfüllt | Bestanden |
| Unter 6, oder eine 0 bei „Es ist sicher“ | Noch nicht: verbessern und erneut abgeben |

Eine 0 bei „Es ist sicher“ bedeutet immer „noch nicht“, unabhängig von den anderen Punkten. Eine Website, die ein
Passwort preisgibt, besteht nicht.

Eine Lehrkraft kann andere Stufen verwenden; diese sind Vorschläge für das Selbststudium.

## Selbstbegutachtung

1. Warten Sie nach dem Abschluss einen Tag.
2. Wiederholen Sie das Projekt anhand Ihres eigenen Berichts auf einer frischen Lernwebsite
   ([Die Lernwebsite zurücksetzen und neu aufbauen](die-lernwebsite-zuruecksetzen-und-neu-aufbauen.md)). Wo Sie hängen
   bleiben, ist Ihr Bericht nicht klar genug.
3. Bewerten Sie jedes Kriterium und jede Prüfung, mit einem Satz als Nachweis.
4. Verbessern Sie zuerst die niedrigste Punktzahl und bewerten Sie dann erneut.

## Gegenseitige Begutachtung

Tauschen Sie Projekte mit anderen Lernenden:

1. Jede Person liest den Bericht der anderen und wiederholt die Arbeit auf ihrer eigenen Lernwebsite.
2. Jede Person bewertet die vier Kriterien mit Nachweisen und listet die erfüllten Prüfungen auf.
3. Jede Person schreibt eine Sache auf, die gut funktioniert, und eine, die besser werden kann.
4. Treffen Sie sich für 20 Minuten und einigen Sie sich auf die Punkte. Wo Sie sich nicht einigen können, fragen Sie
   eine dritte Person.

Bitten Sie niemals jemanden, sich mit Ihrem Passwort an Ihrer Lernwebsite anzumelden; übergeben Sie stattdessen die
Dateien und den Bericht.

## Rückmeldungen, die helfen

| Statt | Schreiben Sie |
|---|---|
| „Gut gemacht“ | „Die Override-Regel trifft nur den einen Artikel, wie das Ziel es verlangt hat.“ |
| „Das Template ist falsch“ | „Zeile 4 gibt `$node.name` ohne `wash` aus.“ |
| „Schlechter Bericht“ | „Schritt 3 sagt nicht, welcher Siteaccess; ich habe `site` verwendet und keine Änderung gesehen.“ |

Die Lektion der Trainer [Rückmeldung zu Ergebnissen geben](../trainer/pruefungen/modul-01-pruefungen-gestalten/rueckmeldung-zu-ergebnissen-geben.md)
geht weiter, und [Bewertungsraster erstellen](../trainer/pruefungen/modul-01-pruefungen-gestalten/bewertungsraster-erstellen.md)
erklärt, wie Bewertungsraster wie diese gestaltet werden.

## Was eine Note bedeutet

Eine Note aus diesen Projekten sagt, dass Sie, andere Lernende oder Ihre Lehrkraft Ihre Arbeit anhand eines
veröffentlichten Bewertungsrasters mit einem benannten Release geprüft haben. Sie ist ein Nachweis für Ihr Portfolio
([Ein Portfolio aufbauen](ein-portfolio-aufbauen.md)).

Sie ist **keine** offizielle Zertifizierung. Offizielle Exponential-Zertifizierungen sind geplant, für die Community,
Partner, Exponential-Entwickler und Lernende, aber sie sind noch nicht verfügbar, und eine Projektnote gehört nicht
dazu ([Zertifizierungen und Zertifikate](zertifizierungen-und-zertifikate.md)). Bezeichnen Sie eine Projektnote nicht
als „Exponential certified“ und nicht als von 7x oder der Exponential Foundation ausgestellt oder anerkannt.

## Verwandte Seiten

- [Lernprojekte: Überblick](lernprojekte-ueberblick.md)
- [Zertifizierungen und Zertifikate](zertifizierungen-und-zertifikate.md)
- [Checklisten für Lernende](checklisten-fuer-lernende.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
