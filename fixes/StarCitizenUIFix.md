# Recap: Fixing Star Citizen UI Mouse Click Issues on CachyOS

## 1. Problem Identification
- **Symptom**: Inability to click on UI elements in Star Citizen.
- **Environment**: CachyOS, NVIDIA RTX 3070 Ti, installed via `lug-helper`.
- **Root Cause**: Wayland display server's strict handling of cursor confinement conflicts with how Wine/Star Citizen attempts to grab the mouse.

## 2. Initial Troubleshooting
- Attempted to use the KDE Wayland "Allow without asking for permission" pointer control setting, but the game remained stubborn.
- Decided to switch the desktop session from Wayland to X11/Xorg, which handles cursor grabbing natively for Wine.

## 3. The Hunt for the X11 Session
- Attempted to find the session selector (gear icon) on the login screen, but it was hidden by the theme.
- Attempted to force X11 via terminal configuration files (`~/.config/ksmserverrc` and `/etc/sddm.conf`), but the system continued to default to Wayland.
- Discovered the root of the configuration failure: the `/usr/share/xsessions/` directory was completely empty.

## 4. The Final Solution
- Researched CachyOS/Arch Linux Plasma 6 changes and discovered that the X11 session is no longer included by default; it is split into its own optional package.
- Installed the missing package via terminal:
  ```bash
  sudo pacman -S plasma-x11-session
