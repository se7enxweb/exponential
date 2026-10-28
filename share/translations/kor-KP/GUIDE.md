# Korean, North (kor-KP): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

The Korean of the Democratic People's Republic of Korea, the cultured language (문화어), spelled by the North Korean
norms (조선말규범집: 맞춤법, 띄여쓰기, 문장부호법). The catalogue was adapted from the complete South Korean one,
`kor-KR`, row by row: what both standards write alike stays as it is, and what North Korean usage writes differently
is changed. `kor-KP` has a catalogue of its own and does not fall back to `kor-KR`.

The tone is neutral, polite and concise. Native Korean words and the established North Korean terms are preferred to
English loanwords (자료기지, not 데이터베이스; 차림표, not 메뉴); a loanword that North Korea uses is written in its
North Korean form, which often follows Russian (클라스, 그루빠, 프로그람, 콤퓨터).

Where North Korean usage for a computing term is not certain, the `kor-KR` term is kept unchanged; section 5 lists
those terms. A native speaker's review is welcome, above all for them.

## 2. Form of address

As in `kor-KR`: the user is not named, 당신 is never used, and the politeness lies in the verb ending.

- **Buttons, menu items, links and action labels** are nouns or noun phrases: "저장", "삭제", "편집", "취소",
  "추가", "적용", "새로 만들기", "모두 선택", "닫기", "보기", "찾아보기". Never an imperative on a button.
- **Titles, column headings and tabs** are nouns: "콘텐츠구조", "사용자계정", "알림 설정".
- **Sentences, hints and messages**: the formal polite style ‑ㅂ니다/‑습니다.
- **Instructions and requests**: ‑십시오, softened with ‑해주십시오: "위치를 선택하십시오",
  "련결을 확인한 후 다시 시도하십시오."
- **Questions and confirmations**: ‑시겠습니까?: "이 클라스를 제거하시겠습니까?"
- **Status messages**: ‑되였습니다 or a short noun phrase: "변경내용이 저장되였습니다."
- Not allowed: 당신, 너, 귀하; the plain style ‑한다; 반말; the polite informal ‑해요/‑하세요.

## 3. Orthography, spacing and typography

### 3.1 Initial ㄹ and ㄴ are kept (no 두음법칙)

A Sino-Korean syllable that has ㄹ or ㄴ keeps it at the start of a word:

| kor-KR | kor-KP | |
|---|---|---|
| 이용, 이용자 | 리용, 리용자 | 利用 |
| 이력, 이유, 이해, 이론, 이익 | 리력, 리유, 리해, 리론, 리익 | |
| 연결, 재연결, 연동, 연속, 연관, 연락 | 련결, 재련결, 련동, 련속, 련관, 련락 | 連 |
| 연도, 연간 | 년도, 년간 | 年 |
| 역사 | 력사 | 歷 |
| 영역 | 령역 | 領 |
| 유형, 유사 | 류형, 류사 | 類 |
| 노출, 노동, 논리, 녹색 | 로출, 로동, 론리, 록색 | |
| 누락, 누적 | 루락, 루적 | |
| 임시, 임계값 | 림시, 림계값 | 臨 |
| 여자 | 녀자 | |
| 예: , 예를 들어, 예제, 예외, 예시 | 례: , 례를 들어, 례제, 례외, 례시 | 例; 예 (yes) stays |
| 나열, 배열 | 라렬, 배렬 | 列 |
| 비율, 효율 | 비률, 효률 | 率 is always 률 |
| 열 (column) | 렬 | 列; 열기 (open) is native and stays |

Syllables whose original sound has no ㄹ/ㄴ stay: 이동 (移), 이전 (以), 이상 (以/異), 여부 (與), 역할 (役),
역순 (逆), 연산자 (演), 연장 (延), 영구 (永), 예약 (豫), 유지 (維), 유효 (有), 요청 (要), 요약 (要).

### 3.2 Spelling

- ‑였‑/‑여 after a stem ending in ㅣ, ㅐ, ㅔ, ㅚ, ㅟ, ㅢ: 되였습니다, 되여있습니다, 비여있음, 이였습니다,
  건너뛰여.
- No 사이시옷: 수자 (숫자), 회수 (횟수), 최대값, 최소값, 결과값, 기대값, 뒤부분, 웃부분, 아래부분, 머리글.

### 3.3 Spacing (띄여쓰기)

- **Dependent nouns are attached** to the word before them: 할수 있습니다, 할수 없습니다, 하는것, 모든것.
- **Auxiliary verbs are attached**: 하고있습니다, 저장되여있습니다, 확인해주십시오, 제거해보십시오.
  The negation stays apart: 하지 않습니다, 사용 안 함.
- **Fixed terms of the glossary are one word**: 사용자그루빠, 사용자이름, 사용자계정, 콘텐츠클라스, 콘텐츠객체,
  화일이름, 미리보기, 새로고침, 자리표시자, 변경내용, 세부정보, 하위항목, 상위항목, 하위트리, 크론작업,
  객체상태, 기본언어, 관리대면부, 전자우편주소, 자료형.
- Particles and the copula are attached as in `kor-KR`. A counter follows a number directly: %count개.

