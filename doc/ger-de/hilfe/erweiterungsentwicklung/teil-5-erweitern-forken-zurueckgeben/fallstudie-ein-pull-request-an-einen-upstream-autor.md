---
title: "Fallstudie: ein Pull Request an einen Upstream-Autor"
audience: developer, app-creator
category: extension-development
order: 170
summary: Ein echter Pull Request an das Projekt einer anderen Organisation, Abschnitt für Abschnitt gelesen - was kaputt war, die einzeilige Korrektur, die erhaltene Sicherheitsabsicht, Tests, die ohne sie fehlschlagen, und was nicht geprüft wurde.
keywords: Fallstudie, Pull Request, Upstream, request-shield, Beschreibung, Tests, Referrer-Policy, Origin, Review, Beitrag, case study, pull request, upstream, description, tests, review, contribution
type: help
---
# Fallstudie: ein Pull Request an einen Upstream-Autor

Teil 5, Kapitel 17, das letzte dieses Teils. Frühere Kapitel haben beschrieben, was ein guter Pull Request enthält.
Dieses liest einen echten: Pull Request 4 an `cjw-network/request-shield` auf GitHub, eröffnet am 8. Oktober 2026 und
am selben Tag übernommen. request-shield ist ein Projekt einer anderen Organisation (unter MIT-Lizenz), das
Exponential-Websites verwenden können; der Pull Request kam aus diesem Projekt, so wie ihn ein externer Beitragender
schicken würde. Sie können ihn vollständig auf GitHub lesen.

## Der Titel

```text
Fixed: The dashboard login works in a browser (Referrer-Policy same-origin instead of no-referrer)
```

Zuerst das Symptom, das ein Benutzer sieht, die Korrektur in Klammern. Ein Maintainer, der nur Titel liest, weiß,
worum es geht.

## „What was broken“

Die Beschreibung beginnt mit dem Fehler, erklärt von der Ursache bis zum Symptom:

- das Anmeldeformular des Dashboards nimmt einen POST nur an, wenn sein `Origin`-Host der Host der Anfrage ist
  („That check is right.“);
- jede Dashboard-Seite wird mit `Referrer-Policy: no-referrer` ausgeliefert, und mit dieser Richtlinie senden
  Chromium und Firefox beim eigenen Formular-POST der Seite `Origin: null` („checked in both with Playwright“);
- also wurde jede Anmeldung aus einem echten Browser mit 403 abgewiesen, „whatever the token“.

Dann ein Satz, den viele Beschreibungen weglassen: warum die vorhandenen Tests den Fehler nicht gefunden haben. Sie
haben entweder ohne `Origin` gesendet (wie curl) oder mit einem von Hand geschriebenen korrekten, „No browser sends
that combination“. Das sagt dem Maintainer, dass die Tests nicht falsch waren, nur unvollständig.

## „The fix“

Eine Zeile in einer Datei: `same-origin` statt `no-referrer`. Die Beschreibung ergänzt, was sich **nicht** geändert
hat: „The `Origin` check stays as strict as it was: `Origin: null` is still refused“, und warum es falsch wäre, ihn
anzunehmen („a form from a sandboxed frame or any other opaque origin“).

Wer als Beitragender Sicherheitscode anfasst, muss zeigen, dass der Schutz intakt ist. Es ausdrücklich zu sagen,
erspart dem Maintainer, es selbst herauszufinden.

## „Why same-origin keeps the privacy intent“

Der ursprüngliche Autor hatte `no-referrer` aus einem Grund gewählt: Dashboard-Adressen dürfen nicht über Links an
andere Websites durchsickern. Der Pull Request erklärt, dass `same-origin` demselben Zweck dient: Andere Websites
erhalten weiterhin keinen `Referer`; nur die eigenen Anfragen des Dashboards tragen einen.

Das ist Respekt in technischer Form: Der Beitragende hat verstanden, warum das Original so geschrieben wurde, und
dessen Ziel beibehalten, statt nur das Symptom verschwinden zu lassen.

## „Tests“

Die neuen Prüfungen wurden den beiden vorhandenen Anmeldetests hinzugefügt (einem Unit-Test der Sperre und einem
End-to-End-Test mit dem eingebauten Server), und die Beschreibung zeigt, wie sie ohne die Korrektur fehlschlagen und
mit ihr bestehen:

```text
without the fix:  ... FAIL - 4 passed, 2 failed, 0 skipped (PHP 8.5.11)
with the fix:     PASS - 6 passed, 0 failed, 0 skipped (PHP 8.5.11)
```

## Der ehrliche Teil

Der letzte Abschnitt ist der, der Vertrauen schafft:

- die gesamte Testsuite lief auf `main` und auf dem Branch, auf beiden mit demselben Ergebnis, in zwei
  Konfigurationen;
- ein Test schlägt auf beiden fehl, und die Beschreibung erklärt warum (auf der Maschine ist eine PHP-Erweiterung
  geladen, die der Test als fehlend erwartet) und dass das nichts mit der Änderung zu tun hat;
- ein Lauf gegen eine gebaute Datei bricht ab, bevor er beginnt, auch auf `main`, „so it is not caused by this change“;
- die statische Analyse „was not run locally: the development tools are not installed on the test machine. The change
  is a string literal in an existing array plus test code.“

Nichts wird verborgen, und nichts wird behauptet, was nicht getan wurde.

## Die Dateien

Vier Dateien wurden geändert: die einzeilige Korrektur, der Test, eine Zeile in `CHANGELOG.md` unter „Unreleased /
Fixed“ und die Dokumentationsseite der Funktion, die den Header nennt. Ein Designvorschlag, der noch den alten Header
nennt, wurde „left as written, as a record of the design“: Der Beitragende hat aktualisiert, was die Gegenwart
beschreibt, und die Geschichte unangetastet gelassen.

## Was Sie übernehmen sollten

| Gewohnheit | In diesem Pull Request |
|---|---|
| Ursache vor Symptom | „What was broken“, Schritt für Schritt |
| Warum die Tests es übersehen haben | ein Absatz |
| Kleinste mögliche Korrektur | eine Zeile |
| Schutz erhalten, und das auch gesagt | `Origin: null` wird weiterhin abgewiesen |
| Ursprüngliche Absicht respektiert | der Datenschutzgrund für den alten Header, beibehalten |
| Tests, die ohne die Korrektur fehlschlagen | mit ihrer Ausgabe gezeigt |
| Dieselben Ergebnisse wie auf main, in einer Tabelle | zwei Konfigurationen |
| Was nicht geprüft wurde, und warum | statische Analyse, ein bereits vorhandener Fehlschlag |
| Changelog und Dokumentation im selben Pull Request aktualisiert | `CHANGELOG.md` und die Seite der Funktion |

## Verwandte Seiten

- Zurück: [Fallstudie: ein Paket aus einem anderen Projekt mit Namensnennung portieren](fallstudie-aus-einem-anderen-projekt-portiert.md)
- Weiter: [Teil 6: Theme-Entwickler](../teil-6-theme-entwickler/README.md)
- [Mit Pull Requests zurückgeben](mit-pull-requests-zurueckgeben.md)
- [Pull-Request-Etikette](pull-request-etikette.md)
- Der Pull Request selbst: `github.com/cjw-network/request-shield/pull/4`
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
