---
title: Wer Datensätze veröffentlichen darf
audience: government, admin, project-manager
category: public-sector
order: 150
summary: Wie Sie Rollen, Sektionen und einen Freigabeschritt einrichten, damit nur das Datenteam Datensätze anlegt, eine Datenverantwortliche sie vor der Veröffentlichung freigibt und jede Veröffentlichung aufgezeichnet wird.
keywords: Rollen, Richtlinien, Datenteam, Datenverantwortliche, Data Steward, Freigabe, Workflow, Sektion, Beschränkung auf Teilbaum, content/create, Veröffentlichungsrechte, Open-Data-Governance, roles, policies, data team, data steward, approval, section, subtree limitation, publish rights, open data governance
type: help
---
# Wer Datensätze veröffentlichen darf

Diese Seite richtet sich an Administratoren und Projektleitungen, die die Veröffentlichungsrechte eines
Open-Data-Programms einrichten. Exponential steuert Rechte mit Rollen und Richtlinien, gruppiert Inhalte in Sektionen
und kann eine Version mit einem Workflow zur Freigabe zurückhalten. Die Bausteine stammen aus
[Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md), [Sektionen](../../../anleitungen/sektionen.md) und
[Workflows](../../../anleitungen/workflows.md).

## Inhalt

1. [Typische Personen und ihre Rechte](#typische-personen-und-ihre-rechte)
2. [Eine Sektion für offene Daten](#eine-sektion-für-offene-daten)
3. [Die Rolle Datenveröffentlichung](#die-rolle-datenveröffentlichung)
4. [Ein Freigabeschritt](#ein-freigabeschritt)
5. [Aufzeichnen, wer was getan hat](#aufzeichnen-wer-was-getan-hat)
6. [Checkliste](#checkliste)

## Typische Personen und ihre Rechte

| Person (Funktion) | Darf | Darf nicht |
|---|---|---|
| Datenveröffentlichung | Datensätze und Distributionen im Katalog anlegen und bearbeiten | Klassen, Rollen oder andere Teile der Website ändern |
| Datenverantwortliche (Data Steward) | Datensätze vor der Veröffentlichung freigeben oder ablehnen | (wahlweise) Inhalte bearbeiten |
| Webredaktion | Datensätze aus Nachrichten und Seiten verlinken | Datensätze anlegen |
| Administration | Klassen, Rollen, Feeds, API-Einstellungen | Daten ohne Freigabe veröffentlichen |

Nennen Sie in Ihrer Dokumentation Funktionen, keine Personen; ordnen Sie Personen Benutzergruppen zu.

## Eine Sektion für offene Daten

1. **Setup > Sektionen**: Legen Sie eine Sektion „Offene Daten“ an (Bezeichner `open_data`).
2. Weisen Sie ihr den Katalogordner mit **Inhalt zuweisen** zu; alles darunter erhält die Sektion, und neue Datensätze
   übernehmen die Sektion ihres übergeordneten Objekts.
3. Stellen Sie sicher, dass die Rolle Anonymous die Sektion lesen darf, sonst sehen Besucher den Katalog nicht
   ([Rechte für Besucher](../../../../eng-us/help/creators/website-creators/visitor-permissions.md)).

## Die Rolle Datenveröffentlichung

Legen Sie unter **Benutzer > Rollen und Richtlinien** (`/role/list`) eine Rolle „Data publisher“ an mit:

| Modul / Funktion | Beschränkung |
|---|---|
| `content/read` | Sektion: Offene Daten, Standard |
| `content/create` | Klasse: dataset, distribution, file; Sektion: Offene Daten |
| `content/edit` | Sektion: Offene Daten |
| `content/versionread` | Sektion: Offene Daten |

Weisen Sie sie einer Benutzergruppe „Datenteam“ zu. Vergeben Sie nicht `content/remove`: Datensätze werden
zurückgezogen, nicht entfernt (siehe „Datensätze versionieren und aktualisieren“). Fügen Sie `content/translate` mit
einer Sprachbeschränkung hinzu, wenn Übersetzer an Beschreibungen von Datensätzen arbeiten.

## Ein Freigabeschritt

So muss jeder neue oder geänderte Datensatz freigegeben werden, bevor er öffentlich ist:

1. **Setup > Workflows**: Öffnen Sie eine Workflow-Gruppe, klicken Sie auf **Neuer Workflow**, benennen Sie ihn
   „Datenfreigabe“, wählen Sie **Event / Approve** und klicken Sie auf **Ereignis hinzufügen**. Setzen Sie im Ereignis
   **Betroffene Sektionen** auf Offene Daten und **Gruppen, die Content freigeben** auf „Datenverantwortliche“, dann
   **OK**.
2. **Setup > Trigger**: Wählen Sie in der Zeile **Vor dem Veröffentlichen von Inhalten** „Datenfreigabe“ und klicken
   Sie auf **Änderungen anwenden**. Ein Workflow pro Trigger: Wenn bereits ein anderer Workflow ihn nutzt, kombinieren
   Sie beide mit einem Ereignis **Multiplexer**.
3. Stellen Sie sicher, dass die Cronjob-Gruppe `frequent` läuft: Sie enthält den Workflow-Cronjob, und ohne ihn
   werden freigegebene Inhalte nie veröffentlicht ([Workflows](../../../anleitungen/workflows.md)).

Die Version einer veröffentlichenden Person wartet dann als **ausstehend**; die Datenverantwortliche findet sie unter
**Zusammenarbeit** auf dem Dashboard und klickt auf **Freigeben** oder **Ablehnen**.

## Aufzeichnen, wer was getan hat

- Die Versionsgeschichte zeigt, wer jede Version erstellt hat ([Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)).
- Der Audit-Trail zeichnet Veröffentlichen, Entfernen, Änderungen von Sektion und Zustand im Kanal `content` und
  Rollenzuweisungen im Kanal `access` auf; er ist standardmäßig eingeschaltet
  ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).

## Checkliste

- [ ] Eine Sektion für den Katalog, für Anonymous lesbar.
- [ ] Die Datenveröffentlichung kann nur Datensatzklassen anlegen, nur in dieser Sektion.
- [ ] Niemand außer der Administration kann Datensätze entfernen.
- [ ] Freigabe-Workflow eingeschaltet, Cronjob läuft, Datenverantwortliche benannt.
- [ ] Rollen einmal im Jahr überprüft; Ausgeschiedene aus der Gruppe Datenteam entfernt.

## Verwandte Seiten

- [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)
- [Sektionen und Objektzustände für Websiteersteller](../../../../eng-us/help/creators/website-creators/sections-and-object-states.md)
- [Entwürfe und ausstehende Einträge](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
