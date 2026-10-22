---
title: Web- und Verlaufswerkzeuge
audience: ai-agents, admin, editor
category: ai-agents
order: 34
summary: Die drei nur lesenden Werkzeuge von Exponential Intelligence, die über den Inhaltsbaum hinausblicken - web_fetch für Seiten von Websites auf der Erlaubnisliste, web_search über einen Suchdienst und project_history für die eigenen Commits und Release Notes des Teams - mit der Richtlinie expai/web, ihren Schutzmaßnahmen (Erlaubnisliste, SSRF-Schutz, robots.txt, Obergrenzen), der Datenrichtlinie für Suchen und warum nach Webtext kein Entwurf geschrieben wird.
keywords: web_fetch, web_search, project_history, Webwerkzeuge, expai/web, AllowedDomains, SSRF-Schutz, robots.txt, Brave Search, externe Daten, Schreibsperre, Git-Verlauf, Release Notes, web tools, SSRF guard, outside data, write lock, git history, release notes
type: help
---
# Web- und Verlaufswerkzeuge

Diese Seite richtet sich an Administratoren, die entscheiden, ob der Assistent über die Website hinausblicken darf, und an Redakteure,
die ihn verwenden. Drei nur lesende Werkzeuge bringen externen Text in ein Gespräch. Jedes wird nur Benutzern mit der Richtlinie
`expai/web` angeboten und erst, wenn es konfiguriert ist
([Dokumentation der Erweiterung: Werkzeuge](https://github.com/se7enxweb/expai/blob/main/doc/tools.md)).

## Inhalt

1. [Die drei Werkzeuge](#die-drei-werkzeuge)
2. [web_fetch](#web_fetch)
3. [web_search](#web_search)
4. [project_history](#project_history)
5. [Keine Entwürfe nach Webtext](#keine-entwürfe-nach-webtext)
6. [Wer sie erhält](#wer-sie-erhält)

## Die drei Werkzeuge

| Werkzeug | Liest | Sendet nach außen |
|---|---|---|
| `web_fetch` | Eine Seite einer Website auf der Erlaubnisliste, als Markdown | Nur die URL |
| `web_search` | Suchergebnisse eines Suchdienstes | Die Abfrage |
| `project_history` | Commits lokaler Klone und öffentliche Release Notes | Nichts über die Inhalte |

Jedes Ergebnis sagt, dass es Daten sind, keine Anweisungen. Das Nutzungsprotokoll hält den Host fest, den das Werkzeug gelesen hat,
nie die URL oder die Abfrage.

## web_fetch

- Nur Hosts auf `[WebSettings] AllowedDomains[]` (ein genauer Host oder `*.domain`), verglichen nach Kleinschreibung und Punycode,
  sodass ähnlich aussehende Hosts nicht passen.
- Ein **SSRF-Schutz**: Der Host wird einmal aufgelöst und abgelehnt, wenn eine Adresse Loopback, privat, Link-Local (einschließlich
  der Metadatenadresse von Clouds), Carrier-Grade-NAT, unspezifiziert oder Multicast ist; die Verbindung wird an die geprüfte Adresse
  gebunden; Weiterleitungen nur zum selben Host und erneut geprüft; standardmäßig nur die Ports 80 und 443.
- Beachtet robots.txt; Obergrenzen für Größe, Zeit und die Zahl der Abrufe je Durchgang (standardmäßig 5); nur Texttypen.
- Nichts außer der URL wird gesendet: keine Cookies, Zugangsdaten, Referrer oder Body. Eine URL kann in ihrer Abfrage trotzdem Text
  tragen, halten Sie die Erlaubnisliste also auf Websites beschränkt, denen Sie vertrauen.
- Seitentext wird in Markierungen "Begin/End of outside data" eingefasst.

## web_search

- Der Dienst wird in `[SearchSettings] Provider` konfiguriert (Brave Search ist der mitgelieferte); das Werkzeug wird nur angeboten,
  wenn sein Schlüssel in `settings/override` gesetzt ist.
- Die Abfrage verlässt die Website, deshalb wird der Knoten des Panels zuerst gegen `Region` und `Retention` des Suchdienstes
  geprüft, wie es die Datenrichtlinie verlangt, und die Werkzeugbeschreibung sagt dem Modell, nie Inhalte der Website in eine Abfrage
  zu schreiben ([Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md)).
- Standardmäßig höchstens 3 Suchen je Durchgang.

## project_history

- Liest die eigenen Repositorys des Teams: Commits aus lokalen Klonen unter `[HistorySettings] Roots[]` und öffentliche Release
  Notes.
- git läuft ohne Shell, mit festem Format und wörtlichen Pfaden, nur `log`, `ls-files` und `rev-parse`, in einer sauberen Umgebung,
  mit Zeit- und Größengrenzen.
- Ausgeschlossene Pfade und unversionierte Dateien werden nie genannt oder gelesen; Autorennamen sind standardmäßig aus.
- Nützlich für Fragen wie "was hat sich diesen Monat an der Newsletter-Erweiterung geändert?" beim Schreiben von
  Release-Neuigkeiten.

## Keine Entwürfe nach Webtext

Nach einem Ergebnis von `web_fetch` oder `web_search` **wird kein Entwurf geschrieben, solange dieses Ergebnis Teil des Gesprächs
ist**, bis ein neues Gespräch beginnt. Das Modell sagt dem Redakteur, was es gefunden hat; um daraus zu schreiben, beginnt der
Redakteur ein neues Gespräch mit dem, was gebraucht wird. `project_history` (die eigene committete Arbeit des Teams) hält das
Schreiben nicht an.

Das ist die wichtigste Abwehr des Assistenten gegen Anweisungen, die in Webseiten versteckt sind
([Nicht vertrauenswürdige Daten und Prompt-Injection](../agentische-ablaeufe/nicht-vertrauenswuerdige-daten-und-prompt-injection.md)). Prüfen Sie
Links in Entwürfen aus Webergebnissen vor der Veröffentlichung.

## Wer sie erhält

Die Richtlinie `expai/web` mit der Einschränkung **Tool** (`web_fetch`, `web_search`, `project_history`); ohne die Einschränkung
alle drei ([Berechtigungen für KI-Funktionen](berechtigungen-fuer-ki-funktionen.md)). EI: Summary listet die erlaubten Domains, den
Suchdienst mit Region, Aufbewahrung und Herkunft des Schlüssels und die Repositorys des Verlaufs.

## Verwandte Seiten

- [Was Exponential Intelligence tut](was-exponential-intelligence-tut.md)
- [Dienstwerkzeuge](dienstwerkzeuge.md)
- [KI-Entwürfe prüfen](../checklisten/ki-entwuerfe-pruefen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
