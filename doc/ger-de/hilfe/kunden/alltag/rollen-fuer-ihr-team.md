---
title: Rollen für Ihr Team
audience: customer, admin, project-manager
category: customer-success
order: 2
summary: Wie ein Website-Betreiber darüber nachdenkt, wer im Team was tun darf - die Rollen, die eine Standardinstallation mitbringt, die Aufgaben, die Menschen tatsächlich erledigen, Einschränkungen nach Sektion, Teilbaum und Sprache und die Berechtigungen, die nur wenigen Personen vorbehalten sein sollten.
keywords: Rollen, Richtlinien, Berechtigungen, Team, Rolle Editor, Administrator, Mitglied, anonym, minimale Rechte, Einschränkung auf Sektion, Teilbaum, Website-Betreiber, roles, policies, permissions, team, editor role, member, anonymous, least privilege, section limitation, subtree, site owner
type: help
---
# Rollen für Ihr Team

Diese Seite ist für den Betreiber oder die Leitung, die entscheidet, wer im Team was tun darf. In Exponential kommt
jede Berechtigung aus einer **Rolle**: einer Menge von **Richtlinien**, von denen jede ein Modul oder eine Funktion
erlaubt, möglicherweise eingeschränkt auf Sektionen, Klassen, Teilbäume, Sprachen oder Siteaccesses. Eine Rolle bewirkt
nichts, bis sie Benutzern oder Benutzergruppen **zugewiesen** ist ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
Ihr Administrator nimmt die Änderungen vor; diese Seite hilft Ihnen zu entscheiden, worum Sie bitten.

## Inhalt

