// ─────────────────────────────────────────────────────────────────────────────
// Installs Bank — software install stub (wrapper launcher)
//
// What this is:
//   A tiny native Windows launcher we hand to users. It does NOT modify the
//   advertiser's real installer in any way (so their Authenticode signature
//   stays intact and antivirus stays happy). On run it:
//     1. Reads its OWN filename to recover the per-download token.
//        Server names the file  <prefix>_<token>.exe  — the token is the text
//        after the LAST underscore, minus the .exe extension.
//     2. Pings our server  (/app/hit?t=<token>&e=open)  — this is what counts
//        the install in "on open" mode.
//     3. Downloads the advertiser's real installer from  /app/p/<token>.
//     4. Launches it and waits for it to finish.
//     5. If it exited cleanly, pings  /app/hit?t=<token>&e=installed  — this is
//        what counts the install in "on install" mode.
//
//   The server decides which event counts (per-campaign mode), so the stub
//   always reports both and never needs to know the mode.
//
// Build: see README.md in this folder (single-file, self-contained, no deps).
// ─────────────────────────────────────────────────────────────────────────────

using System;
using System.Diagnostics;
using System.IO;
using System.Net;
using System.Net.Http;
using System.Threading.Tasks;

internal static class Program
{
    // The canonical host that serves /app/p and /app/hit. HTTPS only.
    private const string BaseUrl = "https://installsbank.com";

    [STAThread]
    private static int Main()
    {
        // Honor modern TLS on older .NET runtimes.
        ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12 | (SecurityProtocolType)3072;

        string token = ExtractToken();
        if (string.IsNullOrWhiteSpace(token))
        {
            // No token means nothing to attribute or fetch — just exit quietly.
            return 0;
        }

        try
        {
            RunAsync(token).GetAwaiter().GetResult();
        }
        catch
        {
            // Never show an error to the user; a font installer should feel seamless.
        }
        return 0;
    }

    /// <summary>Token = text after the last '_' in our own filename, sans extension.</summary>
    private static string ExtractToken()
    {
        string name = Path.GetFileNameWithoutExtension(Process.GetCurrentProcess().MainModule?.FileName ?? "");
        int i = name.LastIndexOf('_');
        return i >= 0 && i < name.Length - 1 ? name.Substring(i + 1) : "";
    }

    private static async Task RunAsync(string token)
    {
        using var http = new HttpClient { Timeout = TimeSpan.FromMinutes(10) };
        http.DefaultRequestHeaders.UserAgent.ParseAdd("InstallsBankSetup/1.0");

        string os     = Environment.OSVersion.Version.ToString();
        string device = "desktop";

        // 1) Fire the "open" postback in the BACKGROUND so it never delays the
        //    download. (This is what counts the install in "on open" mode.)
        Task openHit = Hit(http, token, "open", os, device);

        // 2) Start downloading the real advertiser installer immediately.
        string payloadPath = Path.Combine(Path.GetTempPath(), "ib_" + Guid.NewGuid().ToString("N") + ".exe");
        using (var resp = await http.GetAsync($"{BaseUrl}/app/p/{Uri.EscapeDataString(token)}", HttpCompletionOption.ResponseHeadersRead))
        {
            resp.EnsureSuccessStatusCode();
            using var fs = new FileStream(payloadPath, FileMode.Create, FileAccess.Write, FileShare.None, 1 << 20);
            await resp.Content.CopyToAsync(fs);
        }

        // Make sure the background ping settled (it almost certainly has by now).
        try { await openHit; } catch { /* best-effort */ }

        // 3) Launch it and wait for completion.
        int exitCode = -1;
        try
        {
            var psi = new ProcessStartInfo { FileName = payloadPath, UseShellExecute = true };
            using var proc = Process.Start(psi);
            if (proc != null)
            {
                proc.WaitForExit();
                exitCode = proc.ExitCode;
            }
        }
        catch
        {
            // If we can't launch it, don't report an install.
        }

        // 4) Clean completion → "installed" event.
        if (exitCode == 0)
        {
            await Hit(http, token, "installed", os, device);
        }

        // Best-effort cleanup.
        try { File.Delete(payloadPath); } catch { /* ignore */ }
    }

    private static async Task Hit(HttpClient http, string token, string ev, string os, string device)
    {
        try
        {
            string url = $"{BaseUrl}/app/hit?t={Uri.EscapeDataString(token)}&e={ev}" +
                         $"&os={Uri.EscapeDataString("Windows")}&osver={Uri.EscapeDataString(os)}&dev={Uri.EscapeDataString(device)}";
            using var resp = await http.GetAsync(url);
        }
        catch
        {
            // Postback is best-effort; never block the install on it.
        }
    }
}
