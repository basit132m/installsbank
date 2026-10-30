# Install tracking — integration guide (for the advertiser)

Thanks for running a campaign with us. To count real installs **without any
wrapper or extra download**, add a single HTTPS call to your installer that fires
once the install completes. You then build and **sign your installer as usual**,
so there is no antivirus/SmartScreen warning for your users.

## What to add

One fire-and-forget HTTPS GET at the end of installation:

```
https://installsbank.com/app/hit?c=YOUR_CAMPAIGN_CODE&e=installed
```

- Replace **`YOUR_CAMPAIGN_CODE`** with the code we give you for this campaign.
- It sends nothing but the install event. Country, device and OS are derived on
  our side from the request — no personal data is collected by this call.
- It's counted **once per user per day** (deduped by IP), and only while the
  campaign is active and under its install target.
- It's best-effort: wrap it so a failure never affects your installer.

Optionally add `&os=Windows&osver=10&dev=desktop` if you want to pass those
yourself; otherwise we detect them.

## Snippets by installer type

### Inno Setup  (`[Code]` section)
```pascal
procedure PingInstall();
var
  WinHttp: Variant;
begin
  try
    WinHttp := CreateOleObject('WinHttp.WinHttpRequest.5.1');
    WinHttp.Open('GET', 'https://installsbank.com/app/hit?c=YOUR_CAMPAIGN_CODE&e=installed', False);
    WinHttp.Send();
  except
    // ignore — never block the install
  end;
end;

procedure CurStepChanged(CurStep: TSetupStep);
begin
  if CurStep = ssPostInstall then
    PingInstall();
end;
```

### NSIS  (needs the free `inetc` plugin)
```nsis
Section
  inetc::get /SILENT "https://installsbank.com/app/hit?c=YOUR_CAMPAIGN_CODE&e=installed" "$PLUGINSDIR\ib.txt" /END
SectionEnd
```

### MSI / WiX  (deferred custom action, runs hidden)
```
powershell -WindowStyle Hidden -Command "try{Invoke-WebRequest -UseBasicParsing 'https://installsbank.com/app/hit?c=YOUR_CAMPAIGN_CODE&e=installed'}catch{}"
```

### Any installer that can run a command on finish (or a .bat)
Windows 10/11 ships `curl`:
```bat
curl -s "https://installsbank.com/app/hit?c=YOUR_CAMPAIGN_CODE&e=installed"
```

### Inside the app itself (if you'd rather ping on first run)
C#:
```csharp
try { _ = new System.Net.Http.HttpClient()
        .GetAsync("https://installsbank.com/app/hit?c=YOUR_CAMPAIGN_CODE&e=installed"); }
catch { }
```

## After adding it
1. Rebuild your installer.
2. **Sign it with your code-signing certificate** (as you normally do).
3. Send us the signed installer (or host it) — that's what users download.

That's it: your users get your trusted, signed file with no warning, and we see
each install in real time. The call does nothing else and can be left in safely.
