Set WshShell = CreateObject("WScript.Shell")
WshShell.Run chr(34) & "siapps-stop.bat" & Chr(34), 0
Set WshShell = Nothing
MsgBox "Siapps Berhasil Dimatikan.", 64, "Siapps Desktop"
