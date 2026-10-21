---
title: Die Abdeckung der Oberflächenübersetzung prüfen
audience: government, admin
category: public-sector
order: 130
summary: Aufgabe für Administratoren - herausfinden, wie vollständig die Übersetzung der Administration in eine Sprache ist, welche Übersetzung eine regionale Locale wirklich zeigt und was unübersetzt ist, mit ezchecktranslation, exp:i18n und Setup > Oberflächensprachen.
keywords: Abdeckung der Übersetzung, Oberflächensprachen, Ersatzsprachen, unübersetzte Texte, Oberflächenübersetzung, translation coverage, ezchecktranslation, exp:i18n, Setup Interface languages, fallbacks, untranslated strings, interface translation
type: help
---
# Die Abdeckung der Oberflächenübersetzung prüfen

Diese Aufgabe richtet sich an Administratoren, die entscheiden, ob Mitarbeiter in ihrer Sprache arbeiten können.
Hintergrund: [Oberflächensprachen der Administration](../../sprachen/sprachen-der-administrationsoberflaeche.md) und
[Ersatz für Oberflächenübersetzungen](../../sprachen/ersatz-fuer-oberflaechenuebersetzungen.md).

## Schritte

1. In der Administration **Setup > Oberflächensprachen** (`/setup/languages`, Richtlinie `setup/system_info`): jede
   Locale mit der Übersetzung, die sie zeigt, die Abdeckung des Kernels und der Erweiterungen und die Ersatzkette.
2. Auf der Kommandozeile die Statistik einer Übersetzungsdatei:

   ```bash
   php bin/php/ezchecktranslation.php fre-FR
   ```

3. Die Ersatzkette einer regionalen Locale:

   ```bash
   ./console exp:i18n fallbacks --locale=fre-CA
   ./console exp:i18n check
   ```

4. Vergleichen Sie mit der veröffentlichten Tabelle in
   [Sprachen in Exponential, Abschnitt 4](../../../../../eng-us/languages.md#4-every-administration-translation).

## Prüfen

Sie kennen den Prozentsatz der übersetzten aktuellen Texte, ob die Locale ihre eigene Datei oder einen Ersatz verwendet
und welche Erweiterungen Englisch zeigen.

## Nächste Schritte

- Geringe Abdeckung: Planen Sie mit Ihrem Sprachdienst eine Übersetzung oder Durchsicht und geben Sie sie an das Projekt
  zurück.
- Maschinell unterstützte Übersetzungen: Lassen Sie sie von einem Muttersprachler prüfen, bevor sich Mitarbeiter darauf
  verlassen.

## Verwandte Seiten

- [Sprachen in Exponential](../../../../../eng-us/languages.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
