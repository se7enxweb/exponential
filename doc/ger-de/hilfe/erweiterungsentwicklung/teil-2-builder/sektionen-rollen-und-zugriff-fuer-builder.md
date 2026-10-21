---
title: Sektionen, Rollen und Zugriff für Builder
audience: developer, designer, app-creator, admin
category: extension-development
order: 180
summary: Wer was sehen und tun darf - Sektionen, Objektstatus, Rollen, Richtlinien, Einschränkungen und Zuweisungen - für Builder erklärt, mit einem Mitgliederbereich als ausgearbeitetem Plan und den Template-Operatoren, die nach Zugriff fragen.
keywords: Sektionen, Rollen, Richtlinien, Einschränkungen, Zuweisungen, Zugriff, Anonym, Mitgliederbereich, Objektstatus, Berechtigungen, sections, roles, policies, limitations, assignments, access, anonymous, members area, object states, content/read, can_edit, has_access_to, permissions
type: help
---
# Sektionen, Rollen und Zugriff für Builder

Teil 2, Kapitel 18. Zugriff wird in Exponential konfiguriert, nicht programmiert: **Rollen** enthalten **Richtlinien**,
Richtlinien können **eingeschränkt** werden (nach Sektion, Klasse, Unterbaum, Status, Sprache, Siteaccess), und Rollen
werden Benutzern und Gruppen **zugewiesen**. Ein Builder kann allein mit diesen Werkzeugen einen Bereich nur für
Mitglieder, einen Redaktions-Workflow oder ein privates Intranet anlegen. Die Referenzseiten sind
[Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) und [Sektionen](../../../anleitungen/sektionen.md).

## Die Bausteine

| Baustein | Was er ist |
|---|---|
| Sektion | eine Kennzeichnung, die jedes Objekt trägt (in einer neuen Installation Standard, Users, Media, Setup, Design); dient zum Einschränken von Richtlinien und zur Auswahl von Designs |
| Objektstatus | eine zweite, unabhängige Kennzeichnung aus einer Statusgruppe (zum Beispiel gesperrt oder nicht gesperrt) |
| Richtlinie | die Erlaubnis, ein Modul oder eine seiner Funktionen zu verwenden (`content/read`, `content/edit`, `user/login`) |
| Einschränkung | grenzt eine Richtlinie ein: nur Sektion Standard, nur Klasse Article, nur unterhalb eines Knotens, nur in einem Status |
| Rolle | eine benannte Menge von Richtlinien |
| Zuweisung | eine Rolle, die einem Benutzer oder einer Benutzergruppe gegeben wird, optional auf eine Sektion oder einen Unterbaum beschränkt |

Ein Benutzer hat jede Richtlinie jeder Rolle, die ihm oder einer seiner Gruppen zugewiesen ist. Richtlinien erweitern
nur; ein „Verbieten“ gibt es nicht.

## Warum Besucher nicht alles sehen

Die Standardrolle Anonymous hat `content/read`, eingeschränkt auf die **Sektion Standard**. Alles in anderen Sektionen
(Benutzerkonten, Medienordner, Setup-Objekte) ist für Besucher, die nicht angemeldet sind, unsichtbar. Diese eine
Einschränkung ist das Vorbild für jeden privaten Bereich, den Sie bauen.

## Ausgearbeiteter Plan: ein Bereich nur für Mitglieder

1. **Legen Sie eine Sektion an** `members` (Setup > Sektionen): Name „Members“, Bezeichner `members`.
2. **Weisen Sie die Sektion** dem Unterbaum des Mitgliederbereichs zu (auf der Seite der Sektion einem Knoten
   zuweisen; die Objekte darunter erhalten die Sektion).
3. **Legen Sie eine Rolle an** „Members read“ mit einer Richtlinie: Modul `content`, Funktion `read`, Einschränkung
   Sektion = Members.
4. **Weisen Sie die Rolle** der Benutzergruppe „Members“ zu.
5. Prüfen Sie: Abgemeldet antworten die Seiten des Bereichs mit „Zugriff verweigert“; als Mitglied angemeldet werden
   sie angezeigt.
6. Optional: Geben Sie dem Bereich mit Overrides, die `Match[section_identifier]=members` prüfen, ein eigenes Aussehen.

Es wurde kein Code geschrieben, und der Bereich ist sicher, weil Fetches und Ansichten dieselben Richtlinien anwenden:
Eine Liste der Artikel des Bereichs ist für anonyme Besucher leer.

## In Templates nach Zugriff fragen

| Template | Beantwortet |
|---|---|
| `$node.can_edit`, `$node.can_create`, `$node.can_remove`, `$node.can_read` | ob der aktuelle Benutzer dies mit diesem Knoten tun darf |
| `fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'edit' ) )` | ob der aktuelle Benutzer diese Funktion irgendwo verwenden darf |
| `fetch( 'user', 'current_user' )` | den aktuellen Benutzer, mit `.is_logged_in` |

```text
{if $node.can_edit}
    <a href={concat( '/content/edit/', $node.contentobject_id )|ezurl}>Edit</a>
{/if}
```

Einen Link zu verbergen ist eine Höflichkeit; geschützt wird durch die Richtlinie. Verlassen Sie sich für die Sicherheit
nie allein auf eine Bedingung im Template. Die in 6.0 hinzugekommenen Operatoren für Rollen und Richtlinien sind in
[Template-Operatoren für Rollen und Richtlinien](../../../funktionen/6.0/template-operatoren-fuer-rollen-und-richtlinien.md) beschrieben.

## Den Zugriff eines Benutzers prüfen

```bash
./console exp:accesscheck --help
```

Der Befehl beantwortet, was ein Benutzer mit einem Knoten oder Objekt tun darf und welche Einschränkung es verweigert;
das ist der schnellste Weg, ein „Zugriff verweigert“ zu verstehen.

## Regeln

- Geben Sie Rollen an **Gruppen**, nicht an einzelne Benutzer.
- Eine Richtlinie auf jedes Modul (`*`) nur für Administratoren; die Rollenliste markiert solche Rollen rot.
- Eine Richtlinie auf das Modul `role` erlaubt ihren Benutzern, sich selbst alles zu geben: Behandeln Sie sie wie
  Administratorzugriff.
- Neue Module von Erweiterungen deklarieren eigene Richtlinienfunktionen; vergeben Sie sie bewusst
  ([Richtlinienfunktionen und Zugriffsprüfungen](../teil-3-erweiterungsentwickler/richtlinienfunktionen-und-zugriff.md)).

## Verwandte Seiten

- Zurück: [Inhaltsklassen für Builder](inhaltsklassen-fuer-builder.md)
- Weiter: [Workflows für Builder](workflows-fuer-builder.md)
- [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md), [Sektionen](../../../anleitungen/sektionen.md), [Objektstatus](../../../anleitungen/objektzustaende.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
