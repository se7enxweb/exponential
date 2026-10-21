---
title: Vor einem Release testen
audience: developer, designer, app-creator
category: extension-development
order: 110
summary: Was Sie ausführen, bevor Sie taggen - Lint auf der ältesten und der neuesten PHP-Version, die Tests der Erweiterung, Prüfungen der Metadaten, eine saubere Installation aus dem Repository mit Composer und jede Funktion von Hand - ein Tag lässt sich nicht korrigieren.
keywords: Testen, Release, Tests, saubere Installation, Smoke-Test, älteste PHP-Version, neueste PHP-Version, Checkliste, testing, release, php -l, tests, xmllint, composer install, clean install, smoke test, oldest PHP, newest PHP, checklist
type: help
---
# Vor einem Release testen

Teil 7, Kapitel 11. Ein Tag lässt sich nicht korrigieren, nachdem er gepusht wurde
([Tags und Releases sind dauerhaft](tags-sind-dauerhaft.md)), daher ist die Zeit, Fehler zu finden, davor. Dieses
Kapitel listet auf, was Sie prüfen, von Sekunden bis zu einer Stunde, auf genau dem Commit, den Sie taggen werden.

## 1. Der Baum ist das Release

```bash
git status            # nothing to commit, working tree clean
git log -1 --oneline  # the commit you will tag
```

Nicht committete Änderungen sind nicht im Release, egal, was Ihre lokale Website zeigt.

## 2. Automatische Prüfungen (Minuten)

| Prüfung | Befehl |
|---|---|
| Jede PHP-Datei lässt sich parsen, auf der ältesten und der neuesten PHP-Version, mit angezeigten Deprecations | `find . -name '*.php' -not -path './vendor/*' -print0 \| xargs -0 -n1 php -d error_reporting=-1 -l 2>&1 \| grep -v '^No syntax errors'` (einmal je PHP-Binary) |
| Die eigenen Tests der Erweiterung | `for t in tests/*test.php; do php "$t" \|\| break; done` oder `vendor/bin/phpunit`, je nachdem, was die Erweiterung verwendet |
| Die Metadaten lassen sich parsen | `php -l ezinfo.php`, `xmllint --noout extension.xml`, `composer validate` |
| Die Übersetzungen lassen sich parsen | `xmllint --noout translations/*/translation.ts` |
| Die Versionen stimmen überein | `grep -n "Version\|<version>" ezinfo.php extension.xml` |

Der CI-Workflow sollte bei jedem Push dasselbe ausführen
([Tests und CI für eine aktualisierte Erweiterung](../teil-4-aktualisieren/tests-und-ci-fuer-eine-aktualisierte-erweiterung.md));
sehen Sie sich vor einem Release sein Ergebnis für genau diesen Commit an.

## 3. Eine saubere Installation (15 Minuten)

Ihre Entwicklungs-Website hat die Erweiterung als Arbeitskopie, mit jedem Cache und jeder Einstellung, die Sie
unterwegs angelegt haben. Die Website eines Benutzers hat nichts davon. Installieren Sie den Commit, den Sie taggen
werden, so in eine frische Entwicklungs-Website, wie Benutzer es tun werden:

```json
{
    "repositories": [ { "type": "vcs", "url": "https://github.com/your-account/myext" } ],
    "require": { "your-account/myext": "dev-main" }
}
```

```bash
composer update your-account/myext
```

Folgen Sie dann Ihrer eigenen README, Wort für Wort: aktivieren, Caches leeren, Pakete importieren, Einstellungen
setzen. Jeder Schritt, den die README vergisst, zeigt sich hier.

## 4. Verwenden Sie sie (30 Minuten)

- Öffnen Sie jede Seite und jede View der Erweiterung, als Administrator und als Benutzer, der nur die Richtlinien der
  Erweiterung hat.
- Führen Sie jeden Cronjob-Teil und jedes CLI-Skript einmal aus.
- Probieren Sie die Fehlerfälle: ein leeres Formular, eine falsche ID, eine fehlende Einstellung.
- Mit eingeschalteter Debug-Ausgabe: kein Fehler, keine Warnung und keine Deprecation aus den Dateien der Erweiterung.
- Mit geöffneter Browser-Konsole: keine Skriptfehler, keine Meldungen der Content Security Policy.
- Admin-Seiten im hellen und im dunklen Modus.

## 5. Vom vorigen Release aktualisieren

Installieren Sie das vorige Release, verwenden Sie es und aktualisieren Sie dann auf den neuen Commit. Prüfen Sie, ob
Einstellungen, Inhalte und Tabellen erhalten bleiben und ob die Upgrade-Hinweise im Changelog vollständig sind. Wenn
das Update einen Schritt braucht (einen Cache, ein Skript), gehört er in die Upgrade-Hinweise ([Changelogs](changelogs.md)).

## Wenn etwas fehlschlägt

Beheben Sie es, committen Sie und beginnen Sie die Liste auf dem neuen Commit wieder bei Schritt 1. Taggen Sie nie
einen Commit, der eine Prüfung nicht bestanden hat, „weil die Korrektur trivial ist“: Die Korrektur ist ein neuer
Commit, und der Tag kommt auf diesen.

## Verwandte Seiten

- Zurück: [Pakete (.ezpkg) und der Paket-Ersteller](pakete-ezpkg.md)
- Weiter: [Ein Release ankündigen](ein-release-ankuendigen.md)
- [Tests für Erweiterungen](../teil-3-erweiterungsentwickler/tests-fuer-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
