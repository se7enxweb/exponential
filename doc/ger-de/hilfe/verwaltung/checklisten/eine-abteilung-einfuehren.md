---
title: Eine Abteilung einführen
audience: government, admin, editor, project-manager
category: public-sector
order: 120
summary: Wie Sie eine neue Abteilung einer öffentlichen Stelle auf eine bestehende Exponential-Website bringen - Inhaltsbereich, Sektion, Benutzergruppe, Rollen, Workflow, Design, Schulung und Start - Schritt für Schritt mit den beteiligten Seiten der Administration.
keywords: Einführung, Abteilung, neue Abteilung, Sektion, Benutzergruppe, Rolle, Teilbaum, Workflow, Freigabe, Schulung, Multisite, öffentlicher Sektor, onboarding, department, new department, section, user group, role, subtree, approval, training, multisite, public sector
type: help
---
# Eine Abteilung einführen

Diese Checkliste richtet sich an die Website-Administration und das Webteam einer öffentlichen Stelle, wenn eine
Abteilung zur zentralen Website hinzukommt (oder in einer Multisite-Installation eine eigene Website erhält). Sie nutzt
Sektionen, Benutzergruppen und Rollen, damit die Abteilung ihre eigenen Inhalte veröffentlichen kann, ohne die anderer
zu berühren.

## Inhalt

1. [Den Umfang vereinbaren](#den-umfang-vereinbaren)
2. [Struktur](#struktur)
3. [Menschen und Rechte](#menschen-und-rechte)
4. [Ablauf](#ablauf)
5. [Schulung und Start](#schulung-und-start)

## Den Umfang vereinbaren

- [ ] Inhalte, die die Abteilung veröffentlichen wird, ihre Arten (vorhandene Inhaltsklassen oder benötigte neue) und ihr Umfang.
- [ ] Verantwortliche Person in der Abteilung und eine Vertretung.
- [ ] Sprachen ([Checkliste Mehrsprachigkeit](checkliste-mehrsprachigkeit.md)).
- [ ] Ein Teilbaum der Hauptwebsite oder eine eigene Website in derselben Installation ([Multisite einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md)).

## Struktur

- [ ] Legen Sie den Teilbaum der Abteilung im Inhaltsbaum an ([Den Inhaltsbaum strukturieren](../../../../eng-us/help/creators/website-creators/structuring-the-content-tree.md)).
- [ ] Legen Sie unter **Setup > Sektionen** eine Sektion dafür an und weisen Sie ihr den Teilbaum zu ([Sektionen](../../../anleitungen/sektionen.md)).
- [ ] Menüs und Navigation enthalten den neuen Bereich ([Menüs und Navigation](../../../../eng-us/help/creators/website-creators/menus-and-navigation.md)).
- [ ] Für eine eigene Website: Siteaccess, Design und Einstellungen ([INI-Overrides für mehrere Websites](../../../../eng-us/features/6.0/multi-site-ini-overrides.md)).

## Menschen und Rechte

- [ ] Eine Benutzergruppe für die Redaktion der Abteilung.
- [ ] Eine Rolle, beschränkt auf die Sektion oder den Teilbaum der Abteilung, der Gruppe zugewiesen ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
- [ ] Eine Rolle für Freigebende, wenn eine Freigabe verlangt ist.
- [ ] Konten für namentlich bekannte Personen angelegt; niemand erhält die Rolle vor der Schulung ([Neue Mitarbeitende einarbeiten](../../../../eng-us/help/government/procurement/training/onboarding-new-staff.md)).
- [ ] Prüfen Sie später mit **Benutzerkonten prüfen** (`/user/hygiene`), ob die Konten genutzt werden ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).

## Ablauf

- [ ] Freigabe-Workflow, falls nötig: ein Workflow mit einem Ereignis Freigeben und einem Trigger beim Veröffentlichen ([Workflows](../../../anleitungen/workflows.md#3-durchgerechnetes-beispiel-freigabe-vor-dem-veröffentlichen)).
- [ ] Objektzustände für die Stufen der Abteilung, falls nötig ([Objektzustände](../../../anleitungen/objektzustaende.md)).
- [ ] Benachrichtigungen für Freigebende ([Benachrichtigungen](../../../anleitungen/benachrichtigungen-administrator.md)).
- [ ] Redaktionsleitfaden geteilt ([Redaktionelle Qualität](redaktionelle-qualitaet.md), [Checkliste einfache Sprache](checkliste-einfache-sprache.md)).

## Schulung und Start

- [ ] Redaktion geschult (Redaktion 101), Freigebende in Freigaben ([Rollen und Kurse](../../../../eng-us/help/government/procurement/training/roles-to-courses.md)).
- [ ] Erste Inhalte angelegt und geprüft, bevor der Bereich sichtbar ist.
- [ ] Barrierefreiheit und Links geprüft ([Checkliste Barrierefreiheit](checkliste-barrierefreiheit.md)).
- [ ] Bereich sichtbar gemacht; Abteilung angekündigt.
- [ ] In die nächste [Vierteljährliche Zugriffsprüfung](vierteljaehrliche-zugriffspruefung.md) aufgenommen.

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Sektionen und Objektzustände](../../../../eng-us/help/creators/website-creators/sections-and-object-states.md)
- [An die Redaktion übergeben](../../../../eng-us/help/creators/website-creators/handing-over-to-editors.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
