---
title: Mitgliederwebsites
audience: businesses, project-manager, website-creator
category: business-solutions
order: 1
summary: Die Einstiegsseite für Organisationen, die eine Mitgliederwebsite auf Exponential betreiben - Vereine, Berufsverbände, Communitys und Kundenclubs mit Registrierung, Inhalten nur für Mitglieder, Newslettern und Veranstaltungen - mit jeder Seite des Bereichs, einer empfohlenen Reihenfolge und den benachbarten Bereichen.
keywords: Mitgliederwebsite, Mitgliederbereich, Mitgliedschaft, Club, Verein, Community, Registrierung, Inhalte nur für Mitglieder, Newsletter, Veranstaltungen, Kalender, Foren, member site, members area, membership, club, association, community, registration, members-only content, newsletter, events, calendar, forums
type: help
---
# Mitgliederwebsites

Dieser Bereich ist für Organisationen, deren Website Mitglieder hat: Berufsverbände, Vereine, Alumni-Netzwerke,
Kundenclubs und Communitys. Mitglieder registrieren sich, melden sich an, lesen, was nur Mitglieder lesen dürfen,
bekommen Newsletter und kommen zu Veranstaltungen. Der Bereich ist für die Menschen geschrieben, die die Organisation
führen, und verlinkt die technischen Seiten für die Person, die die Website baut.

## Inhalt

