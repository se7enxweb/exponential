---
title: Abteilungen, Rollen und Sektionen
audience: businesses, admin, project-manager
category: business-solutions
order: 20
summary: Wie ein Intranet Abteilungen auf Exponential abbildet - Benutzergruppen für Personen, Sektionen für Inhaltsbereiche, Rollen mit eingeschränkten Richtlinien und Einschränkungen auf Teilbaum oder Sektion, damit jede Abteilung nur ihre eigenen Seiten bearbeitet.
keywords: Abteilungen, Benutzergruppen, Rollen, Richtlinien, Sektionen, Teilbaum-Einschränkung, Sektions-Einschränkung, mit Einschränkungen zuordnen, minimale Rechte, Berechtigungen im Intranet, content/read, content/edit, departments, user groups, roles, policies, sections, subtree limitation, section limitation, assign with limitation, least privilege, intranet permissions
type: help
---
# Abteilungen, Rollen und Sektionen

Diese Seite ist für Intranetverantwortliche und Administratoren, die entscheiden, wer welchen Teil des Intranets lesen
und bearbeiten darf. Sie erklärt die drei Bausteine, die Exponential verwendet (Benutzergruppen, Sektionen und Rollen),
zeigt das Muster für Abteilungen, die jeweils ihre eigenen Seiten pflegen, und verlinkt die Anleitungen mit jedem
Klick.

![Rollen, eine pro Abteilung, nach Sektion eingeschränkt](../../../../eng-us/help/images/help/bu-dept-sections.webp)

## Inhalt

