---
title: Prüfbare Anforderungen schreiben
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 20
summary: Für Beschaffungsstellen und Projektleitungen, die die Anforderungen einer Ausschreibung für ein Content-Management-System schreiben - wie Sie jede Anforderung so formulieren, dass ein Bieter sie mit Nachweisen beantworten und eine bewertende Person sie prüfen kann.
keywords: Anforderungen, prüfbar, testbar, Ausschreibung, Leistungsbeschreibung, Spezifikation, Abnahmekriterien, Nachweise, MoSCoW, muss, soll, Beschaffung, öffentlicher Sektor, CMS, requirements, verifiable, testable, tender, RFP, specification, acceptance criteria, evidence, must, should, procurement, public sector
type: help
---
# Prüfbare Anforderungen schreiben

Diese Seite richtet sich an Beschaffungsstellen, Projektleitungen und die Fachleute, die ihnen helfen, den
Anforderungsteil einer Ausschreibung für ein Web-Content-Management-System (CMS) zu schreiben. Sie beantwortet eine
Frage: Wie formulieren Sie eine Anforderung so, dass ein Bieter sie mit Nachweisen beantworten und Ihre Bewertenden die
Antwort prüfen können? Die Beispiele verwenden Exponential und seine Dokumentation, weil seine Seiten zu den Standards
zeigen, wie gute Nachweise aussehen ([Standards](../../../../../eng-us/standards/README.md)).

