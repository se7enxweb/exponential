---
title: "Wenn ein Release schiefgeht"
audience: designer, developer
category: theme-distribution
order: 160
summary: Was zu tun ist, wenn ein veröffentlichtes Theme-Release fehlerhaft ist - es stehen lassen, mit der nächsten Version nach vorn korrigieren, klar sagen, was falsch war, und Nutzern sagen, auf welche Version sie wechseln sollen.
keywords: fehlerhaftes Release, nach vorn korrigieren, schlechtes Release, Hotfix, nächste Version, Tag nicht löschen, Release-Fehler, broken release, fix forward, bad release, hotfix, next version, release mistake
type: help
---
# Wenn ein Release schiefgeht

Diese Seite ist für Designer, die einen Fehler in einem bereits veröffentlichten Release entdecken: eine fehlende Datei,
ein fehlerhaftes Template, die falsche Version in `ezinfo.php`.

## Was Sie nicht tun

- Löschen, verschieben oder erstellen Sie den Tag nicht neu.
- Ersetzen Sie keine Dateien des GitHub-Releases.
- Verwenden Sie die Versionsnummer nicht erneut.

Jedes davon lässt manche Nutzer mit dem einen Inhalt und andere mit einem anderen unter derselben Nummer zurück
([Ein gepushter Tag ist endgültig](ein-gepushter-tag-ist-endgueltig.md)).

## Schritt für Schritt: nach vorn korrigieren

1. **Stellen Sie das Problem nach** auf einer Testinstallation mit der veröffentlichten Version.
2. **Beheben Sie es** auf `main`, mit einer Commit-Nachricht, die das Problem nennt (`Fixed: ...`).
3. **Wählen Sie die nächste Nummer** ([Eine Versionsnummer wählen](eine-versionsnummer-waehlen.md)), erhöhen, committen,
   taggen, pushen.
4. **Schreiben Sie die Notes klar**:

   ```markdown
   ## Fixed: the event template missing from 1.0.25

   Release 1.0.25 did not contain `design/mytheme/templates/full/event.tpl`; event pages showed the standard
   template. This release adds it back. Update from 1.0.25 directly; nothing else changed.
   ```

5. **Sagen Sie es den Nutzern** dort, wo sie nachsehen: am Release, in den Neuigkeiten des Projekts auf
   [projects.exponential.earth](eintrag-auf-projects-exponential-earth.md) oder in Ihrem Ankündigungs-Thread und im
   Issue, das es gemeldet hat.
6. **Schließen Sie das Issue** mit einem Link zum neuen Release.

## Wenn der Fehler ein Sicherheitsproblem ist

Folgen Sie [Ein Sicherheitsrelease veröffentlichen](../lebenszyklus/ein-sicherheitsrelease-veroeffentlichen.md): beheben,
veröffentlichen, dann beschreiben.

## Nutzer, die die fehlerhafte Version fest eingetragen haben

Ein Nutzer, der eine genaue Version (`1.0.25`) geschrieben hat, bekommt die Korrektur nicht automatisch. Sagen Sie in den
Notes, welche Bedingung sie übernimmt (`~1.0.25` oder `^1.0`) ([Versionsbedingungen Ihrer Nutzer](versionsbedingungen-fuer-nutzer.md)).

## Verwandte Seiten

- [Release Notes schreiben](release-notes-schreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
