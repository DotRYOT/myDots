# Gray Zone Warfare VOIP Fix Recap

## 1. Initial Setup & Diagnosis
- Downloaded the `gzw_voip_fix-autodetect.py` script from the [Golden-Icon GitHub](https://github.com/Golden-Icon/GZW-0.5-VOIP-FIX) repository.
- Made the script executable: `chmod +x gzw_voip_fix-autodetect.py`.
- Attempted auto-detection with `--appid 2479810 --check`, which initially failed because the Proton prefix needed to be initialized by launching the game at least once.

## 2. Locating the Target File
- After launching the game, auto-detection still struggled. Used the `find` command to manually locate the DLL in the custom Steam library: 
  `find /mnt/games/SteamLibrary/steamapps/compatdata/2479810 -name "ws2_32.dll"`
- Discovered the file in the game's prefix was a symlink pointing to `/run/host/usr/share/steam/...`.
- Recognized that `/run/host/` is a Flatpak-specific mount path, making the symlink appear broken to the native CachyOS terminal environment.

## 3. Finding the Native Proton File
- Bypassed the broken symlink by checking the actual native system path for the CachyOS Proton build.
- Verified the native, un-symlinked Proton file existed at:
  `/usr/share/steam/compatibilitytools.d/proton-cachyos-slr/files/lib/wine/x86_64-windows/ws2_32.dll`
- Confirmed the script could read this native file and reported its status as `CLEAN` (ready to be patched).

## 4. Applying the Patch
- Noted that the native Proton file is owned by `root`, requiring elevated privileges to modify.
- Ensured Gray Zone Warfare was completely closed (a safety check built into the script).
- Executed the patch with `sudo`:
  `sudo python3 gzw_voip_fix-autodetect.py --dll "/usr/share/steam/compatibilitytools.d/proton-cachyos-slr/files/lib/wine/x86_64-windows/ws2_32.dll"`
- The script successfully created a `.gzw-voip-backup` of the original file and applied the VOIP fix to the 64-bit `ws2_32.dll`.
