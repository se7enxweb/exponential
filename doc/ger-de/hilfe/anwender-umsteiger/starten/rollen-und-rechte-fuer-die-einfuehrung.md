---
title: Rollen und Rechte für die Einführung
audience: adopter, admin, project-manager, customer
category: adoption
order: 53
summary: Wie Anwender Rollen und Rechte in Exponential für die Einführung einrichten - von den Rollen der Menschen im Projekt zu Rollen und Richtlinien in der Administration, minimale Rechte, Sektionen und Teilbäume, Tests mit Testkonten und spätere Ordnung.
keywords: Rollen, Rechte, Richtlinien, rollenbasierte Rechte, minimale Rechte, Sektionen, Teilbäume, Redakteure, Administratoren, Testkonten, Austritt, roles, permissions, policies, role-based permissions, least privilege, sections, subtrees, editors, administrators, test accounts, offboarding
type: help
---
# Rollen und Rechte für die Einführung

Exponential hat ein "Fine grained role based permissions system", ein feingranulares rollenbasiertes Rechtesystem
(`README.md`). Es vor dem Start gut einzurichten vermeidet sowohl blockierte Redakteure als auch Redakteure, die ändern
können, was sie nicht sollten.

## Inhalt

1. [Von Menschen zu Rollen](#von-menschen-zu-rollen)
2. [Grundsätze](#grundsätze)
3. [Testen](#testen)
4. [Ordnung halten](#ordnung-halten)

## Von Menschen zu Rollen

| Menschen | Übliche Rolle in der Administration |
|---|---|
| Redakteure einer Abteilung | In ihrer Sektion oder ihrem Teilbaum bearbeiten und anlegen |
| Leitende Redakteure | Zusätzlich veröffentlichen und freigeben |
| Übersetzer | In ihrer Sprache bearbeiten |
| Administratoren | Voller Zugriff, wenige Personen |
| Mitglieder der Website | Nur Zugang zu Bereichen für Mitglieder |

Empfehlungen für Teams: [Rollen für Ihr Team](../../kunden/alltag/rollen-fuer-ihr-team.md),
[Rollen für neue Mitarbeitende wählen](../../kunden/alltag/rollen-fuer-neue-mitarbeitende-waehlen.md).

## Grundsätze

- **Minimale Rechte**: Geben Sie jeder Rolle nur, was die Arbeit braucht.
- **Rollen für Gruppen, nicht für Personen**: Weisen Sie Benutzer Gruppen und Rollen Gruppen zu.
- **Wenige Administratoren**: Jeder ist ein Risiko ([Administratorkonten und Zugriff](../../kunden/gesund-halten/administratorkonten-und-zugriff.md)).
- **Zweistufige Anmeldung** für Administratoren, wo möglich ([Konten, Passwörter und Zwei-Faktor](../../kunden/alltag/konten-passwoerter-und-zweistufige-anmeldung.md)).

## Testen

Legen Sie auf Staging ein Testkonto pro Rolle an, melden Sie sich mit jedem an und probieren Sie aus, was es können muss
und was nicht. Entfernen Sie die Testkonten vor dem Start.

## Ordnung halten

- Entziehen Sie den Zugriff, wenn Menschen gehen ([Austritt von Mitarbeitenden](../../kunden/alltag/ausscheiden-von-mitarbeitenden.md)).
- Überprüfen Sie die Rollen mindestens einmal im Jahr ([Jährliche Überprüfung des Nutzens](../../kunden/nutzen/jaehrliche-ueberpruefung-des-nutzens.md)).

## Verwandte Seiten

- [Starten](README.md)
- [Konten und Anmeldung](../../konten-und-anmeldung.md)
- [Team und Rollen](../planung/team-und-rollen.md)
- [Steuerung nach dem Start](../nach-der-einfuehrung/steuerung-nach-dem-start.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
