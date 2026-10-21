# „Dieses Formular ist abgelaufen“: Ablehnungen durch das Formular-Token

Diese Seite ist für Website-Betreiber, die Seiten oder Warnungen „Formular abgelaufen“ sehen, und für Entwickler, die sie
behandeln. Exponential schützt jedes Formular mit einem Formular-Token (der Extension `ezformtoken` im Kernel) gegen
Cross-Site Request Forgery. Kam ein POST ohne sein Token oder mit einem falschen an, sah der Besucher früher eine Seite
mit 500, und das Protokoll verzeichnete „Unexpected error“ für etwas, das in Wahrheit eine Ablehnung ist. Seit dem
27. September 2026 ist die Antwort eine verständliche Seite mit dem richtigen Status.

## Was ein Besucher sieht

Eine Seite, die in einfachen Worten sagt:

- das Formular ist abgelaufen: Die Seite war lange geöffnet, oder das Formular wurde von einer anderen Seite gesendet;
- **nichts wurde gespeichert**;
- **Laden Sie das Formular neu und senden Sie es erneut**, oder gehen Sie zur Startseite (in der
  Administrationsoberfläche zum Dashboard).

Einem abgemeldeten Besucher wird gesagt, dass er sich möglicherweise neu anmelden muss; war das abgelehnte Formular
selbst ein Anmeldeformular, bittet die Seite darum, sich erneut anzumelden. Der Wortlaut ist für ein fehlendes und ein
falsches Token derselbe und spricht nicht von Sicherheits-Tokens und Sitzungen.

Die Seite ist `design:error/kernel/6.tpl`, mitgeliefert in `design/standard`; die Admin-Designs greifen auf sie zurück.
Prüfen:

```bash
ls design/*/templates/error/kernel/6.tpl
```

Solange ein Design kein solches Template hat oder es leer ausgegeben wird, weil der Template-Cache vor seiner Existenz
aufgebaut wurde, wird eine eingebaute Erklärung gezeigt.

## Was Browser und Skripte erhalten

| | Wert |
|---|---|
| HTTP-Status | `403 Forbidden` (Kernel-Fehler 6, zugeordnet in `error.ini`) |
| Header | `Cache-Control: no-store`, die konfigurierten Sicherheits-Header |
| HTML-Anfrage | Kernel-Fehler 6 über das Fehlermodul, im Seitenlayout des Moduls, an das gesendet wurde; keine Weiterleitung; Sitzung und gesendete Daten bleiben, wie sie sind |
| XHR, `Accept: application/json` | Ein JSON-Body mit derselben Meldung |
| Titel und Pfad der Fehleransicht | „Formular abgelaufen“ statt „kernel (6)“ |

Im Protokoll schreibt eine Ablehnung eine Zeile nach `var/log/warning.log` mit der Prüfung (fehlend oder falsch), der
Methode, dem Pfad und dem Siteaccess, **nie ein Token**. Dieselbe Ablehnung noch einmal (gleiche Prüfung, gleicher
Siteaccess, Pfad und Client) innerhalb von `RefusalLogCollapseSeconds` wird gezählt statt geschrieben, und die nächste
Zeile nennt die Anzahl.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `settings/site.ini` | `HTMLForms` | `RefusalLogCollapseSeconds` | `60` | Zeitfenster zum Zusammenfassen wiederholter Ablehnungen. `0` schreibt jede |
| `settings/site.ini` | `HTMLForms` | `Secret` | Platzhaltertext | CSRF-Geheimnis. Setzen Sie pro Projekt Ihr eigenes |
| `settings/error.ini` | `ErrorSettings-kernel` | `HTTPError[6]` | `403` | Status der Ablehnung |

## Für Entwickler

- `ezpFormTokenException` trägt die fehlgeschlagene Prüfung und den Code 403. Der alte Code warf eine einfache
  `Exception`, die nichts bis zum Handler für nicht abgefangene Ausnahmen auffing.
- `ezpFormTokenRefusal` baut die Antwort: Status, Header, die Template-Parameter von `design:error/kernel/6.tpl`
  (`reason`, `reason_code`, `referrer`, `retry_url`, `is_ajax`, `title`, `message`, `action`), den JSON-Body und die
  eingebaute Seite.
- Der Web-Kernel fängt die Ausnahme bei `request/input` ab. Eine Ablehnung, die anderswo entkommt (der Kernel für REST
  oder für das Baummenü, ein Modul, das die Prüfung selbst ausführt, der Handler für nicht abgefangene Ausnahmen),
  erhält dieselbe 403 und dieselbe Warnzeile.
- Die eingebauten Texte sind feste Aufrufe von `ezpI18n` in den Kontexten `kernel/error/formtoken` und
  `design/standard/error/formtoken`, übersetzt ins Deutsche und in `eng-US`. Eine Ablehnung ohne Treffer schreibt keine
  Warnung `http_response_code() has no effect` mehr.
- Eine Seite mit einem Formular-Token wird mit `Cache-Control: private, no-cache, must-revalidate` gekennzeichnet,
  sodass kein gemeinsamer Cache sie mit einem Token speichern und einem anonymen Besucher ausliefern kann.

## Verwandte Seiten

- [Sicherheitsstandards vom September 2026](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)
- [Härtung von Datentypen und Eingaben](../../spezifikationen/6.0/datentyp-eingabehaertung.md)
- [RAD-Sicherheit](../../bc/6.0/rad-sicherheit.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik, 16. bis 30. September 2026](../../geschichte/2026/2026-09b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
