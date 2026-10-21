---
title: Fragensammlung für Interviews
audience: press
category: press-media
order: 122
summary: Fertige Interviewfragen zu Exponential für 7x und die Exponential Foundation, nach Themen geordnet - Geschichte, Organisationen, Releases, Produkt, Sicherheit, Community, Finanzierung - jeweils mit dem dokumentierten Hintergrund, damit das Interview seine Zeit auf das verwendet, was die Quellen nicht sagen.
keywords: Interview, Fragen, Fragensammlung, 7x, Exponential Foundation, Geschichte, Finanzierung, Steuerung, Releases, Roadmap, Sicherheit, Community, Open Source, interview, questions, question bank, history, funding, governance, releases, roadmap, security, community, open source
type: help
---
# Fragensammlung für Interviews

Diese Seite ist für Interviewer: Journalisten, Podcast-Moderatoren, Video-Creator und studentische Reporter. Zu jeder
Frage steht der Hintergrund, den die Dokumentation bereits liefert, sodass Sie Beantwortetes überspringen und nach
Unbeantwortetem fragen können. Das sind Fragen, keine Antworten; nichts auf dieser Seite ist eine Aussage von 7x oder
der Foundation.

## Inhalt

1. [Wie Sie diese Sammlung nutzen](#wie-sie-diese-sammlung-nutzen)
2. [Geschichte und Beweggründe](#geschichte-und-beweggründe)
3. [Zwei Organisationen](#zwei-organisationen)
4. [Releases und der Weg nach vorn](#releases-und-der-weg-nach-vorn)
5. [Produkt und Technik](#produkt-und-technik)
6. [Sicherheit und Vertrauen](#sicherheit-und-vertrauen)
7. [Community, Finanzierung und Open Source](#community-finanzierung-und-open-source)
8. [Fragen, die Sorgfalt brauchen](#fragen-die-sorgfalt-brauchen)

## Wie Sie diese Sammlung nutzen

Wählen Sie fünf bis acht Fragen aus. Lesen Sie zuerst die Spalte mit dem Hintergrund: Wenn die Dokumentation die Frage
bereits beantwortet, zitieren Sie die Dokumentation und nutzen Sie Ihre Zeit für die Nachfrage. Offene Fragen sind mit
**offen** markiert.

## Geschichte und Beweggründe

| Frage | Hintergrund in den Quellen |
|---|---|
| Warum eine Codebasis am Leben halten, nachdem ihr Hersteller sie eingestellt hat? | „Exponential has made it beyond it's end of life in 2021 and survived“ (README, zitiert in [Wer ist 7x?](../wer-ist-7x.md#kurz-gesagt)). Die Gründe sind **offen** |
| Was geschah in den drei Phasen, 2014, 2022 und Dezember 2023? | [Von Brookins Consulting zu 7x](../wer-ist-7x.md#von-brookins-consulting-zu-7x-in-drei-stufen); die Geschichte hinter jeder Phase ist **offen** |
| Warum der Name Exponential? | Die Projektseite nennt eine Bitte von Ibexa, wirksam ab August 2025 ([Warum das Produkt Exponential heißt](../wer-ist-7x.md#warum-das-produkt-exponential-heißt)); wie der Name gewählt wurde, ist **offen** |
| Was war am schwierigsten daran, den Code auf aktuelles PHP zu bringen? | Die Unterstützung für PHP 7 endete mit 6.0.7; PHP 8.5 kam im Dezember 2025 ([Geschichte](../../../eng-us/history/README.md#the-story-so-far)); die Erfahrung ist **offen** |

## Zwei Organisationen

| Frage | Hintergrund in den Quellen |
|---|---|
| Wie teilen sich 7x und die Foundation die Arbeit? | 7x „führt die Entwicklung“; die Foundation „betreibt das Projekt, finanziert und lenkt es“ ([Die Foundation und 7x](../wer-ist-die-exponential-foundation.md#die-foundation-und-7x)) |
| Wer sitzt im Vorstand der Foundation, und wie wird sie geführt? | **Offen**: „wer im Vorstand der Foundation sitzt, wie sie geführt wird, ob sie außer ihren Unterstützern Mitglieder hat und wie hoch ihr Budget ist“, ist nicht beantwortet ([Was die Quellen nicht sagen](../wer-ist-die-exponential-foundation.md#was-die-quellen-nicht-sagen)) |
| Welche Ziele hat die Foundation für 2026? | Ihre Website erwähnt „eine lange, ausführliche Liste von Wachstumszielen“, aber „die Liste selbst steht nicht auf der Seite“ ([Die Website der Foundation](../wer-ist-die-exponential-foundation.md#die-website-der-foundation)) |
| Warum 2026 eine gemeinnützige Organisation gründen? | Gründungsdatum und -ort sind dokumentiert; die Gründe sind **offen** |

## Releases und der Weg nach vorn

| Frage | Hintergrund in den Quellen |
|---|---|
| Wann erscheint 6.0.15? | Es ist kein Termin veröffentlicht ([Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)). Jede Antwort ist eine Aussage des Sprechers, keine Tatsache |
| Wie entscheiden Sie, wann eine Linie reif für einen Tag ist? | **Offen** |
| Wird es ein Exponential 7 geben? | **Offen**; keine Quelle erwähnt eines |
| Warum zählen Versionsnummern in der letzten Stelle weiter? | Als Regel dokumentiert ([Wie Versionsnummern funktionieren](wie-versionsnummern-funktionieren.md)); die Begründung ist teilweise **offen** |

## Produkt und Technik

| Frage | Hintergrund in den Quellen |
|---|---|
| Für wen ist Exponential heute gedacht? | [Wer ist die Exponential Foundation?, Welches Produkt zu Ihnen passt](../wer-ist-die-exponential-foundation.md#welches-produkt-zu-ihnen-passt) |
| Warum einen Webserver (Velocity) in PHP bauen? | Was er tut, ist dokumentiert ([Über Velocity-Releases berichten](../../../eng-us/help/press/covering-velocity-releases.md)); die Entscheidung ist **offen** |
| Warum schreibt der Schreibassistent nur Entwürfe? | Das Konzept ist dokumentiert ([Gesprächspunkte: Exponential Intelligence](../fuersprecher/gespraechspunkte-exponential-intelligence.md)); die Begründung ist **offen** |
| Warum sechs Datenbanken, darunter SQLite und MongoDB? | [Unterstützte Datenbanken](../datenbankunterstuetzung.md); die Beweggründe sind **offen** |

## Sicherheit und Vertrauen

| Frage | Hintergrund in den Quellen |
|---|---|
| Wie werden Sicherheitsmeldungen behandelt? | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md): vertrauliche Meldungen, erste Antwort „innerhalb eines Werktags“ |
| Veröffentlichen Sie Sicherheitshinweise oder CVE-Kennungen? | **Offen**; SECURITY.md erwähnt sie nicht |
| Was haben die Härtungsrunden 2026 gefunden? | [Changelog 6.0.13](../../../changelogs/6.0/6.0.13.md), [August 2026](../../../eng-us/history/2026/2026-08.md) |

## Community, Finanzierung und Open Source

| Frage | Hintergrund in den Quellen |
|---|---|
| Wie wird das Projekt finanziert? | „finanziert durch Spenden von Zeit und Ressourcen durch 7x (und Exponential Sponsors)“ ([Die Foundation und 7x](../wer-ist-die-exponential-foundation.md#die-foundation-und-7x)); die Beträge sind **offen** |
| Warum jeden Pull Request übernehmen? | Als Regel dokumentiert ([Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst)); die Begründung ist **offen** |
| Wie viele Menschen nutzen Exponential? | **Offen**; keine Quelle zählt Installationen oder Anwender ([Behauptungen, die Sie nicht aufstellen dürfen](../fuersprecher/aussagen-die-sie-nicht-machen-duerfen.md#zahlen)) |
| Was verdanken Sie Netgen, Mugo Web und CJW Network? | [Mitwirkende und Freunde des Projekts](../wer-ist-7x.md#mitwirkende-und-freunde-des-projekts) |

## Fragen, die Sorgfalt brauchen

- **Zu anderen Unternehmen.** Fragen Sie nach den eigenen Entscheidungen von Exponential, nicht nach Meinungen über
  eZ Systems oder Ibexa; Exponential „ist nicht verbunden mit“ ihnen ([Wer ist 7x?](../wer-ist-7x.md#kurz-gesagt)).
- **Zu Kunden.** Kunden dürfen nur mit ihrer Einwilligung genannt werden; drängen Sie nicht auf Namen.
- **Zu Einzelpersonen.** Fragen Sie nach Rollen, nicht nach dem Privatleben; drucken Sie persönliche Angaben nur mit
  Einwilligung.
- **Antworten mit Zahlen.** Fragen Sie „Wo können die Leser das nachprüfen?“ und drucken Sie die Quelle zusammen mit
  der Zahl ([Zahlen überprüfen](zahlen-pruefen.md)).

## Verwandte Seiten

- [Ein Briefing vorbereiten](ein-briefing-vorbereiten.md)
- [Ein Interview anfragen](ein-interview-anfragen.md)
- [Aussagen zuordnen](aussagen-zuordnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
