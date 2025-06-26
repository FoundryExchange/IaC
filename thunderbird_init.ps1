# 检查 Thunderbird 进程是否存在
$process = Get-Process -Name "thunderbird" -ErrorAction SilentlyContinue

# 如果存在，则强制终止进程
if ($process) {
    Stop-Process -Name "thunderbird" -Force
}

# 构建 Thunderbird 目录路径
$userProfile = [Environment]::GetFolderPath('UserProfile')
$thunderbirdPath = Join-Path -Path $userProfile -ChildPath "AppData\Roaming\thunderbird"

# 删除 Thunderbird 目录及其子目录
Remove-Item -Path $thunderbirdPath -Recurse -Force
