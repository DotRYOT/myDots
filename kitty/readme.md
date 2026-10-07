# Step-by-Step Recap: Switching from Alacritty to Kitty

## 1. Initial Context
- **Environment:** CachyOS (KDE Plasma, Wayland) with Fish as the default shell.
- **Goal:** Migrate terminal configuration from Alacritty to Kitty.

## 2. Core Configuration (Font & Dimensions)
- **Font:** RobotoMono Nerd Font at size 10.0.
- **Window Size:** Set to exactly 140 columns and 36 rows (using the `c` suffix for cell-based sizing).
- **Shell:** No explicit shell configuration needed, as Kitty automatically inherits the system default (Fish).

## 3. Color Theme
- **Scheme:** Red on Black (matching the provided Alacritty reference).
- **Settings:** `background #000000`, `foreground #ff0000`, `cursor #ff0000`, `cursor_text_color #000000`.

## 4. Transparency & Blur Effects
- **Opacity:** Added `background_opacity 0.85` to allow the desktop wallpaper to show through.
- **Blur:** Added `background_blur 15` to create a frosted glass effect behind the terminal window.
- **Workflow:** Noted that configuration changes can be applied on the fly without restarting by pressing `Ctrl+Shift+F5`.

## 5. Pending Consideration (For Your Review)
- **Readability vs. Aesthetics:** A question was raised about whether solid red foreground text might become difficult to read against bright or colorful wallpapers when transparency is enabled. We left off considering if you might prefer standard white/light text with red accents for better daily usability.
