---
title: "Fragen zur Lizenz: die GPL und Exponential"
audience: user, admin, developer, designer, partner
category: project
order: 20
summary: Die Lizenz-FAQ von exponential.earth in einfachen Worten - die GPL Version 2 oder später, kompatibler und inkompatibler Code, Assets und Abhängigkeiten, wer das Urheberrecht hält, was Quellcode bei einer Webanwendung bedeutet, ob Ihre Erweiterung oder Ihr Design unter der GPL stehen muss, ob Sie es weitergeben müssen, Verkauf, Agenturen, die für Kunden arbeiten, Lizenzverstöße (was die Weitergabe von Exponential oder eines Designs verlangt, häufige Verstöße, Zurückgeben, was Kunden verlangen können, was zu tun ist), Brücken-Erweiterungen, Besucher von Websites und GPL Version 3. Keine Rechtsberatung.
keywords: Lizenz, Lizenzierung, Lizenzverstoß, Lizenzverstöße, GPL-Verstoß, Verbreitung, schriftliches Angebot, Quellcode, minifiziert, weitere Beschränkungen, Zurückgeben, license violation, GPL compliance, GPL-kompatibel, Urheberrecht, Quellcode, abgeleitetes Werk, Erweiterung, Design, Modul, verkaufen, Agentur, Kunde, Brückenmodul, Besucher einer Website, Marke, license, licensing, GPL, GPL-2.0-or-later, GPL version 3, GPL compatible, AGPL, LGPL, MIT, BSD, PHP license, PEAR, copyright, source code, derivative work, extension, design, module, sell, agency, client, bridge module, website visitors, trademark, Creative Commons
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

Der Vermerk des Kernels nennt die „Exponential Foundation and 7x“ für die Jahre ab 1998 und eZ Systems AS für 1999
bis 2014 (`COPYRIGHT.md`): „Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“ Seit dem
29. Oktober 2026 halten die Foundation und 7x das Urheberrecht gemeinsam, die Foundation zuerst genannt; siehe
[Urheberrecht und Copyleft](../urheberrecht-und-copyleft.md). Der Code enthält außerdem die Arbeit vieler Mitwirkender über die Jahre. Die FAQ der Website
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

