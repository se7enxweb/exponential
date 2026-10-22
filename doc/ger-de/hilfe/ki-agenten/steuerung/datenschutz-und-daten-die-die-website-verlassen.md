---
title: Datenschutz und Daten, die die Website verlassen
audience: ai-agents, admin, businesses, government
category: ai-agents
order: 81
summary: Welche Inhalte eine Exponential-Installation verlassen, wenn KI verwendet wird, und wie Sie das steuern - was Exponential Intelligence sendet und nie sendet, Region und Aufbewahrung des Anbieters, Regeln der Datenrichtlinie je Sektion und Klasse, Bilder und Webwerkzeuge, was die Installation selbst behält und wie lange, die Seite EI: Summary für einen Datenschutzbeauftragten und dieselben Fragen für Agenten, die Inhalte über die REST-API oder expservices lesen.
keywords: Datenschutz, DSGVO, Daten, die die Website verlassen, Datenrichtlinie, Region, Aufbewahrung, Zero Data Retention, SectionRegions, ClassRegions, ZeroRetention, EI: Summary, Datenschutzbeauftragter, Aufbewahrungseinstellungen, Transkripte, personenbezogene Daten, privacy, data protection, GDPR, data leaving the site, data policy, retention, data protection officer, transcripts, personal data
type: help
---
# Datenschutz und Daten, die die Website verlassen

Diese Seite richtet sich an Datenschutzbeauftragte, Administratoren und Führungskräfte, die beantworten müssen "Wohin gehen unsere
Inhalte, wenn wir KI verwenden?". Sie erklärt, was gesendet wird, wie Sie es begrenzen, was behalten wird und wie Sie es
dokumentieren. Sie ist keine Rechtsberatung; ob die Bedingungen eines Anbieters Ihre Pflichten erfüllen, entscheidet Ihre
Organisation.

## Inhalt

