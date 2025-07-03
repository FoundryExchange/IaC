param(
    [Parameter(Mandatory=$true, Position=0)]
    [string]$InputString
)

# 在字符串后追加换行符，与 echo 行为保持一致
$bytes = [System.Text.Encoding]::UTF8.GetBytes("$InputString`n")

# 计算 SHA-256
$hashBytes = [System.Security.Cryptography.SHA256]::Create().ComputeHash($bytes)

# 转为小写十六进制并输出
($hashBytes | ForEach-Object { $_.ToString("x2") }) -join ""
