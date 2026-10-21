---
title: "Rollenentwurf: Redaktionen der Fachbereiche"
audience: government, admin, editor
category: public-sector
order: 70
summary: Eine wiederverwendbare Rolle für die Redaktionen aller Fachbereiche oder Behörden auf einer gemeinsamen öffentlichen Website, einmal je Fachbereich mit einer Teilbaum-Einschränkung zugewiesen, mit optionalen Varianten für Übersetzerinnen und Übersetzer und für Redaktionen, deren Arbeit vor der Veröffentlichung freigegeben wird.
keywords: Fachbereichsredaktion, Rollenentwurf, Teilbaum-Einschränkung, Mit Einschränkungen zuordnen, Behörden, Ministerien, Fachbereiche, dezentrales Veröffentlichen, gemeinsame Website, Redaktionen, Übersetzer, Freigabe, department editor, role design, subtree limitation, Assign with limitation, agencies, ministries, departments, decentralized publishing, shared website, editors, translators, approval
type: help
---
# Rollenentwurf: Redaktionen der Fachbereiche

Diese Seite richtet sich an Administratoren einer Website, auf der viele Fachbereiche, Behörden oder Ämter ihre eigenen Seiten
veröffentlichen. Das Muster ist eine Rolle, „Fachbereichsredaktion“, die der Benutzergruppe jedes Fachbereichs mit einer
Teilbaum-Einschränkung zugewiesen wird. Es folgt dem Zuweisungsmodell aus [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md#5-die-rolle-zuweisen)
und verwendet nur Richtlinienfunktionen, die in `kernel/content/module.php` und `kernel/user/module.php` definiert sind.

## Inhalt
1. [Das Muster](#das-muster)
2. [Die Rolle „Fachbereichsredaktion“](#die-rolle-fachbereichsredaktion)
3. [Zuweisung je Fachbereich](#zuweisung-je-fachbereich)
4. [Varianten](#varianten)
5. [Prüfen](#prüfen)
6. [Fallstricke](#fallstricke)
7. [Verwandte Seiten](#verwandte-seiten)

## Das Muster

```
Users
  Departments
    Department of Example editors      -> Department editor, limited to /services/example/
    Office of Placeholder editors      -> Department editor, limited to /services/placeholder/
```

Eine einzige Rolle hält die Definition an einem Ort: Eine Änderung (zum Beispiel das Recht, Elemente zu verbergen, hinzuzufügen) gilt
sofort für jeden Fachbereich. Jede Zuweisung trägt die Grenze des Fachbereichs.

## Die Rolle „Fachbereichsredaktion“

| Modul | Funktion | Einschränkungen | Warum |
|---|---|---|---|
| user | login | SiteAccess: der Redaktions- oder Administrations-Siteaccess, den Ihre Redaktionen nutzen | dort anmelden, wo sie arbeiten |
| content | read | Sektion: Standard, Media | die öffentliche Website und die Medienbibliothek sehen |
| content | create | Klasse: Ihre Klassen für Seiten, Artikel, Dateien und Bilder | nur die Inhaltstypen anlegen, die der Fachbereich nutzt |
| content | edit | Sektion: Standard, Media | innerhalb der durch die Zuweisung gegebenen Grenze bearbeiten |
| content | publish, hide | Sektion: Standard | innerhalb der Grenze veröffentlichen und verbergen |
| content | remove | Sektion: Standard | innerhalb der Grenze entfernen (vom Web-Team aus dem Papierkorb wiederherstellbar) |
| content | versionread, diff | Sektion: Standard | eigene Versionen und die von Kolleginnen und Kollegen lesen |
| content | dashboard, pendinglist | keine | das Dashboard, **Meine Entwürfe** und **Meine ausstehenden Elemente** |
| user | password, preferences | keine | das eigene Passwort und die eigenen Einstellungen ändern |

Der Teilbaum kommt aus der Zuweisung, nicht aus den Richtlinien: Mit **Mit Einschränkungen zuordnen: Teilbaum** zugewiesen, gilt jede
Richtlinie der Rolle nur innerhalb dieses Teilbaums. Halten Sie `user/login` und die Dashboard-Funktionen in einer getrennten kleinen Rolle
(„Anmeldung Beschäftigte“, ohne Einschränkung allen Beschäftigten zugewiesen), damit die Anmeldung und das Dashboard nie von einer
Teilbaum-Zuweisung abhängen.

Medien: Wenn Fachbereiche Bilder in die gemeinsame Medienbibliothek hochladen, weisen Sie die Rolle derselben Gruppe ein zweites Mal zu,
mit dem Teilbaum des Medienordners dieses Fachbereichs.

## Zuweisung je Fachbereich

1. Legen Sie die Rolle einmal an ([Eine Rolle für Fachbereichsredaktionen für einen Teilbaum anlegen](../../../../../eng-us/help/government/tasks/people-and-permissions/create-a-department-editor-role-for-a-subtree.md)).
2. Legen Sie je Fachbereich eine Benutzergruppe an ([Eine Benutzergruppe für einen Fachbereich anlegen](../../../../../eng-us/help/government/tasks/people-and-permissions/create-a-user-group-for-a-department.md)).
3. Wählen Sie auf der Rollenseite in der Auswahl der Einschränkung **Teilbaum**, klicken Sie auf **Mit Einschränkungen zuordnen**, wählen
   Sie die oberste Seite des Fachbereichs und dann die Gruppe des Fachbereichs.
4. Wiederholen Sie das für jeden Fachbereich. Die Rollenseite listet dann jede Gruppe mit *Nur im Teilbaum ...*.

## Varianten

| Variante | Änderung |
|---|---|
| Übersetzerinnen und Übersetzer | `content/translate` und `content/edit` mit einer Einschränkung `Language` statt vollem Bearbeiten |
| Redaktionen, deren Arbeit eine Freigabe braucht | dieselbe Rolle; nehmen Sie die Gruppe in den Freigabe-Workflow auf und schließen Sie sie nicht aus ([Das Freigabe-Ereignis einrichten](../../../../../eng-us/help/government/operations/editorial-workflows/setting-up-the-approve-event.md)) |
| Leitende Redakteurinnen und Redakteure, die freigeben | nehmen Sie sie in die Gruppe der Freigebenden auf ([Rollenentwurf: Freigebende](rollenentwurf-freigebende.md)) |
| Redaktionen eines Inhaltstyps auf der ganzen Website (zum Beispiel Nachrichten) | Einschränkung `Class` mit einem Teilbaum des Nachrichtenordners |

## Prüfen

- Melden Sie sich in einem privaten Fenster als Mitglied an: Die Seiten des Fachbereichs haben eine Schaltfläche Bearbeiten, die Seiten
  anderer Fachbereiche nicht.
- Die Rollenseite zählt die **Betroffenen Benutzer insgesamt** und listet jede eingeschränkte Zuweisung; bewahren Sie das als Ihren Nachweis auf.
- Versuchen Sie, eine Seite eines anderen Fachbereichs zu bearbeiten, indem Sie ihre Bearbeitungsadresse eintippen: Die Seite wird
  abgewiesen, und die Abweisung wird als `access.permission.refused` erfasst.

## Fallstricke

- **Herausverschobene Inhalte** verlassen sofort die Reichweite der Redaktion, aus deren Teilbaum sie verschoben wurden; hineinverschobene
  Inhalte gehören dann ihr.
- **Weitere Platzierungen** eines Objekts außerhalb des Teilbaums werden von der Teilbaum-Zuweisung nicht abgedeckt.
- **Bearbeiten öffnet Versionen**: Wer ein Objekt bearbeiten darf, darf auch seine Versionsliste öffnen
  ([Sicherheit und Audit, Schritt 2](../../../../anleitungen/sicherheit-und-audit.md#2-nur-den-zugriff-vergeben-den-personen-brauchen-10-minuten)).
- **Geben Sie Fachbereichsleitungen nicht das Modul `role`**, damit sie ihre eigenen Redaktionen verwalten; dank der Verhinderung der
  Rechteausweitung könnten sie zwar nur vergeben, was sie selbst haben, aber das Modul lässt sie auch Rollen und Zuweisungen anderer
  Fachbereiche bearbeiten und entfernen.

## Verwandte Seiten
- [Sektionen und Teilbaum-Einschränkungen](sektionen-und-teilbaum-einschraenkungen.md)
- [Rollenentwurf: das zentrale Web-Team](rollenentwurf-webteam.md)
- [Eine Rolle mit einer Teilbaum-Einschränkung zuweisen](../../../../../eng-us/help/government/tasks/people-and-permissions/assign-a-role-with-a-subtree-limitation.md)
- [Eine Rolle für einen neuen Fachbereich kopieren](../../../../../eng-us/help/government/tasks/people-and-permissions/copy-a-role-for-a-new-department.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
