socket="605759312.449121536"
channel="private-analysis"
secret="local"

sig=$(printf '%s:%s' "$socket" "$channel" \
      | openssl dgst -sha256 -hmac "$secret" -binary \
      | xxd -p -c 256)

echo "$sig"   # e.g. 3d4c0e2ce1b8…
