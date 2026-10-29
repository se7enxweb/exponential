---
title: "Gesprächspunkte: das Grundset"
audience: advocate
category: advocacy
order: 10
summary: Die zehn Gesprächspunkte, die jeder Exponential-Fürsprecher verwenden kann, jeweils als Satz zum Sagen, mit der Quelle, die ihn belegt, und wie ein Zuhörer ihn prüfen kann, mit Hinweisen, wie Sie Punkte für Ihr Publikum auswählen.
keywords: Gesprächspunkte, Kernbotschaften, Botschaften, Pitch, Grundset, Beleg, Quellen, Open Source, GPL, Inhaltsmodell, mehrere Websites, Sprachen, Sicherheit, Velocity, Layouts, Hilfe, Community, talking points, key messages, messaging, pitch, core set, proof, sources, open source, content model, multisite, languages, security, layouts, help, community
featured: true
type: help
---
# Gesprächspunkte: das Grundset

Diese Seite ist für jeden Fürsprecher. Sie bietet die zehn Gesprächspunkte, die in fast jedem Gespräch über Exponential
funktionieren. Jeder hat einen Satz, den Sie sagen können, die Quelle, die ihn belegt, und einen Weg, wie der Zuhörer
ihn prüfen kann. Die Seiten für die Zielgruppen bauen darauf auf: [Redakteure](gespraechspunkte-fuer-redakteure.md),
[Entwickler](gespraechspunkte-fuer-entwickler.md), [Administratoren](gespraechspunkte-fuer-administratoren.md),
[Entscheider](gespraechspunkte-fuer-entscheider.md).

## Inhalt

