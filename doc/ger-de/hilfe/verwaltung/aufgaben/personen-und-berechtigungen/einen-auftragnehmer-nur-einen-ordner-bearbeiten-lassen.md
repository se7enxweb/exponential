---
title: Einen Auftragnehmer nur einen Ordner bearbeiten lassen
audience: government, admin, project-manager
category: public-sector
order: 140
summary: Schritt für Schritt - einer Agentur oder einem Auftragnehmer persönliche Konten in einer Vertragsgruppe geben, eine enge Inhaltsrolle, die einem Ordner zugewiesen ist, die Freigabe alles Veröffentlichten und ein sauberes Ende am letzten Tag des Vertrags.
keywords: Auftragnehmer, Agentur, Dienstleister, ein Ordner, externer Redakteur, befristeter Zugang, Vertragsgruppe, Teilbaum, Mit Einschränkungen zuordnen, Offboarding, Freigabe, contractor, agency, supplier, one folder, external editor, temporary access, contract group, subtree, Assign with limitation, offboarding, approval
type: help
---
# Einen Auftragnehmer nur einen Ordner bearbeiten lassen

Diese Aufgabe ist für Administratoren und Verantwortliche für Verträge. Am Ende können die Mitarbeiter des
Auftragnehmers in einem Ordner Inhalte anlegen und bearbeiten, ihre Arbeit wird freigegeben, bevor sie öffentlich ist,
und der Zugang ist bereit, mit dem Vertrag beendet zu werden. Gestaltung:
[Rollengestaltung: Auftragnehmer und befristete Mitarbeiter](../../../../../eng-us/help/government/operations/people-and-permissions/role-design-contractors.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Gruppe und die Konten anlegen](#die-gruppe-und-die-konten-anlegen)
3. [Die Rolle anlegen und zuweisen](#die-rolle-anlegen-und-zuweisen)
4. [Die Freigabe eingeschaltet lassen](#die-freigabe-eingeschaltet-lassen)
5. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
6. [Am Ende des Vertrags](#am-ende-des-vertrags)
7. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Der Vertrag: welcher Ordner, welche Inhaltstypen, Beginn und Ende, die Namen der Personen.
- Ein Konto mit dem Modul `role` und dem Recht, Benutzer anzulegen.

## Die Gruppe und die Konten anlegen

1. Legen Sie im Baum **Benutzer** eine Benutzergruppe an, die nach dem Vertrag benannt ist, zum Beispiel
   „Contract 2026-07 - web agency“.
2. Legen Sie darin ein persönliches Konto je Person an ([Einen neuen Mitarbeiter hinzufügen](einen-neuen-mitarbeiter-hinzufuegen.md)).
   Keine gemeinsamen Teamkonten.

## Die Rolle anlegen und zuweisen

1. **Benutzer > Rollen und Richtlinien > Neue Rolle** „Contractor - content“.
2. Richtlinien:

   | Modul | Funktion | Einschränkungen |
   |---|---|---|
   | user | login | SiteAccess: der Siteaccess der Redakteure |
   | content | read | Sektion: Standard, Media |
   | content | create | Klasse: die Klassen aus dem Vertrag |
   | content | edit | keine (die Zuweisung schränkt ein) |
   | content | versionread, diff | keine |
   | content | dashboard | keine |
   | user | password | keine |

3. **Speichern**. Wählen Sie auf der Seite der Rolle **Teilbaum**, **Mit Einschränkungen zuordnen**, wählen Sie den
   Ordner des Vertrags, dann die Vertragsgruppe.

Weil `user/login` in dieser Rolle steht, gilt die Zuweisung auf den Teilbaum auch dafür; wenn die Anmeldung
fehlschlägt, verlegen Sie `user/login` in eine eigene Rolle, die der Vertragsgruppe ohne Einschränkung zugewiesen ist.

## Die Freigabe eingeschaltet lassen

Stellen Sie sicher, dass die Sektion des Ordners unter **Betroffene Sektionen** des Freigabe-Workflows steht und die
Vertragsgruppe nicht unter den ausgeschlossenen Gruppen
([Das Ereignis Approve einrichten](../../../../../eng-us/help/government/operations/editorial-workflows/setting-up-the-approve-event.md)).

## Prüfen, ob es funktioniert hat

- Als Konto des Auftragnehmers: Der Ordner bietet **Bearbeiten** an; andere Ordner nicht; das Veröffentlichen wartet auf
  Freigabe.
- Der Siteaccess der Administration lehnt die Anmeldung ab, wenn `user/login` nur den Siteaccess der Redakteure nennt.

## Am Ende des Vertrags

1. Markieren Sie auf der Seite der Rolle die Zuweisung der Vertragsgruppe und wählen Sie **Ausgewählte entfernen**.
2. **Benutzer > Benutzerkonten prüfen**: Wählen Sie die Gruppe unter **Benutzergruppe**, markieren Sie die Konten,
   **Deaktivieren**, **Bestätigen**.
3. **Liste als CSV exportieren** für die Vertragsakte.
4. Optional **Deaktivieren und verschieben** Sie die Konten
   ([Einen ausscheidenden Mitarbeiter deaktivieren und verschieben](einen-ausscheidenden-mitarbeiter-deaktivieren-und-verschieben.md)).

## Verwandte Seiten
- [Eine Rolle mit einer Einschränkung auf einen Teilbaum zuweisen](eine-rolle-mit-einer-einschraenkung-auf-einen-teilbaum-zuweisen.md)
- [Redakteure aus dem Siteaccess der Administration heraushalten](redakteure-aus-dem-siteaccess-der-administration-heraushalten.md)
- [Einen ausscheidenden Mitarbeiter entfernen](einen-ausscheidenden-mitarbeiter-entfernen.md)
- [Funktionstrennung](../../../../../eng-us/help/government/operations/people-and-permissions/separation-of-duties.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
