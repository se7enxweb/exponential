---
title: Berechtigungen für KI-Funktionen
audience: ai-agents, admin
category: ai-agents
order: 31
summary: Die expai-Richtlinien (write, vector, log, settings, model_use, model_manage, lane_fast, chain, web, translation_sync, summary, services) und ihre Einschränkungen, wie sie mit den eigenen Inhaltsberechtigungen des Redakteurs zusammenwirken, und Rolleneinrichtungen für Redakteure, Übersetzer, Prüfer und Datenschutzbeauftragte.
keywords: Berechtigungen, Richtlinien, Rollen, expai/write, expai/vector, expai/model_use, expai/model_manage, expai/web, expai/summary, expai/log, Einschränkungen, ezjscore/call, minimale Rechte, Redakteursrolle, permissions, policies, roles, limitations, least privilege, editor role
type: help
---
# Berechtigungen für KI-Funktionen

Diese Seite richtet sich an Administratoren, die entscheiden, wer Exponential Intelligence wie weit verwenden darf. Zwei Ebenen
entscheiden: Die **expai-Richtlinien** sagen, welche Funktionen ein Benutzer erhält; die **eigenen Inhaltsrichtlinien** des Benutzers
sagen, was der Assistent für ihn lesen und schreiben darf. Der Assistent fügt nie eigene Rechte hinzu.

## Inhalt

1. [Zwei Ebenen](#zwei-ebenen)
2. [Die Richtlinien](#die-richtlinien)
3. [Was als der Redakteur läuft](#was-als-der-redakteur-läuft)
4. [Rolleneinrichtungen, die funktionieren](#rolleneinrichtungen-die-funktionieren)
5. [Eine Rolle einrichten](#eine-rolle-einrichten)

## Zwei Ebenen

| Ebene | Entscheidet | Beispiel |
|---|---|---|
| expai-Richtlinien | Welche Funktionen ein Benutzer sieht und aufrufen darf | `expai/write` zeigt den Reiter Intelligence |
| Inhaltsrichtlinien | Was der Assistent als dieser Benutzer lesen und schreiben darf | `content/create` für die Klasse article unter einem Ordner |
| Über beiden | Die Datenrichtlinie (welche Inhalte an welchen Anbieter gehen dürfen) und das Schreibtor (welche Modelle schreiben dürfen) | [Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md) |

In einer neuen Installation hat nur die Rolle Administrator die expai-Richtlinien. Anonyme Benutzer und Mitglieder ohne die
Richtlinie sehen nichts vom Assistenten.

## Die Richtlinien

Aus der [Berechtigungsseite](https://github.com/se7enxweb/expai/blob/main/doc/permissions.md) der Erweiterung:

| Richtlinie | Erlaubt | Einschränkung |
|---|---|---|
| `expai/write` | Den Reiter Intelligence, die Elemente des Bearbeitungsformulars, das Dashboard (eigene Nutzung), das eigene Protokoll, den Bericht Missing alt text | keine |
| `expai/vector` | Vector und seine Threads (mit `expai/write`) | Subtree, Section, Scope, Review |
| `expai/chain` | KI-Ketten starten und an ihnen handeln (mit `expai/write`) | Chain |
| `expai/translation_sync` | Check translations, Approve, Translate in full, Skip (mit `expai/write`) | keine |
| `expai/model_use` | Modelle von Anbietern außerhalb von `ProvidersWithoutPolicy[]` | Provider |
| `expai/model_manage` | Setup > EI: Models: Anbieter, Vorhandensein von Schlüsseln, Probeergebnisse, der lokale Modellserver | keine |
| `expai/web` | Die Web- und Verlaufswerkzeuge im Panel | Tool (`web_fetch`, `web_search`, `project_history`) |
| `expai/services` | Lesedienste von expservices, die sich für den Assistenten entschieden haben | Domain |
| `expai/lane_fast` | Die schnelle (Premium-)Lane anfordern | keine |
| `expai/log` | Die Anfragen und Versionen aller, die Website-Ansicht des Dashboards, Budgets | keine |
| `expai/settings` | Wo der Standard der Website gesetzt wird; angehaltene Jobs fortsetzen | keine |
| `expai/summary` | Die druckbare Seite EI: Summary; braucht keine andere expai-Richtlinie und keinen Inhaltszugriff | keine |

Der Browser erreicht den Assistenten über `ezjscore/call`, Redakteure brauchen also auch `ezjscore/call` (ohne Einschränkung oder mit
einer Funktionsliste, die `expai` enthält). Viele Installationen geben das allen angemeldeten Benutzern über die Rolle Member.

## Was als der Redakteur läuft

- **Lesen** läuft über die Prüfungen des Kernels für den aktuellen Benutzer: Lesezugriff auf Knoten und Objekte, die Einschränkungen
  des Benutzers für Kinder und Geschwister, die anlegbaren Klassen.
- **Anlegen** prüft `content/create` am Elternknoten für diese Klasse, Elternklasse und Sprache, mit jeder Einschränkung der Rolle.
  Das Objekt wird in der Sektion des Elternknotens angelegt, im Eigentum des Redakteurs.
- **Ändern** prüft `content/edit` in dieser Sprache.
- **Behalten, Senden und Verwerfen** funktionieren nur an Versionen, die der Assistent für diesen Redakteur geschrieben hat und die
  noch dessen eigene Entwürfe sind.

Der Assistent verwendet nie ein anderes Konto, wechselt nie den aktuellen Benutzer und hat keine Einstellung, selbst zu
veröffentlichen.

## Rolleneinrichtungen, die funktionieren

| Rolle | Richtlinien |
|---|---|
| Redakteur, der den Assistenten verwenden darf | `expai/write`, `ezjscore/call` (Funktionsliste `expai`) plus die üblichen Inhaltsrichtlinien des Redakteurs |
| Redakteur für eine Sektion, Massenarbeit immer geprüft | Zusätzlich `expai/vector` mit Subtree oder Section, Scope `node`, Review `staged` |
| Übersetzer | `expai/write`, `expai/translation_sync`, `content/edit` auf die Zielsprachen beschränkt |
| KI-Prüfer | `audit/read` auf den Kanal `ai` beschränkt und `expai/log`, wenn er das Nutzungsprotokoll sehen soll |
| Datenschutzbeauftragter | Nur `expai/summary` (zusätzlich `exphelp/read`, um die Hilfe zu lesen) |
| Administrator der Anbieter | `expai/model_manage`, `expai/settings` |

Geben Sie `expai/web` und `expai/services` nur dort, wo sie gebraucht werden: Sie bringen mehr Daten in das Gespräch, und
Webergebnisse sind Text von außen
([Nicht vertrauenswürdige Daten und Prompt-Injection](../agentische-ablaeufe/nicht-vertrauenswuerdige-daten-und-prompt-injection.md)).

## Eine Rolle einrichten

1. Setup > Rollen und Richtlinien > die Rolle > Neue Richtlinie.
2. Modul `expai`, die Funktion und die Einschränkung, falls sie eine hat.
3. Weisen Sie die Rolle der Gruppe der Redakteure zu, optional auf einen Teilbaum beschränkt.
4. Melden Sie sich als Testredakteur an und prüfen Sie, dass der Reiter Intelligence erscheint und tut, was Sie erwarten.

Die allgemeine Anleitung: [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md).

## Verwandte Seiten

- [Rollen und minimale Rechte](../steuerung/rollen-und-minimale-rechte.md)
- [Zugriffskontrolle im Code](../../ersteller/anwendungsentwickler/zugriffskontrolle-im-code.md)
- [Der Hauptschalter und der Veto-Hook](hauptschalter-und-veto.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
