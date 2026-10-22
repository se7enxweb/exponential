---
title: Agenten und expservices
audience: ai-agents, developer, app-creator
category: ai-agents
order: 63
summary: expservices aus einem KI-Agenten verwenden - benannte Dienste der Administration über die ezjscore-Aufrufschnittstelle, eine Antwortform, ein persönliches Token statt einer Session, der Katalog mit Zugriffsregeln und Schreibmerkmalen als Quelle einer Werkzeugliste und dieselben Deklarationen als Werkzeuge des Assistenten wiederverwendet.
keywords: expservices, Agent, ezjscore call, Dienstkatalog, expsession tokenCreate, expt_-Token, Bearer, X-Exp-Token, Zugriffsregel, Schreibmerkmal, Werkzeugliste, Shell-Client, Python-Client, Dienstwerkzeuge, agent, services catalog, access rule, write flag, tool list, shell client, Python client, service tools
type: help
---
# Agenten und expservices

Diese Seite richtet sich an Entwickler, die möchten, dass ein Agent Funktionen der Administration über das Lesen von Inhalten
hinaus verwendet. Die Erweiterung expservices, Teil der Standarddistribution, macht aus diesen Funktionen über tausend benannte
Dienste. Ihre [Anleitung](../../../anleitungen/entfernte-dienste-und-apps.md) führt durch ihren Aufruf; diese Seite ergänzt, was für einen
Agenten wichtig ist.

## Inhalt

1. [Wie ein Dienstaufruf aussieht](#wie-ein-dienstaufruf-aussieht)
2. [Ein Token für den Agenten](#ein-token-für-den-agenten)
3. [Der Katalog als Werkzeugliste](#der-katalog-als-werkzeugliste)
4. [Regeln, denen jeder Dienst folgt](#regeln-denen-jeder-dienst-folgt)
5. [Eine Deklaration, zwei Kanäle](#eine-deklaration-zwei-kanäle)

## Wie ein Dienstaufruf aussieht

Ein Dienst wird beim Namen, `exp<domain>::<method>`, über die ezjscore-Aufrufschnittstelle aufgerufen und antwortet mit JSON:

```bash
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

Innerhalb der ezjscore-Hülle hat jede Antwort eine Form: `{"ok":true,"data":...,"meta":{...}}` oder
`{"ok":false,"error":{"code":403,"message":"..."}}`. Listen werden seitenweise geliefert, und `meta` enthält `total`, `offset`,
`limit`, `count` und `has_more`. Eine einzige Form macht es dem Code eines Agenten leicht, Erfolg von Ablehnung zu unterscheiden.

## Ein Token für den Agenten

Ein Session-Cookie läuft ab und braucht für jeden Schreibzugriff ein Formular-Token. Verwenden Sie für einen Agenten ein
**persönliches Token**:

1. Melden Sie sich als das eigene Konto des Agenten an (nie als das einer Person).
2. Erstellen Sie ein Token mit dem Dienst `expsession::tokenCreate` (ein Name und eine Laufzeit in Tagen). Es wird einmal angezeigt,
   `expt_` gefolgt von 48 Hexadezimalzeichen; nur sein Hash wird gespeichert.
3. Der Agent sendet es als `Authorization: Bearer <token>` oder `X-Exp-Token: <token>`, wo ein Proxy den ersten Header verwirft.
   Schreibzugriffe brauchen weiterhin POST, aber kein Formular-Token.
4. Listen und widerrufen Sie Tokens mit `expsession::tokenList` und `expsession::tokenRevoke`.

Ein unbekanntes, widerrufenes oder abgelaufenes Token oder eines eines deaktivierten Benutzers antwortet bei jedem Dienst mit 401,
sodass der Agent bemerkt, dass sein Token erloschen ist. Ein Token kann keine Tokens anlegen. Anlegen, Widerruf und Ablehnungen
werden im Audit-Trail festgehalten (`access.expservices.token.*`).

## Der Katalog als Werkzeugliste

Der Katalog listet jeden Dienst mit seiner Zusammenfassung, Zugriffsregel, seinen Argumenten und einem Beispielaufruf:

```bash
cd extension/expservices/bin
./expservices-client.sh catalog node | grep -E '"method"|"summary"'
```

Jeder Eintrag nennt `access` (`public`, `user` oder eine Richtlinie `[module, function]`) und `write` (true für Dienste, die etwas
ändern). Für einen Agenten:

- Bauen Sie die Werkzeugliste des Modells aus den Katalogeinträgen, die Sie auswählen, **nicht** aus allen.
- Lassen Sie jeden Eintrag mit `write: true` weg, bis sich der Leseablauf bewährt hat.
- Geben Sie dem Konto nur die Richtlinien, die die gewählten Dienste brauchen.

Setup > RAD (`/setup/rad`) listet denselben Katalog in der Administration.

## Regeln, denen jeder Dienst folgt

Aus der Anleitung: 401 ohne Anmeldung, 403 ohne die Richtlinie; ein Schreibzugriff braucht POST (und mit einer Session ein
Formular-Token) und hält `service.<domain>.<method>` im Audit-Trail fest; große Operationen werden an Inhaltsjobs weitergeleitet.
Eine scheiternde Richtlinie antwortet mit `error 403`, ein fehlerhaftes Argument mit `error 422` oder `error 400` und dem Grund.

## Eine Deklaration, zwei Kanäle

Ein Dienst wird einmal in seiner Domänenklasse deklariert. Diese Deklaration macht ihn zu:

| Kanal | Name | Wer ihn aufruft |
|---|---|---|
| Einem HTTP-Endpunkt | `ezjscore/call/exp<domain>::<method>` | Menschen, Apps, Agenten mit einem Token |
| Einem Werkzeug von Exponential Intelligence (nur wenn sich der Dienst mit `'ai' => true` dafür entscheidet und ein Lesedienst ist) | ein aus Domäne und Methode abgeleiteter Werkzeugname | Der Assistent, als der Redakteur, mit `expai/services` ([Dienstwerkzeuge](../expai/dienstwerkzeuge.md)) |

Jeder Kanal läuft mit den Berechtigungen seines Aufrufers. Siehe
[Entfernte Dienste mit expservices](../../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md), um eine eigene Dienstdomäne zu
schreiben.

## Verwandte Seiten

- [Eine Schnittstelle für einen Agenten wählen](eine-schnittstelle-waehlen.md)
- [Zugangsdaten für Agenten](zugangsdaten-fuer-agenten.md)
- [Backend-Dienste über ezjscore](../../../bc/6.0/backend-dienste-ueber-ezjscore.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
