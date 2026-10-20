---
title: "Fragen zur Lizenz: die GPL und Exponential"
audience: user, admin, developer, designer, partner
category: project
order: 20
summary: Die Lizenz-FAQ von exponential.earth in einfachen Worten - die GPL Version 2 oder später, kompatibler und inkompatibler Code, Assets und Abhängigkeiten, wer das Urheberrecht hält, was Quellcode bei einer Webanwendung bedeutet, ob Ihre Erweiterung oder Ihr Design unter der GPL stehen muss, ob Sie es weitergeben müssen, Verkauf, Agenturen, die für Kunden arbeiten, Brücken-Erweiterungen, Besucher von Websites und GPL Version 3. Keine Rechtsberatung.
keywords: Lizenz, Lizenzierung, GPL-kompatibel, Urheberrecht, Quellcode, abgeleitetes Werk, Erweiterung, Design, Modul, verkaufen, Agentur, Kunde, Brückenmodul, Besucher einer Website, Marke, license, licensing, GPL, GPL-2.0-or-later, GPL version 3, GPL compatible, AGPL, LGPL, MIT, BSD, PHP license, PEAR, copyright, source code, derivative work, extension, design, module, sell, agency, client, bridge module, website visitors, trademark, Creative Commons
type: help
---
# Fragen zur Lizenz: die GPL und Exponential

