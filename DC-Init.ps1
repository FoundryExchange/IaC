
# 获取安装路径
$EdgePath = "C:\Program Files (x86)\Microsoft\Edge\Application\*.*\Installer\setup.exe"

# 查找安装目录
$EdgeInstaller = Get-ChildItem -Path $EdgePath -Recurse | Where-Object { $_.Name -eq "setup.exe" }

# 如果找到了 setup.exe，则执行卸载
if ($EdgeInstaller) {
    Start-Process -FilePath $EdgeInstaller.FullName -ArgumentList "--uninstall --system-level --force-uninstall" -NoNewWindow -Wait
} else {
    Write-Host "未找到 Microsoft Edge 安装文件"
}


Start-Job -ScriptBlock {
    # 下载 Gpg4win 4.4.0 安装程序
    $gpgUrl     = "https://files.gpg4win.org/gpg4win-4.4.0.exe"
    $outputPath = "$env:TEMP\gpg4win-4.4.0.exe"

    Invoke-WebRequest -Uri $gpgUrl -OutFile $outputPath

    # 静默安装（/S = silent，采用默认设置）
    Start-Process -FilePath $outputPath -ArgumentList "/S" -Wait

    # 清理安装文件
    Remove-Item $outputPath
}

Start-Job -ScriptBlock {
    $url = "https://github.com/keepassxreboot/keepassxc/releases/download/2.7.10/KeePassXC-2.7.10-Win64.msi"
    $outputPath = "$env:TEMP\KeePassXC-Setup.msi"
    Invoke-WebRequest -Uri $url -OutFile $outputPath
    Start-Process -FilePath "msiexec.exe" -ArgumentList "/i", "`"$outputPath`"", "/quiet", "/norestart" -Wait
    Remove-Item $outputPath
}



Start-Job -ScriptBlock {
    $url = "https://jaist.dl.sourceforge.net/project/keepass/KeePass%202.x/2.58/KeePass-2.58-Setup.exe?viasf=1"
    $outputPath = "$env:TEMP\KeePass-Setup.exe"
    Invoke-WebRequest -Uri $url -OutFile $outputPath
    Start-Process -FilePath $outputPath -ArgumentList "/silent" -Wait
    Remove-Item $outputPath
}


Start-Job -ScriptBlock {
    $telegramUrl = "https://telegram.org/dl/desktop/win64"
    $outputPath = "$env:TEMP\Telegram-Setup.exe"
    Invoke-WebRequest -Uri $telegramUrl -OutFile $outputPath
    Start-Process -FilePath $outputPath -ArgumentList "/SP- /VERYSILENT" -Wait
    Remove-Item $outputPath
}




Start-Job -ScriptBlock {
    Invoke-WebRequest -Uri "https://github.com/FoundryExchange/Share/raw/refs/heads/main/OfficeSetup.exe" -OutFile "$env:USERPROFILE\Downloads\OfficeSetup.exe"
    Start-Process "$env:USERPROFILE\Downloads\OfficeSetup.exe" -ArgumentList " " -Wait
}






$WshShell = New-Object -ComObject WScript.Shell
$Shortcut = $WshShell.CreateShortcut("$home\Desktop\restart.lnk")
$Shortcut.TargetPath = "C:\Windows\System32\shutdown.exe"
$Shortcut.Arguments = "-r -f -t 0"
$Shortcut.Save()

Set-ItemProperty -Path 'HKLM:\SOFTWARE\Microsoft\Active Setup\Installed Components\{A509B1A7-37EF-4b3f-8CFC-4F3A74704073}' -Name 'IsInstalled' -Value 0


Set-ItemProperty -Path 'HKLM:\SOFTWARE\Microsoft\Active Setup\Installed Components\{A509B1A8-37EF-4b3f-8CFC-4F3A74704073}' -Name 'IsInstalled' -Value 0

Set-TimeZone -Id "China Standard Time"


Start-Job -ScriptBlock {
    $thunderbirdUrl = "https://download.mozilla.org/?product=thunderbird-latest-ssl&os=win64&lang=en-US"
    $outputPath = "$env:TEMP\Thunderbird-Setup.exe"
    Invoke-WebRequest -Uri $thunderbirdUrl -OutFile $outputPath
    Start-Process -FilePath $outputPath -ArgumentList "/S" -Wait
    Remove-Item $outputPath
}


# 添加简体中文键盘布局
$LanguageList = Get-WinUserLanguageList
$LanguageList.Add("zh-CN")
Set-WinUserLanguageList $LanguageList -Force

# 添加 Microsoft Pinyin 输入法
$InputMethodList = Get-WinUserLanguageList
$InputMethodList[0].InputMethodTips.Clear()
$InputMethodList[0].InputMethodTips.Add('0409:00000409') # 英语键盘
$InputMethodList[0].InputMethodTips.Add('0804:E0200804') # Microsoft Pinyin 输入法
Set-WinUserLanguageList $InputMethodList -Force


# 以管理员权限运行
if (-not ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    Start-Process powershell.exe -Verb RunAs -ArgumentList ('-NoProfile -ExecutionPolicy Bypass -File "{0}"' -f ($myinvocation.MyCommand.Definition))
    exit
}

# 将 Windows Audio 服务设置为自动启动
Set-Service -Name Audiosrv -StartupType Automatic

# 启动 Windows Audio 服务（如果未运行）
Start-Service -Name Audiosrv


# 创建 NewStartPanel 注册表键
$Namespace = "HKCU:\SOFTWARE\Microsoft\Windows\CurrentVersion\Explorer\HideDesktopIcons\NewStartPanel"
if (-not (Test-Path $Namespace)) {
    New-Item -Path $Namespace -Force | Out-Null
}

# 显示“计算机”（此电脑）图标
$Computer = "{20D04FE0-3AEA-1069-A2D8-08002B30309D}"
Set-ItemProperty -Path $Namespace -Name $Computer -Value 0

# 显示“网络”图标
$Network = "{F02C1A0D-BE21-4350-88B0-7367FC96EF3C}"
Set-ItemProperty -Path $Namespace -Name $Network -Value 0

# 显示“控制面板”图标
$ControlPanel = "{5399E694-6CE5-4D6C-8FCE-1D8870FDCBA0}"
Set-ItemProperty -Path $Namespace -Name $ControlPanel -Value 0

# 显示“回收站”图标
$RecycleBin = "{645FF040-5081-101B-9F08-00AA002F954E}"
Set-ItemProperty -Path $Namespace -Name $RecycleBin -Value 0

# 显示“用户文件”图标
$UserFiles = "{59031a47-3f72-44a7-89c5-5595fe6b30ee}"
Set-ItemProperty -Path $Namespace -Name $UserFiles -Value 0

# 刷新桌面
$code = @'
[DllImport("Shell32.dll")]
public static extern int SHChangeNotify(int eventId, int flags, IntPtr item1, IntPtr item2);
'@
$refresh = Add-Type -MemberDefinition $code -Name WinAPI -Namespace Refresh -PassThru
$refresh::SHChangeNotify(0x8000000, 0x1000, [IntPtr]::Zero, [IntPtr]::Zero)

# 设置 Windows 模式 (亮色/深色)
# 亮色模式：1, 深色模式：0
Set-ItemProperty -Path HKCU:\SOFTWARE\Microsoft\Windows\CurrentVersion\Themes\Personalize -Name SystemUsesLightTheme -Value 0

# 设置应用模式 (亮色/深色)
# 亮色模式：1, 深色模式：0
Set-ItemProperty -Path HKCU:\SOFTWARE\Microsoft\Windows\CurrentVersion\Themes\Personalize -Name AppsUseLightTheme -Value 0

# 刷新系统，使更改生效
RUNDLL32.EXE USER32.DLL,UpdatePerUserSystemParameters

# 定义图片URL和下载路径
$url = "https://raw.githubusercontent.com/FoundryExchange/Share/refs/heads/main/004.png"
$downloadPath = "$env:USERPROFILE\Downloads\wallpaper.jpg"

# 下载图片
Invoke-WebRequest -Uri $url -OutFile $downloadPath

# 设置为桌面壁纸
Add-Type -TypeDefinition @"
    using System;
    using System.Runtime.InteropServices;
    public class Wallpaper {
        [DllImport("user32.dll", CharSet = CharSet.Auto)]
        public static extern int SystemParametersInfo(int uAction, int uParam, string lpvParam, int fuWinIni);
    }
"@

# 定义壁纸设置函数
function Set-Wallpaper {
    param(
        [string]$path
    )
    # 设置壁纸的API常量
    $SPI_SETDESKWALLPAPER = 0x0014
    $SPIF_UPDATEINIFILE = 0x01
    $SPIF_SENDCHANGE = 0x02
    
    # 调用API方法设置壁纸
    [Wallpaper]::SystemParametersInfo($SPI_SETDESKWALLPAPER, 0, $path, $SPIF_UPDATEINIFILE -bor $SPIF_SENDCHANGE) | Out-Null
}

# 调用函数，设置壁纸
Set-Wallpaper -path $downloadPath





Start-Job -ScriptBlock {
    $chromiumUrl = "https://storage.googleapis.com/chromium-browser-snapshots/Win_x64/1474103/mini_installer.exe"
    $outputPath = "$env:TEMP\Chromium-Setup.exe"
    Invoke-WebRequest -Uri $chromiumUrl -OutFile $outputPath
    Start-Process -FilePath $outputPath -ArgumentList "/silent", "/install" -Wait
    Remove-Item $outputPath
}


Start-Job -ScriptBlock {
    $chromeUrl = "https://dl.google.com/tag/s/appguid%3D%7B8A69D345-D564-463C-AFF1-A69D9E530F96%7D/chrome/install/ChromeStandaloneSetup64.exe"
    $outputPath = "$env:TEMP\GoogleChromeStandaloneSetup.exe"
    Invoke-WebRequest -Uri $chromeUrl -OutFile $outputPath
    Start-Process -FilePath $outputPath -ArgumentList "/silent", "/install" -Wait
    Remove-Item $outputPath
}



Start-Job -ScriptBlock {
    $firefoxUrl = "https://download.mozilla.org/?product=firefox-latest-ssl&os=win&lang=en-US"
    $outputPath = "$env:TEMP\Firefox-Setup.exe"
    Invoke-WebRequest -Uri $firefoxUrl -OutFile $outputPath
    Start-Process -FilePath $outputPath -ArgumentList "/S" -Wait
    Remove-Item $outputPath
}

Start-Job -ScriptBlock {
    $sevenZipUrl = "https://www.7-zip.org/a/7z2406-x64.exe"
    $outputPath = "$env:USERPROFILE\Downloads\7z2406-x64.exe"
    Invoke-WebRequest -Uri $sevenZipUrl -OutFile $outputPath
    Start-Process $outputPath -ArgumentList "/S" -Wait
    Remove-Item $outputPath
}



Start-Job -ScriptBlock {
    $sharexUrl = "https://github.com/ShareX/ShareX/releases/download/v17.1.0/ShareX-17.1.0-setup.exe"
    $outputPath = "$env:TEMP\ShareX-Setup.exe"
    Invoke-WebRequest -Uri $sharexUrl -OutFile $outputPath
    Start-Process -FilePath $outputPath -ArgumentList "/S" -Wait
    Remove-Item $outputPath
}


Start-Job -ScriptBlock {
    $vlcUrl = "https://mirror.nju.edu.cn/videolan-ftp/vlc/3.0.21/win64/vlc-3.0.21-win64.exe"
    $outputPath = "$env:TEMP\VLC-Setup.exe"
    Invoke-WebRequest -Uri $vlcUrl -OutFile $outputPath
    Start-Process -FilePath $outputPath -ArgumentList "/S" -Wait
    Remove-Item $outputPath
}


