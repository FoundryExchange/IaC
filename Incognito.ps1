
# 获取当前用户的应用程序路径
$localAppDataPath = [System.IO.Path]::Combine($env:USERPROFILE, 'AppData\Local')

# 定义Chrome和Chromium的实际路径
$chromeExePath = [System.IO.Path]::Combine($localAppDataPath, 'Google\Chrome\Application\chrome.exe')
$chromiumExePath = [System.IO.Path]::Combine($localAppDataPath, 'Chromium\Application\chrome.exe')

# 获取当前用户的桌面路径
$desktopPath = [System.IO.Path]::Combine($env:USERPROFILE, 'Desktop')

# 定义快捷方式的完整路径
$chromeShortcut = [System.IO.Path]::Combine($desktopPath, 'Google Chrome.lnk')
$chromiumShortcut = [System.IO.Path]::Combine($desktopPath, 'Chromium.lnk')
$firefoxShortcut = "C:\Users\Public\Desktop\Firefox.lnk"  # 定义公共桌面的 Firefox 快捷方式路径

# 定义一个函数来修改快捷方式
function Add-ArgumentsToShortcut {
    param (
        [string]$shortcutPath,
        [string]$arguments
    )

    # 加载COM对象的WScript.Shell类
    $shell = New-Object -ComObject WScript.Shell
    # 使用COM对象打开快捷方式
    $shortcut = $shell.CreateShortcut($shortcutPath)
    
    # 为快捷方式添加指定的参数
    $shortcut.Arguments = $arguments
    
    # 保存快捷方式的修改
    $shortcut.Save()
}

# 修改Google Chrome快捷方式以添加-Incognito参数
Add-ArgumentsToShortcut -shortcutPath $chromeShortcut -arguments "-Incognito"

# 修改Chromium快捷方式以添加-Incognito参数
Add-ArgumentsToShortcut -shortcutPath $chromiumShortcut -arguments "-Incognito"

# 修改Firefox快捷方式以添加-private参数
Add-ArgumentsToShortcut -shortcutPath $firefoxShortcut -arguments "-private"

Write-Host "快捷方式已修改完成。"
