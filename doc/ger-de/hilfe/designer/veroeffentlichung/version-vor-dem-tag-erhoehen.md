---
title: "Die Version vor dem Tag erhöhen"
audience: designer, developer
category: theme-distribution
order: 110
summary: Der Release-Commit - die neue Nummer in ezinfo.php, extension.xml und die anderen Versionsfelder schreiben, prüfen, committen und erst dann diesen Commit taggen - damit der Tag für immer die richtige Nummer trägt.
keywords: Version erhöhen, Release-Commit, ezinfo.php, extension.xml, composer.json, Tag nach dem Commit, Updated Version, version bump, release commit, tag after commit
type: help
---
# Die Version vor dem Tag erhöhen

Diese Seite ist für alle, die ein Release erstellen. Die Regel ist kurz: **Erhöhen Sie die Version in einem Commit und
taggen Sie dann diesen Commit.** Taggen Sie nie zuerst und erhöhen danach: Der Tag trüge die alte Nummer, und ein
gepushter Tag lässt sich nicht verschieben.

## Schritt für Schritt

1. Wählen Sie die Nummer ([Eine Versionsnummer wählen](eine-versionsnummer-waehlen.md)). Beispiel: `1.0.25`.
2. Schreiben Sie sie ohne `v` in:
   - `ezinfo.php`: `'Version' => "1.0.25"`;
   - `extension.xml`: `<version>1.0.25</version>`;
   - `composer.json`: `"version": "1.0.25"`, nur wenn die Datei das Feld hat;
   - den Kopf von `share/filelist.md5`, nur wenn das Theme eine mitliefert ([Datei-Prüfsummen](filelist-pruefsummen.md)).
3. Tragen Sie das Release in `CHANGELOG.md` ein ([Eine CHANGELOG.md führen](eine-changelog-datei-fuehren.md)).
4. Prüfen Sie:

   ```bash
   php -l ezinfo.php
   xmllint --noout extension.xml
   grep -n "'Version'" ezinfo.php; grep -n '<version>' extension.xml
   git status --short
   ```

   Erwartet: keine Syntaxfehler, keine Ausgabe von `xmllint`, zweimal dieselbe Nummer und nur die Dateien, die Sie
   ändern wollten.
5. Committen Sie: `git commit -am "Updated: Version 1.0.25"` (oder machen Sie es zum Teil des letzten Commits des
   Releases).
6. Taggen Sie diesen Commit ([Annotierte Tags](annotierte-tags.md)):

   ```bash
   git tag -a v1.0.25 -m "v1.0.25: <what this release is about>"
   ```

7. Pushen Sie den Branch und den Tag: `git push origin main v1.0.25`.
8. Legen Sie das Release an ([GitHub-Releases](github-releases.md)).

## Vor dem Push prüfen, nicht danach

Bestätigen Sie vor Schritt 7: Der Arbeitsbaum ist sauber, das Theme wird auf einer Testinstallation dargestellt, und
jede gebaute Datei (kompiliertes CSS) passt zu ihren Quellen. Eine Minute Prüfen kostet weniger als eine für immer
verbrauchte Versionsnummer.

## Verwandte Seiten

- [Alle Versionsfelder synchron halten](../erweiterungen/versionsfelder-synchron-halten.md)
- [Ein gepushter Tag ist endgültig](ein-gepushter-tag-ist-endgueltig.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
