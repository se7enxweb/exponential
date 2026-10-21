---
title: Datenschutzregeln außerhalb der EU
audience: government, project-manager
category: public-sector
order: 310
summary: Für öffentliche Stellen außerhalb der EU oder mit Zielgruppen in mehreren Rechtsordnungen - wie sich die Datenschutzfunktionen von Exponential allgemein zu Regeln verhalten, nur mit den Gesetzen, die die eigenen Dokumente des Projekts nennen (CAN-SPAM, CASL, CCPA/CPRA), und ein funktionsbasierter Ansatz, der unter den meisten Regelwerken trägt.
keywords: andere Rechtsordnungen, Datenschutzgesetze, CAN-SPAM, CASL, CCPA, CPRA, Vereinigte Staaten, Kanada, Kalifornien, Datenschutz, Zuordnung von Funktionen, öffentlicher Sektor, international, other jurisdictions, privacy laws, United States, Canada, California
type: help
---
# Datenschutzregeln außerhalb der EU

Diese Seite richtet sich an Datenschutz- und Projektverantwortliche öffentlicher Stellen außerhalb der EU und von
Stellen, die Menschen in mehreren Rechtsordnungen dienen. Datenschutzregeln unterscheiden sich stark, und öffentliche
Stellen unterliegen oft eigenen Regeln. Diese Seite fasst keine ausländischen Gesetze zusammen; sie nennt nur die, die
die Dokumente des Projekts zuordnen, und zeigt einen funktionsbasierten Ansatz.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Gesetze, die das Projekt zuordnet](#gesetze-die-das-projekt-zuordnet)
2. [Ein funktionsbasierter Ansatz](#ein-funktionsbasierter-ansatz)
3. [Besonderheiten des öffentlichen Sektors](#besonderheiten-des-öffentlichen-sektors)
4. [Verwandte Seiten](#verwandte-seiten)

## Gesetze, die das Projekt zuordnet

| Gesetz | Was das Projekt dokumentiert | Wo |
|---|---|---|
| CAN-SPAM Act (Vereinigte Staaten, 15 U.S.C. 7701 ff.) | **„Erfüllt“** für E-Mails über das E-Mail-Gate: Abmeldelink und `List-Unsubscribe` werden sofort beachtet; die Postanschrift des Absenders bleibt der Website überlassen | [Datenschutzstandards](../../../../eng-us/standards/privacy.md#can-spam-act-and-one-click-unsubscribe) |
| CASL (Kanada) und CCPA/CPRA (Kalifornien) | die Funktionen der E-Mail-Einstellungen, in einer Checkliste zugeordnet | [die Rechts-Checkliste](../../../spezifikationen/6.0/mail-einstellungen-compliance.md) |

E-Mails öffentlicher Stellen sind nicht immer „kommerziell“ im Sinne solcher Gesetze; fragen Sie Ihre Rechtsberatung,
welche Regeln gelten.

## Ein funktionsbasierter Ansatz

Die meisten Datenschutzregelwerke verlangen Ähnliches. Ordnen Sie die Anforderungen Ihres Gesetzes diesen Funktionen
zu:

| Übliche Anforderung | Funktion |
|---|---|
| Information der Betroffenen | Ihre Datenschutzerklärung (siehe „Gliederung einer Datenschutzerklärung“) |
| Einwilligung oder Widerspruch bei Werbe-E-Mails | Kategorien, Double-Opt-in, Abmelden mit einem Klick, Einwilligungsprotokoll |
| Zugang zu den eigenen Daten | Export der E-Mail-Einstellungen, Audit-Export, Administrationswerkzeuge |
| Löschung | Entfernen des Kontos mit Löschung der E-Mail-Daten; `exp:mail:preferences erase` |
| Sicherheitsvorkehrungen | der Sicherheitsbereich dieser Hilfe |
| Begrenzung der Aufbewahrung | Aufbewahrungseinstellungen für Audit, Protokolle und Einwilligungen |
| Erkennung von Datenschutzverletzungen | Audit-Warnungen, Sicherheitsprotokoll |

## Besonderheiten des öffentlichen Sektors

- Archiv- und Informationsfreiheitsgesetze können verlangen, Inhalte aufzubewahren, deren Entfernung ein
  Datenschutzantrag verlangt; Ihre Rechtsberatung entscheidet, was Vorrang hat.
- Manche Rechtsordnungen beschränken, wo öffentliche Daten gehostet werden dürfen; Exponential läuft auf Ihren eigenen
  Servern oder denen Ihres Hosters, der Hosting-Standort ist also eine Frage der Beschaffung.
- Die Barrierefreiheit von Datenschutzinformationen (Erklärung, Formulare, Einwilligungsbanner) ist vielerorts Teil
  des Rechts der Barrierefreiheit.

## Verwandte Seiten

- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Die DSGVO und Exponential](dsgvo-zuordnung.md)
- [Abmelden mit einem Klick](abmelden-mit-einem-klick.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
