#!/usr/bin/env bash
set -euo pipefail

# Simple API test script for PrestaSys backend
# Usage:
#   export API_BASE="https://gabrielfh.duckdns.org/api.php"   # optional (defaults to local dev path)
#   export TEST_USER="admin"                                  # optional
#   export TEST_PASS="secret"                                 # optional
#   bash scripts/test_api.sh

BASE_URL=${API_BASE:-"https://gabrielfh.duckdns.org/api.php"}
COOKIE_FILE=$(mktemp)

curl_i() {
  # curl with headers and HTTP code at the end
  curl -sS -i "$@" | sed -e '$a\'
}

echo "== Healthcheck =="
curl_i "$BASE_URL?action=health" || true
echo

echo "== Login with GET (expect 405) =="
curl_i "$BASE_URL?action=login" || true
echo

echo "== Login with POST but wrong Content-Type (expect 415) =="
curl_i -X POST "$BASE_URL?action=login" -d '{}' || true
echo

echo "== Login with invalid JSON (expect 400 INVALID_JSON) =="
curl_i -X POST "$BASE_URL?action=login" -H 'Content-Type: application/json' -d '{"usuario": "admn"' || true
echo

echo "== Login with missing fields (expect 400 MISSING_FIELDS) =="
curl_i -X POST "$BASE_URL?action=login" -H 'Content-Type: application/json' -d '{}' || true
echo

echo "== Login with short fields (expect 422 VALIDATION_ERROR) =="
curl_i -X POST "$BASE_URL?action=login" -H 'Content-Type: application/json' -d '{"usuario":"ad","password":"12345"}' || true
echo

if [[ -n "${TEST_USER:-}" ]]; then
  echo "== Login wrong password (expect 401 PASSWORD_INCORRECT) =="
  curl_i -X POST "$BASE_URL?action=login" -H 'Content-Type: application/json' -d "{\"usuario\":\"$TEST_USER\",\"password\":\"wrongpass123\"}" || true
  echo

  echo "== Login correct (expect 200) =="
  curl -sS -i -c "$COOKIE_FILE" -X POST "$BASE_URL?action=login" -H 'Content-Type: application/json' -d "{\"usuario\":\"$TEST_USER\",\"password\":\"${TEST_PASS:-}\"}" | sed -e '$a\'
  echo

  echo "== Get session (expect 200 with user) =="
  curl -sS -i -b "$COOKIE_FILE" "$BASE_URL?action=get_session" | sed -e '$a\'
  echo

  echo "== Logout (expect 200) =="
  curl -sS -i -b "$COOKIE_FILE" -X POST "$BASE_URL?action=logout" | sed -e '$a\'
  echo

  echo "== Get session after logout (expect 401) =="
  curl -sS -i -b "$COOKIE_FILE" "$BASE_URL?action=get_session" | sed -e '$a\'
  echo
fi

rm -f "$COOKIE_FILE"
echo "Done."

