; Script Installer Siapps Desktop (Dapodik Style)
; Requires: Inno Setup 6.x

#define MyAppName "Siapps Desktop"
#define MyAppVersion "1.0"
#define MyAppPublisher "Siapps Team"
#define MyAppURL "http://localhost:8899"
#define MyAppExeName "start-app.bat"

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
Name: "indonesian"; MessagesFile: "compiler:Languages\Indonesian.isl"

[Tasks]
Name: "desktopicon"; Description: "{cm:CreateDesktopIcon}"; GroupDescription: "{cm:AdditionalIcons}"; Flags: unchecked

[Files]
; IMPORTANT: Adjust Source paths if your folder structure is different
; We assume you are building from OUTSIDE the project folder, pointing TO the project folder.
; Example: Source: "D:\XAMPP\htdocs\siapps\*"; DestDir: "{app}"; Flags: ignoreversion

; COPY ALL FILES
Source: "*"; DestDir: "{app}"; Flags: ignoreversion recursesubdirs createallsubdirs
; NOTE: You might want to exclude .git, node_modules, tests, etc. manually or via [Source] exclusions if Inno Setup supports patterns widely,
; but usually it's cleaner to have a 'dist' folder to compile from.

[Icons]
Name: "{autoprograms}\{#MyAppName}"; Filename: "{app}\desktop\{#MyAppExeName}"; IconFilename: "{app}\public\favicon.ico"
Name: "{autodesktop}\{#MyAppName}"; Filename: "{app}\desktop\{#MyAppExeName}"; IconFilename: "{app}\public\favicon.ico"; Tasks: desktopicon

[Run]
Filename: "{app}\desktop\{#MyAppExeName}"; Description: "{cm:LaunchProgram,{#MyAppName}}"; Flags: shellexec postinstall skipifsilent

[UninstallDelete]
Type: filesandordirs; Name: "{app}\bin\mysql\data"
Type: filesandordirs; Name: "{app}\storage\logs"
