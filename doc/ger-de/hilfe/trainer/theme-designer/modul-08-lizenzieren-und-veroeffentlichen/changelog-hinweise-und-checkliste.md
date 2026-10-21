---
title: "Lektion 8.3: Changelog, Release Notes und die Release-Checkliste"
audience: trainer, designer, developer
category: training
order: 83
summary: Die Teilnehmenden machen aus ihrer Commit-Historie Einträge in CHANGELOG.md, schreiben Release Notes mit einer Überschrift und einem Abschnitt „Nach dem Aktualisieren“ und gehen als Abschlussübung des Kurses die Checkliste für die Verteilung ihres Themes durch. 60 Minuten.
keywords: theme-design, CHANGELOG.md, changelog-einträge, Added, Updated, Removed, Renamed, Fixed, release notes, nach dem aktualisieren, checkliste für die verteilung, checkliste theme-erweiterung bereit, git log, git describe, kursabschluss, theme design, changelog entries, after updating, distribution checklist, course wrap-up
type: help
---
# Lektion 8.3: Changelog, Release Notes und die Release-Checkliste

Die letzte Lektion des Kurses führt alles zusammen. Die Teilnehmenden beschreiben die Änderungen ihres Themes in der
Form, in der Benutzer sie lesen, als Changelog und Release Notes, und prüfen ihr Theme dann anhand der veröffentlichten
Checklisten. Was auf der Checkliste offen bleibt, ist die ehrliche Liste dessen, was die Teilnehmenden vor der
Veröffentlichung eines echten Themes noch tun würden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../../../../../eng-us/help/trainers/theme-designers/README.md) |
| Modul | [Modul 8: Lizenzieren und veröffentlichen](README.md) |
| Dauer | 60 Minuten (15 Präsentation, 45 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Entwickler, die Themes veröffentlichen |
| Voraussetzungen | [Lektion 8.2](versionen-und-dauerhafte-tags.md): der Tag `v0.1.1` im lokalen Repository |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Commits seit dem letzten Tag sammeln und sie aus Sicht der Benutzer als Changelog-Einträge neu formulieren;
- `CHANGELOG.md` mit dem neuesten Release oben führen;
- Release Notes mit einer Überschrift, den Änderungen, den Schritten nach dem Aktualisieren und der Kompatibilität
  schreiben;
- ein Theme anhand der Checklisten „Die Theme-Erweiterung ist bereit“ und „Vor dem Veröffentlichen“ prüfen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Eine CHANGELOG.md führen](../../../../../eng-us/help/designers/distribution/keeping-a-changelog-file.md),
[Release Notes schreiben](../../../../../eng-us/help/designers/distribution/writing-release-notes.md),
[Checkliste: Die Theme-Erweiterung ist bereit](../../../../../eng-us/help/designers/extensions/checklist-theme-extension-ready.md) und
[Checkliste: Vor dem Veröffentlichen](../../../../../eng-us/help/designers/distribution/distribution-checklist.md).

1. **Changelog** (4 Minuten). Die Datei reist mit dem Theme in jede Installation; ein GitHub-Release tut das nicht.
   Führen Sie beides. Sammeln mit `git log --oneline "$(git describe --tags --abbrev=0)"..HEAD`, mit Präfixen neu
   formulieren, Zusammengehöriges zusammenfassen, interne Commits weglassen, das neueste Release zuerst,
   veröffentlichte Einträge nie umschreiben, außer um eine Tatsache zu korrigieren, und das dann auch sagen.
2. **Release Notes** (4 Minuten). Eine Überschrift mit dem Nutzen; die Änderungen mit Präfixen; was nach dem
   Aktualisieren zu tun ist (zu leerende Caches, zu ergänzende Einstellungen) oder „Nach dem Aktualisieren ist nichts
   zu tun.“; Kompatibilität; Danksagungen mit Einverständnis; eine Korrektur, wenn das Release ein früheres repariert.
   Entfernen Sie alles Private: interne Pfade, Hostnamen, Kundennamen, Zugangsdaten.
3. **Die Checklisten** (4 Minuten). Zeigen Sie die Gruppen der Checkliste für die Veröffentlichung: Repository,
   Lizenzen, Version und Tag, Release und Composer, gefunden und unterstützt werden.
4. **Den Kurs abschließen** (3 Minuten). Fassen Sie die Kette von Modul 1 bis zu dieser Lektion zusammen:
   Design-Kette, eigene Erweiterung, Packer, Regeln der Administration, Barrierefreiheit, Icons, Paketieren,
   Lizenzieren, Veröffentlichen.

### Häufige Fragen

**Brauche ich ein Changelog für ein Theme, das nur meine Firma verwendet?** Ja; die nächste Entwicklerin oder der
nächste Entwickler im Projekt liest es.

**Was gehört bei einem Theme in „Nach dem Aktualisieren“?** Typischerweise die Caches: der Template-Override-Cache für
neue Templates, die Caches des Packers und der Template-Blöcke für geänderte Stylesheets, der INI-Cache für neue
Einstellungen.

**Wo veröffentliche ich?** Das ist eine Entscheidung für ein echtes Theme: siehe
[Wählen, wo veröffentlicht wird](../../../../../eng-us/help/designers/distribution/choosing-where-to-publish.md).

### Stolperfallen

