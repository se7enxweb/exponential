---
title: Ihr erster Beitrag, Schritt für Schritt
audience: student
category: learning
order: 40
summary: Der erste Beitrag von Lernenden zu Exponential auf drei Wegen - eine Korrektur der Dokumentation über Auf GitHub bearbeiten, eine Übersetzung oder eine kleine Codekorrektur über einen Fork und einen Pull Request -, mit den Befehlen aus Mit Git entwickeln, der Regel für Commit-Nachrichten, was in den Pull Request gehört und was danach passiert.
keywords: erster beitrag, pull request, fork, branch, commit, Auf GitHub bearbeiten, korrektur der dokumentation, übersetzung, codekorrektur, begutachtung, review, squash, rebase, lernende, first contribution, Edit on GitHub, documentation fix, translation, code fix, learner
type: help
---
# Ihr erster Beitrag, Schritt für Schritt

Diese Seite führt Sie durch einen ersten Beitrag. Wählen Sie den Weg, der zu dem passt, was Sie gefunden haben. Alle
Wege enden in einem Pull Request, der öffentlich begutachtet wird.

## Inhalt

1. [Eine kleine, echte Aufgabe wählen](#eine-kleine-echte-aufgabe-wählen)
2. [Weg A: eine Korrektur der Dokumentation](#weg-a-eine-korrektur-der-dokumentation)
3. [Weg B: eine Übersetzung](#weg-b-eine-übersetzung)
4. [Weg C: eine Codekorrektur](#weg-c-eine-codekorrektur)
5. [Den Pull Request schreiben](#den-pull-request-schreiben)
6. [Nachdem Sie ihn eingesendet haben](#nachdem-sie-ihn-eingesendet-haben)

## Eine kleine, echte Aufgabe wählen

Gute erste Aufgaben, nach [Einen ersten Beitrag begleiten](../fuersprecher/einen-ersten-beitrag-begleiten.md#eine-kleine-echte-aufgabe-wählen):

- ein Tippfehler oder ein veralteter Schritt in einer Hilfeseite, die Sie gelesen haben;
- ein fehlender Link auf eine weiterführende Seite;
- eine Handvoll Zeichenketten in Ihrer Sprache;
- ein kleiner Fehler, auf den Sie selbst gestoßen sind, mit einer klaren Lösung.

Vermeiden Sie „Übungs“-Pull-Requests ohne echte Änderung und große Aufgaben. Der erste sollte in ein bis zwei Stunden
fertig sein. Durchsuchen Sie zuerst den Issue-Tracker: Wenn Ihr Problem bekannt ist, ergänzen Sie dieses Issue um das,
was Sie wissen.

## Weg A: eine Korrektur der Dokumentation

1. Drücken Sie auf der Hilfeseite **Auf GitHub bearbeiten** ([Dokumentation beitragen](../unterstuetzer/dokumentation-beitragen.md#eine-seite-in-zwei-minuten-korrigieren)).
2. Ändern Sie den Text im Browser. Behalten Sie den Stil der Seite bei: amerikanisches Englisch, „Exponential“ für
   das Produkt, eine Quelle für jede Angabe.
3. Schreiben Sie eine Commit-Nachricht wie `Fixed: The languages help names the Apply button`.
4. Schlagen Sie die Änderung vor und eröffnen Sie den Pull Request.

Die Hilfeseiten sind die Dateien in `doc/` des Kernels, eine Korrektur der Dokumentation ist also ein Pull Request an
[se7enxweb/exponential](https://github.com/se7enxweb/exponential). Mehr: [Die Hilfe als Lernende verbessern](die-hilfe-als-lernende-verbessern.md).

## Weg B: eine Übersetzung

1. Prüfen Sie die Vollständigkeit der Sprache in `doc/eng-us/languages.md`.
2. Forken Sie das Repository und öffnen Sie `share/translations/<locale>/translation.ts`.
3. Übersetzen Sie nicht übersetzte oder unfertige Meldungen; behalten Sie Platzhalter wie `%1` genau bei.
4. Stellen Sie die Administrationsoberfläche auf Ihre Sprache um und sehen Sie sich die Bildschirme an, die Sie
   übersetzt haben.
5. Committen Sie mit einer Betreffzeile wie `Updated: German translation of the help dashboard` und eröffnen Sie
   einen Pull Request.

Quelle: [Übersetzungen beitragen](../unterstuetzer/uebersetzungen-beitragen.md#wie-sie-übersetzen). Mehr:
[Als Lernende übersetzen](als-lernende-uebersetzen.md).

## Weg C: eine Codekorrektur

Nach [Mit Git entwickeln](../mit-git-entwickeln.md#eine-änderung-per-pull-request-beitragen), für eine Erweiterung wie
`eztags` (beim Kernel funktioniert es genauso):

1. Öffnen Sie das Repository auf GitHub und drücken Sie **Fork**.
2. Fügen Sie Ihren Fork als zweites Remote hinzu und beginnen Sie einen Branch:

   ```bash
   cd extension/eztags
   git remote add fork https://github.com/<you>/eztags.git
   git switch -c fix-tag-search
   ```

3. Nehmen Sie die Änderung vor, testen Sie sie auf Ihrer Lernwebsite und committen Sie sie:

   ```bash
   git commit -am "Fixed: The tag search finds tags with an apostrophe"
   git push -u fork fix-tag-search
   ```

4. Folgen Sie dem Link, den GitHub ausgibt, um einen Pull Request zu eröffnen (oder `gh pr create`).

Lesen Sie für den Kernel selbst zuerst `CONTRIBUTING.md`: Führen Sie einmal `make hooks` aus, halten Sie
`share/filelist.md5` mit `make manifest-fix` aktuell und sorgen Sie dafür, dass Ihre unterzeichnete CLA vor dem Merge
hinterlegt ist ([Die CLA für Lernende](die-cla-fuer-lernende.md)).

## Den Pull Request schreiben

```text
Title:    Fixed: <what the change does, in one line>

What:     <the problem, in two sentences>
Why:      <who it affects>
How I tested it:
          <release, PHP version, steps, what I saw before and after>
CLA:      <signed / not needed (documentation fix in an extension of my own, etc.)>
```

Die Vorlage für Pull Requests des Kernels hat ein Kontrollkästchen für die CLA. Halten Sie personenbezogene Daten,
Passwörter und Serverangaben aus der Beschreibung und den Screenshots heraus.

Wählen Sie für einen ersten Beitrag eine Korrektur, gegen die niemand etwas haben kann. Wenn das Verhalten, das Sie
ändern möchten, gewollt oder Auslegungssache sein könnte, ist es vielleicht eine
[negative Funktion](../projekt/unterstuetzung-erhalten.md#negative-funktionen), auf die sich andere Websites verlassen: Eine
solche Änderung braucht ein starkes „Warum“, wird besser als Option statt als neue Voreinstellung angeboten und kann
mehrere Begutachtungsrunden dauern, bevor sie gemergt wird.

## Nachdem Sie ihn eingesendet haben

- **Rechnen Sie mit Begutachtungsrunden.** 7x begutachtet in viel mehr Runden als üblich; das „ist kein Urteil über
  Sie“ ([Richtlinien für Mitwirkende](../../../../CONTRIBUTING.md#code-reviews)).
- **Beantworten Sie jede Anmerkung** im Pull Request.
- **Fassen Sie mit Squash zusammen und rebasen Sie** auf `main`, wenn Sie darum gebeten werden: `git fetch origin`,
  `git rebase -i origin/main`, `git push --force-with-lease`, nur auf den Branch Ihres Pull Requests.
- **Wenn er gemergt ist**, nehmen Sie ihn in Ihr Portfolio und Ihr Tagebuch auf. Wählen Sie dann die nächste Aufgabe.

## Verwandte Seiten

- [Als Lernende beitragen](als-lernende-beitragen.md)
- [Git als Lernende nutzen](git-als-lernende-nutzen.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