1. [Die Rollen, mit denen Sie beginnen](#die-rollen-mit-denen-sie-beginnen)
2. [In Aufgaben denken, nicht in Personen](#in-aufgaben-denken-nicht-in-personen)
3. [Nach Ort und Sprache einschränken](#nach-ort-und-sprache-einschränken)
4. [Berechtigungen, die wenigen Personen vorbehalten sind](#berechtigungen-die-wenigen-personen-vorbehalten-sind)
5. [Wie oft Sie nachsehen sollten](#wie-oft-sie-nachsehen-sollten)

## Die Rollen, mit denen Sie beginnen

Die Demo-Installation unter `https://alpha.se7enx.com` zeigt die Rollen, mit denen eine Standardinstallation arbeitet
(auf der Live-Installation abgelesen, Oktober 2026):

| Rolle | Wer sie hat | Was sie gibt, in Kürze |
|---|---|---|
| **Anonymous** | jeder Besucher, der nicht angemeldet ist | die öffentlichen Teile der Website lesen |
| **Member** | registrierte Besucher | sich anmelden, das eigene Konto, Lesen von Inhalten nur für Mitglieder |
| **Editor** | das Redaktionsteam | Anmelden in der Administration, Inhalte anlegen und bearbeiten, Übersetzungen, Versionen, das Dashboard, zweistufige Anmeldung, Hilfe |
| **Administrator** | sehr wenige Personen | alles, in jedem Modul |

Die Rolle Administrator ist „Vollzugriff“: Die Rollenliste kennzeichnet sie rot. Eine Rolle, die Rollen ändern darf,
wird bernsteinfarben gekennzeichnet, weil ihre Inhaber sich jeden Zugriff selbst geben können
([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)). Ihre eigene Website hat vielleicht weitere Rollen,
die Ihr Partner hinzugefügt hat; fragen Sie nach der Liste.

> **Hinweis**
> Weisen Sie Rollen lieber **Gruppen** zu als einzelnen Benutzern: Jedes Mitglied der Gruppe, jetzt und später, erhält
> die Rolle. Der Benutzerbaum hat Gruppen wie Administrator users, Editors und Members
> ([Die Website an die Redaktion übergeben](../../../../eng-us/help/creators/website-creators/handing-over-to-editors.md)).

## In Aufgaben denken, nicht in Personen

Listen Sie die Aufgaben in Ihrem Team auf und geben Sie dann jeder Aufgabe eine Rolle. Die Übergabe der
Website-Ersteller nennt die üblichen: Autor, Redakteur (veröffentlicht), Layout-Redakteur, Formularbearbeitung
(`infocollector/read`) und Shop-Personal ([Die Website an die Redaktion übergeben](../../../../eng-us/help/creators/website-creators/handing-over-to-editors.md)).

| Aufgabe | Braucht typischerweise | Bitten Sie Ihren Administrator um |
|---|---|---|
| Autor | Entwürfe schreiben, nicht veröffentlichen | eine Redakteursrolle hinter einem Freigabe-Workflow ([Freigabe und Prüfung](freigabe-und-pruefung.md)) |
| Redakteur | schreiben und veröffentlichen | die Rolle Editor, eingeschränkt auf den Teil des Baums, der zum Team gehört |
| Freigebende Person | über ausstehende Versionen entscheiden | Mitgliedschaft in der Gruppe der freigebenden Personen des Freigabe-Workflows |
| Layout-Redakteur | Landingpages ändern | die Richtlinien von `explayouts` ([Landingpages mit Layouts](../wachstum/landingpages-mit-layouts.md)) |
| Formularbearbeitung | Formulareinsendungen lesen und exportieren | `infocollector/read` ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)) |
| Kontenverwaltung | Konten überprüfen und deaktivieren | `user/hygiene` ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)) |
| Übersetzer | in eine Sprache übersetzen | Übersetzen und Bearbeiten, eingeschränkt nach **Sprache** ([Übersetzungsroutine](uebersetzungsroutine.md)) |

Das [Arbeitsblatt für Teamrollen](arbeitsblatt-teamrollen.md) macht aus dieser Tabelle ein Formular zum Ausfüllen.

## Nach Ort und Sprache einschränken

Eine Rolle kann **mit einer Einschränkung** zugewiesen werden: nur in einem Teilbaum oder nur in einer Sektion. Eine
gemeinsame Rolle Editor, die der Gruppe jeder Abteilung mit einer Einschränkung auf die Sektion zugewiesen wird,
bedeutet eine einzige Rolle zu pflegen statt einer pro Team
([Abteilungen, Rollen und Sektionen](../../unternehmen/intranets/abteilungen-rollen-und-sektionen.md)). Die Richtlinien
zum Bearbeiten, Übersetzen und Anlegen von Inhalten lassen sich außerdem nach Sprache einschränken
([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)).

## Berechtigungen, die wenigen Personen vorbehalten sind

| Berechtigung | Warum sie heikel ist |
|---|---|
| Jede Rolle mit Vollzugriff | kann alles tun |
| Das Modul `role` | kann sich selbst jeden Zugriff gewähren |
| `infocollector/read` | liest jede Formulareinsendung, die personenbezogene Daten enthalten kann |
| `user/hygiene` | sieht Name, Benutzername, Status, Gruppen und Rollen jedes Kontos |
| `audit/read`, `audit/manage` | liest oder verwaltet das Prüfprotokoll |

Quellen: [Zugriffsüberprüfungen](../../unternehmen/sicherheit/zugriffspruefungen.md),
[Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md). `PreventPrivilegeEscalation` ist in
`settings/site.ini` standardmäßig eingeschaltet, sodass niemand mehr gewähren kann, als er selbst hat.

## Wie oft Sie nachsehen sollten

- Wenn jemand dazukommt, die Aufgabe wechselt oder geht: [Neue Mitarbeitende einarbeiten](neue-mitarbeitende-einarbeiten.md),
  [Ausscheiden von Mitarbeitenden](ausscheiden-von-mitarbeitenden.md).
- Einmal im Jahr eine vollständige Überprüfung: [Zugriffsüberprüfungen](../../unternehmen/sicherheit/zugriffspruefungen.md).
- Nachdem Ihr Partner eine Änderung geliefert hat, die ein Modul oder eine Rolle hinzufügt: Fragen Sie, was sie gibt und wem.

## Verwandte Seiten

- [Rollen für neue Mitarbeitende wählen](rollen-fuer-neue-mitarbeitende-waehlen.md)
- [Arbeitsblatt für Teamrollen](arbeitsblatt-teamrollen.md)
- [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)
- [Zugriffsüberprüfungen](../../unternehmen/sicherheit/zugriffspruefungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
