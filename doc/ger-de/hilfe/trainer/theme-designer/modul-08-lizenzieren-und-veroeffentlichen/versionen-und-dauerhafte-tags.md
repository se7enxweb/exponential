---
title: "Lektion 8.2: Versionen, und warum ein übertragener Tag dauerhaft ist"
audience: trainer, designer, developer
category: training
order: 82
summary: Die Teilnehmenden wählen die nächste Versionsnummer anhand dessen, was bereits veröffentlicht ist, sortieren Tags richtig (.10 nach .9), heben jedes Versionsfeld in einem Commit vor dem Tag an, legen in ihrem lokalen Repository einen annotierten Tag an und erklären, warum ein übertragener Tag nie verschoben wird. 45 Minuten.
keywords: theme-design, versionsnummer, release, tag, annotierter tag, dauerhafter tag, Packagist, sort version refname, sort -V, nur die letzte stelle zählt, vor dem tag anheben, wenn ein release schiefgeht, versionsfelder synchron halten, theme design, version number, annotated tag, permanent tag, bump before tag, when a release goes wrong
type: help
---
# Lektion 8.2: Versionen, und warum ein übertragener Tag dauerhaft ist

Sobald der Tag eines Themes übertragen ist, liest Packagist ihn einmal, und die Benutzer installieren ihn. Ihn danach zu
verschieben oder neu zu erstellen, erzeugt zwei verschiedene Pakete unter einer Versionsnummer. Diese Lektion vermittelt
die Release-Routine, die das verhindert: prüfen, was veröffentlicht ist, die Nummer wählen, sie überall eintragen,
committen, dann taggen. Die Teilnehmenden üben sie in ihrem lokalen Repository, wo nichts schiefgehen kann.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 8: Lizenzieren und veröffentlichen](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Entwickler, die Themes veröffentlichen |
| Voraussetzungen | [Lektion 7.4](../modul-07-ein-theme-paketieren/ein-theme-unter-git.md): `training_theme` in einem lokalen Git-Repository |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, warum ein übertragener Tag nie gelöscht, verschoben oder neu erstellt wird und was zu tun ist, wenn ein
  Release schiefgeht;
- vorhandene Tags in der richtigen Reihenfolge auflisten und die nächste Nummer wählen (nach `.9` kommt `.10`);
- die Version in jedes Feld eintragen und sie vor dem Taggen committen;
- einen annotierten Tag anlegen und ihn prüfen, bevor er übertragen würde.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Ein übertragener Tag ist dauerhaft](../../../../../eng-us/help/designers/distribution/a-pushed-tag-is-permanent.md),
[Nach .9 kommt .10](../../../../../eng-us/help/designers/distribution/only-the-last-position-counts.md),
[Die Version vor dem Tag anheben](../../../../../eng-us/help/designers/distribution/bumping-versions-before-the-tag.md) und
[Wenn ein Release schiefgeht](../../../../../eng-us/help/designers/distribution/when-a-release-goes-wrong.md).

1. **Warum dauerhaft** (5 Minuten). Packagist liest einen Tag einmal und speichert die gefundene Version im Cache; die
   Benutzer tragen sie in Lock-Dateien ein und spielen sie aus. Git erlaubt es, einen Tag zu verschieben, aber das
   erreicht diese Kopien nicht. Ein „korrigierter“ Tag ergibt zwei Pakete mit einer Versionsnummer.
2. **Die Regeln** (4 Minuten). Einen übertragenen Tag nie löschen, verschieben, umhängen oder neu erstellen; die
   Assets eines veröffentlichten Releases nie ersetzen; prüfen, was veröffentlicht ist, bevor man eine Nummer wählt;
   vor dem Übertragen prüfen; ist eine fehlerhafte Version draußen, sie stehen lassen und die nächste veröffentlichen,
   mit einem Hinweis in deren Release Notes, was falsch war. Nur der Text von Release Notes darf korrigiert werden.
3. **Nummern** (4 Minuten). Nur die letzte Stelle wird hochgezählt: Auf `0.0.4.9` folgt `0.0.4.10`. Eine einfache
   Sortierung nach Namen setzt `.10` vor `.8`; verwenden Sie `git tag --sort=version:refname` oder `sort -V`.
4. **Die Reihenfolge der Arbeit** (4 Minuten). Die Nummer in `ezinfo.php` und `extension.xml` eintragen (und in
   `composer.json`, wenn sie eine `version` hat), committen („Updated: Version 0.1.1“), dann diesen Commit taggen. Nie
   zuerst taggen.
5. **Die Checkliste vor dem Übertragen** (3 Minuten). Sauberes Arbeitsverzeichnis, der Release-Commit obenauf, die
   Versionsfelder stimmen überein, die Nummer ist höher als jede veröffentlichte, das Theme wurde aus diesem Commit
   installiert und angesehen, die Tag-Nachricht stimmt.

### Häufige Fragen

**Ich habe einen Tag mit einem Tippfehler in der Nachricht übertragen. Kann ich ihn korrigieren?** Nein. Lassen Sie ihn
stehen. Das nächste Release kann sagen, was falsch war.

**Wann ändert sich die erste oder zweite Stelle?** Wenn die Bedeutung des Releases es verlangt (zum Beispiel eine
inkompatible Änderung), bewusst entschieden, nie weil die letzte Stelle neun erreicht hat
([Eine Versionsnummer wählen](../../../../../eng-us/help/designers/distribution/choosing-a-version-number.md)).

