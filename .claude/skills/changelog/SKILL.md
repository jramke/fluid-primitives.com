---
name: changelog
description: 'Check whether a change to the library (packages/fluid-primitives) needs an entry in its CHANGELOG.md and write it. Invoke before every commit or PR that touches the library, and when asked to update the changelog.'
---

# Changelog

`packages/fluid-primitives/CHANGELOG.md` is the release notes of the library, shown on the docs site at
`/docs/releases`. It is a symlink target, so there is exactly one file. Entries go under `## Unreleased`.
Read [TONE_OF_VOICE.md](../../../TONE_OF_VOICE.md) first: "you", active voice, hyphen not em dash, specific names.

## 1. Decide

Look at what is about to be committed (`git -C packages/fluid-primitives diff --cached`, or `git show <sha>`
for a commit already made) and ask: **would someone using the library notice this?**

Needs an entry:

- A new component, ViewHelper, prop, option, client API or `ui:` command.
- A behaviour change, a fix of a bug a user could hit, a changed or clearer error message.
- Anything a user has to change in their project (rename, removed API, new required argument) - this is a **breaking change**.

No entry:

- Tests, lint/CI/tooling, formatting, refactors without a behaviour change, docblock or docs-only wording.
- Version bumps (`chore: version`), merge commits, reverts of unreleased work.
- A fix to something that is itself still under `Unreleased` - change that entry instead.

A commit that follows [CONTRIBUTING.md](../../../packages/fluid-primitives/CONTRIBUTING.md) already answers most
of this: `feat` and `fix` (and `perf`) usually need an entry, `docs`, `test`, `refactor`, `style`, `build`, `ci`
and `chore` usually do not. Check that against the question above, a `refactor` that changes an error message
still needs one. A `!` or a `BREAKING CHANGE:` footer is a breaking change.

When unsure, skip and say so in one line. An empty changelog entry is worse than a missing one.

## 2. Write

Add one bullet to `## Unreleased` under the heading of the commit scope, creating the heading when missing. The
table in [CONTRIBUTING.md](../../../packages/fluid-primitives/CONTRIBUTING.md) maps a commit scope to a heading.
Headings of a release, in order: `### Breaking changes`, `### General changes`, then one per scope in alphabetical
order (`### Select`, `` ### `ui:attributes` ``, `### Client`, `### CLI`, `### Core`).

- A `!` or `BREAKING CHANGE:` goes under Breaking changes only, not twice, whatever its scope.
- A component, a ViewHelper that is not part of one, `Client`, `CLI` or `Core`: the heading of that scope.
  A change to the registry copy of a component counts as the component.
- Several components or the package as a whole (packaging, translations, anything no single heading holds): General
  changes. Never copy one entry under several headings. When the commit has no scope, pick the heading from the diff,
  and ask when none fits instead of inventing one.
- Under Breaking changes the bullet starts with the bold scope, because the heading does not name it:
  `**Select, Combobox:** ...`, ``**`ui:attributes`:** ...``. The same goes for a General changes entry about
  specific components. An entry about the package as a whole has no prefix. Under any other heading the bullet does
  not repeat the scope.
- Features come before fixes inside a heading (`feat` first, `fix` and `perf` after), otherwise next to related entries.

- One sentence, two at most. Name the thing: the ViewHelper, the prop, the function, the exact error.
- Say what the user sees or must do ("call `extbase.post(...)` instead"), not how the code works.
- Describe the result, not the commit. "`ListCollection` accepts plain objects as items", not "Fix crash in getItemValue".
- Extend an existing bullet when the change continues one under `Unreleased`; do not add a second bullet for it.

Link the source at the end of the bullet, in parentheses:

- A change that lands through a pull request links the PR:
  `([#85](https://github.com/jramke/fluid-primitives/pull/85))`. Get the number with
  `gh pr view --json number -q .number`; when the PR does not exist yet, add the link once it does.
- A direct commit on `main` links the commit:
  ``([`04f1555`](https://github.com/jramke/fluid-primitives/commit/04f1555))``, the short sha of the commit
  that made the change. Several commits for one entry: link each.

## 3. Commit

A PR entry goes into the same commit as the change. A commit entry needs the sha of the commit it describes,
so it cannot be in that commit: commit the change first, then write the entry with
`git -C packages/fluid-primitives rev-parse --short HEAD` and commit `CHANGELOG.md` alone as
`docs: update changelog`. Never amend to add it (the sha would change). A changelog-only commit needs no entry.

## Release

Not part of this skill: `npm run version <x.y.z>` is followed by renaming `## Unreleased` to `## <x.y.z>`, a
`_YYYY-MM-DD_` line below it. Add a new `## Unreleased` with the next entry.
