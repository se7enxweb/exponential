---
title: Bereitschaft für das Veröffentlichen in Wahlzeiten
audience: government, editor, admin, project-manager
category: public-sector
order: 200
summary: Gängige Praxis öffentlicher Stellen, die das Veröffentlichen in einer Wahlzeit einschränken oder prüfen, und wie Rollen, Sektionen, Objektzustände, Freigabe-Workflows und der Audit-Trail von Exponential diese Regeln unterstützen können - als Praxis, nicht als rechtliche Vorgabe.
keywords: Wahlzeit, Vorwahlzeit, Neutralitätsgebot, Karenzzeit, Einschränkungen beim Veröffentlichen, Freigabe, Objektzustände, Workflows, Rollen, Audit-Trail, Neutralität, öffentlicher Sektor, election period, pre-election, purdah, caretaker period, publishing restrictions, approval, object states, roles, audit trail, neutrality, public sector
type: help
---
# Bereitschaft für das Veröffentlichen in Wahlzeiten

Diese Checkliste richtet sich an die Leitung der Kommunikation, Redaktionen und die Website-Administration öffentlicher
Stellen, die vor einer Wahl besondere Regeln für das Veröffentlichen anwenden (in manchen Ländern Vorwahlzeit oder
Übergangszeit genannt). Die Regeln selbst kommen aus Ihrer Rechtsordnung und Ihrer Organisation; diese Seite zeigt, wie
die Funktionen von Exponential Ihnen helfen können, sie einzuhalten. Sie beschreibt Praxis, keine Pflichten.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das Recht
> und die Leitlinien, die für Ihre Organisation gelten, mit Ihrer Rechtsberatung.

## Inhalt

1. [Kennen Sie Ihre Regeln](#kennen-sie-ihre-regeln)
2. [Rechte](#rechte)
3. [Prüfung vor dem Veröffentlichen](#prüfung-vor-dem-veröffentlichen)
4. [Bestehende Inhalte](#bestehende-inhalte)
5. [Aufzeichnungen dessen, was veröffentlicht wurde](#aufzeichnungen-dessen-was-veröffentlicht-wurde)
6. [Nach der Wahlzeit](#nach-der-wahlzeit)

## Kennen Sie Ihre Regeln

- [ ] Die Daten der Wahlzeit und die geltenden Regeln, schriftlich, von Ihrem Rechts- oder Governance-Team.
- [ ] Welche Inhalte betroffen sind (zum Beispiel Ankündigungen, Kampagnen, Profile gewählter Amtsträger) und welche nicht (gesetzlich vorgeschriebene Bekanntmachungen, Notfallinformationen).
- [ ] Wer in Grenzfällen entscheidet.
- [ ] Die Redaktion ist vor Beginn der Wahlzeit eingewiesen.

## Rechte

- [ ] Sektionen oder Teilbäume für betroffene Inhalte festgelegt ([Sektionen](../../../anleitungen/sektionen.md)).
- [ ] Für die Wahlzeit eine Rolle, die in diesen Sektionen nicht veröffentlichen oder dort nur Entwürfe anlegen kann ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
- [ ] Ein Objektzustand wie „zurückgestellt wegen Wahlzeit“, mit Rechten, die davon abhängen ([Objektzustände](../../../anleitungen/objektzustaende.md)).
- [ ] Die Änderungen an den Rollen schriftlich festgehalten, damit sie danach genau rückgängig gemacht werden können.

## Prüfung vor dem Veröffentlichen

- [ ] Ein Freigabe-Workflow für betroffene Sektionen: ein Workflow mit einem Ereignis Freigeben und einem Trigger vor dem Veröffentlichen ([Workflows](../../../anleitungen/workflows.md#3-durchgerechnetes-beispiel-freigabe-vor-dem-veröffentlichen)).
- [ ] Freigebende benannt und darin geschult, die Regeln zu prüfen ([Redaktion 201, Modul 5](../../../../eng-us/help/trainers/editors-201/module-05-approvals-and-collaboration/README.md)).
- [ ] Der Workflow-Cronjob läuft, damit freigegebene Einträge veröffentlicht werden ([Workflows](../../../anleitungen/workflows.md#34-sicherstellen-dass-der-workflow-cronjob-läuft)).
- [ ] Notfallinformationen über einen eigenen Weg ausgenommen ([Bereitschaft für Notfallwarnungen](bereitschaft-fuer-notfallwarnungen.md)).

## Bestehende Inhalte

- [ ] Inhalte festgestellt, die während der Wahlzeit verborgen werden müssen; verbergen statt löschen, damit sie unverändert zurückkehren können.
- [ ] Newsletter und Feeds geprüft: geplante Versendungen pausiert, wenn sie betroffene Inhalte enthalten ([Newsletter versenden](../../../../eng-us/help/creators/website-creators/sending-newsletters.md)).
- [ ] Gemeinsame Blöcke (Kopfbereich, Startseite) geprüft ([Gemeinsamer Kopf- und Fußbereich](../../../../eng-us/help/creators/website-creators/shared-header-and-footer.md)).

## Aufzeichnungen dessen, was veröffentlicht wurde

- [ ] Der Audit-Trail zeichnet Veröffentlichungen und Änderungen an Rechten auf; Sie wissen, wie Sie ihn durchsuchen ([Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)).
- [ ] Versionen zeigen, was jede Seite wann sagte ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)).
- [ ] Entscheidungen in Grenzfällen werden außerhalb der Website festgehalten.

## Nach der Wahlzeit

- [ ] Rollen und Workflows wie schriftlich festgehalten wiederhergestellt.
- [ ] Verborgene Inhalte nach Prüfung wieder sichtbar gemacht.
- [ ] Inhalte an das Ergebnis angepasst (zum Beispiel neue Amtsträger) ([Redaktionelle Qualität](redaktionelle-qualitaet.md)).
- [ ] Erkenntnisse für die nächste Wahlzeit festgehalten.

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Sektionen und Objektzustände](../../../../eng-us/help/creators/website-creators/sections-and-object-states.md)
- [Vierteljährliche Zugriffsprüfung](vierteljaehrliche-zugriffspruefung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
