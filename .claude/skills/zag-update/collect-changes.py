#!/usr/bin/env python3
"""Collect what changed in Zag.js between two releases, as raw material for the zag-update report.

Usage: collect-changes.py --repo ~/personal/dev/zag --from @zag-js/core@2.0.0-next.3 --to @zag-js/core@2.0.0-next.4 \
           --lib packages/fluid-primitives/package.json --out <dir>

Both refs are release tags of a zag checkout (`git -C <repo> fetch upstream --tags` first). Writes into <dir>:
  packages.txt     machines and packages added or removed between the releases
  used.txt         CHANGELOG lines of the packages the library depends on (directly or through other zag packages)
  other.txt        CHANGELOG lines of every other package
  commits.txt      the commits worth reading (no docs, chore, test, ci, website or examples)

Nothing is filtered away that could matter: CHANGELOG lines only lose the "Updated dependencies" noise.
"""

import argparse
import glob
import json
import os
import re
import subprocess


def sh(repo, *args, check=False):
    return subprocess.run(['git', '-C', repo, *args], capture_output=True, text=True, check=check).stdout


def package_names(repo, rev):
    """name -> path of every package.json below packages/ at a revision"""
    found = {}
    for path in sh(repo, 'ls-tree', '-r', '--name-only', rev).split('\n'):
        if not path.startswith('packages/') or not path.endswith('/package.json') or 'node_modules' in path:
            continue
        try:
            found[json.loads(sh(repo, 'show', f'{rev}:{path}'))['name']] = path
        except (ValueError, KeyError):
            pass
    return found


def used_closure(repo, to, lib_package_json):
    """the zag packages the library depends on, plus the zag packages those depend on"""
    used = {n for n in json.load(open(lib_package_json)).get('dependencies', {}) if n.startswith('@zag-js/')}
    deps = {}
    for name, path in package_names(repo, to).items():
        manifest = json.loads(sh(repo, 'show', f'{to}:{path}'))
        deps[name] = {d for d in manifest.get('dependencies', {}) if d.startswith('@zag-js/')}
    seen, todo = set(used), list(used)
    while todo:
        for dep in deps.get(todo.pop(), ()):
            if dep not in seen:
                seen.add(dep)
                todo.append(dep)
    return used, seen, package_names(repo, to)


def changelog_sections(repo, frm, to, names_by_path):
    sections = {}
    for path in sh(repo, 'diff', '--name-only', frm, to, '--', '*CHANGELOG.md').split():
        package_json = os.path.join(os.path.dirname(path), 'package.json')
        name = names_by_path.get(package_json, path)
        diff = sh(repo, 'diff', '-U0', frm, to, '--', path)
        added = [l[1:] for l in diff.split('\n') if l.startswith('+') and not l.startswith('+++')]
        added = [l for l in added if l.strip() and 'Updated dependencies' not in l and not re.match(r'\s*- @zag-js/[\w-]+@', l)]
        sections[name] = added
    return sections


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--repo', required=True)
    ap.add_argument('--from', dest='frm', required=True)
    ap.add_argument('--to', required=True)
    ap.add_argument('--lib', required=True)
    ap.add_argument('--out', required=True)
    args = ap.parse_args()
    repo = os.path.expanduser(args.repo)
    os.makedirs(args.out, exist_ok=True)

    before, after = package_names(repo, args.frm), package_names(repo, args.to)
    with open(f'{args.out}/packages.txt', 'w') as f:
        f.write('added: ' + ', '.join(sorted(set(after) - set(before))) + '\n')
        f.write('removed: ' + ', '.join(sorted(set(before) - set(after))) + '\n')
        f.write('machines at --to: ' + ', '.join(sorted(os.path.basename(os.path.dirname(p)) for p in after.values() if p.startswith('packages/machines/'))) + '\n')

    used, closure, names = used_closure(repo, args.to, os.path.expanduser(args.lib))
    names_by_path = {path: name for name, path in names.items()}
    sections = changelog_sections(repo, args.frm, args.to, names_by_path)
    with open(f'{args.out}/used.txt', 'w') as f_used, open(f'{args.out}/other.txt', 'w') as f_other:
        for name, lines in sorted(sections.items()):
            label = 'direct' if name in used else 'transitive' if name in closure else 'other'
            target = f_used if name in closure else f_other
            target.write(f'\n##### {name} [{label}]\n' + '\n'.join(lines) + '\n')

    skip = re.compile(r'^[0-9a-f]+ (docs|chore|test|ci|build|style)(\(|:|!)|Version Packages|\((website|examples)\)|^[0-9a-f]+ \w+\((website|examples)')
    log = sh(repo, 'log', '--no-merges', '--format=%h %s', f'{args.frm}..{args.to}').split('\n')
    with open(f'{args.out}/commits.txt', 'w') as f:
        f.write('\n'.join(l for l in log if l and not skip.search(l)) + '\n')

    print(f'Wrote {args.out}: {len(sections)} changelogs, {len(closure)} packages in the closure of the library')


if __name__ == '__main__':
    main()
