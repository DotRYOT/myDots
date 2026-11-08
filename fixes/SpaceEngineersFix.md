# Space Engineers: Pulsar + DLC Unlocker Setup Recap (cachyOS)

## 1. Initial DLC Unlocker Installation
- Downloaded the `Space_Engineers_DLC_unlocker` from GitHub.
- Created and executed a bash script (`~/fix_se_dlc.sh`) to safely back up the original `steam_api64.dll`, rename it to `steam_api64_o.dll`, and place the unlocker's proxy DLL and `cream_api.ini` into the main `Bin64` directory, as well as Pulsar's `Libraries/Legacy` and `Libraries/Interim` subdirectories.

## 2. Identifying the Native Linux Conflict
- Discovered that Pulsar's default `Interim.bin` runs the game natively on Linux.
- Native Linux execution ignores Windows `.dll` files, causing the CreamAPI unlocker to be bypassed (which resulted in grayed-out DLC blocks).

## 3. Switching to the Windows Pulsar Wrapper
- Downloaded the **Windows** release (`.zip`) of Pulsar from GitHub to obtain the `Legacy.exe` file.
- Extracted it into `/mnt/games/SteamLibrary/steamapps/common/SpaceEngineers/Bin64/`.

## 4. Configuring Steam Launch Options for Proton
- Updated the Steam Launch Options to force Proton to execute Pulsar's Windows wrapper while prioritizing the custom unlocker DLL:
  WINEDLLOVERRIDES="steam_api64=n,b" bash -c 'exec "${@:0:$#}" "/mnt/games/SteamLibrary/steamapps/common/SpaceEngineers/Bin64/Pulsar/Legacy.exe" "${@:$#}"' %command%
- This successfully unlocked the DLCs, but introduced a secondary issue: missing in-game audio.

## 5. Resolving the Proton Audio Bug
- Identified that Proton's default `xaudio2_9.dll` is broken or missing for Space Engineers' updated FMOD audio engine.
- Created and executed a bash script (`~/fix_se_audio.sh`) to download the official Microsoft XAudio2 Redist package, extract `xaudio2_9redist.dll`, rename it to `xaudio2_9.dll`, and place it directly into the `Bin64` folder.
- Fully restarted Steam to apply the new DLL override, restoring all UI and in-game sounds.

### fix_se_audio.sh

```bash
#!/bin/bash

# 1. Define your game path
SE_BIN64="/mnt/games/SteamLibrary/steamapps/common/SpaceEngineers/Bin64"

# 2. Go to a temporary directory
cd /tmp

# 3. Download the official Microsoft XAudio2 Redist package (it's a zip file in disguise)
echo "Downloading Microsoft XAudio2 package..."
wget -q https://www.nuget.org/api/v2/package/Microsoft.XAudio2.Redist/1.2.11 -O xaudio2.zip

# 4. Extract only the specific 64-bit DLL we need
echo "Extracting DLL..."
unzip -q -o xaudio2.zip "build/native/release/bin/x64/xaudio2_9redist.dll"

# 5. Copy and rename it to your game's Bin64 folder
echo "Installing DLL to game folder..."
cp build/native/release/bin/x64/xaudio2_9redist.dll "$SE_BIN64/xaudio2_9.dll"

# 6. Clean up temporary files
rm -f xaudio2.zip
rm -rf build

echo "✅ Audio fix complete! Fully restart Steam and launch the game."

```

## fix_se_dlc.sh

```bash
#!/bin/bash

# 1. Define your exact paths
SE_BIN64="/mnt/games/SteamLibrary/steamapps/common/SpaceEngineers/Bin64"
UNLOCKER_DIR="/home/mstrcomp/Downloads/Space_Engineers_DLC_unlocker-main"

# 2. Navigate to the game's Bin64 folder
cd "$SE_BIN64"

# 3. Ensure the original DLL is safely backed up and renamed
if [ -f "steam_api64.dll" ] && [ ! -f "steam_api64_o.dll" ]; then
    cp steam_api64.dll steam_api64.dll.original_backup
    mv steam_api64.dll steam_api64_o.dll
fi

# 4. Copy the unlocker files to the main Bin64 folder
cp "$UNLOCKER_DIR/cream_api.ini" .
cp "$UNLOCKER_DIR/Backup/CreamAPI/steam_api64.dll" .

# 5. Create Pulsar library directories if they don't already exist
mkdir -p "$SE_BIN64/Pulsar/Libraries/Legacy"
mkdir -p "$SE_BIN64/Pulsar/Libraries/Interim"

# 6. Copy the unlocker files AND the renamed original DLL into Pulsar's folders
cp "$UNLOCKER_DIR/cream_api.ini" "$SE_BIN64/Pulsar/Libraries/Legacy/"
cp "$UNLOCKER_DIR/Backup/CreamAPI/steam_api64.dll" "$SE_BIN64/Pulsar/Libraries/Legacy/"
cp "steam_api64_o.dll" "$SE_BIN64/Pulsar/Libraries/Legacy/"

cp "$UNLOCKER_DIR/cream_api.ini" "$SE_BIN64/Pulsar/Libraries/Interim/"
cp "$UNLOCKER_DIR/Backup/CreamAPI/steam_api64.dll" "$SE_BIN64/Pulsar/Libraries/Interim/"
cp "steam_api64_o.dll" "$SE_BIN64/Pulsar/Libraries/Interim/"

echo "Installation complete. You can now launch the game."
```
