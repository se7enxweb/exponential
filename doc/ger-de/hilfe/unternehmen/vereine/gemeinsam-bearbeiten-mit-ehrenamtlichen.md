---
title: Gemeinsam bearbeiten mit Ehrenamtlichen
audience: businesses, editor, admin
category: business-solutions
order: 20
summary: Wie eine kleine Gruppe mehrere Ehrenamtliche ihre Exponential-Website sicher bearbeiten lässt - ein Konto pro Person, wenige einfache, auf Sektionen beschränkte Rollen, zweistufige Anmeldung, auf Wunsch eine Freigabe vor dem Veröffentlichen, Versionen zum Rückgängigmachen von Fehlern, das Prüfprotokoll und Hausregeln, an die sich alle halten können.
keywords: ehrenamtliche Redakteure, gemeinsames Bearbeiten, Rollen, Richtlinien, Berechtigungen, zweistufige Anmeldung, Freigabe-Workflow, Versionen, rückgängig machen, Prüfprotokoll, Hausregeln, kleine Gruppe, volunteer editors, shared editing, roles, policies, permissions, two-step sign-in, approval workflow, versions, undo, audit trail, house rules, small group
type: help
---
# Gemeinsam bearbeiten mit Ehrenamtlichen

Diese Seite ist für den Web-Koordinator oder Administrator eines Vereins, einer Gesellschaft oder einer anderen
ehrenamtlich geführten Gruppe. Sie erklärt, wie mehrere Ehrenamtliche bearbeiten können, ohne einander in die Quere zu
kommen, wie Sie den Schaden eines Fehlers begrenzen und wie Sie ihn rückgängig machen. Sie beschränkt sich auf die
wenigen Funktionen, die eine kleine Gruppe braucht; die vollständigen Anleitungen sind verlinkt.

## Inhalt