## Inhalt
1. [Was eine Anforderung prüfbar macht](#was-eine-anforderung-prüfbar-macht)
2. [Die Bestandteile einer guten Anforderung](#die-bestandteile-einer-guten-anforderung)
3. [Schwache und starke Beispiele](#schwache-und-starke-beispiele)
4. [Prioritäten: muss, soll, kann](#prioritäten-muss-soll-kann)
5. [Nach Nachweisen fragen](#nach-nachweisen-fragen)
6. [Fallstricke](#fallstricke)

## Was eine Anforderung prüfbar macht

Eine Anforderung ist prüfbar, wenn zwei Personen, die die Antwort des Bieters lesen, zum selben Schluss kommen. Dazu
braucht es:

- **Einen Gegenstand je Anforderung.** „Das System muss sicher und barrierefrei sein“ lässt sich nicht bewerten;
  teilen Sie sie auf.
- **Einen benannten Standard oder ein messbares Ergebnis.** „WCAG 2.2 Stufe AA“ lässt sich prüfen;
  „benutzerfreundlich“ nicht.
- **Einen Geltungsbereich.** Welcher Teil des Systems: die öffentliche Website, die Administration, der Editor, die
  API.
- **Eine Form des Nachweises.** Ein Dokument, ein Test, eine Vorführung, eine Einstellung, ein Protokoll.
- **Eine neutrale Formulierung.** Sie beschreibt, was Sie brauchen, nicht die Funktionsnamen eines Produkts. Ein
  Produkt oder eine Marke in einer Anforderung zu nennen, schränkt meist den Wettbewerb ein; prüfen Sie Ihre
  Vergaberegeln.

## Die Bestandteile einer guten Anforderung

| Bestandteil | Beantwortete Frage | Beispiel |
|---|---|---|
| Kennung | Wie verweisen wir darauf? | `SEC-07` |
| Aussage | Was wird gebraucht? | „Administratoren können bei der Anmeldung einen zweiten Faktor verlangen.“ |
| Geltungsbereich | Wo gilt sie? | „Alle Konten mit Zugang zur Administration.“ |
| Priorität | Wie wichtig ist sie? | Muss |
| Verlangter Nachweis | Wie belegt der Bieter sie? | „Nennen Sie die Komponente, verlinken Sie ihre Dokumentation, zeigen Sie sie in der Vorführung.“ |
| Prüfung | Wie prüfen wir sie? | „Eine bewertende Person meldet sich während der Vorführung mit einem zweiten Faktor an.“ |

## Schwache und starke Beispiele

| Schwach | Stark | Warum |
|---|---|---|
| „Das CMS muss barrierefrei sein.“ | „Die von der Redaktion genutzte Administrationsoberfläche erfüllt WCAG 2.2 Stufe AA. Geben Sie Ihren Konformitätsstatus je Kriterium und die Nachweise an (Testberichte, Audit, Konformitätsbericht).“ | Nennt Standard, Stufe, Geltungsbereich und Nachweise. |
| „Gute Sicherheit.“ | „Das System schützt jedes Formular gegen Cross-Site Request Forgery. Nennen Sie den Mechanismus und zeigen Sie, wo er konfiguriert ist.“ | Eine Maßnahme, ein benannter Mechanismus. Exponential dokumentiert Formular-Tokens in [Sicherheitsstandards](../../../../../eng-us/standards/security.md). |
| „Unterstützt viele Sprachen.“ | „Die Redaktion kann dieselbe Seite in mindestens <Anzahl> Sprachen veröffentlichen, einschließlich Schriften von rechts nach links, und Besuchern wird die Sprache der Adresse angezeigt, die sie verwendet haben.“ | Messbar; die Zahl füllen Sie aus. |
| „Daten können exportiert werden.“ | „Alle veröffentlichten Inhalte mit ihren Metadaten und Dateien lassen sich ohne Mitwirkung des Bieters in einem offenen, dokumentierten Format exportieren. Beschreiben Sie Werkzeuge und Formate.“ | In einem Proof of Concept prüfbar. Vergleichen Sie [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md). |
| „Support rund um die Uhr.“ | „Kritische Störungen erhalten zu jeder Stunde innerhalb von <Zeit> eine erste Reaktion; nennen Sie die Personalausstattung, die das leistet.“ | Die Zahl ist eine Lücke, die Sie nach Ihrem eigenen Bedarf füllen; der Bieter muss sagen, wie er sie einhält. |

## Prioritäten: muss, soll, kann

Die meisten Vergaberahmen trennen Mindestanforderungen (bestanden oder nicht bestanden) von bewerteten Anforderungen.
Halten Sie die Liste der Mindestanforderungen kurz: Jedes „muss“, das ein guter Bieter nicht erfüllen kann, entfernt
einen Bieter.

| Priorität | Verwenden für | Bewertung |
|---|---|---|
| Muss | rechtliche Pflichten, Grundlagen der Sicherheit, Dinge, ohne die Sie nicht arbeiten können | bestanden oder nicht bestanden |
| Soll | wichtig, aber eine Lücke kann vom Bieter oder von Ihnen geschlossen werden | bewertet |
| Kann | nützliche Extras | gering bewertet oder als Entscheidung bei Gleichstand |

Eine Anforderung, die der Markt heute nicht erfüllen kann, ist ein „soll“, mit der Bitte, den Plan zu beschreiben.
Konformitätsberichte zur Barrierefreiheit sind ein Beispiel: Exponential hat keinen ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)),
und viele andere Open-Source-Systeme auch nicht.

## Nach Nachweisen fragen

Fragen Sie nach Nachweisen, die es gibt und die Sie selbst prüfen können:

- **Links zur Dokumentation** auf die genaue Seite und den Abschnitt, nicht auf eine Startseite.
- **Einstellungen** nach Datei und Schlüssel (zum Beispiel `settings/site.ini [UserSettings] MinPasswordLength`), die
  Sie in einer Testinstallation sehen können.
- **Tests**, die in öffentlicher CI laufen. Die Testsuiten von Exponential stehen in
  [Tests und kontinuierliche Integration](../../../../../eng-us/standards/testing-and-ci.md).
- **Eine Vorführung** auf einem System, das Sie selbst bedienen können, oder eine Installation für einen Proof of
  Concept, die Sie selbst betreiben. Exponential lässt sich mit einem Befehl auf SQLite installieren ([Exponential mit einem Befehl installieren](../../../../../eng-us/features/6.0/install-in-one-command.md)).
- **Ehrliche Lücken.** Bitten Sie den Bieter, aufzulisten, was nicht erfüllt ist. Ein Bieter, der jede Zeile ohne
  Nachweis mit „voll konform“ beantwortet, gibt Ihnen weniger Information als einer, der „Teilweise erfüllt“ sagt und die
  Lücke zeigt.

## Fallstricke

- **Anforderungen aus einer anderen Ausschreibung kopieren**, ohne sie gegen Ihren Bedarf zu prüfen.
- **Nach einem Zertifikat fragen, das die Produktkategorie nicht hat.** Fragen Sie, was es gibt, und bewerten Sie es.
- **Produkt und Leistung vermischen.** „Die Software unterstützt Sicherungen“ und „der Lieferant erstellt tägliche
  Sicherungen“ sind zwei Anforderungen mit zwei verschiedenen Verantwortlichen.
- **Versteckte Zahlen.** Brauchen Sie eine Reaktionszeit, schreiben Sie sie hinein; lassen Sie die Bieter nicht raten.
- **Keine Prüfmethode.** Entscheiden Sie beim Schreiben einer Anforderung, wie Sie sie prüfen werden, nicht erst bei
  der Bewertung.

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Anforderungen Nachweisen zuordnen](anforderungen-nachweisen-zuordnen.md)
- [Was Exponential nachweisen kann und was nicht](was-exponential-nachweisen-kann-und-was-nicht.md)
- [Standards](../../../../../eng-us/standards/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