1. [Drei Bausteine, drei Fragen](#drei-bausteine-drei-fragen)
2. [Das Muster für Abteilungen](#das-muster-für-abteilungen)
3. [Sektions-Einschränkung oder Teilbaum-Einschränkung](#sektions-einschränkung-oder-teilbaum-einschränkung)
4. [Weitere Einschränkungen, die man kennen sollte](#weitere-einschränkungen-die-man-kennen-sollte)
5. [Prüfen, was eine Rolle wirklich gibt](#prüfen-was-eine-rolle-wirklich-gibt)
6. [Fehler, die Sie vermeiden sollten](#fehler-die-sie-vermeiden-sollten)
7. [Checkliste](#checkliste)
8. [Verwandte Seiten](#verwandte-seiten)

## Drei Bausteine, drei Fragen

| Baustein | Beantwortet | Wo er liegt |
|---|---|---|
| **Benutzergruppe** | Wer sind diese Personen? (Vertrieb, Personal, alle Mitarbeiter) | der Benutzerbaum, unter **Benutzer** in der Administration |
| **Sektion** | Zu welchem Bereich gehören diese Inhalte? (Vertriebsseiten, Personalrichtlinien) | **Setup > Sektionen** (`/section/list`) |
| **Rolle** | Was dürfen die Personen einer Gruppe tun, und wo? | **Benutzer > Rollen und Richtlinien** (`role/list`) |

Eine Rolle ist eine Menge von Richtlinien; eine Richtlinie erlaubt ein Modul oder eine Funktion, gegebenenfalls nur in
manchen Sektionen, Klassen, Teilbäumen, Sprachen oder Siteaccesses. Eine Rolle bewirkt nichts, bis sie Benutzern oder
Gruppen zugeordnet ist ([Rollen und Richtlinien, In short](../../../anleitungen/rollen-und-richtlinien.md#kurz-gesagt)). Jedes
Objekt gehört zu genau einer Sektion, und neue Inhalte übernehmen die Sektion ihres übergeordneten Elements
([Sektionen, In short](../../../anleitungen/sektionen.md#kurz-gesagt)).

## Das Muster für Abteilungen

Die Anleitung zu Sektionen beschreibt genau diesen Fall, „An intranet with departments“
([Sektionen, 13.2](../../../anleitungen/sektionen.md#132-ein-intranet-mit-abteilungen)). In geschäftlichen Begriffen:

1. **Eine Sektion pro Abteilungsbereich**, zum Beispiel Vertrieb und Marketing. Der Ordner jeder Abteilung wird mit
   **Inhalt zuweisen** seiner Sektion zugewiesen, was dem Ordner und allem darunter diese Sektion gibt.
2. **Eine Benutzergruppe pro Abteilung**, mit den Mitarbeitern dieser Abteilung darin.
3. **Eine Rolle Mitarbeiter für alle** mit `content/read`, beschränkt auf die Mitarbeitersektionen, damit jeder
   Mitarbeiter die Seiten jeder Abteilung lesen kann.
4. **Eine gemeinsame Rolle Redakteur**, der Gruppe jeder Abteilung **mit einer Sektions-Einschränkung** auf die Sektion
   dieser Abteilung zugeordnet ([Rollen und Richtlinien, 5. Assign the role](../../../anleitungen/rollen-und-richtlinien.md#5-die-rolle-zuweisen)).
   Ein Redakteur des Vertriebs kann dann Vertriebsseiten bearbeiten und keine Marketingseiten.

Der Vorteil für das Unternehmen: Es gibt eine Rolle Redakteur zu pflegen, nicht eine pro Abteilung. Eine Abteilung
hinzuzufügen bedeutet eine Sektion, eine Gruppe und eine weitere Zuordnung.

| Abteilung | Sektion | Gruppe | Rolle Redakteur zugeordnet mit |
|---|---|---|---|
| Vertrieb | Vertrieb (`sales`) | Mitarbeiter Vertrieb | Section( Vertrieb ) |
| Marketing | Marketing (`marketing`) | Mitarbeiter Marketing | Section( Marketing ) |
| Personal | Personal (`hr`) | Mitarbeiter Personal | Section( Personal ) |
| Ganzes Unternehmen | Standard oder Intranet | Alle Mitarbeiter | nur lesen, über die Rolle Mitarbeiter |

> **Hinweis**
> Eine Sektion ist kein Platz im Baum. Sie hat keine URL und keinen Wurzelknoten; Objekte einer Sektion können überall
> liegen ([Sektionen, 1](../../../anleitungen/sektionen.md#1-was-eine-sektion-ist-und-was-nicht)). Die Inhalte jeder
> Abteilung unter einem Ordner zu halten, ist eine Konvention, die die Struktur leicht verständlich macht.

## Sektions-Einschränkung oder Teilbaum-Einschränkung

Eine Rolle kann statt mit einer Sektions-Einschränkung mit einer **Teilbaum**-Einschränkung zugeordnet werden. Beides
funktioniert; sie verhalten sich unterschiedlich, wenn Inhalte verschoben werden.

| | Sektions-Einschränkung | Teilbaum-Einschränkung |
|---|---|---|
| Folgt | der Sektion, die an jedem Objekt gespeichert ist | dem Platz im Baum |
| An einen anderen Ort verschobene Inhalte | behalten ihre Sektion, dieselben Redakteure behalten also den Zugriff | verlassen den Teilbaum, die Redakteure verlieren also den Zugriff |
| Nützlich, wenn | Bereiche über die Zuständigkeit definiert sind | Bereiche darüber definiert sind, wo Seiten liegen |
| Sichtbar auf | der Seite der Sektion, die die Rollen auflistet, die sie verwenden | der Rollenseite, die den Teilbaum nennt |

Für eine Dokumentenbibliothek, die mehrere Abteilungen teilen, ist eine Sektion meist übersichtlicher. Für einen
Projektbereich, der durch Verschieben archiviert wird, ist ein Teilbaum naheliegend.

## Weitere Einschränkungen, die man kennen sollte

Das Modul content bietet mehr Einschränkungen als Sektion und Teilbaum (`kernel/content/module.php`):

| Einschränkung | Verwendung in einem Intranet |
|---|---|
| Class | Redakteure des Nachrichtenbereichs dürfen News-Artikel anlegen, keine Ordner |
| Owner = Self | Autoren dürfen nur bearbeiten, was sie selbst angelegt haben |
| Language | Übersetzer dürfen nur ihre Sprache bearbeiten |
| Node, Subtree | Zugriff auf eine Seite oder einen Zweig |
| `StateGroup_<identifier>` | Zugriff nur auf Inhalte in manchen Zuständen, zum Beispiel Freigegeben ([Objektzustände, 8](../../../anleitungen/objektzustaende.md#8-zustände-in-rollen-und-richtlinien)) |
| `user/login` SiteAccess | Mitarbeiter dürfen sich im Siteaccess des Intranets anmelden (`kernel/user/module.php`) |

Erweiterungen können eigene Einschränkungen hinzufügen; eine Einschränkung, die kein Handler auswertet, verweigert den
Zugriff ([Einschränkungen von Inhaltsrichtlinien aus Erweiterungen](../../../../eng-us/features/6.0/content-limitation-handlers.md)).

## Prüfen, was eine Rolle wirklich gibt

Die Rollenseite schreibt jede Richtlinie als Satz, zum Beispiel *May read content, in section Standard, of the classes
Article and Folder*, und zählt jeden Benutzer, den die Rolle über ihre Gruppen erreicht
([Rollen und Richtlinien, 2](../../../anleitungen/rollen-und-richtlinien.md#2-eine-rolle-lesen)). Zwei Kennzeichen verdienen
Aufmerksamkeit:

- **Voller Zugriff** (rot): eine Richtlinie über alle Module.
- **Kann Rollen ändern** (gelb): eine Richtlinie über das Modul role, mit der sich ihre Benutzer jeden Zugriff selbst
  geben können.

Keines davon gehört zu einer Redakteursrolle einer Abteilung. Planen Sie einen regelmäßigen Blick auf beide Kennzeichen
ein ([Zugriffsprüfungen](../sicherheit/zugriffspruefungen.md)).

## Fehler, die Sie vermeiden sollten

- `content/read` ohne Sektions-Einschränkung vergeben: Mitarbeiter könnten Benutzerkonten und Setup-Objekte öffnen
  ([Was Besucher sehen dürfen, Common mistakes](../../../../eng-us/help/creators/website-creators/visitor-permissions.md#common-mistakes)).
- Eine Rolle pro Person anlegen, statt eine Rolle einer Gruppe zuzuordnen.
- Die neue Sektion in der Rolle Mitarbeiter vergessen: Ihre Seiten zeigen dann allen „Zugriff verweigert“.
- Redakteure einer Abteilung in zwei Gruppen setzen, deren Rollen zusammen mehr ergeben als beabsichtigt; Richtlinien
  mehrerer Rollen addieren sich.

## Checkliste

- [ ] Eine Sektion pro Bereich, jede mit einer Kennung.
- [ ] Eine Benutzergruppe pro Abteilung.
- [ ] Eine Rolle Mitarbeiter mit `content/read` für die Mitarbeitersektionen und `user/login` für den Siteaccess des
      Intranets.
- [ ] Eine Rolle Redakteur, pro Abteilung mit einer Sektions-Einschränkung zugeordnet.
- [ ] Keine Abteilungsrolle zeigt Voller Zugriff oder Kann Rollen ändern.
- [ ] Getestet durch Anmeldung als Mitglied jeder Abteilung.

## Verwandte Seiten

- [Sektionen](../../../anleitungen/sektionen.md)
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md)
- [Sektionen und Objektzustände für Website-Ersteller](../../../../eng-us/help/creators/website-creators/sections-and-object-states.md)
- [Freigabe-Workflows](freigabe-workflows.md)
- [Kunden- und Partnerbereiche](../extranets/kunden-und-partnerbereiche.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
