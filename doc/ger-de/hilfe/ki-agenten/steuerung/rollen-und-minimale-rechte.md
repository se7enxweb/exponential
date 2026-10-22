---
title: Rollen und minimale Rechte
audience: ai-agents, admin, developer
category: ai-agents
order: 84
summary: Wie Sie KI-Assistenten und Agenten in Exponential genau die Rechte geben, die sie brauchen - als wer jeder Teil läuft (der Redakteur, der Eigentümer eines Schlüssels oder Tokens), eigene Konten und Rollen für Agenten, Einschränkungen nach Teilbaum, Sektion, Klasse und Sprache, enge Zugangsdaten und wie Sie wirksame Rechte mit exp:access:check prüfen, bevor etwas läuft.
keywords: minimale Rechte, Rollen, Richtlinien, Einschränkungen, eigenes Konto, Agentenkonto, Geltungsbereiche von API-Schlüsseln, expservices-Token, Teilbaum-Einschränkung, Sektions-Einschränkung, exp:access:check, Rollenprüfung, least privilege, roles, policies, limitations, dedicated account, agent account, API key scopes, subtree limitation, section limitation, role audit
type: help
---
# Rollen und minimale Rechte

Diese Seite richtet sich an Administratoren, die entscheiden, was ein Assistent oder ein Agent tun darf. In Exponential handelt
jeder KI-Teil als ein Konto, und die Rollen und Richtlinien des Kontos sind das einzige Berechtigungsmodell. Minimale Rechte
bedeuten daher eines: das richtige Konto mit der kleinsten Rolle, die die Aufgabe erledigt.

## Inhalt

1. [Als wer jeder Teil läuft](#als-wer-jeder-teil-läuft)
2. [Ein eigenes Konto je Agent](#ein-eigenes-konto-je-agent)
3. [Eine Rolle eingrenzen](#eine-rolle-eingrenzen)
4. [Die Zugangsdaten eingrenzen](#die-zugangsdaten-eingrenzen)
5. [Wirksame Rechte prüfen](#wirksame-rechte-prüfen)
6. [Regelmäßig prüfen](#regelmäßig-prüfen)

## Als wer jeder Teil läuft

| Teil | Läuft als | Zusätzliche Grenzen |
|---|---|---|
| Exponential Intelligence | Der angemeldete Redakteur; eingereihte Arbeit als der Benutzer, der sie eingereiht hat | Die Richtlinien `expai/*`, die Datenrichtlinie, das Schreibtor, der Schreibbereich des Panels oder Threads |
| Dienstwerkzeuge des Assistenten | Der angemeldete Redakteur | `expai/services`, `ezjscore/call`, die eigene Zugriffsregel jedes Dienstes ([Dienstwerkzeuge](../expai/dienstwerkzeuge.md)) |
| REST-API | Der Eigentümer des Schlüssels oder OAuth-Tokens | Die Geltungsbereiche des Schlüssels |
| expservices | Der Benutzer des Tokens | Die Zugriffsregel jedes Dienstes |
| Ein Coding-Agent auf dem Server | Der Systembenutzer, unter dem Sie ihn starten | Was dieser Benutzer lesen und ausführen darf; halten Sie ihn von der Produktion fern ([Coding-Agenten in einem Exponential-Projekt](../agentische-ablaeufe/coding-agenten-in-einem-exponential-projekt.md)) |

Kein Teil wechselt den Benutzer oder hat ein eigenes Dienstkonto.

## Ein eigenes Konto je Agent

- Legen Sie ein Konto je Agent oder je automatisiertem Ablauf an, in einer Benutzergruppe für Agenten.
- Geben Sie ihm eine Rolle, die für diese Aufgabe gemacht ist; nie die Rolle Administrator, nie das Konto einer Person.
- Deaktivieren Sie das Konto, wenn der Agent außer Dienst geht; jeder Schlüssel und jedes Token eines deaktivierten Eigentümers hört
  sofort auf zu funktionieren.

## Eine Rolle eingrenzen

| Eingrenzen nach | Beispiel |
|---|---|
| Teilbaum | `content/create` nur unterhalb von "Partner-Veranstaltungen" |
| Sektion | `content/read` nur in der öffentlichen Sektion |
| Klasse | `content/create` nur für `event` |
| Sprache | `content/edit` nur in den Sprachen, die ein Übersetzer bearbeitet |
| Funktion | `content/read` ohne `content/remove` |

Für den Assistenten fügt `expai/vector` eigene Einschränkungen hinzu (Subtree, Section, Scope, Review), und `expai/model_use`,
`expai/web` und `expai/services` lassen sich nach Anbieter, Werkzeug und Domain einschränken
([Berechtigungen für KI-Funktionen](../expai/berechtigungen-fuer-ki-funktionen.md)).

## Die Zugangsdaten eingrenzen

- **REST**: ein persönlicher API-Schlüssel nur mit den nötigen Geltungsbereichen (oft nur `read`) und einer kurzen Laufzeit
  ([Ein nur lesender API-Schlüssel, Schritt für Schritt](../agentische-ablaeufe/ein-nur-lesender-api-schluessel-schritt-fuer-schritt.md)).
- **expservices**: ein Token mit kurzer Laufzeit und nur den Diensten, die Sie aus dem Katalog für das Modell des Agenten
  ausgewählt haben ([Agenten und expservices](../agentische-ablaeufe/agenten-und-expservices.md)).
- **Der Assistent**: Geben Sie `expai/web` und `expai/services` nur dort, wo sie gebraucht werden.

Ein nicht gewährter Geltungsbereich und ein nicht angebotener Dienst können nicht missbraucht werden.

## Wirksame Rechte prüfen

Bevor ein Agent läuft:

```bash
./console exp:access:check --user=<agent login> --node=<node id>
./console exp:access:check --user=<agent login> --node=<node id> --function=edit
./console exp:access:check --user=anonymous --node=2 --json
```

Der Befehl sagt, was ein Benutzer mit einem Knoten oder Objekt tun darf und welche Einschränkung es verweigert. Prüfen Sie außerdem
die Rollenzuweisungen des Kontos in der Administration und melden Sie sich einmal als dieses Konto an, um auszuprobieren, was es tun
soll und was nicht.

Allgemeiner Hintergrund: [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md),
[Richtlinienfunktionen und Einschränkungen](../../ersteller/anwendungsentwickler/richtlinienfunktionen-und-einschraenkungen.md).

## Regelmäßig prüfen

- EI: Summary listet jede Rolle mit einer Richtlinie `expai/*`.
- Der Audit-Trail hält Rollenänderungen (`access.role.*`) und jedes Ereignis zu Schlüsseln und Tokens fest.
- Einmal im Quartal: Agentenkonten, ihre Rollen, ihre Schlüssel und Tokens auflisten und entfernen, was nicht mehr verwendet wird.

## Verwandte Seiten

- [Zugangsdaten für Agenten](../agentische-ablaeufe/zugangsdaten-fuer-agenten.md)
- [Zugriffskontrolle im Code](../../ersteller/anwendungsentwickler/zugriffskontrolle-im-code.md)
- [Reaktion auf KI-Vorfälle](reaktion-auf-ki-vorfaelle.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
