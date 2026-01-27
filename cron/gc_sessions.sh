#!/bin/bash
# Strict mode: exit on error (-e), undefined variables (-u), pipeline failures (-o pipefail)
# Ensures the script stops immediately if any command fails or uses undefined variables
set -euo pipefail

SESSION_PATH="${SESSION_PATH:-/var/lib/php/sessions}"
MAX_LIFETIME="${SESSION_MAX_LIFETIME:-3600}"
start_ts=$(date -u +%Y-%m-%dT%H:%M:%SZ)

# Check if session path exists. If not, log and exit gracefully.
if [ ! -d "$SESSION_PATH" ]; then
  echo "$start_ts session-gc: path not found: $SESSION_PATH" >&2
  exit 0
fi

# Convert lifetime to minutes for find
cutoff_minutes=$(( MAX_LIFETIME / 60 ))
removed=0

# Remove expired session files based on mtime
while IFS= read -r file; do
  if [ -n "$file" ]; then
    rm -f "$file" && removed=$((removed + 1))
  fi
done <<EOF
$(find "$SESSION_PATH" -type f -name 'sess_*' -mmin "+${cutoff_minutes}" -print 2>/dev/null)
EOF

# Log only if sessions were actually removed
if [ "$removed" -gt 0 ]; then
  echo "$start_ts session-gc: removed_sessions=$removed path=$SESSION_PATH lifetime_s=$MAX_LIFETIME"
fi
