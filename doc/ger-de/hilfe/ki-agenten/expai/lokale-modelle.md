---
title: Lokale Modelle
audience: ai-agents, admin, hosting
category: ai-agents
order: 25
summary: Ein Open-Weight-Modell mit Exponential Intelligence auf Ihrem eigenen Server betreiben, sodass keine Inhalte ihn verlassen - was die Erweiterung liefert (ein festgelegtes Ollama auf der Loopback-Adresse, systemd-Grenzen, ein root-eigener Helfer mit Erlaubnisliste), was Sie auf einer CPU erwarten können und wie die Datenrichtlinie Sektionen nur bei lokalen Modellen halten kann.
keywords: lokales Modell, Open-Weight, Ollama, On-Premises, selbst gehostet, nichts verlässt den Server, Region local, Qwen, CPU, systemd, expai-llm-helper, models.allow, expai/models, Load model, Test prompt, local model, on premises, self hosted, nothing leaves the server
type: help
---
# Lokale Modelle

Diese Seite richtet sich an Administratoren und Hosting-Personal, die möchten, dass der Assistent arbeitet, ohne Inhalte an einen
externen Anbieter zu senden. Exponential Intelligence kann ein Open-Weight-Modell verwenden, das auf demselben Server läuft, über
denselben Adapter wie ein gehosteter Anbieter. Die [Seite zu lokalen Modellen](https://github.com/se7enxweb/expai/blob/main/doc/local-models.md)
der Erweiterung enthält die vollständige Installation.

## Inhalt

1. [Was Sie erhalten](#was-sie-erhalten)
2. [Was Sie auf einer CPU erwarten können](#was-sie-auf-einer-cpu-erwarten-können)
3. [Wie es installiert wird](#wie-es-installiert-wird)
4. [Es aus der Administration verwalten](#es-aus-der-administration-verwalten)
5. [Inhalte bei lokalen Modellen halten](#inhalte-bei-lokalen-modellen-halten)
6. [Sicherheit der Einrichtung](#sicherheit-der-einrichtung)

## Was Sie erhalten

- Ein festgelegtes Release von **Ollama**, das nur auf der Loopback-Adresse lauscht (`127.0.0.1:11434`).
- systemd-Grenzen (CPU-Kontingent und -Gewicht, Nice-Level, Speichergrenzen, kein Swap), damit das Modell die Websites auf demselben
  Host nicht verdrängt. Der Dienst startet nicht beim Booten; er startet bei Bedarf, und ein Leerlauf-Timer hält ihn nach 30 Minuten
  ohne geladenes Modell an.
- Einen Modellanbieter `local` mit `Region=local` und `Retention=none`, Preis 0 und einem kompakten Prompt, der zu kleinen Modellen
  passt.
- Start, Stopp, Herunterladen, Entfernen, einen Testprompt und "Load model" unter **Setup > EI: Models**, über einen root-eigenen
  Helfer.

## Was Sie auf einer CPU erwarten können

Aus den eigenen Messungen der Erweiterung: Auf einer CPU sind nur kleine Modelle praktikabel. Ein Modell mit 4 bis 5 Milliarden
Parametern in 4-Bit-Quantisierung braucht etwa 3 bis 4 GB Festplatte und 4 bis 6 GB Speicher bei einem Kontext von 16k. Qwen3.5 4B
ist das mitgelieferte Beispiel; die Erweiterung nennt Gemma 4 E4B und Granite als Alternativen und Qwen3.5 2B als kleinere Option.

| Erwarten Sie | Auf einem ausgelasteten geteilten CPU-Host |
|---|---|
| Laden des Modells | Etwa eine Minute |
| Schreiben | Ein bis zwei Tokens je Sekunde |
| Eine einfache Frage mit dem kompakten Prompt | Etwa eine Minute |
| Ein Schritt mit Werkzeugen | Minuten |

Langsame Modelle laufen daher im Hintergrund (Vector und der Reiter Intelligence), mit eigener Zeitgrenze. Für viele Redakteure
empfiehlt die Erweiterung einen eigenen CPU-Host oder einen gehosteten Anbieter offener Modelle mit demselben Adapter.

## Wie es installiert wird

Die Installation erfolgt einmal, als root, aus den Dateien in `share/systemd/` der Erweiterung:

1. Ein festgelegtes Ollama-Release herunterladen und seine Prüfsumme prüfen.
2. Den Systembenutzer `ollama` und seine Verzeichnisse anlegen.
3. Die Dienstdatei (mit der Loopback-Adresse und den Grenzen), den Helfer, seine Pfad- und Leerlauf-Units, die Erlaubnisliste der
   Modelle (`/etc/expai-llm/models.allow`) und die Modelfiles installieren.
4. Den Benutzer des Webservers in die Gruppe `expai-llm` aufnehmen und den PHP-FPM der Website neu laden.
5. In `settings/override/expai.ini.append.php` `local` zu `[ProviderSettings] Providers[]` hinzufügen.

Die mitgelieferten Gruppen `[Provider_local]` und `[Model_local.qwen3.5-4b]` enthalten den Rest (kein Schlüssel-Header, Region
`local`, Aufbewahrung `none`, ein längeres Timeout, native Werkzeugnutzung mit ausgeschaltetem Thinking, Preise 0).

## Es aus der Administration verwalten

**Setup > EI: Models** (Richtlinie `expai/model_manage`) zeigt, ob der Server läuft, seine Version, die geladenen und installierten
Modelle, und bietet an:

| Schaltfläche | Tut |
|---|---|
| Start, Stop | Startet oder stoppt den Ollama-Dienst |
| Pull, Remove | Holt oder entfernt ein Modell von der Erlaubnisliste |
| Test prompt | Reiht einen Test ein; zeigt die Zeit bis zum ersten Token, Tokens je Sekunde, Gesamtzeit, Kosten und die Antwort |
| Load model | Lädt das Modell in den Speicher, damit der erste Redakteur nicht wartet |

Test prompt und Load model laufen als Jobs der Job-Warteschlange (der Erweiterung `expqueue`), weil das Laden länger dauert, als
eine Webanfrage laufen darf. **Laden Sie das Modell, bevor die Redakteure zu arbeiten beginnen.**

## Inhalte bei lokalen Modellen halten

Die Datenrichtlinie kann für Teile der Website eine lokale Verarbeitung verlangen:

```ini
[DataPolicy]
ClassRegions[contract]=local      # contracts never leave the server
SectionRegions[6]=eu;local        # section 6 only to EU or local providers
```

Ein Anbieter `local` erfüllt jede Regionsanforderung. Mit solchen Regeln geht eine Anfrage zu einem Vertrag nur an ein lokales
Modell, und die Auswahl bietet nur die Modelle an, die die Inhalte erhalten dürfen
([Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md)).

## Sicherheit der Einrichtung

- **Der Webprozess führt nie einen Befehl aus.** Eine Schaltfläche schreibt eine kleine Anfragedatei in ein Spool-Verzeichnis; der
  root-eigene Helfer bearbeitet jeweils eine Anfrage, nur feste Aktionen, nur Modelle auf der root-eigenen Erlaubnisliste, und
  verwirft Anfragen, die alt, groß, fehlerhaft oder keine reguläre Datei sind. Jede Aktion geht ins Systemjournal.
- **Nur Loopback.** Ollama lauscht auf `127.0.0.1`; prüfen Sie es mit `ss -ltn`.
- **Ein lokales Modell liest trotzdem Inhalte.** Seine Antworten sind Entwürfe wie alle anderen, und dieselbe Prüfung gilt.

## Verwandte Seiten

- [Anbieter und Modelle](anbieter-und-modelle.md)
- [Einen Anbieter konfigurieren](einen-anbieter-konfigurieren.md)
- [Warteschlangen mit expqueue](../../ersteller/anwendungsentwickler/warteschlangen-mit-expqueue.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