1. [Wie Sie Gesprächspunkte verwenden](#wie-sie-gesprächspunkte-verwenden)
2. [Die zehn Punkte](#die-zehn-punkte)
3. [Drei für Ihren Zuhörer auswählen](#drei-für-ihren-zuhörer-auswählen)
4. [Aus einem Punkt eine Geschichte machen](#aus-einem-punkt-eine-geschichte-machen)
5. [Bevor Sie sie öffentlich verwenden](#bevor-sie-sie-öffentlich-verwenden)

## Wie Sie Gesprächspunkte verwenden

- **Sagen Sie sie nicht auf.** Wählen Sie die zwei oder drei, die beantworten, was Ihrem Zuhörer wichtig ist.
- **Nennen Sie die Quelle.** „Die README sagt ...“, „Die Sicherheitsrichtlinie sagt ...“. Das macht Sie glaubwürdig
  und zeigt dem Zuhörer, wo er nachschauen kann.
- **Bieten Sie eine Prüfung an.** Ein Link, ein Befehl, ein Bildschirm in der Administration.
- **Hören Sie auf, wenn Sie unsicher sind.** „Ich weiß es nicht; ich suche die Quelle“ ist eine starke Antwort.

## Die zehn Punkte

| # | Sagen Sie | Quelle | Lassen Sie es prüfen |
|---|---|---|---|
| 1 | „Exponential ist ein professionelles PHP-Anwendungsframework mit fortgeschrittener CMS-Funktionalität. Seine bemerkenswerteste Eigenschaft ist ein vollständig anpassbares Inhaltsmodell.“ | [README](https://github.com/se7enxweb/exponential/blob/main/README.md), [Exponential 6](../wer-ist-die-exponential-foundation.md#exponential-6-der-kernel) | **Setup > Klassen** auf jeder Installation |
| 2 | „Es ist freie Software: der Code unter der GNU GPL Version 2 oder später, die Dokumentation unter der GNU FDL 1.3.“ | [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE), [Die Lizenz dieser Dokumentation](../../README-LIZENZ.md) | Die Dateien lesen |
| 3 | „Es wird aktiv gepflegt: fünfzehn Releases 6.0.x seit dem 1. Januar 2024, 6.0.15 in Entwicklung und Tests bei jedem Push.“ | [Wer ist 7x?, Die Releases 6.0.x](../wer-ist-7x.md#die-releases-60x), [CI](../../bc/6.0/ci.md) | `git tag -l 'v6.0.*' --sort=version:refname` |
| 4 | „Ein Unternehmen und eine gemeinnützige Organisation stehen dahinter: 7x leitet es, und die Exponential Foundation betreibt das Projekt.“ | [Die Foundation und 7x](../wer-ist-die-exponential-foundation.md#die-foundation-und-7x) | Die zwei Websites |
| 5 | „Es läuft, wo Sie möchten: PHP 7.4 bis 8.5, sechs Datenbanken von SQLite bis Oracle und Linux, BSD, macOS oder Windows.“ | [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen), [Datenbankunterstützung](../datenbankunterstuetzung.md), [Betriebssystemunterstützung](../betriebssystemunterstuetzung.md) | **Setup > Systeminformationen** |
| 6 | „Eine Installation kann mehrere Websites und viele Sprachen bedienen, auch von rechts nach links.“ | [Funktions-Highlights](../geschaeft/funktionshighlights-fuer-kunden.md#mehrere-sites-aus-einer-installation), [Sprachen in Exponential](../../sprachen.md) | Die Globus-Schaltfläche und die Sprachkennzeichen in der Administration |
| 7 | „Sicherheit ist ein Prozess, den man sehen kann: vertrauliche Meldung mit einer ersten Antwort innerhalb eines Werktags, eine Content Security Policy, ein Prüfprotokoll und die zweistufige Anmeldung.“ | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md), [Sicherheitshärtung](../sicherheitshaertung.md) | Der Reiter **Audit**, `/user2fa/setup` |
| 8 | „Redakteure bauen Seiten selbst mit Exponential Layouts und bekommen Hilfe beim Schreiben von Exponential Intelligence, das nur Entwürfe schreibt.“ | [Der Layouts-Editor](../layout-editor.md), [expai](../../funktionen/6.0/erweiterungen/expai.md#kurz-gesagt) | Der Reiter **Layouts** |
| 9 | „Die Hilfe ist eingebaut: Die Dokumentation steckt in der Administration, mit einer Hilfe-Schaltfläche auf jeder Seite.“ | [Das Hilfesystem](../../funktionen/6.0/hilfesystem.md) | Das „?“ in der Kopfzeile |
| 10 | „Die Community ist offen: Jeder Pull Request wird übernommen, und die Foren, der Telegram-Chat und die Issue-Tracker sind öffentlich.“ | [Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst), [Orte der Community](../wer-ist-die-exponential-foundation.md#orte-der-community) | Die Kanäle |

## Drei für Ihren Zuhörer auswählen

| Zuhörer | Beginnen Sie mit | Dann | Ausführliche Seite |
|---|---|---|---|
| Redakteur | 8, 9 | 6 | [Gesprächspunkte für Redakteure](gespraechspunkte-fuer-redakteure.md) |
| Entwickler | 1, 10 | 5 | [Gesprächspunkte für Entwickler](gespraechspunkte-fuer-entwickler.md) |
| Administrator oder Hoster | 5, 7 | 3 | [Gesprächspunkte für Administratoren](gespraechspunkte-fuer-administratoren.md) |
| Entscheider | 3, 4 | 2 | [Gesprächspunkte für Entscheider](gespraechspunkte-fuer-entscheider.md) |
| Person aus der Open-Source-Welt | 2, 10 | 4 | [Open Source und Lizenzierung](gespraechspunkte-open-source-und-lizenzierung.md) |
| Person mit Fokus auf Barrierefreiheit | 9, dann [Barrierefreiheit](gespraechspunkte-barrierefreiheit.md) | 6 | [Gesprächspunkte: Barrierefreiheit](gespraechspunkte-barrierefreiheit.md) |
| Person mit Fokus auf Sicherheit | 7 | 3 | [Gesprächspunkte: Sicherheit](gespraechspunkte-sicherheit.md) |

## Aus einem Punkt eine Geschichte machen

Ein Punkt ist eine Behauptung; eine Geschichte bleibt hängen. Nutzen Sie das Muster **Situation, Handlung, Ergebnis,
Quelle**:

```text
Situation  "A colleague's team had three sites in two languages and three separate installations."
Action     "They moved them into one Exponential installation with one siteaccess per site."   (your experience)
Result     "Editors now sign in once and translate an item side by side."                     (your experience)
Source     "Siteaccesses and translations are described in the help: Languages, and the feature pages."
```

Erzählen Sie nur Geschichten, die Ihre eigenen sind oder die Sie erzählen dürfen, und halten Sie Menschen und Kunden
anonym, es sei denn, sie haben zugestimmt ([Die Privatsphäre von Menschen schützen](privatsphaere-schuetzen.md)).

## Bevor Sie sie öffentlich verwenden

- [ ] Öffnen Sie jede Quelle einmal und prüfen Sie, ob sie noch sagt, was der Punkt sagt (Quellen ändern sich mit Releases).
- [ ] Lesen Sie [Aussagen, die Sie nicht machen dürfen](aussagen-die-sie-nicht-machen-duerfen.md).
- [ ] Verwenden Sie die richtigen Namen ([Kurzkarte zu den Namensregeln](kurzkarte-namensregeln.md)).
- [ ] Halten Sie die Links zum Einfügen bereit ([Der Werkzeugkasten für Fürsprecher](werkzeugkasten-fuer-fuersprecher.md)).

## Verwandte Seiten

- [Aussagen, die Sie nicht machen dürfen](aussagen-die-sie-nicht-machen-duerfen.md)
- [Wo die Fakten stehen](wo-die-fakten-stehen.md)
- [Positionierung und Nutzenversprechen](../geschaeft/positionierung-und-nutzenversprechen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
