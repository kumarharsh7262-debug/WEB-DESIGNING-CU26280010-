#!/usr/bin/env bash
# Q20 – Create a venv and install dependencies.
# Usage: bash setup_venv.sh [target_dir]    (default: ./venv)
set -euo pipefail

TARGET="${1:-venv}"
REQ="$(dirname "$0")/../requirements.txt"

python3 -m venv "$TARGET"
"$TARGET/bin/python" -m pip install --upgrade pip
"$TARGET/bin/pip" install -r "$REQ"

echo
echo "venv ready at: $(cd "$TARGET" && pwd)"
echo "Interpreter  : $(cd "$TARGET" && pwd)/bin/python"
echo "Installed    :"
"$TARGET/bin/pip" list --format=freeze | grep -i -E "mysql|pymysql|sqlalchemy" || true
