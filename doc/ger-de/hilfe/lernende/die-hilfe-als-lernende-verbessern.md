---
title: Die Hilfe als Lernende verbessern
audience: student
category: learning
order: 42
summary: Wie Lernende die Exponential-Hilfe verbessern - warum Lernende die besten Fehler in der Dokumentation finden, der schnelle Weg mit Auf GitHub bearbeiten, der gründliche Weg mit einem Klon, die Front Matter und die Stilregeln von Hilfeseiten, was die GNU FDL für Ihren Beitrag bedeutet und was Sie vermeiden sollten.
keywords: dokumentation, hilfe, verbessern, Auf GitHub bearbeiten, pull request, front matter, stil, Markdown, GFDL, freie dokumentation, tippfehler, veralteter schritt, lernende, documentation, help, improve, Edit on GitHub, style, free documentation, typo, outdated step, learner
type: help
---
# Die Hilfe als Lernende verbessern

Sie lesen die Hilfe gründlicher als alle anderen: Sie folgen jedem Schritt und merken sofort, wenn einer nicht
funktioniert. Das macht Lernende zu den besten Testern der Dokumentation. Diese Seite zeigt, wie Sie aus dem, was Ihnen
auffällt, eine Verbesserung für alle machen.

## Inhalt

1. [Was Lernenden auffällt](#was-lernenden-auffällt)
2. [Der schnelle Weg: Auf GitHub bearbeiten](#der-schnelle-weg-auf-github-bearbeiten)
3. [Der gründliche Weg: ein Klon](#der-gründliche-weg-ein-klon)
4. [Wie eine Hilfeseite geschrieben wird](#wie-eine-hilfeseite-geschrieben-wird)
5. [Ihre Rechte und die Lizenz](#ihre-rechte-und-die-lizenz)
6. [Was Sie vermeiden sollten](#was-sie-vermeiden-sollten)

## Was Lernenden auffällt

| Ihnen fällt auf | Beitrag |
|---|---|
| Ein Tippfehler oder ein verunglückter Satz | Eine einzeilige Korrektur |
| Ein Schritt, der nicht zur Software passt | Ein korrigierter Schritt, mit dem Release, das Sie getestet haben |
| Ein Wort, das niemand erklärt hat | Ein Link auf das Glossar oder eine kurze Erklärung |
| Eine „Erwartet“-Ausgabe, die abweicht | Die tatsächliche Ausgabe, mit dem Befehl |
| Ein fehlender Link auf die nächste Seite | Der Link |
| Eine Seite, die Sie nicht finden konnten | Ein Issue mit einem Vorschlag, wo sie verlinkt werden sollte |

## Der schnelle Weg: Auf GitHub bearbeiten

Jede Hilfeseite hat einen Link **Auf GitHub bearbeiten**. Öffnen Sie ihn, ändern Sie den Text im Browser und schlagen
Sie die Änderung als Pull Request vor ([Dokumentation beitragen](../unterstuetzer/dokumentation-beitragen.md#eine-seite-in-zwei-minuten-korrigieren)).
Tippfehler, veraltete Schritte und unklare Sätze sind alle willkommen. Schreiben Sie eine Commit-Betreffzeile wie
`Fixed: The languages help names the Apply button`.

## Der gründliche Weg: ein Klon

Für mehrere Seiten oder eine neue Seite arbeiten Sie in Ihrem Klon des Kernels: Die Hilfe besteht aus den
Markdown-Dateien in `doc/`, Hilfeseiten liegen unter `doc/eng-us/help/` ([Die Hilfe verbessern](../fuersprecher/die-hilfe-verbessern.md),
[Git als Lernende nutzen](git-als-lernende-nutzen.md)). Legen Sie einen Branch an, bearbeiten Sie, sehen Sie sich das
Ergebnis in der Hilfe Ihrer Lernwebsite an, committen Sie und eröffnen Sie einen Pull Request.

Die Installation prüft die Links und Bilder der Hilfeseiten mit einem Konsolenbefehl:

```bash
php bin/php/console exp:help check --below=help/ --allow-root-user
```

Führen Sie ihn aus, bevor Sie einen Pull Request einsenden, der Links hinzufügt oder verschiebt (`bin/php/exphelp.php`).

## Wie eine Hilfeseite geschrieben wird

Jede Hilfeseite beginnt mit einer Front Matter ([Dokumentation beitragen](../unterstuetzer/dokumentation-beitragen.md#eine-neue-hilfeseite-schreiben)):

```text
---
title: <Title>
audience: editor, admin
category: editing
order: 50
summary: <One or two sentences: who it is for and what it answers.>
keywords: <comma separated>
type: help
---
```

Die Werte für Zielgruppe und Kategorie, die die Hilfe versteht, sind in `settings/exphelp.ini` konfiguriert. Die
Stilregeln:

- Amerikanisches Englisch; „Exponential“ für das Produkt; der alte Name nur für die Geschichte.
- Jede Angabe mit ihrer Quelle: eine Datei, eine Seite oder ein Befehl mit seiner erwarteten Ausgabe.
- Schritte, die auf einer aktuellen Installation funktionieren; nennen Sie die Version, die Sie getestet haben.
- Keine Geheimnisse, keine privaten Hostnamen, keine personenbezogenen Daten in Beispielen.
- Tabellen und Checklisten, wo sie helfen; kurze Sätze.

## Ihre Rechte und die Lizenz

Die Dokumentation ist freie Dokumentation unter der GNU Free Documentation License 1.3 oder später. Ihr Beitrag wird
unter derselben Lizenz veröffentlicht, sodass andere ihn kopieren, übersetzen und verbessern dürfen
([Freie Dokumentation](../freie-dokumentation.md)). Sie dürfen die Hilfe auch in Ihren eigenen Notizen und Kursarbeiten
verwenden, wenn Sie die Hinweise beibehalten ([Freie Dokumentation](../freie-dokumentation.md#was-sie-tun-dürfen)).

## Was Sie vermeiden sollten

- Angaben ändern, die Sie nicht geprüft haben. Wenn Sie unsicher sind, eröffnen Sie ein Issue statt eines Pull
  Requests.
- Text aus Quellen kopieren, deren Lizenz das nicht erlaubt.
- Große Umarbeitungen als ersten Beitrag; fangen Sie klein an.
- Funktionen beschreiben, die es nicht gibt. Jede Aussage in der Hilfe wird anhand von Code, Einstellungen oder einer
  echten Installation geprüft.

## Verwandte Seiten

- [Dokumentation beitragen](../unterstuetzer/dokumentation-beitragen.md)
- [Ihr erster Beitrag, Schritt für Schritt](ihr-erster-beitrag-schritt-fuer-schritt.md)
- [Die Hilfe verbessern](../fuersprecher/die-hilfe-verbessern.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
