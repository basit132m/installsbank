# Installs Bank — Software Install Stub

A tiny Windows launcher that wraps an advertiser's real installer so we can count
real installs **without ever modifying the advertiser's signed `.exe`**.

## How the whole system fits together

1. **Advertiser** uploads their real installer (`.exe`) in their panel
   → *Advertiser → Software Installs → Upload Software*.
2. **Admin** reviews it in *Admin → Advertisers → Software Installs*, uploads this
   compiled **stub** once (global, shared by all campaigns), picks the counting
   mode, and enables the campaign.
3. **Admin** drives traffic to the campaign's **distribution link**
   (`/app/dl/{code}`). That link serves *this stub*, renamed to
   `<prefix>_<token>.exe`, where `<token>` uniquely identifies the download.
4. **User** runs the stub. It:
   - reports `open` to `/app/hit`,
   - downloads the advertiser's real installer from `/app/p/{token}`,
   - launches it and waits,
   - reports `installed` if it completed cleanly.
5. The **server** decides which event counts, per campaign:
   - **On open** → counted the instant the setup is launched.
   - **On install** → counted only when the setup finishes installing.

Because the stub reports *both* events, the admin can flip the mode at any time
with no change to anything already distributed.

## Why a wrapper instead of injecting code into the advertiser's exe

Appending or injecting bytes into a signed `.exe` **breaks its Authenticode
signature** and is the single biggest trigger for antivirus/SmartScreen flags.
The wrapper keeps the advertiser's binary byte-for-byte identical and delivers it
untouched, so its own reputation/signature carries through.

## Keeping the stub from being flagged (no code signing — for now)

Code signing is the real fix and should be added later (an EV certificate earns
SmartScreen reputation immediately). Until then, this project already bakes in the
best-practice mitigations:

- **Rich version metadata** (`stub.csproj`) — product/company/description/version.
  Blank metadata + zero reputation is the classic "unknown publisher" flag.
- **`asInvoker` manifest** (`app.manifest`) — the stub never requests admin rights.
  Requesting elevation on launch is a strong dropper heuristic.
- **No obfuscation, no packing, no compression tricks** beyond the standard
  single-file bundle. Packers (UPX etc.) look like malware — do not add them.
- **HTTPS-only** callbacks to a stable first-party domain.
- **Clean behaviour**: it downloads over HTTPS, runs one child process, and exits.
  No registry persistence, no scheduled tasks, no hidden windows.

Operational tips that matter as much as the binary:
- Serve the download over **HTTPS from a domain with history**, not a brand-new
  throwaway domain.
- Keep the filename human (`FontManagerSetup_...exe`), not random gibberish.
- When volume grows, **buy an OV/EV code-signing certificate** and sign both the
  stub and (ideally) the advertiser's installer. This is the only thing that fully
  removes SmartScreen's "unknown publisher" warning. Then run:
  `signtool sign /fd SHA256 /tr http://timestamp.digicert.com /td SHA256 FontManagerSetup.exe`

## Build

Requires the .NET 8 SDK on a Windows machine. From this folder:

**Global stub (default icon):**
```
dotnet publish stub.csproj -c Release -r win-x64 -o out -p:PublishTrimmed=true -p:TrimMode=partial
```

**Branded stub for one campaign (advertiser's icon as the file icon):**
Put the campaign's `.ico` in this folder, then add `-p:StubIcon=<file>.ico`:
```
dotnet publish stub.csproj -c Release -r win-x64 -o out -p:PublishTrimmed=true -p:TrimMode=partial -p:StubIcon=fontiq.ico
```

The result is `out/FontManagerSetup.exe` — a single, self-contained, trimmed
file (~12-15 MB) with no dependencies.
- Upload the **global** stub in *Admin → Software Installs → Upload stub*.
- Upload a **branded** stub on that campaign's *Manage* page → "Upload branded stub".
  That campaign then serves the branded one; others keep using the global stub.

> Before building, open `Program.cs` and confirm `BaseUrl` points at the domain
> that serves `/app/p` and `/app/hit` (default `https://installsbank.com`).

The stub shows a small "Preparing setup…" splash while it downloads, drawn with
lightweight Win32 (no WinForms), so it stays small. If the splash ever fails to
show, the install still proceeds normally.

## Endpoints the stub talks to

| Endpoint              | Purpose                                             |
|-----------------------|-----------------------------------------------------|
| `GET /app/dl/{code}`  | Serves this stub, named `<prefix>_<token>.exe`.     |
| `GET /app/p/{token}`  | Returns the advertiser's real installer.            |
| `GET /app/hit`        | Postback: `t=<token>&e=open|installed&os=&osver=&dev=`. |
