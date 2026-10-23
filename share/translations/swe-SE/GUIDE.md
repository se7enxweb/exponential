# Swedish (swe-SE): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Swedish of Sweden, in the plain, friendly style of Swedish software. No other locale reads this
catalogue. Use established Swedish IT terms; where Swedish IT keeps the English word (cache, token, prompt, plugin,
worker, layout, design), keep it and inflect it the Swedish way (cachen, cachar).

## 2. Form of address

The user is **du** (dig, din, ditt, dina), never Ni or Er. "du" is written small inside a sentence. Write dig and
mig, not the spoken dej and mej.

- **Buttons, menu items, links and short actions** use the imperative: Spara, Ta bort, Redigera, Avbryt,
  Tillämpa, Publicera, Ladda upp, Markera allt, Kasta utkast, Lägg till i bokmärken.
- **Titles, column headings and tabs** are nouns or noun phrases.
- **Sentences, hints, questions and error messages** use the imperative or a du sentence:
  - Är du säker på att du vill ta bort de här objekten? (Are you sure you want to remove these items?)
  - Välj en plats (Please choose a location)
  - Välj den fil du vill ladda upp. (Select the file that you want to upload.)
- "Please" is left out; the imperative alone is polite in Swedish ("Please choose a location" = "Välj en plats").
  Not "Var god" or "Vänligen".
- Success messages are short: "Utkastet sparades."

## 3. Script and typography

- Sentence case: only the first word of a label is capitalised, unless the source has a name.
- Compound words are written together (innehållsklass, användargrupp, webbplatsingång). With a code, abbreviation
  or placeholder, join with a hyphen: URL-alias, PHP-fil, '%class'-objektet.
