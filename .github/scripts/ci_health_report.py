#!/usr/bin/env python3
"""CI health report for GitHub Actions: pass rate per repository, workflow and event over N days,
the steps that fail most, the tests that fail most and the flaky ones.

Reads everything through the gh CLI (`gh api`), so it needs `gh` logged in (or GH_TOKEN set).
Only the Python standard library is used, so the same file runs on a GitHub runner.

  ci_health_report.py                                   # se7enxweb/exponential, last 14 days
  ci_health_report.py --days 7 --repo se7enxweb/exponential --repo se7enxweb/ezjscore
  ci_health_report.py --org se7enxweb                   # every non-archived repository pushed to lately
  ci_health_report.py --org se7enxweb --all-repos       # every non-archived repository (one API call each)
  ci_health_report.py --org se7enxweb --md report.md --json report.json

Definitions
  pass rate   success / (success + failure + timed_out + startup_failure), on the final attempt of each run.
              cancelled, skipped, neutral and action_required runs are counted but not in the rate.
  flaky run   a run whose final attempt succeeded after an earlier attempt failed (a rerun), or a
              failed run whose head sha later passed in another run of the same workflow and event.
  flaky test  a test that failed in a flaky run's failed attempt (PHPUnit "N) Class::method" lines).

Text goes to stdout, Markdown to --md (default: none; '-' writes Markdown to stdout instead of text).
Exit status is 0 unless gh fails; --fail-under PCT exits 1 when any repo/workflow/event group with at
least --min-runs completed runs is below PCT.
"""
import argparse
import collections
import concurrent.futures
import datetime
import hashlib
import json
import os
import re
import subprocess
import sys
import time

FAILED = ('failure', 'timed_out', 'startup_failure')
RATED = ('success',) + FAILED
ANSI = re.compile(r'\x1b\[[0-9;]*[A-Za-z]')
SECTION = re.compile(r'There (?:was|were) \d+ (\w+)')
TEST_LINE = re.compile(r'(?:^|\s)\d+\) ((?:[A-Za-z_][\w]*\\)*[A-Za-z_]\w*::\w+)')


class RateLimited(RuntimeError):
    pass


class Gh:
    def __init__(self, cache_dir, verbose):
        self.cache_dir = cache_dir
        self.verbose = verbose
        self.calls = 0
        if cache_dir:
            os.makedirs(cache_dir, exist_ok=True)

    def _cache_path(self, key):
        return os.path.join(self.cache_dir, hashlib.sha1(key.encode()).hexdigest() + '.json')

    def api(self, path, params=None, paginate=False, jq=None, raw=False, cache=False):
        """gh api; with jq the output is one JSON value per line, returned as a list."""
        cmd = ['gh', 'api', '-X', 'GET', path]
        for k, v in (params or {}).items():
            cmd += ['-f', '%s=%s' % (k, v)]
        if paginate:
            cmd.append('--paginate')
        if raw:
            cmd.append('--allow-escape-sequences')  # job logs carry color codes
        if jq:
            cmd += ['--jq', jq]
        key = ' '.join(cmd)
        if cache and self.cache_dir:
            p = self._cache_path(key)
            if os.path.exists(p):
                with open(p) as fh:
                    return json.load(fh)
        self.calls += 1
        if self.verbose:
            sys.stderr.write('gh %s\n' % key[7:])
        for attempt in range(4):  # GitHub answers 502/503/504 now and then; retry with a pause
            proc = subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
            err = proc.stderr.decode('utf-8', 'replace').strip()
            if proc.returncode == 0 or not re.search(r'HTTP 5\d\d|timeout|connection reset', err, re.I):
                break
            time.sleep(2 * (attempt + 1))
        if proc.returncode != 0:
            # 404/410: no Actions, logs expired, repository gone. Not an error for a report.
            # A rate limit is a 403 too, and must stop the report instead of counting as "no runs".
            if re.search(r'rate limit', err, re.I):
                raise RateLimited('gh api %s: %s (the hourly API limit; try again after `gh api rate_limit`)'
                                   % (path, err.splitlines()[0][:160]))
            if 'HTTP 404' in err or 'HTTP 410' in err or 'HTTP 403' in err:
                return None
            raise RuntimeError('gh api %s: %s' % (path, err))
        out = proc.stdout.decode('utf-8', 'replace')
        if raw:
            result = out
        elif jq:
            result = [json.loads(line) for line in out.splitlines() if line.strip()]
        else:
            result = json.loads(out) if out.strip() else None
        if cache and self.cache_dir:
            with open(self._cache_path(key), 'w') as fh:
                json.dump(result, fh)
        return result


