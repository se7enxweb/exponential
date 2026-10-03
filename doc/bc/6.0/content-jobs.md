# Content jobs: large removes, copies and moves in the background

Read this page if your editors remove, copy or move large subtrees, if you run a cluster, or if you override the
admin confirmation pages. From Exponential 6.0.15 a large operation runs as a **content job** in the background
instead of inside the web request.

Before, removing, copying or moving a subtree happened in the request that confirmed it. That is fine for ten nodes.
For a few hundred it hits the request time limit, the memory limit or one huge database transaction, and the request
fails half-way: part of the subtree is gone, or copied, and part is not. The admin also refused such operations above
`MaxNodesRemoveSubtree` / `MaxNodesCopySubtree` and sent the editor to a command line script.

A content job runs in a background worker, in batches of 50 nodes, each batch in its own database transaction, with a
checkpoint after every batch. The editor sees a progress page, can leave it, cancel the job or resume it, and a job
finishes even when its worker is killed. Small operations are unchanged: below `SynchronousLimit` (50 nodes) the
editor gets the page and the result they always got.

## In short

| | |
|---|---|
| What changed | Large remove, copy, move, hide/reveal, section, subtree state and location operations become jobs (`content/job/<id>`, `content/jobs`). New settings block `content.ini [ContentJobSettings]`, new policy `content/jobs`, new cronjob part `contentjobs`, new CLI `bin/php/expcontentjob.php`. `MaxNodesRemoveSubtree` and `MaxNodesCopySubtree` now only limit "now". |
| Who is affected | Editors of large subtrees (they get a choice and a progress page). Clusters: the job directory must be shared. Overrides of the confirmation and browse pages (new blocks). Roles that should see every user's jobs. |
| How to check | `grep -n -A12 "\[ContentJobSettings\]" settings/content.ini` and `grep -n -A3 "CronjobPart-contentjobs" settings/cronjob.ini` |
| How to fix | Run the `frequent` cronjob group; share `var/<site var dir>/jobs/content/` across cluster nodes; give `content/jobs` to roles that supervise; compare template overrides with the shipped ones. |

## Upgrade checklist

1. Make sure the cronjob part `contentjobs` runs (it is in the frequent group). It starts jobs whose worker never
   started and resumes jobs whose worker died: `php bin/php/console cron:frequent --allow-root-user`.