- A dash in running text is a spaced en dash ( – ).
- Quotation marks: "" or ” ” as in the source.
- Numbers: digits 0–9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Swedish | Note |
|---|---|---|
| content | innehåll | |
| content object / object | innehållsobjekt / objekt | |
| node | nod | noden, noder |
| location | plats | main location = huvudplats |
| content class / class | innehållsklass / klass | class group = klassgrupp |
| attribute | attribut | |
| datatype | datatyp | |
| version | version | |
| draft | utkast | My drafts = Mina utkast |
| publish / published | publicera / publicerad | |
| translation / language | översättning / språk | main language = huvudspråk |
| siteaccess | webbplatsingång | plural webbplatsingångar |
| site | webbplats | |
| design | design | |
| section | sektion | the Exponential section only; never avsnitt |
| object state / state group | objekttillstånd / tillståndsgrupp | |
| role / policy | roll / policy | plural policyer |
| permission / access | behörighet / åtkomst | Access denied = Åtkomst nekad |
| limitation | begränsning | |
| user / user group | användare / användargrupp | anonymous user = anonym användare |
| My account | Mitt konto | |
| log in / log out | logga in / logga ut | login (noun) = inloggning |
| password / username | lösenord / användarnamn | |
| e-mail | e-post | |
| cache / clear cache | cache / töm cachen | |
| template | mall | |
| override | åsidosättning | template overrides = mallåsidosättningar |
| fallback | reserv | fallback language = reservspråk |
| workflow / workflow event | arbetsflöde / arbetsflödeshändelse | |
| trigger | utlösare | |
| trash / empty trash | papperskorg / töm papperskorgen | |
| bookmark | bokmärke | |
| settings | inställningar | |
| Setup (admin tab) | Konfiguration | |
| Dashboard | Instrumentpanel | |
| Content structure / Media library | Innehållsstruktur / Mediabibliotek | |
| layout / block / zone / slot | layout / block / zon / plats | to block a user = blockera |
| extension / plugin | tillägg / plugin | |
| package | paket | |
| module / view / function | modul / vy / funktion | View (button) = Visa |
| search / search engine | sök / sökmotor | |
| search index / index / reindex | sökindex / index, indexera / indexera om | |
| log / log file | logg / loggfil | |
| audit / audit trail | granskning / granskningslogg | |
| cronjob | cronjobb | |
| job / queue / worker | jobb / kö / worker | |
| preview | förhandsgranska (button) / förhandsvisning | |
| upload / download | ladda upp / ladda ner | |
| export / import | exportera / importera | |
| remove / delete | ta bort / ta bort | see section 5 |
| edit / save / store | redigera / spara / spara | "Store" is the save button; only the shop tab of the header is Butik |
| discard | kasta | Discard draft = Kasta utkast |
| apply / cancel / OK | tillämpa / avbryt / OK | |
| back / next / previous | tillbaka / nästa / föregående | |
| yes / no | ja / nej | |
| add / create / new | lägg till / skapa / ny | |
| move / copy | flytta / kopiera | |
| close / update, refresh | stäng / uppdatera | |
| reset / restore | återställ / återställ | to zero a counter = nollställ |
| select / select all | välj, markera / markera allt | |
| enable / disable | aktivera / inaktivera | enabled = aktiverad, disabled = inaktiverad |
| visible / hidden | synlig / dold | hide = dölj, reveal = visa |
| archive | arkiv, arkivera | |
| order (shop) / order (sorting) | beställning / ordning | |
| sort / ascending / descending | sortera / stigande / fallande | |
| tag / custom tag | tagg / anpassad tagg | for keyword tags and HTML tags alike |
| keyword | nyckelord | |
| owner / creator | ägare / skapare | |
| created / modified | skapad / modifierad | in a sentence also ändrad |
| relation / related objects | relation / relaterade objekt | reverse related = omvänt relaterade |
| parent / children / sub-items | förälder / underordnade / underobjekt | |
| subtree | underträd | |
| ID / identifier / remote ID | ID / identifierare / fjärr-ID | |
| URL alias / URL wildcard | URL-alias / URL med jokertecken | |
| priority / default / required | prioritet / standard / obligatorisk | in a sentence: krävs |
| status / state | status / tillstånd | |
| warning / error | varning / fel | |
| notification | avisering | |
| collaboration | samarbete | |
| information collection / collected information | informationsinsamling / insamlad information | |
| file / folder / directory | fil / mapp / katalog | |
| image / media | bild / media | |
| thumbnail | miniatyr | |
| path / browse | sökväg / bläddra | |
| database | databas | |
| statistics / system information | statistik / systeminformation | |
| install / uninstall | installera / avinstallera | |
| shop / basket / customer / product | butik / kundvagn / kund / produkt | |
| VAT / discount / price / currency | moms / rabatt / pris / valuta | |
| wish list | önskelista | |
| newsletter / subscription | nyhetsbrev / prenumeration | |
| session | session | |
| field | fält | |
| format | format | |
| feed | flöde | |
| tab | flik | |
| theme | tema | |
| menu / toolbar | meny / verktygsfält | |
| name / title / description / type / value / date | namn / titel / beskrivning / typ / värde / datum | |
| AI / model / prompt / token | AI / modell / prompt / token | |
| embedding / chunk / vector | inbäddning / textavsnitt / vektor | |
| provider | leverantör | |
| deploy, deployment | driftsätta / driftsättning | |
| mapping | koppling | |
| editor (software) | redigerare | |
| interface language | gränssnittsspråk | |

## 5. Decisions after the translation

| English | Swedish | Note |
|---|---|---|
| Remove / Delete | Ta bort | both buttons say Ta bort, as in Swedish software generally; where the two stand side by side, Delete becomes Radera |
| Workflow | arbetsflöde | singular for a singular source; arbetsflöden only for "Workflows" |
| Override | åsidosättning | not anpassa and not stilmall, which is a stylesheet |
| Descending | fallande | |
| Reset | återställ | the button is an imperative, not återställd |
| Locale | språkinställning | the INI key `Locale` stays |
| Charset | teckenuppsättning | |
| Wildcard | jokertecken | |
| Approve / Approval | godkänn / godkännande | |
| Confirm / Reject | bekräfta / avvisa | |
| Pending | väntar | |
| Container | behållare | |
| Redirect | omdirigera | |
| License | licens | |
| Placement | placering | |
| Overview | översikt | |
| Rich text | formaterad text | |
| Preset | förval | |
| Batch | omgång | |
| Scope / assign | omfång / tilldela | |
| Thread | tråd | |
| Facets | fasetter | |
