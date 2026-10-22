---
title: Ehrenamtliche als Redakteure
audience: non-profit, editor, admin, businesses
category: non-profits
order: 60
summary: Wie eine Non-Profit-Organisation Ehrenamtliche ihre Exponential-Website bearbeiten lässt, ohne sie zu gefährden - ein Konto pro Person, auf Bereiche beschränkte Gruppen und Rollen, Freigabe vor dem Veröffentlichen mit dem dafür nötigen Cronjob, Anmeldung in zwei Schritten mit sevenx_authentication_2fa, Versionen und Papierkorb zum Rückgängigmachen von Fehlern, das Audit-Protokoll, Layouts nur von einer Person zur Zeit bearbeiten und Seiten, die nur Mitarbeitende ändern dürfen.
keywords: ehrenamtliche Redakteure, Rollen, Gruppen, Einschränkung auf Bereiche, Freigabe-Workflow, ausstehend, Cronjob, Zwei-Faktor-Anmeldung, sevenx_authentication_2fa, Versionen, Papierkorb, Audit-Protokoll, Layout-Entwürfe, Hausregeln, volunteer editors, roles, groups, section limitation, approval workflow, pending, two-factor login, versions, trash, audit trail, layouts drafts, house rules
type: help
---
# Ehrenamtliche als Redakteure

Diese Seite richtet sich an Mitarbeitende, die entscheiden, wer die Website ändern darf, und an den Administrator, der
das einrichtet. Viele Websites von Non-Profit-Organisationen werden von Ehrenamtlichen am Leben gehalten, die
Neuigkeiten schreiben, Veranstaltungen aktualisieren und Seiten übersetzen. Das funktioniert gut, wenn jeder
Ehrenamtliche genau seine Aufgabe erledigen kann und jeder Fehler rückgängig zu machen ist. Dieselben Ratschläge für
kleine Gruppen stehen in [Gemeinsames Bearbeiten mit Ehrenamtlichen](../../unternehmen/vereine/gemeinsam-bearbeiten-mit-ehrenamtlichen.md);
diese Seite passt sie an eine Non-Profit-Organisation an, in der bezahlte Mitarbeitende und Ehrenamtliche Seite an
Seite arbeiten.

## Inhalt

