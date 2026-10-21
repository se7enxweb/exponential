---
title: "Ein Theme an einen neuen Betreuer übergeben"
audience: designer, project-manager
category: theme-lifecycle
order: 280
summary: Ein Theme so an einen neuen Betreuer übergeben, dass Nutzer nichts als Kontinuität bemerken - jemanden wählen, eine Übergabezeit, Dokumentation, Zugänge, Repository und Paket, und die Ankündigung.
keywords: Übergabe, neuer Betreuer, Nachfolge, Übertragung, Zugang, Dokumentation, Kontinuität, Ankündigung, handover, new maintainer, succession, transfer, access, documentation, continuity, announcement
type: help
---
# Ein Theme an einen neuen Betreuer übergeben

Diese Seite ist für Designer, die möchten, dass ihr Theme ihre eigene Beteiligung überdauert.

## Jemanden wählen

Gute Kandidaten haben schon beigetragen (Pull-Requests, Antworten in Issues) oder hängen beruflich von dem Theme ab. Fragen
Sie öffentlich in den Issues und auf share.exponential.earth und sprechen Sie dann mit den Kandidaten.

## Schritt für Schritt

1. **Vereinbaren** Sie die Übergabe schriftlich: ab wann, was übernommen wird, ob Sie als Mitwirkender bleiben.
2. **Schreiben Sie auf, was in Ihrem Kopf ist**: Release-Routine, Testumgebung, Seitenliste, bekannte Probleme, Kunden,
   die darauf angewiesen sind (ohne deren private Daten) ([Eine Upgrade-Testumgebung](upgrade-testumgebung.md)).
3. **Geben Sie Zugang**, zuerst als Mitbetreuer: Schreibzugriff auf das Repository, Betreuer bei Packagist. Machen Sie ein
   Release zusammen.
4. **Übertragen** Sie Repository und Paket, wenn es so weit ist
   ([Das Repository und das Packagist-Paket übertragen](repository-und-packagist-paket-uebertragen.md)).
5. **Aktualisieren Sie die Metadaten**: `Info_url`, `homepage`, `support` und Autoren in `ezinfo.php`, `extension.xml`,
   `composer.json`; behalten Sie Ihre Copyright-Zeilen und fügen Sie die der neuen Person hinzu
   ([Danksagungen und Namensnennung](danksagungen-und-namensnennung.md)).
6. **Kündigen Sie es an**, in einer Release-Notiz, im README, auf der Projektseite und in Ihrem Ankündigungs-Thread.
7. **Entfernen Sie Ihren Zugang**, wenn die Übergabe abgeschlossen ist, falls so vereinbart.

## Was bleiben muss

Die Copyright-Hinweise aller, die beigetragen haben, und die Lizenz. Eine Übergabe ändert, wer pflegt, nicht, wer
geschrieben hat.

## Verwandte Seiten

- [Ein verwaistes Theme übernehmen](ein-verwaistes-theme-uebernehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