def list_org_repos(gh, org, pushed_since=None):
    """Non-archived repositories; with pushed_since only those pushed to since then (GitHub disables the
    scheduled workflows of a repository without activity for 60 days, so older ones have no runs)."""
    rows = gh.api('orgs/%s/repos' % org, {'per_page': 100, 'type': 'all'}, paginate=True,
                  jq='.[] | select(.archived|not) | {full_name, pushed_at}')
    if rows is None:  # a user account, not an organisation
        rows = gh.api('users/%s/repos' % org, {'per_page': 100, 'type': 'owner'}, paginate=True,
                      jq='.[] | select(.archived|not) | {full_name, pushed_at}')
    return sorted(r['full_name'] for r in rows or [] if not pushed_since or (r['pushed_at'] or '') >= pushed_since)


RUN_FIELDS = ('.workflow_runs[] | {id, name, event, status, conclusion, head_sha, head_branch, '
              'run_attempt, created_at, html_url}')


def list_runs(gh, repo, since):
    runs = gh.api('repos/%s/actions/runs' % repo, {'per_page': 100, 'created': '>=' + since}, paginate=True,
                  jq=RUN_FIELDS) or []
    if len(runs) < 1000:
        return runs
    # The API returns at most 1000 runs per query: ask day by day instead.
    runs = []
    day = datetime.datetime.strptime(since, '%Y-%m-%d').date()
    today = datetime.datetime.now(datetime.timezone.utc).date()
    while day <= today:
        part = gh.api('repos/%s/actions/runs' % repo, {'per_page': 100, 'created': day.isoformat()}, paginate=True,
                      jq=RUN_FIELDS) or []
        if len(part) >= 1000:
            sys.stderr.write('WARN %s: 1000 or more runs on %s, the rest are not counted\n' % (repo, day))
        runs += part
        day += datetime.timedelta(days=1)
    return runs


def run_jobs(gh, repo, run):
    """Every job of every attempt of a completed run (cached: a completed attempt never changes)."""
    try:
        return gh.api('repos/%s/actions/runs/%d/jobs' % (repo, run['id']),
                      {'per_page': 100, 'filter': 'all', '_attempt': run['run_attempt']}, paginate=True,
                      jq='.jobs[] | {id, name, run_attempt, conclusion, steps: [.steps[]? | {name, conclusion}]}',
                      cache=True) or []
    except RateLimited:
        raise
    except RuntimeError as e:  # one unreadable run must not stop the report
        sys.stderr.write('WARN jobs of run %d: %s\n' % (run['id'], str(e)[:200]))
        return []


def job_failed_tests(gh, repo, job_id):
    """The tests PHPUnit lists under 'There were N failures/errors:' (not risky, skipped, deprecations)."""
    cache_file = os.path.join(gh.cache_dir, 'tests-%s-%d.json' % (repo.replace('/', '_'), job_id)) if gh.cache_dir else None
    if cache_file and os.path.exists(cache_file):
        with open(cache_file) as fh:
            return json.load(fh)
    try:
        log = gh.api('repos/%s/actions/jobs/%d/logs' % (repo, job_id), raw=True)
    except RateLimited:
        raise
    except RuntimeError as e:  # one unreadable log must not stop the report
        sys.stderr.write('WARN log of job %d: %s\n' % (job_id, str(e)[:200]))
        return []
    tests = []
    counting = False
    for line in ANSI.sub('', log or '').splitlines():
        sec = SECTION.search(line)
        if sec:
            counting = sec.group(1).lower().startswith(('failure', 'error'))
            continue
        m = TEST_LINE.search(line) if counting else None
        if m and m.group(1) not in tests:
            tests.append(m.group(1))
    if cache_file and log is not None:
        with open(cache_file, 'w') as fh:
            json.dump(tests, fh)
    return tests


def looks_like_tests(job, step):
    text = (job['name'] + ' ' + step).lower()
    return 'phpunit' in text or 'test' in text


def pct(n, d):
    return '%5.1f%%' % (100.0 * n / d) if d else '   - '