### 3.4 Punctuation

- Quotation marks are 《…》, and 〈…〉 inside a quotation, in running text. Straight quotes in HTML attributes,
  JavaScript and code stay as in the source.
- The full stop, comma, colon, question mark and ellipsis are the Latin ones, as in the source.

### 3.5 Particles after placeholders and Latin words

As in `kor-KR`: a Korean noun after the placeholder carries the particle ("%name 객체를"), or a label form
("사용자: %name"), or the double form ("%name을(를)"). When a noun is replaced (네트워크 → 망, 그룹 → 그루빠),
the particle after it follows the new final sound (망을, 그루빠를, 화상을).

## 4. Glossary: where North Korean usage differs

The glossary of [kor-KR/GUIDE.md](../kor-KR/GUIDE.md) applies with these replacements, which are binding.

| English | kor-KR | kor-KP | Note |
|---|---|---|---|
| object | 개체 | 객체 | content object: 콘텐츠객체 |
| class | 클래스 | 클라스 | |
| group | 그룹 | 그루빠 | user group: 사용자그루빠 |
| file | 파일 | 화일 | file name: 화일이름 |
| folder / directory | 폴더 / 디렉터리 | 등록부 | |
| page | 페이지 | 페지 | |
| site / website | 사이트 / 웹 사이트 | 싸이트 / 웨브싸이트 | web: 웨브 |
| siteaccess | 사이트 액세스 | 싸이트접근 | |
| access | 액세스 | 접근 | access denied: 접근 거부됨 |
| database | 데이터베이스 | 자료기지 | |
| data / datatype | 데이터 / 데이터 형식 | 자료 / 자료형 | |
| table (database, HTML) | 테이블 | 표 | |
| field | 필드 | 마당 | |
| index / indexing | 인덱스 / 인덱싱 | 색인 / 색인화 | |
| menu | 메뉴 | 차림표 | context menu: 상황에 맞는 차림표 |
| server | 서버 | 봉사기 | |
| system | 시스템 | 체계 | system information: 체계정보 |
| interface | 인터페이스 | 대면부 | administration interface: 관리대면부 |
| version | 버전 | 판 | version history: 판기록 |
| error | 오류 | 오유 | |
| message | 메시지 | 통보문 | |
| e-mail | 이메일 | 전자우편 | e-mail address: 전자우편주소 |
| image | 이미지 | 화상 | |
| link | 링크 | 련결 | symbolic link: 심볼릭 련결 |
| browser | 브라우저 | 열람기 | |
| click | 클릭 | 찰칵 | 찰칵하면, 찰칵하십시오 |
| upload / download | 업로드 / 다운로드 | 올리적재 / 내리적재 | |
| update | 업데이트 | 갱신 | |
| backup | 백업 | 여벌 | |
| queue | 큐 | 대기렬 | job queue: 작업대기렬 |
| encoding | 인코딩 | 부호화 | |
| source code | 소스 코드 | 원천코드 | |
| category | 카테고리 | 범주 | |
| computer | 컴퓨터 | 콤퓨터 | |
| program / software | 프로그램 / 소프트웨어 | 프로그람 / 쏘프트웨어 | |
| network / internet | 네트워크 / 인터넷 | 망 / 인터네트 | |
| keyboard | 키보드 | 건반 | |
| paste | 붙여넣기 | 붙이기 | |
| drag | 드래그 | 끌기 | in a sentence: 끌어서 |
| preview | 미리 보기 | 미리보기 | |
| placeholder | 자리 표시자 | 자리표시자 | |
| refresh | 새로 고침 | 새로고침 | |
| quotation marks | “…” | 《…》 | |

## 5. Terms kept from kor-KR (North Korean usage uncertain)

No settled North Korean term could be confirmed for these computing words, so the `kor-KR` term is kept. They are
the first candidates for a review by a native speaker.

| English | Kept term | Possible North Korean term |
|---|---|---|
| content | 콘텐츠 | 내용물 |
| node | 노드 | 마디 |
| template | 템플릿 | 본보기, 형타 |
| cache | 캐시 | 캐쉬 |
| package | 패키지 | 묶음, 파케지 |
| module | 모듈 | 모쥴 |
| section | 섹션 | 부문 |
| log in / log out | 로그인 / 로그아웃 | 가입 / 탈퇴 |
| log | 로그 | 기록 |
| event | 이벤트 | 사건 |
| workflow | 워크플로 | |
| session / cookie | 세션 / 쿠키 | |
| text | 텍스트 | 본문 |
| icon | 아이콘 | 그림기호 |
| block | 블록 | 블로크 |
| kernel | 커널 | 핵심부 |
| license | 라이선스 | 허가 |
| debug | 디버그 | 오유수정 |
| save / store | 저장 | 보관 (보관 stays archive here) |
| dialog | 대화 상자 | 대화칸 |
| user | 사용자 | 리용자 |
| Yes / No | 예 / 아니요 | 예 / 아니 |
| layout, dashboard, plugin, token, script | 레이아웃, 대시보드, 플러그인, 토큰, 스크립트 | |

## 6. Shop tab

The header tab "Store" of the shop is named like the shop menu (상점); every other "Store" is the button 저장.
