---
title: Integrationen zurückgeben
audience: integrator, developer, partner
category: integration
order: 420
summary: Was Integratoren an Exponential zurückgeben können und warum es sich lohnt - beim Integrieren gefundene Korrekturen, allgemeine Konnektoren als öffentliche Erweiterungen, Dokumentation eines Integrationsmusters - wie Sie allgemeinen Code von Kundencode trennen, Punkte zur Lizenzierung und der Weg von einem privaten Repository zu einem Pull Request oder einer veröffentlichten Erweiterung.
keywords: mitwirken, zurückgeben, Open Source, Pull Request, Konnektor, öffentliche Erweiterung, GPL, Lizenz, Kundencode, allgemeiner Code, Dokumentation, Community, Packagist, Partnerpunkte, contribute, contributing back, connector, public extension, license, customer code, partner points
type: help
---
# Integrationen zurückgeben

Diese Seite ist für Integratoren und Integrationsunternehmen. Integrationsprojekte finden Fehler an den Rändern eines
Systems, bauen Konnektoren, die auch andere Kunden brauchen, und lernen Muster, die es wert sind, aufgeschrieben zu
werden. Das zurückzugeben macht das nächste Projekt für Sie und für alle günstiger, und so wächst Exponential. Die
allgemeine Anleitung ist [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md).

## Inhalt