def analyse(gh, repos, since, max_logs, workers):
    runs_by_repo = {}
    with concurrent.futures.ThreadPoolExecutor(max_workers=workers) as ex:
        futs = {ex.submit(list_runs, gh, r, since): r for r in repos}
        for f in concurrent.futures.as_completed(futs):
            runs = [r for r in f.result() if r['status'] == 'completed']
            if runs:
                runs_by_repo[futs[f]] = runs

    groups = collections.OrderedDict()   # (repo, workflow, event) -> Counter
    passed_shas = set()                  # (repo, workflow, event, sha) with a success
    for repo in sorted(runs_by_repo):
        for r in runs_by_repo[repo]:
            key = (repo, r['name'], r['event'])
            groups.setdefault(key, collections.Counter())[r['conclusion'] or 'none'] += 1
            groups[key]['runs'] += 1
            if r['conclusion'] == 'success':
                passed_shas.add(key + (r['head_sha'],))

    # Which runs need their jobs: failed ones and reruns.
    todo = []
    for repo, runs in runs_by_repo.items():
        for r in runs:
            if r['conclusion'] in FAILED or r['run_attempt'] > 1:
                todo.append((repo, r))
    jobs_of = {}
    with concurrent.futures.ThreadPoolExecutor(max_workers=workers) as ex:
        futs = {ex.submit(run_jobs, gh, repo, r): (repo, r['id']) for repo, r in todo}
        for f in concurrent.futures.as_completed(futs):
            jobs_of[futs[f]] = f.result()

    step_fail = collections.Counter()       # (repo, workflow, job, step) -> failed attempts
    step_example = {}
    flaky_runs = []
    test_jobs = []                          # (repo, run, job, flaky)
    for repo, r in todo:
        key = (repo, r['name'], r['event'])
        jobs = jobs_of.get((repo, r['id']), [])
        failed_attempts = sorted({j['run_attempt'] for j in jobs if j['conclusion'] in FAILED})
        rerun_flaky = r['conclusion'] == 'success' and failed_attempts
        sha_flaky = r['conclusion'] in FAILED and key + (r['head_sha'],) in passed_shas
        flaky = bool(rerun_flaky or sha_flaky)
        if flaky:
            groups[key]['flaky'] += 1
            flaky_runs.append({'repo': repo, 'workflow': r['name'], 'event': r['event'], 'sha': r['head_sha'][:10],
                               'how': 'rerun passed' if rerun_flaky else 'same sha passed in another run',
                               'url': r['html_url']})
        for j in jobs:
            if j['conclusion'] not in FAILED:
                continue
            steps = [s['name'] for s in j['steps'] if s['conclusion'] in FAILED] or ['(job: %s)' % j['conclusion']]
            for s in steps:
                k = (repo, r['name'], re.sub(r'\s*\(.*\)$', '', j['name']), s)
                step_fail[k] += 1
                step_example.setdefault(k, r['html_url'])
            if any(looks_like_tests(j, s) for s in steps):
                test_jobs.append((repo, r, j, flaky))

    # Logs: flaky jobs first, then the newest failures, up to max_logs.
    flaky_first = [t for t in test_jobs if t[3]] + sorted([t for t in test_jobs if not t[3]],
                                                          key=lambda t: t[1]['created_at'], reverse=True)
    test_jobs = flaky_first[:max_logs]
    test_fail = collections.Counter()
    test_flaky = collections.Counter()
    test_where = collections.defaultdict(set)
    with concurrent.futures.ThreadPoolExecutor(max_workers=workers) as ex:
        futs = {ex.submit(job_failed_tests, gh, repo, j['id']): (repo, r, j, flaky) for repo, r, j, flaky in test_jobs}
        for f in concurrent.futures.as_completed(futs):
            repo, r, j, flaky = futs[f]
            for t in f.result():
                test_fail[(repo, t)] += 1
                test_where[(repo, t)].add(re.sub(r'\s*\(.*\)$', '', j['name']) + ' ' + r['event'])
                if flaky:
                    test_flaky[(repo, t)] += 1

    return {
        'since': since,
        'repos_scanned': len(repos),
        'repos_with_runs': len(runs_by_repo),
        'groups': [{'repo': k[0], 'workflow': k[1], 'event': k[2], 'runs': c['runs'], 'success': c['success'],
                    'failed': sum(c[x] for x in FAILED), 'cancelled': c['cancelled'],
                    'other': c['runs'] - c['success'] - sum(c[x] for x in FAILED) - c['cancelled'],
                    'flaky': c['flaky']} for k, c in groups.items()],
        'steps': [{'repo': k[0], 'workflow': k[1], 'job': k[2], 'step': k[3], 'failures': n, 'example': step_example[k]}
                  for k, n in step_fail.most_common()],
        'tests': [{'repo': k[0], 'test': k[1], 'failures': n, 'flaky': test_flaky[k],
                   'where': sorted(test_where[k])} for k, n in test_fail.most_common()],
        'flaky_runs': flaky_runs,
        'logs_read': len(test_jobs),
        'api_calls': gh.calls,
    }


def rate(g):
    return (g['success'], g['success'] + g['failed'])


