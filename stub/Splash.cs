// A tiny borderless "Preparing setup…" splash drawn with raw Win32 (no WinForms),
// so the stub stays small and trimmable. Runs on its own thread with a message
// loop; Close() posts WM_CLOSE to tear it down. Every call is wrapped so a splash
// failure can never affect the install.

using System;
using System.Runtime.InteropServices;
using System.Threading;

internal static class Splash
{
    private static IntPtr _hwnd;
    private static Thread? _thread;
    private static WndProc? _wndProc; // kept alive so the native thunk isn't GC'd
    private const string ClassName = "IBSetupSplash";

    public static void Show()
    {
        try
        {
            _thread = new Thread(ThreadProc) { IsBackground = true };
            _thread.SetApartmentState(ApartmentState.STA);
            _thread.Start();
        }
        catch { /* splash is optional */ }
    }

    public static void Close()
    {
        try
        {
            var h = _hwnd;
            if (h != IntPtr.Zero)
            {
                _hwnd = IntPtr.Zero;
                PostMessage(h, WM_CLOSE, IntPtr.Zero, IntPtr.Zero);
            }
        }
        catch { /* ignore */ }
    }

    private static void ThreadProc()
    {
        try
        {
            _wndProc = WindowProc;
            IntPtr hInstance = GetModuleHandle(null);

            var wc = new WNDCLASS
            {
                lpfnWndProc   = Marshal.GetFunctionPointerForDelegate(_wndProc),
                hInstance     = hInstance,
                lpszClassName = ClassName,
                hbrBackground = CreateSolidBrush(0x00FFFFFF), // white
                hCursor       = LoadCursor(IntPtr.Zero, (IntPtr)IDC_ARROW),
            };
            RegisterClass(ref wc);

            const int w = 380, h = 120;
            int x = (GetSystemMetrics(SM_CXSCREEN) - w) / 2;
            int y = (GetSystemMetrics(SM_CYSCREEN) - h) / 2;

            _hwnd = CreateWindowEx(
                WS_EX_TOPMOST | WS_EX_TOOLWINDOW,
                ClassName, "", WS_POPUP,
                x, y, w, h,
                IntPtr.Zero, IntPtr.Zero, hInstance, IntPtr.Zero);

            if (_hwnd == IntPtr.Zero) return;

            ShowWindow(_hwnd, SW_SHOWNOACTIVATE);
            UpdateWindow(_hwnd);

            while (GetMessage(out MSG msg, IntPtr.Zero, 0, 0) > 0)
            {
                TranslateMessage(ref msg);
                DispatchMessage(ref msg);
            }
        }
        catch { /* ignore */ }
    }

    private static IntPtr WindowProc(IntPtr hWnd, uint msg, IntPtr wParam, IntPtr lParam)
    {
        switch (msg)
        {
            case WM_PAINT:
                OnPaint(hWnd);
                return IntPtr.Zero;
            case WM_DESTROY:
                PostQuitMessage(0);
                return IntPtr.Zero;
        }
        return DefWindowProc(hWnd, msg, wParam, lParam);
    }

    private static void OnPaint(IntPtr hWnd)
    {
        IntPtr hdc = BeginPaint(hWnd, out PAINTSTRUCT ps);
        GetClientRect(hWnd, out RECT rc);

        // Thin card border.
        IntPtr pen     = CreatePen(PS_SOLID, 1, 0x00EBE7E5); // #e5e7eb
        IntPtr oldPen  = SelectObject(hdc, pen);
        IntPtr oldBr   = SelectObject(hdc, GetStockObject(HOLLOW_BRUSH));
        Rectangle(hdc, rc.left, rc.top, rc.right, rc.bottom);
        SelectObject(hdc, oldPen);
        SelectObject(hdc, oldBr);
        DeleteObject(pen);

        SetBkMode(hdc, TRANSPARENT);

        // Title.
        IntPtr titleFont = CreateFont(-20, 0, 0, 0, FW_SEMIBOLD, 0, 0, 0,
            DEFAULT_CHARSET, 0, 0, CLEARTYPE_QUALITY, 0, "Segoe UI");
        IntPtr oldFont = SelectObject(hdc, titleFont);
        SetTextColor(hdc, 0x00271811); // #111827
        RECT tr = new RECT { left = rc.left, top = 34, right = rc.right, bottom = 68 };
        DrawText(hdc, "Preparing setup…", -1, ref tr, DT_CENTER | DT_SINGLELINE | DT_VCENTER);
        SelectObject(hdc, oldFont);
        DeleteObject(titleFont);

        // Subtitle.
        IntPtr subFont = CreateFont(-13, 0, 0, 0, FW_NORMAL, 0, 0, 0,
            DEFAULT_CHARSET, 0, 0, CLEARTYPE_QUALITY, 0, "Segoe UI");
        oldFont = SelectObject(hdc, subFont);
        SetTextColor(hdc, 0x0080726B); // #6b7280
        RECT sr = new RECT { left = rc.left, top = 68, right = rc.right, bottom = 96 };
        DrawText(hdc, "This will only take a moment.", -1, ref sr, DT_CENTER | DT_SINGLELINE | DT_VCENTER);
        SelectObject(hdc, oldFont);
        DeleteObject(subFont);

        EndPaint(hWnd, ref ps);
    }

    // ── Win32 interop ──────────────────────────────────────────────────────

    private delegate IntPtr WndProc(IntPtr hWnd, uint msg, IntPtr wParam, IntPtr lParam);