1. [Ein Konto pro Person](#ein-konto-pro-person)
2. [Wenige einfache Rollen](#wenige-einfache-rollen)
3. [Zweistufige Anmeldung](#zweistufige-anmeldung)
4. [Freigabe vor dem Veröffentlichen](#freigabe-vor-dem-veröffentlichen)
5. [Fehler mit Versionen rückgängig machen](#fehler-mit-versionen-rückgängig-machen)
6. [Wer was getan hat](#wer-was-getan-hat)
7. [Dieselbe Seite zur selben Zeit bearbeiten](#dieselbe-seite-zur-selben-zeit-bearbeiten)
8. [Hausregeln](#hausregeln)
9. [Ehrenamtlichen den Einstieg erleichtern](#ehrenamtlichen-den-einstieg-erleichtern)

## Ein Konto pro Person

„Ein persönliches Konto pro Person; keine gemeinsam genutzten Zugangsdaten“
([Die Website an Redakteure übergeben, Konten und Rollen](../../ersteller/website-ersteller/die-website-an-die-redaktion-uebergeben.md#konten-und-rollen)).
Für eine Gruppe ist das wichtiger als für ein Unternehmen: Wenn ein Ehrenamtlicher geht, deaktivieren Sie ein Konto,
statt ein Passwort zu ändern, das alle kannten, und das Prüfprotokoll kann zeigen, wer was getan hat.

## Wenige einfache Rollen

„Eine **Rolle** ist eine Menge von **Richtlinien**“, und „eine Rolle bewirkt nichts, bis sie Benutzern oder
Benutzergruppen **zugewiesen** wird“
([Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md)). Eine kleine Gruppe
braucht selten mehr als vier:

| Rolle | Darf | Beschränken auf |
|---|---|---|
| Autor | Entwürfe anlegen und bearbeiten | Die eigene Sektion (Neuigkeiten, Veranstaltungen, die Seiten einer Arbeitsgruppe) |
| Redakteur | Veröffentlichen | Die öffentlichen Sektionen |
| Formularleser | Formulareinsendungen lesen (`infocollector/read`) | Sonst nichts |
| Administrator | Alles | Nur zwei Personen |

Richtlinien lassen sich auf Sektionen, Klassen, Teilbäume, Sprachen oder Siteaccesses beschränken (dieselbe
Anleitung), sodass das Veranstaltungsteam nur den Kalender bearbeiten kann. Weisen Sie Rollen **Benutzergruppen** zu
(„Autoren“, „Redakteure“) statt einzelnen Personen; ein neuer Ehrenamtlicher tritt dann einfach einer Gruppe bei.

> **Warnung**
> Die Anleitung zu Rollen kennzeichnet zwei Arten von Rollen farbig: Eine Richtlinie über alle Module gibt vollen
> Zugriff (rot), und eine Richtlinie über das Rollenmodul erlaubt ihren Benutzern, sich selbst jeden Zugriff zu geben
> (gelb). Behalten Sie beide den Administratoren vor.

## Zweistufige Anmeldung

Die zweistufige Anmeldung fügt nach dem Passwort einen Code aus einer Authenticator-App oder einer E-Mail hinzu,
sodass „das Passwort allein für andere dann nutzlos ist“. Sie wird von `sevenx_authentication_2fa` bereitgestellt,
das `composer.json` voraussetzt und die Installationsprogramme aktivieren; das Einschalten dauert etwa 15 Minuten und
die Einrichtung pro Benutzer 2 Minuten ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)). Verwenden Sie
sie mindestens für alle, die veröffentlichen dürfen; Ehrenamtliche verwenden Passwörter oft auf mehreren Websites.

## Freigabe vor dem Veröffentlichen

Wenn neue Ehrenamtliche nicht direkt veröffentlichen sollen, verwenden Sie einen Freigabe-Workflow: „Vor dem
Veröffentlichen von Inhalten den Freigabe-Workflow ausführen“. Die Version wartet dann als „ausstehend“, bis ein
Freigebender entscheidet, und der Cronjob des Workflows veröffentlicht sie nach der Freigabe
([Workflows](../../../anleitungen/workflows.md)). Die Anleitung enthält ein durchgerechnetes Beispiel
([Freigabe vor dem Veröffentlichen](../../../anleitungen/workflows.md#3-durchgerechnetes-beispiel-freigabe-vor-dem-veröffentlichen)).

Halten Sie es schlank: Freigabe für neue Autoren in ihren ersten Monaten, direktes Veröffentlichen für erfahrene
Redakteure. „Ohne diesen Cronjob werden freigegebene Inhalte nie veröffentlicht“, prüfen Sie also, dass Cron läuft.

## Fehler mit Versionen rückgängig machen

Jede Veröffentlichung eines Objekts ist eine Version. Auf der Versionsseite können Redakteure zwei Versionen
vergleichen, aus einer alten Version einen neuen Entwurf machen und ihn veröffentlichen sowie alte Entwürfe entfernen
([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)). Für Redakteure siehe auch
[Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md). Sagen Sie das den Ehrenamtlichen an
ihrem ersten Tag: Die meisten Fehler sind mit einem Klick rückgängig gemacht, sie brauchen also keine Angst vor dem
Bearbeiten zu haben.

Inhalte, die in den Papierkorb verschoben wurden, lassen sich mit der Schaltfläche **Wiederherstellen**
zurückholen, und die Papierkorbansicht zeigt, wer ein Element in den Papierkorb gelegt hat und wo es war
([Die Papierkorbansicht](../../../bc/6.0/papierkorb.md)). Benutzerkonten sind die Ausnahme: „Ein Benutzerobjekt kommt nie
in den Papierkorb“, das Entfernen eines Kontos ist also endgültig
([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).

## Wer was getan hat

Das Prüfprotokoll hält „Anmeldungen und Abweisungen, Änderungen an Inhalten, Schreibvorgänge an Einstellungen, das
Leeren von Caches, Deployments, Bestellungen und Änderungen an der USt“ in einem manipulationssicheren Protokoll fest,
das „standardmäßig eingeschaltet ist und keine Einrichtung braucht“
([Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)). Für eine Gruppe beantwortet es die Frage „Wer hat den
Termin des Treffens geändert?“ ohne Schuldzuweisung, und es zeigt dem nächsten Administrator, was vor ihm geschah.

## Dieselbe Seite zur selben Zeit bearbeiten

- Inhalte: Jeder Redakteur arbeitet in seinem eigenen Entwurf; die Versionsseite zeigt Entwürfe anderer.
- Layouts: „Entwürfe gelten pro Layout, nicht pro Benutzer: Zwei Personen, die dasselbe Layout bearbeiten,
  überschreiben einander“
  ([Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md#entwürfe-und-veröffentlichen)).
  Vereinbaren Sie, dass immer nur eine Person zur selben Zeit das Layout der Startseite bearbeitet.

## Hausregeln

Aus [Die Website an Redakteure übergeben, Hausregeln für Inhalte](../../ersteller/website-ersteller/die-website-an-die-redaktion-uebergeben.md#hausregeln-für-inhalte),
plus zwei für Gruppen:

- [ ] Titel kurz und klar; aus ihnen werden Adressen.
- [ ] Jedes Bild mit einem Alternativtext.
- [ ] Linktexte, die sagen, wohin sie führen.
- [ ] Überschriften in der richtigen Reihenfolge; keine fetten Absätze als Überschriften.
- [ ] Entfernen statt für immer verbergen; bitten Sie um eine Weiterleitung, wenn eine Seite wegfällt.
- [ ] Keine personenbezogenen Daten von Mitgliedern auf öffentlichen Seiten.
- [ ] Fragen Sie, bevor Sie eine Seite ändern, für die ein anderer Ehrenamtlicher verantwortlich ist.

## Ehrenamtlichen den Einstieg erleichtern

1. Legen Sie das Konto in der richtigen Benutzergruppe an; bitten Sie die Person, die zweistufige Anmeldung
   einzurichten.
2. Gehen Sie den Rundgang durch die Administration mit Ihren eigenen Inhalten durch
   ([Ein Rundgang durch die Administration](../../rundgang-durch-die-administration.md)); „eine Schulung, die dem Rundgang durch die
   Administration mit den eigenen Inhalten der Website folgt, bringt die meisten Redakteure von null zur Arbeitsfähigkeit“
   ([Die Website an Redakteure übergeben, Schulung](../../ersteller/website-ersteller/die-website-an-die-redaktion-uebergeben.md#schulung)).
3. Geben Sie ihnen die Hausregeln und die Hilfeseiten [Erste Schritte](../../erste-schritte.md) und
   [Inhalte erstellen und bearbeiten](../../inhalte-bearbeiten.md).
4. Lassen Sie sie den Seiten folgen, für die sie zuständig sind, damit sie über Änderungen benachrichtigt werden
   ([Benachrichtigungen](../../../funktionen/6.0/benachrichtigungen.md)).

## Verwandte Seiten

- [Vereine und kleine Gruppen](README.md)
- [Übergabe bei Wechsel der Ehrenamtlichen](uebergabe-bei-wechsel-der-ehrenamtlichen.md)
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md)
- [Zugriffsprüfungen](../sicherheit/zugriffspruefungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
