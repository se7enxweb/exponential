---
title: "Demo-Skript: Exponential Intelligence"
audience: press
category: press-media
order: 158
summary: Ein sechsminütiges Videoskript für Exponential Intelligence, den Schreibassistenten, der nur Entwürfe schreibt - die Einrichtung außerhalb der Kamera mit einem widerrufbaren Anbieterschlüssel, der Tab Intelligence, die Aktionen im Bearbeitungsformular, die geschriebenen Entwürfe und die Regeln für Daten und Kosten - mit dem, was Sie sagen, was Sie nie zeigen und was Sie nicht behaupten.
keywords: Demo-Skript, Exponential Intelligence, expai, KI, Schreibassistent, nur Entwürfe, Anbieterschlüssel, Tab Intelligence, Bearbeitungsformular, Datenrichtlinie, Region, Kosten, Video, Creator, demo script, AI, writing assistant, drafts only, provider key, Intelligence tab, edit form, data policy, region, cost
type: help
---
# Demo-Skript: Exponential Intelligence

Diese Seite ist für Creator, die Exponential Intelligence zeigen, den Schreibassistenten der Administration. Die eine
Botschaft, die jeden Schnitt Ihres Videos überstehen muss: **Er schreibt nur Entwürfe**, als der angemeldete Redakteur
und mit dessen Rechten, und veröffentlicht nie von sich aus (`README.md`, „Exponential Intelligence“;
[Exponential Intelligence](../../../eng-us/features/6.0/extensions/expai.md)).

Es ist die Erweiterung `se7enxweb/expai`, Release 0.0.1.0, die seit 6.0.15 in `composer.json` verlangt wird, das in
Entwicklung ist. Sagen Sie das: „die Entwicklungslinie 6.0.15“ oder die Version, die Sie wirklich verwenden. Das Skript
dauert etwa sechs Minuten; die Einrichtung geschieht **außerhalb der Kamera**.

## Inhalt

