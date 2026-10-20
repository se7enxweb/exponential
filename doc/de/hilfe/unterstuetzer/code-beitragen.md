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
  PHP-Versionen (`doc/en/us/bc/6.0/ci.md`).
- Er wird begutachtet. „Jeder Pull-Request wird gemergt, und seine Commits bleiben so erhalten, wie der Mitwirkende
  sie geschrieben hat, und werden niemals umgeschrieben“ (`doc/en/us/bc/6.0/ci.md`); was geändert werden muss, wird in
  Commits darüber geändert.

## Lizenz

„Mit Ihrem Beitrag erklären Sie sich damit einverstanden, dass Ihre Beiträge unter der GNU General Public License,
Version 2.0 oder höher, lizenziert werden“ (CONTRIBUTING.md).

## Ideen für einen ersten Beitrag

- Ein Test, der einen gemeldeten Fehler reproduziert.
- Eine Korrektur einer PHP-Deprecation für die neueste PHP-Version.
- Eine kleine Verbesserung der Benutzerfreundlichkeit in der Administration.

## Verwandte Seiten

- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Testen und Fehler melden](testen-und-fehler-melden.md)
- [Code zurückgeben (für Partner)](../../../en/us/help/partners/contributing-code-back.md)
- [Zum Projekt beitragen](../projekt/zum-projekt-beitragen.md): alle Wege, beim Aufbau von Exponential zu helfen
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