def render(rep, days, top, markdown):
    out = []
    w = out.append
    gs = rep['groups']
    tot_s = sum(g['success'] for g in gs)
    tot_d = sum(g['success'] + g['failed'] for g in gs)
    tot_runs = sum(g['runs'] for g in gs)
    head = 'CI health, last %d days (runs created since %s)' % (days, rep['since'])
    summary = ('%d repositories scanned, %d with runs; %d completed runs, pass rate %s (%d of %d rated), '
               '%d cancelled, %d flaky' % (rep['repos_scanned'], rep['repos_with_runs'], tot_runs,
                                           pct(tot_s, tot_d).strip(), tot_s, tot_d,
                                           sum(g['cancelled'] for g in gs), sum(g['flaky'] for g in gs)))
    # Per repository totals, worst first.
    per_repo = collections.OrderedDict()
    for g in gs:
        a = per_repo.setdefault(g['repo'], collections.Counter())
        for f in ('runs', 'success', 'failed', 'cancelled', 'flaky'):
            a[f] += g[f]
    repo_order = sorted(per_repo, key=lambda r: ((per_repo[r]['success'] / float(per_repo[r]['success'] + per_repo[r]['failed']))
                                                  if per_repo[r]['success'] + per_repo[r]['failed'] else 2, r))
    if markdown:
        w('# ' + head)
        w('')
        w(summary + '.')
        w('')
        w('## Per repository')
        w('')
        w('| Repository | Runs | Pass rate | Failed | Cancelled | Flaky |')
        w('|---|---:|---:|---:|---:|---:|')
        for r in repo_order:
            a = per_repo[r]
            w('| %s | %d | %s | %d | %d | %d |' % (r, a['runs'], pct(a['success'], a['success'] + a['failed']).strip(),
                                                  a['failed'], a['cancelled'], a['flaky']))
        w('')
        w('## Per workflow and event')
        w('')
        w('| Repository | Workflow | Event | Runs | Pass rate | Success | Failed | Cancelled | Other | Flaky |')
        w('|---|---|---|---:|---:|---:|---:|---:|---:|---:|')
        for g in gs:
            w('| %s | %s | %s | %d | %s | %d | %d | %d | %d | %d |' % (
                g['repo'], g['workflow'], g['event'], g['runs'], pct(*rate(g)).strip(), g['success'], g['failed'],
                g['cancelled'], g['other'], g['flaky']))
        w('')
        w('## Top failing steps')
        w('')
        if rep['steps']:
            w('| Failed jobs | Repository | Workflow | Job | Step | Example |')
            w('|---:|---|---|---|---|---|')
            for s in rep['steps'][:top]:
                w('| %d | %s | %s | %s | %s | [run](%s) |' % (s['failures'], s['repo'], s['workflow'], s['job'],
                                                              s['step'].replace('|', '\\|'), s['example']))
        else:
            w('None.')
        w('')
        w('## Failing tests (from %d job logs)' % rep['logs_read'])
        w('')
        if rep['tests']:
            w('| Failures | Of them flaky | Repository | Test | Jobs |')
            w('|---:|---:|---|---|---|')
            for t in rep['tests'][:top]:
                w('| %d | %d | %s | `%s` | %s |' % (t['failures'], t['flaky'], t['repo'], t['test'], ', '.join(t['where'])))
        else:
            w('None found in the logs read.')
        w('')
        w('## Flaky runs')
        w('')
        if rep['flaky_runs']:
            for f in rep['flaky_runs'][:top]:
                w('- %s %s (%s) %s: %s, [run](%s)' % (f['repo'], f['workflow'], f['event'], f['sha'], f['how'], f['url']))
        else:
            w('None.')
        w('')
        w('_%d API calls. Pass rate = success / (success + failure + timed out + startup failure), final attempt of each run._'
          % rep['api_calls'])
    else:
        w(head)
        w(summary)
        w('')
        w('%-46s %5s %7s %6s %6s %5s' % ('REPOSITORY', 'RUNS', 'PASS', 'FAIL', 'CANCEL', 'FLAKY'))
        for r in repo_order:
            a = per_repo[r]
            w('%-46s %5d %7s %6d %6d %5d' % (r[:46], a['runs'], pct(a['success'], a['success'] + a['failed']),
                                             a['failed'], a['cancelled'], a['flaky']))
        w('')
        w('%-34s %-24s %-19s %4s %7s %4s %4s %4s' % ('REPOSITORY', 'WORKFLOW', 'EVENT', 'RUNS', 'PASS', 'FAIL', 'CANC', 'FLKY'))
        for g in gs:
            w('%-34s %-24s %-19s %4d %7s %4d %4d %4d' % (g['repo'][:34], g['workflow'][:24], g['event'][:19], g['runs'],
                                                         pct(*rate(g)), g['failed'], g['cancelled'], g['flaky']))
        w('')
        w('TOP FAILING STEPS (failed jobs, matrix legs counted one by one)')
        for s in rep['steps'][:top]:
            w('%4d  %s / %s / %s / %s' % (s['failures'], s['repo'], s['workflow'], s['job'], s['step']))
        if not rep['steps']:
            w('  none')
        w('')
        w('FAILING TESTS (from %d job logs; flaky = failed in a run that passed on rerun or at the same sha)' % rep['logs_read'])
        for t in rep['tests'][:top]:
            w('%4d  flaky %-3d %s  %s  [%s]' % (t['failures'], t['flaky'], t['repo'], t['test'], ', '.join(t['where'])))
        if not rep['tests']:
            w('  none found')
        w('')
        w('FLAKY RUNS')
        for f in rep['flaky_runs'][:top]:
            w('  %s %s (%s) %s: %s  %s' % (f['repo'], f['workflow'], f['event'], f['sha'], f['how'], f['url']))
        if not rep['flaky_runs']:
            w('  none')
        w('')
        w('%d API calls' % rep['api_calls'])
    return '\n'.join(out) + '\n'