Die Projektwebsite beantwortet die häufigen Fragen zur Lizenz unter
[exponential.earth/faq/licensing](https://exponential.earth/faq/licensing). Diese Seite gibt dieselben Antworten in
einfachen Worten wieder, abgeglichen mit den Lizenzdateien dieses Repositorys. Sie ist **keine Rechtsberatung**:
Lesen Sie für Ihren eigenen Fall die Lizenz und fragen Sie einen Anwalt, wie es auch die Website empfiehlt.

Die Lizenztexte: `LICENSE` und `COPYRIGHT.md` im Hauptverzeichnis der Installation sowie die
[GNU GPL Version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) auf gnu.org.

## Die Lizenz

### Unter welcher Lizenz steht Exponential?

Unter der **GNU General Public License, Version 2 oder (nach Ihrer Wahl) jeder späteren Version** (`COPYRIGHT.md`;
`"license": "GPL-2.0-or-later"` in `composer.json`). „Oder jeder späteren Version“ bedeutet, dass Sie den
Bedingungen der GPL Version 2 oder der GPL Version 3 folgen dürfen, je nachdem, was Ihnen besser passt, und dass Sie
Exponential mit Code unter Lizenzen kombinieren dürfen, die mit einer der beiden kompatibel sind, etwa der GNU
Affero GPL Version 3, wie die Website anmerkt.

### Wer hält das Urheberrecht?

Der Vermerk des Kernels nennt „7x & Exponential Foundation“ für die Jahre ab 1998 und eZ Systems AS für 1999 bis 2014
(`COPYRIGHT.md`). Der Code enthält außerdem die Arbeit vieler Mitwirkender über die Jahre. Die FAQ der Website
erklärt die Regel: Mitwirkende behalten das Urheberrecht an ihrem Code und erklären sich bereit, ihn unter derselben
Lizenz wie Exponential zu veröffentlichen.

### Wird die GPL auf ausschließlich Version 3 umgestellt?

Laut Website gibt es keine Pläne, Exponential ausschließlich unter der GPL Version 3 oder später zu verbreiten,
auch wenn das eines Tages erwogen werden könnte. Weil die Lizenz bereits „oder jeder späteren Version“ sagt, darf es
jeder schon heute unter der GPL 3 verbreiten.

## Exponential verwenden

### Muss ich den Code meiner Website an ihre Besucher herausgeben?

**Nein.** Der Besuch einer Website ist keine Verbreitung im Sinne der GPL, also verpflichtet Sie nichts, den Code
weiterzugeben, der auf Ihrem Server läuft. Die GPL Versionen 2 und 3 enthalten keine Klausel zur Nutzung über ein
Netzwerk.

### Darf ich Exponential für kommerzielle Arbeit nutzen?

Ja. Die GPL erlaubt jede Nutzung, einschließlich des Betriebs einer kommerziellen Site, des Baus von Sites für Kunden
und des Verkaufs von Dienstleistungen rund darum.

### Darf ich Exponential oder eine Erweiterung oder ein Design dafür verkaufen?

**Ja**, aber Sie verkaufen es unter der GPL Version 2 oder später: Der Käufer erhält den Quellcode und darf ihn
ändern und weitergeben, auch kostenlos. Sie dürfen für die Kopie, die Installation, den Support und Updates Geld
verlangen.

## Ihre eigenen Erweiterungen und Designs

### Muss meine Erweiterung oder mein Design unter der GPL stehen?

Die Antwort der Website: Erweiterungen („Module“) und Designs sind abgeleitete Werke von Exponential; **wenn Sie sie
verbreiten**, verbreiten Sie sie also unter der GPL Version 2 oder später. Verbreiten Sie sie nie, stellt sich die
Frage nicht.

Was die GPL erfasst, erklärt die Website, ist Code, der mit dem Code von Exponential zusammenarbeitet:

- Ihr **PHP-Code**, der Klassen von Exponential verwendet, muss unter der GPL oder einer GPL-kompatiblen Lizenz
  stehen;
- **Bilder und andere Dateien**, die Ihr PHP nur an den Browser schickt, sind Daten und nicht betroffen, es sei denn,
  Sie haben sie aus einem Bild erstellt, das Exponential unter der GPL bereitstellt;
- **JavaScript**, das mit dem eigenen JavaScript von Exponential zusammenarbeitet (das unter der GPL steht,
  einschließlich der mitgelieferten Kopie von jQuery), muss ebenfalls unter der GPL oder einer GPL-kompatiblen Lizenz
  stehen.

### Muss ich meine Erweiterung allen geben?

**Nein.** Die GPL sagt nur, dass jeder, der Ihre Erweiterung von Ihnen *erhält*, auch ihren Quellcode erhalten muss,
unter der GPL. Sie müssen sie niemandem geben, und eine Erweiterung, die Sie nur innerhalb Ihrer Organisation
verwenden, müssen Sie überhaupt nicht weitergeben. Die Website fügt hinzu, dass es trotzdem eine gute Idee ist, eine
allgemein nützliche Erweiterung zu teilen: Sie bekommen Rückmeldungen, Fehlermeldungen und Verbesserungen zurück. Wie
das geht: [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md).

### Was bedeutet „Quellcode“ bei einer Webanwendung?

Die Form einer Datei, die Menschen bearbeiten:

| Datei | Ihr Quellcode |
|---|---|
| PHP, Templates | die Datei selbst, nicht komprimiert oder verschleiert |
| JavaScript, CSS | die Datei selbst, nicht minifiziert oder verschleiert; bei einer minifizierten Datei das Original, aus dem sie erzeugt wurde |
| Bilder | die zum Bearbeiten gedachte Fassung: das PNG oder JPEG selbst, ein hochauflösendes Original oder die Datei des Zeichenprogramms |
| Kompilierte oder gepackte Formate | die bearbeitbaren Dateien und alles, was zum Erzeugen des Ergebnisses nötig ist |

### Meine Erweiterung verwendet eine Bibliothek unter einer anderen Lizenz. Ist das erlaubt?

| Die Bibliothek ist | Dann |
|---|---|
| GPL oder GPL-kompatibel (MIT, BSD, LGPL und andere auf der [Liste der FSF](https://www.gnu.org/licenses/license-list.html#GPLCompatibleLicenses)) | Sie dürfen Ihre Erweiterung zusammen mit ihr verbreiten. Die Kombination wird unter der GPL verbreitet. |
| GPL-inkompatibel, aber Ihre Erweiterung spricht mit ihr nur über das Netzwerk (HTTP, XML-RPC, SOAP, eine Web-API) | Erlaubt: Das andere System bleibt getrennt. |
| GPL-inkompatibel und in denselben PHP-Prozess geladen | Sie dürfen die Kombination nicht verbreiten: Sie wäre ein abgeleitetes Werk beider und würde die eine oder die andere Lizenz verletzen. Die Website nennt die PHP License vieler PEAR-Pakete als Beispiel für eine Open-Source-Lizenz, die nicht GPL-kompatibel ist. |
| GPL-inkompatibel, vom Benutzer über Composer installiert | Laut Website ist die Abhängigkeit von solchem Code erlaubt, weil die GPL die Verbreitung beschränkt, nicht die Nutzung; das Projekt hostet diesen Code nur nicht selbst. |

Assets, die kein Code sind, etwa Schriften und Symbole unter anderen Lizenzen, dürfen „als Zusammenstellung“ mit
GPL-Code ausgeliefert werden, wenn Sie das Recht haben, sie zu verbreiten, fügt die Website hinzu.

Schreiben Sie eine „Brücken“-Erweiterung für eine externe Bibliothek, bittet die Website Sie, die Bibliothek nicht in
ein Repository des Projekts zu kopieren; sagen Sie den Benutzern stattdessen, wie sie sie installieren (wo möglich
mit Composer), und eröffnen Sie zuerst ein Issue, wenn Sie meinen, dass sie wirklich mitgeliefert werden muss.

### Darf ich Code ausschließlich unter GPL Version 3 oder ausschließlich unter GPL Version 2 beitragen?

Ihre eigene Arbeit dürfen Sie unter jeder Lizenz veröffentlichen, die mit der GPL 2 oder später kompatibel ist. Code,
der **in die eigenen Repositories des Projekts** eingeht, muss aber unter derselben Lizenz stehen wie Exponential,
GPL Version 2 oder später, damit alle die Wahl zwischen den Versionen behalten. Wenn Sie einen Beitrag nicht so
lizenzieren können oder wollen, reichen Sie ihn nicht ein, sagt die Website. Die `CONTRIBUTING.md` des Kernels sagt
dasselbe.

## Agenturen und Kunden

### Verbreitet eine Agentur Code, wenn sie eine Site für einen Kunden baut?

Die Antwort der Website lautet **nein**: Eine Agentur, ein Freiberufler oder ein anderer Dienstleister, der im
Auftrag eines Kunden eine Codebasis zusammenstellt, handelt als Beauftragter des Kunden; das ist keine Verbreitung im
Sinne der GPL. Die Website fügt hinzu, dass ein solcher Dienstleister daher für den Kunden GPL-Code mit inkompatiblem
Code kombinieren darf, diese Kombination aber nicht öffentlich verbreiten darf.

Der Kunde darf mit dem Ergebnis tun, was die GPL erlaubt. Dieselbe Erweiterung an andere Kunden zu verkaufen oder zu
geben oder sie zu veröffentlichen, ist Verbreitung, mit allem, was daraus folgt.

## Probleme und Fragen

### Ich habe eine Erweiterung gefunden, die „nur GPL 2“, „nur GPL 3“ oder eine andere Lizenz angibt. Was soll ich tun?

Die Website bittet Sie, zuerst ein Issue im Tracker dieses Projekts zu eröffnen und, falls der Maintainer nicht
antwortet, es im [Issue-Tracker von Exponential](https://github.com/se7enxweb/exponential/issues) zu melden. Bevor
Sie Code aus einer solchen Erweiterung in Ihren eigenen übernehmen, lesen Sie
[Lizenzen lesen, bevor Sie etwas wiederverwenden](../../../en/us/help/extension-development/part-1-from-user-to-developer/reading-licenses-before-reusing.md).

### Welche Lizenz hat die Dokumentation?

Diese Hilfe und die Dokumentation in `doc/` stehen unter der GNU Free Documentation License 1.3 oder später
([Die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md), [Freie Dokumentation](../freie-dokumentation.md)). Die
Lizenz-FAQ der Website sagt, ihre eigenen Inhalte stünden unter Creative Commons Attribution-ShareAlike 2.0, während
die Fußzeile von exponential.earth „All rights reserved“ lautet; solange beides nicht übereinstimmt, verlinken Sie auf
die Website, statt sie zu kopieren.

### Ich habe eine andere Frage.

Stellen Sie allgemeine Fragen in den [Foren von Share Exponential](https://share.exponential.earth/forums). Für Ihren
eigenen Fall fragen Sie einen Anwalt: Das Projekt leistet keine Rechtsberatung.

## Verwandte Seiten

- [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md)
- [Lizenzen lesen, bevor Sie etwas wiederverwenden](../../../en/us/help/extension-development/part-1-from-user-to-developer/reading-licenses-before-reusing.md)
- [Exponential: Häufige Fragen](faq.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt
  und verbessert werden.
