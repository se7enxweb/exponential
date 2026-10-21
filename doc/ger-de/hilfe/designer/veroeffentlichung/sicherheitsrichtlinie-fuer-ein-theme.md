---
title: "Eine Sicherheitsrichtlinie für ein Theme"
audience: designer, developer
category: theme-distribution
order: 390
summary: Eine SECURITY.md für ein Theme schreiben - unterstützte Versionen, ein vertraulicher Meldeweg, was eine Meldung enthalten soll, was Meldende erwarten können - nach dem Vorbild der Richtlinie des Repositorys von Exponential.
keywords: SECURITY.md, Sicherheitsrichtlinie, Sicherheitslücke, vertrauliche Meldung, Offenlegung, unterstützte Versionen, GitHub private vulnerability reporting, security policy, vulnerability, private report, disclosure, supported versions
type: help
---
# Eine Sicherheitsrichtlinie für ein Theme

Diese Seite ist für Designer, die ein Theme veröffentlichen. Themes enthalten Code: Templates, die Nutzereingaben
ausgeben, Skripte, manchmal PHP. Eine Meldung über eine Sicherheitslücke braucht einen vertraulichen Weg zu Ihnen, sonst
landet sie als öffentliches Issue, das zuerst Angreifern hilft.

## Das Vorbild: die eigene Richtlinie von Exponential

Die `SECURITY.md` im Wurzelverzeichnis des Repositorys von Exponential hat drei Teile:

1. **Unterstützte Versionen**: welche Linie Korrekturen bekommt und dass ein veröffentlichter Tag nie geändert wird;
   eine Korrektur kommt als nächstes Release.
2. **Melden**: vertraulich per E-Mail melden, nicht im öffentlichen Issue-Tracker, mit einer angegebenen Zeit bis zur
   ersten Antwort.
3. **Was enthalten sein soll**: die Version, die PHP-Version, die Server-Engine und die Datenbank, was ein Angreifer
   tun kann, die Schritte zum Nachvollziehen und keine Passwörter, Geheimnisse oder personenbezogenen Daten.

Sie endet damit, was der Meldende wann erfährt.

## Schritt für Schritt

1. Legen Sie `SECURITY.md` im Wurzelverzeichnis des Themes an:

   ```markdown
   # Security Policy

   ## Supported versions

   The newest 1.0.x release gets security fixes. A published tag is never changed: a fix arrives as the
   next release.

   ## Reporting a vulnerability

   Please report security issues privately to <your security address>, not in the public issues.
   Include the theme version, the Exponential and PHP versions, what an attacker can do and how to
   reproduce it. Leave out passwords and personal data.

   You will get an answer within <a time you can keep>.
   ```

2. Verwenden Sie wenn möglich eine Adresse, die mehr als eine Person liest.
3. Auf GitHub können Sie für das Repository auch die vertrauliche Meldung von Sicherheitslücken aktivieren
   (**Settings > Security**), die Meldenden ein vertrauliches Formular gibt.
4. Verlinken Sie `SECURITY.md` im Support-Abschnitt des README.

## Verwandte Seiten

- [Sicherheitsmeldungen bearbeiten](../lebenszyklus/sicherheitsmeldungen-bearbeiten.md)
- [Ein Sicherheitsrelease veröffentlichen](../lebenszyklus/ein-sicherheitsrelease-veroeffentlichen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
