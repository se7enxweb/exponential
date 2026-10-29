---
title: Code beitragen
audience: supporter
category: supporting
order: 30
summary: Wie Einzelpersonen Code zu Exponential beitragen – etwas finden, woran man arbeitet, die Einrichtung aus Git, der Branch- und Pull-Request-Ablauf aus CONTRIBUTING.md, Commit-Nachrichten, das Dateimanifest, Tests und CI, die Merge-Regel und die Lizenz der Beiträge.
keywords: Code, mitwirken, Pull-Request, Fork, Branch, Git, GitHub, Commit-Nachricht, CI, Tests, Manifest, GPL, erster Beitrag, code, contribute, pull request, fork, branch, commit message, tests, manifest, first contribution
type: help
---
# Code beitragen

Diese Seite ist für Entwickler, die ihren ersten oder nächsten Pull-Request an Exponential schicken möchten. Die Regeln
stehen in [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md); der Git-Ablauf wird
Schritt für Schritt unter [Mit Git entwickeln](../mit-git-entwickeln.md) erklärt.

## Etwas finden, woran Sie arbeiten

- Offene Issues: <https://github.com/se7enxweb/exponential/issues>.
- Etwas, das Sie bei Ihrer eigenen Arbeit gestört hat: Das ist meist der beste erste Beitrag.
- Erweiterungen: Jede Erweiterung `se7enxweb/*` ist ein eigenes Repository mit eigenen Issues.

## Einrichtung

Installieren Sie Exponential aus Git mit vollständiger Historie ([Mit Git entwickeln](../mit-git-entwickeln.md)). SQLite
ist die Standarddatenbank, eine Entwicklungsinstallation braucht also keinen Datenbankserver
([Datenbankunterstützung](../datenbankunterstuetzung.md)).

## Der Ablauf

1. Erstellen Sie einen Fork des Repositorys und klonen Sie Ihren Fork.
2. Legen Sie einen Branch an: `git checkout -b feature/your-feature`.
3. Halten Sie sich an die PSR-Standards und den Programmierstil; Templates der Administration folgen den Gestaltungsregeln
   (keine `<style>`-Blöcke, keine `style=""`-Attribute; gemeinsame Komponenten und Tokens) (CONTRIBUTING.md).
4. Committen Sie mit einer Betreffzeile, die mit `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder `Renamed: `
   beginnt, zum Beispiel `Fixed: The redirect keeps the query string on the own host` (CONTRIBUTING.md).
5. Aktualisieren Sie `share/filelist.md5` für jede Datei, die Sie hinzufügen, ändern oder entfernen
   (`make manifest-fix`); `make hooks` installiert einen Pre-Commit-Hook, der das Manifest, die PHP-Syntax und den
   Programmierstil prüft.
6. Führen Sie die Tests aus, aktualisieren Sie die Dokumentation, pushen Sie und eröffnen Sie einen Pull-Request.
   „Halten Sie Ihren Pull-Request fokussiert. Wenn Sie mehrere Beiträge haben, reichen Sie mehrere Pull-Requests ein.“

## Was mit Ihrem Pull-Request geschieht

- Die CI prüft Syntax, Manifest, Commit-Nachricht, Programmierstil, PHPStan und Tests auf den unterstützten
  PHP-Versionen (`doc/eng-us/bc/6.0/ci.md`).
- Er wird begutachtet, und jeder Pull-Request wird gemergt. Solange er offen ist, fassen Sie ihn zusammen (Squash) und
  setzen ihn per Rebase auf `main` auf, wie es die Reviews verlangen (`git fetch origin`, `git rebase -i origin/main`,
  `git push --force-with-lease` nur auf den Branch Ihres Pull-Requests); sobald er gemergt ist, wird die Historie von
  `main` nie umgeschrieben (`doc/eng-us/bc/6.0/ci.md`).
- Ein Pull-Request, der ein beabsichtigtes Verhalten ändert, eine
  [negative Funktion](../projekt/unterstuetzung-erhalten.md#negative-funktionen), auf die sich andere Sites verlassen,
  braucht eine klare Begründung und kann mehrere Runden der Prüfung durchlaufen, bevor er angenommen und gemergt wird.
  Eine Option hinzuzufügen, statt den Standard zu ändern, ist der leichtere Weg.

## Lizenz

„Mit Ihrem Beitrag erklären Sie sich damit einverstanden, dass Ihre Beiträge unter der GNU General Public License,
Version 2.0 oder höher, lizenziert werden“ (CONTRIBUTING.md).

**Übertragung des Urheberrechts (CLA).** 7x und die Exponential Foundation mergen einen Code-Beitrag zum Kernel, in
welcher Form auch immer eingereicht (Pull-Request, Patch, Issue oder E-Mail), erst, nachdem Sie das Contributor
Licensing Agreement unterzeichnet haben. **Mit Unterschrift und Einreichung übertragen Sie das Urheberrecht an Ihrem
Beitrag an 7x, das damit sein Eigentümer wird, und geben die Kontrolle darüber ab:** 7x darf ihn auf jede Weise
verwenden, ändern, neu lizenzieren und verbreiten, auch unter anderen Lizenzen und in kommerziellen Produkten, ohne Sie
zu fragen oder zu bezahlen, und Sie können ihn nicht zurückziehen und keine Bedingungen daran knüpfen. Eine Unterschrift
deckt alle Ihre künftigen Beiträge ab. Erweiterungen, die Sie für sich selbst schreiben, sind nicht betroffen. So
unterzeichnen Sie: [Übertragung des Urheberrechts (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla).

## Ideen für einen ersten Beitrag

- Ein Test, der einen gemeldeten Fehler reproduziert.
- Eine Korrektur einer PHP-Deprecation für die neueste PHP-Version.
- Eine kleine Verbesserung der Benutzerfreundlichkeit in der Administration.

## Verwandte Seiten

- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Testen und Fehler melden](testen-und-fehler-melden.md)
- [Code zurückgeben (für Partner)](../partner/code-zurueckgeben.md)
- [Zum Projekt beitragen](../projekt/zum-projekt-beitragen.md): alle Wege, beim Aufbau von Exponential zu helfen
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
