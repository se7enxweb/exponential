# Icelandic (isl-IS): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Icelandic as written in Iceland, following the official spelling rules (Ritreglur, Íslensk málnefnd). No
other locale falls back to this catalogue. The tone is neutral, concise and professional, as in the Icelandic
versions of Windows and Microsoft 365. Icelandic prefers native words: use the established Icelandic IT term
(Microsoft terminology, Tölvuorðasafn of Orðanefnd Skýrslutæknifélags Íslands) instead of an English loan, and do
not coin new words where an established one exists. Avoid slang and colloquial loans ("seiva", "deleta", "lógga
inn").

## 2. Form of address

The user is addressed with the informal singular **þú**, as everywhere in Icelandic software; Icelandic has no
formal "you" in current use (the old "þér" sounds archaic and is not used).

- **Buttons, menu items, links and action labels** use the short command form as in Microsoft's Icelandic
  interfaces, i.e. the infinitive without "að": "Vista", "Eyða", "Breyta", "Bæta við", "Hætta við", "Stofna nýtt",
  "Nota breytingar", "Senda til birtingar", "Velja allt", "Vista drög", "Henda drögum", "Tæma ruslafötu". Not the
  imperative with the clitic pronoun on buttons ("Vistaðu", "Eyddu").
- **Titles, column headings and tabs** are nouns or noun phrases: "Efnisskipan", "Notandareikningar",
  "Útlitsgerð", "Tilkynningastillingar", "Ástönd hluta".
- **Sentences, hints and instructions** address the user with **þú** (lower case: þú, þig, þér, þín, þinn) and the
  imperative with the clitic pronoun:
  - "Veldu staðsetningu" (Please choose a location)
  - "Sláðu inn gilt netfang."
  - "Smelltu á „Hætta við“ og reyndu að fjarlægja aðeins þær staðsetningar sem þú mátt fjarlægja."
  - "Þú getur breytt stillingum útgáfusögu í content.ini ..."
  - "Athugaðu tenginguna og reyndu aftur."
- **Questions and confirmations**: "Viltu örugglega …?": "Viltu örugglega fjarlægja þennan klasa?", "Viltu
  örugglega fjarlægja tungumálið?".
- **Error messages** state the fact, addressing the user where needed: "Þú hefur ekki heimild til að fjarlægja
  klasa.", "Skráin fannst ekki.", "Ekki tókst að vista drögin."
- **Tooltips that describe a click**: "Smelltu á táknið til að birta flýtivalmyndina."
- **Status messages** are passive or impersonal: "Tilkynningin var fjarlægð.", "Breytingarnar voru vistaðar.",
  "Engin cron-keyrsla fannst."
- "Please" is not translated with "vinsamlegast" in every message; use it only where the source stresses a request,
  otherwise the plain imperative ("Veldu …") is polite enough.
- Not allowed: "þér/yður" as a formal address, "Þú/Þín" with a capital letter inside a sentence, the imperative
  with clitic ("Vistaðu") on buttons, the bare infinitive as an instruction inside a sentence ("Velja staðsetningu
  áður en …" is wrong; write "Veldu staðsetningu áður en …"), and "maður" (one) where the user is meant.

## 3. Script and typography

- Latin script with the full Icelandic alphabet (á ð é í ó ú ý þ æ ö). Never replace ð/þ with d/th, never drop
  accents.
- **Sentence case**: only the first word and proper names are capitalized: "Efnisskipan", "Ástönd hluta",
  "Stillingar vefaðgangs". Names of languages, nationalities, weekdays and months are lower case ("enska",
  "mánudagur", "janúar").
- **Quotation marks** in running text: „…“ (U+201E, U+201C): „%name“. Straight quotes stay inside HTML attributes,
  JavaScript and where the source quotes code or a value typed exactly.
- **No space** before ":", ";", "?" and "!". Abbreviations take a full stop: "t.d.", "o.s.frv.", "þ.e.".
- **Compounds with a code or an English name** take a hyphen: "cron-verk", "Solr-kjarni", "HTML-ívafsmerki",
  "PHP-klasi", "Alt-texti" (but "vefslóðarsamnefni" in running text).
- **Plural and number forms**: Icelandic uses the singular after numbers ending in 1 except 11 ("21 hlutur",
  "11 hlutir"), the source only "item(s)". Never write "hlutur/hlutir" or "hlut(ir)". Use a label form: "Hlutir:
  %count", "Vinnsluferli: %count", "Drög: %count".
- **Placeholders and case endings**: Icelandic nouns inflect, a placeholder cannot. Never attach an ending to a
  placeholder (no "%namear", no "%name-s"). Put the placeholder in apposition after an inflected head noun, or use a
  label construction:
  - "Breyta klasanum <%class_name>.", "Klasinn „%classname“ er þegar til.", "Velja innsendingar hlutarins %name til
    að fjarlægja"
  - "Hlutir: %count", "Tungumál: %locales".
  - Genitive phrases are turned around: not "%name útgáfa" but the inflected head noun first, followed by the
    placeholder ("… hlutarins %name"), or a label form.
- **Numbers**: decimal comma and a full stop (or non-breaking space) as thousands separator, as the locale
  `isl-IS` formats them: "1.234,5". Percent with a space: "%rate %". Dates "23.10.2026".
- **Time units** after a number: "sek.", "mín.", "klst.", "dagar"; "fyrir %n mín.". A placeholder is never
  followed by an inflected unit ("%days dagar", "%count daga", "%1 mínútur"): use the abbreviated, case-neutral
  unit ("%count mín.", "%count klst.") or the label form ("Dagar: %days"). For "%1 ago" / "in %1" the dative forms
  of the relative-time strings stay ("fyrir %days dögum").

## 4. Glossary

| English | Icelandic | Note |
|---|---|---|
| content | efni | |
| content object / object | efnishlutur / hlutur | gen. hlutar; pl. hlutir; "staðsetning" only where the source says location, "hnútur" for node |
| item (list entry, general) | atriði | items: atriði |
| node | hnútur | main node: aðalhnútur |
| location / main location | staðsetning / aðalstaðsetning | |
| content class / class | efnisklasi / klasi | a PHP class: PHP-klasi; not "flokkur" (that is category; VAT type: VSK-flokkur); the verb "flokka" (to file, to group) is right |
| class group | klasahópur | |
| attribute | eigind | pl. eigindir |
| datatype | gagnagerð | |
| version | útgáfa | version history: útgáfusaga |
| draft | drög | neuter plural: "Vista drög", "Henda drögum" |
| publish / published / publishing | birta / birt / birting | Send for publishing: Senda til birtingar |
| translation / language | þýðing / tungumál | |
| main language / content language | aðaltungumál / efnistungumál | interface language: tungumál viðmóts |
| fallback (language) | varatungumál | fallback chain: varakeðja; fallback in general: varaúrræði |
| siteaccess | vefaðgangur | pl. vefaðgangar, dat. vefaðgöngum |
| site / website | vefsvæði / vefur | |
| design (Exponential design) | hönnun | the set of templates and styles; not "útlit" |
| structure | skipan | Content structure: Efnisskipan |
| template | sniðmát | template editor: sniðmátsritill |
| override / overridden / override template | hnekking / hnekkt / hnekkingarsniðmát | verb: hnekkja (Microsoft) |
| alias / URL alias | samnefni / vefslóðarsamnefni | |
| wildcard (URL) | algildisvefslóð / algildisstafur | the rule: algildisvefslóð; the * character: algildisstafur; "wildcard URL alias": algildissamnefni |
| redirect / forward (URL) | endurbeina / áframsenda | noun: endurbeining |
| cache / clear cache | skyndiminni / hreinsa skyndiminni | |
| workflow | verkflæði | |
| workflow event / trigger | verkflæðisatburður / kveikja | event: atburður |
| role | hlutverk | |
| policy | stefna | pl. stefnur; not "regla" (that is rule) |
| permission / access denied | heimild / aðgangi hafnað | access: aðgangur |
| limitation (policy) | takmörkun | |
| user / user group / user account | notandi / notendahópur / notandareikningur | |
| anonymous user | nafnlaus notandi | |
| administrator / admin interface | kerfisstjóri / stjórnborð kerfis | administration: stjórnun |
| log in / log out | skrá inn / skrá út | "Skráðu þig inn" in sentences |
| login (field) / username / password | notandanafn / notandanafn / aðgangsorð | Microsoft uses aðgangsorð; not "lykilorð" (clashes with keywords) |
| e-mail | tölvupóstur | e-mail address: netfang |
| section | hluti | the Exponential section; keep apart from hlutur (object), from an INI section (bálkur) and from a page section (kafli) |
| object state / state group | ástand hlutar / ástandshópur | state alone: ástand, pl. ástönd; never "staða" |
| status | staða | also the state of a package, an installation or a link check |
| relation / related objects | tengsl / tengdir hlutir | reverse related: hlutir sem vísa á þennan hlut |
| trash | ruslafata | Empty trash: Tæma ruslafötu |
| bookmark | bókamerki | |
| sub-items / children / parent | undiratriði / undirhlutir / yfirhlutur | |
| subtree | undirtré | |
| owner / creator / modifier | eigandi / höfundur / breytandi | |
| created / modified | stofnað / breytt | column headings |
| layout (Exponential Layouts) | útlit | layout type: útlitsgerð; the name Exponential Layouts stays |
| layout (directory or release structure) | skipulag | |
| block / zone | bálkur / svæði | an INI section is also bálkur |
| setting / settings | stilling / stillingar | |
| Setup (admin tab) | Uppsetning | |
| configuration / configure | grunnstillingar / stilla | |
| Content structure | Efnisskipan | |
| Media library | Miðlasafn | |
| Dashboard | Mælaborð | |
| extension | viðbót | |
| plugin | tengiforrit | |
| package | pakki | |
| module / view / function | eining / sýn / aðgerð | "Module view": sýn einingar; the button "View": Skoða |
| search | leita / leit | search engine: leitarvél |
| search index / reindex / index (verb) | leitarskrá / endurskrá / skrá í leitarskrá | reindexing: endurskráning |
| index (database) | vísir | |
| index file | upphafsskrá | index.php stays as is |
| cronjob | cron-verk | |
| schedule | áætlun | scheduled: áætlað |
| job / queue / worker | verk / biðröð / vinnsluferli | job queue: verkbiðröð |
| lane (queue) | braut | |
| log / log file | annáll / annálsskrá | never "log in" |
| rotation / rotate (log) | annálaskipti / skipta um annál | rotated log: eldri annáll |
| audit / audit trail | úttekt / úttektarslóð | |
| notification | tilkynning | |
| collaboration | samvinna | |
| preview | forskoðun | |
| ID / remote ID / identifier | kenni / fjarkenni / auðkenni | object ID: kenni hlutar |
| file / folder / directory | skrá / mappa / mappa | |
| image / media | mynd / miðlar | |
| thumbnail | smámynd | |
| upload / download | hlaða upp / sækja | |
| shop / basket | verslun / karfa | |
| order (shop) | pöntun | order status: staða pöntunar |
| order (sorting) | röð | sort order: röðun; "in order to" is not a noun |
| customer / product | viðskiptavinur / vara | |
| checkout | greiðsluferli | |
| VAT / VAT type | VSK / VSK-flokkur | |
| discount / discount rule | afsláttur / afsláttarregla | |
| rule | regla | |
| wish list | óskalisti | |
| currency / price | gjaldmiðill / verð | |
| payment gateway / gateway | greiðslugátt / gátt | |
| newsletter / subscriber | fréttabréf / áskrifandi | |
| subscription / subscribe | áskrift / gerast áskrifandi | unsubscribe: segja upp áskrift |
| tag (keyword, eztags) / keyword (tag) / keyword (search query) | merki / efnisorð / leitarorð | |
| tag (HTML/XML) / custom tag | ívafsmerki / sérsniðið ívafsmerki | |
| export / import | flytja út / flytja inn | noun: útflutningur / innflutningur |
| move / copy / swap | færa / afrita / víxla | |
| embedding (vector) / chunk / vector | vigurgreyping / bútur / vigur | never "innfelling" for a vector embedding |
| embed / embedded (object in text) | fella inn / innfelldur | noun: innfelling |
| prompt / model / token | kvaðning / líkan / tóki | system prompt: kerfiskvaðning |
| AI | gervigreind | |
| deploy / deployment | dreifa / dreifing | |
| release (deployment) | útgáfupakki | keep apart from version (útgáfa) |
| enable / disable | virkja / gera óvirkt | "slökkva á" only for a switch or option (on/off) |
| enabled / disabled / active / inactive | virkt / óvirkt / virkur / óvirkur | agree in gender with the noun |
| warning / error / notice | viðvörun / villa / ábending | |
| required / optional | áskilið / valfrjálst | |
| default / priority | sjálfgefið / forgangur | |
| sort / ascending / descending | raða / hækkandi / lækkandi | Asc. / Desc.: Hækk. / Lækk. |
| visible / hidden | sýnilegt / falið | |
| hide / show / reveal | fela / sýna / sýna | not "birta" on a show button (that is publish) |
| expand / collapse | víkka út / fella saman | |
| OK / Cancel / Yes / No | Í lagi / Hætta við / Já / Nei | |
| Back / Next / Previous / Close | Til baka / Áfram / Fyrri / Loka | Next page: Næsta síða |
| select / browse | velja / fletta | |
| Select all / Deselect all / Select none | Velja allt / Afvelja allt / Velja ekkert | |
| apply | nota | Apply changes: Nota breytingar |
| store / save | vista | Store (shop tab of the header): Verslun; a store as a place: safn (Explanation store: Skýringasafn) |
| discard | henda | Discard draft: Henda drögum |
| edit | breyta | |
| view (button) | skoða | |
| remove | fjarlægja | take out of a list, group or place |
| delete | eyða | destroy; takes the dative: "eyða hlutnum" |
| new / create / add | nýr / stofna / bæta við | Create new: Stofna nýtt; see section 5 |
| refresh / update / reset | endurnýja / uppfæra / endurstilla | |
| restore | endurheimta | |
| assign / assignment | úthluta / úthlutun | |
| run (verb) / run (noun) | keyra / keyrsla | |
| dry run / self-test | prufukeyrsla / sjálfsprófun | |
| debug | villuleit | debug output: villuleitarúttak |
| statistics / system information | tölfræði / kerfisupplýsingar | |
| database | gagnagrunnur | |
| browse / path | fletta / slóð | |
| handler / wizard | meðhöndlari / leiðsagnarforrit | |
| backend / provider / endpoint | bakendi / veita / endapunktur | a hosting company: hýsingaraðili |
| method / listener / controller | aðferð / hlustari / stýring | |
| allowlist / read-only / argument | leyfislisti / skrifvarið / færibreyta | |
| slot / collection | rauf / samsafn | pl. samsöfn (Layouts block, MongoDB, cache); for the information collector see below |
| facets / elevate (search) | leitarþættir / lyfta í röðun | |
| filter | sía | verb: sía |
| tick (checkbox) | haka við | untick: taka hakið af |
| container (content class, "Is container") | ílát | "Er ílát" |
| container (Bootstrap/CSS) | umgjörð | the CSS class name stays |
| locale | svæðisstillingar | the INI key Locale and the placeholder %locale stay |
| localization | staðfærsla | |
| approve / approval / approved / approver | samþykkja / samþykki / samþykkt / samþykkjandi | |
| deny / reject / rejected | neita / hafna / hafnað | |
| accept / accepted | samþykkja / samþykkt | |
| pending | í bið | pending list: biðlisti |
| charset | stafatafla | |
| license | leyfi | licence (of content): notkunarleyfi; the file name LICENSE stays |
| privilege (database) / grant | réttindi / veita | |
| format / formatting | snið / sniðmótun | file format: skráarsnið |
| linked / common | tengdur, tengt / sameiginlegt | |
| flags (class flags) / flag (language icon) | flagg, pl. flögg / fáni, pl. fánar | |
| teaser | kynningartexti | |
| slug | slóðarheiti | |
| placeholder | staðgengill | |
| preset | forstilling | |
| information collection / collector / collection (one submitted form) | upplýsingasöfnun / upplýsingasafnari / innsending | "innsending" is used for the information collector only |
| overview / summary | yfirlit / samantekt | |
| tab | flipi | |
| rich text | sniðinn texti | |
| feed | straumur | RSS feed: RSS-straumur; feed reader: straumlesari; podcast: hlaðvarp |
| session | seta | |
| scope | umfang | |
| thread / chain | þráður / keðja | |
| pool (of processes or threads) | ferlahópur | |
| batch | lota | |
| schema / registry / manifest | skema / skrásetning / lýsiskrá | |
| stylesheet / theme / icon | stílsnið / þema / tákn | |
| port | gátt | |
| digest (newsletter) | samantektarpóstur | |
| profile | prófíll | user profile: notandaprófíll |
| core (Solr) | Solr-kjarni | |
| kernel | kerfiskjarni | |
| editor (person) / editor (software) | ritstjóri / ritill | |
| Editor (siteaccess type) | Ritstjórn | |
| user interface | notendaviðmót | |
| page / pages | síða / síður | |
| link | tengill | |
| title / name / description | titill / heiti / lýsing | a person's name: nafn |
| date / time | dagsetning / tími | |
| type | gerð | |
| group / list | hópur / listi | |
| value / field | gildi / reitur | |
| number / count | tala / fjöldi | |
| width / height / size | breidd / hæð / stærð | |
| server / client | þjónn / biðlari | |
| request / response | beiðni / svar | |
| task | verkefni | |
| details | nánar | heading: Upplýsingar |
| archive (verb) / unarchive (button) | safnvista / taka úr safni | as in Microsoft (Outlook) |
| archived (version, object, order) | safnvistað | |
| archive (place, menu item "Archive", log archive) | safn | |
| archive (file .tar/.tgz/.zip/.phar/.ezpkg) | safnskrá | |
| repository | geymsla | |
| pattern | mynstur | |
| maintainer | umsjónaraðili | |
| install / uninstall / installation | setja upp / fjarlægja / uppsetning | |
| checksum | gátsumma | |
| quarantine | sóttkví | |
| mapping | vörpun | |
| resolver / resolve (path, name) | leysir / leysa | resolvable: leysanlegt |
| checkpoint | eftirlitspunktur | |
| child documents (Solr) | undirskjöl | |
| script | skrifta | |
| revoke | afturkalla | |
| label | merkimiði | |
| confirmation questions ("Delete …?", "Cancel this …?") | "Viltu örugglega eyða …?", "Viltu hætta við …?" | |

## 5. Decisions after the translation

These decisions take precedence over the glossary in section 4.

### 5.1 Usage of core terms

- **Create**: buttons and link labels use **Stofna** ("Stofna afrit", "Stofna pakka", "Stofna nýtt samnefni"),
  also where a sentence quotes such a button. In running sentences "búa til" is allowed; "Generate", "Make" and
  "Build" labels may keep "Búa til".
- **Show / display** is "sýna" on buttons and toggles; "birta" is publishing there. In running text the
  intransitive "birtast" (appear) and "birta" for showing a page or menu are accepted.
- **Object state** is always "ástand" (pl. "ástönd"); "staða" only for a status or the state of a package, an
  installation or a link check ("Staða uppsetningar").
- **Remove section?** in the package list is a slip in the source for "Remove selection?": "Fjarlægja valið?".
- **Approve and accept** both become "samþykkja"; the coincidence is accepted.
- **Welcome**: "Velkomin(n)". Icelandic has no gender-neutral form; the bracket form is the rule.
- **Gendered states of the user** are rephrased impersonally: "Ekki skráð inn", "Ertu ekki með reikning?".
- When the English term is kept in brackets after the Icelandic one ("(commit)", "(escaping)", "(pagelayout)"),
  the bracket appears once per text, not after every occurrence.

### 5.2 Terms outside the glossary

| English | Icelandic | Note |
|---|---|---|
| git commit / merge commit / last committed | commit (n., indeclinable) / sameiningar-commit / síðast fært inn (commit) | not "innsending" (information collector); a database or Solr commit: staðfesting (commit) |
| global (URL alias) | almennur | "Almenn vefslóðarsamnefni" |
| global (INI scope, override, PHP globals) | víðvær | "víðvær hnekking", "víðværar stillingar"; not "altækur" |
| address (URL in running text) / URL (label) | vistfang / vefslóð | e-mail address: netfang; postal address: heimilisfang |
| destination (URL, node) | áfangastaður | áfangahnútur, áfangaslóð |
| resource (web page, error page) / resource (template, design) / system resources | tilfang / forði / kerfisauðlindir | hönnunarforði, sniðmátsforði |
| subject (e-mail, message) | efni | as in Outlook |
| vendor (package) | útgefandi | |
| validate / validation | sannprófa / sannprófun | keep apart from approve (samþykkja) and confirm (staðfesta) |
| staging / production | forútgáfa / raunumhverfi | "in production" in a sentence: í raunkeyrslu |
| cluster (DFS, server) | þyrping | "klasi" is class; "Skráameðhöndlari þyrpingar"; the verb "klasa" for a clustered index is right |
| template operator / operator | sniðmátsvirki / virki | virki m., pl. virkjar; an operator as a person: stjórnandi |
| fetch function / fetch alias | fetch-fall / fetch-samnefni | pl. fetch-föll |
| cronjob part | verkhluti (cron) | pl. verkhlutar; label "Cron-verkhluti", heading "Cron-verkhlutar" |
| navigation part | leiðsagnarsvæði | not "hluti" (section) |
| alternative text (alt) | Alt-texti | |
| element (XML, DOM, HTML) / element of a URL | stak / liður | stak n., pl. stök; not "eining" |
| engine (search, server) / database engine | vél / gagnagrunnsrekill | leitarvél, sjálfgefna vélin |
| cache hit / miss / hit rate | hitting / geigun / hittihlutfall | not "smellur" (that is a click) |
| alert (audit) | viðvörun | together with warning: úttektarviðvörun |
| sink (audit) | móttakari | pl. móttakarar; not "viðtakandi" |
| budget (AI) | fjárheimild | a token allowance of a model: kvóti |
| transaction (DB) | færsla | |
| markup | ívafsmál | |
| image alias / image variation | myndasamnefni / myndafbrigði | |
| page layout (pagelayout.tpl) | síðuútlit (pagelayout) | not "síðuuppsetning"; a layout of Exponential Layouts stays "útlit" |
| syndication | efnismiðlun | "Really Simple Syndication" stays |
| dependencies (package, extension) | kröfur | "Engar kröfur" |
| secret / client secret (OAuth) | leyndarmál / leyndarmál biðlara | |
| asset (content check) | eign | |
| hostname / host | hýsilheiti / hýsill | |
| interface (PHP) / user interface | skil / viðmót | |
| extends (PHP class) / extends (extension.xml) | erfir frá / víkkar út | |
| compile / compiled templates | þýða / þýdd sniðmát | |
| preload, warm (cache) | forhlaða / forhleðsla | |
| orphan(ed) | munaðarlaus | |
| escape / escaping | með lausnarstöfum / lausnarstöfun | |
| poll / feedback | könnun / endurgjöf | |
| webshop | vefverslun | |
| environment | umhverfi | |