def main():
    ap = argparse.ArgumentParser(description=__doc__.split('\n')[0])
    ap.add_argument('--days', type=int, default=14)
    ap.add_argument('--repo', action='append', default=[], help='owner/name; repeatable')
    ap.add_argument('--org', help='every non-archived repository of this owner pushed to in the last DAYS+60 days (plus --repo)')
    ap.add_argument('--all-repos', action='store_true', help='with --org: also the repositories without a recent push (one API call each)')
    ap.add_argument('--md', help="write the Markdown report to this file ('-' = stdout instead of the text)")
    ap.add_argument('--json', help='write the raw figures as JSON to this file')
    ap.add_argument('--top', type=int, default=20, help='rows in the step, test and flaky lists')
    ap.add_argument('--max-logs', type=int, default=150, help='job logs read for test names (flaky jobs first)')
    ap.add_argument('--workers', type=int, default=6)
    ap.add_argument('--cache-dir', default=None,
                    help="cache for jobs and logs of completed runs (default var/tmp/ci_health_cache when it exists, "
                         "'none' to disable)")
    ap.add_argument('--fail-under', type=float, help='exit 1 when a group with --min-runs rated runs is below this pass rate')
    ap.add_argument('--min-runs', type=int, default=5)
    ap.add_argument('-v', '--verbose', action='store_true')
    a = ap.parse_args()

    cache = a.cache_dir
    if cache is None:
        cache = 'var/tmp/ci_health_cache' if os.path.isdir('var/tmp') else 'none'
    gh = Gh(None if cache == 'none' else cache, a.verbose)

    repos = list(a.repo)
    if a.org:
        idle = None if a.all_repos else (datetime.datetime.now(datetime.timezone.utc)
                                          - datetime.timedelta(days=a.days + 60)).strftime('%Y-%m-%d')
        repos += [r for r in list_org_repos(gh, a.org, idle) if r not in repos]
    if not repos:
        repos = [os.environ.get('GITHUB_REPOSITORY') or 'se7enxweb/exponential']
    since = (datetime.datetime.now(datetime.timezone.utc) - datetime.timedelta(days=a.days)).strftime('%Y-%m-%d')

    try:
        rep = analyse(gh, repos, since, a.max_logs, a.workers)
    except RuntimeError as e:
        sys.stderr.write('FAIL %s\n' % e)
        return 2

    md = render(rep, a.days, a.top, True)
    if a.md == '-':
        sys.stdout.write(md)
    else:
        sys.stdout.write(render(rep, a.days, a.top, False))
        if a.md:
            with open(a.md, 'w') as fh:
                fh.write(md)
            sys.stderr.write('Markdown written to %s\n' % a.md)
    if a.json:
        with open(a.json, 'w') as fh:
            json.dump(rep, fh, indent=1)
    if a.fail_under is not None:
        low = [g for g in rep['groups'] if g['success'] + g['failed'] >= a.min_runs
               and 100.0 * g['success'] / (g['success'] + g['failed']) < a.fail_under]
        for g in low:
            sys.stderr.write('BELOW %.0f%%: %s %s %s %s\n' % (a.fail_under, g['repo'], g['workflow'], g['event'],
                                                            pct(*rate(g)).strip()))
        return 1 if low else 0
    return 0


if __name__ == '__main__':
    sys.exit(main())
