---
title: Benutzergruppen und Sektionen für Extranets
audience: government, admin
category: public-sector
order: 30
summary: Wie Sie ein Extranet in Exponential so strukturieren, dass jeder Auftragnehmer oder Partner nur seine eigenen Inhalte sieht - eine Benutzergruppe und eine Sektion (oder ein Teilbaum) je Partei, eine mit Einschränkung zugewiesene Rolle und Tests von jeder Seite.
keywords: Benutzergruppen, Sektionen, Aufbau eines Extranets, Einschränkung der Rollenzuweisung, Teilbaum, Auftragnehmer, Partner, Trennung, user groups, sections, extranet structure, role assignment limitation, subtree, contractors, partners, separation, content/read
type: help
---
# Benutzergruppen und Sektionen für Extranets

Diese Seite richtet sich an Administratoren. Die Trennung zwischen externen Parteien beruht auf Benutzergruppen,
Sektionen und Rollenzuweisungen ([Sektionen](../../../../anleitungen/sektionen.md),
[Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)).

## Inhalt

1. [Die Struktur](#die-struktur)
2. [Eine Rolle, mit Einschränkung zugewiesen](#eine-rolle-mit-einschränkung-zugewiesen)
3. [Gemeinsames Material für alle Parteien](#gemeinsames-material-für-alle-parteien)
4. [Tests](#tests)

## Die Struktur

```
Users
  External
    Contractor A (user group)
    Contractor B (user group)
Content
  Extranet (folder)
    Project X - Contractor A (section: Extranet A)
    Project Y - Contractor B (section: Extranet B)
    For all partners (section: Extranet shared)
```

Legen Sie die Sektionen unter **Setup > Sektionen** an und verwenden Sie **Inhalt zuweisen** für jeden Ordner.

## Eine Rolle, mit Einschränkung zugewiesen

Legen Sie eine Rolle „Extranet-Leser“ mit `content/read` und `user/login` (für den Extranet-Siteaccess) an. Weisen Sie
sie jeder Gruppe mit einer Einschränkung auf die Sektion oder den Teilbaum dieser Partei zu: Die Gruppe von
Auftragnehmer A erhält sie eingeschränkt auf die Sektion „Extranet A“ (und „Extranet shared“). Eine Rolle mit vielen
eingeschränkten Zuweisungen ist leichter zu überprüfen als eine Rolle je Partei.

Darf eine Partei hochladen, fügen Sie `content/create` für die Klasse File in ihrem eigenen Teilbaum hinzu, in einer
eigenen Rolle mit derselben Einschränkung.

## Gemeinsames Material für alle Parteien

Eine Sektion, die jede externe Gruppe liest, enthält allgemeine Informationen (Regeln, Vorlagen, Kontakte).

## Tests

Verwenden Sie in jeder Gruppe ein Testkonto: Das Konto von Auftragnehmer A öffnet Projekt X und nicht Projekt Y;
abgemeldet öffnet sich keines. Verweigerte Aufrufe werden im Audit-Protokoll als `access.permission.refused`
festgehalten.

## Verwandte Seiten

- [Einem Auftragnehmer Zugang geben](einem-auftragnehmer-zugang-geben.md)
- [Sektionen und Rollen für Intranet-Inhalte](../intranets/sektionen-und-rollen-fuer-intranet-inhalte.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
