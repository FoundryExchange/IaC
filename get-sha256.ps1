<#
.SYNOPSIS
  计算输入字符串的 SHA-256 摘要，并输出小写十六进制。

.PARAMETER InputString
  要计算哈希的字符串。

.EXAMPLE
  PS> .\Get-Sha256.ps1 "hello, world"
  a591a6d40bf420404a011733cfb7b190d62c65bf0bcda32b\
  b4eaeabd45a6d6
#>

param(
    [Parameter(Mandatory=$true, Position=0)]
    [string]$InputString
)

# 将字符串转为 UTF-8 字节
$bytes = [System.Text.Encoding]::UTF8.GetBytes($InputString)

# 计算 SHA-256
$hashBytes = [System.Security.Cryptography.SHA256]::Create().ComputeHash($bytes)

# 转为小写十六进制并输出
$hashHex = ($hashBytes | ForEach-Object { $_.ToString("x2") }) -join ""
Write-Output $hashHex
