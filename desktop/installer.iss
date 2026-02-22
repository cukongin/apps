; Script Installer Siapps Desktop (Dapodik Style)
; Requires: Inno Setup 6.x

#define MyAppName "Siapps Desktop"
#define MyAppVersion "1.0"
#define MyAppPublisher "Siapps Team"
#define MyAppURL "http://localhost:8899"
#define MyAppExeName "Siapps.vbs"

[Setup]
; NOTE: The value of AppId uniquely identifies this application. Do not use the same AppId value in installers for other applications.
; (To generate a new GUID, click Tools | Generate GUID inside the IDE.)
AppId={{E829559F-0785-4299-B0F3-7A39B2A12345}
AppName={#MyAppName}
AppVersion={#MyAppVersion}
AppPublisher={#MyAppPublisher}
AppPublisherURL={#MyAppURL}
AppSupportURL={#MyAppURL}
AppUpdatesURL={#MyAppURL}
DefaultDirName={autopf}\{#MyAppName}
DisableProgramGroupPage=yes
; Remove the following line to run in administrative install mode (install for all users.)
PrivilegesRequired=lowest
OutputDir=Output
OutputBaseFilename=Siapps_Desktop_Setup_v1.0
Compression=lzma
SolidCompression=yes
WizardStyle=modern

[Languages]
Name: "english"; MessagesFile: "compiler:Default.isl"

[Tasks]
Name: "desktopicon"; Description: "{cm:CreateDesktopIcon}"; GroupDescription: "{cm:AdditionalIcons}"; Flags: unchecked

[Files]
; KITA MENGAMBIL EXCLUSIVE DARI FOLDER DIST (Dataweb, PHP, Database)
Source: "dist\*"; DestDir: "{app}"; Flags: ignoreversion recursesubdirs createallsubdirs

[Icons]
Name: "{autoprograms}\{#MyAppName}"; Filename: "{app}\{#MyAppExeName}"; IconFilename: "{app}\dataweb\public\favicon.ico"
Name: "{autoprograms}\Stop {#MyAppName}"; Filename: "{app}\Stop Siapps.vbs"; IconFilename: "{app}\dataweb\public\favicon.ico"
Name: "{autodesktop}\{#MyAppName}"; Filename: "{app}\{#MyAppExeName}"; IconFilename: "{app}\dataweb\public\favicon.ico"; Tasks: desktopicon

[Run]
Filename: "{app}\{#MyAppExeName}"; Description: "{cm:LaunchProgram,{#MyAppName}}"; Flags: shellexec postinstall skipifsilent

[UninstallDelete]
Type: filesandordirs; Name: "{app}\database\data"
Type: filesandordirs; Name: "{app}\dataweb\storage\logs"
