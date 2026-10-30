---
title: Die CLA für Lernende
audience: student
category: learning
order: 44
summary: Was das Contributor Licensing Agreement für Lernende bedeutet - wann es erforderlich ist (Codebeiträge zum Kernel), was Sie abgeben (das Urheberrecht wird an die Exponential Foundation und 7x übertragen), was es nicht umfasst (Ihre eigenen Erweiterungen, Websites und Arbeiten, die Sie nur selbst veröffentlichen), wie Sie unterzeichnen, und Fragen zu Arbeitgebern, Schulen und kleinen Korrekturen.
keywords: CLA, contributor licensing agreement, urheberrecht, übertragung, 7x, kernel, pull request, eigene erweiterung, GPL, lernende, schule, arbeitgeber, unterzeichnen, copyright, assignment, own extension, student, school, employer, sign
type: help
---
# Die CLA für Lernende

Bevor Sie Code an den Exponential-Kernel senden, lesen Sie diese Seite. Sie erklärt das Contributor Licensing
Agreement (CLA) in einfachen Worten für Lernende. Sie fasst `CONTRIBUTING.md` zusammen; maßgeblich ist die Vereinbarung
selbst, und diese Seite ist keine Rechtsberatung ([Übertragung des Urheberrechts (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla)).

## Inhalt

1. [In einem Absatz](#in-einem-absatz)
2. [Wann Sie sie brauchen](#wann-sie-sie-brauchen)
3. [Wann nicht](#wann-nicht)
4. [Wie Sie unterzeichnen](#wie-sie-unterzeichnen)
5. [Fragen, die Lernende stellen](#fragen-die-lernende-stellen)

## In einem Absatz

7x und die Exponential Foundation verlangen für jeden Codebeitrag zum Kernel von Exponential, dem Repository
[se7enxweb/exponential](https://github.com/se7enxweb/exponential), eine unterzeichnete CLA, bevor er gemergt werden
kann. „Indem Sie die CLA unterzeichnen und einen Beitrag zum Kernel einreichen, übertragen Sie das Urheberrecht an
diesem Beitrag an die Exponential Foundation und 7x. Sie werden Inhaber des Urheberrechts.“ Sie geben die Kontrolle
ab: Die Exponential Foundation und 7x dürfen den Beitrag auf jede
Weise nutzen, ändern, neu lizenzieren und verbreiten, auch kommerziell, ohne Sie zu fragen oder zu bezahlen, und Sie
können ihn nicht zurückziehen. **Ihre eigenen Erweiterungen sind davon nicht betroffen.**

## Wann Sie sie brauchen

- Ein Pull Request, ein Patch oder Code in einem Issue oder einer E-Mail an den Kernel: `kernel/`, `lib/`, `bin/`,
  `design/`, `settings/`, `doc/`, die darin mitgelieferten Erweiterungen und alles andere in diesem Repository.
- Ein Pull Request von jemandem ohne hinterlegte, unterzeichnete CLA wird begutachtet, aber erst gemergt, wenn die CLA
  eingegangen ist.
- Eine Erweiterung, die Sie **an 7x zur Aufnahme** in das Projekt übergeben, ist auf dieselbe Weise erfasst.

Beachten Sie, dass `doc/` Teil des Kernel-Repositorys ist. Korrekturen der Dokumentation dort sind Beiträge zu diesem
Repository.

## Wann nicht

- Ihre eigenen Erweiterungen, Site-Pakete, Designs und Themes, die Sie selbst schreiben, behalten und lizenzieren,
  ob privat oder in Ihrem eigenen Repository veröffentlicht. Die Seite zur CLA sagt: „Wenn Sie einen Beitrag
  geschrieben haben und ihn auf GitHub (https://github.com) zur Verfügung stellen möchten, müssen Sie die CLA nicht
  unterzeichnen.“
- Eine Website, die Sie mit Exponential bauen und betreiben; dafür gilt die GPL.
- Alles, was Sie schriftlich als „Not a Contribution“ kennzeichnen.
- Für „kleine Patches oder Fehlerkorrekturen über unser Fehlersystem brauchen wir in den meisten Fällen keine
  unterzeichnete CLA“ (Seite zur CLA). Für Pull Requests an den Kernel rät `CONTRIBUTING.md`, trotzdem zu
  unterzeichnen, da ein Merge eine hinterlegte CLA voraussetzt und Sie nur einmal unterzeichnen.

## Wie Sie unterzeichnen

Nach `CONTRIBUTING.md`:

1. Lesen Sie die [Seite zur CLA](https://share.se7enx.com/community-project/contributor-licensing-agreement-cla) und
   laden Sie die dort verlinkte Vereinbarung herunter.
2. Füllen Sie Abschnitt 2 (den Inhaber des Urheberrechts) und den Unterschriftenblock aus und unterschreiben Sie.
3. Lassen Sie sie 7x per Post, als Scan per E-Mail oder per Fax zukommen, wie die Vereinbarung es sagt; bei E-Mail
   vergewissern Sie sich, dass sie angekommen ist.
4. Erwähnen Sie in Ihrem Pull Request, dass Ihre CLA unterzeichnet ist; die Vorlage für Pull Requests hat dafür ein
   Kontrollkästchen.

„Die CLA wird einmal für alle Ihre zukünftigen Beiträge unterzeichnet.“

## Fragen, die Lernende stellen

**Ich bin Lernende oder Lernender. Wem gehört, was ich schreibe?** Normalerweise Ihnen. Wenn Sie es aber im Rahmen
einer Anstellung oder eines Praktikums schreiben, kann Ihr Arbeitgeber Rechte daran haben; dann brauchen Sie seine
Erlaubnis oder seinen Verzicht, oder das Unternehmen unterzeichnet (wie `CONTRIBUTING.md` die Vereinbarung
zusammenfasst). Wenn Ihre Schule oder Ihr Kurs Regeln zum geistigen Eigentum an Kursarbeiten hat, lesen Sie sie oder
fragen Sie Ihre Lehrkraft, bevor Sie das Urheberrecht übertragen.

**Kann ich meine Erweiterung unter meinem eigenen Namen behalten?** Ja. Veröffentlichen Sie sie in Ihrem eigenen
Repository; die CLA gilt nicht. Die GPL gilt weiterhin, wenn Sie eine Erweiterung an andere weitergeben
([Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md)).

**Kann ich einen Beitrag später zurücknehmen?** Nein. Sie können ihn weder zurückziehen noch an Bedingungen knüpfen.

**Ersetzt die CLA die GPL?** Nein. Ihr Beitrag wird wie der Rest von Exponential unter der GPL veröffentlicht; für den
Kernel überträgt die CLA zusätzlich sein Urheberrecht an die Exponential Foundation und 7x.

**Brauche ich sie für eine Übersetzung oder eine Korrektur der Dokumentation?** Das sind Beiträge zum
Kernel-Repository, und `CONTRIBUTING.md` führt „Dokumentationsdateien, Übersetzungen“ unter den Arten von Code auf, die
die Seite zur CLA nennt. Unterzeichnen Sie sie, oder fragen Sie in Ihrem Pull Request nach.

## Verwandte Seiten

- [Als Lernende beitragen](als-lernende-beitragen.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md#eine-änderung-per-pull-request-beitragen)
- [Ihr erster Beitrag, Schritt für Schritt](ihr-erster-beitrag-schritt-fuer-schritt.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