1. [Nichts verlässt die Website, bis Sie es konfigurieren](#nichts-verlässt-die-website-bis-sie-es-konfigurieren)
2. [Was der Assistent sendet](#was-der-assistent-sendet)
3. [Region und Aufbewahrung jedes Anbieters](#region-und-aufbewahrung-jedes-anbieters)
4. [Regeln der Datenrichtlinie](#regeln-der-datenrichtlinie)
5. [Bilder und Webwerkzeuge](#bilder-und-webwerkzeuge)
6. [Was die Installation behält](#was-die-installation-behält)
7. [Die Seite EI: Summary](#die-seite-ei-summary)
8. [Agenten, die Inhalte lesen](#agenten-die-inhalte-lesen)

## Nichts verlässt die Website, bis Sie es konfigurieren

Exponential Intelligence sendet nirgendwohin etwas, bis ein Administrator einen Anbieterschlüssel oder ein lokales Modell
hinzufügt. Die REST-API und expservices rufen überhaupt keinen Anbieter auf; sie antworten dem Agenten, den Sie verbinden, und der
Anbieter dieses Agenten erhält, was der Agent liest.

## Was der Assistent sendet

| An den Anbieter des Modells gesendet | Nie gesendet |
|---|---|
| Die Anweisungen, mit Name, Klasse und Pfad des geöffneten Knotens | Name, Login oder E-Mail des Redakteurs |
| Die Nachrichten und Anweisungen des Redakteurs | Passwörter und Session-Daten |
| Namen und Feldwerte von Inhalten, die der Redakteur lesen darf, so wie die Werkzeuge sie zurückgeben | Felder und Dateien von Benutzerkonten |
| Klassendefinitionen | Den Inhalt der Entwürfe anderer Benutzer |
| Im Bearbeitungsformular: die Felder des Objekts, wie sie im Formular stehen (höchstens etwa 30 KB) | Den Anbieterschlüssel (er geht nur im Request-Header an den Endpunkt) |
| Für Alternativtext: das Bild dieses Feldes, verkleinert, in der Anfrage | |

Quelle: die [Sicherheitsseite](https://github.com/se7enxweb/expai/blob/main/doc/security.md) der Erweiterung.

## Region und Aufbewahrung jedes Anbieters

Jeder Anbieter erklärt, wo er Anfragen verarbeitet und wie lange er sie behält:

| Einstellung | Werte | Bedeutung |
|---|---|---|
| `Region` | `us`, `eu`, `local` | `local`: Die Daten verlassen den Server nie |
| `Retention` | `none`, `zdr`, `30d`, `unknown` | `zdr`: Zero Data Retention vereinbart; `none`: behält nichts (lokal) |
| `TermsCheckedAt` | ein Datum | Der Tag, an dem jemand die Bedingungen des Anbieters gelesen hat; "not checked", bis es gesetzt ist |

Diese Werte sind **Erklärungen, die Sie abgeben**, nachdem Sie die Bedingungen des Anbieters und Ihren Vertrag gelesen haben. Die
für Beispielanbieter mitgelieferten Werte sind ein Ausgangspunkt, keine rechtliche Aussage. Sie werden mit jeder Anfrage
festgehalten.

## Regeln der Datenrichtlinie

```ini
[DataPolicy]
DefaultRegions[]                  # empty: every region
SectionRegions[6]=eu;local        # section 6 only to EU or local providers
ClassRegions[contract]=local      # contracts never leave the server
ZeroRetention=disabled
ZeroRetentionSections[]=7         # section 7 only to providers that keep nothing
ZeroRetentionClasses[]
```

Die Anforderung eines Inhaltsobjekts ist die Schnittmenge aus den Standardregionen und den Regeln seiner Sektion und seiner
Klasse. Ein Anbieter mit unbekannter Region erfüllt keine Regionsanforderung. Ohne jede Regel darf alles an jeden aktiven
Anbieter gehen ([Dokumentation der Erweiterung: Datenrichtlinie](https://github.com/se7enxweb/expai/blob/main/doc/data-policy.md)).

Wo sie durchgesetzt wird: vor der ersten Anfrage im Panel; bei jedem Werkzeug (abgelehnte Objekte sind Werkzeugfehler, Listen
lassen sie aus und zählen sie als zurückgehalten); im Bearbeitungsformular; für Alternativtext; für Antworten von
Dienstwerkzeugen; und erneut, wenn ein Gespräch zu einem anderen Modell wechselt. Darf der Anbieter eines Modells die Inhalte nicht
erhalten, wird dem Redakteur das gesagt, und er kann ein anderes Modell wählen.

Für Inhalte, die nie irgendwohin gesendet werden dürfen, können die [Veto-Regeln](../expai/hauptschalter-und-veto.md#die-veto-regeln)
Anfragen nach Sektion, Klasse oder Teilbaum rundweg ablehnen.

## Bilder und Webwerkzeuge

- **Bilder** folgen den Regeln für Sektion und Klasse ihres Objekts und können strengeren Regeln unterworfen werden
  (`[AltTextSettings] ImageRegions[]`, `ImageZeroRetention`). Das Anfrageprotokoll behält die Pixel nie.
- **web_fetch** sendet nichts außer der URL an eine Website auf der Erlaubnisliste. **web_search** sendet seine Abfrage an einen
  Suchdienst, dessen Region und Aufbewahrung gegen die Richtlinie des Knotens geprüft werden
  ([Web- und Verlaufswerkzeuge](../expai/web-und-verlaufswerkzeuge.md)).

## Was die Installation behält

| Was | Wie lange |
|---|---|
| Das Nutzungsprotokoll: Benutzer-ID (keine Namen oder E-Mail), Knoten, Anbieter, Modell, Tokens, Kosten, Werkzeuge, Fehler; nicht die Texte | `[UsageSettings] RetentionDays` |
| Vollständige Anfragetexte | Nur mit `[UsageSettings] LogRequests=enabled`, einer Debug-Einstellung, die in der Produktion aus bleibt |
| Die kurze Anfragezusammenfassung im Entwurfsprotokoll | Nach `[RetentionSettings] PromptSummaryKeepDays` (365) vom Cron-Teil `expai` geleert |
| Gespräche (Nachrichten, Antworten, Werkzeugergebnisse) je Benutzer und Knoten | Bis "New conversation" oder nach `ConversationKeepDays` (365) ohne Änderung; Threads nach `ThreadKeepDays` |
| Der Audit-Kanal `ai`: Aktionen und Metadaten, nie ein Prompt, eine Antwort oder ein Schlüssel | Nach den Einstellungen des Kanals (standardmäßig 90 Tage live, 730 archiviert) |

Gespräche eines Benutzers, der nicht mehr existiert, entfernt der nächste Cron-Lauf mit `[RetentionSettings] Mode=delete`.

## Die Seite EI: Summary

**Setup > EI: Summary** (`expai/summary`) ist eine druckbare Seite für einen Datenschutzbeauftragten: jeder Anbieter mit Host,
Region, Aufbewahrung, dem Datum, an dem die Bedingungen geprüft wurden, und **woher der Schlüssel kommt** (nie der Schlüssel), wer
ihn verwenden darf, die Nutzung dieses Monats; die Lanes und ihre Aufbewahrung; die Datenrichtlinie; die Grenzen und das Budget;
die Werkzeuge, die über die Website hinausreichen; was die Website behält; und jede Rolle mit einer expai-Richtlinie. Zwei freie
Texte (`[SummarySettings] Contact` und `Note`) können nennen, wer Fragen beantwortet, und Ihr Verarbeitungsverzeichnis. Sie wird
jedes Mal aus den laufenden Einstellungen gelesen und veraltet daher nie. "Print or save as PDF" erzeugt eine Kopie für Ihre
Unterlagen.

Die Richtlinie `expai/summary` allein öffnet sie, ohne jeden Zugriff auf Inhalte.

## Agenten, die Inhalte lesen

Bei einem Agenten, der über die REST-API oder expservices liest, erhält der eigene Anbieter des Agenten alles, was der Agent liest.
Die Datenrichtlinie von Exponential Intelligence gilt dort nicht; die Berechtigungen des Kontos sind der einzige Filter. Deshalb:

- Geben Sie dem Konto des Agenten Lesezugriff nur auf Inhalte, die an diesen Anbieter gehen dürfen;
- lassen Sie personenbezogene Daten (Benutzerkonten, Formulareinsendungen, Bestellungen) aus seiner Rolle heraus, sofern die
  Aufgabe sie nicht braucht;
- behandeln Sie die Transkripte der Agentenplattform als Daten, die Ihre Organisation hält;
- verwenden Sie für Entwicklungsarbeit eine Entwicklungskopie ohne echte personenbezogene Daten
  ([Zugangsdaten für Agenten](../agentische-ablaeufe/zugangsdaten-fuer-agenten.md)).

## Verwandte Seiten

- [Personenbezogene Daten in Exponential](../../unternehmen/sicherheit/personenbezogene-daten-in-exponential.md)
- [DSGVO-Grundlagen](../../unternehmen/sicherheit/dsgvo-grundlagen.md)
- [Lokale Modelle](../expai/lokale-modelle.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
