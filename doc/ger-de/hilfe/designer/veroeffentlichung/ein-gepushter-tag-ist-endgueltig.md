---
title: "Ein gepushter Tag ist endgültig"
audience: designer, developer
category: theme-distribution
order: 130
summary: Warum ein Tag und das daraus gebaute Release nach dem Push nie gelöscht, verschoben oder neu erstellt werden können - Packagist speichert, was es gelesen hat, Nutzer behalten, was sie installiert haben - und die Regeln, die daraus für jeden Theme-Autor folgen.
keywords: Unveränderlichkeit von Tags, endgültiges Release, Tags nicht verschieben, Force-Push, Packagist-Cache, neu erstellen, veröffentlichtes Release, nach vorn korrigieren, tag immutability, permanent release, force push, re-cut, fix forward
type: help
---
# Ein gepushter Tag ist endgültig

Diese Seite ist für jeden Designer, der Releases veröffentlicht. Lesen Sie sie vor Ihrem ersten Tag.

## Warum

Wenn Sie einen Tag eines öffentlichen Composer-Pakets pushen:

- **Packagist liest ihn einmal und speichert die Version, die es gefunden hat.** Alles, was diese Version aufgelöst
  hat, behält den Inhalt, den es gesehen hat.
- **Nutzer installieren ihn**, spiegeln ihn, tragen ihn in Lock-Dateien ein und rollen ihn aus.

Git erlaubt Ihnen, einen Tag zu löschen oder zu verschieben, aber das erreicht diese Kopien nicht. Ein „korrigierter“
Tag erzeugt zwei verschiedene Pakete mit derselben Versionsnummer: schlimmer als der ursprüngliche Fehler und von außen
nicht zu diagnostizieren.

## Die Regeln

1. **Löschen, verschieben, umhängen oder erstellen Sie einen gepushten Tag nie neu.** Nicht für einen Tippfehler in
   der Nachricht, nicht für eine vergessene Datei, nicht für ein veraltetes Archiv.
2. **Ersetzen oder bearbeiten Sie nie die Assets eines veröffentlichten GitHub-Releases.** Ein korrigiertes Asset
   unter derselben Version ist dasselbe Problem wie ein verschobener Tag.
3. **Prüfen Sie, was schon veröffentlicht ist, bevor Sie eine Nummer wählen**
   ([Prüfen, was schon veröffentlicht ist](pruefen-was-schon-veroeffentlicht-ist.md)).
4. **Prüfen Sie vor dem Push.** Sauberer Arbeitsbaum, das Theme wird dargestellt, die Versionsfelder stimmen überein,
   gebaute Dateien passen zu ihren Quellen.
5. **Ist eine fehlerhafte Version veröffentlicht, lassen Sie sie stehen** und veröffentlichen Sie die nächste Version
   mit der Korrektur; sagen Sie in deren Notes klar, was falsch war ([Wenn ein Release schiefgeht](wenn-ein-release-schiefgeht.md)).

## Eine Checkliste vor `git push origin v<version>`

- [ ] `git status --short` gibt nichts aus.
- [ ] `git log --oneline -1` ist der Release-Commit.
- [ ] Die Versionsfelder zeigen die neue Nummer ([Alle Versionsfelder synchron halten](../../../../eng-us/help/designers/extensions/keeping-version-fields-in-sync.md)).
- [ ] Die Nummer ist höher als jeder veröffentlichte Tag und jedes Release.
- [ ] Das Theme wurde aus diesem Commit auf einer Testinstallation installiert und angesehen.
- [ ] Die Tag-Nachricht stimmt (`git show v<version> | head -5`).

## Was sich noch ändern lässt

Der **Text** eines GitHub-Releases (seine Notes) darf korrigiert werden, wenn er falsch ist, solange der Tag und die
Assets bleiben. Sagen Sie in den Notes, dass sie korrigiert wurden.

## Verwandte Seiten

- [Annotierte Tags](annotierte-tags.md)
- [Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
