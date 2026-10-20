---
title: "Lesson 2.2: Editors, practical task"
audience: trainer, editor
category: training
order: 22
summary: A 45-minute practical task for the end of Editors 101, build a small news section with a folder, two articles, an uploaded image and a translation, restore an earlier version and an item from the trash, scored with a five-criterion rubric. 70 minutes with scoring.
keywords: assessments and practical exercises, editors 101, practical task, rubric, folder, article, upload, alternative text, translation, manage versions, trash, restore
type: help
---
# Lesson 2.2: Editors, practical task

The written test checks understanding; this task checks that the learner can do the work. In 45 minutes each learner
builds a small news section on their own training installation, the way an editor would on a first working day, and
repairs two mistakes on purpose.

> **Note**
> This is the trainer's own assessment, not an official certification. Use a training installation, never a live
> site, and learner accounts at `example.invalid`.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 2: Assessing the editorial courses](README.md) |
| Duration | 70 minutes (5 briefing, 45 task, 20 scoring) |
| Level | Beginner |
| Audience | Learners at the end of [Editors 101](../../editors-101/README.md) |
| Prerequisites | [Lesson 2.1](editors-written-test.md); an Editor account on a training installation |

## Learning objectives

Learners who pass this task have shown that they can:

- create a folder and articles in the right place and publish them;
- upload an image with a title and an alternative text and use it in an article;
- translate an article into a second language;
- restore an earlier version of an article;
- restore a removed item from the trash.

## Trainer's notes

### How to run it

1. **Before**: each learner has a training installation (or a shared one with one course folder per learner), an
   Editor account, a second content language set up on the site, and a small image file on their machine. Check that
   the Editor role may restore from the trash on your installation.
2. **Briefing**: hand out the steps and the rubric together. 45 minutes. The help may be open.
3. **Scoring**: open each learner's folder in the administration and fill in the rubric's evidence while the
   installation is still there.

### Common questions

**What if the site has no second language?** Leave out step 7 and its criterion, and change the pass rule to the
remaining points.

**May learners create the image in the media library instead of next to the article?** Yes. The task asks for the
result.

### Pitfalls

- **Learners working in someone else's folder.** On a shared site, give each learner a folder named after their
  account and say which one.
- **No image file.** Provide one, free of rights issues, in the lab files.

## Hands-on exercise

The task, as handed to learners. Use your own training installation and your Editor account.

1. Below **Training** (or the folder your trainer names), create a **Folder** named `News <your account name>` and
   publish it.
2. In that folder, create an **Article** titled `Opening of the new office`. Fill in **Intro** and **Body** with two
   sentences each and publish it.
3. Upload the image file your trainer gave you into your folder (use **Upload** on the folder's page). Give it the
   title `Office entrance` and an alternative text that says what it shows.
4. Edit the article and add the image to its **Image** field. Publish.
5. Create a second article titled `Team lunch on Friday`, with **Intro** and **Body**, and publish it.
6. Edit `Opening of the new office` once more: change the title to `Opening postponed` and publish.
7. Translate `Team lunch on Friday` into the second language of the site and publish the translation.
8. You changed your mind: bring back the title `Opening of the new office` from the earlier version, and publish it.
9. Remove `Team lunch on Friday`. Then restore it from the trash to its original place.
10. Tell the trainer you are done.

## Expected result

- The learner's folder holds two published articles and the image item.
- `Opening of the new office` is published with that title again, at a version number higher than in step 6, and
  shows the image.
- `Team lunch on Friday` is back in its folder and has two translations (filled badges for both languages).
- The image item has a title and an alternative text.
- The trainer checks each point in the administration: the folder's **Sub items**, the article's **Manage versions**
  page, the language badges and the image's page.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Structure | No folder, or items elsewhere | Folder exists, items partly outside it | All items in the folder, one name wrong | Folder and both articles in place, names as asked | The folder's **Sub items** |
| Articles and required fields | Nothing published | One article published | Both published, one field empty | Both published with Intro and Body | Each article's page |
| Image | Not uploaded | Uploaded, not used | Used, title or alternative text missing | Used, with title and alternative text | The image's page and the article |
| Translation | None | Draft only | Published, text not translated | Published second translation | The language badges of the item |
| Restore (must) | Neither restore done | One of the two | Both, one in the wrong way (for example retyped the title) | Version restored via **Manage versions** and item restored from the trash | **Manage versions** page; the item's place in the tree |

**Pass**: 10 of 15 points or more, and **Restore** at 2 or 3. Restoring is the skill that saves content in real
work. Without a second language, the pass mark is 8 of 12.

## Quiz

Debrief questions to ask after scoring.

1. In step 8, why is a new draft made from the old version better than retyping the old title?
2. What would have happened to `Team lunch on Friday` if you had pressed **Empty trash** before step 9?
3. Where did the image item end up, and could another editor find it again?
4. After step 7, which translation is the main language of `Team lunch on Friday`?

<details>
<summary>Answers</summary>

1. It brings back every field of that version exactly, not only the title, and the history shows where it came from
   ([Publishing, versions and the trash](../../../publishing-and-versions.md#go-back-to-an-earlier-version)).
2. It would be deleted for good; **Empty trash** cannot be undone
   ([Publishing, versions and the trash](../../../publishing-and-versions.md#the-trash)).
3. In the learner's folder; others find it by title with the search
   ([Media and uploads](../../../media-and-uploads.md#good-practice)).
4. The language it was created in ([Languages](../../../languages.md#the-main-language)).

</details>

## Further reading

- [Building scoring rubrics](../module-01-designing-assessments/building-scoring-rubrics.md): how this rubric was
  made.
- [Media and uploads](../../../media-and-uploads.md): uploading and alternative texts.
- [Languages](../../../languages.md): translations and the main language.

## Related pages

- [Lesson 2.1: Editors, written test](editors-written-test.md)
- [Module 2: Assessing the editorial courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
