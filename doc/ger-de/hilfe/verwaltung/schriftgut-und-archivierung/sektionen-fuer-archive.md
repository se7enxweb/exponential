---
title: Sektionen für Archive
audience: government, admin
category: public-sector
order: 80
summary: Für Administratoren - wie eine Sektion in Exponential archivierte Inhalte aufnehmen kann, sodass Rollen entscheiden, wer sie liest (öffentliches Archiv, Archiv nur für Mitarbeitende), wie Inhalte in eine Sektion gelangen, was Verschieben mit Sektionen macht und welche Regeln für das Entfernen von Sektionen gelten.
keywords: Sektionen, Archivsektion, section/assign, Inhalt zuweisen, Sektionsbeschränkung, Rolle Anonymous, internes Archiv, Navigationsbereich, Inhaltsbaum, Schriftgut, öffentlicher Sektor, sections, archive section, Assign content, Section limitation, Anonymous role, staff-only archive, navigation part, content tree, records, public sector
type: help
---
# Sektionen für Archive

Diese Seite richtet sich an Administratoren. Eine **Sektion** ist eine benannte Gruppe von Objekten; jedes Objekt gehört
zu genau einer. Rollen lassen sich auf Sektionen beschränken, was eine Sektion zu einem einfachen Mittel macht, um
archivierten Inhalten eigene Leseregeln zu geben ([Sektionen](../../../anleitungen/sektionen.md)).

## Inhalt

1. [Wann eine Sektion hilft](#wann-eine-sektion-hilft)
2. [Eine Archivsektion einrichten](#eine-archivsektion-einrichten)
3. [Inhalte hineinbringen](#inhalte-hineinbringen)
4. [Fallstricke](#fallstricke)
5. [Verwandte Seiten](#verwandte-seiten)

## Wann eine Sektion hilft

| Bedarf | Ansatz mit Sektion |
|---|---|
| Archivierte Inhalte nur für Mitarbeitende lesbar | eine Sektion „Archiv (intern)“; die Rolle Anonymous hat keine Leserichtlinie dafür |
| Archivierte Inhalte öffentlich, aber nur von der Schriftgutverwaltung bearbeitbar | eine Sektion „Archiv“, lesbar für Anonymous; `content/edit` der Redaktion auf andere Sektionen beschränkt |
| Andere Templates für archivierte Seiten (ein Banner „archivierter Inhalt“) | Template-Overrides über `Match[section_identifier]` |

Die Standardinstallation hat fünf Sektionen: Standard (1), Benutzer (2), Medien (3), Setup (4), Design (5). Die Rolle
Anonymous darf nur Standard lesen, sodass Inhalte in einer neuen Sektion für Besucher verborgen sind, bis eine
Richtlinie sie erlaubt.

## Eine Archivsektion einrichten

1. **Setup > Sektionen** (`/section/list`), **Neue Sektion**: Name, Bezeichner (zum Beispiel `archive`),
   Navigationsbereich.
2. Rollen: Fügen Sie Richtlinien mit einer Sektionsbeschränkung hinzu oder beschränken Sie sie (Benutzer > Rollen und
   Richtlinien): zum Beispiel `content/read` für Anonymous einschließlich `archive`, wenn es öffentlich sein soll.
3. Templates: Überschreiben Sie die Vollansicht für `Match[section_identifier]=archive`, um einen Hinweis „archiviert“
   anzuzeigen ([Sektionen: Templates und Designs nach Sektion](../../../anleitungen/sektionen.md#11-templates-und-designs-nach-sektion)).

## Inhalte hineinbringen

- **Inhalt zuweisen** (Seite der Sektion oder `section/assign`): Der gewählte Eintrag und alles darunter erhalten die
  Sektion.
- **Verschieben** eines Teilbaums überträgt die Sektion des neuen übergeordneten Knotens auf Objekte, die noch die
  Sektion des alten übergeordneten Knotens hatten; wenn Sie also Bekanntmachungen in einen Archivordner mit der Sektion
  `archive` verschieben, kommen sie in diese Sektion.
- Neue Inhalte erhalten die Sektion ihres übergeordneten Knotens.

Änderungen an Sektionen werden im Audit aufgezeichnet (`content.node.section`, `content.section.*`).

## Fallstricke

- Eine Sektion ist kein Ort: Die URL bleibt dort, wo der Inhalt im Baum steht.
- Eine Sektion kann nur entfernt werden, wenn nichts sie verwendet (Objekte, Richtlinien, Rollenzuweisungen); das
  Entfernen lässt sich nicht rückgängig machen.
- Prüfen Sie jede Rolle, nachdem Sie Sektionsbeschränkungen geändert haben; Richtlinien mehrerer Rollen addieren sich.

## Verwandte Seiten

- [Sektionen](../../../anleitungen/sektionen.md)
- [Alte Bekanntmachungen archivieren](alte-bekanntmachungen-archivieren.md)
- [Rechte der Besucher](../../../../eng-us/help/creators/website-creators/visitor-permissions.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