- **Commit-Nachrichten eins zu eins übernehmen.** Benutzer brauchen die Wirkung, nicht die Arbeitsschritte.
- **Den Abschnitt „Nach dem Aktualisieren“ weglassen.** Ein Theme mit einem neuen Template verlangt, dass ein Cache
  geleert wird; sagen Sie es.
- **Nicht zutreffende Punkte der Checkliste als erledigt abhaken.** Markieren Sie sie mit Begründung als „nicht
  zutreffend“.

## Praktische Übung

In `extension/training_theme`, dem lokalen Repository aus den Lektionen 7.4 und 8.2.

1. Machen Sie eine weitere Änderung für das nächste Release: Geben Sie in `training.css` der Fußzeile oben mehr Abstand.
   Committen Sie sie mit einer Nachricht mit Präfix.
2. Sammeln Sie die Commits seit dem letzten Tag:

   ```bash
   git log --oneline "$(git describe --tags --abbrev=0)"..HEAD
   ```

3. Legen Sie `CHANGELOG.md` an, mit einer Überschrift `# Changelog`, einem Abschnitt `## 0.1.2` mit dem Eintrag für
   Schritt 1 und je einem Abschnitt `## 0.1.1` und `## 0.1.0` mit ein oder zwei Einträgen aus Ihrer Erinnerung an den
   Kurs (die Brotkrumennavigation, der Dashboard-Block, die Icons).
4. Schreiben Sie Release Notes für `0.1.2` in eine Datei `var/tmp/training_theme-0.1.2-notes.md` der Installation
   (nicht im Theme): Überschrift, Änderungen, „Nach dem Aktualisieren“ (welche Caches?), Kompatibilität (die
   Exponential-Version Ihrer Übungsumgebung, von der Seite „Über“).
5. Lesen Sie Ihre Notes als Benutzerin oder Benutzer, die oder der zwei Releases übersprungen hat. Entfernen Sie alles
   Private.
6. Öffnen Sie [Checkliste: Die Theme-Erweiterung ist bereit](../../../../../eng-us/help/designers/extensions/checklist-theme-extension-ready.md)
   und gehen Sie sie für `training_theme` durch. Markieren Sie jeden Punkt als erledigt, offen oder nicht zutreffend.
7. Machen Sie dasselbe mit [Checkliste: Vor dem Veröffentlichen](../../../../../eng-us/help/designers/distribution/distribution-checklist.md),
   bis zur Gruppe „Version und Tag“.
8. Schreiben Sie die drei wichtigsten offenen Punkte auf eine Karte. Teilen Sie sie mit der Gruppe.

## Erwartetes Ergebnis

- `CHANGELOG.md` enthält drei Releases, das neueste oben, mit für Benutzer geschriebenen Einträgen mit Präfix.
- Die Release Notes haben eine Überschrift, die Änderungen, einen Abschnitt „Nach dem Aktualisieren“, der für die
  Änderung am Stylesheet die Caches des Packers und der Template-Blöcke nennt, und die getestete Version.
- Alle Teilnehmenden haben eine markierte Checkliste und drei offene Punkte, etwa einen Screenshot, eine `SECURITY.md`
  oder ein echtes Repository.
- Der Trainer sammelt die offenen Punkte an der Tafel; sie sind die nächsten Schritte der Teilnehmenden nach dem Kurs.

## Quiz

1. Warum eine `CHANGELOG.md` führen, wenn das Repository Release Notes hat?
2. Welcher Befehl listet die Commits seit dem neuesten Tag auf?
3. Welcher Abschnitt der Release Notes sagt den Benutzern, welche Caches sie leeren müssen?
   A. Die Überschrift B. Danksagungen C. Nach dem Aktualisieren D. Kompatibilität
4. Dürfen Sie einen Changelog-Eintrag eines veröffentlichten Releases umschreiben?
5. Nennen Sie zwei Dinge, die vor dem Veröffentlichen aus den Release Notes entfernt werden müssen.

<details>
<summary>Antworten</summary>

1. Die Datei reist mit dem Theme in jede Installation; ein Release auf einer Hosting-Plattform tut das nicht
   ([Eine CHANGELOG.md führen](../../../../../eng-us/help/designers/distribution/keeping-a-changelog-file.md)).
2. `git log --oneline "$(git describe --tags --abbrev=0)"..HEAD`.
3. **C.**
4. Nur, um einen sachlichen Fehler zu korrigieren, und das dann auch sagen.
5. Zwei beliebige: interne Pfade, Hostnamen, Kundennamen, Zugangsdaten
   ([Release Notes schreiben](../../../../../eng-us/help/designers/distribution/writing-release-notes.md)).

</details>

## Weiterführende Literatur

- [Changelog-Einträge](../../../../../eng-us/help/designers/distribution/changelog-prefixes.md): die Präfixe.
- [INSTALL.md und CHANGELOG.md](../../../../../eng-us/help/designers/extensions/install-and-changelog-files.md).
- [Ein Theme verteilen: hier beginnen](../../../../../eng-us/help/designers/distribution/distribution-start-here.md): der ganze Weg zur
  Veröffentlichung.
- [Der Lebenszyklus eines Themes](../../../../../eng-us/help/designers/lifecycle/the-life-cycle-of-a-theme.md): nach dem ersten Release.

## Verwandte Seiten

- [Modul 8: Lizenzieren und veröffentlichen](README.md)
- [Theme-Design: Kursüberblick](../../../../../eng-us/help/trainers/theme-designers/README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
