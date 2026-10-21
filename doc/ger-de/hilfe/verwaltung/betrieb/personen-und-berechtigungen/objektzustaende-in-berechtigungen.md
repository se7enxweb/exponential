---
title: Objektzustände in Berechtigungen
audience: government, admin, editor
category: public-sector
order: 40
summary: Wie Objektzustände (zum Beispiel Entwurf, In Prüfung, Freigegeben oder Rechtlich geprüft) es einer öffentlichen Stelle ermöglichen, den Zugang von Beschäftigten und Besuchern an die Phase zu knüpfen, die ein Inhalt erreicht hat, mit den Einschränkungen StateGroup und NewState und einem Beispiel für einen Prüfprozess.
keywords: Objektzustände, Zustandsgruppen, StateGroup, NewState, state/assign, Prüfung, Freigabe, rechtliche Prüfung, Sperrfrist, Lebenszyklus von Inhalten, Berechtigungen, object states, state groups, review, approval, legal clearance, embargo, content lifecycle, permissions
type: help
---
# Objektzustände in Berechtigungen

Diese Seite richtet sich an Administratoren, die Zugriffsrechte davon abhängig machen wollen, wo ein Inhalt in seinem Lebenszyklus
steht: in Prüfung, freigegeben, rechtlich geprüft, archiviert. Genau das leisten die Objektzustände von Exponential. Die Fakten
stammen aus [Objektzustände](../../../../anleitungen/objektzustaende.md); diese Seite zeigt, wie eine öffentliche Stelle sie nutzt.

