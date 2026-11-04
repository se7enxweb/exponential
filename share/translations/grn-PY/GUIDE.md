# Guarani (grn-PY): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
November 2026 from the English source, machine-assisted.

## 1. Language and register

Paraguayan Guarani (Avañe'ẽ), the co-official language of Paraguay, in the standard written form of the Academia de
la Lengua Guaraní (Guarani Ñe'ẽ Rerekuapavẽ) and the neutral, plain register of the Guarani texts of the Paraguayan
state, bilingual school books and the Guarani localisations of Mozilla, Google and Wikipedia. No other locale falls
back to `grn-PY`. Spanish (`esl-ES`) gives the loan words where Guarani has none, as everyday Paraguayan Guarani does.

- Guarani words are used where they exist, preferring the terms of the Academia where it has published one:
  moherakuã, ñongatu, mbosako'i, ñembogue, moambue, jeheka, jeporavo, mbojoapy, moheñói, tetepy, marandurenda,
  ñe'ẽñemi, ñanduti veve, puruhára, mba'ekuaarenda, mohendahavusu, ñembohasa.
- Where no Guarani term is known, the established Paraguayan Spanish loan is used in Guarani spelling and stress:
  klase, atrivúto, nódo, módulo, póliza, clave, kolúmna, línea, tavla, licencia, formato, plataforma. Never an
  English word, and never a coined word where the loan is what Paraguayans say. These loans are marked "loan" in the
  glossary.
- Plural with **-kuéra** (after a noun that needs it; often the plural is left to context, as Guarani does), locative
  **-pe/-me**, ablative **-gui**, "for" **peg̃uarã/-pe g̃uarã**, "without" **-'ỹre**: puruhárakuéra, ñongatuhápe,
  marandurendágui, ñe'ẽñemi'ỹre.

## 2. Form of address

The interface addresses the user with **nde** (you, singular), as Guarani software does: "nde mba'ete", "nde
ñe'ẽñemi", verbs in the second person singular **re-/ere-**: "Ikatu remoambue ...", "Rehupytýta ...".

- **Buttons, menu items and links**: the imperative singular in **e-** (**emo-/embo-** for causatives): Eñongatu,
  Embogue, Embosako'i, Emoherakuã, Eheka, Eiporavo, Embojoapy, Emoheñói, Ejevy, Esẽ.
- **Titles, column headings and tabs**: nouns or noun phrases: Ñemboheko, Ñembohasa, Tenda tenondegua.
- **Instructions**: the imperative, softened with **-mi** where a request needs it: "Eha'arõmi ...". A reference
  ("see ...") is "Ehecha ...".
- **Confirmations and questions**: with **-pa**: "Remboguesépa ko pehẽ?", "Añetehápepa ...?". "Are you sure" is
  "Añetehápepa".
- **What the user can or may do**: "Ikatu re...", "Ndaikatúi re...". "You do not have permission" is "Ndaikatúi
  rejapo ..." with the policy in brackets as the source has it.
- **Negation**: the circumfix **nd(a)-...-i**: "Ndaipóri", "Noñemoambuéi", "Ndojejuhúi".
- Not allowed: slash forms.

## 3. Script and typography

- **Latin**, the official Paraguayan Guarani alphabet of the Academia de la Lengua Guaraní, the script of the shipped
  locale (`share/locale/grn-PY.ini`): a, ã, ch, e, ẽ, g, g̃, h, i, ĩ, j, k, l, m, mb, n, nd, ng, nt, ñ, o, õ, p, r,
  rr, s, t, u, ũ, v, y, ỹ and the puso ' (glottal stop).
- **Nasal vowels** take the tilde as precomposed characters (ã ẽ ĩ õ ũ ỹ); g̃ is g followed by the combining tilde
  U+0303, as every Guarani text on the web writes it.
- **Puso**: the straight apostrophe ' (U+0027), as the Academia's own texts and Guarani software write it:
  mba'e, ñe'ẽ, ha'e. Never the modifier letter apostrophe U+02BC or a typographic quote, which keyboards and search
  do not match. Inside an attribute value in quotes the source's apostrophe rules hold.
- **Stress**: an acute accent marks a stressed vowel that is not the last one (puruhára, tetã, mba'ekuaarenda is
  stressed on the last syllable and takes none); a nasal vowel carries no acute.
- **Latin text inside Guarani**: product names, codes and acronyms stay as they are: "PHP", "URL", "HTML", "GitHub
  Pages", "WebDAV".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free. Guarani glues its
  suffixes to the word; never glue a suffix to a placeholder directly ("%namepe" is a different placeholder). Join it
  with a hyphen ("%name-pe", "%site-gui", "%date-pe") or rephrase so that the placeholder stands alone: "Mba'e: %name".
- **Numbers**: the number comes before the noun, which stays in the singular: "%count mba'e", "3 marandurenda". Digits
  0-9.
- **Quotation marks**: the guillemets «...» or straight quotes where the source has its own quotation marks in text;
  where the source has `&quot;` or a straight " that is code or an entity, keep it exactly. The Spanish opening ¿
  and ¡ are not used.
- **Punctuation**: as in the source. A label keeps its colon.

## 4. Glossary

Binding for the kernel and every extension. "Loan" marks an established Paraguayan Spanish loan used because no
Guarani term is known.

| English | Guarani | Note |
|---|---|---|
| content | tetepy | |
| content object | tetepy mba'e | |
| object | mba'e | |
| node | nódo | loan |
| location | tenda | main location: tenda tenondegua |
| item (list entry) | mba'e | |
| class | klase | loan; a PHP class: PHP klase |
| content class | tetepy klase | |
| attribute | atrivúto | loan |
| datatype | mba'ekuaarã ichagua | |
| version | peteĩchagua | |
| draft | jehaipyrã | |
| publish | moherakuã | published: oñemoherakuãva |
| discard | emombo | |
| pending | oha'arõva | |
| siteaccess | siteaccess | the configuration unit; kept as the technical term |
| site, website | tenda, ñanduti renda | |
| section | pehẽ | the Exponential section |
| role | tembiapo | |
| policy | póliza | loan |
| permission | moneĩ | access rights: jeike moneĩ |
| module | módulo | loan |
| function | tembiaporã | |
| user | puruhára | |
| user group | puruhára aty | |
| group | aty | |
| anonymous | héra'ỹva | anonymous user: puruhára héra'ỹva |
| administrator | ñangarekohára | |
| administration (interface) | ñangarekoha | |
| username | puruhára réra | |
| login (name) | téra jeikeha | |
| password | ñe'ẽñemi | |
| log in / sign in | eike | button: Eike |
| log out / sign out | esẽ | button: Esẽ |
| e-mail | ñanduti veve | e-mail address: ñanduti veve kundaharape |
| address | kundaharape | |
| cache | kachíme | loan (from Spanish caché) |
| template | tembiecharã | |
| override | ñemyengovia | override template: tembiecharã ñemyengovia |
| fallback | jerere | fallback chain: jerere rape |
| design | ta'ãngarupi | |
| workflow | tembiapo rape | |
| event | jehu | |
| trash | yty ryru | empty trash: Emopotĩ yty ryru |
| bookmark | techaukaha | |
| settings | ñemboheko | |
| setup | Ñemboheko | the Setup tab |
| layout | ñemohenda / ta'ãngarupi | Exponential Layouts keeps its name |
| block | pehẽ | |
| zone | tenda | |
| extension | jepysokue | |
| package | paquete | loan |
| search | jeheka | button: Eheka |
| log | kuatia | log file: kuatia marandurenda |
| job / task | tembiapo | |
| cronjob | tembiapo aravorã | |
| script | iskrípto | loan |
| worker | mba'apohára | |
| kernel | kernel | kept as the technical term |
| server | mohendahavusu | web server: mohendahavusu ñanduti |
| database | mba'ekuaarenda | |
| folder, directory | ñongatuha | |
| file | marandurenda | |
| image | ta'ãnga | |
| media | ta'ãngamýi | |
| preview | jehecha ypy | |
| upload | embojupi | |
| download | emboguejy | |
| export / import | moguenohẽ / jegueru | |
| remove, delete | embogue | |
| edit | embosako'i | |
| editor (person / program) | mbosako'ihára / mbosako'iha | |
| save / store | eñongatu | |
| Store (shop tab) | Ñemuha | the word the shop menu uses for "Shop" |
| cancel | heja | |
| OK | OK | |
| back | ejevy | |
| enable / disable | embojuruja / emboty | a setting or feature; enabled / disabled: ojurujáva / ombotýva |
| run | emoñepyrũ | a script or job |
| session | jeike | |
| hidden / visible | kañymby / ojehecháva | hide / reveal: emokañy / ehechauka |
| archive | kuatiaryru | |
| sort | emohenda | |
| order (shop) | jerure | |
| shop | ñemuha | |
| basket | ajaka | |
| product | mba'e | |
| price | hepykue | |
| VAT | IVA | loan; the abbreviation used in Paraguay |
| tag (keyword) | techaukaha | an HTML tag: HTML techaukaha |
| translation | ñembohasa | |
| language | ñe'ẽ | main language: ñe'ẽ tenondegua |
| locale | locale | kept as the technical term |
| state | teko | the object state |
| status | teko | |
| filter | mbogua | |
| select | eiporavo | selected: oñeiporavóva |
| browse | eguata | |
| copy | emonguatia | |
| move | emongu'e | |
| add | embojoapy | |
| create | emoheñói | |
| new | pyahu | |
| restore | emoĩ jey | |
| install | emoĩ | installation: ñemoĩ |
| sub items | mba'e'i | |
| subtree | ñemohenda'i | |
| relation, related | joaju, ojoajúva | |
| URL alias | URL téra mokõiha | |
| redirect | mbojere | |
| identifier / ID | techaukaha / ID | |
| owner | jára | |
| date / time | ára / aravo | |
| error / warning | jejavy / jesarekorã | |
| help | pytyvõ | |
| notification | marandu | |
| audit | jehechapy | audit log: jehechapy kuatia |
| repository | ñongatuha | |
| checksum | checksum | kept as the technical term |
| account | mba'ete | |
| default | ijypykuéva | |
| toolbar | tembipuru barra | |
| AI | IA | the Spanish abbreviation used in Paraguay |
| approve | moneĩ | |
| request (HTTP) | jerure | |
| query | porandu | |
| engine | mba'apohára | |
| update / upgrade | mbopyahu | |
| backup | ñongatu jey | |
| maintenance | ñangareko | |
| caption (snapshot) | ñongatupy | |
| migration | ñemongu'e | |
| deploy | ñemondo | |
| remote | mombyrygua | |
| secret | ñemi | |
| key | clave | loan |
| passkey | passkey | kept as the technical term |
| two-step sign-in | mokõi jeho jeike | |
| device / browser | mba'e'oka / kundahára | |
| link | joajuha | |
| registration | ñemboheraguapy | |
| form | formulario | loan |
| comment | ñe'ẽ jehai | |
| newsletter / subscription | ñe'ẽmondo jasyrehegua / jehaiporã | |
| field | ñemoĩha | |
| value | repy | |
| list | lísta | loan |
| line, row | línea | loan |
| column | kolúmna | loan |
| table | tavla | loan |
| page | kuatiarogue | |
| title / name | téra | |
| description | myesakã | |
| type | ichagua | |
| path | rape | |
| size | tuichakue | |
| license | licencia | loan |
| format | formato | loan |
| platform | plataforma | loan |
| budget | presupuesto | loan |
| company | empresa | loan |
| foundation | fundación | loan |
| parameter / variable | parámetro / variable | loan |
| none | mba'eve | |
| yes / no | héẽ / nahániri | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Ñemuha, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is Eñongatu.
- "Settings" is Ñemboheko and "edit" is Embosako'i, so that the two are never confused.
- The setup text whose source holds a `<byte/>` element keeps its source whole.
