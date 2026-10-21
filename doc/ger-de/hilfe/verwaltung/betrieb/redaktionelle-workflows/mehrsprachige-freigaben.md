---
title: Mehrsprachige Freigaben
audience: government, admin, editor
category: public-sector
order: 80
summary: Wie Sie Inhalte in mehreren Amtssprachen freigeben - eine Freigabe für alle Sprachen oder Freigebende je Sprache mit dem Feld Betroffene Sprachen und auf Sprachen eingeschränkten Rollen - und wie Übersetzerinnen, Übersetzer und Freigebende in das Versionsmodell passen.
keywords: mehrsprachig, Übersetzungen, Amtssprachen, zweisprachig, Freigabe je Sprache, Betroffene Sprachen, Übersetzer, Einschränkung Language, content/translate, Ursprungssprache, Freigabe-Workflow, multilingual, translations, official languages, bilingual, language approval, Affected languages, translators, Language limitation, initial language, approval workflow
type: help
---
# Mehrsprachige Freigaben

Diese Seite richtet sich an Webverantwortliche öffentlicher Websites, die in mehr als einer Amtssprache veröffentlicht werden. Sie zeigt, wie der
Freigabe-Workflow Übersetzungen behandelt und wie Sie jedem Sprachteam eigene Freigebende geben. Quellen:
[Workflows, Abschnitt 3.2](../../../../anleitungen/workflows.md#32-das-ereignis-freigeben-einrichten),
[Sprachen von Inhalten](../../../../anleitungen/inhaltssprachen.md) und die Einschränkung `Language` in
`kernel/content/module.php`.

## Inhalt
1. [Wie eine Übersetzung zur Freigabe gelangt](#wie-eine-übersetzung-zur-freigabe-gelangt)
2. [Muster 1: eine Freigabe für jede Sprache](#muster-1-eine-freigabe-für-jede-sprache)
3. [Muster 2: Freigebende je Sprache](#muster-2-freigebende-je-sprache)
4. [Rollen für Übersetzerinnen und Übersetzer](#rollen-für-übersetzerinnen-und-übersetzer)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Wie eine Übersetzung zur Freigabe gelangt

Jedes Speichern eines Objekts ist eine Version, und eine Version wird in einer **Ursprungssprache** erstellt: Die Übersetzung einer Seite ins
Französische erzeugt eine neue Version mit Französisch als Ursprungssprache. Ihre Veröffentlichung führt denselben Trigger aus, **Vor dem
Veröffentlichen von Inhalten**, wie jede andere Version. Das Feld **Betroffene Sprachen** des Freigabe-Ereignisses entscheidet, welche
Ursprungssprachen eine Freigabe brauchen („nur Versionen, deren Ursprungssprache eine dieser Sprachen ist“).

## Muster 1: eine Freigabe für jede Sprache

**Betroffene Sprachen** = *Alle Sprachen*; eine Gruppe von Freigebenden, die jede Sprache liest. Einfach und geeignet, wenn die Freigebenden alle
Amtssprachen fließend beherrschen oder die Übersetzung von einem vertrauenswürdigen Dienst erstellt wird.

## Muster 2: Freigebende je Sprache

Wenn jede Sprache eigene Prüfende hat:

1. Legen Sie eine Gruppe von Freigebenden je Sprache an („Freigebende - Englisch“, „Freigebende - Französisch“).
2. Legen Sie einen Workflow je Sprache an, jeweils mit einem Freigabe-Ereignis: **Betroffene Sprachen** = diese Sprache, **Gruppen, die Content
   freigeben** = die Gruppe dieser Sprache.
3. Weil ein Trigger einen Workflow ausführt, legen Sie einen dritten Workflow mit einem Ereignis **Multiplexer** an, der die Sprach-Workflows
   ausführt, und verbinden Sie diesen mit **Vor dem Veröffentlichen von Inhalten**.
4. Testen Sie mit einer Version in jeder Sprache: Jede wartet nur auf ihre eigene Gruppe, weil das Freigabe-Ereignis der anderen Sprache sie
   durchlässt.

Exponential übersetzt keine Inhalte und prüft vor dem Veröffentlichen nicht, ob jede Sprache vorhanden ist; eine Seite kann in einer Sprache
öffentlich sein, während ihre Übersetzung noch ein Entwurf ist. Wenn Ihre Regeln eine gleichzeitige Veröffentlichung verlangen, halten Sie die
erste Sprache per Verfahren unveröffentlicht, bis die Übersetzung freigegeben ist.

## Rollen für Übersetzerinnen und Übersetzer

| Modul | Funktion | Einschränkungen | Warum |
|---|---|---|---|
| content | read | der Bereich | die Quelle sehen |
| content | translate | Sprache: die Zielsprache | übersetzen |
| content | edit | Sprache: die Zielsprache, Teilbaum | nur die eigene Sprache ändern |
| content | versionread, diff | der Bereich | mit der Quellversion vergleichen |

`content/create`, `edit`, `publish`, `translate` und `hide` akzeptieren die Einschränkung `Language`. Freigebende je Sprache erhalten dieselbe
Spracheinschränkung für jedes Bearbeitungsrecht, das sie haben.

## Fallstricke

- **Welche Sprachen eine Website zeigt**, ist eine Einstellung des Siteaccess (`SiteLanguageList`), getrennt von den Freigaben
  ([Sprachen von Inhalten, Abschnitt 5](../../../../anleitungen/inhaltssprachen.md#5-eine-sprache-anzeigen-sitelanguagelist-und-siteaccesses)).
- **Immer verfügbar**: Eine als immer verfügbar markierte Seite kann in ihrer Hauptsprache in einem Siteaccess für eine andere Sprache
  angezeigt werden; das ist eine Anzeigeregel, keine Freigabe.
- **Das Entfernen einer Sprache** entfernt Inhalte; lesen Sie zuerst [Sprachen von Inhalten, Abschnitt 9](../../../../anleitungen/inhaltssprachen.md#9-übersetzungen-und-sprachen-sicher-entfernen).
- **Sprachen mit Schreibrichtung von rechts nach links** werden in der Administration und in Designs unterstützt, die damit umgehen können;
  siehe [Von rechts nach links](../../../../../eng-us/features/6.0/right-to-left.md).

## Verwandte Seiten
- [Das Freigabe-Ereignis einrichten](das-freigabe-ereignis-einrichten.md)
- [Überblick über Freigabe-Workflows](freigabe-workflows-im-ueberblick.md)
- [Sprachen für öffentliche Stellen (Hilfe)](../../../sprachen.md)
- [Leitfaden zu Sprachen von Inhalten](../../../../anleitungen/inhaltssprachen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