1. [Was Sie zurückgeben können](#was-sie-zurückgeben-können)
2. [Allgemeinen Code von Kundencode trennen](#allgemeinen-code-von-kundencode-trennen)
3. [Punkte zur Lizenzierung](#punkte-zur-lizenzierung)
4. [Vom privaten zum öffentlichen Repository](#vom-privaten-zum-öffentlichen-repository)
5. [Was es Ihnen bringt](#was-es-ihnen-bringt)

## Was Sie zurückgeben können

| Beitrag | Beispiel | Wohin er geht |
|---|---|---|
| Eine beim Integrieren gefundene Korrektur am Kernel oder an einer Erweiterung | Das `fromString()` eines Datentyps speicherte einen ungültigen Wert; eine REST-Route antwortete mit dem falschen Status | Ein Pull Request an das Repository auf <https://github.com/se7enxweb> ([Mit Pull Requests zurückgeben](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md)) |
| Ein Fehlerbericht mit Reproduktion | Eine Einstellung, die nicht tut, was ihr Kommentar sagt | Ein Issue im Repository ([Gute Issues schreiben](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/gute-issues-schreiben.md)) |
| Ein allgemeiner Konnektor | Ein Mail-Transport für die API eines Anbieters, ein Login-Handler für einen Identitätsanbieter, ein Plugin für eine Suchmaschine | Eine eigene öffentliche Erweiterung |
| Ein Erweiterungspunkt | Ein Filter oder Ereignis, das der Kernel melden sollte, damit Integrationen keinen Override brauchen | Ein Pull Request mit dem Ereignis und seiner Dokumentation |
| Dokumentation | Ein Muster, eine Falle, ein Rezept | Ein Pull Request an die Dokumentation ([Dokumentation und Übersetzungen beitragen](../partner/dokumentation-und-uebersetzungen-beitragen.md)) |
| Eine Änderung an einem Verhalten, das gewollt, für Ihre Integration aber unpraktisch ist | Ein Standardwert, der zu den meisten Websites passt, aber nicht zu Ihrer | Zuerst ein Issue; es kann eine [negative Funktion](../projekt/unterstuetzung-erhalten.md#negative-funktionen) sein, auf die andere angewiesen sind, also begründen Sie es klar, rechnen Sie mit mehreren Prüfrunden und schlagen Sie eine Option vor |

## Allgemeinen Code von Kundencode trennen

Entwerfen Sie die Integration von Anfang an als zwei Erweiterungen
([Eine Integration als Erweiterung bauen](eine-integration-als-erweiterung-bauen.md#eine-erweiterung-oder-mehrere)):

- **Der Konnektor**: der Client der API des anderen Systems, seine Fehlerbehandlung, der Mail-Transport oder
  Login-Handler. Über Einstellungen konfiguriert, ohne Kundennamen, Knoten-IDs, URLs oder Zugangsdaten im Code.
- **Die Kundenerweiterung**: die Zuordnung, die Klassen, der Zeitplan, die Einstellungen des Kunden.

Prüfen Sie den Konnektor vor dem Veröffentlichen:

- [ ] Keine Kundennamen, Domains, Knoten-IDs, internen URLs oder Testdaten des Kunden.
- [ ] Keine Zugangsdaten in Code, Einstellungsdateien, Tests oder der Git-Historie (durchsuchen Sie die Historie,
  nicht nur den letzten Commit).
- [ ] Einstellungen dokumentiert, mit sicheren Standardwerten.
- [ ] Tests, die ohne das andere System laufen.
- [ ] Eine README mit Zweck, Anforderungen, Installation und einer Beispielkonfiguration.

## Punkte zur Lizenzierung

Der Exponential-Kernel steht unter der GNU General Public License v2 (oder einer späteren Version). Eine Erweiterung,
die innerhalb von Exponential läuft und an andere verbreitet wird, muss diese Lizenz beachten; die Wahl von GPL v2 oder
später für eine öffentliche Erweiterung hält sie kompatibel. Eine Erweiterung nur selbst zu nutzen oder für einen
Kunden, der sie als Teil eines Projekts erhält, bringt andere Pflichten mit sich als ihre Veröffentlichung. Lesen Sie
[Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md#was-die-gpl-verlangt-und-wann) und
[Lizenzierung für Partner](../partner/lizenzierung-fuer-partner.md), und vereinbaren Sie im Vertrag, wem was gehört
([Leistungsbeschreibungen](leistungsbeschreibungen-fuer-integrationen.md)). Das ist keine Rechtsberatung.

## Vom privaten zum öffentlichen Repository

1. Vereinbaren Sie mit dem Kunden schriftlich, dass der allgemeine Konnektor veröffentlicht werden darf.
2. Verschieben Sie den Konnektor in ein eigenes Repository mit sauberer Historie (oder beginnen Sie ein neues aus dem
   bereinigten Code).
3. Fügen Sie `LICENSE`, `README.md`, `CHANGELOG.md`, `composer.json`, `ezinfo.php` und `extension.xml` mit derselben
   Version hinzu ([Veröffentlichen](../erweiterungsentwicklung/teil-7-veroeffentlichen/README.md)).
4. Veröffentlichen Sie eine erste Version; Tags sind dauerhaft, prüfen Sie also vor dem Taggen
   ([Tags sind dauerhaft](../erweiterungsentwicklung/teil-7-veroeffentlichen/tags-sind-dauerhaft.md)).
5. Registrieren Sie das Paket bei Packagist und kündigen Sie es an
   ([Composer und Packagist](../erweiterungsentwicklung/teil-7-veroeffentlichen/composer-und-packagist.md)).
6. Lassen Sie die Kundenerweiterung den veröffentlichten Konnektor verlangen.

## Was es Ihnen bringt

- Korrekturen im Hauptprojekt bedeuten keine privaten Patches, die Sie durch jedes Upgrade tragen müssen.
- Ein öffentlicher Konnektor wird von anderen geprüft und verbessert und zeigt Interessenten die Kompetenz Ihres
  Unternehmens.
- Für Exponential-Partner zählt Community-Arbeit zu den Punkten des Partnerprogramms
  ([Punkte durch Community-Arbeit sammeln](../partner/punkte-durch-community-arbeit.md)).

## Verwandte Seiten

- [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md)
- [Erweiterungen als Partner zurückgeben](../partner/erweiterungen-zurueckgeben.md)
- [Code zurückgeben](../partner/code-zurueckgeben.md)
- [Teil 5 des Erweiterungsbuchs: erweitern, forken, mitwirken](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