## Inhalt
1. [Was Zustände den Rollen hinzufügen](#was-zustände-den-rollen-hinzufügen)
2. [Die zwei Einschränkungen](#die-zwei-einschränkungen)
3. [Beispiel: eine Prüfgruppe](#beispiel-eine-prüfgruppe)
4. [Zustände, Freigaben und der Audit-Trail](#zustände-freigaben-und-der-audit-trail)
5. [Grenzen](#grenzen)
6. [Verwandte Seiten](#verwandte-seiten)

## Was Zustände den Rollen hinzufügen

Sektionen sagen, wohin ein Inhalt gehört; der Baum sagt, worunter er liegt; **Zustände** sagen, wie weit er ist. Jedes Objekt
hat einen Zustand aus jeder Zustandsgruppe. Exponential liefert eine Gruppe mit, `ez_lock` (Nicht gesperrt, Gesperrt), und Sie fügen
unter **Setup > Zustände** (`/state/groups`) eigene hinzu. Zustände selbst ändern nichts: Erst Rollen geben ihnen Bedeutung
([Objektzustände, „Kurz gesagt“](../../../../anleitungen/objektzustaende.md#kurz-gesagt)).

Typische Zustandsgruppen im öffentlichen Sektor (Beispiele; benennen Sie sie nach Ihrem eigenen Prozess):

| Gruppe | Zustände | Zweck |
|---|---|---|
| Prüfung | Entwurf, In Prüfung, Freigegeben | nur freigegebene Inhalte sind öffentlich |
| Recht | Nicht nötig, Rechtliche Prüfung ausstehend, Geprüft | rechtliche Freigabe vor der Veröffentlichung |
| Barrierefreiheitsprüfung | Nicht geprüft, Geprüft | eine manuelle Prüfung der Barrierefreiheit verfolgen |
| Aufbewahrung | Aktuell, Prüfung fällig, Archiviert | Seiten markieren, die erneut angesehen werden sollen |

## Die zwei Einschränkungen

| Einschränkung | Angeboten von | Wirkung |
|---|---|---|
| `StateGroup_<identifier>` | `content/read`, `edit`, `publish`, `manage_locations`, `remove`, `state/assign` | die Richtlinie gilt nur für Objekte in den gewählten Zuständen |
| `NewState` | `state/assign` | welche Zustände der Benutzer setzen darf (`group/state`) |

Eine Richtlinie `content/read` mit `StateGroup_review = Approved` lässt ihre Rolle nur freigegebene Objekte lesen; alles andere aus
dieser Gruppe ist für sie unsichtbar, auf Seiten und in Fetches
([Objektzustände, Abschnitt 8](../../../../anleitungen/objektzustaende.md#8-zustände-in-rollen-und-richtlinien)). Gruppen, deren Kennung mit `ez`
beginnt, gehören zu Exponential und können nicht in Richtlinien verwendet werden.

## Beispiel: eine Prüfgruppe

Das ausgearbeitete Beispiel des Leitfadens, übertragen auf Rollen im öffentlichen Sektor
([Objektzustände, Abschnitt 9](../../../../anleitungen/objektzustaende.md#9-ein-durchgearbeitetes-beispiel-ein-prüf-workflow)):

| Rolle | Richtlinie | Einschränkungen |
|---|---|---|
| Anonymous (Besucher) | `content/read` | Sektion Standard, `StateGroup_review = Approved` |
| Autorin oder Autor im Fachbereich | `content/create`, `content/edit` | Eigentümer: selbst, `StateGroup_review = Draft` |
| Autorin oder Autor im Fachbereich | `state/assign` | Eigentümer: selbst, `StateGroup_review = Draft`, `NewState = review/in_review` |
| Prüfende Person | `content/read`, `content/edit` | (Teilbaum des Fachbereichs) |
| Prüfende Person | `state/assign` | `NewState = review/draft, review/approved` |

Bevor Sie die Rolle Anonymous einschränken, setzen Sie die bereits öffentlichen Inhalte auf Freigegeben, am obersten Knoten mit „Auch
für alles unterhalb dieses Knotens“; sonst verschwindet die ganze Website für Besucher auf einen Schlag, weil jedes bestehende Objekt
im ersten Zustand der Gruppe beginnt.

## Zustände, Freigaben und der Audit-Trail

- Jede Zustandsänderung an einem Objekt wird im Audit-Trail als `content.object.state` erfasst, mit den Zuständen vorher und nachher
  ([Objektzustände, Abschnitt 12](../../../../anleitungen/objektzustaende.md#12-workflows-ereignisse-audit-und-suche)).
  Dieser Eintrag ist der Beleg dafür, dass eine prüfende Person etwas freigegeben hat.
- Zustände und der Freigabe-Workflow sind zwei verschiedene Werkzeuge. Der Workflow hält eine Version zurück, bis eine freigebende
  Person entscheidet ([Überblick über Freigabe-Workflows](../../../../../eng-us/help/government/operations/editorial-workflows/approval-workflows-overview.md)); Zustände halten eine
  Phase am Objekt über Versionen hinweg fest. Viele Organisationen nutzen den Workflow für das Veröffentlichen und einen Zustand für eine
  getrennte Prüfung wie die rechtliche Freigabe ([Schritt der rechtlichen Prüfung](../../../../../eng-us/help/government/operations/editorial-workflows/legal-review-step.md)).
- Ein Workflow kann bei Zustandsänderungen laufen, sobald `workflow.ini [OperationSettings] AvailableOperationList[]=content_updateobjectstate`
  gesetzt ist; **Setup > Trigger** bietet die Operation dann an.

## Grenzen

- Ein Zustand ist keine Version: Das Setzen eines Zustands erzeugt keine neue Version, und eine spätere Bearbeitung setzt den Zustand
  nicht zurück, es sei denn, Ihr Prozess oder ein Workflow tut das.
- Zustände verbergen Inhalte nicht von selbst; das tut nur eine Leserichtlinie mit einer Zustandseinschränkung.
- Exponential hat kein Datumsfeld an einem Zustand; ein Zustand „Prüfung fällig“ wird von Personen oder von Ihrem eigenen Code gesetzt,
  nicht von einem Zeitgeber. Siehe [Prüftermine mit Objektzuständen](../../../../../eng-us/help/government/operations/editorial-workflows/review-dates-with-object-states.md).

## Verwandte Seiten
- [Eine Zustandsgruppe für Prüfungen einrichten](../../../../../eng-us/help/government/tasks/people-and-permissions/set-up-a-review-state-group.md)
- [Rollen, Richtlinien und Einschränkungen](rollen-richtlinien-und-einschraenkungen.md)
- [Funktionstrennung](funktionstrennung.md)
- [Leitfaden zu Objektzuständen](../../../../anleitungen/objektzustaende.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