1. [Grundsätze](#grundsätze)
2. [Konten](#konten)
3. [Gruppen und Rollen](#gruppen-und-rollen)
4. [Seiten, die nur Mitarbeitende ändern dürfen](#seiten-die-nur-mitarbeitende-ändern-dürfen)
5. [Freigabe vor dem Veröffentlichen](#freigabe-vor-dem-veröffentlichen)
6. [Anmeldung in zwei Schritten](#anmeldung-in-zwei-schritten)
7. [Fehler rückgängig machen](#fehler-rückgängig-machen)
8. [Wer hat was getan](#wer-hat-was-getan)
9. [Gleichzeitiges Bearbeiten](#gleichzeitiges-bearbeiten)
10. [Checkliste](#checkliste)

## Grundsätze

- **Ein Konto pro Person.** Keine gemeinsam genutzten Zugänge.
- **Die kleinste Rolle, die für die Aufgabe genügt.**
- **Jeder Fehler umkehrbar.** Versionen, Papierkorb, Freigabe.
- **Jede Handlung nachvollziehbar.** Das Audit-Protokoll, ohne Schuldzuweisung.

## Konten

„Ein persönliches Konto pro Person; keine gemeinsam genutzten Zugänge“
([Die Website an Redakteure übergeben](../../ersteller/website-ersteller/die-website-an-die-redaktion-uebergeben.md#konten-und-rollen)).
Wenn ein Ehrenamtlicher geht, deaktivieren Sie ein Konto, statt ein Passwort zu ändern, das alle kannten
([Wenn Ehrenamtliche gehen](../betrieb/wenn-ehrenamtliche-gehen.md)).

## Gruppen und Rollen

Weisen Sie Rollen Benutzergruppen zu, nicht Personen; ein neuer Ehrenamtlicher tritt dann einfach einer Gruppe bei.
Richtlinien lassen sich auf Bereiche, Klassen, Teilbäume, Sprachen oder Siteaccesses beschränken
([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

| Gruppe | Rolle | Beschränkt auf |
|---|---|---|
| Ehrenamtliche Autoren | Entwürfe anlegen und bearbeiten, über Freigabe veröffentlichen | Ihren Bereich: Neuigkeiten, Veranstaltungen, die Seiten eines Projekts |
| Ehrenamtliche Übersetzer | Übersetzen | Eine Sprache ([Rollen und Berechtigungen für Übersetzer](../../verwaltung/sprachen/rollen-und-rechte-fuer-uebersetzer.md)) |
| Redakteure unter den Mitarbeitenden | Veröffentlichen | Die öffentlichen Bereiche |
| Koordinatoren | `infocollector/read` | Nichts weiter |
| Administratoren | Alles | Zwei Personen, nach Möglichkeit Mitarbeitende |

Füllen Sie das [Arbeitsblatt: Rollen und Berechtigungen](../vorlagen/arbeitsblatt-rollen-und-berechtigungen.md) aus, bevor
Sie Konten anlegen.

> **Warnung**
> Eine Richtlinie über alle Module gibt vollen Zugriff, und eine Richtlinie über das Rollenmodul erlaubt ihren
> Inhabern, sich selbst jeden Zugriff zu geben. Behalten Sie beides den Administratoren vor
> ([Gemeinsames Bearbeiten mit Ehrenamtlichen](../../unternehmen/vereine/gemeinsam-bearbeiten-mit-ehrenamtlichen.md#wenige-einfache-rollen)).

## Seiten, die nur Mitarbeitende ändern dürfen

Manche Seiten haben rechtliches oder finanzielles Gewicht. Legen Sie sie in einen Bereich, den die Rollen der
Ehrenamtlichen nicht abdecken:

- die Spendenschaltfläche und die Spendenseite ([Vertrauen der Spender und Dank](../spenden/vertrauen-der-spender-und-dank.md));
- die Datenschutzerklärung und die Erklärung zur Barrierefreiheit;
- Jahresveröffentlichungen und Finanzdokumente ([Transparenz](../transparenz/README.md));
- Vorstandsseiten und Dokumente zur Organisationsführung.

Bereiche werden in [Bereiche und Objektzustände](../../ersteller/website-ersteller/sektionen-und-objektzustaende.md)
erklärt.

## Freigabe vor dem Veröffentlichen

Mit einem Freigabe-Workflow wartet die Version eines Ehrenamtlichen als „ausstehend“, bis jemand mit
Freigaberecht entscheidet, und der Workflow-Cronjob veröffentlicht sie nach der Freigabe. „Ohne diesen Cronjob werden
freigegebene Inhalte nie veröffentlicht“ ([Workflows](../../../anleitungen/workflows.md),
[Freigaben und Prüfung](../../kunden/alltag/freigabe-und-pruefung.md)).

Halten Sie es schlank: Freigabe für neue Autoren in ihren ersten Monaten, direktes Veröffentlichen für erfahrene.
Benennen Sie mindestens zwei Personen mit Freigaberecht, damit während der Ferien nichts liegen bleibt.

## Anmeldung in zwei Schritten

Die Anmeldung in zwei Schritten ergänzt das Passwort um einen Code aus einer Authenticator-App oder einer E-Mail. Sie
wird von `sevenx_authentication_2fa` bereitgestellt, das `composer.json` voraussetzt
([Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)). Verwenden Sie sie für alle, die veröffentlichen
können, und immer für Administratoren und Koordinatoren: Ehrenamtliche verwenden Passwörter oft auf mehreren Websites.

## Fehler rückgängig machen

- **Versionen.** Jede Veröffentlichung ist eine Version; Redakteure können zwei Versionen vergleichen und eine alte
  erneut veröffentlichen ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md),
  [Entwürfe, Versionen und Rückgängigmachen](../../kunden/alltag/entwuerfe-versionen-und-rueckgaengig.md)).
- **Papierkorb.** Entfernte Inhalte lassen sich wiederherstellen, und die Ansicht des Papierkorbs zeigt, wer sie
  hineingelegt hat ([Archivieren und der Papierkorb](../../kunden/alltag/archivieren-und-der-papierkorb.md)).
- **Ausnahme:** Benutzerkonten kommen nicht in den Papierkorb; das Entfernen eines Kontos ist endgültig
  ([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).

Sagen Sie das den Ehrenamtlichen am ersten Tag. Menschen bearbeiten besser, wenn sie keine Angst haben, etwas kaputt
zu machen.

## Wer hat was getan

Das Audit-Protokoll zeichnet Inhaltsänderungen, Anmeldungen und mehr auf und ist standardmäßig eingeschaltet
([Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md)). Es beantwortet „Wer hat das Datum der Veranstaltung
geändert?“ ohne Rätselraten und zeigt der nächsten Koordinatorin, was vor ihrer Zeit geschah. Zum Lesen ist
`audit/read` erforderlich.

## Gleichzeitiges Bearbeiten

- Inhalte: Jeder Redakteur arbeitet in seinem eigenen Entwurf.
- Layouts: „Entwürfe gibt es pro Layout, nicht pro Benutzer: Zwei Personen, die dasselbe Layout bearbeiten,
  überschreiben sich gegenseitig“
  ([Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md#entwürfe-und-veröffentlichen)).
  Vereinbaren Sie, dass jeweils nur eine Person das Layout einer Kampagne oder der Startseite bearbeitet.

## Checkliste

- [ ] Ein Konto pro Person; Gruppen, nicht Einzelpersonen, haben die Rollen.
- [ ] Rollen der Ehrenamtlichen auf ihre Bereiche beschränkt.
- [ ] Sensible Seiten in einem Bereich nur für Mitarbeitende.
- [ ] Freigabe-Workflow für neue Autoren; Cronjob läuft; zwei Personen mit Freigaberecht benannt.
- [ ] Anmeldung in zwei Schritten für alle, die veröffentlichen.
- [ ] Ehrenamtliche geschult ([Ehrenamtliche Redakteure schulen](ehrenamtliche-redakteure-schulen.md)).

## Verwandte Seiten

- [Ehrenamtliche](README.md)
- [Rollen für Ihr Team](../../kunden/alltag/rollen-fuer-ihr-team.md)
- [Rollen für neue Mitarbeitende wählen](../../kunden/alltag/rollen-fuer-neue-mitarbeitende-waehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
