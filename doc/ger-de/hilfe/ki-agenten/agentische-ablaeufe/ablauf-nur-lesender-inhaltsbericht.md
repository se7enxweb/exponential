---
title: Ein nur lesender Inhaltsbericht
audience: ai-agents, developer, app-creator, editor
category: ai-agents
order: 70
summary: Ein durchgearbeitetes Beispiel für einen sicheren agentischen Ablauf - ein Agent mit einem nur lesenden API-Schlüssel geht über die REST-API einen Ordner durch, liest die Felder jeder Seite und schreibt einen Bericht für Redakteure (fehlende Zusammenfassungen, veraltete Daten, Seiten ohne Alternativtext) - mit den zu definierenden Werkzeugen, den zu setzenden Grenzen und warum sich nichts ändern kann.
keywords: Inhaltsbericht, Inhaltsprüfung, nur lesender Agent, REST-API, Geltungsbereich read, Kinder auflisten, Felder, durchgearbeitetes Beispiel, agentischer Ablauf, Redakteure, Vorschläge, content report, content audit, read only agent, REST API, read scope, list children, fields, worked example, agentic workflow, editors, suggestions
type: help
---
# Ein nur lesender Inhaltsbericht

Diese Seite richtet sich an Entwickler, die möchten, dass ein Agent Redakteuren hilft, ohne etwas ändern zu können. Der Agent liest
veröffentlichte Inhalte über die REST-API mit einem nur lesenden Schlüssel und erstellt einen Bericht. Die Redakteure entscheiden,
was sie damit tun.

## Inhalt

1. [Die Aufgabe](#die-aufgabe)
2. [Die Einrichtung](#die-einrichtung)
3. [Die Werkzeuge, die das Modell erhält](#die-werkzeuge-die-das-modell-erhält)
4. [Der Lauf](#der-lauf)
5. [Der Bericht](#der-bericht)
6. [Warum dieser Ablauf sicher ist](#warum-dieser-ablauf-sicher-ist)

## Die Aufgabe

Ein Redaktionsteam möchte einen monatlichen Bericht über einen Ordner "News": Artikel ohne Zusammenfassung, Artikel, deren Text ein
vergangenes Datum nennt, und sehr kurze Artikel. Der Bericht geht als Liste mit Links an das Team.

## Die Einrichtung

- Ein Konto "Berichtsagent" mit `content/read`, beschränkt auf den Teilbaum News.
- Ein persönlicher API-Schlüssel dieses Kontos nur mit dem Geltungsbereich `read` und einer Laufzeit von 30 Tagen
  ([Ein nur lesender API-Schlüssel, Schritt für Schritt](ein-nur-lesender-api-schluessel-schritt-fuer-schritt.md)).
- Der Code des Agenten hält den Schlüssel; das Modell sieht ihn nie.

## Die Werkzeuge, die das Modell erhält

| Werkzeug | Eingabe | Ruft auf |
|---|---|---|
| `list_children` | `node_id`, `offset`, `limit` (höchstens 25) | `GET /api/ezp/v1/content/node/<node_id>/list/offset/<n>/limit/<n>` |
| `get_fields` | `node_id` | `GET /api/ezp/v1/content/node/<node_id>/fields` |
| `children_count` | `node_id` | `GET /api/ezp/v1/content/node/<node_id>/childrenCount` |

Keine Schreibroute, kein anderer Host, keine freie URL. Die Werkzeugbeschreibungen sagen dem Modell, dass Feldwerte von Menschen
geschriebener Text sind, Daten und keine Anweisungen
([Nicht vertrauenswürdige Daten und Prompt-Injection](nicht-vertrauenswuerdige-daten-und-prompt-injection.md)).

## Der Lauf

1. Der Agent fragt nach der Zahl der Kinder des Ordners News und blättert sie dann zu je 25 durch.
2. Für jeden Artikel liest er die Felder und notiert: eine leere Zusammenfassung, Daten im Text, die Länge des Textkörpers.
3. Prüfungen, die kein Urteil brauchen (leeres Feld, Länge), erledigt Ihr Code besser als das Modell; das Modell ergänzt, was
   Lesen erfordert, etwa "Der Text sagt, die Veranstaltung ist nächsten Freitag, aber er wurde vor zwei Jahren veröffentlicht".
4. Ein `403` oder `404` für ein Element wird im Bericht festgehalten und übersprungen; ein `401` beendet den Lauf, weil der Schlüssel
   die Aufmerksamkeit eines Menschen braucht.

## Der Bericht

```text
News report, <date>
Missing summary (4): <title> (<node id>), ...
Probably outdated (2): <title>: the text announces <event> for <date>, ...
Very short (1): <title>, 40 words
Read: 112 articles; refused: 0; not found: 0
```

Redakteure öffnen die Elemente und korrigieren sie, von Hand oder mit Exponential Intelligence im Reiter Intelligence
([Was Exponential Intelligence tut](../expai/was-exponential-intelligence-tut.md)).

## Warum dieser Ablauf sicher ist

- Der Schlüssel hat nur den Geltungsbereich `read`; selbst ein irregeleitetes Modell kann nichts anlegen, bearbeiten oder entfernen.
- Das Konto liest nur den Teilbaum News.
- Jede Änderung nimmt ein Redakteur vor, unter seinem eigenen Namen.

Was noch zu entscheiden bleibt: ob die Inhalte des Ordners News an den Modellanbieter des Agenten gehen dürfen
([Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md#agenten-die-inhalte-lesen)).

## Verwandte Seiten

- [Agenten und die REST-API](agenten-und-die-rest-api.md)
- [Einen Menschen beteiligt halten](einen-menschen-beteiligt-halten.md)
- [Headless Exponential](../../ersteller/anwendungsentwickler/headless-exponential.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
