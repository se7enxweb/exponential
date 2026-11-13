---
title: "Request Shield: die Regeln"
audience: admin, hosting, developer
category: administration
order: 40
summary: Die Seite Regeln unter Setup > Request Shield und die Regeldateien dahinter - welche Hauptregeldatei der Shield verwendet, die mitgelieferten Exponential-Regeln, die eigene Datei der Website und ihre Kopien, ob sich die Regeln übersetzen lassen, und die Befehle, die sie auf dem Server prüfen, testen und verfolgen.
keywords: Request Shield, Regeln, requestshield/rules, Regeldatei, request-shield.rules, exponential.rules, exponential-admin-uri.rules, exponential-admin-host.rules, settings/request-shield, EXP_REQUEST_SHIELD_RULES, include, übersetzen, check, test, trace, expect, host, www.example.org, ShowRuleText, Regel-ID, rules, rule file, compile
type: help
---
# Request Shield: die Regeln

Der Shield wird über **Regeldateien** konfiguriert, einfache Textdateien auf dem Server. **Setup > Request Shield >
Regeln** (`/requestshield/rules`) zeigt sie, nur lesend: ob sie sich übersetzen lassen, die Hauptdatei und die
eingebundenen Dateien und die Befehle, die sie prüfen. Geändert werden die Regeln auf dem Server, mit Ausnahme des
Modus, den [die Schalter](die-schalter.md) ändern können.

## Inhalt

1. [Welche Datei der Shield verwendet](#welche-datei-der-shield-verwendet)
2. [Die mitgelieferten Dateien](#die-mitgelieferten-dateien)
3. [Die eigene Datei der Website](#die-eigene-datei-der-website)
4. [Die Regeln auf dem Server prüfen](#die-regeln-auf-dem-server-prüfen)
5. [Eine Regel lesen](#eine-regel-lesen)

## Welche Datei der Shield verwendet

Der Shield liest eine **Hauptregeldatei**, die weitere einbinden kann. Es ist die erste von:

1. der Datei, die `config.php` mit `EXP_REQUEST_SHIELD_RULES` nennt;
2. der eigenen Datei der Website, `settings/request-shield/request-shield.rules`;
3. der Vorgabe der Extension, `extension/exp_request_shield/rules/request-shield.rules`.

Die Seite sagt, welche verwendet wird und warum. Ist es die Vorgabe der Extension, sagt sie das: Aktualisierungen der
Extension ersetzen diese Datei, deshalb sollte eine echte Website ihre eigene haben.

## Die mitgelieferten Dateien

| Datei | Was sie ist |
|---|---|
| `request-shield.rules` | die Hauptdatei: bindet die Exponential-Regeln ein, setzt den Modus (enforce), den Speicher und das Protokoll |
| `exponential.rules` | die Regeln für die öffentliche Website: System-URLs, interne Dateien, Formulare, die Suche, das Tempo |
| `exponential-admin-uri.rules` | ergänzt die Administration als Siteaccess `/admin` |
| `exponential-admin-host.rules` | ergänzt die Administration auf einem eigenen Hostnamen |

Die drei `exponential*.rules` stammen unverändert aus der Bibliothek; warum es jede Regel gibt, erklärt deren
[Anwendungsfall Exponential](https://github.com/cjw-network/request-shield/blob/main/docs/use-cases/exponential.md)
(auf Englisch).

## Die eigene Datei der Website

Die Datei der Website liegt in `settings/request-shield/`, mit Kopien der drei `exponential*.rules` daneben (ein
`include` darf mit `..` nicht aus dem Verzeichnis seiner Datei hinausgreifen). Der erste Moduswechsel auf der
Übersicht legt diese Dateien an; sonst kopiert sie die Person, die den Server betreut
([der Leitfaden](../../anleitungen/request-shield.md#2-der-website-eigene-regeln-geben)).

Darin nennt die Website ihre eigenen Hostnamen, sodass eine Anfrage an jeden anderen Namen abgewiesen wird. Behalten
Sie `www.example.org` in einer `host`-Zeile: Die mitgelieferten Beispiele verwenden diese Dokumentationsdomain, und
die Tests lassen ohne sie fünf davon scheitern. Kein echter Besucher sendet sie.

## Die Regeln auf dem Server prüfen

Die Seite nennt die Befehle; führen Sie sie als der Benutzer aus, unter dem PHP für die Website läuft:

```bash
php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield test  settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.org/the/path"
```

- `check` benennt jeden Fehler mit Datei und Zeile.
- `test` entscheidet jedes `expect`-Beispiel neben den Regeln; nach jeder Änderung muss er bestehen.
- `trace` zeigt, wie eine einzelne Anfrage Regel für Regel entschieden wird: die schnellste Antwort auf die Frage
  „Warum wurde das abgewiesen?“.

## Eine Regel lesen

Jede Regel hat eine ID in eckigen Klammern, und `expect`-Zeilen daneben sind Beispiele:

```
[SITE-DASH]  restrict /rs/** to 127.0.0.1 ::1
expect GET /rs/stats/overview from 198.51.100.27 403 by SITE-DASH
```

Diese Regel sperrt die eigenen Seiten der Bibliothek unter `/rs` für alle außer dem Server, und das Beispiel sagt,
dass eine Anfrage von außen durch diese Regel 403 erhält. Dieselbe ID erscheint im Protokoll. Die Regelsprache ist in
der Bibliothek dokumentiert (auf Englisch):
[Regeldateien](https://github.com/cjw-network/request-shield/blob/main/docs/reference/rule-files.md),
[Einstellungen](https://github.com/cjw-network/request-shield/blob/main/docs/reference/settings.md).

Der Text der Dateien wird gezeigt, wenn in `requestshield.ini [AdminSettings]` `ShowRuleText=enabled` steht (die
Vorgabe).
