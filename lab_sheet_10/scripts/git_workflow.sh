#!/usr/bin/env bash
# Q1 + Q2: initialise Git, commit in logical steps, branch, create + resolve a merge conflict,
# and (optionally) push to GitHub.   Run from the project root:   bash scripts/git_workflow.sh
set -euo pipefail
cd "$(dirname "$0")/.."

git init -b main
git config user.name  "${GIT_AUTHOR_NAME:-Student}"
git config user.email "${GIT_AUTHOR_EMAIL:-student@example.com}"

# ---------- Q1: logical commits ----------
git add .gitignore README.md composer.json storage/logs/.gitkeep storage/uploads/.gitkeep docs
git commit -m "chore: add .gitignore, README, composer.json and docs"

git add config app/core
git commit -m "feat(core): add config, bootstrap, router, base controller/model and Database singleton"

git add app/models database
git commit -m "feat(models): add User and Product models and MySQL schema"

git add app/controllers app/views config/routes.php
git commit -m "feat(mvc): add controllers, views and routes (auth, products, API)"

git add public
git commit -m "feat(public): add front controller, .htaccess, CSS and JS API client"

git add apache scripts
git commit -m "chore(ops): add Apache vhost, SSL and git helper scripts"
git log --oneline

# ---------- Q2: feature branch ----------
git checkout -b feature/changelog-update
sed -i 's/^Status: .*/Status: feature branch - added product search and pagination/' docs/CHANGELOG.md
git commit -am "docs: describe the product search feature in the changelog"

# ---------- change the SAME line on main -> deliberate conflict ----------
git checkout main
sed -i 's/^Status: .*/Status: main branch - added API token authentication/' docs/CHANGELOG.md
git commit -am "docs: describe API authentication in the changelog"

echo "=== merging (a conflict is EXPECTED) ==="
git merge feature/changelog-update || true
git status --short            # shows:  UU docs/CHANGELOG.md
echo "--- conflicted file ---"; cat docs/CHANGELOG.md

# ---------- resolve: keep both statements, remove the conflict markers ----------
cat > docs/CHANGELOG.md <<'EOT'
# Changelog

Status: added API token authentication and product search with pagination
EOT
git add docs/CHANGELOG.md
git commit -m "merge: resolve changelog conflict between main and feature/changelog-update"
git log --oneline --graph --all

# ---------- push to GitHub (create an EMPTY repo on github.com first) ----------
# git remote add origin https://github.com/<your-username>/myproject.git
# git push -u origin main
# git push origin feature/changelog-update
echo "Done. Uncomment the last three git lines and put your GitHub URL to push."