Diese Antwort betrifft einen engen Fall: einen Auftragnehmer, der im Namen des Kunden und nach dessen Weisung
arbeitet, ähnlich wie ein Angestellter. Ob eine bestimmte Übergabe das ist oder eine Verbreitung, hängt vom Vertrag
und von den Umständen ab, und nicht jeder zieht die Grenze an derselben Stelle. Behandeln Sie die Lieferung im
Zweifel als Verbreitung und erfüllen Sie die Pflichten aus [Lizenzverstöße](#lizenzverstöße): Das kostet die Agentur
nichts außer der Übergabe von Dateien, die sie ohnehin hat.

## Lizenzverstöße

Dieser Abschnitt erklärt, wie die Lizenz von Exponential in der Praxis verletzt wird, damit wer Exponential oder ein
Design weitergibt, weiß, was er schuldet, und wer es erhält, weiß, was ihm zusteht. Er folgt der
[GNU GPL Version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) und nennt ihre Abschnitte (§); wo die
[GPL Version 3](https://www.gnu.org/licenses/gpl-3.0.html), die nach „oder jeder späteren Version“ jeder wählen darf,
abweicht, ist das vermerkt. Maßgeblich ist der englische Lizenztext. Dies ist keine Rechtsberatung.

### Was die Pflichten auslöst: die Verbreitung

Die Bedingungen der GPL gelten, wenn Sie Exponential oder ein darauf beruhendes Werk an einen Dritten **verbreiten**
(die GPL Version 3 sagt „convey“, übertragen). Ein auf Exponential beruhendes Werk ist zum Beispiel:

- Exponential selbst, verändert oder unverändert;
- eine Erweiterung, die die Klassen von Exponential verwendet und im selben PHP-Prozess läuft;
- ein Site-Paket, gebaut aus den Paketen und Daten von Exponential;
- ein Design oder Theme, dessen Templates, Stylesheets oder Skripte GPL-Templates, -Stylesheets oder -Skripte von
  Exponential enthalten oder daraus entstanden sind, etwa Overrides der Standard-Templates oder ein mitgeliefertes
  Design, das kopiert und verändert wurde.

Verbreiten heißt, jemand anderem eine Kopie zu übergeben, in jeder Form: als Archiv, als Git-Repository, als Kopie
auf dem Server des Empfängers, als Download aus einem Theme-Shop. Typische Fälle:

- Eine Agentur liefert eine fertige Site mit Design und Erweiterungen an ihren Kunden, der sie selbst betreibt oder
  von einem anderen Hoster betreiben lässt.
- Ein Designer verkauft ein Theme oder verschenkt es.
- Ein Unternehmen veröffentlicht eine Erweiterung oder ein Site-Paket oder verkauft es.

Was nach der GPL Version 2 **keine** Verbreitung ist:

- Exponential für sich selbst zu installieren und zu betreiben, verändert oder unverändert. Die GPL schränkt das
  Ausführen des Programms nicht ein (§0: andere Handlungen als Kopieren, Verbreiten und Verändern deckt die Lizenz
  nicht ab);
- Kopien, die innerhalb Ihrer eigenen Organisation angefertigt und genutzt werden;
- eine mit Exponential gebaute Website ihren Besuchern anzubieten. Der PHP-Code läuft auf Ihrem Server; die Besucher
  erhalten seine Ausgabe, nicht das Programm. Weder die GPL Version 2 noch die Version 3 enthält eine Klausel zur
  Nutzung über ein Netzwerk. Die GNU Affero GPL hat eine (ihr §13), aber Exponential steht nicht unter der AGPL; nur
  Code unter der AGPL, den Sie einer Installation hinzufügen, brächte diese Pflicht mit sich.

Ein genauer Punkt: Die JavaScript- und Stylesheet-Dateien, die eine Site an den Browser sendet, sind Kopien dieser
Dateien, und die Free Software Foundation sieht das Senden von GPL-JavaScript an Besucher als Übertragung an sie.
Lassen Sie die Lizenzvermerke in solchen Dateien stehen und machen Sie ihren nicht minifizierten Quellcode
zugänglich, etwa mit einem Link auf das öffentliche Repository, aus dem er stammt.

### Was Sie schulden, wenn Sie verbreiten

| Pflicht | GPL Version 2 | GPL Version 3 |
|---|---|---|
| Jedem Empfänger mit dem Programm eine Kopie der Lizenz geben | §1 | §4 |
| Die Urheberrechtsvermerke, die Lizenzvermerke und den Hinweis auf den Gewährleistungsausschluss unverändert lassen | §1 | §4, §5 |
| Geänderte Dateien mit einem deutlichen Hinweis versehen, dass und wann Sie sie geändert haben | §2(a) | §5(a) |
| Das ganze auf Exponential beruhende Werk jedem Dritten unentgeltlich unter der GPL lizenzieren | §2(b) | §5(c), §10 |
| Den vollständigen zugehörigen Quellcode bereitstellen | §3 | §6 |
| Den Empfängern keine weiteren Beschränkungen ihrer Rechte auferlegen | §6 | §10, §7 |

Der Quellcode ist nach der Lizenz „the preferred form of the work for making modifications to it“, die bevorzugte
Form des Werks, um es zu verändern; zum vollständigen Quellcode gehören auch die Skripte, die Kompilierung und
Installation steuern (§3). Für eine Site oder ein Design bedeutet das:

- die Templates und den PHP-Code der Erweiterungen;
- das ursprüngliche, nicht minifizierte CSS und JavaScript und die Dateien, aus denen es gebaut wird (Sass, Less,
  TypeScript und Ähnliches): Ein minifiziertes Bundle oder ein kompiliertes Stylesheet ist kein Quellcode;
- die Build-Konfiguration, die nötig ist, um die gelieferten Dateien aus diesen Quellen zu erzeugen (`package.json`,
  die Konfiguration des Bundlers, ein `Makefile`, Build-Skripte);
- das bearbeitbare Original eines Bildes, das Sie aus einem GPL-Bild gemacht haben.

Nach §3 stellen Sie ihn auf eine dieser Arten bereit:

- **mit der Lieferung** (§3(a)): Der Quellcode geht mit der Kopie, bei einem Webprojekt meist als Repository oder als
  Archiv davon;
- **mit einem schriftlichen Angebot** (§3(b)), mindestens drei Jahre gültig, **jedem Dritten** den vollständigen
  Quellcode für höchstens die Kosten der physischen Kopie zu geben;
- **durch Weitergabe eines Angebots**, das Sie selbst erhalten haben (§3(c)), nur bei nichtkommerzieller Verbreitung
  erlaubt.

Zwei weitere Regeln vervollständigen das Bild:

- **Sie dürfen Geld verlangen.** Für den physischen Vorgang der Übertragung einer Kopie dürfen Sie eine Gebühr
  verlangen und gegen Entgelt eine Gewährleistung anbieten (§1). Einen Preis für ein Theme, eine Site, die
  Installation, den Support oder das Hosting zu nehmen, ist erlaubt. Die Freiheiten dürfen Sie nicht nehmen: Der
  Empfänger erhält die Lizenz selbst unentgeltlich (§2(b)) und darf das Werk ändern, weitergeben, verkaufen oder
  verschenken.
- **Keine weiteren Beschränkungen.** Sie dürfen den Empfängern keine weiteren Beschränkungen bei der Ausübung der
  ihnen gewährten Rechte auferlegen (§6). Hindert Sie ein Vertrag, eine Geheimhaltungsvereinbarung oder eine andere
  Verpflichtung daran, die Bedingungen der GPL zu erfüllen, dürfen Sie das Werk gar nicht verbreiten (§7). Nach der
  GPL Version 3 darf ein Empfänger eine weitere Beschränkung entfernen, die mit dem Werk kommt (§7).

Teile, die eigenständige Werke sind und nur daneben mitgeliefert werden, etwa Fotos, Illustrationen und Schriften,
die die Agentur selbst gemacht oder eigens lizenziert hat, dürfen ihre eigenen Lizenzen behalten, weil die bloße
Zusammenstellung („mere aggregation“) mit einem GPL-Werk sie nicht unter die GPL bringt (§2, letzter Absatz). Sagen
Sie, welche Teile das sind und unter welchen Bedingungen sie kommen.

### Häufige Verstöße

- Eine Site ohne den Quellcode ihres Designs oder ihrer eigens entwickelten Erweiterungen liefern.
- Nur minifizierte oder kompilierte Assets liefern, ohne die Dateien und Build-Skripte, aus denen sie entstanden.
- Ohne den Lizenztext liefern, oder mit einem schriftlichen Angebot, das nie eingelöst wird.
- Die Urheberrechts- und Lizenzvermerke von Exponential oder eines Templates, auf dem ein Design aufbaut, entfernen
  oder ersetzen. Einen eigenen Urheberrechtsvermerk für die eigenen Änderungen hinzuzufügen ist in Ordnung; die
  anderen zu entfernen nicht.
- Verträge oder Bedingungen, die dem Kunden verbieten, den Code weiterzugeben, zu ändern, wiederzuverwenden, zu
  veröffentlichen oder einen anderen Dienstleister daran arbeiten zu lassen.
- Ein Design als proprietär bezeichnen oder unter einer „Einzelplatzlizenz“ verkaufen, obwohl es von GPL-Templates,
  -Stylesheets oder -Skripten abgeleitet ist.
- Eine Lizenzgebühr für die GPL-Teile verlangen oder ihre Nutzung von einem Lizenzschlüssel abhängig machen.

Die Folge nach der GPL Version 2: Die Rechte des Verbreiters aus der Lizenz erlöschen automatisch (§4: jeder andere
Versuch, das Programm zu kopieren, zu verändern, zu unterlizenzieren oder zu verbreiten, ist nichtig und beendet
automatisch Ihre Rechte aus dieser Lizenz); wer bereits Kopien von ihm erhalten hat, behält seine eigene Lizenz.
Verbreiten ohne Lizenz ist eine Urheberrechtsverletzung, die die Rechteinhaber vor Gericht bringen können; deutsche
Gerichte setzen die GPL durch, seit das Landgericht München I am 19. Mai 2004 so entschieden hat (21 O 6123/04). Wer
als Verbreiter der GPL Version 3 folgt, erhält die Lizenz zurück, indem er den Verstoß beendet: vorläufig sofort, und
dauerhaft, wenn der Rechteinhaber ihn nicht innerhalb von 60 Tagen nach dem Ende darauf hinweist, oder wenn es der
erste Hinweis dieses Rechteinhabers ist und der Verstoß innerhalb von 30 Tagen nach dessen Erhalt behoben wird (§8).

### Zurückgeben: worum das Projekt bittet und was die GPL nicht verlangt

Die GPL verpflichtet **niemanden**, seine Änderungen, Erweiterungen oder Designs an das Projekt Exponential zu geben.
Sie verpflichtet den, der sie verbreitet, den Quellcode und die Lizenz **denen zu geben, an die er sie verbreitet**.
Ein Design nicht an das Projekt zurückzugeben, ist deshalb keine Verletzung der Lizenz.

Hat der Kunde das Design unter der GPL erhalten, hat er alle Rechte, die die GPL gibt: es zu nutzen, zu ändern, von
jedem ändern zu lassen und mit jedem zu teilen, auch mit dem Projekt.

Das Projekt bittet Agenturen und Designer, ihre Designs, Erweiterungen und Korrekturen zurückzugeben, und die
Community erwartet es: So bleibt freie Software gesund, und jede mit Exponential gebaute Site steht auf Arbeit, die
andere vorher gegeben haben. Es ist eine Bitte und eine Erwartung der Community, keine rechtliche Pflicht. Wie es
geht: [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md) und
[Zum Projekt beitragen](zum-projekt-beitragen.md).

### Was Kunden von ihrer Agentur verlangen können

- Den Lizenztext und die Urheberrechtsvermerke dessen, was geliefert wurde.
- Den vollständigen Quellcode von allem, was unter der GPL geliefert wurde: die Erweiterungen, das Design mit seinen
  Templates, den nicht minifizierten Stylesheets und Skripten und ihren Build-Skripten, das Site-Paket, am besten als
  Git-Repository.
- Einen Vertrag, der die Rechte aus der GPL bestätigt und keine Klausel enthält, die Änderungen, Weitergabe,
  Wiederverwendung oder die Wahl eines anderen Dienstleisters verbietet. Klauseln, auf die Sie achten und die Sie
  verlangen sollten:
  [Zu vermeidende Lock-in-Klauseln](../verwaltung/beschaffung/ausstiegsplaene/zu-vermeidende-lock-in-klauseln.md) und die
  [Klausel zur Open-Source-Lizenz und zum Quellcode](../verwaltung/beschaffung/ausschreibungsvorlagen/klausel-open-source-lizenz-und-quellcode.md).
- Eine Liste der Teile unter anderen Lizenzen (Fotos, Schriften, kostenpflichtige Bibliotheken) und was deren
  Bedingungen erlauben.

Der Vertrag darf dem Kunden mehr geben als die GPL, etwa das Urheberrecht an bezahlter Arbeit; weniger darf er nie
geben.

### Was Sie bei einem Verstoß tun

1. **Zuerst fragen.** Die meisten Verstöße sind Versehen. Schreiben Sie der Agentur oder dem Anbieter, nennen Sie, was
   fehlt (die Lizenz, der Quellcode, eine Klausel, die der GPL widerspricht), verweisen Sie auf die Abschnitte oben und
   setzen Sie eine angemessene Frist.
2. **Die Rechteinhaber können die Lizenz durchsetzen.** Für den Kernel sind das 7x und die Exponential Foundation,
   für den übernommenen Code eZ Systems AS (`COPYRIGHT.md`); für eine Erweiterung oder ein Design deren Autoren.
   Teilen Sie ihnen mit, was Sie gefunden haben: für Exponential an 7x unter info@se7enx.com. Ein Kunde, der nicht
   erhalten hat, was versprochen war, kann außerdem vertragliche Ansprüche haben; das ist eine Frage für seinen
   Anwalt.
3. **Lesen Sie die Leitfäden zur Einhaltung der GPL.**
   - Free Software Foundation: [Violations of the GNU licenses](https://www.gnu.org/licenses/gpl-violation.html)
     und die [Frequently asked questions about the GNU licenses](https://www.gnu.org/licenses/gpl-faq.html).
   - Software Freedom Conservancy: [Copyleft compliance](https://sfconservancy.org/copyleft-compliance/), die
     [Principles of community-oriented GPL enforcement](https://sfconservancy.org/copyleft-compliance/principles.html)
     und [Copyleft and the GNU General Public License: a comprehensive tutorial and guide](https://copyleft.org/guide/).

## Probleme und Fragen

### Ich habe eine Erweiterung gefunden, die „nur GPL 2“, „nur GPL 3“ oder eine andere Lizenz angibt. Was soll ich tun?

Die Website bittet Sie, zuerst ein Issue im Tracker dieses Projekts zu eröffnen und, falls der Maintainer nicht
antwortet, es im [Issue-Tracker von Exponential](https://github.com/se7enxweb/exponential/issues) zu melden. Bevor
Sie Code aus einer solchen Erweiterung in Ihren eigenen übernehmen, lesen Sie
[Lizenzen lesen, bevor Sie etwas wiederverwenden](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md).

### Welche Lizenz hat die Dokumentation?

Diese Hilfe und die Dokumentation in `doc/` stehen unter der GNU Free Documentation License 1.3 oder später
([Die Lizenz dieser Dokumentation](../../README-LIZENZ.md), [Freie Dokumentation](../freie-dokumentation.md)). Die
Lizenz-FAQ der Website sagt, ihre eigenen Inhalte stünden unter Creative Commons Attribution-ShareAlike 2.0, während
die Fußzeile von exponential.earth „All rights reserved“ lautet; solange beides nicht übereinstimmt, verlinken Sie auf
die Website, statt sie zu kopieren.

### Ich habe eine andere Frage.

Stellen Sie allgemeine Fragen in den [Foren von Share Exponential](https://share.exponential.earth/forums). Für Ihren
eigenen Fall fragen Sie einen Anwalt: Das Projekt leistet keine Rechtsberatung.

## Verwandte Seiten

- [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md)
- [Lizenzen lesen, bevor Sie etwas wiederverwenden](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md)
- [Exponential: Häufige Fragen](faq.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt
  und verbessert werden.
