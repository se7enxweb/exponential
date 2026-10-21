---
title: Anforderungen an Open Source und Lizenzierung
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 130
summary: Formulierungen für die Open-Source-Lizenzierung in einer Ausschreibung für ein CMS - Lizenz der Software, Verfügbarkeit des Quellcodes, Rechte an eigens entwickelter Arbeit, Komponenten Dritter und Lizenz der Dokumentation - mit den Fakten zur Lizenzierung von Exponential unter GPL und GFDL.
keywords: Open Source, Lizenz, GPL, GPL-2.0-or-later, GFDL, Quellcode, eigens entwickelte Arbeit, geistiges Eigentum, Komponenten Dritter, Open Source Definition, Public Money Public Code, Ausschreibung, Anforderungen, open source, license, source code, custom work, intellectual property, third-party components, public money public code, tender, requirements
type: help
---
# Anforderungen an Open Source und Lizenzierung

Diese Seite richtet sich an Beschaffungsteams und Rechtsberatung, die Open-Source-Software wollen und das prüfbar in
eine Ausschreibung schreiben müssen. Sie gibt Formulierungen für Anforderungen und die Fakten zur Lizenzierung von
Exponential, aus [Fragen zur Lizenzierung: die GPL und Exponential](../../../projekt/lizenz-faq.md) und
[Die Lizenz dieser Dokumentation](../../../../../eng-us/README-LICENSE.md).

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Warum es für eine öffentliche Stelle zählt](#warum-es-für-eine-öffentliche-stelle-zählt)
2. [Formulierung der Anforderungen](#formulierung-der-anforderungen)
3. [Fakten zur Lizenzierung von Exponential](#fakten-zur-lizenzierung-von-exponential)
4. [Eigens entwickelte Arbeit unter der GPL](#eigens-entwickelte-arbeit-unter-der-gpl)
5. [Fallstricke](#fallstricke)

## Warum es für eine öffentliche Stelle zählt

- **Keine Lizenzgebühr** für die Software selbst, die Kosten verlagern sich also auf Leistungen: Bau, Hosting, Support,
  Schulung.
- **Freiheit, den Lieferanten zu wechseln**: Jeder darf den Code ausführen, untersuchen, ändern und weitergeben.
- **Einsicht**: Prüfende Stellen und Sicherheitsteams können den Quellcode lesen.
- **Wiederverwendung**: Was eine Stelle bauen lässt und bezahlt, können andere nutzen, wenn es veröffentlicht wird.

Die [Open Source Definition](https://opensource.org/osd) führt die Kriterien auf, die eine Open-Source-Lizenz erfüllt;
verlangen Sie eine Lizenz, die sie erfüllt, statt „Open Source“ als Etikett.

## Formulierung der Anforderungen

| Id | Anforderung |
|---|---|
| OSS-01 | Die Kernsoftware steht unter einer Lizenz, die die Open Source Definition erfüllt; nennen Sie die Lizenz. |
| OSS-02 | Der vollständige Quellcode des gelieferten Systems, einschließlich Erweiterungen, Designs und Integrationscode, wird dem Kunden zu jedem Release und am Vertragsende in einem Versionsverwaltungs-Repository geliefert. |
| OSS-03 | Führen Sie jede Komponente Dritter mit ihrer Lizenz auf; keine Komponente darf die Rechte des Kunden nach OSS-01 einschränken. |
| OSS-04 | Unter diesem Vertrag bezahlte eigens entwickelte Arbeit wird dem Kunden unter <Lizenz> lizenziert; der Kunde darf sie veröffentlichen. |
| OSS-05 | Der Kunde darf das System ohne Zustimmung des Lieferanten von jeder anderen Partei pflegen lassen. |
| OSS-06 | Gelieferte Dokumentation steht unter einer Lizenz, die dem Kunden erlaubt, sie zu kopieren und anzupassen. |
| OSS-07 | Kein Teil des gelieferten Systems hängt von einer proprietären Komponente oder einem Dienst ab, den der Kunde nicht ersetzen kann; führen Sie alle auf, die es gibt. |

## Fakten zur Lizenzierung von Exponential

| Thema | Fakt | Quelle |
|---|---|---|
| Lizenz der Software | GNU GPL Version 2 oder (nach Ihrer Wahl) jede spätere Version; `"license": "GPL-2.0-or-later"` in `composer.json` | [Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md), [GPL v2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) |
| Lizenzgebühr | keine; Sie dürfen für Kopien, Installation, Support und Updates Geld verlangen | [Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md) |
| Nutzung über ein Netzwerk | Der Besuch einer Website ist keine Weitergabe; nichts verpflichtet den Betreiber, den Code zu veröffentlichen, den er betreibt | [Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md) |
| Quellcode | Öffentliche Repositorys auf GitHub unter `se7enxweb`, installierbar mit Composer | [Den Code holen](../../../../installation/03-den-code-beschaffen.md) |
| Dokumentation | GNU Free Documentation License 1.3 oder neuer | [Die Lizenz dieser Dokumentation](../../../../../eng-us/README-LICENSE.md), [GFDL 1.3](https://www.gnu.org/licenses/fdl-1.3.html) |
| Urheberrecht | „7x & Exponential Foundation“ und eZ Systems AS für 1999 bis 2014, dazu Mitwirkende; Mitwirkende am Kernel übertragen das Urheberrecht an ihren Beiträgen unter dem CLA an 7x ([CONTRIBUTING.md](../../../../../../CONTRIBUTING.md#copyright-assignment-cla)) | [Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md) |
| Lizenzen der Erweiterungen | Jede Erweiterung gibt ihre Lizenz in `ezinfo.php` und `extension.xml` an | [Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md), [Lizenzen und Versionen der Pakete](../../../../../eng-us/features/6.0/package-licenses-and-versions.md) |
| Dienste Dritter | Manche Erweiterungen rufen bei Aktivierung externe Dienste auf (Analyse, CAPTCHA, Anmeldung über soziale Netzwerke) | [Datenschutzstandards](../../../../../eng-us/standards/privacy.md) |

## Eigens entwickelte Arbeit unter der GPL

Die Fragen zur Lizenzierung des Projekts erklären, dass Erweiterungen und Designs abgeleitete Werke sind, sie werden
also, **wenn sie weitergegeben werden**, unter der GPL Version 2 oder neuer weitergegeben. Eine Agentur, die eine
Website für einen Kunden baut, handelt als dessen Beauftragte, was laut den Fragen keine Weitergabe ist. Zwei Folgen für
eine Ausschreibung:

- Sie können den Quellcode eigens entwickelter Arbeit (OSS-02) unabhängig von der GPL verlangen; die GPL allein
  verpflichtet einen Lieferanten, der nie an Sie weitergibt, nicht, Ihnen den Quellcode in brauchbarer Form zu
  übergeben. Schreiben Sie es hinein.
- Wollen Sie die eigens entwickelte Arbeit für andere veröffentlichen, erlaubt die GPL das bereits, sobald Sie sie
  erhalten haben; OSS-04 macht die Absicht ausdrücklich.

Siehe auch die Vorlage [Rechte an individuellen Entwicklungen](../ausschreibungsvorlagen/klausel-rechte-an-individuellen-entwicklungen.md).

## Fallstricke

- „Open Core“ akzeptieren: eine offene Basis mit geschlossenen Erweiterungen, von denen Sie abhängen. OSS-07 erkennt
  das.
- Schriften, Bilder und JavaScript-Bibliotheken im Design vergessen; auch sie haben Lizenzen.
- Annehmen, die Dokumentation habe dieselbe Lizenz wie der Code. Bei Exponential ist es die GFDL, nicht die GPL.

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Ausschreibungsvorlage: Open-Source-Lizenz und Verfügbarkeit des Quellcodes](../ausschreibungsvorlagen/klausel-open-source-lizenz-und-quellcode.md)
- [Anforderungen an Ausstieg und Portabilität](anforderungen-an-ausstieg-und-portabilitaet.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
