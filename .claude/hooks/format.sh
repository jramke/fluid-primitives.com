#!/usr/bin/env bash
# PostToolUse hook: formats the file Claude just wrote or edited, inside ddev
# (PHP with mago, everything else with prettier, which skips unknown file types).
f=$(jq -r '.tool_input.file_path // empty')
root=${CLAUDE_PROJECT_DIR:-$PWD}

[ -f "$f" ] || exit 0
case "$f" in
    "$root"/*) rel=${f#"$root"/} ;;
    *) exit 0 ;;
esac

case "$rel" in
    *.php) ddev composer format -- "$rel" ;;
    *) ddev npm run format -- "$rel" ;;
esac >/dev/null 2>&1 || true
