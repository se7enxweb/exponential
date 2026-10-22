---
title: Die Arbeit mit Ehrenamtlichen teilen
audience: individual, website-creator, customer
category: personal-sites
order: 1716
summary: Wie die eine ehrenamtliche Person, die eine Vereinsseite mit Exponential betreibt, aufhört, die einzige zu sein - Rollen für Redakteure und eine Vertretung des Administrators, Freigabe vor dem Veröffentlichen, ein kleines Handbuch, zweistufige Anmeldung und eine Übergabe, die beim Wechsel von Ehrenamtlichen funktioniert.
keywords: Ehrenamtliche, Arbeit teilen, Redakteure, Rollen, Rolle Editor, Vertretung, Freigabe-Workflow, Handbuch, Übergabe, Kontinuität, Zwei-Faktor, Passwörter, Verein, Vorstand, Busfaktor, volunteers, sharing work, editors, deputy, approval workflow, handbook, handover, bus factor
type: help
---
# Die Arbeit mit Ehrenamtlichen teilen

Diese Seite ist für Privatpersonen, die eine Vereinsseite betreiben und nicht die einzige Person sein möchten, die
das Spielergebnis einstellen oder ein Passwort zurücksetzen kann. Exponential ist für mehrere Personen mit
unterschiedlichen Rechten gebaut; wer das früh nutzt, hält die Website am Leben, wenn er beschäftigt oder krank ist
oder aufhört.

## Inhalt

