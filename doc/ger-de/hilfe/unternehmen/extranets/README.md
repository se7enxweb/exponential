---
title: Extranets und Partnerportale
audience: businesses, project-manager, partner
category: business-solutions
order: 1
summary: Die Einstiegsseite für Organisationen, die Partnern, Kunden, Händlern oder Lieferanten einen privaten Bereich auf Exponential geben - was der Bereich abdeckt, für wen er ist, jede Seite mit einer Zeile, eine empfohlene Reihenfolge und die benachbarten Bereiche.
keywords: Extranet, Partnerportal, Kundenportal, Händlerportal, Lieferantenportal, B2B, Registrierung, Freigabe, Siteaccess, eigener Host, Sektionen, Objektzustände, Dateien teilen, Berechtigungsprüfung, extranet, partner portal, customer portal, dealer portal, supplier portal, registration, approval, separate host, sections, object states, file sharing, permissions review
type: help
---
# Extranets und Partnerportale

Dieser Bereich ist für Organisationen, die Informationen mit Menschen außerhalb des Unternehmens teilen: Wiederverkäufer,
die Preislisten brauchen, Kunden, die ihre Dokumente herunterladen, Händler, die bestellen, Lieferanten, die
Zertifikate hochladen. Er ist für die Führungskräfte geschrieben, die diese Beziehungen verantworten, und für die
Personen, die das Portal bauen und betreiben. Jede Seite erklärt, was Exponential für die Aufgabe bietet, und verlinkt
die technischen Anleitungen.

## Inhalt

