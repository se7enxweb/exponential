---
title: Aufgaben für Behörden
audience: government, editor, admin, project-manager
category: public-sector
order: 10
summary: Der Index der Schritt-für-Schritt-Aufgaben für Behörden - Veröffentlichen, Compliance, Sites und Sprachen, Betrieb, Personen und Berechtigungen, Beschaffung und Schulung - und wie eine Aufgabenseite aufgebaut ist.
keywords: Aufgaben, Anleitung, Schritt für Schritt, Abläufe, veröffentlichen, Tagesordnung, Protokoll, Bekanntmachung, Warnmeldung, barrierefreies Formular, Abteilungs-Site, übersetzen, archivieren, zweistufige Anmeldung, CSP, Sicherung, Wiederherstellung, Wartungsmodus, Rollen, Berechtigungen, Freigabe, Beschaffung, Schulung, öffentlicher Sektor, Verwaltung, tasks, how to, step by step, procedures, publish, agenda, minutes, notice, alert, two-step sign-in, backup, restore, maintenance mode, roles, permissions, approval, procurement, training, public sector, government
type: help
---
# Aufgaben für Behörden

Dies ist der Index der Aufgabenseiten des Bereichs für Behörden: kurze, konkrete Abläufe für die Arbeit, die eine
Behörde mit Exponential erledigt, von „Die Tagesordnung einer Ratssitzung veröffentlichen“ bis „Einen ausscheidenden
Mitarbeiter entfernen“ oder „Einen Ausstiegsplan schreiben“. Sie richten sich an Redakteure, Site-Administratoren,
IT-Betrieb, Projektleiter und Beschaffungsverantwortliche.

Zurück zum Anfang der Hilfe für Behörden: [Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md).

## Inhalt

