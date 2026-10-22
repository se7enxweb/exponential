---
title: Einen Anbieter konfigurieren
audience: ai-agents, admin, developer
category: ai-agents
order: 24
summary: Eine Aufgabenanleitung für Administratoren - einen Anbieterschlüssel dort ablegen, wo er hingehört, einen OpenAI-kompatiblen Anbieter und seine Modelle hinzufügen, Region, Aufbewahrung und Datum der Bedingungen setzen, das Standardmodell der Website wählen und das Ergebnis unter Setup > EI: Models prüfen, ohne je den Schlüssel anzuzeigen.
keywords: Anbieter konfigurieren, API-Schlüssel, settings/override, expai.ini, ProviderSettings, Provider_, Model_, DefaultModel, Region, Retention, TermsCheckedAt, KeyLocations, expai/models, key present, configure provider, API key
type: help
---
# Einen Anbieter konfigurieren

Diese Seite richtet sich an Administratoren, die Exponential Intelligence mit einem KI-Anbieter verbinden. Sie geht die
Einstellungen in der Reihenfolge durch, in der Sie sie brauchen, und sagt, wohin jeder Wert gehört. Jede Einstellung ist vollständig
in der [Konfigurationsreferenz](https://github.com/se7enxweb/expai/blob/main/doc/configuration.md) der Erweiterung beschrieben.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritt 1: Der Schlüssel kommt nach settings/override](#schritt-1-der-schlüssel-kommt-nach-settingsoverride)
3. [Schritt 2: ein weiterer Anbieter und seine Modelle](#schritt-2-ein-weiterer-anbieter-und-seine-modelle)
4. [Schritt 3: Region, Aufbewahrung und Datum der Bedingungen](#schritt-3-region-aufbewahrung-und-datum-der-bedingungen)
5. [Schritt 4: der Standard der Website und wer wählen darf](#schritt-4-der-standard-der-website-und-wer-wählen-darf)
6. [Schritt 5: prüfen](#schritt-5-prüfen)
7. [Was schiefgehen kann](#was-schiefgehen-kann)

## Bevor Sie beginnen

- Die Erweiterung ist für Ihre Administrations-Siteaccesses aktiv. Eine neue Installation erledigt das für Sie; eine ältere folgt
  den Schritten im [README](../../../../../README.md#how-to-enable-it).
- Sie haben einen Schlüssel des Anbieters oder ein lokales Modell ([Lokale Modelle](lokale-modelle.md)).
- Sie haben die Bedingungen des Anbieters zu Aufbewahrung und Training gelesen und wissen, welche Inhalte dorthin gehen dürfen
  ([Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md)).

## Schritt 1: Der Schlüssel kommt nach settings/override

Schlüssel gehören nach `settings/override/expai.ini.append.php` (Modus 0640), nie in eine committete Datei:

```ini
[AISettings]
Enabled=true
ApiKey=<your Anthropic API key>
```

Ohne weitere Anbietergruppen beschreibt `[AISettings]` den Anbieter `anthropic`. Die Erweiterung liefert `ApiKey=` leer aus. Ein
Schlüssel kann auch an einem nicht versionierten Ort liegen, der in `[ProviderSettings] KeyLocations[]` genannt ist; Setup >
EI: Models warnt vor einem Schlüssel in jeder anderen Datei und vor einem Schlüssel in einer Datei, die Git verfolgt.

Der Schlüssel wird nur auf dem Server gelesen und nur an den konfigurierten Endpunkt gesendet. Er erreicht nie den Browser, ein
Template, ein Log oder die Debug-Ausgabe; eine schlüsselähnliche Zeichenkette in der Fehlermeldung eines Anbieters wird durch
`[redacted]` ersetzt.

## Schritt 2: ein weiterer Anbieter und seine Modelle

Ein OpenAI-kompatibler Anbieter, hier OpenRouter wie im eigenen Beispiel der Erweiterung:

```ini
# settings/override/expai.ini.append.php
[ProviderSettings]
Providers[]=openrouter

[Provider_openrouter]
ApiKey=<your key>
TermsCheckedAt=<YYYY-MM-DD>
```

Die mitgelieferte Gruppe `[Provider_openrouter]` hat bereits den Typ `openai_chat`, ihre Basis-URL und Beispielmodelle. Für einen
Anbieter ohne mitgelieferte Gruppe schreiben Sie die Gruppe selbst:

| Einstellung | Bedeutung |
|---|---|
| `Type` | `anthropic`, `openai_chat` oder `custom` |
| `Flavor` | Für `openai_chat`: `openai`, `huggingface`, `openrouter`, `mistral`, `xai`, `ollama`, `llamacpp`, `melious` oder `generic` |
| `Label` | Der Name in der Auswahl und in Fehlertexten |
| `BaseURL` | Die Basis der API, zum Beispiel `https://api.mistral.ai/v1` |
| `ApiKey`, `ApiKeyHeader` | Der Schlüssel und wie er gesendet wird (`authorization`, `x-api-key`, `api-key`, `x-goog-api-key` oder `none` für einen lokalen Server) |
| `Region`, `Retention`, `TermsCheckedAt` | Für die Datenrichtlinie (Schritt 3) |
| `Timeout` | Sekunden für einen Aufruf; ein lokales CPU-Modell braucht mehr |

Ein Modell ist eine Gruppe `[Model_<alias>]` mit `Provider`, `Id` (was die API erwartet), `Label`, `ToolUse` (`native`,
`prompted`, `none`), `WriteTools` und seinen Preisen je Million Tokens (`InputPerMTok`, `OutputPerMTok` und den Cache-Preisen).
Ohne Preise hat ein Modell eines anderen Anbieters unbekannte Kosten statt geschätzter.

## Schritt 3: Region, Aufbewahrung und Datum der Bedingungen

```ini
[Provider_mistral]
Region=eu            # us | eu | local
Retention=30d        # none | zdr | 30d | unknown
TermsCheckedAt=2026-10-11
```

`local` bedeutet, dass die Daten den Server nie verlassen. `zdr` bedeutet, dass mit dem Anbieter Zero Data Retention vereinbart
ist; `none`, dass er nichts behält. Setup > EI: Models zeigt "not checked", bis Sie den Tag eintragen, an dem Sie die Bedingungen
geprüft haben. Diese Werte steuern die Datenrichtlinie und werden mit jeder Anfrage festgehalten.

## Schritt 4: der Standard der Website und wer wählen darf

```ini
[ProviderSettings]
DefaultModel=<alias>
ProvidersWithoutPolicy[]
ProvidersWithoutPolicy[]=anthropic
```

- `DefaultModel` ist der Alias des Standards der Website; leer bedeutet das Anthropic-Modell aus `[AISettings] Model`.
- Modelle von Anbietern in `ProvidersWithoutPolicy[]` kann jeder mit `expai/write` wählen; die anderen brauchen `expai/model_use`,
  das sich nach Anbieter einschränken lässt ([Berechtigungen für KI-Funktionen](berechtigungen-fuer-ki-funktionen.md)).
- `[AISettings] AllowUserModelChoice` schaltet die Wahl ganz aus.

## Schritt 5: prüfen

1. Leeren Sie den INI-Cache, damit die neuen Einstellungen gelesen werden.
2. Öffnen Sie **Setup > EI: Models** (`expai/models`, Richtlinie `expai/model_manage`). Jeder Anbieter zeigt "key present" oder
   "No key", seine Region, Aufbewahrung und das Datum der Bedingungen, und jedes Modell seinen Werkzeugmodus und sein Schreibtor.
3. Senden Sie von dieser Seite den Testprompt eines Modells.
4. Führen Sie für ein Modell, das Entwürfe schreiben soll, die Probe aus ([Das Schreibtor und die Probe](das-schreibtor-und-die-probe.md)).
5. Öffnen Sie **Setup > EI: Summary** und prüfen Sie, dass sie sagt, was Sie gemeint haben.

## Was schiefgehen kann

| Meldung | Übliche Ursache |
|---|---|
| "refused the request (access)" (401/403) | Falscher Schlüssel oder ein Schlüssel, der für diesen Endpunkt nicht gilt |
| "no credit left" (402) | Das Konto beim Anbieter hat kein Guthaben |
| "does not know the configured model" (404) | Die `Id` des Modells ist für diesen Anbieter falsch |
| "busy or over its limit" (429/503/529) | Ratenlimit; der Assistent wartet ein kurzes `retry-after` ab und meldet es sonst |
| "cost unknown" im Protokoll | Das Modell hat keine Preise, und der Anbieter meldet keine |

Die [Fehlerbehebungsseite](https://github.com/se7enxweb/expai/blob/main/doc/troubleshooting.md) der Erweiterung enthält mehr.

## Verwandte Seiten

- [Anbieter und Modelle](anbieter-und-modelle.md)
- [Umgebung, Einstellungen und Geheimnisse](../../ersteller/anwendungsentwickler/umgebungseinstellungen-und-geheimnisse.md)
- [Bevor Sie Exponential Intelligence einschalten](../checklisten/bevor-sie-exponential-intelligence-einschalten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
