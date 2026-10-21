---
title: Sektionen und Rollen für Intranet-Inhalte
audience: government, admin
category: public-sector
order: 40
summary: Wie interne Inhalte in Exponential intern bleiben - eine Sektion für Intranet-Inhalte, die die Rolle Anonymous nicht lesen kann, eine Rolle für Mitarbeiter, die es kann, Abteilungssektionen für eingeschränktes Material, und wie Sie von beiden Seiten testen.
keywords: Intranet-Sektion, Sektionen, Rollen, Rolle Anonymous, Rolle für Mitarbeiter, Einschränkung auf Sektion, interne Inhalte, Vertraulichkeit, Zugriffstest, intranet section, sections, roles, Anonymous role, Staff role, content/read, section limitation, internal content, confidentiality, access test
type: help
---
# Sektionen und Rollen für Intranet-Inhalte

Diese Seite richtet sich an Administratoren. Jedes Objekt gehört zu genau einer Sektion, und Richtlinien können auf
Sektionen eingeschränkt werden; die Rolle Anonymous darf in einer neuen Installation nur Standard lesen, sodass Inhalte
in einer neuen Sektion vor Besuchern verborgen sind, bis eine Richtlinie sie erlaubt
([Sektionen](../../../../anleitungen/sektionen.md)).

## Inhalt

1. [Sektionen](#sektionen)
2. [Rollen](#rollen)
3. [Eingeschränktes Material](#eingeschränktes-material)
4. [Von beiden Seiten testen](#von-beiden-seiten-testen)

## Sektionen

1. **Setup > Sektionen**: Legen Sie „Intranet“ an (Bezeichner `intranet`).
2. Legen Sie den Intranet-Ordner an und **Inhalt zuweisen**: Der Ordner und alles darunter erhalten die Sektion. Neue
   Inhalte übernehmen die Sektion ihres übergeordneten Elements.
3. Prüfen Sie, dass die Rolle Anonymous `intranet` nicht liest
   ([Berechtigungen für Besucher](../../../ersteller/website-ersteller/rechte-fuer-besucher.md)).

## Rollen

| Rolle | Richtlinien |
|---|---|
| Mitarbeiter (alle Mitarbeiter) | `content/read` eingeschränkt auf die Sektionen Intranet und Standard; `user/login` für den Intranet-Siteaccess |
| Intranet-Redakteure | `content/create`, `content/edit` eingeschränkt auf die Sektion Intranet (oder einen Teilbaum) |

Weisen Sie die Rolle für Mitarbeiter der Benutzergruppe der Mitarbeiter zu (oder den Gruppen, die LDAP anlegt; siehe
„Anmeldung über LDAP“).

## Eingeschränktes Material

Für Material, das nur einige Mitarbeiter lesen dürfen (Personal, Recht), verwenden Sie eine weitere Sektion (zum
Beispiel „Intranet eingeschränkt“), die nur eine Gruppe liest. Denken Sie daran, dass eine Person mit einer Rolle, die
`content/read` auf allen Sektionen erlaubt, alles liest; halten Sie solche Rollen gering.

## Von beiden Seiten testen

- Abgemeldet auf der öffentlichen Site: Die Adresse einer Intranet-Seite darf sich nicht öffnen, und Intranet-Inhalte
  dürfen nicht in Suche oder Listen erscheinen.
- Angemeldet mit einem Testkonto für Mitarbeiter (nicht dem einer echten Person): Intranet-Seiten öffnen sich,
  eingeschränkte Seiten nicht.
- Verweigerte Aufrufe werden im Kanal `access` des Audit-Protokolls als `access.permission.refused` festgehalten
  ([Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md)).

## Verwandte Seiten

- [Sektionen und Objektzustände für Site-Ersteller](../../../ersteller/website-ersteller/sektionen-und-objektzustaende.md)
- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