1. [Wie eine Aufgabenseite aufgebaut ist](#wie-eine-aufgabenseite-aufgebaut-ist)
2. [Aufgabengruppen](#aufgabengruppen)
3. [Eine Aufgabe finden](#eine-aufgabe-finden)
4. [Bevor Sie mit einer Aufgabe beginnen](#bevor-sie-mit-einer-aufgabe-beginnen)
5. [Verwandte Seiten](#verwandte-seiten)

## Wie eine Aufgabenseite aufgebaut ist

Jede Aufgabenseite hat dieselben Teile, sodass Sie sie schnell überfliegen können:

| Teil | Was er Ihnen sagt |
|---|---|
| Wer und wann | die Rolle, die die Aufgabe erledigt, und der Zeitpunkt, zu dem sie erledigt wird |
| Bevor Sie beginnen | die Rechte, Informationen und Entscheidungen, die Sie brauchen |
| Schritte | nummerierte Schritte mit den Beschriftungen, die die Administration zeigt, oder den Befehlen, die auszuführen sind |
| Ergebnis | wie Sie prüfen, ob die Aufgabe gelungen ist |
| Fallstricke | die Fehler, die häufig gemacht werden, und wie Sie sie vermeiden |
| Verwandte Seiten | die Hintergrundseiten und die Quelldokumentation |

Die Schritte verwenden die Beschriftungen des Administrationsdesigns admin4. Andere Designs (die klassische
Administration, admin3) zeigen dieselben Funktionen in einer anderen Anordnung; die Moduladressen wie
`/content/dashboard` oder `/user/hygiene` sind in jedem Design dieselben.

## Aufgabengruppen

| Gruppe | Für | Beispiele |
|---|---|---|
| [Veröffentlichen](veroeffentlichen/README.md) | Redakteure | die Tagesordnung einer Ratssitzung veröffentlichen, das Protokoll nach der Genehmigung veröffentlichen, eine öffentliche Bekanntmachung veröffentlichen, eine veröffentlichte Bekanntmachung korrigieren |
| [Compliance](../../../../eng-us/help/government/tasks/compliance/README.md) | Administratoren, Sicherheits- und Datenschutzbeauftragte | die zweistufige Anmeldung einschalten, die Content Security Policy durchsetzen, die Aufbewahrung der Logs festlegen, die Bekanntmachungen des Vorjahres archivieren |
| [Sites und Sprachen](../../../../eng-us/help/government/tasks/sites-and-languages/README.md) | Administratoren, Website-Ersteller | eine Seite in die zweite Amtssprache übersetzen, eine Abteilungs-Site einrichten, eine Sprache mit Schreibrichtung von rechts nach links hinzufügen |
| [Betrieb](../../../../eng-us/help/government/tasks/operations/README.md) | IT-Betrieb | Wartungsmodus für ein Upgrade, eine Sicherung auf einem Testserver wiederherstellen, prüfen, ob die Cronjobs laufen |
| [Personen und Berechtigungen](../../../../eng-us/help/government/tasks/people-and-permissions/README.md) | Administratoren | eine Redakteursrolle für eine Abteilung anlegen, einen ausscheidenden Mitarbeiter entfernen, einen Freigabe-Workflow einrichten |
| [Beschaffung und Schulung](../../../../eng-us/help/government/tasks/procurement-and-training/README.md) | Projektleiter, Beschaffungsverantwortliche, Schulungskoordinatoren | eine Anforderung an die Barrierefreiheit schreiben, einen Proof of Concept durchführen, die Schulung der Redakteure planen |


## Eine Aufgabe finden

- **Durchsuchen Sie die Hilfe** nach dem Verb und dem Gegenstand: „Tagesordnung veröffentlichen“, „Seite
  übersetzen“, „ausscheidenden Mitarbeiter entfernen“. Die Suche der Hilfe gewichtet Titel und Überschriften höher als
  den Fließtext, und jeder Aufgabentitel nennt die Handlung.
- **Filtern Sie nach Zielgruppe**: Die Zielgruppe für Behörden listet jede Seite dieses Bereichs; die Zielgruppen für
  Redakteure und Administratoren listen die Aufgaben für diese Rollen in der ganzen Hilfe.
- **Beginnen Sie bei einem Thema**: Jeder Bereich der Hilfe für Behörden verlinkt die Aufgaben, die zu ihm gehören.

## Bevor Sie mit einer Aufgabe beginnen

1. **Arbeiten Sie zuerst auf einer Test-Site**, wenn die Aufgabe Einstellungen, Rollen oder Workflows ändert. Eine
   Kopie der Produktion mit eigener Datenbank ist der sichere Ort zum Üben
   ([Installation mit einem Befehl](../../../../eng-us/features/6.0/install-in-one-command.md) liefert Ihnen schnell eine
   Testinstallation).
2. **Prüfen Sie Ihre Rechte.** Eine Aufgabe in der Administration scheitert mit einer Seite „Zugriff verweigert“, wenn
   Ihrer Rolle die Richtlinie fehlt; fragen Sie die Person, die die Rollen verwaltet
   ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
3. **Wissen Sie, wohin Einstellungen gehören.** Site-spezifische Einstellungen gehören nach `settings/override/` oder
   in den Ordner des Siteaccess, niemals in die mitgelieferten Dateien `settings/*.ini`
   ([Einstellungsseite](../../../anleitungen/einstellungsseite.md)).
4. **Leeren Sie die richtigen Caches** nach einer Änderung an Einstellungen oder Templates
   ([Caches](../../../anleitungen/caches.md)).
5. **Halten Sie fest, was Sie geändert haben**, in Ihrem Änderungsprotokoll; das Audit-Protokoll zeichnet auf, wer
   in der Administration was getan hat ([Audit-Protokoll](../../../../eng-us/features/6.0/audit-trail.md)).

## Verwandte Seiten

- [Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md)
- [Inhalte bearbeiten](../../inhalte-bearbeiten.md) und [Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md)
- [Eine Site betreiben](../../../anleitungen/betrieb-einer-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
