# Recap: Fixing Star Citizen UI Mouse Click Issues on Linux

## 1. Problem Identification
- **Symptom**: Inability to click on UI elements in Star Citizen.
- **Environment**: CachyOS, NVIDIA RTX 3070 Ti, installed via `lug-helper`.
- **Root Cause**: Wayland display server's strict handling of cursor confinement and relative mouse motion conflicts with how Wine/Star Citizen attempts to grab the cursor.

## 2. Solution Evaluation
- **Option A**: Edit the `lug-helper` launch script to force cursor grabbing via `gamescope` (can be complex on NVIDIA).
- **Option B**: Switch the desktop session from Wayland to X11/Xorg (most reliable, zero configuration changes to the game).

## 3. Execution (Chosen Path: Option B)
1. Saved any open work and **logged out** of the current desktop session.
2. At the display manager (login) screen, clicked on the user profile.
3. Located the session selector (usually a gear icon or dropdown menu in the corner).
4. Selected the **X11/Xorg** equivalent (e.g., "Plasma (X11)" or "GNOME on Xorg").
5. Logged back in.

## 4. Result
- The display server now handles cursor grabbing natively as expected by Wine.
- Star Citizen UI is fully interactive and clickable.
- All programs, files, and `lug-helper` configurations remain completely untouched.