1. [Was eine Mitgliederwebsite auf Exponential ist](#was-eine-mitgliederwebsite-auf-exponential-ist)
2. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
3. [Eine empfohlene Reihenfolge](#eine-empfohlene-reihenfolge)
4. [Mitgliederwebsites im Vergleich mit Intranets und Extranets](#mitgliederwebsites-im-vergleich-mit-intranets-und-extranets)
5. [Benachbarte Bereiche](#benachbarte-bereiche)
6. [Verwandte Seiten](#verwandte-seiten)

## Was eine Mitgliederwebsite auf Exponential ist

Eine Mitgliederwebsite ist eine öffentliche Website mit einem Mitgliederbereich. Die Bausteine kommen aus dem Kernel und
aus Erweiterungen, die in `composer.json` aufgeführt sind:

| Bedarf | Funktion | Quelle |
|---|---|---|
| Menschen registrieren sich und bestätigen ihre Adresse | `user/register`, E-Mail-Bestätigung | [Mitgliederregistrierung und Bereiche nur für Mitglieder](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md) |
| Seiten, die nur Mitglieder lesen dürfen | eine Mitgliedersektion, die die Rolle Anonymous nicht liest | [Sektionen, 13.1](../../../anleitungen/sektionen.md#131-ein-mitgliederbereich) |
| Newsletter mit Double-Opt-in | `cjw_newsletter` | [cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md) |
| Mail, wenn neue Inhalte erscheinen | Benachrichtigungen | [Benachrichtigungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/notifications.md) |
| Jede Person wählt, welche Mails sie bekommt | E-Mail-Einstellungen | [E-Mail-Einstellungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/mail-preferences.md) |
| Veranstaltungen und Kalender | die Klassen `event`, `event_calendar` und `multicalendar` von `ezwebin` | [Veranstaltungen und Kalender](veranstaltungen-und-kalender.md) |
| Diskussionen | die Forenklassen von `ezwebin` | [ezwebin](../../../../eng-us/features/6.0/extensions/ezwebin.md) |

> **Hinweis**
> Dieser Bereich beschreibt, was das Repository und die Demonstrationsinstallation enthalten. Er nennt keine
> Mitgliederorganisationen und zitiert keine Statistiken. Wo etwas nicht eingebaut ist, etwa wiederkehrende
> Mitgliedsbeiträge oder Tickets für Veranstaltungen, sagt die Seite das.

## Die Seiten dieses Bereichs

| Seite | Was sie beantwortet |
|---|---|
| [Mitgliederwebsite planen](mitgliederwebsite-planen.md) | Was zuerst zu entscheiden ist: Mitgliedschaftsstufen, was öffentlich ist, wer die Website betreibt |
| [Registrierung von Mitgliedern](registrierung-von-mitgliedern.md) | Wie Menschen beitreten, bestätigen und sich anmelden, und wie die Organisation sie freigibt |
| [Inhalte nur für Mitglieder](inhalte-nur-fuer-mitglieder.md) | Bereiche, Stufen und Downloads, die nur Mitglieder öffnen können |
| [Kommunikation mit Mitgliedern](kommunikation-mit-mitgliedern.md) | Newsletter, Benachrichtigungen und E-Mail-Einstellungen |
| [Veranstaltungen und Kalender](veranstaltungen-und-kalender.md) | Veranstaltungsseiten, Kalender und Anmeldeformulare, und was nicht eingebaut ist |
| [Checkliste für Mitgliederwebsites](checkliste-fuer-mitgliederwebsites.md) | Alles, was abzuhaken ist, bevor Mitglieder eingeladen werden |
| [FAQ zu Mitgliederwebsites](faq-zu-mitgliederwebsites.md) | Kurze Antworten auf häufige Fragen |

## Eine empfohlene Reihenfolge

1. [Mitgliederwebsite planen](mitgliederwebsite-planen.md) mit dem Vorstand oder der Leitung der Organisation.
2. [Registrierung von Mitgliedern](registrierung-von-mitgliedern.md) und [Inhalte nur für Mitglieder](inhalte-nur-fuer-mitglieder.md),
   um festzulegen, wie die Website offen und geschlossen ist.
3. [Kommunikation mit Mitgliedern](kommunikation-mit-mitgliedern.md) und [Veranstaltungen und Kalender](veranstaltungen-und-kalender.md)
   für die Aktivitäten, für die Mitglieder kommen.
4. [Checkliste für Mitgliederwebsites](checkliste-fuer-mitgliederwebsites.md) vor dem Start.
5. Halten Sie die [FAQ zu Mitgliederwebsites](faq-zu-mitgliederwebsites.md) für Fragen von Mitgliedern und Ehrenamtlichen bereit.

## Mitgliederwebsites im Vergleich mit Intranets und Extranets

| | Mitgliederwebsite | Intranet | Extranet |
|---|---|---|---|
| Wer sich anmeldet | Menschen aus der Öffentlichkeit, die beitreten | Mitarbeiter | Menschen von Partnerorganisationen |
| Öffentlicher Teil | groß | keiner | klein oder keiner |
| Wie Konten entstehen | offene Registrierung, manchmal mit Freigabe | Verzeichnis oder Administratoren | Registrierung mit Freigabe oder von Mitarbeitern angelegt |
| Typische Extras | Newsletter, Veranstaltungen, Foren | Dokumente, Freigabe | Dateien, Bestellen |

## Benachbarte Bereiche

- [Vereine und kleine Gruppen](../vereine/README.md): eine Website mit Ehrenamtlichen betreiben.
- [Gemeinnützige Organisationen](../gemeinnuetzige/README.md): Spenden, Ehrenamtliche und Kampagnen.
- [Newsletter](../newsletter/README.md): das Newsletter-System im Detail.
- [Extranets](../extranets/README.md) und [Intranets](../intranets/README.md): dieselben Werkzeuge für Partner und
  Mitarbeiter.
- [Sicherheit](../sicherheit/README.md): DSGVO-Grundlagen, Einwilligung und Konten.
- Öffentliche Stellen: [Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md).

## Verwandte Seiten

- [Unternehmen: hier beginnen](../unternehmen-hier-beginnen.md)
- [Welcher Bereich zu Ihrer Organisation passt](../welcher-bereich-zu-ihrer-organisation-passt.md)
- [Was Besucher sehen dürfen: die Rolle Anonymous](../../../../eng-us/help/creators/website-creators/visitor-permissions.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