1. [Was ein Extranet auf Exponential ist](#was-ein-extranet-auf-exponential-ist)
2. [Wie es sich von einem Intranet unterscheidet](#wie-es-sich-von-einem-intranet-unterscheidet)
3. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
4. [Eine empfohlene Reihenfolge](#eine-empfohlene-reihenfolge)
5. [Benachbarte Bereiche](#benachbarte-bereiche)
6. [Verwandte Seiten](#verwandte-seiten)

## Was ein Extranet auf Exponential ist

Ein Extranet ist eine Exponential-Website, meist ein Siteaccess unter einem eigenen Hostnamen, auf der sich externe
Benutzer anmelden und sehen, was ihrer Organisation zusteht. Alles, was es braucht, steckt im Kernel:

| Bedarf | Funktion von Exponential | Quelle |
|---|---|---|
| Partner registrieren sich, das Unternehmen gibt frei | Registrierung mit E-Mail-Bestätigung, die Mail „Neuer Benutzer registriert“ mit signierten Links zum Freigeben und Ablehnen, ein Freigabe-Workflow | [Signierte Links in der Mail „New user registered“](../../../../eng-us/features/6.0/registration-admin-links.md) |
| Eine eigene Adresse wie `partners.example.com` | Siteaccess-Zuordnung über den Host | [Siteaccess über das Host-Präfix](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md) |
| Jeder Partner sieht nur seinen eigenen Bereich | Sektionen, Teilbaum-Einschränkungen, Objektzustände | [Sektionen](../../../anleitungen/sektionen.md), [Objektzustände](../../../anleitungen/objektzustaende.md) |
| Partner laden Dokumente sicher herunter | `content/download` prüft die Berechtigungen | [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md) |
| Partnergruppen bekommen eigene Preise | Ermäßigungsgruppen des Shops pro Benutzergruppe | [Produkte, Preise und USt](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md#discounts) |
| Regelmäßige Prüfung, wer Zugriff hat | Rollenseiten, Benutzerkonten prüfen, der Audit-Trail | [Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md), [Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) |

> **Hinweis**
> Dieser Bereich beschreibt, was das Repository enthält. Er nennt keine Kunden und zitiert keine Statistiken.

## Wie es sich von einem Intranet unterscheidet

| | Intranet | Extranet |
|---|---|---|
| Benutzer | Ihre Mitarbeiter | Menschen anderer Organisationen |
| Woher die Konten kommen | aus Ihrem Verzeichnis oder von Administratoren | Registrierung mit Freigabe oder von Ihren Mitarbeitern angelegt |
| Hauptrisiko | die falsche Abteilung sieht eine Seite | ein Partner sieht die Daten eines anderen Partners |
| Typische Struktur | Bereiche pro Abteilung | Bereiche pro Partner, Stufe oder Region |
| Ausscheiden | Mitarbeiter gehen über die Personalabteilung | Partnerverträge enden; Konten müssen geschlossen werden |

Die Werkzeuge sind dieselben, daher lohnt sich auch der Bereich [Intranets](../intranets/README.md).

## Die Seiten dieses Bereichs

| Seite | Was sie beantwortet |
|---|---|
| [Partnerportal planen](partnerportal-planen.md) | Was zuerst zu entscheiden ist: Zielgruppen, Stufen, was geteilt wird, wer freigibt |
| [Registrierung und Kontofreigabe](registrierung-und-kontofreigabe.md) | Wie externe Benutzer Konten bekommen und wie das Unternehmen sie freigibt oder ablehnt |
| [Kunden- und Partnerbereiche](kunden-und-partnerbereiche.md) | Jedem Partner oder jeder Stufe mit Sektionen, Teilbäumen und Zuständen einen eigenen Bereich geben |
| [Dateien mit Partnern teilen](dateien-mit-partnern-teilen.md) | Dokumente, Preislisten und Uploads, mit Downloads, die Berechtigungen prüfen |
| [Getrennte Hosts und Siteaccesses](getrennte-hosts-und-siteaccesses.md) | Ein Portal unter eigener Adresse neben der öffentlichen Website, mit eigenen Anmelderegeln |
| [Händler- und Lieferantenportale](haendler-und-lieferantenportale.md) | Bestellen mit Gruppenpreisen, Produktinformationen, Formulare für Lieferanten |
| [Berechtigungen im Extranet prüfen](berechtigungen-im-extranet-pruefen.md) | Eine Routine, um zu prüfen, wer was sehen kann, und zu entfernen, was nicht mehr gebraucht wird |
| [Checkliste für den Extranet-Start](checkliste-fuer-den-extranet-start.md) | Alles, was abzuhaken ist, bevor der erste Partner eingeladen wird |
| [Extranet-FAQ](extranet-faq.md) | Kurze Antworten auf häufige Fragen |

## Eine empfohlene Reihenfolge

1. [Partnerportal planen](partnerportal-planen.md) mit den Personen, die die Partnerbeziehungen verantworten.
2. [Getrennte Hosts und Siteaccesses](getrennte-hosts-und-siteaccesses.md), um Adresse und Form festzulegen.
3. [Kunden- und Partnerbereiche](kunden-und-partnerbereiche.md), um die Struktur festzulegen.
4. [Registrierung und Kontofreigabe](registrierung-und-kontofreigabe.md), um festzulegen, wie Konten entstehen.
5. [Dateien mit Partnern teilen](dateien-mit-partnern-teilen.md) und, für Handelsportale,
   [Händler- und Lieferantenportale](haendler-und-lieferantenportale.md).
6. [Checkliste für den Extranet-Start](checkliste-fuer-den-extranet-start.md), dann
   [Berechtigungen im Extranet prüfen](berechtigungen-im-extranet-pruefen.md) als wiederkehrende Routine.

## Benachbarte Bereiche

- [Intranets](../intranets/README.md): dieselben Werkzeuge für Mitarbeiter.
- [Mitgliederwebsites](../mitglieder/README.md): offene Registrierung für die Öffentlichkeit.
- [Shops](../shops/README.md): der eingebaute Shop für den Online-Verkauf.
- [Sicherheit](../../../../eng-us/help/businesses/security/README.md): Zugriffsprüfungen, DSGVO-Grundlagen, Konten und Passwörter.
- [Multisite](../../../../eng-us/help/businesses/multisite/README.md): mehrere Websites in einer Installation.
- Öffentliche Stellen: [Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md).

## Verwandte Seiten

- [Unternehmen: hier beginnen](../unternehmen-hier-beginnen.md)
- [Wie Unternehmen online verkaufen](../wie-unternehmen-online-verkaufen.md)
- [Mitgliederregistrierung und Bereiche nur für Mitglieder](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