1. [Außerhalb der Kamera: die Einrichtung](#außerhalb-der-kamera-die-einrichtung)
2. [Das Skript](#das-skript)
3. [Was Sie nie zeigen](#was-sie-nie-zeigen)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Ohne Anbieterschlüssel](#ohne-anbieterschlüssel)

## Außerhalb der Kamera: die Einrichtung

Auf einer neuen Installation der Entwicklungslinie ist der Assistent laut README für die Siteaccesses der Administration
bereits aktiv, seine Tabellen existieren und die Rolle Administrator hat seine Richtlinien; er "sends nothing anywhere
until you add a provider key". Auf einer älteren Installation folgen Sie den Upgrade-Schritten in der README oder
[Einschalten](../../../eng-us/features/6.0/extensions/expai.md#switch-it-on).

```text
[ ] Ein Anbieterschlüssel mit niedrigem Ausgabelimit, nur für diese Aufnahme erstellt, den Sie danach widerrufen können
[ ] Der Schlüssel steht in settings/override/expai.ini.append.php ([AISettings] Enabled=true, ApiKey=...), nie in einer
    committeten Datei, und diese Datei wird nie auf dem Bildschirm geöffnet
[ ] Die Cron-Teile expqueue und expai laufen, wenn Sie Arbeit in der Warteschlange zeigen (Ketten, Alt-Text-Berichte)
[ ] Der Demo-Redakteur hat expai/write und ezjscore/call, oder Sie nehmen als Administrator auf
[ ] Ein Ordner mit erfundenen Inhalten, unter dem geschrieben wird
[ ] Sie haben den genauen Prompt vorher ausprobiert und wissen ungefähr, was er kostet
```

Die Datenrichtlinie entscheidet, wohin Inhalte gehen dürfen: Jeder Anbieter nennt seine Region (`us`, `eu` oder `local`)
und seine Aufbewahrung, und jede Anfrage hält fest, wohin sie ging (`README.md`, „Safety“).

## Das Skript

| Zeit | Klick | Sagen Sie |
|---|---|---|
| 0:00 | Titelkarte | Zeile zur Offenlegung. „Das ist Exponential Intelligence, der Schreibassistent von Exponential, auf meiner eigenen Testinstallation, <Version>.“ |
| 0:15 | Einen Ordner öffnen; den Tab **Intelligence** wählen | „Ein Gespräch pro Benutzer und pro Objekt. Ich frage in normaler Sprache.“ |
| 0:30 | Eine kurze, harmlose Bitte tippen, zum Beispiel ein kurzer Artikel über eine erfundene Veranstaltung unter diesem Ordner | „Er schlägt nach, was er braucht, etwa den Inhaltstyp und den Baum darum herum, und schreibt dann.“ |
| 1:00 | Die Liste der Schritte zeigen | „Jedes Nachschlagen und jeder Entwurf ist ein Schritt, den ich verfolgen kann.“ |
| 1:45 | Auf die geschriebenen Entwürfe zeigen | „Das sind Entwürfe. Nichts ist veröffentlicht.“ |
| 2:10 | Den Entwurf öffnen, dann zurückgehen | „Er hat als ich geschrieben, mit meinen Rechten. **„Save as drafts“** behält sie; **„Send for approval“** führt den normalen Veröffentlichungsvorgang als ich aus, sodass ein Freigabe-Workflow die Version weiterhin zurückhält.“ |
| 3:00 | Das Bearbeitungsformular eines Objekts öffnen | „Im Formular bietet der Assistent Aktionen pro Feld: schreiben, umschreiben, kürzen, übersetzen, vorschlagen, und ein Menü **„Improve“**.“ |
| 3:30 | **„Rewrite“** oder **„Shorten“** auf ein Feld anwenden | „Der Wert kommt zur Prüfung ins Formular. Nichts ändert sich, bis ich ihn annehme und den Entwurf speichere.“ |
| 4:10 | Auf die Zeile mit Modell, Region und Kosten zeigen | „Jeder Klick zeigt das Modell, die Region und was er kostet.“ |
| 4:40 | Zurück zum Tab **Intelligence**; den Entwurf verwerfen | „Ich werfe den Entwurf weg; die Website ist unverändert.“ |
| 5:10 | Titelkarte | „Jede Anfrage wird im Audit-Trail festgehalten, ohne Prompts, Antworten oder Schlüssel. Die Dokumentation ist unten verlinkt.“ |

## Was Sie nie zeigen

- Den Anbieterschlüssel, die Datei `settings/override/expai.ini.append.php`, das eigene Dashboard oder die
  Abrechnungsseite des Anbieters.
- Inhalte, die Sie nicht an einen externen Dienst schicken dürfen: Verwenden Sie nur erfundenen Text.
- Namen echter Menschen in Prompts. Prompts können an den gewählten Anbieter gesendet werden.

Widerrufen Sie nach der Aufnahme den Schlüssel beim Anbieter, auch wenn Sie glauben, er sei nie zu sehen gewesen
([Geheimnisse vom Bildschirm fernhalten](geheimnisse-vom-bildschirm-fernhalten.md)).

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „Die KI veröffentlicht für Sie.“ | „Er schreibt nur Entwürfe.“ |
| „Ihre Daten verlassen nie den Server.“ | „Mit einem lokalen Modell über Ollama verlassen die Daten nie den Server; mit einem gehosteten Anbieter gehen sie in dessen Region.“ |
| „Es ist kostenlos.“ | „Die Erweiterung ist freie Software; ein gehosteter Anbieter berechnet die Nutzung, und der Assistent zeigt die geschätzten Kosten.“ |
| „Es funktioniert mit jedem KI-Dienst.“ | „Anthropic, jeder OpenAI-kompatible Dienst oder ein lokales Modell über Ollama; nur Modelle, die einen Live-Test bestanden haben, dürfen Entwürfe schreiben.“ |
| „Es ist konform mit <Gesetz>.“ | „Es gibt eine druckbare Seite EI: Summary für einen Datenschutzbeauftragten.“ Rechtliche Schlüsse überlassen Sie diesem |
| „Es ist in 6.0.14.“ | Es wird ab 6.0.15 verlangt, das in Entwicklung ist; es braucht Exponential 6.0.14 oder später |

## Ohne Anbieterschlüssel

Zeigen Sie stattdessen die Dokumentationsseite und sagen Sie: „Er sendet nichts irgendwohin, bis ein Anbieterschlüssel
hinzugefügt ist.“ Das ist ein vollständiger, ehrlicher Abschnitt ([Demo-Drehbücher, Teil 6](../geschaeft/demo-drehbuecher.md#teil-6-exponential-intelligence-4-minuten-optional)).

```text
BESCHREIBUNGSBLOCK
Zeigt Exponential Intelligence (expai <die installierte Version>) in Exponential <Version>, auf meiner eigenen
Testinstallation. Es schreibt nur Entwürfe und veröffentlicht nie von sich aus.
In diesem Video verwendeter Anbieter: <Anbieter, Region>. Die Kosten dieser Aufnahme hat <ich | wer> bezahlt.
Funktionsseite: <Link zu „Exponential Intelligence“> - Repository: https://github.com/se7enxweb/expai
```

## Verwandte Seiten

- [Gesprächspunkte: Exponential Intelligence](../fuersprecher/gespraechspunkte-exponential-intelligence.md)
- [Einwand: „Was ist mit den KI-Funktionen?“](../fuersprecher/einwand-ki-funktionen.md)
- [Geheimnisse vom Bildschirm fernhalten](geheimnisse-vom-bildschirm-fernhalten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