2. On a cluster, give every node that serves the admin or runs the cronjob the same `var/` (see
   [Cluster note](#cluster-note)).
3. Give the `content/jobs` policy to roles that must see every user's jobs. It is part of `content/*`.
4. If PHP's `exec` is disabled, or the process limit is low, workers start from the cronjob part within a minute.
   Set `PhpBinary` if the command line PHP is not found.
5. On SQLite, consider `BatchPause` of 100 to 200 milliseconds.
6. If you override the confirmation or browse pages listed under [What the editor sees](#what-the-editor-sees), compare
   them with the shipped templates: the new blocks and the choice are added there.

## What the editor sees

### Before: the confirmation, with what it touches and a choice

The remove confirmation (`content/removeobject`), the browse page of a subtree copy (`content/copysubtree`), of
a move (`content/action`, Move) and of "Add a location for selected" (below), and, for large operations, a new
confirmation for hide/reveal (`content/hide`), for assigning a section (`section/assign`), for setting a state on
a subtree (`state/assign`) and for removing many locations (`content/action`, RemoveAssignment) show:

- **What this touches**: the number of locations and objects, how many objects also have locations outside
  (removing only these locations, they keep existing), the classes involved with their counts, and who last
  modified each root and when.
- **Background jobs working on this part of the tree**, with links, when there are any: an overlapping
  operation is refused until they are done.
- **How to run it**:
  - "Run in the background (recommended for N items: you can leave the page)"
  - "Run now (this page waits until it is done)"

  Below `SynchronousLimit` items "now" is preselected, from it on "background". The editor's last choice per
  operation is remembered (the preferences `admin_content_job_mode_<operation>`) and preselected next time.
  Above `NowLimit` (1000) "now" is greyed out with the reason; so it is above an older limit of the page
  (`MaxNodesRemoveSubtree`, `MaxNodesCopySubtree`), because the old code would refuse it there.
- For a remove, what "Move to trash" means (restorable from the trash) against deleting, and that items the
  editor may not remove are marked in red and stop the whole removal.
- For a copy, which versions and which creator the copy gets (`content.ini [CopySettings]`).

Hide/reveal and section assignment had no confirmation page and still have none for small subtrees: the page
appears only when the background is preselected (a large subtree, or the editor chose the background last time).
The same holds for the three operations below.

On a browse page the choice is shown above the browse form; it is sent with that form whichever comes first in
the page. (Until 6.0.15 the choice of the move and copy browse pages was not sent, and the automatic decision by
`SynchronousLimit` applied whatever the editor chose.)

### States for a subtree, and adding or removing many locations

- **States for a subtree** (job type `state`): the states form of the node's Details tab has **Also for everything
  below this node** when the node has children and the editor may assign a state. "Now" sets the chosen states on
  every object of the subtree in the request, through the kernel's `updateobjectstate` operation, which leaves out
  states the editor may not assign to an object. "Background" starts a `state` job, which skips such objects with a
  warning. A job sets one state, so the background needs one changed state group; changing two groups at once can
  only run now, up to `NowLimit`. The editor must be allowed to assign the state to the node's own object,
  otherwise the whole request is refused with the reason. Without the option the form sets the object's states
  as before. An installation whose only state group is the internal `ez_lock` shows no states form and so
  no option.
- **Removing many locations** (job type `removelocation`): the locations window's **Remove selected**. A location
  with children still goes to the remove confirmation as before; otherwise many selected locations (or the
  editor's last choice "background") get the confirmation ("Selected locations", the objects, which keep their
  other locations) and a `removelocation` job; a few are removed at once as before.
- **Adding a location for many items** (job type `addlocation`): **Add a location for selected** in the sub items
  list's **More actions** opens a browse page for the new parent with what it touches and the choice. "Now" adds
  the location per object through the kernel's `addlocation` operation, as the locations window does for one
  object; "background" starts an `addlocation` job. Objects already placed under the chosen node, and items that
  would be placed below themselves, are left out. The locations window's own **Add locations** (one object under
  several new parents) is unchanged: no job type covers that shape, and it adds one node per chosen parent.

### During: the progress page `content/job/<id>`

- the title ("Copying Fit & Healthy"), the state (Waiting, Running, Done, Failed, Cancelled), "120 of 400 nodes",
  the seconds it has run, a progress bar and the current step;
- the last 40 lines of the worker log, updated live, and **Download the whole log** (`?log=1`);
- **Who**: the user it runs as (linked), their login, who created it on their behalf when that differs (the
  partial-copy remove, the cronjob), who cancelled or resumed it and when;
- **What**: the operation, the source and the target with their full paths (linked), when the source was last
  modified and by whom, the old and new place of a move, the options (trash or delete; versions, creator and
  time of a copy; the chosen mode), and afterwards the new copy (linked) or, for a removal to the trash, a link
  to the trash;
- **Counts**: items done of the total, and what the engine counts: locations and objects removed or copied,
  objects moved to the trash, locations removed whose object stays, skipped, failed, warnings;
- **When**: created, started, the wait for a worker, finished, the duration, and while it runs an estimate of
  the time left from the rate so far;
- **Batches**: the batch size, the current batch, the average and the slowest batch, the peak memory;
- **Where it runs**: the siteaccess, where it was started from and where its worker runs (Apache / PHP-FPM,
  Exponential Velocity or the command line, the host, the pid, the system user), whether the worker is alive
  (with the age of its last heartbeat) or stalled, how many times a worker was started, and the lock it holds.

The page asks `content/job/<id>?json=1` for the status every two seconds. When the job is done it opens the
result after three seconds: the new copy, the moved node in its new place, or the parent of what was removed.
When it fails or is cancelled the page reloads to show the right buttons.

| Button | When | Does |
|---|---|---|
| Cancel | waiting, running, failed | a waiting or failed job stops at once; a running one after its current batch. What is done is kept. |
| Resume | failed, or the worker died | continues from the last finished batch; nothing is done twice |
| Remove the partial copy | a cancelled copy that created something | starts a remove job (delete, not trash) for the partial copy |

A failed job shows the error and a link to the node it is about.

### Afterwards: the jobs list `content/jobs`

The editor's own jobs; with the `content/jobs` policy also **All users** (`content/jobs/(all)/1`). Per job: the
user (login, full name on hover), the operation, source and target, the state, progress and items, the start
time and the duration, and the server (where it was started from, and the siteaccess). Above the list: the
number of jobs per state (each a filter), filters by state, operation and user, sorting by state, items, start
time and duration, 25 jobs a page. While a job runs, the list reloads every five seconds.

## When a job is used

| The editor chooses | Items | What happens |
|---|---|---|
| background | any | a job |
| now | up to `NowLimit` (and up to the page's old limit) | the old synchronous code, exactly as before |
| now | above | refused in the form (greyed out); a forged or stale form gets a job |
| (no choice: an older form, a script posting to the view) | below `SynchronousLimit` and the old limit | the old synchronous code |
| | at or above either | a job |

The small synchronous path does not load the engine at all: the view counts the nodes from the tree and reads the
lock index (`locks.json`) itself. A remove or copy of a few nodes runs the same code with the same pages as
before; only the new information block and the choice are added (proven, see Tests).

## Locks

A job locks its subtrees from the moment it is created until it is done or cancelled (a failed job keeps its
lock until it is resumed and done, or cancelled): a remove each removed subtree, a copy the source subtree, the
target node and the new copy, a move the subtree in its old and new place, a state job its subtree, a
removelocation job each removed location, an addlocation job the target and each selected node. Any remove,
copy, move, hide, section or subtree state assignment, removal of locations or adding of a location that
overlaps a locked subtree, synchronous or as a job, is refused with a page that says so and links to the job
holding the lock. Nothing is queued silently.

## Permissions

| Who | Sees and acts on |
|---|---|
| the user who started the job | their job: progress page, JSON status, log, Cancel, Resume, Remove the partial copy |
| a user with the `content/jobs` policy (new; part of `content/*`) | every job, and the **All users** list |
| a user with unlimited `content/remove` (remove jobs) or `content/create` (copy jobs) | those jobs |

`content/job` and `content/jobs` need `content/read` to be opened at all. The engine checks the operation's own
permissions for the whole set when the job is created, and per node as the requesting user in the worker.

## The command and the cronjob part

```bash
php bin/php/expcontentjob.php list [--all]                 # or: php bin/php/console exp:expcontentjob list
php bin/php/expcontentjob.php show <id>
php bin/php/expcontentjob.php remove <node>[,<node>...] [--trash|--delete] [--background]
php bin/php/expcontentjob.php copy <source> <new parent> [--all-versions] [--keep-creator] [--keep-time] [--background]
php bin/php/expcontentjob.php move <node> <new parent> | hide <node> | reveal <node> | section <node> <section id>
php bin/php/expcontentjob.php run <id> | resume <id> [--background] | cancel <id>
```

The cronjob part `contentjobs` (frequent group, `settings/cronjob.ini`) starts jobs whose worker never started
(after `QueuedGrace` seconds) and resumes jobs whose worker died. A job is therefore never lost when the web
server cannot start a worker (`exec` disabled, the process limit reached): it starts within a minute.

## Settings: `content.ini [ContentJobSettings]`

| Setting | Default | Meaning |
|---|---|---|
| `SynchronousLimit` | 50 | from this many items the background is preselected (0: always) |
| `NowLimit` | 1000 | above this many items "now" is not offered (0: no ceiling) |
| `BatchSize` | 50 | nodes per batch, one transaction each |
| `BatchPause` | 0 | milliseconds between batches, room for other writers (on SQLite worth 100 to 200) |
| `QueuedGrace` | 60 | seconds before the cronjob part starts a job nobody started |
| `MaxAttempts` | 5 | worker starts without a finished batch before the job is failed |
| `KeepDays` | 30 | finished jobs are removed after this many days |
| `PhpBinary` | (empty) | the PHP command line binary for the worker |
| `JobTypes[<name>]` | remove, copy, move, hide, reveal, section, state, addlocation, removelocation | the job types |

The older `[RemoveSettings] MaxNodesRemoveSubtree` and `[CopySettings] MaxNodesCopySubtree` now only limit what
may run "now"; with background jobs available a larger subtree becomes a job instead of being refused. They
refuse as before only when the job layer cannot be used, and then say so.

## How it scales

Measured on a test installation (SQLite, PHP 8.5, 2026-10-02), batches of 50:

| Operation | Items | Time | Per batch | Peak memory |
|---|---|---|---|---|
| copy of "Fit & Healthy" (mixed classes, images, relations) | 142 | 11 s | 3.0 to 4.4 s | 17.6 to 19.7 MB |
| removal of that copy (delete) | 143 | 5 s | 1.4 to 1.8 s | 16.4 to 17.6 MB |
| copy of a generated test tree, depth 5 | 2,340 | 2 min 9 s | about 2.8 s | 15.7 MB |

Memory stays flat: the engine clears the in-memory caches after every batch, so the peak of batch 90 is the
peak of batch 1. Time grows linearly with the number of nodes. The engine's own scale, kill -9 and concurrency
tests are described in its test suite (`tests/tests/kernel/classes/contentjob/`).

## Failure handling

- **A batch fails**: its transaction is rolled back, the job is `failed` with the error (and the node it is
  about), it keeps its lock, and Resume continues from the last finished batch.
- **The worker is killed** (kill -9, a reboot, a deploy): the job stays `running` without a worker; the progress
  page shows the worker as stalled and offers Resume; the cronjob part resumes it by itself.
- **The web server cannot start a worker**: the job waits; the cronjob part starts it after `QueuedGrace`.
- **Cancel**: after the current batch. A cancelled remove leaves the rest in place; a cancelled copy leaves the
  partial copy and offers to remove it.
- **The engine is missing or broken**: every view falls back to its old synchronous code, and logs the error.

## Adding a job type

A job type is a class implementing `expContentJobType` (`kernel/classes/contentjob/expcontentjobtype.php`),
registered in `content.ini [ContentJobSettings] JobTypes[<name>]=<class>`. The engine does the rest: the store,
the locks, the worker, the batches with a transaction each, the checkpoint, the heartbeat, cancel and resume.
The contract that makes a job survive a kill at any point: `runBatch()` writes only to the database (the worker
wraps it in a transaction), and it is idempotent: run again after a crash it finds its work already done.

This example hides every node of a subtree, deepest first, in batches. It is tested as it stands
(61 nodes, BatchSize 20, done after 4 batches,
every node hidden, a second run does nothing, the lock released). The kernel ships a real `hide` type; this one
is for learning the contract, registered under another name.

```php
<?php
// extension/myext/classes/myextjobhidesubtree.php
class myExtJobHideSubtree implements expContentJobType
{
    public function validate( array $params, eZUser $user )
    {
        $node = eZContentObjectTreeNode::fetch( (int) ( isset( $params['node_ids'][0] ) ? $params['node_ids'][0] : 0 ) );
        if ( !$node instanceof eZContentObjectTreeNode )
            throw new expContentJobException( 'The node does not exist.' );
        if ( !$node->canHide() )
            throw new expContentJobException( 'You may not hide this node.' );
        return array( 'node_ids' => array( (int) $node->attribute( 'node_id' ) ),
                      'path' => $node->attribute( 'path_string' ) );
    }

    public function countNodes( array $params )
    {
        $node = eZContentObjectTreeNode::fetch( (int) $params['node_ids'][0] );
        return $node ? $node->subTreeCount( array( 'Limitation' => array() ) ) + 1 : 0;
    }

    public function locks( array $params )
    {
        return array( array( 'path' => $params['path'], 'mode' => expContentJobLock::SUBTREE ) );
    }

    public function describe( array $params )
    {
        return 'Hide the subtree of node ' . $params['node_ids'][0];
    }

    public function prepare( expContentJob $job )
    {
        $checkpoint =& $job->checkpoint();
        $checkpoint += array( 'hidden' => 0 );
    }

    public function runBatch( expContentJob $job, $batchSize )
    {
        $params = $job->params();
        $db = eZDB::instance();
        // what is still visible, deepest first; a batch run twice finds its nodes hidden and skips them
        $rows = $db->arrayQuery( "SELECT node_id FROM ezcontentobject_tree
                                  WHERE path_string LIKE '" . $db->escapeString( $params['path'] ) . "%' AND is_hidden = 0
                                  ORDER BY depth DESC, node_id", array( 'limit' => (int) $batchSize ) );
        foreach ( $rows as $row )
            eZContentObjectTreeNode::hideSubtree( eZContentObjectTreeNode::fetch( (int) $row['node_id'] ) );
        $checkpoint =& $job->checkpoint();
        $checkpoint['hidden'] += count( $rows );
        return array( 'done' => count( $rows ), 'finished' => count( $rows ) < (int) $batchSize,
                      'message' => 'Hidden ' . $checkpoint['hidden'] . ' nodes' );
    }

    public function afterBatch( expContentJob $job )
    {
        eZContentCacheManager::clearAllContentCache();
    }

    public function finish( expContentJob $job )
    {
        $checkpoint =& $job->checkpoint();
        $job->setResult( 'hidden', $checkpoint['hidden'] );
    }
}
```

```ini
# extension/myext/settings/content.ini.append.php
[ContentJobSettings]
JobTypes[myhide]=myExtJobHideSubtree
```

```php
$job = expContentJob::create( 'myhide', array( 'node_ids' => array( 123 ) ), eZUser::currentUser() );
$job->spawn();                                  // the progress page: content/job/<id>
```

The progress page and the jobs list show any registered type: its `describe()` as the title, the result's
counts, the batches and the log. To give a view a choice for it, call
`Exponential\View\Kernel\Content\Job::modeChoice()` for the confirmation page, `chosenAsJob()` when it is posted,
and `startJob()`; `interstitial()` does all three for an operation that has no confirmation page of its own. The
RAD survey (`setup/radsurvey`) counts the registered job types.

## Cluster note

Jobs live as files in `var/<site var dir>/jobs/content/` (`<id>.json`, `<id>.log`, the copy's work list and id
map, `locks.json`), written atomically under `flock`. On a cluster every node that serves the admin or runs the
cronjob needs to see the same directory (a shared `var/`), as for the repair queue; otherwise a job started on
one node is invisible, and unlocked, on the others. The files belong to the site user, also when the job is
created under Exponential Velocity (root).

## Tests

- `tests/tests/kernel/classes/contentjob/`: the engine (store, locks, spawn, worker, types), including its scale,
  kill -9 and concurrency tests.
- The admin flows were checked by browser at 960 pixels with device scale 2 (200% zoom), on Apache and on Velocity,
  in the admin and admin4 designs:
  - a small remove and copy have the same pages, fields and results as before (with the new blocks set aside);
  - remove and copy of 201 nodes through the progress page to the redirect, cancel and "Remove the partial copy",
    kill -9 of a worker and Resume, the refusal of an overlapping operation, the jobs list;
  - one copy or remove of a real section ("Fit & Healthy");
  - hide and reveal (small: the old flow without a page; large: the confirmation, a job, and "now"), move small with
    "now" and large with "background" (old and new place shown), section assignment as a job and "now";
  - a subtree state post refused with the reason (now and background) and the old path without the option;
    removing one location (the old path) and two with "background" (confirmation, job, both gone); adding a
    location for one item "now" and for two "background" (job, both placed);
  - the `state` job with a state the user may assign, through a mock type registered in its own process only
    (the test installation has no assignable state; `ez_lock` cannot be assigned): the choice, the lock while the
    job waits, the job done with every object handed over, the lock released and the state links unchanged;
  - the worked example of a job type above;
  - no view answers 5xx on either server.
- After each removal a report confirmed that nothing was left behind, and after each copy a fingerprint confirmed
  that the source subtree was unchanged.

## Related pages

- [Content jobs (feature)](../../features/6.0/content-jobs.md)
- [Behaviour changes of 1 and 2 October 2026](behaviour-changes-2026-10.md)
- [Exponential Console](console.md)
- [Content model and editing guide](../../guides/content-model-and-editing.md)