1. [Wer welche Rechte braucht](#wer-welche-rechte-braucht)
2. [Mitgelieferte Rollen](#mitgelieferte-rollen)
3. [Eine ehrenamtliche Redakteurin oder einen Redakteur einrichten](#eine-ehrenamtliche-redakteurin-oder-einen-redakteur-einrichten)
4. [Freigabe vor dem Veröffentlichen](#freigabe-vor-dem-veröffentlichen)
5. [Die Vertretung des Administrators](#die-vertretung-des-administrators)
6. [Ein einseitiges Handbuch](#ein-einseitiges-handbuch)
7. [Übergabe](#übergabe)
8. [Checkliste](#checkliste)

## Wer welche Rechte braucht

| Ehrenamtliche Person | Braucht | Braucht nicht |
|---|---|---|
| Schreibt Neuigkeiten | Neuigkeiten und Veranstaltungen erstellen und bearbeiten | Benutzer, Einstellungen, Klassen |
| Fotografiert | Bilder in Galerien hochladen | Alles andere |
| Versendet den Newsletter | Das Newsletter-Modul | Inhalte außerhalb des Newsletters |
| Führt die Mitgliederliste | Benutzer zwischen Gruppen verschieben, Konten deaktivieren | Einstellungen, Klassen |
| Vertritt den Administrator | Alles | |

Geben Sie jeder Person das Mindeste, was sie braucht. Wer nur Neuigkeiten bearbeiten kann, kann die Website nicht
versehentlich beschädigen.

## Mitgelieferte Rollen

Eine neue Installation hat eine Rolle *Editor* und eine Rolle *Administrator*, und das Standard-Seitenpaket passt
die Richtlinien der Rolle Editor bei der Installation an (`sevenx-multi-site-installer.php`). Sehen Sie unter
**Benutzer > Rollen** nach, was jede Rolle auf Ihrer Website genau erlaubt
([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md),
[Rollen für Ihr Team](../../kunden/alltag/rollen-fuer-ihr-team.md)).

Für Ehrenamtliche mit engerem Aufgabenbereich kopieren Sie die Rolle Editor und beschränken sie auf einen Teilbaum
(nur Neuigkeiten und Kalender) oder auf bestimmte Klassen
([Rollen für neue Mitarbeiter wählen](../../kunden/alltag/rollen-fuer-neue-mitarbeitende-waehlen.md)).

## Eine ehrenamtliche Redakteurin oder einen Redakteur einrichten

1. Legen Sie eine Benutzergruppe „Ehrenamtliche" an und weisen Sie ihr eine Rolle zu, die auf die Teile beschränkt
   ist, die sie bearbeiten.
2. Legen Sie das persönliche Konto der Person in dieser Gruppe an
   ([Familienmitglieder einladen](../familienseiten/familienmitglieder-einladen.md#ein-konto-selbst-anlegen) funktioniert
   genauso).
3. Zeigen Sie ihr einmal den Editor oder die Administration ([Anmelden im Editor](../../anmelden-im-editor.md),
   [Inhalte bearbeiten](../../inhalte-bearbeiten.md)).
4. Bitten Sie sie, die zweistufige Anmeldung einzuschalten ([Zwei-Faktor-Anmeldung](../im-betrieb/zwei-faktor-anmeldung.md)).
5. Prüfen Sie ihren ersten Beitrag gemeinsam.

## Freigabe vor dem Veröffentlichen

Wenn mehrere Personen im Namen des Vereins schreiben, hält ein Freigabe-Workflow die Änderungen einer ehrenamtlichen
Person zurück, bis eine freigebende Person sie veröffentlicht
([Freigaben und Prüfung](../../kunden/alltag/freigabe-und-pruefung.md),
[Workflows](../../../anleitungen/workflows.md)). Bei zwei oder drei vertrauenswürdigen Ehrenamtlichen kann das mehr Mühe
machen, als es bringt; bei einer größeren Gruppe oder bei allem, was Mitglieder betrifft, lohnt es sich.

## Die Vertretung des Administrators

Immer zwei Administratoren. Die Vertretung:

- hat ein persönliches Administratorkonto (nie eine gemeinsame „admin"-Anmeldung);
- weiß, wo das Hostingkonto, die Domain und die Sicherungen sind;
- kann eine Sicherung wiederherstellen oder weiß, wer es kann ([Sicherungen für eine Person](../im-betrieb/sicherungen-fuer-eine-person.md));
- wird informiert, wenn Sie etwas Wichtiges ändern.

## Ein einseitiges Handbuch

Legen Sie im Bereich für Mitglieder oder Ehrenamtliche einen Artikel an mit:

1. Wie man sich anmeldet und wo man um Hilfe bittet.
2. Wie man Neuigkeiten einstellt (mit zwei Bildschirmfotos).
3. Wie man eine Veranstaltung hinzufügt.
4. Wie man den Newsletter versendet, falls die Person das tut.
5. Hausregeln: Fotos nur mit Einwilligung, keine Mitgliederdaten auf öffentlichen Seiten.
6. Wen man anruft, wenn etwas kaputt ist.

Halten Sie es so kurz, dass es tatsächlich gelesen wird.

## Übergabe

Wenn Sie aufhören, gehen Sie mit Ihrer Nachfolge die Seite
[Übergabe beim Wechsel von Ehrenamtlichen](../../unternehmen/vereine/uebergabe-bei-wechsel-der-ehrenamtlichen.md) durch und
dann:

- [ ] Die Nachfolge hat ein persönliches Administratorkonto; Ihres wird nach der Übergabe deaktiviert.
- [ ] Hosting- und Domainkonten laufen auf den Namen des Vereins, mit den Kontaktdaten der Nachfolge.
- [ ] Sicherungen einmal gemeinsam getestet.
- [ ] Handbuch aktualisiert.
- [ ] Offene Aufgaben aufgeschrieben.

## Checkliste

- [ ] Mindestens zwei Administratoren mit persönlichen Konten.
- [ ] Ehrenamtliche haben nur die Rechte, die sie brauchen.
- [ ] Zweistufige Anmeldung für alle, die veröffentlichen können.
- [ ] Freigabe-Workflow, wo mehrere Personen schreiben.
- [ ] Ein einseitiges Handbuch.
- [ ] Übergabeliste bereit, bevor Sie sie brauchen.

## Verwandte Seiten

- [Vereinsseiten](README.md)
- [Gemeinsames Bearbeiten mit Ehrenamtlichen](../../unternehmen/vereine/gemeinsam-bearbeiten-mit-ehrenamtlichen.md)
- [Übergabe an Redakteure](../../ersteller/website-ersteller/die-website-an-die-redaktion-uebergeben.md)
- [Ausscheiden von Mitarbeitern](../../kunden/alltag/ausscheiden-von-mitarbeitenden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