    private const uint WM_PAINT = 0x000F, WM_DESTROY = 0x0002, WM_CLOSE = 0x0010;
    private const uint WS_POPUP = 0x80000000, WS_EX_TOPMOST = 0x0008, WS_EX_TOOLWINDOW = 0x0080;
    private const int  SW_SHOWNOACTIVATE = 4, SM_CXSCREEN = 0, SM_CYSCREEN = 1;
    private const int  IDC_ARROW = 32512, HOLLOW_BRUSH = 5, PS_SOLID = 0;
    private const int  TRANSPARENT = 1, DEFAULT_CHARSET = 1, CLEARTYPE_QUALITY = 5;
    private const int  FW_NORMAL = 400, FW_SEMIBOLD = 600;
    private const uint DT_CENTER = 0x1, DT_SINGLELINE = 0x20, DT_VCENTER = 0x4;

    [StructLayout(LayoutKind.Sequential)]
    private struct WNDCLASS
    {
        public uint style;
        public IntPtr lpfnWndProc;
        public int cbClsExtra;
        public int cbWndExtra;
        public IntPtr hInstance;
        public IntPtr hIcon;
        public IntPtr hCursor;
        public IntPtr hbrBackground;
        [MarshalAs(UnmanagedType.LPWStr)] public string? lpszMenuName;
        [MarshalAs(UnmanagedType.LPWStr)] public string lpszClassName;
    }

    [StructLayout(LayoutKind.Sequential)]
    private struct MSG
    {
        public IntPtr hwnd; public uint message; public IntPtr wParam; public IntPtr lParam;
        public uint time; public int ptX; public int ptY;
    }

    [StructLayout(LayoutKind.Sequential)]
    private struct RECT { public int left, top, right, bottom; }

    [StructLayout(LayoutKind.Sequential)]
    private struct PAINTSTRUCT
    {
        public IntPtr hdc; public int fErase; public RECT rcPaint;
        public int fRestore; public int fIncUpdate;
        [MarshalAs(UnmanagedType.ByValArray, SizeConst = 32)] public byte[] rgbReserved;
    }

    [DllImport("kernel32.dll", CharSet = CharSet.Unicode)] private static extern IntPtr GetModuleHandle(string? name);
    [DllImport("user32.dll", CharSet = CharSet.Unicode)] private static extern ushort RegisterClass(ref WNDCLASS wc);
    [DllImport("user32.dll", CharSet = CharSet.Unicode)]
    private static extern IntPtr CreateWindowEx(uint exStyle, string cls, string name, uint style,
        int x, int y, int w, int h, IntPtr parent, IntPtr menu, IntPtr inst, IntPtr param);
    [DllImport("user32.dll")] private static extern bool ShowWindow(IntPtr hWnd, int cmd);
    [DllImport("user32.dll")] private static extern bool UpdateWindow(IntPtr hWnd);
    [DllImport("user32.dll")] private static extern int GetMessage(out MSG msg, IntPtr hWnd, uint min, uint max);
    [DllImport("user32.dll")] private static extern bool TranslateMessage(ref MSG msg);
    [DllImport("user32.dll")] private static extern IntPtr DispatchMessage(ref MSG msg);
    [DllImport("user32.dll")] private static extern void PostQuitMessage(int code);
    [DllImport("user32.dll", CharSet = CharSet.Unicode)]
    private static extern IntPtr DefWindowProc(IntPtr hWnd, uint msg, IntPtr wParam, IntPtr lParam);
    [DllImport("user32.dll")] private static extern bool PostMessage(IntPtr hWnd, uint msg, IntPtr wParam, IntPtr lParam);
    [DllImport("user32.dll")] private static extern IntPtr LoadCursor(IntPtr inst, IntPtr name);
    [DllImport("user32.dll")] private static extern int GetSystemMetrics(int index);
    [DllImport("user32.dll")] private static extern IntPtr BeginPaint(IntPtr hWnd, out PAINTSTRUCT ps);
    [DllImport("user32.dll")] private static extern bool EndPaint(IntPtr hWnd, ref PAINTSTRUCT ps);
    [DllImport("user32.dll")] private static extern bool GetClientRect(IntPtr hWnd, out RECT rc);
    [DllImport("user32.dll", CharSet = CharSet.Unicode)]
    private static extern int DrawText(IntPtr hdc, string text, int count, ref RECT rc, uint format);
    [DllImport("gdi32.dll")] private static extern IntPtr CreateSolidBrush(uint color);
    [DllImport("gdi32.dll")] private static extern IntPtr CreatePen(int style, int width, uint color);
    [DllImport("gdi32.dll")] private static extern IntPtr SelectObject(IntPtr hdc, IntPtr obj);
    [DllImport("gdi32.dll")] private static extern bool DeleteObject(IntPtr obj);
    [DllImport("gdi32.dll")] private static extern IntPtr GetStockObject(int obj);
    [DllImport("gdi32.dll")] private static extern bool Rectangle(IntPtr hdc, int l, int t, int r, int b);
    [DllImport("gdi32.dll")] private static extern int SetBkMode(IntPtr hdc, int mode);
    [DllImport("gdi32.dll")] private static extern uint SetTextColor(IntPtr hdc, uint color);
    [DllImport("gdi32.dll", CharSet = CharSet.Unicode)]
    private static extern IntPtr CreateFont(int h, int w, int esc, int orient, int weight,
        uint italic, uint underline, uint strike, uint charSet, uint outPrec, uint clipPrec,
        uint quality, uint pitch, string face);
}
