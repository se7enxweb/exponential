---
title: "Private Inhalte privat halten"
audience: admin, website-creator, editor, hosting, government
category: administration
order: 17
summary: Wie statisches Veröffentlichen dafür sorgt, dass nur öffentliche Inhalte in ein öffentliches Repository kommen - Builds als anonymer Benutzer, das Bereinigen und die Ablehnungsprüfungen, Seiten und Sektionen auslassen, und vor jedem öffentlichen Push die Änderungen lesen.
keywords: Statisches Veröffentlichen, Datenschutz, private Inhalte, anonymer Benutzer, auslassen, ablehnen, Sitzungscookie, Formular-Token, Debug-Ausgabe, öffentliches Repository, Änderungen prüfen, DSGVO, static publishing, privacy, private content, anonymous user, exclude, ExcludePaths, ExcludeSections, refuse, session cookie, form token, public repository, diff, GDPR, GitHub Pages
featured: true
type: help
---
# Private Inhalte privat halten

Ein öffentliches Repository ist vollständig öffentlich, auch sein Verlauf: Eine Seite, die einmal gepusht wurde, ist
wieder auffindbar, selbst wenn die nächste Veröffentlichung sie entfernt. Statisches Veröffentlichen ist so gebaut, dass
das nicht aus Versehen geschieht; diese Seite zeigt die Schutzmaßnahmen und was Ihnen bleibt.

## Was Sie am Ende haben

Ein Ziel, das nur veröffentlicht, was jeder Besucher ohnehin sehen darf, und die Gewohnheit, jede Veröffentlichung zu
prüfen.

## Was Exponential für Sie tut

1. **Baut als nicht angemeldeter Besucher.** Der Build sendet kein Cookie, also ist jede Seite das, was der anonyme
   Benutzer sieht. Inhalte hinter einer Anmeldung, Mitgliederseiten und Entwürfe werden nie abgerufen.
2. **Bereinigt jede Seite.** Debug-Ausgabe, Formular-Token, Sitzungs-IDs in Adressen und Sicherheits-Nonces werden
   herausgenommen.
3. **Lehnt ab, was es nicht bereinigen kann.** Eine Seite, die noch eines davon enthält oder deren Antwort eine
   Sitzung gestartet hat, stoppt den Build. Nichts wird geschrieben und nichts veröffentlicht.
4. **Kopiert nur öffentliche Dateien.** Speicherbilder nur der veröffentlichten Version von Inhalten, die der anonyme
   Benutzer lesen darf; nie Bilder von Entwürfen oder hochgeladene Originale, die eine Seite nicht jedem Besucher
   anbietet.
5. **Hält Geheimnisse heraus.** Token und Schlüssel sind Namen in den Einstellungen; ihre Werte erreichen nie eine
   Seite, eine Befehlszeile oder ein Log.

## Was Sie entscheiden

- **Wer was lesen darf.** Die Rolle des anonymen Benutzers bestimmt, was die Kopie enthält. Eine Sektion, die intern
  bleiben soll, darf für Anonymous nicht lesbar sein oder muss ausgelassen werden.
- **Auslassen** von Seiten nach Pfad (`/members/*`) oder nach Sektion, in Schritt 7 des Assistenten. Links auf sie
  führen zu Ihrer Live-Website.
- **Die Änderungen lesen** vor jeder Veröffentlichung: Die Vorschau listet jede Seite und Datei, die hinzukommt, sich
  ändert oder wegfällt. Veröffentlichen verlangt, dass Sie die Prüfung bestätigen.
- **Wer veröffentlichen darf**: die Richtlinie `staticpublish/publish` und **Bestätigen Sie, dass Sie es sind** bei
  jeder Veröffentlichung ([Wer veröffentlichen darf](wer-veroeffentlichen-darf-rollen-und-richtlinien.md)).
- **Öffentliches oder privates Repository.** Kostenlose GitHub-Pläne liefern nur öffentliche Repositorys aus. Inhalte,
  die vertraulich bleiben müssen, gehören in ein privates Repository eines bezahlten Plans oder zu einem anderen Host,
  nicht auf eine kostenlose öffentliche Website.

## Wenn etwas Privates veröffentlicht wurde

Veröffentlichen Sie erneut ohne es oder nehmen Sie zurück ([Verlauf und Rücknahme](verlauf-und-ruecknahme.md)). Die
Seite verschwindet dann von der Website, bleibt aber im Verlauf des Repositorys. Um sie auch aus dem Verlauf zu
entfernen, müssen Sie das Repository auf GitHub umschreiben oder es löschen und in ein neues veröffentlichen; bitten Sie
bei Bedarf den Support von GitHub, zwischengespeicherte Ansichten zu leeren. Ändern Sie jedes Passwort und jede
personenbezogene Angabe, die offengelegt wurde.

## Nächster Schritt

[Build, Vorschau und Veröffentlichen](build-vorschau-und-veroeffentlichen.md).
