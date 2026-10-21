---
title: Extranets für Auftragnehmer und Partner
audience: government, project-manager, admin
category: public-sector
order: 20
summary: Wie eine Behörde Auftragnehmern, Partnerorganisationen oder anderen Behörden mit Exponential Zugang zu ausgewählten Inhalten gibt - getrennte Benutzergruppen und Sektionen, ein Extranet-Siteaccess oder -Bereich, geschützte Dateien, und die Lücken (kein Ablaufdatum für Konten, keine Föderation externer Identitäten) mit dem, was stattdessen zu tun ist.
keywords: Extranet, Auftragnehmer, Partner, externe Benutzer, Benutzergruppen, Sektionen, geschützter Bereich, Ablauf von Konten, Offboarding, Zusammenarbeit, extranet, contractors, partners, external users, user groups, sections, protected area, account expiry, offboarding, collaboration
type: help
---
# Extranets für Auftragnehmer und Partner

Diese Seite richtet sich an Führungskräfte und Administratoren, die Informationen mit Menschen außerhalb der
Organisation teilen: Auftragnehmern in einem Projekt, Partnerorganisationen, anderen Behörden. In Exponential ist ein
Extranet ein geschützter Bereich oder Siteaccess, eine Benutzergruppe je externer Partei und Sektionen, die nur diese
Gruppen lesen dürfen.

## Inhalt

1. [Die Bausteine](#die-bausteine)
2. [Bereich oder Siteaccess](#bereich-oder-siteaccess)
3. [Was fehlt und was zu tun ist](#was-fehlt-und-was-zu-tun-ist)

## Die Bausteine

| Bedarf | Exponential | Seite |
|---|---|---|
| Parteien trennen | eine Benutzergruppe je Auftragnehmer oder Partner | „Benutzergruppen und Sektionen für Extranets“ |
| Inhalte, die nur sie sehen | eine Sektion je Partei, nur von ihrer Gruppe lesbar | dieselbe |
| Konten | von einem Administrator angelegt, nie durch Selbstregistrierung | „Einem Auftragnehmer Zugang geben“ |
| Dokumente | Dateien in den geschützten Sektionen, über `content/download` heruntergeladen | „Dateien mit Auftragnehmern teilen“ |
| Ende des Zugangs | deaktivieren, oder deaktivieren und verschieben | „Den Zugang beenden“ |
| Wer was getan hat | das Audit-Protokoll | „Aktivitäten von Auftragnehmern und das Audit-Protokoll“ |

## Bereich oder Siteaccess

| Möglichkeit | Wann |
|---|---|
| Ein geschützter Bereich auf der öffentlichen Site (Ordner nur für Mitglieder) | einige Dokumente für einen Partner |
| Ein eigener Extranet-Siteaccess mit `RequireUserLogin=true` | viele Parteien, eigenes Design und eigene Adresse |

Beide werden wie Intranets eingerichtet (siehe den [Bereich Intranets](../intranets/README.md)).

## Was fehlt und was zu tun ist

- **Kein Ablaufdatum für Konten.** Exponential hat kein Feld, das ein Konto an einem Datum beendet. Halten Sie das
  Enddatum in Ihrem Vertragsregister fest und deaktivieren Sie Konten rechtzeitig; **Benutzer > Benutzerkonten prüfen**
  findet Konten, die seit N Tagen nicht gesehen wurden (siehe „Verwaiste Konten überprüfen“).
- **Keine Föderation mit den Identitätssystemen von Partnern** über die verfügbaren Login-Handler hinaus (siehe
  „Anmeldeoptionen für Mitarbeiter“ im Bereich Intranets).
- **Standardmäßig kein Zugriffsprotokoll je Dokument**: Downloads werden nur im Audit-Protokoll aufgezeichnet, wenn
  Lesezugriffe eingeschaltet sind.

## Verwandte Seiten

- [Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)
- [Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
