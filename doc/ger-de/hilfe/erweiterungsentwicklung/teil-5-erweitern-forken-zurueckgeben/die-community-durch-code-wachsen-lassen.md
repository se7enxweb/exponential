---
title: Die Community durch Code wachsen lassen
audience: developer, designer, app-creator, user
category: extension-development
order: 150
summary: Wie Code selbst eine Community wachsen lässt - veröffentlichen, was Sie gebaut haben, gute Einsteiger-Issues, Dokumentation und Übersetzungen, Hilfeseiten in Ihrer Erweiterung und die Community-Websites, auf denen Leute nach Erweiterungen suchen.
keywords: Community, Open Source, veröffentlichen, good first issue, Dokumentation, Übersetzungen, Hilfeseiten, community, open source, publish, documentation, translations, help pages, share.exponential.earth, projects.exponential.earth, ezpedia.exponential.earth
type: help
---
# Die Community durch Code wachsen lassen

Teil 5, Kapitel 15. Eine Community wächst nicht durch Ankündigungen. Sie wächst, wenn Menschen etwas Nützliches
finden, es verstehen und sehen, wo sie helfen können. Jede Erweiterung, die Sie veröffentlichen, kann das leisten.
Dieses Kapitel sammelt die Gewohnheiten, die aus Code einen Ort machen, zu dem Menschen zurückkehren.

## Veröffentlichen Sie, was Sie bauen

Der wirksamste Beitrag ist die Erweiterung, die Sie ohnehin schon für sich selbst geschrieben haben. Wenn sie ein
Problem für Sie löst, löst sie es auch für jemand anderen. Die Schritte (aufräumen, Lizenz, Metadaten, GitHub,
Packagist) stehen in [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md) und das Vorgehen beim Release in
[Teil 7](../teil-7-veroeffentlichen/README.md). Entfernen Sie, was nur Ihre Website betrifft (Namen, Adressen,
Einstellungen mit Geheimnissen), und behalten Sie den Rest.

## Machen Sie den Einstieg leicht

- **Eine README, die drei Fragen** auf ihrem ersten Bildschirm beantwortet: was sie tut, wie man sie installiert, wie
  man sie in Aktion sieht.
- **Ein Screenshot oder ein kurzes Beispiel** des Ergebnisses.
- **Voreinstellungen, die funktionieren**, damit die erste Installation etwas zeigt.
- **Hilfeseiten in der Erweiterung**, die das Hilfesystem der Administration neben denen des Kernels anzeigt
  ([Ihre Erweiterung dokumentieren](../teil-3-erweiterungsentwickler/ihre-erweiterung-dokumentieren.md)
  und das Tutorial
  [Hilfeseiten für Ihre Erweiterung](../../../../eng-us/help/extension-development/part-3-extension-developer/tutorials/by-hand-help-pages-for-your-extension.md)).

## Machen Sie das Helfen leicht

- **Kennzeichnen Sie Issues** mit `good first issue`, wenn sie klein und gut beschrieben sind. Neue Beitragende suchen
  nach diesem Label.
- **Sagen Sie in der README, welche Hilfe Sie sich wünschen**: Übersetzungen, Tests auf PostgreSQL, eine Prüfung des
  dunklen Modus.
- **Halten Sie Übersetzungen offen.** Oberflächentexte als Übersetzungs-Strings (`i18n` mit dem Kontext Ihrer
  Erweiterung) erlauben jedem, mit einer einzigen Datei eine Sprache hinzuzufügen. expchangeclass hat auf diese Weise
  Spanisch, Hebräisch, Latein und Klingonisch bekommen (Commit c2a4f41, neben Deutsch, das schon früher dazukam).
- **Beantworten Sie den ersten Pull Request einer neuen beitragenden Person schnell und freundlich.** Davon hängt ab,
  ob es einen zweiten gibt.

## Machen Sie sie auffindbar

| Ort | Wofür er da ist |
|---|---|
| [Packagist](https://packagist.org) | `composer require` findet sie; Suche nach Stichwort |
| GitHub-Topics | fügen Sie dem Repository Topics wie `exponential` und `exponential-extension` hinzu |
| [projects.exponential.earth](https://projects.exponential.earth) | das Projektverzeichnis der Community |
| [share.exponential.earth](https://share.exponential.earth) | die Website der Community zum Teilen und Diskutieren |
| [ezpedia.exponential.earth](https://ezpedia.exponential.earth) | die Wissensdatenbank der Community, in der Artikel Ihre Erweiterung beschreiben und verlinken können |

Teil 1 beschreibt jede Website und was Sie dort finden
([projects.exponential.earth](../../../../eng-us/help/extension-development/part-1-from-user-to-developer/projects-exponential-earth.md),
[ezpedia.exponential.earth](../../../../eng-us/help/extension-development/part-1-from-user-to-developer/ezpedia-exponential-earth.md)).

## Teilen Sie, was Sie lernen

Ein gelöstes Problem, das aufgeschrieben wird, erspart der nächsten Person einen Tag:

- ein Artikel auf ezpedia.exponential.earth über eine Technik;
- eine Korrektur an diesem Buch: Jede Seite ist freie Dokumentation unter der GNU FDL, und Verbesserungen sind
  willkommen ([Freie Dokumentation](../../freie-dokumentation.md));
- eine Antwort auf share.exponential.earth auf die Frage eines anderen.

## Seien Sie die Art Projekt, bei der Menschen mitmachen wollen

- Nennen Sie alle, jedes Mal ([Namensnennung und Urheberrechtshinweise](namensnennung-und-urheberrechtshinweise.md)).
- Erklären Sie Entscheidungen öffentlich, in Issues und Commit-Nachrichten.
- Bedanken Sie sich.
- Seien Sie nie der Grund, warum jemand geht.

## Verwandte Seiten

- Zurück: [Der Weg zum Maintainer](der-weg-zum-maintainer.md)
- Weiter: [Fallstudie: ein Paket aus einem anderen Projekt mit Namensnennung portieren](fallstudie-aus-einem-anderen-projekt-portiert.md)
- [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md)
- [Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
