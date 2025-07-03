param(
    [Parameter(Mandatory = $true, Position = 0)]
    [string]$InputString
)

# 创建无 BOM 的 UTF-8 编码器
$utf8 = New-Object System.Text.UTF8Encoding($false, $false)

# 只在字符串后追加单个 LF (0x0A)，与 macOS echo "A" 一致
$bytes = $utf8.GetBytes("$InputString`n")

# 计算 SHA-256
$hashBytes = [System.Security.Cryptography.SHA256]::Create().ComputeHash($bytes)

# 转为小写十六进制并输出
$hex = ($hashBytes | ForEach-Object { $_.ToString("x2") }) -join ""
Write-Output $hex
