@echo off
REM ─────────────────────────────────────────────────────────────────────────
REM  CODE-SIGNING CONFIG for the Installs Bank stub.
REM
REM  1. Copy this file and rename the copy to:  sign-config.bat
REM  2. Fill in your certificate details below.
REM  3. That's it — build.bat / build-branded.bat will sign every build.
REM
REM  (sign-config.bat is git-ignored, so your password/cert never gets committed.)
REM ─────────────────────────────────────────────────────────────────────────

REM Pick ONE signing method: "pfx" (a .pfx/.p12 file) or "store" (a cert
REM installed in Windows, incl. EV hardware tokens).
set "IB_SIGN_METHOD=pfx"

REM ── Option A: IB_SIGN_METHOD=pfx  (a certificate file + password) ──
set "IB_PFX_PATH=C:\certs\your-code-signing.pfx"
set "IB_PFX_PASS=your-pfx-password"

REM ── Option B: IB_SIGN_METHOD=store  (cert in the Windows store / EV token) ──
REM Use the cert's SHA1 thumbprint (40 hex chars, no spaces) OR its subject name.
set "IB_CERT_SHA1="
set "IB_CERT_SUBJECT=Your Company Name, Inc."

REM Timestamp server — leave as-is unless your CA tells you otherwise.
REM (Timestamping keeps the signature valid after the cert expires.)
set "IB_TIMESTAMP_URL=http://timestamp.digicert.com"