**Wo prüfe ich, was veröffentlicht ist?** Die Tags und Releases des Repositorys und die Packagist-Seite; ein Tag kann
ohne Release existieren, und der lokale Klon hat womöglich nicht alle Tags abgerufen
([Prüfen, was bereits veröffentlicht ist](../../../../../eng-us/help/designers/distribution/checking-what-is-already-published.md)).

### Stolperfallen

- **An einem echten Repository üben.** Die Übung ist lokal; es gibt kein Remote, also kann nichts übertragen werden.
- **„Den neuesten Tag“ mit `tail -1` aus einer unsortierten Liste nehmen.** Immer nach Version sortieren.
- **Ein Versionsfeld vergessen.** Die Seite „Über“ liest `extension.xml` zuerst; ein vergessenes Feld fällt auf.

## Praktische Übung

In `extension/training_theme` auf Ihrer eigenen Installation, dem lokalen Repository aus Lektion 7.4. Fügen Sie kein
Remote hinzu.

1. Prüfen Sie, dass das Repository kein Remote hat: `git remote -v` gibt nichts aus.
2. Legen Sie zum Üben des Sortierens fünf lokale Wegwerf-Tags auf dem aktuellen Commit an:
   `for v in 0.0.8 0.0.9 0.0.10 0.0.11 0.0.2; do git tag "practice-$v"; done`.
3. Listen Sie sie nach Namen auf: `git tag -l 'practice-*' | tail -1`. Dann nach Version: `git tag -l 'practice-*' --sort=version:refname | tail -1`.
4. Löschen Sie die Übungs-Tags (sie wurden nie übertragen, deshalb ist das erlaubt):
   `git tag -l 'practice-*' | xargs git tag -d`.
5. Das Theme steht auf `0.1.0` (Lektion 7.1). Wählen Sie die nächste Nummer für ein Release, das nur den Stil der
   Brotkrumennavigation ändert.
6. Tragen Sie diese Nummer in `ezinfo.php` und `extension.xml` ein. Prüfen Sie: `grep -n "Version\|<version>" ezinfo.php extension.xml`.
7. Führen Sie `php -l ezinfo.php` und `xmllint --noout extension.xml` aus.
8. Committen Sie: `git commit -am "Updated: Version 0.1.1"` (mit Ihrer Nummer).
9. Legen Sie auf diesem Commit einen annotierten Tag an: `git tag -a v0.1.1 -m "Training theme 0.1.1"`.
10. Prüfen Sie ihn so, wie Sie es vor dem Übertragen täten: `git status --short` (leer), `git log --oneline -1`, `git show v0.1.1 | head -5`.

## Erwartetes Ergebnis

- Schritt 3: Die einfache Liste endet mit `practice-0.0.9`, die sortierte mit `practice-0.0.11`.
- Schritt 5: Die Teilnehmenden wählen `0.1.1`.
- Schritte 6 bis 10: Beide Dateien sagen `0.1.1`, der Release-Commit liegt obenauf, und der annotierte Tag `v0.1.1`
  zeigt mit seiner Nachricht darauf.
- Der Trainer fragt: „Sie finden nach dem Übertragen einen Tippfehler in der Tag-Nachricht. Was tun Sie?“ Erwartete
  Antwort: ihn stehen lassen und ihn in den Release Notes des nächsten Releases erwähnen.

## Quiz

1. Was folgt auf Version `2.3.9` bei einem Release mit Fehlerbehebungen?
   A. `2.4.0` B. `2.3.10` C. `3.0.0` D. `2.3.9.1`
2. Warum darf ein übertragener Tag nie verschoben werden?
3. Was kommt zuerst: der Commit, der die Version anhebt, oder der Tag?
4. Welcher Befehl listet Tags in der richtigen Versionsreihenfolge auf?
5. Ein Release ist mit einer fehlenden Datei hinausgegangen. Was tun Sie?

<details>
<summary>Antworten</summary>

1. **B.** Nur die letzte Stelle zählt
   ([Nach .9 kommt .10](../../../../../eng-us/help/designers/distribution/only-the-last-position-counts.md)).
2. Packagist und die Benutzer haben die Version, auf die er zeigte, bereits; ein verschobener Tag ergibt zwei Pakete
   mit einer Nummer
   ([Ein übertragener Tag ist dauerhaft](../../../../../eng-us/help/designers/distribution/a-pushed-tag-is-permanent.md)).
3. Der Commit, der die Version anhebt; danach der Tag auf diesem Commit.
4. `git tag -l --sort=version:refname` (oder eine Liste, die durch `sort -V` geleitet wird).
5. Das Release so lassen, wie es ist, und die nächste Version mit der Datei veröffentlichen, mit einem Hinweis in
   deren Release Notes, was gefehlt hat.

</details>

## Weiterführende Literatur

- [Ein übertragener Tag ist dauerhaft](../../../../../eng-us/help/designers/distribution/a-pushed-tag-is-permanent.md).
- [Tags richtig sortieren](../../../../../eng-us/help/designers/distribution/sorting-tags-correctly.md).
- [Annotierte Tags](../../../../../eng-us/help/designers/distribution/annotated-tags.md).
- [Alle Versionsfelder synchron halten](../../../../../eng-us/help/designers/extensions/keeping-version-fields-in-sync.md).

## Verwandte Seiten

- [Modul 8: Lizenzieren und veröffentlichen](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
